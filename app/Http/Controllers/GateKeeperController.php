<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\GateLog;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;


class GateKeeperController extends Controller
{
    /**
     * Get the late time threshold for a given shift.
     */
    private function getShiftLateTime(string $shift): string
    {
        return match ($shift) {
            'afternoon' => setting('shift_afternoon_late_time', '13:00'),
            'night' => setting('shift_night_late_time', '18:00'),
            default => setting('shift_morning_late_time', '07:30'),
        };
    }

    /**
     * Determine attendance status based on shift and current time.
     */
    private function determineAttendanceStatus(string $shift, string $currentTime): string
    {
        $lateThreshold = $this->getShiftLateTime($shift);
        return $currentTime > $lateThreshold ? 'late' : 'present';
    }

    /**
     * Create a GateLog entry for a student.
     */
    private function createGateLog(Student $student, string $action, string $method = 'manual'): GateLog
    {
        return GateLog::create([
            'student_id' => $student->id,
            'class_id'   => $student->currentEnrollment?->class_id,
            'action'     => $action,
            'logged_at'  => now(),
            'method'     => $method,
            'logged_by'  => auth()->id() ?? 1,
            'notes'      => null,
        ]);
    }

    /**
     * Also sync the Attendance record (so presence tracking still works).
     */
    private function syncAttendance(Student $student, string $action, string $method): void
    {
        if (!$student->currentEnrollment) {
            return;
        }

        $class      = $student->currentEnrollment->class;
        $shift      = $class->shift ?? 'morning';
        $status     = $this->determineAttendanceStatus($shift, now()->format('H:i'));
        $shiftLabel = ClassRoom::SHIFT_LABELS[$shift] ?? 'Manhã';
        $methodLabel = $method === 'qr' ? 'via QR' : 'na Portaria Digital';
        $actionLabel = $action === 'entry' ? 'Entrada' : 'Saída';

        Attendance::updateOrCreate(
            [
                'student_id'      => $student->id,
                'class_id'        => $student->currentEnrollment->class_id,
                'attendance_date' => now()->toDateString(),
            ],
            [
                'status'       => $status,
                'arrival_time' => now()->toTimeString(),
                'notes'        => "Registado {$methodLabel} às " . now()->format('H:i') . " ({$actionLabel}) (Turno da {$shiftLabel})",
                'marked_by'    => auth()->id() ?? 1,
            ]
        );
    }

    /**
     * Portaria Digital main verification screen.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'super_admin', 'secretary', 'security']) && !auth()->user()->can('view_students')) {
            abort(403, 'Acesso não autorizado à Portaria Digital.');
        }

        $searchedStudent = null;

        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchedStudent = Student::with(['currentEnrollment.class', 'parent'])
                ->where('student_number', $search)
                ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
                ->first();
        }

        // Date filter — default to today
        $filterDate = $request->input('date', now()->toDateString());

        // Load gate logs for the selected date
        $todayLogs = GateLog::with(['student', 'class', 'loggedBy'])
            ->whereDate('logged_at', $filterDate)
            ->orderByDesc('logged_at')
            ->paginate(50);

        // Stats for today
        $stats = [
            'total'   => GateLog::whereDate('logged_at', $filterDate)->count(),
            'entries' => GateLog::whereDate('logged_at', $filterDate)->where('action', 'entry')->count(),
            'exits'   => GateLog::whereDate('logged_at', $filterDate)->where('action', 'exit')->count(),
        ];

        // Student trail for modal (if searched)
        $studentTrail = null;
        if ($searchedStudent) {
            $studentTrail = GateLog::where('student_id', $searchedStudent->id)
                ->orderByDesc('logged_at')
                ->take(30)
                ->get();
        }

        return view('gatekeeper.index', compact('searchedStudent', 'todayLogs', 'stats', 'filterDate', 'studentTrail'));
    }

    /**
     * Handle QR code scan — auto-detect student and register entry/exit.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $qrData = trim($request->qr_data);

        // Try to parse as JSON payload first (our generated QR format)
        $decoded = json_decode($qrData, true);
        if ($decoded && isset($decoded['student_number'])) {
            $studentNumber = $decoded['student_number'];
        } else {
            // Fallback: treat entire QR string as student_number
            $studentNumber = $qrData;
        }

        $student = Student::with(['currentEnrollment.class'])
            ->where('student_number', $studentNumber)
            ->first();

        if (!$student) {
            return redirect()->route('gatekeeper.index')
                ->with('error', "QR Code inválido ou aluno não encontrado: {$studentNumber}");
        }

        // Determine entry vs exit: if the last gate_log today is 'entry', this is 'exit'
        $lastLog = GateLog::where('student_id', $student->id)
            ->whereDate('logged_at', now()->toDateString())
            ->latest('logged_at')
            ->first();

        $action = ($lastLog && $lastLog->action === 'entry') ? 'exit' : 'entry';

        // Create gate log (always INSERT, never overwrite)
        $this->createGateLog($student, $action, 'qr');

        // Sync attendance record (for presence tracking)
        $this->syncAttendance($student, $action, 'qr');

        return redirect()->route('gatekeeper.index', ['search' => $student->student_number])
            ->with('success', 'QR Scan: ' . ($action === 'entry' ? 'Entrada' : 'Saída') . ' de ' . $student->full_name . ' registada com sucesso!');
    }

    /**
     * Log manual entrance/exit for student at gate.
     */
    public function logAccess(Request $request, Student $student)
    {
        $action = $request->input('action', 'entry');

        // Create gate log (always INSERT)
        $this->createGateLog($student, $action, 'manual');

        // Sync attendance record
        $this->syncAttendance($student, $action, 'manual');

        return redirect()->route('gatekeeper.index', ['search' => $student->student_number])
            ->with('success', 'Passagem (' . ($action === 'entry' ? 'Entrada' : 'Saída') . ') de ' . $student->full_name . ' registada com sucesso!');
    }

