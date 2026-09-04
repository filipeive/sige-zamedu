<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Assessment;
use App\Models\Event;
use App\Models\Communication;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherPortalController extends Controller
{
    public function dashboard()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        // Estatísticas
        $stats = [
            'total_classes' => $teacher->classes()->active()->currentYear()->count(),
            'total_students' => $this->getTotalStudents($teacher),
            'today_attendance' => $this->getTodayAttendance($teacher),
            'pending_grades' => $this->getPendingGrades($teacher),
            'upcoming_events' => $this->getUpcomingEvents($teacher),
        ];

        // Turmas do professor
        $myClasses = $teacher->classes()
            ->active()
            ->currentYear()
            ->withCount(['students'])
            ->orderBy('name')
            ->get();

        // Próximos eventos
        $upcomingEvents = Event::where('event_date', '>=', today())
            ->orderBy('event_date')
            ->take(5)
            ->get();
        $newCommsCount = Communication::forTeachers()->published()->recent(7)->count();
        // Comunicações recentes
        $recentCommunications = Communication::where('target_audience', 'teachers')
            ->orWhere('target_audience', 'all')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Aniversariantes da semana
        $birthdaysThisWeek = Student::active()
            ->whereNotNull('birthdate')
            ->get()
            ->filter(function ($student) {
                if (!$student->birthdate) {
                    return false;
                }
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $birthdayThisYear = $student->birthdate->copy()->setYear($startOfWeek->year);
                return $birthdayThisYear->between($startOfWeek, $endOfWeek);
            })
            ->take(5)
            ->values();

        return view('teacher-portal.dashboard', compact(
            'teacher',
            'stats',
            'myClasses',
            'upcomingEvents',
            'newCommsCount',
            'recentCommunications',
            'birthdaysThisWeek'
        ));
    }

    public function myClasses()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $classes = $teacher->classes()
            ->withCount(['students', 'subjects'])
            ->active()
            ->currentYear()
            ->get();

        return view('teacher-portal.classes', compact('teacher', 'classes'));
    }

    public function classStudents($classId)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $class = ClassRoom::with([
            'students' => function ($q) {
                $q->orderBy('last_name')->orderBy('first_name');
            }
        ])->findOrFail($classId);

        // Verificar se o professor tem acesso a esta turma
        if (!$teacher->classes()->where('classes.id', $classId)->exists()) {
            abort(403, 'Acesso não autorizado a esta turma.');
        }

        $students = $class->students;

        return view('teacher-portal.class-students', compact('teacher', 'class', 'students'));
    }



    //classes
    public function classes()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $classes = $teacher->classes()
            ->withCount(['students', 'subjects'])
            ->active()
            ->currentYear()
            ->get();

        return view('teacher-portal.classes', compact('teacher', 'classes'));
    }
    public function classDetails($classId)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $class = ClassRoom::with(['students', 'subjects'])->findOrFail($classId);

        // Verificar se o professor tem acesso a esta turma
        if (!$teacher->classes()->where('classes.id', $classId)->exists()) {
            abort(403, 'Acesso não autorizado a esta turma.');
        }

        $todayAttendance = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', today())
            ->get()
            ->keyBy('student_id');

        return view('teacher-portal.class-detail', compact('teacher', 'class', 'todayAttendance'));
    }

    public function todayAttendance($classId)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $class = ClassRoom::with(['students'])->findOrFail($classId);

        // Verifica se o professor tem acesso à turma
        if (!$teacher->classes()->where('classes.id', $classId)->exists()) {
            abort(403, 'Acesso não autorizado.');
        }

        $attendanceDate = today()->format('Y-m-d');

        $attendances = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $attendanceDate)
            ->get();

        return view('teacher-portal.class-detail', compact('teacher', 'class', 'attendances'));
    }


    public function Classattendance($classId)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $class = ClassRoom::with(['students'])->findOrFail($classId);

        if (!$teacher->classes()->where('classes.id', $classId)->exists()) {
            abort(403, 'Acesso não autorizado.');
        }

        $attendanceDate = request('date', today()->format('Y-m-d'));
        $existingAttendance = Attendance::where('class_id', $classId)
            ->whereDate('attendance_date', $attendanceDate)
            ->get()
            ->keyBy('student_id');

        return view('teacher-portal.attendance', compact(
            'teacher',
            'class',
            'attendanceDate',
            'existingAttendance'
        ));
    }

    public function storeAttendance(Request $request, $classId)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        if (!$teacher->classes()->where('classes.id', $classId)->exists()) {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'attendance_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late',
        ]);

        try {
            foreach ($request->attendances as $attendanceData) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $attendanceData['student_id'],
                        'class_id' => $classId,
                        'attendance_date' => $request->attendance_date,
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'marked_by' => Auth::id(),
                        'notes' => $attendanceData['notes'] ?? null,
                        'arrival_time' => $attendanceData['status'] == 'late' ? now() : null,
                    ]
                );
            }
            // CORREÇÃO: Redirecionar para a rota correta
            return redirect()->route('teacher.attendance.class', $classId)
                ->with('success', 'Presenças registradas com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao registrar presenças: ' . $e->getMessage())
                ->withInput();
        }
    }
    // No TeacherPortalController
    public function pendingGrades()
    {
        $teacher = Teacher::where('user_id', Auth::id())
            ->with([
                'classes' => function ($query) {
                    $query->active()->currentYear()->withCount('students');
                }
            ])
            ->firstOrFail();

        // Buscar avaliações reais do banco
        $upcomingAssessments = Assessment::with(['class', 'subject'])
            ->forTeacher($teacher->id)
            ->upcoming(7)
            ->get();

        $overdueAssessments = Assessment::with(['class', 'subject'])
            ->forTeacher($teacher->id)
            ->overdue()
            ->get();

        return view('teacher-portal.pending-grades', compact(
            'teacher',
            'upcomingAssessments',
            'overdueAssessments'
        ));
    }

    public function communications()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        // Buscar comunicados reais do banco
        $communications = Communication::forTeachers()
            ->published()
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('teacher-portal.communications', compact('teacher', 'communications'));
    }

    // No TeacherPortalController

    public function gradebook($class)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        $class = ClassRoom::with(['students', 'subjects'])->findOrFail($class);

        // Verificar acesso (regente OU professor de uma disciplina)
        if (! $teacher->canAccessClass($class->id)) {
            abort(403, 'Acesso não autorizado a esta turma.');
        }

        // Disciplinas que o professor lecciona nesta turma
        $teacherSubjects = $teacher->taughtSubjectsInClass($class);
        $teacherSubjectIds = $teacherSubjects->pluck('id')->toArray();

        $selectedSubject = request('subject_id', $teacherSubjectIds[0] ?? null);
        $selectedTerm = (int) request('term', 1);
        $currentYear = (int) request('year', current_school_year());

        // Validação: a subject seleccionada tem de ser leccionada por este professor
        if ($selectedSubject && ! in_array((int) $selectedSubject, $teacherSubjectIds, true)) {
            abort(403, 'Não lecciona esta disciplina nesta turma.');
        }

        // Buscar notas existentes (agrupadas por aluno → tipo de avaliação)
        $grades = Grade::whereIn('student_id', $class->students->pluck('id'))
            ->when($selectedSubject, function ($query) use ($selectedSubject) {
                return $query->where('subject_id', $selectedSubject);
            })
            ->where('term', $selectedTerm)
            ->where('year', $currentYear)
            ->get()
            ->groupBy(['student_id', 'assessment_type']);

        // Tipos de avaliação (MINEDH)
        $assessmentTypes = [
            'ACS1' => 'ACS 1',
            'ACS2' => 'ACS 2',
            'ACS3' => 'ACS 3',
            'ACP'  => 'ACP',
            'ACF'  => 'ACF / Exame',
        ];

        return view('teacher-portal.gradebook', compact(
            'teacher',
            'class',
            'selectedSubject',
            'selectedTerm',
            'currentYear',
            'teacherSubjects',
            'teacherSubjectIds',
            'grades',
            'assessmentTypes'
        ));
    }
    public function storeGrade(Request $request)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'grade' => 'required|numeric|min:0|max:20',
            'assessment_type' => 'required|in:ACS1,ACS2,ACS3,ACP,ACF,MAC,PPT,CPT,CAP,Exame,test,homework,project,participation',
            'term' => 'required|in:1,2,3',
            'year' => 'nullable|integer|min:2020|max:2030',
        ]);

        try {
            if (! $teacher->canAccessClassSubject($request->class_id, $request->subject_id)) {
                abort(403, 'Acesso não autorizado: não lecciona esta disciplina nesta turma.');
            }

            Grade::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'class_id' => $request->class_id,
                    'subject_id' => $request->subject_id,
                    'assessment_type' => $request->assessment_type,
                    'term' => $request->term,
                    'year' => (int) ($request->year ?? current_school_year()),
                ],
                [
                    'grade' => $request->grade,
                    'teacher_id' => $teacher->id,
                    'date_recorded' => now(),
                    'comments' => $request->comments,
                ]
            );

            return redirect()->back()
                ->with('success', 'Nota registada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao registar nota: ' . $e->getMessage());
        }
    }

    public function profile()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();
        return view('teacher-portal.profile', compact('teacher'));
    }

    public function createProfile()
    {
        $user = Auth::user();
        // Check if teacher already exists
        if (Teacher::where('user_id', $user->id)->exists()) {
            return redirect()->route('teacher.dashboard');
        }
        return view('teacher-portal.create-profile', compact('user'));
    }

    public function storeProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'bi_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
        ]);

        try {
            $nameParts = explode(' ', $user->name);
            $lastName = count($nameParts) > 1 ? end($nameParts) : '';

            Teacher::create([
                'user_id' => $user->id,
                'first_name' => $nameParts[0],
                'last_name' => $lastName,
                'email' => $user->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'qualification' => $request->qualification,
                'specialization' => $request->specialization,
                'bi_number' => $request->bi_number,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'hire_date' => now(), // Default to today
                'status' => 'active',
            ]);

            return redirect()->route('teacher.dashboard')
                ->with('success', 'Perfil de professor criado com sucesso! Bem-vindo ao portal.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao criar perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateProfile(Request $request)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        try {
            $teacher->update($request->only(['phone', 'address', 'qualification', 'specialization']));

            return redirect()->back()
                ->with('success', 'Perfil atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }

    // Métodos auxiliares
    private function getTotalStudents($teacher)
    {
        return $teacher->classes()
            ->active()
            ->currentYear()
            ->withCount('students')
            ->get()
            ->sum('students_count');
    }

    private function getTodayAttendance($teacher)
    {
        return Attendance::whereIn('class_id', $teacher->classes()->pluck('classes.id'))
            ->whereDate('attendance_date', today())
            ->where('status', 'present')
            ->count();
    }

    private function getPendingGrades($teacher)
    {
        // Simulação - na prática seria baseado em datas de avaliação
        return $teacher->classes()->active()->currentYear()->count() * 2;
    }

    private function getUpcomingEvents($teacher)
    {
        return Event::where('event_date', '>=', today())
            ->orderBy('event_date')
            ->count();
    }

    /**
     * Marcar presença individual ou em lote
     */
    public function markAttendance(Request $request, $classId)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        // Verificar acesso
        if (!$teacher->classes()->where('classes.id', $classId)->exists()) {
            abort(403, 'Acesso não autorizado a esta turma.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:present,absent,late,excused',
            'attendance_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            Attendance::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'class_id' => $classId,
                    'attendance_date' => $request->attendance_date,
                ],
                [
                    'status' => $request->status,
                    'marked_by' => Auth::id(),
                    'notes' => $request->notes,
                    'arrival_time' => $request->status === 'late' ? $request->arrival_time ?? now() : null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Presença registrada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar presença: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar notas em lote
     */
    public function batchUpdateGrades(Request $request)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.subject_id' => 'required|exists:subjects,id',
            'grades.*.class_id' => 'required|exists:classes,id',
            'grades.*.assessment_type' => 'required|in:ACS1,ACS2,ACS3,ACP,ACF,MAC,PPT,CPT,CAP,Exame,test,homework,project,participation',
            'grades.*.grade' => 'required|numeric|min:0|max:20',
            'grades.*.term' => 'required|in:1,2,3',
        ]);

        try {
            $successCount = 0;
            $skippedCount = 0;

            foreach ($request->grades as $gradeData) {
                // Verificar se o professor tem acesso à (turma, disciplina)
                if (! $teacher->canAccessClassSubject($gradeData['class_id'], $gradeData['subject_id'])) {
                    $skippedCount++;
                    continue;
                }

                Grade::updateOrCreate(
                    [
                        'student_id' => $gradeData['student_id'],
                        'class_id' => $gradeData['class_id'],
                        'subject_id' => $gradeData['subject_id'],
                        'assessment_type' => $gradeData['assessment_type'],
                        'term' => $gradeData['term'],
                        'year' => (int) ($gradeData['year'] ?? current_school_year()),
                    ],
                    [
                        'grade' => $gradeData['grade'],
                        'teacher_id' => $teacher->id,
                        'date_recorded' => now(),
                        'comments' => $gradeData['comments'] ?? null,
                    ]
                );

                $successCount++;
            }

            $msg = "{$successCount} notas actualizadas com sucesso.";
            if ($skippedCount > 0) {
                $msg .= " {$skippedCount} ignoradas (sem permissão).";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'Erro ao actualizar notas: ' . $e->getMessage()
            );
        }
    }

    /**
     * Formulário para criar comunicação
     */
    public function createCommunication()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $classes = $teacher->classes()
            ->active()
            ->currentYear()
            ->withCount('students')
            ->get();

        return view('teacher-portal.create-communication', compact('teacher', 'classes'));
    }

    /**
     * Enviar comunicação
     */
    public function sendCommunication(Request $request)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:parents,students,all',
            'priority' => 'required|in:low,normal,medium,high,urgent',
        ]);

        try {
            $priority = match ($request->priority) {
                'normal' => 'medium',
                'urgent' => 'high',
                default => $request->priority,
            };

            $communication = Communication::create([
                'title' => $request->title,
                'message' => $request->message,
                'target_audience' => $request->target_audience,
                'priority' => $priority,
                'created_by' => Auth::id(),
                'is_published' => true,
                'publish_at' => now(),
            ]);

            // TODO: Enviar notificações para destinatários

            return redirect()->route('teacher.communications.index')
                ->with('success', 'Comunicado enviado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao enviar comunicado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Listar pedidos de licença do professor
     */
    public function leaveRequests()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $leaveRequests = $teacher->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $statistics = [
            'total' => $teacher->leaveRequests()->count(),
            'pending' => $teacher->leaveRequests()->where('status', 'pending')->count(),
            'approved' => $teacher->leaveRequests()->where('status', 'approved')->count(),
            'rejected' => $teacher->leaveRequests()->where('status', 'rejected')->count(),
        ];

        return view('teacher-portal.leave-requests', compact(
            'teacher',
            'leaveRequests',
            'statistics'
        ));
    }

    /**
     * Formulário para criar pedido de licença
     */
    public function createLeaveRequest()
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        return view('teacher-portal.create-leave-request', compact('teacher'));
    }

    /**
     * Salvar pedido de licença
     */
    public function storeLeaveRequest(Request $request)
    {
        $teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'leave_type' => 'required|in:sick,personal,vacation,maternity,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $data = $request->only(['leave_type', 'start_date', 'end_date', 'reason']);
            $data['staff_id'] = $teacher->id;
            $data['status'] = 'pending';

            $leaveRequest = \App\Models\StaffLeaveRequest::create($data);

            activity('leave_requests')
                ->performedOn($leaveRequest)
                ->causedBy(auth()->user())
                ->withProperties([
                    'decision' => 'created',
                    'staff_id' => $teacher->id,
                    'leave_type' => $leaveRequest->leave_type,
                    'period' => [
                        'start_date' => optional($leaveRequest->start_date)->toDateString(),
                        'end_date' => optional($leaveRequest->end_date)->toDateString(),
                    ],
                ])
                ->log('leave_request_created');

            // TODO: Notificar administradores sobre novo pedido

            return redirect()->route('teacher.leave-requests.index')
                ->with('success', 'Pedido de licença enviado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao enviar pedido de licença: ' . $e->getMessage())
                ->withInput();
        }
    }
}
