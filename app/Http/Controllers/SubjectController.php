<?php

namespace App\Http\Controllers;

use App\Helpers\EducationLevel;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\ClassSubject;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of the subjects.
     */
    public function index(Request $request)
    {
        $this->authorize('view_subjects');

        $query = Subject::withCount(['classes', 'grades']);

        // Filtro por escola (super admin / territorial)
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grade_level') && $request->grade_level != '') {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }

        $subjects = $query->orderBy('grade_level')->orderBy('name')->paginate(20);

        $gradeLevels = EducationLevel::GRADES;

        $totalSubjects = Subject::count();
        $activeSubjects = Subject::active()->count();
        $schools = School::orderBy('name')->get();

        return view('subjects.index', compact(
            'subjects', 
            'gradeLevels',
            'totalSubjects',
            'activeSubjects',
            'schools'
        ));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $this->authorize('create_subjects');

        $gradeLevels = EducationLevel::GRADES;

        return view('subjects.create', compact('gradeLevels'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $this->authorize('create_subjects');

        $request->validate([
            'name' => 'required|string|max:50|unique:subjects,name',
            'code' => 'required|string|max:10|unique:subjects,code',
            'description' => 'nullable|string|max:255',
            'grade_level' => 'required|integer|min:0|max:12',
            'weekly_hours' => 'required|integer|min:1|max:20',
        ]);

        try {
            $subject = Subject::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'grade_level' => $request->grade_level,
                'weekly_hours' => $request->weekly_hours,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('subjects.show', $subject)
                ->with('success', 'Disciplina criada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar disciplina: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $this->authorize('view_subjects');

        $subject->load([
            'classes.teacher',
            'classSubjects.teacher',
            'grades.student'
        ]);

        // Estatísticas
        $stats = [
            'total_classes' => $subject->classes->count(),
            'total_grades' => $subject->grades->count(),
            'average_grade' => $subject->grades->avg('grade') ?? 0,
            'active_teachers' => $subject->classSubjects->unique('teacher_id')->count(),
        ];

        // Turmas que usam esta disciplina
        $classesUsingSubject = $subject->classes->unique();

        // Professores que lecionam esta disciplina
        $teachersTeachingSubject = $subject->classSubjects->map(function($classSubject) {
            return $classSubject->teacher;
        })->filter()->unique('id');

        return view('subjects.show', compact(
            'subject', 
            'stats',
            'classesUsingSubject',
            'teachersTeachingSubject'
        ));
    }

    /**
     * Show the form for editing the subject.
     */
    public function edit(Subject $subject)
    {
        $this->authorize('edit_subjects');

        $gradeLevels = EducationLevel::GRADES;

        return view('subjects.edit', compact('subject', 'gradeLevels'));
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        $this->authorize('edit_subjects');

        $request->validate([
            'name' => 'required|string|max:50|unique:subjects,name,' . $subject->id,
            'code' => 'required|string|max:10|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string|max:255',
            'grade_level' => 'required|integer|min:0|max:12',
            'weekly_hours' => 'required|integer|min:1|max:20',
        ]);

        try {
            $subject->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'grade_level' => $request->grade_level,
                'weekly_hours' => $request->weekly_hours,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('subjects.show', $subject)
                ->with('success', 'Disciplina atualizada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar disciplina: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        $this->authorize('delete_subjects');

        try {
            DB::beginTransaction();

            // Verificar se a disciplina está sendo usada
            if ($subject->classes()->exists() || $subject->grades()->exists()) {
                return back()->with('error', 'Não é possível excluir a disciplina. Ela está sendo usada em turmas ou possui notas registradas.');
            }

            // Remover associações com turmas
            $subject->classSubjects()->delete();

            // Excluir disciplina
            $subject->delete();

            DB::commit();

            return redirect()->route('subjects.index')
                ->with('success', 'Disciplina excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir disciplina: ' . $e->getMessage());
        }
    }

    /**
     * Show classes using this subject.
     */
    public function classes(Subject $subject)
    {
        $this->authorize('view_subjects');

        $subject->load(['classSubjects.class.teacher', 'classSubjects.teacher']);
        
        $availableClasses = ClassRoom::active()
            ->whereNotIn('id', $subject->classes->pluck('id'))
            ->get();

        $availableTeachers = Teacher::active()->get();

        return view('subjects.classes', compact(
            'subject', 
            'availableClasses',
            'availableTeachers'
        ));
    }

    /**
     * Assign subject to a class.
     */
    public function assignToClass(Request $request, Subject $subject)
    {
        $this->authorize('manage_subjects');

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        try {
            // Verificar se já existe esta associação
            $existing = ClassSubject::where('class_id', $request->class_id)
                ->where('subject_id', $subject->id)
                ->first();

            if ($existing) {
                return back()->with('error', 'Esta disciplina já está associada a esta turma.');
            }

            ClassSubject::create([
                'class_id' => $request->class_id,
                'subject_id' => $subject->id,
                'teacher_id' => $request->teacher_id,
            ]);

            return back()->with('success', 'Disciplina associada à turma com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao associar disciplina: ' . $e->getMessage());
        }
    }

    /**
     * Remove subject from a class.
     */
    public function removeFromClass(Subject $subject, ClassRoom $class)
    {
        $this->authorize('manage_subjects');

        try {
            ClassSubject::where('class_id', $class->id)
                ->where('subject_id', $subject->id)
                ->delete();

            return back()->with('success', 'Disciplina removida da turma com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao remover disciplina: ' . $e->getMessage());
        }
    }

    /**
     * Show subject grades statistics.
     */
    public function grades(Subject $subject)
    {
        $this->authorize('view_subjects');

        $subject->load(['grades.student', 'grades.teacher']);

        // Estatísticas de notas
        $gradeStats = [
            'total_grades' => $subject->grades->count(),
            'average_grade' => $subject->grades->avg('grade') ?? 0,
            'max_grade' => $subject->grades->max('grade') ?? 0,
            'min_grade' => $subject->grades->min('grade') ?? 0,
            'approval_rate' => $subject->grades->count() > 0 ? 
                round(($subject->grades->where('grade', '>=', 10)->count() / $subject->grades->count()) * 100, 1) : 0,
        ];

        // Distribuição de notas
        $gradeDistribution = [
            'excellent' => $subject->grades->where('grade', '>=', 14)->count(),
            'good' => $subject->grades->whereBetween('grade', [12, 13.9])->count(),
            'sufficient' => $subject->grades->whereBetween('grade', [10, 11.9])->count(),
            'insufficient' => $subject->grades->where('grade', '<', 10)->count(),
        ];

        return view('subjects.grades', compact(
            'subject',
            'gradeStats',
            'gradeDistribution'
        ));
    }
}