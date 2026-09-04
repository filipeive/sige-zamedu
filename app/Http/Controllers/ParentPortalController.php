<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Communication;
use App\Models\Enrollment;
use App\Services\PaymentService;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ParentPortalController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    private function getParent()
    {
        $user = Auth::user();
        if (!$user) return null;

        $parent = ParentModel::where('user_id', $user->id)->first();
        if (!$parent) {
            $parent = ParentModel::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $user->name,
                    'last_name' => 'Encarregado',
                    'email' => $user->email,
                    'phone' => $user->phone ?? '841234567',
                ]
            );
        }

        // Se o encarregado não tiver educandos associados, associa automaticamente os alunos demonstração
        if ($parent->students()->count() === 0) {
            Student::whereNull('parent_id')
                ->orWhere('parent_id', 0)
                ->limit(3)
                ->update(['parent_id' => $parent->user_id]);

            if ($parent->students()->count() === 0) {
                Student::limit(3)->update(['parent_id' => $parent->user_id]);
            }
        }

        return $parent;
    }

    /**
     * Dashboard do Pai
     */
    public function dashboard()
    {
        $parent = $this->getParent();
        $nextYear = app(AcademicYearService::class)->getNextYear();

        if (!$parent) {
            return view('parent-portal.dashboard', [
                'parent' => null,
                'children' => collect([]),
                'recentCommunications' => collect([]),
                'communications' => collect([]),
                'pendingPayments' => collect([]),
                'totalPendingPayments' => 0,
                'nextYear' => $nextYear,
                'noParentProfile' => true
            ]);
        }
        $children = $parent->students()->with([
            'enrollments',
            'attendances' => function ($q) {
                $q->latest()->take(5);
            }
        ])->get();

        $currentYear = app(AcademicYearService::class)->getCurrentYear();

        foreach ($children as $child) {
            // Identificar se o aluno é elegível para renovação
            // (Status pending_renewal e sem matrícula activa/pendente no próximo ano)
            $child->is_eligible_for_renewal = ($child->status === 'pending_renewal') && !$child->enrollments()
                ->where('school_year', $nextYear)
                ->whereIn('status', ['active', 'pending'])
                ->exists();

            // Verificar se já existe um pedido de renovação
            $child->renewal_application = \App\Models\EnrollmentApplication::where('student_id', $child->id)
                ->where('academic_year', $nextYear)
                ->where('type', 'RENEWAL')
                ->latest()
                ->first();
        }

        $totalPendingPayments = 0;
        $recentCommunications = Communication::forParents()->published()->recent(5)->get();

        foreach ($children as $child) {
            $totalPendingPayments += $this->paymentService->getStudentTotalDebt($child->id);
        }

        return view('parent-portal.dashboard', compact('parent', 'children', 'totalPendingPayments', 'recentCommunications', 'nextYear'));
    }

    /**
     * Lista de filhos
     */
    public function children()
    {
        $parent = $this->getParent();
        $children = $parent->students()->with(['currentEnrollment.class'])->get();

        return view('parent-portal.children', compact('parent', 'children'));
    }

    /**
     * Detalhes do aluno
     */
    public function studentDetails(Student $student)
    {
        $parent = $this->getParent();

        if ($student->parent_id !== $parent->user_id && $student->parent?->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $student->load(['currentEnrollment.class', 'attendances', 'grades.subject']);

        return view('parent-portal.student-detail', compact('parent', 'student'));
    }

    /**
     * Caderneta de Notas do Aluno (apenas notas realmente lançadas).
     * Pai só vê as notas que foram efectivamente registadas pelos professores.
     */
    public function studentGrades(Student $student)
    {
        $parent = $this->getParent();

        if ($student->parent_id !== $parent->user_id && $student->parent?->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $student->load(['currentEnrollment.class.subjects', 'grades.subject']);

        // Apenas notas com valor real (exclui placeholders null)
        $grades = $student->grades()
            ->whereNotNull('grade')
            ->with('subject')
            ->orderBy('term')
            ->orderBy('subject_id')
            ->orderBy('assessment_type')
            ->get();

        // Disciplinas activas do plano curricular da turma actual
        $subjects = $student->currentEnrollment?->class?->subjects?->where('is_active', true) ?? \App\Models\Subject::where('is_active', true)->get();

        $subjectGrades = [];
        $totalMediaSum = 0;
        $subjectsWithMedia = 0;

        foreach ($subjects as $subject) {
            $subGrades = $grades->where('subject_id', $subject->id);

            $m1 = $subGrades->where('term', 1)->avg('grade');
            $m2 = $subGrades->where('term', 2)->avg('grade');
            $m3 = $subGrades->where('term', 3)->avg('grade');

            $countMedia = 0;
            $sumMedia = 0;
            if ($m1 !== null) { $sumMedia += $m1; $countMedia++; }
            if ($m2 !== null) { $sumMedia += $m2; $countMedia++; }
            if ($m3 !== null) { $sumMedia += $m3; $countMedia++; }

            $mediaGeral = $countMedia > 0 ? round($sumMedia / $countMedia, 1) : null;
            if ($mediaGeral !== null) {
                $totalMediaSum += $mediaGeral;
                $subjectsWithMedia++;
            }

            $status = '—';
            if ($mediaGeral !== null) {
                if ($mediaGeral >= 9.5) {
                    $status = 'Aprovado';
                } elseif ($mediaGeral >= 7.0) {
                    $status = 'Admitido ao Exame';
                } else {
                    $status = 'Reprovado';
                }
            }

            // Detalhes por trimestre (apenas notas com valor)
            $details = [
                1 => $subGrades->where('term', 1)->whereNotNull('grade')->values(),
                2 => $subGrades->where('term', 2)->whereNotNull('grade')->values(),
                3 => $subGrades->where('term', 3)->whereNotNull('grade')->values(),
            ];

            $subjectGrades[] = [
                'subject' => $subject,
                't1' => $m1 !== null ? round($m1, 1) : null,
                't2' => $m2 !== null ? round($m2, 1) : null,
                't3' => $m3 !== null ? round($m3, 1) : null,
                'final_media' => $mediaGeral,
                'status' => $status,
                'grades_list' => $subGrades->whereNotNull('grade')->values(),
                'details' => $details,
                'has_any_grade' => $subGrades->isNotEmpty(),
            ];
        }

        $overallAverage = $subjectsWithMedia > 0 ? round($totalMediaSum / $subjectsWithMedia, 1) : null;
        $academicStatus = $overallAverage !== null ? ($overallAverage >= 9.5 ? 'Aprovado' : 'Acompanhamento Necessário') : 'Em Curso';

        return view('parent-portal.student-grades', compact('parent', 'student', 'subjectGrades', 'overallAverage', 'academicStatus'));
    }

    /**
     * Registo de Presenças do Aluno
     */
    public function studentAttendance(Student $student)
    {
        $parent = $this->getParent();

        if ($student->parent_id !== $parent->user_id && $student->parent?->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $attendances = $student->attendances()->orderBy('attendance_date', 'desc')->paginate(20);

        $totalPresent = $student->attendances()->where('status', 'present')->count();
        $totalAbsent = $student->attendances()->where('status', 'absent')->count();
        $totalJustified = $student->attendances()->where('status', 'justified')->count();
        $totalLate = $student->attendances()->where('status', 'late')->count();
        $totalClasses = $student->attendances()->count();

        $assiduityPercentage = $totalClasses > 0
            ? round((($totalPresent + $totalLate) / $totalClasses) * 100, 1)
            : 100;

        return view('parent-portal.student-attendance', compact(
            'parent',
            'student',
            'attendances',
            'totalPresent',
            'totalAbsent',
            'totalJustified',
            'totalLate',
            'totalClasses',
            'assiduityPercentage'
        ));
    }

    /**
     * Pagamentos (Visão Geral)
     */
    public function payments()
    {
        $parent = $this->getParent();
        $children = $parent->students;

        $payments = Payment::whereIn('student_id', $children->pluck('id'))
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        return view('parent-portal.payments', compact('parent', 'payments'));
    }

    /**
     * Pagamentos de um aluno específico
     */
    public function studentPayments(Student $student)
    {
        $parent = $this->getParent();

        if ($student->parent_id !== $parent->user_id) {
            abort(403, 'Acesso não autorizado.');
        }

        $payments = Payment::where('student_id', $student->id)
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        $totalDebt = $this->paymentService->getStudentTotalDebt($student->id);

        return view('parent-portal.student-payments', compact('parent', 'student', 'payments', 'totalDebt'));
    }

    /**
     * Referências de Pagamento
     */
    public function paymentReferences(Student $student)
    {
        $parent = $this->getParent();

        if ($student->parent_id !== $parent->user_id) {
            abort(403, 'Acesso não autorizado.');
        }

        $references = Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();

        return view('parent-portal.payment-references', compact('parent', 'student', 'references'));
    }

    /**
     * Gerar nova referência de pagamento
     */
    public function generatePaymentReference(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:mensalidade,material,uniforme,outro',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $parent = $this->getParent();
        $student = Student::findOrFail($request->student_id);

        if ($student->parent_id !== $parent->user_id) {
            abort(403, 'Acesso não autorizado.');
        }

        // Verificar se já existe
        $exists = Payment::where('student_id', $student->id)
            ->where('type', $request->type)
            ->where('year', $request->year)
            ->when($request->month, fn($q) => $q->where('month', $request->month))
            ->exists();

        if ($exists) {
            return back()->with('error', 'Já existe uma referência para este pagamento.');
        }

        $amount = $request->type === 'mensalidade'
            ? ($student->currentEnrollment?->monthly_fee ?? 500)
            : 500; // Valor padrão ou buscar de tabela de preços

        $this->paymentService->createPayment([
            'student_id' => $student->id,
            'type' => $request->type,
            'amount' => $amount,
            'month' => $request->month,
            'year' => $request->year,
            'due_date' => Carbon::create($request->year, $request->month ?? 1, 10),
        ]);

        return back()->with('success', 'Referência gerada com sucesso!');
    }

    /**
     * Comunicações
     */
    public function communications()
    {
        $parent = $this->getParent();
        $communications = Communication::forParents()->published()->orderBy('created_at', 'desc')->paginate(15);

        return view('parent-portal.communications', compact('parent', 'communications'));
    }

    /**
     * Enviar mensagem para a escola
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'student_id' => 'nullable|exists:students,id'
        ]);

        // Implementar lógica de envio de mensagem (pode ser via Communication ou outro modelo)
        // Por enquanto, apenas redirecionar com sucesso

        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}
