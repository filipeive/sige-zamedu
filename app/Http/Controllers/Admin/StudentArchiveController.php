<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentArchiveController extends Controller
{
    /**
     * Listar estudantes que saíram da escola.
     */
    public function index(Request $request)
    {
        $query = Student::whereIn('status', ['transferred', 'graduated', 'inactive']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('exit_status')) {
            $query->where('status', $request->exit_status);
        }

        $students = $query->latest('updated_at')->paginate(20);
        $stats = $this->getStats();

        return view('admin.students-archive.index', compact('students', 'stats'));
    }

    /**
     * Mostrar detalhes de um estudante arquivado.
     */
    public function show(Student $student)
    {
        $student->load(['enrollments.class', 'grades.subject', 'payments']);

        return view('admin.students-archive.show', compact('student'));
    }

    /**
     * Reativar um estudante (nova matrícula).
     */
    public function reactivate(Request $request, Student $student)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'monthly_fee' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $currentYear = current_school_year();

            // Verificar se já existe matrícula ativa
            $existing = Enrollment::where('student_id', $student->id)
                ->where('school_year', $currentYear)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'Este aluno já tem uma matrícula para o ano '.$currentYear.'.');
            }

            // Criar nova matrícula
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $request->class_id,
                'school_year' => $currentYear,
                'enrollment_date' => now(),
                'monthly_fee' => $request->monthly_fee,
                'payment_day' => $request->payment_day ?? 5,
                'status' => 'pending',
                'observations' => 'Reativação - '.($request->reason ?? 'Motivo não especificado'),
            ]);

            // Reativar estudante
            $student->update(['status' => 'pending_renewal']);

            DB::commit();

            return redirect()->route('admin.students-archive.index')
                ->with('success', "{$student->full_name} reativado! Aguardando pagamento da matrícula.");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Erro ao reativar: '.$e->getMessage());
        }
    }

    /**
     * Exportar lista de estudantes arquivados.
     */
    public function export()
    {
        $students = Student::whereIn('status', ['transferred', 'graduated', 'inactive'])
            ->with('enrollments')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Por agora, retornamos JSON - pode ser implementado com Excel depois
        return response()->json([
            'total' => $students->count(),
            'students' => $students->map(fn ($s) => [
                'name' => $s->full_name,
                'number' => $s->student_number,
                'status' => $s->status,
                'last_enrollment' => $s->enrollments->last()?->school_year,
            ]),
        ]);
    }

    /**
     * Obter estatísticas.
     */
    private function getStats(): array
    {
        return [
            'total' => Student::whereIn('status', ['transferred', 'graduated', 'inactive'])->count(),
            'transferred' => Student::where('status', 'transferred')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'inactive' => Student::where('status', 'inactive')->count(),
        ];
    }
}
