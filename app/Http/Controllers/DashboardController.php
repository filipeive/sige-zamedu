<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\StaffLeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin' || $user->hasRole('super_admin')) {
            if (session()->has('support_mode') && session()->has('support_mode_school_id')) {
                return $this->adminDashboard();
            }
            return $this->superAdminDashboard();
        }

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard();
            case 'secretary':
                return $this->secretaryDashboard();
            case 'pedagogy':
                return $this->pedagogyDashboard();
            case 'teacher':
                return $this->teacherDashboard();
            case 'parent':
                return $this->parentDashboard();
            default:
                return $this->basicDashboard();
        }
    }

    private function getGreetingData(): array
    {
        $hour = now()->hour;
        $greeting = match (true) {
            $hour < 12 => 'Bom dia',
            $hour < 18 => 'Boa tarde',
            default => 'Boa noite',
        };

        $daysPt = [
            'Sunday' => 'Domingo', 'Monday' => 'Segunda-feira', 'Tuesday' => 'Terça-feira',
            'Wednesday' => 'Quarta-feira', 'Thursday' => 'Quinta-feira', 'Friday' => 'Sexta-feira', 'Saturday' => 'Sábado'
        ];
        $monthsPt = [
            'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março',
            'April' => 'Abril', 'May' => 'Maio', 'June' => 'Junho',
            'July' => 'Julho', 'August' => 'Agosto', 'September' => 'Setembro',
            'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
        ];

        $dayName = $daysPt[now()->format('l')] ?? now()->format('l');
        $monthName = $monthsPt[now()->format('F')] ?? now()->format('F');
        $formattedDate = $dayName . ', ' . now()->format('d') . ' de ' . $monthName . ' de ' . now()->format('Y');

        $activeSchoolName = 'Escola dos Visionários';
        if (session('active_branch') === 'all') {
            $activeSchoolName = 'Rede Escola dos Visionários (Consolidado)';
        } elseif ($curSchool = current_school()) {
            $activeSchoolName = $curSchool->name;
        } else {
            $activeSchoolName = setting('school_name', 'Escola dos Visionários');
        }

        return [
            'greeting' => $greeting . ', ' . auth()->user()->name,
            'greeting_period' => match (true) {
                $hour < 12 => 'morning',
                $hour < 18 => 'afternoon',
                default => 'evening',
            },
            'current_date' => $formattedDate,
            'school_name' => $activeSchoolName,
            'school_year' => current_school_year(),
        ];
    }

    private function getBirthdaysThisWeek()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return Student::active()
            ->whereNotNull('birthdate')
            ->get()
            ->filter(function ($student) use ($startOfWeek, $endOfWeek) {
                if (!$student->birthdate) {
                    return false;
                }
                $birthdayThisYear = $student->birthdate->copy()->setYear($startOfWeek->year);
                return $birthdayThisYear->between($startOfWeek, $endOfWeek);
            })
            ->take(5)
            ->values();
    }

    private function getMonthBirthdays(int $month, int $year)
    {
        return Student::active()
            ->whereNotNull('birthdate')
            ->get()
            ->filter(function ($student) use ($month, $year) {
                if (!$student->birthdate) {
                    return false;
                }
                return $student->birthdate->month === $month && $student->birthdate->year <= $year;
            })
            ->groupBy(fn($student) => $student->birthdate->day)
            ->map(fn($students, $day) => [
                'day' => $day,
                'students' => $students->take(3)->values(),
            ]);
    }

    private function getMonthEvents(int $month, int $year)
    {
        return Event::whereMonth('event_date', $month)
            ->whereYear('event_date', $year)
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($event) => $event->event_date->day)
            ->map(fn($events, $day) => [
                'day' => $day,
                'events' => $events->take(3)->values(),
            ]);
    }

    private function getTodayActivities(): array
    {
        $today = today();

        return [
            'payments_count' => Payment::paid()->whereDate('payment_date', $today)->count(),
            'payments_total' => Payment::paid()->whereDate('payment_date', $today)->sum('amount'),
            'expenses_count' => \App\Models\Expense::whereDate('expense_date', $today)->count(),
            'expenses_total' => \App\Models\Expense::whereDate('expense_date', $today)->sum('amount'),
            'enrollments_count' => Enrollment::whereDate('created_at', $today)->count(),
            'attendances_count' => Attendance::whereDate('attendance_date', $today)->count(),
            'new_students_count' => Student::whereDate('created_at', $today)->count(),
        ];
    }

    private function superAdminDashboard()
    {
        $greetingData = $this->getGreetingData();

        // 1. Métricas da Plataforma Nacional
        $totalSchools = \App\Models\School::count();
        $publicSchoolsCount = \App\Models\School::whereIn('type', ['publica', 'public'])->count();
        $privateSchoolsCount = \App\Models\School::whereIn('type', ['privada', 'comunitaria', 'private', 'community'])->count();

        $totalStudents = \App\Models\Student::withoutSchoolScope()->count();
        $totalTeachers = \App\Models\Teacher::withoutSchoolScope()->count();

        $totalProvinces = \App\Models\Province::count();
        $totalDistricts = \App\Models\District::count();
        $totalZips = \App\Models\Zip::count();

        $totalUsers = \App\Models\User::count();
        $activeLicenses = \App\Models\License::withoutSchoolScope()->where('status', 'active')->count();

        // 2. Lista Multi-Escola com Dados Territoriais e Licenciamento
        $schools = \App\Models\School::with(['province', 'district', 'zip', 'latestLicense'])
            ->withCount(['students', 'teachers', 'users'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        // 3. Submissões Recentes de Pré-Inscrição
        $recentApplications = \App\Models\EnrollmentApplication::with('school')->latest()->take(5)->get();

        return view('dashboard.super_admin', compact(
            'greetingData',
            'totalSchools',
            'publicSchoolsCount',
            'privateSchoolsCount',
            'totalStudents',
            'totalTeachers',
            'totalProvinces',
            'totalDistricts',
            'totalZips',
            'totalUsers',
            'activeLicenses',
            'schools',
            'recentApplications'
        ));
    }

    private function adminDashboard()
    {
        $user = auth()->user();
        $isConsolidated = session('active_branch') === 'all';

        // Filiais da Rede dos Visionários para o Dono/Director
        $visionariosSchools = \App\Models\School::where('code', 'like', 'VIS-%')
            ->orWhere('name', 'like', '%Visionários%')
            ->get(['id', 'code', 'name', 'phone', 'address', 'type', 'principal_name']);

        if ($visionariosSchools->isEmpty()) {
            $visionariosSchools = \App\Models\School::where('type', 'private')
                ->get(['id', 'code', 'name', 'phone', 'address', 'type', 'principal_name']);
        }

        $branches = $visionariosSchools;
        $currentBranchId = session('active_branch') ?? ($user->school_id ?: ($branches->first()?->id ?? 1));

        if (!$isConsolidated && $currentBranchId) {
            \App\Services\SchoolContext::setSchoolId((int) $currentBranchId);
        }

        $cacheKey = 'admin_dashboard_analytics_v4_' . ($isConsolidated ? 'all' : $currentBranchId) . '_' . current_school_year();

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($isConsolidated, $branches) {
            $year = current_school_year();
            $networkSchoolIds = $branches->pluck('id')->toArray();

            if ($isConsolidated && !empty($networkSchoolIds)) {
                $studentsQuery = Student::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->active();
                $teachersQuery = Teacher::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->active();
                $classesQuery = ClassRoom::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->active()->where('school_year', $year);
                $paymentsPaidMonth = Payment::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->paid()
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
                $paymentsPaidLastMonth = Payment::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->paid()
                    ->whereMonth('payment_date', now()->subMonth()->month)
                    ->whereYear('payment_date', now()->subMonth()->year);
                $pendingPayments = Payment::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->pending()->count();
                $overduePayments = Payment::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->overdue()->count();
                $overdueAmount = Payment::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->overdue()->sum('amount');
                $totalExpenses = \App\Models\Expense::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->sum('amount');
                $thisMonthExpenses = \App\Models\Expense::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)
                    ->whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year)
                    ->sum('amount');
                $attendancesCount = Attendance::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->count();
                $presentCount = Attendance::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->whereIn('status', ['present', 'late'])->count();
            } else {
                $studentsQuery = Student::active();
                $teachersQuery = Teacher::active();
                $classesQuery = ClassRoom::active()->where('school_year', $year);
                $paymentsPaidMonth = Payment::paid()
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
                $paymentsPaidLastMonth = Payment::paid()
                    ->whereMonth('payment_date', now()->subMonth()->month)
                    ->whereYear('payment_date', now()->subMonth()->year);
                $pendingPayments = Payment::pending()->count();
                $overduePayments = Payment::overdue()->count();
                $overdueAmount = Payment::overdue()->sum('amount');
                $totalExpenses = \App\Models\Expense::sum('amount');
                $thisMonthExpenses = \App\Models\Expense::whereMonth('expense_date', now()->month)
                    ->whereYear('expense_date', now()->year)
                    ->sum('amount');
                $attendancesCount = Attendance::count();
                $presentCount = Attendance::whereIn('status', ['present', 'late'])->count();
            }

            // 1. Estatísticas Fundamentais & KPIs
            $totalStudents = (clone $studentsQuery)->count();
            $femaleStudents = (clone $studentsQuery)->where('gender', 'F')->count();
            $maleStudents = (clone $studentsQuery)->where('gender', 'M')->count();
            $totalTeachers = $teachersQuery->count();
            $totalClasses = $classesQuery->count();

            $currentMonthRevenue = $paymentsPaidMonth->sum('amount');
            $lastMonthRevenue = $paymentsPaidLastMonth->sum('amount');

            $revenueChange = $lastMonthRevenue > 0 
                ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
                : 0;

            // 2. Taxa de Assiduidade Global
            $overallAttendanceRate = $attendancesCount > 0 
                ? round(($presentCount / $attendancesCount) * 100, 1) 
                : 95.0;

            // 3. Média Académica Global
            $globalGradeAvg = round(Grade::currentYear()->avg('grade') ?? 14.5, 1);

            // 4. Top Alunos
            $topStudentsQuery = $isConsolidated && !empty($networkSchoolIds)
                ? Student::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->active()
                : Student::active();

            $topStudents = $topStudentsQuery->withCount('grades')
                ->get()
                ->map(function ($student) use ($year) {
                    $avg = Grade::where('student_id', $student->id)->where('year', $year)->avg('grade');
                    $student->average_grade = round($avg ?? 0, 1);
                    return $student;
                })
                ->filter(fn($s) => $s->average_grade > 0)
                ->sortByDesc('average_grade')
                ->take(5)
                ->values();

            // 5. Ranking de Turmas
            $topClassesQuery = $isConsolidated && !empty($networkSchoolIds)
                ? ClassRoom::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->active()->where('school_year', $year)
                : ClassRoom::active()->where('school_year', $year);

            $topClasses = $topClassesQuery->get()
                ->map(function ($class) use ($year) {
                    $avg = Grade::where('class_id', $class->id)->where('year', $year)->avg('grade');
                    $class->average_grade = round($avg ?? 0, 1);
                    return $class;
                })
                ->sortByDesc('average_grade')
                ->take(5)
                ->values();

            // 6. Dados dos Gráficos (12 meses de receitas)
            $monthlyRevenueChart = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $paidSumQuery = $isConsolidated && !empty($networkSchoolIds)
                    ? Payment::withoutSchoolScope()->whereIn('school_id', $networkSchoolIds)->paid()
                    : Payment::paid();

                $paidSum = $paidSumQuery
                    ->whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->sum('amount');

                $monthlyRevenueChart[] = [
                    'month' => $date->format('M/Y'),
                    'revenue' => round($paidSum, 2),
                ];
            }

            // 7. Alertas & Análises Inteligentes
            $smartInsights = [];
            if ($overallAttendanceRate >= 90) {
                $smartInsights[] = [
                    'type' => 'success',
                    'icon' => 'fa-circle-check',
                    'title' => 'Excelente Nível de Assiduidade',
                    'message' => "A taxa global de presença situa-se nos {$overallAttendanceRate}%, demonstrando elevado empenho dos alunos."
                ];
            }
            if ($revenueChange > 0) {
                $smartInsights[] = [
                    'type' => 'info',
                    'icon' => 'fa-chart-line',
                    'title' => 'Crescimento de Arrecadação',
                    'message' => "A receita do mês atual registou um aumento de +{$revenueChange}% em relação ao mês anterior."
                ];
            }
            if ($overdueAmount > 0) {
                $smartInsights[] = [
                    'type' => 'warning',
                    'icon' => 'fa-triangle-exclamation',
                    'title' => 'Atenção às Propinas Pendentes',
                    'message' => "Existem {$overduePayments} propinas em atraso no valor total de " . number_format($overdueAmount, 2) . " MT. Recomenda-se o envio de lembretes e aplicação de multas regulamentares."
                ];
            }

            $stats = [
                'total_students' => $totalStudents,
                'female_students' => $femaleStudents,
                'male_students' => $maleStudents,
                'total_teachers' => $totalTeachers,
                'total_classes' => $totalClasses,
                'monthly_revenue' => $currentMonthRevenue,
                'revenue_change' => $revenueChange,
                'overall_attendance_rate' => $overallAttendanceRate,
                'global_grade_avg' => $globalGradeAvg,
                'pending_payments' => $pendingPayments,
                'overdue_payments' => $overduePayments,
                'overdue_amount' => $overdueAmount,
                'total_expenses' => $totalExpenses,
                'this_month_expenses' => $thisMonthExpenses,
                'todays_events' => Event::today()->count(),
                'total_enrollments' => Enrollment::active()->where('school_year', $year)->count(),
                'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
                'pending_leave_requests' => StaffLeaveRequest::where('status', 'pending')->count(),
                'pending_actions' => $overduePayments + Enrollment::where('status', 'pending')->count() + StaffLeaveRequest::where('status', 'pending')->count()
            ];

            return [
                'stats' => $stats,
                'monthlyRevenueChart' => $monthlyRevenueChart,
                'topStudents' => $topStudents,
                'topClasses' => $topClasses,
                'smartInsights' => $smartInsights,
            ];
        });

        $recentActivities = $this->getRecentActivities();
        $upcomingEvents = Event::with('createdBy')
            ->where('event_date', '>=', now())
            ->where('event_date', '<=', now()->addDays(7))
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $revenueData = $this->getRevenueData();
        $studentsDistribution = $this->getStudentsDistribution();

        $greetingData = $this->getGreetingData();
        $birthdaysThisWeek = $this->getBirthdaysThisWeek();
        $todayActivities = $this->getTodayActivities();

        $calendarMonth = request('calendar_month', now()->month);
        $calendarYear = request('calendar_year', now()->year);
        $monthEvents = $this->getMonthEvents($calendarMonth, $calendarYear);
        $monthBirthdays = $this->getMonthBirthdays($calendarMonth, $calendarYear);

        return view('dashboard.admin', array_merge(
            $data,
            compact('greetingData', 'birthdaysThisWeek', 'todayActivities', 'recentActivities', 'upcomingEvents', 'revenueData', 'studentsDistribution', 'calendarMonth', 'calendarYear', 'monthEvents', 'monthBirthdays', 'branches', 'currentBranchId', 'isConsolidated')
        ));
    }

    private function secretaryDashboard()
    {
        $stats = [
            'total_students' => Student::active()->count(),
            'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
            'monthly_revenue' => Payment::paid()
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'pending_payments' => Payment::pending()->count(),
            'overdue_payments' => Payment::overdue()->count(),
            'overdue_amount' => Payment::overdue()->sum('amount'),
            'todays_payments' => Payment::paid()
                ->whereDate('payment_date', today())
                ->count(),
            'total_payments_today' => Payment::paid()
                ->whereDate('payment_date', today())
                ->sum('amount'),
            'new_enrollments_month' => Enrollment::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        $recentPayments = Payment::with(['student'])
            ->latest()
            ->take(10)
            ->get();

        $overduePayments = Payment::overdue()
            ->with(['student'])
            ->take(10)
            ->get();

        $pendingEnrollments = Enrollment::with(['student', 'class'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        $greetingData = $this->getGreetingData();
        $todayActivities = $this->getTodayActivities();
        $birthdaysThisWeek = $this->getBirthdaysThisWeek();

        return view('dashboard.secretary', compact(
            'greetingData',
            'stats',
            'recentPayments',
            'overduePayments',
            'pendingEnrollments',
            'todayActivities',
            'birthdaysThisWeek'
        ));
    }

    private function pedagogyDashboard()
    {
        $stats = [
            'total_students' => Student::active()->count(),
            'total_teachers' => Teacher::active()->count(),
            'total_classes' => ClassRoom::active()->where('school_year', current_school_year())->count(),
            'average_attendance' => $this->calculateAverageAttendance(),
            'pending_grades' => $this->getPendingGradesCount(),
            'upcoming_exams' => Event::where('type', 'exam')->upcoming()->count(),
            'total_subjects' => \App\Models\Subject::active()->count(),
            'class_performance_avg' => $this->getAverageClassPerformance(),
        ];

        $classPerformance = $this->getClassPerformance();
        $attendanceStats = $this->getAttendanceStats();
        $teacherStats = $this->getTeacherStats();
        $upcomingExams = Event::where('type', 'exam')
            ->upcoming()
            ->with('createdBy')
            ->take(5)
            ->get();

        $greetingData = $this->getGreetingData();
        $todayActivities = $this->getTodayActivities();
        $birthdaysThisWeek = $this->getBirthdaysThisWeek();

        return view('dashboard.pedagogy', compact(
            'greetingData',
            'stats',
            'classPerformance',
            'attendanceStats',
            'teacherStats',
            'upcomingExams',
            'todayActivities',
            'birthdaysThisWeek'
        ));
    }

    private function teacherDashboard()
    {
        $teacher = Teacher::findByUser(auth()->id());

        if (!$teacher) {
            return redirect()->route('teacher.create-profile')
                ->with('warning', 'Complete seu perfil de professor para acessar o dashboard.');
        }

        $myClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->active()
            ->where('school_year', current_school_year())
            ->with(['students', 'subjects'])
            ->get();

        $stats = [
            'my_classes' => $myClasses->count(),
            'total_students' => $myClasses->sum(function ($class) {
                return $class->students->count();
            }),
            'todays_attendance' => Attendance::whereIn('class_id', $myClasses->pluck('id'))
                ->whereDate('attendance_date', today())
                ->count(),
            'pending_grades' => Grade::where('teacher_id', $teacher->id)
                ->whereNull('grade')
                ->count(),
            'total_subjects' => $myClasses->sum(function ($class) {
                return $class->subjects->count();
            }),
            'classes_with_attendance' => Attendance::whereIn('class_id', $myClasses->pluck('id'))
                ->whereDate('attendance_date', today())
                ->distinct('class_id')
                ->count('class_id'),
        ];

        $todaysSchedule = $this->getTodaysSchedule($teacher->id);
        $recentGrades = Grade::where('teacher_id', $teacher->id)
            ->with(['student', 'subject'])
            ->latest()
            ->take(10)
            ->get();

        $myStudents = Student::active()
            ->whereHas('enrollments', function ($q) use ($myClasses) {
                $q->whereIn('class_id', $myClasses->pluck('id'))
                    ->where('status', 'active');
            })
            ->with(['currentEnrollment.class']) // 🔥 carrega a matrícula e a classe juntas
            ->get();

        $calendarMonth = request('calendar_month', now()->month);
        $calendarYear = request('calendar_year', now()->year);

        return view('dashboard.teacher', compact(
            'stats',
            'myClasses',
            'todaysSchedule',
            'recentGrades',
            'myStudents',
            'teacher',
            'calendarMonth',
            'calendarYear'
        ));
    }

    private function parentDashboard()
    {
        return redirect()->route('parent.dashboard');
    }

    private function basicDashboard()
    {
        $stats = [
            'welcome_message' => 'Bem-vindo ao Sistema Visionários',
            'user_name' => auth()->user()->name,
            'last_login' => auth()->user()->last_login_at?->format('d/m/Y H:i') ?? 'Primeiro acesso',
        ];

        return view('dashboard.basic', compact('stats'));
    }

    // ========== MÉTODOS AUXILIARES ATUALIZADOS ==========

    private function calculateRevenueChange()
    {
        $currentMonthRevenue = Payment::paid()
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $lastMonthRevenue = Payment::paid()
            ->whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('amount');

        if ($lastMonthRevenue == 0) {
            return $currentMonthRevenue > 0 ? 100 : 0;
        }

        return round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
    }

    private function getRevenueData()
    {
        $months = [];
        $amounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M/Y');

            $revenue = Payment::paid()
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');

            $amounts[] = $revenue;
        }

        return [
            'months' => $months,
            'amounts' => $amounts
        ];
    }

    private function getStudentsDistribution()
    {
        $distribution = ClassRoom::withCount([
            'students as students_count' => function ($query) {
                $query->where('enrollments.status', 'active');
            }
        ])
            ->where('is_active', true)
            ->where('school_year', current_school_year())
            ->get();


        return [
            'labels' => $distribution->pluck('name'),
            'data' => $distribution->pluck('students_count')
        ];
    }

    private function getRecentActivities()
    {
        // Atividades recentes - você pode substituir por um sistema de logs real
        $activities = collect();

        // Pagamentos recentes
        $recentPayments = Payment::with(['student'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'type' => 'payment',
                    'icon' => 'money-bill-wave',
                    'title' => 'Pagamento Recebido',
                    'description' => $payment->student->full_name . ' - ' . number_format($payment->amount, 2, ',', '.') . ' MT',
                    'user_name' => 'Sistema',
                    'created_at' => $payment->created_at
                ];
            });

        // Matrículas recentes
        $recentEnrollments = Enrollment::with(['student', 'class'])
            ->where('status', 'active')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($enrollment) {
                return (object) [
                    'type' => 'enrollment',
                    'icon' => 'user-plus',
                    'title' => 'Nova Matrícula',
                    'description' => $enrollment->student->full_name . ' - ' . $enrollment->class->name,
                    'user_name' => 'Secretaria',
                    'created_at' => $enrollment->created_at
                ];
            });

        return $recentPayments->toBase()->merge($recentEnrollments->toBase())->sortByDesc('created_at')->take(5);
    }

    private function calculateAverageAttendance()
    {
        $totalAttendances = Attendance::whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->count();

        $presentAttendances = Attendance::whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->where('status', 'present')
            ->count();

        if ($totalAttendances == 0)
            return 0;

        return round(($presentAttendances / $totalAttendances) * 100, 1);
    }

    private function getPendingGradesCount()
    {
        return Grade::whereNull('grade')
            ->whereHas('assessment', function ($query) {
                $query->where('due_date', '>=', now());
            })
            ->count();
    }

    private function getAverageClassPerformance()
    {
        $average = Grade::whereYear('created_at', now()->year)
            ->avg('grade');

        return round($average ?? 0, 1);
    }

    private function getClassPerformance()
    {
        return ClassRoom::active()
            ->where('school_year', current_school_year())
            ->with([
                'students.grades' => function ($q) {
                    $q->whereYear('created_at', now()->year);
                }
            ])
            ->get()
            ->map(function ($class) {
                $grades = $class->students->flatMap->grades;
                $averageGrade = $grades->avg('grade');

                return [
                    'name' => $class->full_name ?? $class->name,
                    'class_name' => $class->full_name ?? $class->name,
                    'avg_grade' => round($averageGrade ?? 0, 1),
                    'average_grade' => round($averageGrade ?? 0, 1),
                    'student_count' => $class->students->count(),
                    'total_students' => $class->students->count(),
                    'teacher_name' => $class->teacher->full_name ?? ($class->teacher->user->name ?? 'N/A')
                ];
            });
    }

    private function getAttendanceStats()
    {
        $last7Days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $totalAttendances = Attendance::whereDate('attendance_date', $date)->count();
            $presentAttendances = Attendance::whereDate('attendance_date', $date)
                ->where('status', 'present')
                ->count();

            $percentage = $totalAttendances > 0 ?
                round(($presentAttendances / $totalAttendances) * 100, 1) : 0;

            $last7Days[] = [
                'date' => $date->format('d/m'),
                'percentage' => $percentage,
                'total' => $totalAttendances,
                'present' => $presentAttendances
            ];
        }

        return $last7Days;
    }

    private function getTeacherStats()
    {
        return Teacher::active()
            ->withCount([
                'classes' => function ($q) {
                    $q->active()->where('school_year', current_school_year());
                }
            ])
            ->with([
                'classes' => function ($q) {
                    $q->active()->where('school_year', current_school_year())->withCount('students');
                }
            ])
            ->get()
            ->map(function ($teacher) {
                $totalStudents = $teacher->classes->sum('students_count');

                return [
                    'name' => $teacher->full_name,
                    'classes_count' => $teacher->classes_count,
                    'total_students' => $totalStudents,
                    'specialization' => $teacher->specialization,
                    'email' => $teacher->email
                ];
            });
    }

    private function getTodaysSchedule($teacherId)
    {
        try {
            // Verificar se a tabela class_schedules existe
            if (!Schema::hasTable('class_schedules')) {
                return $this->getFallbackSchedule($teacherId);
            }

            $schedules = \App\Models\ClassSchedule::with(['class', 'subject'])
                ->where('teacher_id', $teacherId)
                ->where('weekday', now()->dayOfWeek)
                ->where('status', 'active')
                ->where('academic_year', current_school_year())
                ->orderBy('start_time')
                ->get()
                ->map(function ($schedule) {
                    $isCurrent = $schedule->isHappeningNow();

                    return [
                        'id' => $schedule->id,
                        'class_name' => $schedule->class->name ?? 'Turma não encontrada',
                        'grade_level' => $schedule->class->grade_level ?? 'N/A',
                        'subject' => $schedule->subject->name ?? 'Disciplina não encontrada',
                        'time' => $schedule->start_time->format('H:i') . ' - ' . $schedule->end_time->format('H:i'),
                        'time_range' => $schedule->time_range,
                        'classroom' => $schedule->classroom ?? $schedule->class->classroom ?? 'Sala não definida',
                        'is_current' => $isCurrent,
                        'status' => $isCurrent ? 'current' : ($schedule->start_time->format('H:i') > now()->format('H:i') ? 'upcoming' : 'completed'),
                        'duration' => $schedule->duration . ' min',
                        'weekday_name' => $schedule->weekday_name
                    ];
                });

            return $schedules;
        } catch (\Exception $e) {
            // Fallback: buscar turmas do professor e criar horários fictícios
            Log::error('Erro ao buscar horário do professor: ' . $e->getMessage());
            return $this->getFallbackSchedule($teacherId);
        }
    }

    private function getFallbackSchedule($teacherId)
    {
        $teacherClasses = ClassRoom::where('teacher_id', $teacherId)
            ->active()
            ->where('school_year', current_school_year())
            ->with(['subjects'])
            ->get();

        if ($teacherClasses->isEmpty()) {
            return collect();
        }

        $schedules = [];
        $startTimes = ['08:00', '09:30', '11:00', '14:00', '15:30'];
        $weekdayNames = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];

        foreach ($teacherClasses as $index => $class) {
            if ($class->subjects->isNotEmpty()) {
                $subject = $class->subjects->first();
                $startTime = $startTimes[$index % count($startTimes)];
                $endTime = date('H:i', strtotime($startTime . ' +90 minutes'));

                $schedules[] = [
                    'class_name' => $class->name,
                    'grade_level' => $class->grade_level,
                    'grade_level_name' => $class->grade_level_name,
                    'subject' => $subject->name,
                    'time' => $startTime . ' - ' . $endTime,
                    'classroom' => $class->classroom ?? 'Sala ' . ($index + 101),
                    'is_current' => false,
                    'status' => 'upcoming',
                    'weekday_name' => $weekdayNames[now()->dayOfWeek] ?? 'Hoje',
                    'is_fallback' => true // Para identificar que são dados simulados
                ];
            }
        }

        return collect($schedules);
    }

    private function calculateChildrenAttendanceRate($children)
    {
        $totalAttendances = 0;
        $presentAttendances = 0;

        foreach ($children as $child) {
            $childAttendances = $child->attendances->where('attendance_date', '>=', now()->subDays(30));
            $totalAttendances += $childAttendances->count();
            $presentAttendances += $childAttendances->where('status', 'present')->count();
        }

        if ($totalAttendances == 0)
            return 0;

        return round(($presentAttendances / $totalAttendances) * 100, 1);
    }

    // Método para API de contadores (usado no dashboard)
    public function counters()
    {
        $user = auth()->user();
        $data = [];

        switch ($user->role) {
            case 'admin':
                $data = [
                    'notifications' => $user->unreadNotifications->count(),
                    'overdue_payments' => Payment::overdue()->count(),
                    'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
                    'pending_leave_requests' => StaffLeaveRequest::where('status', 'pending')->count(),
                ];
                break;

            case 'secretary':
                $data = [
                    'notifications' => $user->unreadNotifications->count(),
                    'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
                    'overdue_payments' => Payment::overdue()->count(),
                    'todays_payments' => Payment::paid()->whereDate('payment_date', today())->count(),
                ];
                break;

            case 'teacher':
                $teacher = Teacher::findByUser($user->id);
                if ($teacher) {
                    $data = [
                        'notifications' => $user->unreadNotifications->count(),
                        'pending_grades' => Grade::where('teacher_id', $teacher->id)->whereNull('grade')->count(),
                        'todays_classes' => \App\Models\ClassSchedule::where('teacher_id', $teacher->id)
                            ->where('weekday', now()->dayOfWeek)
                            ->count(),
                    ];
                }
                break;

            case 'parent':
                $parent = $user->parent;
                if ($parent) {
                    $data = [
                        'notifications' => $user->unreadNotifications->count(),
                        'pending_payments' => Payment::whereIn('student_id', $parent->students->pluck('id'))
                            ->pending()
                            ->count(),
                        'children_count' => $parent->students->count(),
                    ];
                }
                break;
        }

        return response()->json($data);
    }
}
