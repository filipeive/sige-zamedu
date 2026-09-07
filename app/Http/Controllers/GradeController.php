<?php

// app/Http/Controllers/GradeController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeRequest;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    /**
     * Display a listing of the grades.
     */
    /**
     * Display class grade matrix and editable Pauta.
     */
    public function index(Request $request)
    {
        $this->authorize('view_grades');

        $currentYear = (int) $request->get('year', current_school_year());
        $term = (int) $request->get('term', 1);
        $level = $request->get('level', 'all');

        $selectedClassId = $request->get('class_id');

        if ($selectedClassId) {
            $selectedClass = ClassRoom::with('subjects')->find($selectedClassId);
            if ($selectedClass) {
                $currentYear = (int) $request->get('year', $selectedClass->school_year);
            }
        }

        $classesQuery = ClassRoom::active();
        if ($level === 'preschool') {
            $classesQuery->whereIn('grade_level', [0, 1]);
        } elseif ($level === 'primary') {
            $classesQuery->whereIn('grade_level', [2, 3, 4, 5, 6, 7]);
        } elseif ($level === 'secondary') {
            $classesQuery->whereIn('grade_level', [8, 9, 10, 11, 12, 13]);
        }

        $classes = $classesQuery->orderBy('grade_level')->orderBy('name')->get();

        if (isset($selectedClass) && ! $classes->contains('id', $selectedClass->id)) {
            $selectedClass = null;
        }

        if (! isset($selectedClass)) {
            $selectedClass = $classes->first() ? ClassRoom::with('subjects')->find($classes->first()->id) : null;
        }

        $students = collect();
        $subjects = collect();
        $matrixGrades = [];
        $calculatedMatrix = [];

        if ($selectedClass) {
            $students = Student::whereHas('enrollments', function ($q) use ($selectedClass) {
                $q->where('class_id', $selectedClass->id);
            })->orderBy('first_name')->orderBy('last_name')->get();

            if ($students->isEmpty()) {
                $students = $selectedClass->students;
            }

            $subjects = $selectedClass->subjects->isEmpty()
                ? Subject::active()->get()
                : $selectedClass->subjects;

            $allGrades = Grade::where('class_id', $selectedClass->id)
                ->where('term', $term)
                ->where('year', $currentYear)
                ->get();

            foreach ($allGrades as $g) {
                $matrixGrades[$g->student_id][$g->subject_id][$g->assessment_type] = $g->grade;
            }

            foreach ($students as $student) {
                foreach ($subjects as $subject) {
                    $macs = Grade::calculateMACS($student->id, $subject->id, $selectedClass->id, $term, $allGrades);
                    $mt = Grade::calculateMT($student->id, $subject->id, $selectedClass->id, $term, $allGrades);

                    $acpGrades = $allGrades->filter(function ($g) use ($student, $subject, $term) {
                        return (int) $g->student_id === $student->id
                            && (int) $g->subject_id === $subject->id
                            && (int) $g->term === $term
                            && $g->assessment_type === Grade::TYPE_ACP;
                    });
                    $acp = $acpGrades->isNotEmpty() ? (float) $acpGrades->first()->grade : null;

                    $calculatedMatrix[$student->id][$subject->id] = [
                        'macs' => $macs,
                        'acp' => $acp,
                        'mt' => $mt,
                    ];
                }
            }
        }

        $assessmentTypes = [
            'ACS1' => 'ACS 1 (Avaliação Contínua 1)',
            'ACS2' => 'ACS 2 (Avaliação Contínua 2)',
            'ACS3' => 'ACS 3 (Avaliação Contínua 3)',
            'ACP' => 'ACP (Avaliação de Controlo Parcial)',
            'ACF' => 'ACF / Exame Final',
        ];

        return view('grades.index', compact(
            'classes',
            'selectedClass',
            'students',
            'subjects',
            'matrixGrades',
            'calculatedMatrix',
            'term',
            'currentYear',
            'level',
            'assessmentTypes'
        ));
    }

    /**
     * Dashboard central de gestão de notas — ponto de entrada único.
     */
    public function dashboard(Request $request)
    {
        $this->authorize('view_grades');

        $level = $request->get('level', 'all');
        $currentYear = (int) $request->get('year', current_school_year());
        $term = (int) $request->get('term', 1);

        $classes = ClassRoom::active()
            ->with(['teacher', 'classSubjects.subject', 'classSubjects.teacher'])
            ->byEducationLevel($level)
            ->orderBy('grade_level')->orderBy('name')
            ->get();

        // Stats
        $totalGrades = Grade::where('year', $currentYear)->count();
        $average = Grade::where('year', $currentYear)->avg('grade');
        $passingCount = Grade::where('year', $currentYear)->where('grade', '>=', 10)->count();
        $passingRate = $totalGrades > 0 ? round(($passingCount / $totalGrades) * 100, 1) : 0;

        $levelStats = [
            'all' => ClassRoom::active()->count(),
            'preschool' => ClassRoom::active()->byEducationLevel('preschool')->count(),
            'primary' => ClassRoom::active()->byEducationLevel('primary')->count(),
            'secondary' => ClassRoom::active()->byEducationLevel('secondary')->count(),
        ];

        return view('grades.dashboard', compact(
            'classes', 'level', 'currentYear', 'term', 'totalGrades', 'average', 'passingRate', 'levelStats'
        ));
    }

    /**
     * Show the form for creating a new grade.
     */
    public function create()
    {
        $this->authorize('create_grades');

        $students = Student::active()->with('currentEnrollment.class')->get();
        $subjects = Subject::active()->get();
        $classes = ClassRoom::active()->get();
        $currentYear = current_school_year();

        return view('grades.create', compact('students', 'subjects', 'classes', 'currentYear'));
    }

    /**
     * Store a newly created grade.
     */
    public function store(StoreGradeRequest $request)
    {
        $this->authorize('create_grades');

        try {
            $user = $request->user();

            // Verificar se o aluno está ativo
            $student = Student::findOrFail($request->student_id);
            if (! in_array($student->status, ['active', 'pending_renewal'])) {
                return back()->with('error', 'Não é possível atribuir nota a um aluno inativo.')->withInput();
            }

            // Determinar teacher_id
            $teacherId = $user->teacher?->id;
            if (! $teacherId && ($user->hasRole('admin') || $user->hasRole('super-admin') || $user->can('create_grades'))) {
                $classTeacherId = ClassRoom::find($request->class_id)?->teacher_id;
                $teacherId = $classTeacherId ?? \App\Models\Teacher::first()?->id;
            }

            if (! $teacherId) {
                return back()->with('error', 'Apenas professores podem atribuir notas.')->withInput();
            }

            // Verificar se o aluno está matriculado na turma
            $isEnrolled = \App\Models\Enrollment::where('student_id', $request->student_id)
                ->where('class_id', $request->class_id)
                ->where('status', 'active')
                ->exists();

            if (! $isEnrolled) {
                return back()->with('error', 'O aluno não está matrículado nesta turma.')->withInput();
            }

            $grade = Grade::create([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'grade' => $request->grade,
                'assessment_type' => $request->assessment_type,
                'term' => $request->term,
                'year' => $request->year,
                'date_recorded' => $request->date_recorded,
                'teacher_id' => $teacherId,
                'comments' => $request->comments,
            ]);

            return redirect()->route('grades.index')
                ->with('success', 'Nota atribuída com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar nota: '.$e->getMessage(), [
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Erro ao atribuir nota. Contacte o administrador.')->withInput();
        }
    }

    /**
     * Show the form for batch creating grades.
     */
    public function batchCreate(Request $request)
    {
        $this->authorize('create_grades');

        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');
        $term = $request->get('term', 1);
        $year = $request->get('year', current_school_year());
        $assessmentType = $request->get('assessment_type', 'test');

        $classes = ClassRoom::active()->get();
        $subjects = Subject::active()->get();

        $students = collect();
        $existingGrades = collect();

        if ($classId && $subjectId) {
            $selectedClassObj = ClassRoom::find($classId);
            $students = Student::whereHas('enrollments', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })->orderBy('first_name')->orderBy('last_name')->get();

            if ($students->isEmpty() && $selectedClassObj) {
                $students = $selectedClassObj->students;
            }

            // Buscar notas existentes para esta combinação (garantindo separação estrita entre ACS1, ACS2 e ACS3)
            $typeQuery = [$assessmentType];
            if ($assessmentType === 'ACS1') {
                $typeQuery = ['ACS1', 'test'];
            } elseif ($assessmentType === 'ACS2') {
                $typeQuery = ['ACS2'];
            } elseif ($assessmentType === 'ACS3') {
                $typeQuery = ['ACS3'];
            } elseif (in_array($assessmentType, ['ACP', 'exam'])) {
                $typeQuery = ['ACP', 'exam'];
            } elseif (in_array($assessmentType, ['ACF', 'final'])) {
                $typeQuery = ['ACF', 'final'];
            }

            $existingGrades = Grade::whereIn('student_id', $students->pluck('id'))
                ->where('subject_id', $subjectId)
                ->where('term', $term)
                ->where('year', $year)
                ->whereIn('assessment_type', array_unique($typeQuery))
                ->get()
                ->keyBy('student_id');
        }

        return view('grades.batch-create', compact(
            'classes',
            'subjects',
            'students',
            'existingGrades',
            'classId',
            'subjectId',
            'term',
            'year',
            'assessmentType'
        ));
    }

    /**
     * Store batch grades.
     */
    public function batchStore(Request $request)
    {
        $this->authorize('create_grades');

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2020|max:2030',
            'assessment_type' => 'required|in:test,assignment,exam,project,participation,ACS1,ACS2,ACS3,ACP,ACF,behavioral',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.grade' => 'nullable|numeric|min:0|max:20',
        ]);

        try {
            DB::beginTransaction();

            $user = $request->user();
            $teacherId = $user->teacher?->id;

            if (! $teacherId && ($user->hasRole('admin') || $user->hasRole('super-admin') || $user->can('create_grades'))) {
                $classTeacherId = ClassRoom::find($request->class_id)?->teacher_id;
                $teacherId = $classTeacherId ?? \App\Models\Teacher::first()?->id;
            }

            if (! $teacherId) {
                throw new \Exception('Não foi possível identificar o professor responsável. Certifique-se de que existe pelo menos um professor registado.');
            }

            $successCount = 0;
            $updatedCount = 0;

            foreach ($request->grades as $gradeData) {
                if (isset($gradeData['grade']) && $gradeData['grade'] !== '' && $gradeData['grade'] !== null) {
                    $typeQuery = [$request->assessment_type];
                    if ($request->assessment_type === 'ACS1') {
                        $typeQuery = ['ACS1', 'test'];
                    } elseif ($request->assessment_type === 'ACS2') {
                        $typeQuery = ['ACS2'];
                    } elseif ($request->assessment_type === 'ACS3') {
                        $typeQuery = ['ACS3'];
                    } elseif (in_array($request->assessment_type, ['ACP', 'exam'])) {
                        $typeQuery = ['ACP', 'exam'];
                    } elseif (in_array($request->assessment_type, ['ACF', 'final'])) {
                        $typeQuery = ['ACF', 'final'];
                    }

                    // Verificar se já existe uma nota para este aluno
                    $existingGrade = Grade::where('student_id', $gradeData['student_id'])
                        ->where('subject_id', $request->subject_id)
                        ->where('term', $request->term)
                        ->where('year', $request->year)
                        ->whereIn('assessment_type', array_unique($typeQuery))
                        ->first();

                    if ($existingGrade) {
                        // Atualizar nota existente
                        $existingGrade->update([
                            'grade' => $gradeData['grade'],
                            'date_recorded' => now(),
                            'teacher_id' => $teacherId,
                            'comments' => $gradeData['comments'] ?? null,
                        ]);
                        $updatedCount++;
                    } else {
                        // Criar nova nota
                        Grade::create([
                            'student_id' => $gradeData['student_id'],
                            'class_id' => $request->class_id,
                            'subject_id' => $request->subject_id,
                            'grade' => $gradeData['grade'],
                            'assessment_type' => $request->assessment_type,
                            'term' => $request->term,
                            'year' => $request->year,
                            'date_recorded' => now(),
                            'teacher_id' => $teacherId,
                            'comments' => $gradeData['comments'] ?? null,
                        ]);
                        $successCount++;
                    }
                }
            }

            DB::commit();

            $message = 'Notas processadas com sucesso! ';
            $message .= "{$successCount} novas notas adicionadas, ";
            $message .= "{$updatedCount} notas atualizadas.";

            return redirect()->route('grades.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar notas em lote: '.$e->getMessage());

            return back()->with('error', 'Erro ao processar notas: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified grade.
     */
    public function edit(Grade $grade)
    {
        $this->authorize('edit_grades');

        // Verificar se o usuário pode editar esta nota
        $user = request()->user();
        if ($user->hasRole('teacher') && $grade->teacher_id !== $user->teacher?->id) {
            return redirect()->route('grades.index')
                ->with('error', 'Você só pode editar as suas próprias notas.');
        }

        $students = Student::active()->get();
        $subjects = Subject::active()->get();

        return view('grades.edit', compact('grade', 'students', 'subjects'));
    }

    /**
     * Update the specified grade.
     */
    public function update(Request $request, Grade $grade)
    {
        $this->authorize('edit_grades');

        // Verificar se o usuário pode editar esta nota
        $user = $request->user();
        if ($user->hasRole('teacher') && $grade->teacher_id !== $user->teacher?->id) {
            return redirect()->route('grades.index')
                ->with('error', 'Você só pode editar as suas próprias notas.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'grade' => 'required|numeric|min:0|max:20',
            'assessment_type' => 'required|in:test,assignment,exam,project,participation,ACS1,ACS2,ACS3,ACP,ACF,behavioral',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2020|max:2030',
            'class_id' => 'required|exists:classes,id',
            'date_recorded' => 'required|date',
            'comments' => 'nullable|string|max:500',
        ]);

        try {
            $grade->update([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'grade' => $request->grade,
                'assessment_type' => $request->assessment_type,
                'term' => $request->term,
                'year' => $request->year,
                'date_recorded' => $request->date_recorded,
                'comments' => $request->comments,
            ]);

            return redirect()->route('grades.index')
                ->with('success', 'Nota atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar nota: '.$e->getMessage());

            return back()->with('error', 'Erro ao atualizar nota. Contacte o administrador.')->withInput();
        }
    }

    /**
     * Display student report card.
     */
    public function reportCard(Student $student)
    {
        $this->authorize('view_grades');

        $student->load(['grades.subject', 'currentEnrollment.class']);

        $currentYear = current_school_year();
        $terms = [1, 2, 3];

        $gradesByTerm = [];
        foreach ($terms as $term) {
            $gradesByTerm[$term] = $student->grades()
                ->where('year', $currentYear)
                ->where('term', $term)
                ->with('subject')
                ->get()
                ->groupBy('subject_id');
        }

        $subjects = Subject::active()->get();
        $currentEnrollment = $student->currentEnrollment;

        return view('grades.report-card', compact(
            'student',
            'gradesByTerm',
            'subjects',
            'currentEnrollment',
            'currentYear'
        ));
    }

    /**
     * Display class grade report.
     */
    public function classReport(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');

        $year = (int) $request->get('year', $class->school_year ?: current_school_year());

        $students = Student::whereHas('enrollments', function ($q) use ($class) {
            $q->where('class_id', $class->id);
        })->orderBy('first_name')->orderBy('last_name')->get();

        if ($students->isEmpty()) {
            $students = $class->students;
        }

        $subjects = $class->subjects->isEmpty()
            ? Subject::active()->get()
            : $class->subjects;

        $allGrades = Grade::where('class_id', $class->id)
            ->where('year', $year)
            ->get();

        $matrix = [];
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                for ($term = 1; $term <= 3; $term++) {
                    $mt = Grade::calculateMT($student->id, $subject->id, $class->id, $term, $allGrades);
                    $matrix[$student->id][$subject->id][$term] = $mt;
                }
            }
        }

        $terms = [1, 2, 3];

        return view('grades.class-report', compact(
            'class',
            'students',
            'subjects',
            'matrix',
            'terms',
            'year'
        ));
    }

    /**
     * Display grade sheet for a class.
     */
    public function gradeSheet(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');

        $subjectId = $request->get('subject_id');
        $term = $request->get('term', 1);
        $year = $request->get('year', current_school_year());

        $class->load([
            'students' => function ($query) {
                $query->orderBy('first_name')->orderBy('last_name');
            },
        ]);

        $subjects = $class->subjects;
        $selectedSubject = $subjectId ? Subject::find($subjectId) : $subjects->first();

        $grades = [];
        if ($selectedSubject) {
            $grades = Grade::whereIn('student_id', $class->students->pluck('id'))
                ->where('subject_id', $selectedSubject->id)
                ->where('term', $term)
                ->where('year', $year)
                ->get()
                ->keyBy('student_id');
        }

        return view('grades.grade-sheet', compact(
            'class',
            'subjects',
            'selectedSubject',
            'grades',
            'term',
            'year'
        ));
    }

    /**
     * Get grade statistics.
     */
    private function getGradeStatistics($grades)
    {
        if ($grades->isEmpty()) {
            return [
                'average' => 0,
                'highest' => 0,
                'lowest' => 0,
                'count' => 0,
            ];
        }

        return [
            'average' => round($grades->avg('grade'), 2),
            'highest' => $grades->max('grade'),
            'lowest' => $grades->min('grade'),
            'count' => $grades->count(),
        ];
    }
}