    /**
     * Return JSON history trail for a student (used by AJAX modal).
     */
    public function history(Request $request, Student $student)
    {
        $days = $request->input('days', 7);

        $logs = GateLog::with(['class', 'loggedBy'])
            ->where('student_id', $student->id)
            ->where('logged_at', '>=', now()->subDays($days))
            ->orderByDesc('logged_at')
            ->get()
            ->map(function ($log) {
                return [
                    'id'         => $log->id,
                    'action'     => $log->action,
                    'label'      => $log->action_label,
                    'logged_at'  => $log->logged_at->format('d/m/Y H:i:s'),
                    'date'       => $log->logged_at->format('d/m/Y'),
                    'time'       => $log->logged_at->format('H:i'),
                    'method'     => $log->method_label,
                    'class'      => $log->class?->name ?? 'N/A',
                    'logged_by'  => $log->loggedBy?->name ?? 'Sistema',
                ];
            });

        return response()->json([
            'student' => [
                'id'             => $student->id,
                'full_name'      => $student->full_name,
                'student_number' => $student->student_number,
            ],
            'logs' => $logs,
        ]);
    }

    /**
     * Generate and return a QR Code PNG for a student.
     */
    public function generateQr(Student $student)
    {
        $payload = json_encode([
            'student_number' => $student->student_number,
            'name' => $student->full_name,
            'school' => setting('school_short_name', 'ZamEdu'),
        ]);

        $payloadB64 = base64_encode($payload);
        $scriptPath = base_path('scripts/generate_qr.py');
        $logoPath = public_path('logo.png');

        $cmd = "python3 {$scriptPath} --payload-b64 {$payloadB64} --size 400";
        if (file_exists($logoPath)) {
            $cmd .= " --logo-path {$logoPath}";
        }

        $result = Process::run($cmd);

        if ($result->successful()) {
            return response($result->output(), 200)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'inline; filename="qr_' . $student->student_number . '.png"');
        }

        Log::error('QR generation failed: ' . $result->errorOutput());

        // Fallback: generate a simple SVG QR code
        return response('QR generation error', 500);
    }

    /**
     * Download printable QR card for a student.
     */
    public function downloadCard(Student $student)
    {
        $payload = json_encode([
            'student_number' => $student->student_number,
            'name' => $student->full_name,
            'school' => setting('school_short_name', 'ZamEdu'),
        ]);

        $payloadB64 = base64_encode($payload);
        $scriptPath = base_path('scripts/generate_qr.py');

        $result = Process::run("python3 {$scriptPath} --payload-b64 {$payloadB64} --size 400");

        if ($result->successful()) {
            $qrBase64 = base64_encode($result->output());
        } else {
            $qrBase64 = '';
        }

        return view('gatekeeper.card', compact('student', 'qrBase64'));
    }
}
