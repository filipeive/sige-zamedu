<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom as SchoolClass;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\School;
use App\Notifications\GenericNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = current_school_year();
        $selectedYear = $request->input('year', $currentYear);

        $query = Enrollment::with(['student', 'class']);

        // Filtro por ano lectivo (padrão: ano atual)
        $query->where('school_year', $selectedYear);

        // Filtro por escola (super admin / territorial)
        if ($request->filled('school_id')) {
            $query->withoutGlobalScope(\App\Scopes\SchoolScope::class)
                ->whereHas('student', function ($q) use ($request) {
                    $q->where('school_id', $request->school_id);
                });
        }

        // Filtros adicionais
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->latest()->paginate(20)->appends($request->query());

        // Anos lectivos disponíveis (com matrículas registadas)
        $availableYears = Enrollment::select('school_year')
            ->distinct()
            ->orderByDesc('school_year')
            ->pluck('school_year');

        // Garantir que o ano atual sempre aparece na lista
        if (!$availableYears->contains($currentYear)) {
            $availableYears->prepend($currentYear);
            $availableYears = $availableYears->sortDesc()->values();
        }

        // Turmas filtradas pelo ano selecionado
        $classes = SchoolClass::where('school_year', $selectedYear)->get();

        // Escolas para filtro (super admin / territorial)
        $schools = School::orderBy('name')->get();

        return view('enrollments.index', compact('enrollments', 'classes', 'currentYear', 'selectedYear', 'availableYears', 'schools'));
    }

    public function create()
    {
        // Busca alunos que não têm matrícula ativa no ano corrente
        $students = Student::whereDoesntHave('enrollments', function ($query) {
            $query->where('school_year', current_school_year())
                ->whereIn('status', ['active', 'pending']);
        })->active()->get();

        $classes = SchoolClass::all();
        $currentYear = current_school_year();

        return view('enrollments.create', compact('students', 'classes', 'currentYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'school_year' => 'required|integer|min:2020|max:2030',
            'monthly_fee' => 'nullable|numeric|min:0',
            'enrollment_fee' => 'nullable|numeric|min:0',
            'payment_day' => 'nullable|integer|min:1|max:28',
            'enrollment_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            // Verifica se o aluno já tem matrícula ativa ou pendente no mesmo ano
            $existingEnrollment = Enrollment::where('student_id', $request->student_id)
                ->where('school_year', $request->school_year)
                ->whereIn('status', ['active', 'pending'])
                ->first();

            if ($existingEnrollment) {
                return redirect()->back()
                    ->with('error', 'Este aluno já possui uma matrícula ativa ou pendente para o ano selecionado.')
                    ->withInput();
            }

            // Determina o status baseado no valor da taxa de matrícula
            $status = $request->enrollment_fee > 0 ? Enrollment::STATUS_PENDING : Enrollment::STATUS_ACTIVE;
            // Cria a matrícula
            $enrollment = Enrollment::create([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'school_year' => $request->school_year,
                'enrollment_date' => $request->enrollment_date,
                'monthly_fee' => $request->monthly_fee,
                'payment_day' => $request->payment_day,
                'status' => $status,
                'observations' => $request->observations,
            ]);

            // Cria o pagamento da taxa de matrícula se houver valor
            if ($request->enrollment_fee > 0) {
                $enrollmentPayment = Payment::create([
                    'reference_number' => Payment::generateReference($request->student_id, 0, $request->school_year),
                    'student_id' => $request->student_id,
                    'enrollment_id' => $enrollment->id,
                    'type' => 'matricula',
                    'amount' => $request->enrollment_fee,
                    'month' => null,
                    'year' => $request->school_year,
                    'due_date' => $request->enrollment_date,
                    'status' => 'pending',
                    'notes' => 'Taxa de matrícula - '.($request->observations ?: 'Matrícula ano letivo '.$request->school_year),
                ]);

                // Se o pagamento for feito à vista, atualiza status
                if ($request->has('pay_now') && $request->pay_now) {
                    $enrollmentPayment->update([
                        'status' => 'paid',
                        'payment_date' => now(),
                        'payment_method' => $request->payment_method ?? 'cash',
                    ]);

                    // Ativa a matrícula
                    $enrollment->update(['status' => 'active']);
                }
            }

            // Notificar o encarregado sobre a matrícula e atribuição de turma
            $student = Student::with('parent.user')->find($request->student_id);
            $class = SchoolClass::find($request->class_id);
            if ($student && $student->parent && $student->parent->user && $class) {
                $student->parent->user->notify(new GenericNotification([
                    'title' => 'Matrícula & Turma Atribuída',
                    'message' => "A matrícula do aluno {$student->full_name} na turma {$class->name} (Ano Letivo {$request->school_year}) foi registada com sucesso.",
                    'action_url' => route('parent-portal.dashboard'),
                    'type' => 'success'
                ]));
            }

            DB::commit();

            $message = $status == 'active'
                ? 'Matrícula realizada e ativada com sucesso!'
                : 'Matrícula realizada com sucesso! Aguardando pagamento da taxa de matrícula.';

            return redirect()->route('enrollments.show', $enrollment->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erro ao realizar matrícula: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'class', 'payments']);

        // Busca o pagamento da matrícula específico
        $enrollmentPayment = $enrollment->payments()
            ->where('type', 'matricula')
            ->first();

        return view('enrollments.show', compact('enrollment', 'enrollmentPayment'));
    }

    public function edit(Enrollment $enrollment)
    {
        $classes = SchoolClass::all();

        // Busca o pagamento da matrícula
        $enrollmentPayment = $enrollment->payments()
            ->where('type', 'matricula')
            ->first();

        return view('enrollments.edit', compact('enrollment', 'classes', 'enrollmentPayment'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'monthly_fee' => 'nullable|numeric|min:0',
            'payment_day' => 'nullable|integer|min:1|max:28',
            'status' => 'required|in:active,pending,inactive,transferred,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'class_id' => $request->class_id,
                'monthly_fee' => $request->monthly_fee,
                'payment_day' => $request->payment_day,
                'status' => $request->status,
                'observations' => $request->observations,
            ];

            // Se a matrícula foi cancelada ou transferida, define a data de cancelamento
            if (in_array($request->status, ['cancelled', 'transferred']) && ! $enrollment->cancellation_date) {
                $updateData['cancellation_date'] = now();
            }

            // Se foi reativada, remove a data de cancelamento
            if ($request->status == 'active' && $enrollment->cancellation_date) {
                $updateData['cancellation_date'] = null;
            }

            $enrollment->update($updateData);

            DB::commit();

            return redirect()->route('enrollments.show', $enrollment->id)
                ->with('success', 'Matrícula atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erro ao atualizar matrícula: '.$e->getMessage())
                ->withInput();
        }
    }

    // Ativar matrícula quando o pagamento for confirmado
    public function activate(Enrollment $enrollment)
    {
        try {
            if (is_private_school()) {
                // Verifica se existe pagamento pendente da matrícula em escola privada
                $enrollmentPayment = $enrollment->payments()
                    ->where('type', 'matricula')
                    ->where('status', 'pending')
                    ->first();

                if ($enrollmentPayment) {
                    return redirect()->back()
                        ->with('error', 'Não é possível ativar a matrícula. Existe taxa de matrícula pendente.');
                }
            }

            $enrollment->update([
                'status' => 'active',
                'cancellation_date' => null,
            ]);

            return redirect()->back()
                ->with('success', 'Matrícula ativada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao ativar matrícula: '.$e->getMessage());
        }
    }

    // Método para confirmar pagamento e ativar matrícula
    public function confirmPayment(Enrollment $enrollment)
    {
        try {
            DB::beginTransaction();

            // Busca o pagamento da matrícula
            $enrollmentPayment = $enrollment->payments()
                ->where('type', 'matricula')
                ->where('status', 'pending')
                ->first();

            if (! $enrollmentPayment) {
                return redirect()->back()
                    ->with('error', 'Não há taxa de matrícula pendente para confirmar.');
            }

            // Atualiza o pagamento
            $enrollmentPayment->update([
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            // Ativa a matrícula
            $enrollment->update(['status' => 'active']);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Pagamento confirmado e matrícula ativada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erro ao confirmar pagamento: '.$e->getMessage());
        }
    }

    public function cancel(Enrollment $enrollment)
    {
        try {
            $enrollment->update([
                'status' => 'cancelled',
                'cancellation_date' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Matrícula cancelada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao cancelar matrícula: '.$e->getMessage());
        }
    }

    public function transfer(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'new_class_id' => 'required|exists:classes,id',
            'transfer_date' => 'required|date',
            'transfer_fee' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Verifica se a matrícula atual está ativa
            if ($enrollment->status != 'active') {
                return redirect()->back()
                    ->with('error', 'Só é possível transferir matrículas ativas.');
            }

            // Cancela a matrícula atual
            $enrollment->update([
                'status' => 'transferred',
                'cancellation_date' => $request->transfer_date,
                'observations' => ($enrollment->observations ? $enrollment->observations."\n" : '').
                    'Transferido para nova turma em '.now()->format('d/m/Y'),
            ]);

            // Cria nova matrícula
            $newEnrollment = Enrollment::create([
                'student_id' => $enrollment->student_id,
                'class_id' => $request->new_class_id,
                'school_year' => $enrollment->school_year,
                'enrollment_date' => $request->transfer_date,
                'monthly_fee' => $enrollment->monthly_fee,
                'payment_day' => $enrollment->payment_day,
                'status' => 'active', // Nova matrícula começa ativa
                'observations' => 'Transferido da turma '.$enrollment->class->name.' em '.now()->format('d/m/Y'),
            ]);

            // Cria pagamento da taxa de transferência se aplicável
            if ($request->transfer_fee > 0) {
                Payment::create([
                    'reference_number' => Payment::generateReference($enrollment->student_id, 0, $enrollment->school_year).'T',
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $newEnrollment->id,
                    'type' => 'outro',
                    'amount' => $request->transfer_fee,
                    'month' => null,
                    'year' => $enrollment->school_year,
                    'due_date' => $request->transfer_date,
                    'status' => 'pending',
                    'notes' => 'Taxa de transferência de turma',
                ]);
            }

            DB::commit();

            return redirect()->route('enrollments.show', $newEnrollment->id)
                ->with('success', 'Aluno transferido com sucesso!'.
                    ($request->transfer_fee > 0 ? ' Taxa de transferência registrada.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erro ao transferir aluno: '.$e->getMessage());
        }
    }

    public function print(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'class', 'payments']);

        // Busca o pagamento da matrícula
        $enrollmentPayment = $enrollment->payments()
            ->where('type', 'matricula')
            ->first();

        $pdf = PDF::loadView('enrollments.print', compact('enrollment', 'enrollmentPayment'));

        return $pdf->download("matricula-{$enrollment->student->first_name}-{$enrollment->student->last_name}-{$enrollment->school_year}.pdf");
    }

    // Matrícula automática ao criar estudante
    public static function createAutomaticEnrollment($studentId, $classId, $monthlyFee = null, $enrollmentFee = null)
    {
        try {
            $currentYear = current_school_year();
            $student = \App\Models\Student::find($studentId);
            $defaultFee = $monthlyFee ?? $student->monthly_fee ?? 2500.00;
            $defaultEnrollmentFee = $enrollmentFee ?? 500.00;

            // Status baseado na taxa de matrícula
            $status = $defaultEnrollmentFee > 0 ? 'pending' : 'active';

            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'class_id' => $classId,
                'school_year' => $currentYear,
                'enrollment_date' => now(),
                'monthly_fee' => $defaultFee,
                'payment_day' => 10,
                'status' => $status,
            ]);

            // Cria pagamento da taxa de matrícula se houver valor
            if ($defaultEnrollmentFee > 0) {
                Payment::create([
                    'reference_number' => Payment::generateReference($studentId, 0, $currentYear),
                    'student_id' => $studentId,
                    'enrollment_id' => $enrollment->id,
                    'type' => 'matricula',
                    'amount' => $defaultEnrollmentFee,
                    'month' => null,
                    'year' => $currentYear,
                    'due_date' => now(),
                    'status' => 'pending',
                    'notes' => 'Taxa de matrícula - Matrícula automática',
                ]);
            }

            return $enrollment;

        } catch (\Exception $e) {
            \Log::error('Erro ao criar matrícula automática: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Display a listing of students pending renewal.
     */
    public function renewalIndex(Request $request)
    {
        $query = Student::where('status', 'pending_renewal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(20);
        $classes = SchoolClass::active()->get();

        return view('enrollments.renewals.index', compact('students', 'classes'));
    }

    /**
     * Process student renewal for the current academic year.
     */
    public function renew(Request $request, Student $student)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'monthly_fee' => 'required|numeric|min:0',
            'payment_day' => 'required|integer|min:1|max:28',
            'enrollment_fee' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $currentYear = current_school_year();

            // Verificar se já existe matrícula ativa para este ano
            $existingEnrollment = Enrollment::where('student_id', $student->id)
                ->where('school_year', $currentYear)
                ->whereIn('status', ['active', 'pending'])
                ->first();

            if ($existingEnrollment) {
                return redirect()->back()
                    ->with('error', 'Este aluno já tem matrícula ativa para o ano letivo de '.$currentYear.'.');
            }

            // Create new enrollment
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $request->class_id,
                'school_year' => $currentYear,
                'enrollment_date' => now(),
                'monthly_fee' => $request->monthly_fee,
                'payment_day' => $request->payment_day,
                'status' => ($request->enrollment_fee > 0) ? 'pending' : 'active',
                'observations' => 'Renovação de matrícula para o ano letivo '.$currentYear,
            ]);

            // Create renewal fee payment if applicable
            if ($request->enrollment_fee > 0) {
                Payment::create([
                    'reference_number' => Payment::generateReference($student->id, 0, $currentYear),
                    'student_id' => $student->id,
                    'enrollment_id' => $enrollment->id,
                    'type' => 'matricula',
                    'amount' => $request->enrollment_fee,
                    'month' => null,
                    'year' => $currentYear,
                    'due_date' => now(),
                    'status' => 'pending',
                    'notes' => 'Taxa de renovação de matrícula - ano '.$currentYear,
                ]);
            }

            // Update student status back to active
            $student->update(['status' => 'active']);

            DB::commit();

            return redirect()->route('admin.enrollments.renewals')
                ->with('success', "Matrícula de {$student->full_name} renovada com sucesso para o ano letivo {$currentYear}!");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Erro ao processar renovação: '.$e->getMessage());
        }
    }
}
