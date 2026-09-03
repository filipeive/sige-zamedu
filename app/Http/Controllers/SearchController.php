<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Payment;
use App\Models\Enrollment;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return redirect()->back()->with('warning', 'Digite pelo menos 2 caracteres para pesquisar.');
        }

        $results = [
            'students' => collect(),
            'teachers' => collect(),
            'classes' => collect(),
            'subjects' => collect(),
            'payments' => collect()
        ];

        // Buscar alunos (se tiver permissão)
        if (auth()->user()->can('view_students')) {
            $results['students'] = Student::where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('student_number', 'like', "%{$query}%")
                      ->orWhere('emergency_phone', 'like', "%{$query}%");
                })
                ->active()
                ->with(['currentEnrollment.class'])
                ->limit(20)
                ->get();
        }

        // Buscar professores (se tiver permissão)
        if (auth()->user()->can('view_teachers')) {
            $results['teachers'] = Teacher::where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%")
                      ->orWhere('specialization', 'like', "%{$query}%");
                })
                ->active()
                ->limit(20)
                ->get();
        }

        // Buscar turmas (se tiver permissão)
        if (auth()->user()->can('view_classes')) {
            $results['classes'] = ClassRoom::where('name', 'like', "%{$query}%")
                ->orWhere('classroom', 'like', "%{$query}%")
                ->active()
                ->currentYear()
                ->with(['teacher', 'students'])
                ->limit(10)
                ->get();
        }

        // Buscar disciplinas
        if (auth()->user()->can('view_subjects')) {
            $results['subjects'] = Subject::where('name', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->active()
                ->with(['teacher'])
                ->limit(10)
                ->get();
        }

        // Buscar pagamentos (se escola for privada e tiver permissão)
        if (is_private_school() && auth()->user()->can('view_payments')) {
            $results['payments'] = Payment::where('reference_number', 'like', "%{$query}%")
                ->orWhereHas('student', function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->with(['student'])
                ->latest()
                ->limit(10)
                ->get();
        }

        $totalResults = array_sum(array_map('count', $results));

        return view('search.results', compact('results', 'query', 'totalResults'));
    }

    public function liveSearch(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Busca rápida de alunos
        if (auth()->user()->can('view_students')) {
            $students = Student::where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('student_number', 'like', "%{$query}%");
                })
                ->active()
                ->limit(5)
                ->get()
                ->map(function($student) {
                    return [
                        'type' => 'student',
                        'title' => $student->full_name,
                        'subtitle' => $student->student_number,
                        'url' => route('students.show', $student),
                        'icon' => 'fas fa-user-graduate'
                    ];
                });

            $results = array_merge($results, $students->toArray());
        }

        // Busca rápida de professores
        if (auth()->user()->can('view_teachers')) {
            $teachers = Teacher::where(function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->active()
                ->limit(5)
                ->get()
                ->map(function($teacher) {
                    return [
                        'type' => 'teacher',
                        'title' => $teacher->full_name,
                        'subtitle' => $teacher->specialization,
                        'url' => route('teachers.show', $teacher),
                        'icon' => 'fas fa-chalkboard-teacher'
                    ];
                });

            $results = array_merge($results, $teachers->toArray());
        }

        return response()->json($results);
    }

    /**
     * Resposta JSON para o Command Palette (Ctrl+K)
     */
    public function quickSearch(Request $request)
    {
        $query = trim($request->get('q', ''));
        $results = [];

        // Atalhos do Sistema quando campo estiver com busca curta
        if (empty($query) || strlen($query) < 2) {
            $shortcuts = [
                ['type' => 'shortcut', 'title' => 'Dashboard', 'subtitle' => 'Painel Principal', 'url' => route('dashboard'), 'icon' => 'fas fa-home'],
                ['type' => 'shortcut', 'title' => 'Portaria Digital', 'subtitle' => 'Controlo de Acesso Escolar', 'url' => route('gatekeeper.index'), 'icon' => 'fas fa-id-card-clip'],
                ['type' => 'shortcut', 'title' => 'Alunos', 'subtitle' => 'Lista de Estudantes', 'url' => route('students.index'), 'icon' => 'fas fa-user-graduate'],
                ['type' => 'shortcut', 'title' => 'Central de Relatórios', 'subtitle' => 'Analytics e Pautas', 'url' => route('reports.index'), 'icon' => 'fas fa-chart-pie'],
                ['type' => 'shortcut', 'title' => 'Comunicados', 'subtitle' => 'Mural de Avisos', 'url' => route('communications.index'), 'icon' => 'fas fa-bullhorn'],
            ];
            return response()->json($shortcuts);
        }

        // 1. Alunos
        if (auth()->user()->can('view_students')) {
            $students = Student::where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('student_number', 'like', "%{$query}%")
                ->limit(6)
                ->get()
                ->map(fn($st) => [
                    'type' => 'student',
                    'title' => $st->full_name,
                    'subtitle' => 'Nº: ' . $st->student_number,
                    'url' => route('students.show', $st->id),
                    'icon' => 'fas fa-user-graduate'
                ]);
            $results = array_merge($results, $students->toArray());
        }

        // 2. Professores
        if (auth()->user()->can('view_teachers')) {
            $teachers = Teacher::where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->limit(4)
                ->get()
                ->map(fn($tc) => [
                    'type' => 'teacher',
                    'title' => $tc->full_name,
                    'subtitle' => $tc->specialization ?? 'Docente',
                    'url' => route('teachers.show', $tc->id),
                    'icon' => 'fas fa-chalkboard-teacher'
                ]);
            $results = array_merge($results, $teachers->toArray());
        }

        // 3. Turmas
        if (auth()->user()->can('view_classes')) {
            $classes = ClassRoom::where('name', 'like', "%{$query}%")
                ->limit(4)
                ->get()
                ->map(fn($cl) => [
                    'type' => 'class',
                    'title' => 'Turma: ' . $cl->name,
                    'subtitle' => $cl->grade_level . 'ª Classe · ' . ($cl->classroom ?? 'Sala N/A'),
                    'url' => route('classes.show', $cl->id),
                    'icon' => 'fas fa-school'
                ]);
            $results = array_merge($results, $classes->toArray());
        }

        // 4. Pagamentos (apenas escola privada)
        if (is_private_school() && auth()->user()->can('view_payments')) {
            $payments = Payment::where('reference_number', 'like', "%{$query}%")
                ->orWhereHas('student', function($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%");
                })
                ->with('student')
                ->latest()
                ->limit(4)
                ->get()
                ->map(fn($p) => [
                    'type' => 'payment',
                    'title' => 'Pagamento: ' . $p->reference_number,
                    'subtitle' => ($p->student ? $p->student->full_name . ' · ' : '') . number_format($p->amount, 2, ',', '.') . ' MT',
                    'url' => route('payments.show', $p->id),
                    'icon' => 'fas fa-money-bill-wave'
                ]);
            $results = array_merge($results, $payments->toArray());
        }

        // 5. Despesas
        if (auth()->user()->can('manage_expenses')) {
            $expenses = \App\Models\Expense::where('description', 'like', "%{$query}%")
                ->latest()
                ->limit(4)
                ->get()
                ->map(fn($ex) => [
                    'type' => 'expense',
                    'title' => 'Despesa: ' . $ex->description,
                    'subtitle' => number_format($ex->amount, 2, ',', '.') . ' MT',
                    'url' => route('expenses.show', $ex->id),
                    'icon' => 'fas fa-receipt'
                ]);
            $results = array_merge($results, $expenses->toArray());
        }

        return response()->json($results);
    }
}