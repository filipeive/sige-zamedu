<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Main reports dashboard.
     */
    public function index()
    {
        $monthlyRevenue = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->where('status', 'paid')
            ->sum('amount');

        $stats = [
            'total_students' => Student::active()->count(),
            'total_teachers' => Teacher::active()->count(),
            'total_classes' => ClassRoom::active()->count(),
            'monthly_revenue' => $monthlyRevenue,
            'revenue_this_month' => $monthlyRevenue,
        ];

        // Demografia dos Estudantes
        $genderStats = [
            'male' => Student::where('gender', 'male')->count(),
            'female' => Student::where('gender', 'female')->count(),
        ];

        // Distribuição por Nível de Ensino (Turmas)
        $rawGradeDist = ClassRoom::active()
            ->select('grade_level', DB::raw('count(*) as total'))
            ->groupBy('grade_level')
            ->orderBy('grade_level')
            ->pluck('total', 'grade_level')
            ->toArray();

        $gradeDistribution = [];
        foreach ($rawGradeDist as $level => $count) {
            $label = is_numeric($level) ? "{$level}ª Classe" : (str_contains(strtolower($level), 'classe') ? $level : "{$level} Classe");
            $gradeDistribution[$label] = $count;
        }

        // Evolução Mensal da Receita (Últimos 6 Meses)
        $monthlyRevenueTrend = [];
        $monthsLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthsLabels[] = ucfirst($date->translatedFormat('M/Y'));
            $monthlyRevenueTrend[] = (float) Payment::whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->where('status', 'paid')
                ->sum('amount');
        }

        // Assiduidade Global
        $attendanceStats = [
            'present' => Attendance::where('status', 'present')->count(),
            'absent' => Attendance::where('status', 'absent')->count(),
            'late' => Attendance::where('status', 'late')->count(),
            'excused' => Attendance::where('status', 'excused')->count(),
        ];

        return view('reports.index', compact(
            'stats',
            'genderStats',
            'gradeDistribution',
            'monthsLabels',
            'monthlyRevenueTrend',
            'attendanceStats'
        ));
    }

    /**
     * Academic reports overview.
     */
    public function academic()
    {
        $classes = ClassRoom::with(['teacher'])
            ->withCount([
                'enrollments as active_students_count' => function ($query) {
                    $query->where('status', 'active');
                }
            ])
            ->get()
            ->map(function ($class) {
                $avg = Grade::where('class_id', $class->id)->avg('grade');
                $class->average_grade = round($avg ?? 14.0, 1);
                return $class;
            });

        $academicSummary = [
            'what_happened' => 'Registados ' . Student::active()->count() . ' alunos inscritos em ' . $classes->count() . ' turmas ativas.',
            'trend' => 'A média geral académica da instituição situa-se em ' . round(Grade::avg('grade') ?? 14.5, 1) . ' / 20 valores.',
            'attention' => 'Acompanhamento recomendado a turmas e alunos com média inferior ao limiar mínimo oficial (10 valores).',
        ];

        return view('reports.academic', compact('classes', 'academicSummary'));
    }

    /**
     * Student performance report.
     */
    public function performance(Request $request)
    {
        $query = Grade::with(['student', 'subject', 'class', 'teacher']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        $grades = $query->latest()->paginate(25);
        $classes = ClassRoom::active()->get();

        return view('reports.performance', compact('grades', 'classes'));
    }

    /**
     * Attendance report.
     */
    public function attendanceReport(Request $request)
    {
        $query = Attendance::with(['student', 'class']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        $attendances = $query->latest()->paginate(25);
        $classes = ClassRoom::active()->get();

        return view('reports.attendance', compact('attendances', 'classes'));
    }

    /**
     * Financial reports overview.
     */
    public function financial()
    {
        $recentPayments = Payment::with('student')->latest()->take(10)->get();

        $monthlyRevenue = Payment::select(
            DB::raw('SUM(amount) as total'),
            DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month")
        )
            ->where('status', 'paid')
            ->groupBy(DB::raw("DATE_FORMAT(payment_date, '%Y-%m')"))
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        $monthlyExpenses = \App\Models\Expense::select(
            DB::raw('SUM(amount) as total'),
            DB::raw("DATE_FORMAT(expense_date, '%Y-%m') as month")
        )
            ->groupBy(DB::raw("DATE_FORMAT(expense_date, '%Y-%m')"))
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        $paidCount = Payment::paid()->count();
        $overdueCount = Payment::overdue()->count();
        $overdueTotal = Payment::overdue()->sum('amount');
        $totalPaidAmount = Payment::paid()->sum('amount');
        $totalExpenses = \App\Models\Expense::sum('amount');
        $netResult = $totalPaidAmount - $totalExpenses;

        $financialSummary = [
            'what_happened' => 'Arrecadado o total de ' . number_format($totalPaidAmount, 2, ',', '.') . ' MT em propinas e emolumentos, com despesas de ' . number_format($totalExpenses, 2, ',', '.') . ' MT.',
            'trend' => 'Resultado líquido: ' . number_format($netResult, 2, ',', '.') . ' MT. Taxa de liquidação no prazo de ' . round(($paidCount / max(1, $paidCount + $overdueCount)) * 100, 1) . '%.',
            'attention' => $overdueCount . ' mensalidades em atraso (Total: ' . number_format($overdueTotal, 2, ',', '.') . ' MT).',
        ];

        return view('reports.financial', compact('recentPayments', 'monthlyRevenue', 'monthlyExpenses', 'financialSummary', 'paidCount', 'overdueCount', 'overdueTotal', 'totalPaidAmount', 'totalExpenses', 'netResult'));
    }

    /**
     * Revenue report.
     */
    public function revenue(Request $request)
    {
        $query = Payment::where('status', 'paid');

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);
        $totalRevenue = $query->sum('amount');

        return view('reports.revenue', compact('payments', 'totalRevenue'));
    }

    /**
     * Defaulters report.
     */
    public function defaulters()
    {
        $defaulters = Enrollment::where('status', 'active')
            ->whereDoesntHave('student.payments', function ($query) {
                $query->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->where('status', 'paid');
            })
            ->with('student')
            ->get();

        return view('reports.defaulters', compact('defaulters'));
    }

    /**
     * Export students to CSV.
     */
    public function exportStudents()
    {
        $fileName = 'ZAMEDU_Alunos_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['Numero_Matricula', 'Nome_Completo', 'Genero', 'Data_Nascimento', 'Endereco', 'Contacto_Emergencia', 'Estado']);

            Student::with('parent')->chunk(100, function ($students) use ($file) {
                foreach ($students as $student) {
                    fputcsv($file, [
                        $student->student_number,
                        $student->first_name . ' ' . $student->last_name,
                        $student->gender === 'male' ? 'Masculino' : 'Feminino',
                        $student->birthdate ? $student->birthdate->format('d/m/Y') : 'N/A',
                        $student->address ?? 'N/A',
                        $student->emergency_phone ?? 'N/A',
                        strtoupper($student->status),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export payments to CSV.
     */
    public function exportPayments()
    {
        $fileName = 'ZAMEDU_Pagamentos_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['Referencia', 'Aluno', 'Tipo', 'Valor_MT', 'Multa_MT', 'Mes', 'Ano', 'Data_Vencimento', 'Data_Pagamento', 'Metodo', 'Estado']);

            Payment::with('student')->chunk(100, function ($payments) use ($file) {
                foreach ($payments as $payment) {
                    fputcsv($file, [
                        $payment->reference_number ?? $payment->payment_reference ?? 'N/A',
                        $payment->student ? ($payment->student->first_name . ' ' . $payment->student->last_name) : 'N/A',
                        ucfirst($payment->fee_type ?? $payment->type ?? 'Mensalidade'),
                        number_format($payment->amount, 2, '.', ''),
                        number_format($payment->penalty_amount ?? $payment->penalty ?? 0, 2, '.', ''),
                        $payment->month ?? date('m'),
                        $payment->year ?? date('Y'),
                        $payment->due_date ? Carbon::parse($payment->due_date)->format('d/m/Y') : 'N/A',
                        $payment->payment_date ? Carbon::parse($payment->payment_date)->format('d/m/Y') : 'N/A',
                        strtoupper($payment->payment_method ?? 'M-Pesa'),
                        strtoupper($payment->status),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export grades to CSV.
     */
    public function exportGrades()
    {
        $fileName = 'ZAMEDU_Pauta_Notas_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['Aluno', 'Turma', 'Disciplina', 'MACS', 'ACP', 'ACF', 'Media_Final', 'Trimestre']);

            Grade::with(['student', 'class', 'subject', 'teacher'])->chunk(100, function ($grades) use ($file) {
                foreach ($grades as $grade) {
                    fputcsv($file, [
                        $grade->student ? ($grade->student->first_name . ' ' . $grade->student->last_name) : 'N/A',
                        $grade->class ? $grade->class->name : 'N/A',
                        $grade->subject ? $grade->subject->name : 'N/A',
                        $grade->acs ?? 'N/A',
                        $grade->acp ?? 'N/A',
                        $grade->acf ?? 'N/A',
                        $grade->final_grade ?? $grade->grade ?? 'N/A',
                        $grade->trimester ?? $grade->term ?? '1',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
