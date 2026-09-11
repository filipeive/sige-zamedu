<?php

namespace App\Http\Controllers;

use App\Helpers\EducationLevel;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\ClassSubject;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoom::with(['teacher', 'assistantTeacher'])
            ->withCount([
                'enrollments as active_students_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'classSubjects as subjects_count',
            ]);

        // Filtro por nível de ensino (tabs server-side)
        $level = $request->get('level', 'all');
        $query->byEducationLevel($level);

        // Filtro por escola (super admin / territorial)
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Filtros adicionais
        if ($request->has('teacher_id') && $request->teacher_id != '') {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('school_year') && $request->school_year != '') {
            $query->where('school_year', $request->school_year);
        }

        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('teaching_mode') && $request->teaching_mode != '') {
            $query->where('teaching_mode', $request->teaching_mode);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('classroom', 'like', "%{$search}%");
            });
        }

        $classes = $query->orderBy('grade_level')->orderBy('name')->paginate(20)->withQueryString();

        // Stats por nível e ciclo MINEDH
        $levelStats = [
            'all'       => ClassRoom::count(),
            'preschool' => ClassRoom::byEducationLevel('preschool')->count(),
            'primary'   => ClassRoom::byEducationLevel('primary')->count(),
            'secondary' => ClassRoom::byEducationLevel('secondary')->count(),
            'ep1'       => ClassRoom::byEducationLevel('ep1')->count(),
            'ep2'       => ClassRoom::byEducationLevel('ep2')->count(),
            'esg1'      => ClassRoom::byEducationLevel('esg1')->count(),
            'esg2'      => ClassRoom::byEducationLevel('esg2')->count(),
        ];

        $teachers = Teacher::active()->get();
        $currentYear = current_school_year();
        $schools = School::orderBy('name')->get();

        $gradeLevels = $this->getGradeLevelsList();

        return view('classes.index', compact(
            'classes',
            'teachers',
            'currentYear',
            'gradeLevels',
            'level',
            'levelStats',
            'schools'
        ));
    }

    public function create()
    {
        $teachers = Teacher::active()->get();
        $subjects = Subject::active()->get();
        $currentYear = current_school_year();
        $gradeLevels = $this->getGradeLevelsList();

        return view('classes.create', compact(
            'teachers',
            'subjects',
            'currentYear',
            'gradeLevels'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
            'grade_level' => 'required|integer|min:0|max:12',
            'shift' => 'required|in:morning,afternoon,night',
            'teaching_mode' => 'nullable|in:single_teacher,subject_based',
            'teacher_id' => 'nullable|exists:teachers,id',
            'assistant_teacher_id' => 'nullable|exists:teachers,id|different:teacher_id',
            'max_students' => 'required|integer|min:1|max:50',
            'classroom' => 'nullable|string|max:50',
            'school_year' => 'required|integer|min:2020|max:2030',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        try {
            DB::beginTransaction();

            // Auto-resolver teaching_mode se não enviado
            $teachingMode = $request->teaching_mode ?: ($this->isSecondaryGrade((int) $request->grade_level) ? 'subject_based' : 'single_teacher');

            $class = ClassRoom::create([
                'name' => $request->name,
                'grade_level' => $request->grade_level,
                'shift' => $request->shift,
                'teaching_mode' => $teachingMode,
                'teacher_id' => $teachingMode === 'single_teacher' ? $request->teacher_id : null,
                'assistant_teacher_id' => $teachingMode === 'single_teacher' ? $request->assistant_teacher_id : null,
                'max_students' => $request->max_students,
                'current_students' => 0,
                'classroom' => $request->classroom,
                'school_year' => $request->school_year,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Associar disciplinas (para secundário é obrigatório atribuir professor por disciplina)
            if ($request->has('subjects')) {
                foreach ($request->subjects as $subjectId) {
                    ClassSubject::create([
                        'class_id' => $class->id,
                        'subject_id' => $subjectId,
                        'teacher_id' => $request->teacher_id,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('classes.show', $class->id)
                ->with('success', 'Turma criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao criar turma: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ClassRoom $class)
    {
        $class->load([
            'teacher',
            'assistantTeacher',
            'enrollments.student',
            'classSubjects.subject',
            'classSubjects.teacher'
        ]);

        $stats = [
            'total_students' => $class->enrollments()->where('status', 'active')->count(),
            'capacity_percentage' => $class->capacity_percentage,
            'male_students' => $class->students()->where('gender', 'male')->count(),
            'female_students' => $class->students()->where('gender', 'female')->count(),
            'average_age' => $this->calculateAverageAge($class),
        ];

        // Próximos aniversários
        $upcomingBirthdays = $this->getUpcomingBirthdays($class);

        // Para atribuição por disciplina (secundário)
        $availableTeachers = Teacher::active()->orderBy('first_name')->get();

        return view('classes.show', compact(
            'class', 'stats', 'upcomingBirthdays', 'availableTeachers'
        ));
    }

    public function edit(ClassRoom $class)
    {
        $teachers = Teacher::active()->get();
        $subjects = Subject::active()->get();
        $class->load(['classSubjects', 'assistantTeacher']);

        // Obter IDs das disciplinas já associadas
        $currentSubjectIds = $class->classSubjects->pluck('subject_id')->toArray();

        $gradeLevels = $this->getGradeLevelsList();

        return view('classes.edit', compact(
            'class',
            'teachers',
            'subjects',
            'currentSubjectIds',
            'gradeLevels'
        ));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')->ignore($class->id),
            ],
            'grade_level' => 'required|integer|min:0|max:12',
            'shift' => 'required|in:morning,afternoon,night',
            'teaching_mode' => 'nullable|in:single_teacher,subject_based',
            'teacher_id' => 'nullable|exists:teachers,id',
            'assistant_teacher_id' => 'nullable|exists:teachers,id|different:teacher_id',
            'max_students' => 'required|integer|min:1|max:50',
            'classroom' => 'nullable|string|max:50',
            'school_year' => 'required|integer|min:2020|max:2030',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        try {
            DB::beginTransaction();

            $teachingMode = $request->teaching_mode ?: $class->resolveTeachingMode();

            $class->update([
                'name' => $request->name,
                'grade_level' => $request->grade_level,
                'shift' => $request->shift,
                'teaching_mode' => $teachingMode,
                'teacher_id' => $teachingMode === 'single_teacher' ? $request->teacher_id : null,
                'assistant_teacher_id' => $teachingMode === 'single_teacher' ? $request->assistant_teacher_id : null,
                'max_students' => $request->max_students,
                'classroom' => $request->classroom,
                'school_year' => $request->school_year,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Sincronizar disciplinas (atribuição por disciplina)
            if ($request->has('subjects')) {
                ClassSubject::where('class_id', $class->id)->delete();
                foreach ($request->subjects as $subjectId) {
                    ClassSubject::create([
                        'class_id' => $class->id,
                        'subject_id' => $subjectId,
                        'teacher_id' => $request->teacher_id,
                    ]);
                }
            } else {
                ClassSubject::where('class_id', $class->id)->delete();
            }

            DB::commit();

            return redirect()->route('classes.show', $class->id)
                ->with('success', 'Turma atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao atualizar turma: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(ClassRoom $class)
    {
        try {
            // Verificar se a turma tem alunos matriculados
            $activeEnrollments = $class->enrollments()->where('status', 'active')->count();
            if ($activeEnrollments > 0) {
                return redirect()->back()
                    ->with('error', 'Não é possível excluir a turma. Existem alunos matriculados.');
            }

            // Verificar se há registros de presença
            $hasAttendance = \App\Models\Attendance::where('class_id', $class->id)->exists();
            if ($hasAttendance) {
                return redirect()->back()
                    ->with('error', 'Não é possível excluir a turma. Existem registros de presença associados.');
            }

            DB::beginTransaction();

            // Remover disciplinas associadas
            ClassSubject::where('class_id', $class->id)->delete();

            // Remover matrículas
            $class->enrollments()->delete();

            // Excluir turma
            $class->delete();

            DB::commit();

            return redirect()->route('classes.index')
                ->with('success', 'Turma excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao excluir turma: ' . $e->getMessage());
        }
    }

    public function students(ClassRoom $class)
    {
        $students = $class->students()
            ->with([
                'enrollments' => function ($query) use ($class) {
                    $query->where('class_id', $class->id)
                        ->where('status', 'active');
                }
            ])
            ->get();

        $availableStudents = Student::active()
            ->whereDoesntHave('enrollments', function ($query) use ($class) {
                $query->where('school_year', $class->school_year)
                    ->whereIn('status', ['active', 'pending']);
            })
            ->get();

        // Conta por gênero — normalizado (aceita “male”/“Masculino” etc.)
        $maleCount = $students->filter(function ($s) {
            return in_array(strtolower($s->gender), ['male', 'masculino']);
        })->count();

        $femaleCount = $students->filter(function ($s) {
            return in_array(strtolower($s->gender), ['female', 'feminino']);
        })->count();

        return view('classes.students', compact(
            'class',
            'students',
            'availableStudents',
            'maleCount',
            'femaleCount'
        ));
    }


    public function addStudent(Request $request, ClassRoom $class)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Verificar se o aluno já está matriculado nesta turma/ano
            $existingEnrollment = Enrollment::where('student_id', $request->student_id)
                ->where('school_year', $class->school_year)
                ->whereIn('status', ['active', 'pending'])
                ->first();

            if ($existingEnrollment) {
                return redirect()->back()
                    ->with('error', 'Este aluno já está matriculado em outra turma para este ano letivo.');
            }

            // Criar matrícula
            $enrollment = Enrollment::create([
                'student_id' => $request->student_id,
                'class_id' => $class->id,
                'school_year' => $class->school_year,
                'enrollment_date' => now()->format('Y-m-d'),
                'monthly_fee' => $request->monthly_fee,
                'payment_day' => 10, // Dia padrão
                'status' => 'active',
            ]);

            // Atualizar contador de alunos na turma
            $class->update([
                'current_students' => $class->enrollments()->where('status', 'active')->count()
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Aluno adicionado à turma com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao adicionar aluno: ' . $e->getMessage());
        }
    }

    public function removeStudent(ClassRoom $class, Student $student)
    {
        try {
            DB::beginTransaction();

            // Encontrar e cancelar a matrícula
            $enrollment = Enrollment::where('student_id', $student->id)
                ->where('class_id', $class->id)
                ->where('status', 'active')
                ->first();

            if ($enrollment) {
                $enrollment->update([
                    'status' => 'cancelled',
                    'cancellation_date' => now()->format('Y-m-d'),
                ]);

                // Atualizar contador de alunos na turma
                $class->update([
                    'current_students' => $class->enrollments()->where('status', 'active')->count()
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Aluno removido da turma com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao remover aluno: ' . $e->getMessage());
        }
    }

    public function attendance(ClassRoom $class)
    {
        $attendanceDate = request('date', today()->format('Y-m-d'));
        $students = $class->students;

        $existingAttendance = \App\Models\Attendance::where('class_id', $class->id)
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->keyBy('student_id');

        return view('classes.attendance', compact(
            'class',
            'students',
            'attendanceDate',
            'existingAttendance'
        ));
    }

    /**
     * Atribuir professores por disciplina (turma do secundário ou onde teaching_mode = subject_based).
     */
    public function assignSubjects(Request $request, ClassRoom $class)
    {
        $this->authorize('manage_classes');

        $data = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.class_subject_id' => 'required|exists:class_subjects,id',
            'assignments.*.teacher_id' => 'required|exists:teachers,id',
        ]);

        try {
            DB::beginTransaction();
            foreach ($data['assignments'] as $row) {
                $cs = ClassSubject::where('id', $row['class_subject_id'])
                    ->where('class_id', $class->id)
                    ->first();
                if ($cs) {
                    $cs->update(['teacher_id' => $row['teacher_id']]);
                }
            }
            DB::commit();
            return back()->with('success', 'Atribuições de professores por disciplina actualizadas.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao actualizar atribuições: '.$e->getMessage());
        }
    }

    /**
     * Atribuir/actualizar professor assistente (turma do primário).
     */
    public function updateAssistant(Request $request, ClassRoom $class)
    {
        $this->authorize('manage_classes');

        $data = $request->validate([
            'assistant_teacher_id' => 'nullable|exists:teachers,id|different:'.$class->teacher_id,
        ]);

        $class->update(['assistant_teacher_id' => $data['assistant_teacher_id'] ?? null]);

        return back()->with('success', 'Professor assistente actualizado com sucesso.');
    }

    private function isSecondaryGrade(int $gradeLevel): bool
    {
        return EducationLevel::isSecondary($gradeLevel);
    }

    // Métodos auxiliares
    private function calculateAverageAge($class)
    {
        $students = $class->students()->get();
        if ($students->isEmpty())
            return 0;

        $totalAge = 0;
        $count = 0;

        foreach ($students as $student) {
            if ($student->birthdate) {
                $totalAge += $student->birthdate->age;
                $count++;
            }
        }

        return $count > 0 ? round($totalAge / $count, 1) : 0;
    }

    private function getUpcomingBirthdays($class, $days = 30)
    {
        return $class->students()
            ->whereNotNull('birthdate')
            ->whereRaw('MONTH(birthdate) = ? AND DAY(birthdate) >= ?', [now()->month, now()->day])
            ->orWhereRaw('MONTH(birthdate) = ? AND DAY(birthdate) <= ?', [now()->addMonth()->month, now()->addDays($days)->day])
            ->orderByRaw('MONTH(birthdate), DAY(birthdate)')
            ->take(5)
            ->get();
    }

    // Métodos auxiliares
    private function getGradeLevelsList(): array
    {
        return EducationLevel::GRADES;
    }
}