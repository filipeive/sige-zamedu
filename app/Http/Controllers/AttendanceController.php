<?php
// app/Http/Controllers/AttendanceController.php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard organized by classes
     */
    public function index(Request $request)
    {
        $this->authorize('view_attendances');

        $selectedDate = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($selectedDate);

        $classesQuery = ClassRoom::active()
            ->where('school_year', current_school_year())
            ->withCount(['students as total_students'])
            ->with(['attendances' => function ($q) use ($date) {
                $q->whereDate('attendance_date', $date);
            }])
            ->orderBy('name');

        // Filtro por escola (super admin / territorial)
        if ($request->filled('school_id')) {
            $classesQuery->where('school_id', $request->school_id);
        }

        $classes = $classesQuery->get()
            ->map(function ($class) use ($date) {
                $totalStudents = $class->total_students;
                $attendances = $class->attendances;
                
                $present = $attendances->where('status', 'present')->count();
                $absent = $attendances->where('status', 'absent')->count();
                $late = $attendances->where('status', 'late')->count();
                $excused = $attendances->where('status', 'excused')->count();
                $marked = $attendances->count();
                
                $attendanceRate = $totalStudents > 0 ? round(($present / $totalStudents) * 100, 1) : 0;
                
                return [
                    'class' => $class,
                    'total_students' => $totalStudents,
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'excused' => $excused,
                    'marked' => $marked,
                    'unmarked' => $totalStudents - $marked,
                    'attendance_rate' => $attendanceRate,
                ];
            });

        $globalStats = [
            'total_classes' => $classes->count(),
            'total_students' => $classes->sum('total_students'),
            'total_present' => $classes->sum('present'),
            'total_absent' => $classes->sum('absent'),
            'total_late' => $classes->sum('late'),
            'total_excused' => $classes->sum('excused'),
            'total_unmarked' => $classes->sum('unmarked'),
        ];

        $schools = School::orderBy('name')->get();

        return view('attendances.index', compact('classes', 'globalStats', 'selectedDate', 'schools'));
    }

    /**
     * Show form to mark attendance
     */
    public function mark(Request $request)
    {
        $this->authorize('mark_attendances');

        $classes = ClassRoom::active()
            ->where('school_year', current_school_year())
            ->with([
                'students' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->orderBy('name')
            ->get();

        return view('attendances.mark', compact('classes'));
    }

    /**
     * Store marked attendance
     */
    public function storeMark(Request $request)
    {
        $this->authorize('mark_attendances');

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'attendance_date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused',
        ]);

        DB::beginTransaction();

        try {
            $class = ClassRoom::findOrFail($validated['class_id']);
            $attendanceDate = Carbon::parse($validated['attendance_date']);

            // Verificar se já existem presenças para esta turma nesta data
            $existingCount = Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $attendanceDate)
                ->count();

            if ($existingCount > 0) {
                return back()->with('warning', 'Já existem presenças marcadas para esta turma nesta data.');
            }

            $markedCount = 0;

            foreach ($validated['attendance'] as $studentId => $status) {
                // Verificar se o aluno pertence à turma
                $student = Student::findOrFail($studentId);

                if (!$student->currentEnrollment || $student->currentEnrollment->class_id != $class->id) {
                    continue;
                }

                Attendance::create([
                    'student_id' => $studentId,
                    'class_id' => $class->id,
                    'attendance_date' => $attendanceDate,
                    'status' => $status,
                    'marked_by' => auth()->id(),
                    'arrival_time' => $status === 'late' ? now() : null,
                ]);

                $markedCount++;
            }

            DB::commit();

            return redirect()->route('attendances.index')
                ->with('success', "Presenças marcadas com sucesso! {$markedCount} alunos registados.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Erro ao marcar presenças: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance for a specific class
     */
    public function classAttendances(Request $request, ClassRoom $class)
    {
        $this->authorize('view_attendances');

        $selectedDate = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($selectedDate);

        $students = $class->students()
            ->with(['attendances' => function ($q) use ($date) {
                $q->whereDate('attendance_date', $date);
            }])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(function ($student) use ($date) {
                $attendance = $student->attendances->first();
                
                return [
                    'student' => $student,
                    'attendance' => $attendance,
                    'attendance_status' => $attendance?->status ?? null,
                    'arrival_time' => $attendance?->arrival_time ?? null,
                    'marked_by' => $attendance?->markedBy ?? null,
                ];
            });

        return view('attendances.class-attendances', compact('class', 'students', 'selectedDate'));
    }

    /**
     * Show form to mark attendance for a specific class
     */
    public function markByClass(Request $request, ClassRoom $class)
    {
        $this->authorize('mark_attendances');

        $date = $request->get('date', today()->format('Y-m-d'));

        $existingAttendances = Attendance::where('class_id', $class->id)
            ->whereDate('attendance_date', $date)
            ->pluck('status', 'student_id');

        $students = $class->students()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('attendances.mark-by-class', compact('class', 'students', 'date', 'existingAttendances'));
    }

    /**
     * Store attendance for a specific class
     */
    public function storeMarkByClass(Request $request, ClassRoom $class)
    {
        $this->authorize('mark_attendances');

        $validated = $request->validate([
            'attendance_date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $attendanceDate = Carbon::parse($validated['attendance_date']);

            // Deletar presenças existentes para permitir atualização
            Attendance::where('class_id', $class->id)
                ->whereDate('attendance_date', $attendanceDate)
                ->delete();

            $markedCount = 0;

            foreach ($validated['attendance'] as $studentId => $status) {
                $notes = $validated['notes'][$studentId] ?? null;

                Attendance::create([
                    'student_id' => $studentId,
                    'class_id' => $class->id,
                    'attendance_date' => $attendanceDate,
                    'status' => $status,
                    'notes' => $notes,
                    'marked_by' => auth()->id(),
                    'arrival_time' => $status === 'late' ? now() : null,
                ]);

                $markedCount++;

                // Notificar pais em caso de ausência (implementar depois)
                if ($status === 'absent') {
                    // $this->notifyParentAbsence($studentId, $attendanceDate);
                }
            }

            DB::commit();

            return redirect()->route('attendances.class-attendances', $class)
                ->with('success', "Frequências atualizadas! {$markedCount} alunos.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Erro ao salvar: ' . $e->getMessage());
        }
    }

    /**
     * Display attendance reports
     */
    public function reports(Request $request)
    {
        $this->authorize('view_attendances');

        $classId = $request->get('class_id');
        $period = $request->get('period', 'month');

        // Definir intervalo de datas baseado no período
        switch ($period) {
            case 'today':
                $startDate = today();
                $endDate = today();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'custom':
                $startDate = $request->get('start_date', now()->startOfMonth());
                $endDate = $request->get('end_date', now()->endOfMonth());
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
        }

        $query = Attendance::whereBetween('attendance_date', [$startDate, $endDate]);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        // Estatísticas gerais
        $totalAttendances = $query->count();
        $presentCount = (clone $query)->where('status', 'present')->count();
        $absentCount = (clone $query)->where('status', 'absent')->count();
        $lateCount = (clone $query)->where('status', 'late')->count();
        $excusedCount = (clone $query)->where('status', 'excused')->count();

        // Dados para gráficos
        $dailyStats = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->when($classId, function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->select(
                'attendance_date',
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late')
            )
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get();

        // Classes com maior taxa de ausência
        $classesAbsence = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->select(
                'class_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count')
            )
            ->groupBy('class_id')
            ->with('class')
            ->get()
            ->map(function ($item) {
                $item->absence_rate = $item->total > 0 ? ($item->absent_count / $item->total) * 100 : 0;
                return $item;
            })
            ->sortByDesc('absence_rate')
            ->take(5);

        // Alunos com mais faltas
        $studentsWithMostAbsences = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'absent')
            ->when($classId, function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->select('student_id', DB::raw('COUNT(*) as absence_count'))
            ->groupBy('student_id')
            ->orderByDesc('absence_count')
            ->with('student')
            ->take(10)
            ->get();

        $classes = ClassRoom::active()
            ->where('school_year', current_school_year())
            ->orderBy('name')
            ->get();

        $stats = compact(
            'totalAttendances',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'dailyStats',
            'classesAbsence',
            'studentsWithMostAbsences'
        );

        return view('attendances.report', compact('stats', 'classes', 'startDate', 'endDate'));
    }

    /**
     * Show attendance report for a specific class
     */
    public function classReport(ClassRoom $class)
    {
        $this->authorize('view_attendances');

        $students = $class->students()
            ->with([
                'attendances' => function ($q) {
                    $q->whereMonth('attendance_date', now()->month)
                        ->whereYear('attendance_date', now()->year);
                }
            ])
            ->get()
            ->map(function ($student) {
                $attendances = $student->attendances;
                $total = $attendances->count();

                return [
                    'student' => $student,
                    'total_days' => $total,
                    'present' => $attendances->where('status', 'present')->count(),
                    'absent' => $attendances->where('status', 'absent')->count(),
                    'late' => $attendances->where('status', 'late')->count(),
                    'excused' => $attendances->where('status', 'excused')->count(),
                    'attendance_rate' => $total > 0 ? round(($attendances->where('status', 'present')->count() / $total) * 100, 1) : 0,
                ];
            });

        return view('attendances.class-report', compact('class', 'students'));
    }

    /**
     * Show attendance report for a specific student
     */
    public function studentReport(Student $student)
    {
        $this->authorize('view_attendances');

        $attendances = Attendance::where('student_id', $student->id)
            ->with('class')
            ->latest('attendance_date')
            ->paginate(30);

        $stats = [
            'total' => Attendance::where('student_id', $student->id)->count(),
            'present' => Attendance::where('student_id', $student->id)->where('status', 'present')->count(),
            'absent' => Attendance::where('student_id', $student->id)->where('status', 'absent')->count(),
            'late' => Attendance::where('student_id', $student->id)->where('status', 'late')->count(),
            'this_month_present' => Attendance::where('student_id', $student->id)
                ->whereMonth('attendance_date', now()->month)
                ->where('status', 'present')
                ->count(),
            'this_month_total' => Attendance::where('student_id', $student->id)
                ->whereMonth('attendance_date', now()->month)
                ->count(),
        ];

        $stats['attendance_rate'] = $stats['total'] > 0
            ? round(($stats['present'] / $stats['total']) * 100, 1)
            : 0;

        $stats['this_month_rate'] = $stats['this_month_total'] > 0
            ? round(($stats['this_month_present'] / $stats['this_month_total']) * 100, 1)
            : 0;

        return view('attendances.student-report', compact('student', 'attendances', 'stats'));
    }

    /**
     * Display the specified attendance record.
     */
    public function show(Attendance $attendance)
    {
        $this->authorize('view_attendances');

        $attendance->load('student', 'class', 'markedBy');

        return view('attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified attendance record.
     */
    public function edit(Attendance $attendance)
    {
        $this->authorize('mark_attendances');

        $attendance->load('student', 'class');

        $statuses = ['present' => 'Presente', 'absent' => 'Ausente', 'late' => 'Atrasado', 'excused' => 'Justificado'];

        return view('attendances.edit', compact('attendance', 'statuses'));
    }

    /**
     * Update an attendance record
     */
    public function update(Request $request, Attendance $attendance)
    {
        $this->authorize('mark_attendances');

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $attendance->notes,
            'arrival_time' => $validated['status'] === 'late' ? ($attendance->arrival_time ?? now()) : null,
        ]);

        return back()->with('success', 'Presença atualizada com sucesso!');
    }

    /**
     * Delete an attendance record
     */
    public function destroy(Attendance $attendance)
    {
        $this->authorize('mark_attendances');

        $attendance->delete();

        return back()->with('success', 'Registo de presença excluído com sucesso!');
    }

    /**
     * Export attendance report
     */
    public function export(Request $request)
    {
        $this->authorize('export_reports');

        // Implementar exportação para Excel/PDF
        // Usar Maatwebsite\Excel ou DomPDF

        return response()->download(storage_path('exports/attendance_report.xlsx'));
    }

    /**
     * Get students for a class (API endpoint)
     */
    public function getClassStudents(ClassRoom $class)
    {
        $students = $class->students()
            ->where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'student_number', 'passport_photo')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'student_number' => $student->student_number,
                    'photo_url' => $student->photo_url,
                ];
            });

        return response()->json($students);
    }

    /**
     * Send notification to parent about absence (private method)
     */
    private function notifyParentAbsence($studentId, $date)
    {
        $student = Student::with('parent')->find($studentId);

        if ($student && $student->parent) {
            // Implementar notificação por email/SMS
            // Notification::send($student->parent->user, new StudentAbsenceNotification($student, $date));
        }
    }
}

// app/Http/Controllers/Api/AttendanceApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    /**
     * Get students for a class
     */
    public function getClassStudents($classId)
    {
        $class = ClassRoom::findOrFail($classId);

        $students = $class->students()
            ->where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'student_number', 'passport_photo')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'student_number' => $student->student_number,
                    'photo_url' => $student->photo_url,
                ];
            });

        return response()->json($students);
    }

    /**
     * Get attendance statistics
     */
    public function getStats(Request $request)
    {
        $classId = $request->get('class_id');
        $date = $request->get('date', today());

        $query = Attendance::whereDate('attendance_date', $date);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $stats = [
            'total' => $query->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'excused' => (clone $query)->where('status', 'excused')->count(),
        ];

        return response()->json($stats);
    }
}