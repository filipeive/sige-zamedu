<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\StudentArchiveController;
use App\Http\Controllers\Admin\StudentPromotionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GateKeeperController;
use App\Http\Controllers\HttpSmsWebhookController;
use App\Http\Controllers\LeaveRequestManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsInboxController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentSupportController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherPortalController;
use App\Http\Controllers\TerritorialDashboardController;
use App\Http\Controllers\TerritorialAggregationController;
use App\Http\Controllers\SchoolOnboardingController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/demo-access', [DemoController::class, 'index'])->name('demo.access');
Route::get('/demo-access/{role}', [DemoController::class, 'loginAsRole'])->name('demo.role');

// Rota de Licença Suspensa
Route::get('/license/suspended', [LicenseController::class, 'suspended'])->name('license.suspended');

// Páginas públicas diretas
Route::get('/sobre', fn () => view('public.about'))->name('sobre');
Route::get('/contacto', fn () => view('public.contact'))->name('contacto');
Route::get('/registar-escola', [App\Http\Controllers\TrialRegisterController::class, 'create'])->name('public.trial-register');
Route::get('/pre-inscricao-online', [App\Http\Controllers\TrialRegisterController::class, 'create'])->name('trial.register');
Route::post('/registar-escola', [App\Http\Controllers\TrialRegisterController::class, 'store'])->name('public.trial-register.store');

// Informações públicas da escola
Route::prefix('public')->name('public.')->group(function () {
    Route::get('/about', fn () => view('public.about'))->name('about');
    Route::get('/contact', fn () => view('public.contact'))->name('contact');
    Route::get('/material-lists', [App\Http\Controllers\PublicInfoController::class, 'materialLists'])->name('material-lists');
    Route::get('/announcements', [App\Http\Controllers\PublicInfoController::class, 'announcements'])->name('announcements');

    // Pré-matrícula online
    Route::get('/pre-enrollment', [App\Http\Controllers\EnrollmentApplicationController::class, 'create'])->name('pre-enrollment');
    Route::post('/pre-enrollment', [App\Http\Controllers\EnrollmentApplicationController::class, 'store'])->name('pre-enrollment.store');
    Route::get('/pre-enrollment/success/{application}', function (App\Models\EnrollmentApplication $application) {
        return view('public.pre-enrollment-success', compact('application'));
    })->name('pre-enrollment.success');
    // Verificação de pagamentos — removida exposição pública.
    // Se for necessário consulta pública, implementar token temporário por e-mail/SMS.
});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Autenticadas)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Painel Territorial de Inspeção & Analytics MINEDH
    Route::prefix('territorial')->name('territorial.')->group(function () {
        Route::get('/dashboard', [TerritorialDashboardController::class, 'index'])->name('dashboard');
        Route::get('/school/{school}', [TerritorialDashboardController::class, 'schoolDrillDown'])->name('school');
        Route::get('/map', [TerritorialAggregationController::class, 'pedagogicalMap'])->name('map');
        Route::get('/map/pdf', [TerritorialAggregationController::class, 'exportPdf'])->name('map.pdf');
        Route::get('/map/csv', [TerritorialAggregationController::class, 'exportCsv'])->name('map.csv');
    });

    // Onboarding Multi-Escola
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/school', [SchoolOnboardingController::class, 'wizard'])->name('school');
        Route::post('/school', [SchoolOnboardingController::class, 'store'])->name('school.store');
        Route::get('/school/success/{school}', [SchoolOnboardingController::class, 'success'])->name('school.success');
    });

    // Perfil do Usuário
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // Pesquisa Global & Quick Search
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::get('/search/quick', [SearchController::class, 'quickSearch'])->name('search.quick');

    // Portaria Digital (Controlo de Acesso)
    Route::get('/gatekeeper', [GateKeeperController::class, 'index'])->name('gatekeeper.index');
    Route::post('/gatekeeper/scan', [GateKeeperController::class, 'scan'])->name('gatekeeper.scan');
    Route::post('/gatekeeper/{student}/log', [GateKeeperController::class, 'logAccess'])->name('gatekeeper.log');
    Route::get('/gatekeeper/{student}/qr', [GateKeeperController::class, 'generateQr'])->name('gatekeeper.qr');
    Route::get('/gatekeeper/{student}/card', [GateKeeperController::class, 'downloadCard'])->name('gatekeeper.card');
    Route::get('/gatekeeper/{student}/history', [GateKeeperController::class, 'history'])->name('gatekeeper.history');


    /*
    |--------------------------------------------------------------------------
    | Gestão de Alunos
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_students')
        ->prefix('students')
        ->name('students.')
        ->controller(StudentController::class)
        ->group(function () {
            Route::middleware('permission:create_students')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
            });

            Route::get('/', 'index')->name('index');
            Route::get('/{student}', 'show')->name('show');
            Route::get('/{student}/grades', 'grades')->name('grades');
            Route::get('/{student}/grades/pdf', 'gradesPdf')->name('grades.pdf');
            Route::post('/{student}/grades/share-email', 'shareGradesEmail')->name('grades.share-email');
            Route::get('/{student}/attendance', 'attendance')->name('attendance');
            Route::get('/{student}/payments', 'payments')->name('payments');
            Route::get('/{student}/support', [StudentSupportController::class, 'index'])
                ->middleware('permission:view_observations')
                ->name('support.index');

            Route::middleware('permission:edit_students')->group(function () {
                Route::get('/{student}/edit', 'edit')->name('edit');
                Route::patch('/{student}', 'update')->name('update');
                Route::post('/{student}/upload-photo', 'uploadPhoto')->name('upload-photo');
            });

            Route::middleware('permission:create_observations')->group(function () {
                Route::post('/{student}/support/observations', [StudentSupportController::class, 'storeObservation'])
                    ->name('support.observations.store');
            });

            Route::middleware('permission:manage_observations')->group(function () {
                Route::delete('/{student}/support/observations/{observation}', [StudentSupportController::class, 'destroyObservation'])
                    ->name('support.observations.destroy');
            });

            Route::middleware('permission:manage_student_records')->group(function () {
                Route::post('/{student}/support/records', [StudentSupportController::class, 'storeRecord'])
                    ->name('support.records.store');
                Route::delete('/{student}/support/records/{record}', [StudentSupportController::class, 'destroyRecord'])
                    ->name('support.records.destroy');
                Route::post('/{student}/support/mark-exit', [StudentSupportController::class, 'markExit'])
                    ->name('support.mark-exit');
            });

            // SMS ao encarregado
            Route::post('/{student}/send-sms', [SmsController::class, 'sendToGuardian'])
                ->name('sms.send');

            Route::middleware('permission:delete_students')->group(function () {
                Route::delete('/{student}', 'destroy')->name('destroy');
            });
        });

    // Gestão de Encarregados de Educação
    Route::middleware('permission:view_students')
        ->prefix('parents')
        ->name('parents.')
        ->controller(\App\Http\Controllers\ParentController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{parent}', 'show')->name('show');
            Route::get('/{parent}/edit', 'edit')->name('edit');
            Route::put('/{parent}', 'update')->name('update');
            Route::patch('/{parent}', 'update')->name('update');
            Route::delete('/{parent}', 'destroy')->name('destroy');
        });

    // Rotas de Controlo do Super Admin & Modo Suporte Técnico
    Route::middleware(['auth', 'role:super_admin'])
        ->prefix('super-admin')
        ->name('super-admin.')
        ->controller(\App\Http\Controllers\SuperAdminControlController::class)
        ->group(function () {
            Route::post('/impersonate/{school}', 'impersonate')->name('impersonate');
            Route::post('/stop-impersonating', 'stopImpersonating')->name('stop-impersonating');
        });

    // Emissão de Certidões & Certificados Oficiais MINEDH
    Route::middleware(['auth', 'role:admin|super_admin|secretary|pedagogy'])
        ->prefix('certificates')
        ->name('certificates.')
        ->controller(\App\Http\Controllers\CertificateController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{student}/certidao', 'certidao')->name('certidao');
            Route::get('/{student}/habilidades', 'certidao')->name('habilidades');
            Route::get('/{student}/certificado', 'certificado')->name('certificado');
            Route::get('/{student}/conclusao', 'certificado')->name('conclusao');
            Route::get('/{student}/official', 'officialCertificate')->name('official');
        });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Professores
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_teachers')
        ->prefix('teachers')
        ->name('teachers.')
        ->controller(TeacherController::class)
        ->group(function () {
            Route::middleware('permission:create_teachers')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
            });

            Route::get('/', 'index')->name('index');
            Route::get('/{teacher}', 'show')->name('show');
            Route::get('/{teacher}/classes', 'classes')->name('classes');
            Route::get('/{teacher}/schedule', 'schedule')->name('schedule');

            Route::middleware('permission:edit_teachers')->group(function () {
                Route::get('/{teacher}/edit', 'edit')->name('edit');
                Route::patch('/{teacher}', 'update')->name('update');
                Route::post('/{teacher}/toggle-status', 'toggleStatus')->name('toggle-status');
                Route::post('/{teacher}/assign-class', 'assignClass')->name('assign-class');
            });

            Route::middleware('permission:delete_teachers')->group(function () {
                Route::delete('/{teacher}', 'destroy')->name('destroy');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Turmas
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_classes')
        ->prefix('classes')
        ->name('classes.')
        ->controller(ClassRoomController::class)
        ->group(function () {
            Route::middleware('permission:create_classes')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
            });

            Route::get('/', 'index')->name('index');
            Route::get('/{class}', 'show')->name('show');
            Route::get('/{class}/students', 'students')->name('students');

            Route::middleware('permission:edit_classes')->group(function () {
                Route::get('/{class}/edit', 'edit')->name('edit');
                Route::patch('/{class}', 'update')->name('update');
                Route::post('/{class}/assign-teacher', 'assignTeacher')->name('assign-teacher');
                Route::post('/{class}/add-students', 'addStudent')->name('add-student');
                Route::post('/{class}/remove-student/{student}', 'removeStudent')->name('remove-student');
                Route::post('/{class}/assign-subjects', 'assignSubjects')->name('assign-subjects');
                Route::post('/{class}/update-assistant', 'updateAssistant')->name('update-assistant');
            });

            Route::middleware('permission:delete_classes')->group(function () {
                Route::delete('/{class}', 'destroy')->name('destroy');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Disciplinas
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_subjects')
        ->prefix('subjects')
        ->name('subjects.')
        ->controller(SubjectController::class)
        ->group(function () {
            Route::middleware('permission:create_subjects')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
            });

            Route::get('/', 'index')->name('index');
            Route::get('/{subject}', 'show')->name('show');
            Route::get('/{subject}/classes', 'classes')->name('classes');
            Route::get('/{subject}/grades', 'grades')->name('grades');

            Route::middleware('permission:edit_subjects')->group(function () {
                Route::get('/{subject}/edit', 'edit')->name('edit');
                Route::patch('/{subject}', 'update')->name('update');
            });

            Route::middleware('permission:delete_subjects')->group(function () {
                Route::delete('/{subject}', 'destroy')->name('destroy');
            });

            Route::middleware('permission:manage_subjects')->group(function () {
                Route::post('/{subject}/assign-to-class', 'assignToClass')->name('assign-to-class');
                Route::delete('/{subject}/remove-from-class/{class}', 'removeFromClass')->name('remove-from-class');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Matrículas
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_enrollments')
        ->prefix('enrollments')
        ->name('enrollments.')
        ->controller(EnrollmentController::class)
        ->group(function () {
            Route::middleware('permission:create_enrollments')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
            });

            Route::get('/', 'index')->name('index');
            Route::get('/{enrollment}', 'show')->name('show');
            Route::get('/{enrollment}/print', 'print')->name('print');

            Route::middleware('permission:edit_enrollments')->group(function () {
                Route::get('/{enrollment}/edit', 'edit')->name('edit');
                Route::patch('/{enrollment}', 'update')->name('update');
                Route::post('/{enrollment}/activate', 'activate')->name('activate');
                Route::post('/{enrollment}/cancel', 'cancel')->name('cancel');
                Route::post('/{enrollment}/transfer', 'transfer')->name('transfer');
                Route::post('/{enrollment}/confirm-payment', 'confirmPayment')->name('confirm-payment');
            });
            // Gestão de Pedidos de Matrícula (Pré-inscrições e Renovação)
            Route::prefix('applications')->name('applications.')->controller(App\Http\Controllers\EnrollmentApplicationController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{application}', 'show')->name('show');
                Route::post('/{application}/status', 'updateStatus')->name('update-status');
                Route::post('/{application}/confirm-payment', 'confirmPayment')->name('confirm-payment');
                Route::post('/{application}/finalize', 'finalize')->name('finalize');
            });
        });

    /*
|--------------------------------------------------------------------------
| Gestão Financeira
|--------------------------------------------------------------------------
*/
    /*
    |--------------------------------------------------------------------------
    | Gestão Financeira
    |--------------------------------------------------------------------------
    */

    Route::middleware(['financial.access', 'permission:view_payments'])
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {

            // Rotas de criação
            Route::middleware('permission:create_payments')->group(function () {
                Route::get('/create', [App\Http\Controllers\PaymentController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\PaymentController::class, 'store'])->name('store');
            });

            // Rotas principais (GET)
            Route::get('/', [App\Http\Controllers\PaymentController::class, 'index'])->name('index');
            Route::get('/references', [App\Http\Controllers\PaymentController::class, 'references'])->name('references');
            Route::get('/pending', [App\Http\Controllers\PaymentController::class, 'references'])->name('pending');
            Route::get('/reports', [App\Http\Controllers\PaymentController::class, 'reports'])->name('reports');
            Route::get('/overdue', [App\Http\Controllers\PaymentController::class, 'overdue'])->name('overdue');
            Route::get('/with-penalties', [App\Http\Controllers\PaymentController::class, 'withPenalties'])->name('with-penalties');
            Route::get('/print-bulk', [App\Http\Controllers\PaymentController::class, 'printBulk'])->name('print-bulk');
            Route::get('/{payment}', [App\Http\Controllers\PaymentController::class, 'show'])->name('show');

            // Rotas de processamento
            Route::middleware('permission:process_payments')->group(function () {
                Route::post('/{payment}/process', [App\Http\Controllers\PaymentController::class, 'process'])->name('process');
                Route::post('/{payment}/cancel', [App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');
                Route::post('/{payment}/apply-penalty', [App\Http\Controllers\PaymentController::class, 'applyPenalty'])->name('apply-penalty');
                Route::post('/{payment}/remove-penalty', [App\Http\Controllers\PaymentController::class, 'removePenalty'])->name('remove-penalty');
                Route::post('/apply-bulk-penalties', [App\Http\Controllers\PaymentController::class, 'applyBulkPenalties'])->name('apply-bulk-penalties');
            });

            // Rotas de geração de referências
            Route::middleware('permission:generate_payment_references')->group(function () {
                Route::post('/generate-reference', [App\Http\Controllers\PaymentController::class, 'generateReference'])->name('generate-reference');
                Route::post('/generate-monthly-fees', [App\Http\Controllers\PaymentController::class, 'generateMonthlyFees'])->name('generate-monthly-fees');
                Route::get('/reference/{payment}/download', [App\Http\Controllers\PaymentController::class, 'downloadReference'])->name('download-reference');
            });
        });
    /*
    |--------------------------------------------------------------------------
    | Gestão Financeira - Despesas
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:manage_expenses')->prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [App\Http\Controllers\ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}', [App\Http\Controllers\ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [App\Http\Controllers\ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [App\Http\Controllers\ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('destroy');
        Route::post('/{expense}/approve', [App\Http\Controllers\ExpenseController::class, 'approve'])->name('approve');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Presenças
    |--------------------------------------------------------------------------
    */

    /*  Route::middleware('permission:view_attendances')
         ->prefix('attendances')
         ->name('attendances.')
         ->controller(AttendanceController::class)
         ->group(function () {
             Route::get('/', 'index')->name('index');
             Route::get('/reports', 'reports')->name('reports');
             Route::get('/class/{class}/report', 'classReport')->name('class-report');
             Route::get('/student/{student}/report', 'studentReport')->name('student-report');

             Route::middleware('permission:mark_attendances')->group(function () {
                 Route::get('/mark', 'mark')->name('mark');
                 Route::post('/mark', 'storeMark')->name('store-mark');
                 Route::get('/class/{class}/mark', 'markByClass')->name('mark-by-class');
                 Route::post('/class/{class}/mark', 'storeMarkByClass')->name('store-mark-by-class');
             });
         }); */

    // ========== GESTÃO DE PRESENÇAS ==========
    Route::middleware('permission:view_attendances')->prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/class/{class}', [AttendanceController::class, 'classAttendances'])->name('class-attendances');
        
        Route::get('/reports', [AttendanceController::class, 'reports'])->name('reports');
        Route::get('/class/{class}/report', [AttendanceController::class, 'classReport'])->name('class-report');
        Route::get('/student/{student}/report', [AttendanceController::class, 'studentReport'])->name('student-report');

        Route::middleware('permission:mark_attendances')->group(function () {
            Route::get('/class/{class}/mark', [AttendanceController::class, 'markByClass'])->name('mark-by-class');
            Route::post('/class/{class}/mark', [AttendanceController::class, 'storeMarkByClass'])->name('store-mark-by-class');
        });

        Route::get('/{attendance}', [AttendanceController::class, 'show'])->name('show');
        Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');

        Route::middleware('permission:mark_attendances')->group(function () {
            Route::patch('/{attendance}', [AttendanceController::class, 'update'])->name('update');
            Route::delete('/{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('permission:export_reports')->group(function () {
            Route::get('/export', [AttendanceController::class, 'export'])->name('export');
        });
    });

    // ========== API ROUTES PARA ATTENDANCES ==========
    Route::prefix('api')->name('api.')->group(function () {
        // Obter alunos de uma turma (usado na interface de marcar presenças)
        Route::get('/classes/{class}/students', [AttendanceController::class, 'getClassStudents'])
            ->name('classes.students');

        // Estatísticas de presenças
        Route::get('/attendances/stats', [App\Http\Controllers\Api\AttendanceApiController::class, 'getStats'])
            ->name('attendances.stats');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Notas/Avaliações
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_grades')
        ->prefix('grades')
        ->name('grades.')
        ->controller(GradeController::class)
        ->group(function () {
            Route::get('/', 'dashboard')->name('index');
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/student/{student}/report-card', 'reportCard')->name('student-report-card');
            Route::get('/class/{class}/report', 'classReport')->name('class-report');
            Route::get('/class/{class}/grade-sheet', 'gradeSheet')->name('grade-sheet');

            Route::middleware('permission:create_grades')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/batch-create', 'batchCreate')->name('batch-create');
                Route::post('/batch-store', 'batchStore')->name('batch-store');
            });

            Route::middleware('permission:edit_grades')->group(function () {
                Route::get('/{grade}/edit', 'edit')->name('edit');
                Route::patch('/{grade}', 'update')->name('update');
            });
        });

    // Módulo de Pautas Oficiais (Moçambique - MINEDH)
    Route::middleware('permission:view_grades')
        ->prefix('pautas')
        ->name('pautas.')
        ->controller(\App\Http\Controllers\PautaController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/trimestral/{class}', 'trimestral')->name('trimestral');
            Route::get('/trimestral/{class}/excel', 'exportExcel')->name('trimestral.excel');
            Route::get('/anual/{class}', 'anual')->name('anual');
            Route::get('/final/{class}', 'final')->name('final');
            Route::get('/exame/{class}', 'exame')->name('exame');
            Route::post('/exame/{class}/jury', 'updateJury')->name('exame.jury');
            Route::get('/pdf/{class}', 'exportPdf')->name('pdf');
            Route::get('/excel/{class}', 'exportExcel')->name('excel');
            Route::post('/update-calculation-rules', 'updateCalculationRules')->name('update-rules');
        });

    /*
    |--------------------------------------------------------------------------
    | Gestão de Eventos
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_events')
        ->prefix('events')
        ->name('events.')
        ->controller(EventController::class)
        ->group(function () {
            Route::middleware('permission:create_events')->group(function () {
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
            });

            Route::get('/', 'index')->name('index');
            Route::get('/calendar', 'calendar')->name('calendar');
            Route::get('/{event}', 'show')->name('show');

            Route::middleware('permission:edit_events')->group(function () {
                Route::get('/{event}/edit', 'edit')->name('edit');
                Route::patch('/{event}', 'update')->name('update');
                Route::post('/{event}/send-notification', 'sendNotification')->name('send-notification');
            });

            Route::middleware('permission:delete_events')->group(function () {
                Route::delete('/{event}', 'destroy')->name('destroy');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Comunicações e Notificações
    |--------------------------------------------------------------------------
    */

    Route::prefix('communications')->name('communications.')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');

        Route::middleware('permission:send_notifications')->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/send', 'send')->name('send');
        });

        Route::middleware('permission:send_bulk_notifications')->group(function () {
            Route::get('/bulk', 'bulk')->name('bulk');
            Route::post('/bulk-send', 'bulkSend')->name('bulk-send');
        });
    });

    // Notificações do Usuário
    Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'userNotifications')->name('index');
        Route::post('/{notification}/read', 'markAsRead')->name('mark-read');
        Route::post('/mark-all-read', 'markAllAsRead')->name('mark-all-read');
        Route::delete('/clear-all', 'clearAll')->name('clear-all');
    });

    /*
    |--------------------------------------------------------------------------
    | Relatórios
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:view_reports')
        ->prefix('reports')
        ->name('reports.')
        ->controller(ReportController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');

            // Relatórios Acadêmicos
            Route::get('/academic', 'academic')->name('academic');
            Route::get('/academic/performance', 'performance')->name('academic.performance');
            Route::get('/academic/attendance', 'attendanceReport')->name('academic.attendance');
            Route::get('/pedagogical-map', [\App\Http\Controllers\PedagogicalMapController::class, 'index'])->name('pedagogical-map');
            Route::get('/pedagogical-map/export', [\App\Http\Controllers\PedagogicalMapController::class, 'exportCsv'])->name('pedagogical-map.export');

            // Relatórios Financeiros
            Route::middleware('permission:view_financial_reports')->group(function () {
                Route::get('/financial', 'financial')->name('financial');
                Route::get('/financial/revenue', 'revenue')->name('financial.revenue');
                Route::get('/financial/defaulters', 'defaulters')->name('financial.defaulters');
            });

            // Exportações
            Route::middleware('permission:export_reports')->group(function () {
                Route::get('/export/students', 'exportStudents')->name('export.students');
                Route::get('/export/payments', 'exportPayments')->name('export.payments');
                Route::get('/export/grades', 'exportGrades')->name('export.grades');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Portal dos Pais
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:parent')
        ->prefix('parent')
        ->name('parent.')
        ->controller(ParentPortalController::class)
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');

            // Informações dos filhos
            Route::get('/children', 'children')->name('children');
            Route::get('/student/{student}', 'studentDetails')->name('student-details');
            Route::get('/student/{student}/grades', 'studentGrades')->name('student-grades');
            Route::get('/student/{student}/attendance', 'studentAttendance')->name('student-attendance');

            // Renovação de Matrícula
            Route::get('/student/{student}/renewal', [App\Http\Controllers\EnrollmentApplicationController::class, 'renewalCreate'])->name('student-renewal');
            Route::post('/student/{student}/renewal', [App\Http\Controllers\EnrollmentApplicationController::class, 'renewalStore'])->name('student-renewal.store');

            // Pagamentos
            Route::get('/payments', 'payments')->name('payments');
            Route::get('/student/{student}/payments', 'studentPayments')->name('student-payments');
            Route::get('/student/{student}/payment-references', 'paymentReferences')->name('payment-references');
            Route::post('/generate-payment-reference', 'generatePaymentReference')->name('generate-payment-reference');
            Route::post('/payments/{payment}/pay-online', [App\Http\Controllers\PaymentController::class, 'initiateOnlinePayment'])->name('pay-online');

            // Comunicações
            Route::get('/communications', 'communications')->name('communications');
            Route::post('/send-message', 'sendMessage')->name('send-message');
        });

    /*
    |--------------------------------------------------------------------------
    | Portal do Professor
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:teacher'])
        ->prefix('teacher')
        ->name('teacher.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [TeacherPortalController::class, 'dashboard'])->name('dashboard');

            // Turmas
            Route::prefix('classes')->name('classes.')->group(function () {
                Route::get('/', [TeacherPortalController::class, 'classes'])->name('index');
                Route::get('/{classId}', [TeacherPortalController::class, 'classDetails'])->name('detail');
                Route::get('/{classId}/students', [TeacherPortalController::class, 'classStudents'])->name('students');
            });

            // Presenças
            Route::prefix('attendance')->name('attendance.')->group(function () {
                // Presenças de hoje
                Route::get('/today/{classId}', [TeacherPortalController::class, 'todayAttendance'])->name('today');
                // Formulário de presenças para uma turma
                Route::get('/class/{classId}', [TeacherPortalController::class, 'Classattendance'])->name('class');
                // Salvar presenças
                Route::post('/class/{classId}', [TeacherPortalController::class, 'storeAttendance'])->name('store');
                // Marcar presença individual
                Route::post('/mark/{classId}', [TeacherPortalController::class, 'markAttendance'])->name('mark');
            });

            // Adicionar aluno à turma
            Route::post('/classes/{class}/add-student', [TeacherPortalController::class, 'addStudentToClass'])->name('classes.add-student');

            // Caderno de Notas (Gradebook)
            Route::get('/gradebook/{class}', [TeacherPortalController::class, 'gradebook'])->name('gradebook');

            // Notas/Avaliações
            Route::prefix('grades')->name('grades.')->group(function () {
                Route::get('/pending', [TeacherPortalController::class, 'pendingGrades'])->name('pending');
                Route::post('/store', [TeacherPortalController::class, 'storeGrade'])->name('store');
                Route::post('/batch-update', [TeacherPortalController::class, 'batchUpdateGrades'])->name('batch-update');
            });

            // Comunicações
            Route::prefix('communications')->name('communications.')->group(function () {
                Route::get('/', [TeacherPortalController::class, 'communications'])->name('index');
                Route::get('/create', [TeacherPortalController::class, 'createCommunication'])->name('create');
                Route::post('/send', [TeacherPortalController::class, 'sendCommunication'])->name('send');
            });

            // Licenças
            Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
                Route::get('/', [TeacherPortalController::class, 'leaveRequests'])->name('index');
                Route::get('/create', [TeacherPortalController::class, 'createLeaveRequest'])->name('create');
                Route::post('/', [TeacherPortalController::class, 'storeLeaveRequest'])->name('store');
            });

            // Perfil
            Route::get('/profile', [TeacherPortalController::class, 'profile'])->name('profile');
            Route::post('/profile', [TeacherPortalController::class, 'updateProfile'])->name('update-profile');

            // Criação de Perfil (Primeiro Acesso)
            Route::get('/create-profile', [TeacherPortalController::class, 'createProfile'])->name('create-profile');
            Route::post('/create-profile', [TeacherPortalController::class, 'storeProfile'])->name('store-profile');
        });

    /*
    |--------------------------------------------------------------------------
    | Gestão Central de Licenças (Admin/Pedagogia)
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:manage_leave_requests')
        ->prefix('staff-leave-requests')
        ->name('staff-leave-requests.')
        ->controller(LeaveRequestManagementController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export/csv', 'exportCsv')->name('export.csv');
            Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
            Route::get('/{leaveRequest}', 'show')->name('show');

            Route::middleware('permission:approve_leave_requests')->group(function () {
                Route::post('/{leaveRequest}/approve', 'approve')->name('approve');
                Route::post('/{leaveRequest}/reject', 'reject')->name('reject');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Administração
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:manage_users')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Gestão de Usuários
            Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{user}', 'show')->name('show');
                Route::get('/{user}/edit', 'edit')->name('edit');
                Route::patch('/{user}', 'update')->name('update');
                Route::delete('/{user}', 'destroy')->name('destroy');
                Route::post('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
            });

            // Configurações do Sistema
            Route::prefix('settings')
                ->name('settings.')
                ->controller(SettingsController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::patch('/', 'update')->name('update');
                    Route::post('/remove-logo', 'removeLogo')->name('remove-logo');
                    Route::post('/permissions', 'updatePermissions')->name('permissions.update');
                });

            // Gestão de Licença
            Route::get('/license', [LicenseController::class, 'index'])->name('license');
            Route::put('/license', [LicenseController::class, 'update'])->name('license.update');

            // Comunicações (Acesso para Admin/Secretaria sem restrição)
            Route::get('/communications', [NotificationController::class, 'index'])->name('communications.index');
            Route::get('/communications/create', [NotificationController::class, 'create'])->name('communications.create');
            Route::post('/communications/send', [NotificationController::class, 'send'])->name('communications.send');

            // Backup
            Route::middleware('permission:backup_system')->group(function () {
                Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
                Route::post('/backup/create', [SettingsController::class, 'createBackup'])->name('backup.create');
                Route::get('/backup/download/{filename}', [SettingsController::class, 'downloadBackup'])->name('backup.download');
                Route::get('/backup/delete/{filename}', [SettingsController::class, 'deleteBackup'])->name('backup.delete');
                Route::delete('/backup/delete/{filename}', [SettingsController::class, 'deleteBackup']);
            });

            // Gestão de Ano Lectivo
            Route::prefix('academic-years')->name('academic-years.')->controller(\App\Http\Controllers\Admin\AcademicYearController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/transition', 'transition')->name('transition');
            });

            // Passagem de Classe (Promoção)
            Route::get('/promotion', [StudentPromotionController::class, 'index'])->name('promotion.index');
            Route::post('/promotion/process', [StudentPromotionController::class, 'promote'])->name('promotion.process');
            Route::post('/promotion/reset', [StudentPromotionController::class, 'reset'])->name('promotion.reset');

            // Renovação de Matrículas
            Route::get('/enrollments/renewals', [EnrollmentController::class, 'renewalIndex'])->name('enrollments.renewals');
            Route::post('/enrollments/{student}/renew', [EnrollmentController::class, 'renew'])->name('enrollments.renew');

            // Estudantes Arquivados
            Route::get('/students-archive', [StudentArchiveController::class, 'index'])->name('students-archive.index');
            Route::get('/students-archive/{student}', [StudentArchiveController::class, 'show'])->name('students-archive.show');
            Route::post('/students-archive/{student}/reactivate', [StudentArchiveController::class, 'reactivate'])->name('students-archive.reactivate');

            // Logs
            Route::middleware('permission:view_logs')->group(function () {
                Route::get('/logs', [SettingsController::class, 'logs'])->name('logs');
            });

            // Auditoria
            Route::middleware('permission:view_audit_logs')->group(function () {
                Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | API Routes (Internas)
    |--------------------------------------------------------------------------
    */

    Route::prefix('api')->name('api.')->group(function () {

        // Contadores do Dashboard
        Route::get('/dashboard/counters', [DashboardController::class, 'counters'])->name('dashboard.counters');

        // Pesquisa ao vivo
        Route::get('/search/live', [SearchController::class, 'liveSearch'])->name('search.live');

        // Notificações para pais
        Route::get('/parent/notifications-check', function () {
            $hasNewNotifications = auth()->user()->unreadNotifications()
                ->where('created_at', '>', now()->subMinutes(5))
                ->exists();

            return response()->json([
                'hasNewNotifications' => $hasNewNotifications,
                'count' => auth()->user()->unreadNotifications->count(),
            ]);
        })->name('parent.notifications-check');

        // Dados para gráficos
        Route::get('/charts/revenue-monthly', [ReportController::class, 'monthlyRevenueChart'])->name('charts.revenue-monthly');
        Route::get('/charts/attendance-weekly', [ReportController::class, 'weeklyAttendanceChart'])->name('charts.attendance-weekly');
        Route::get('/charts/students-by-grade', [ReportController::class, 'studentsByGradeChart'])->name('charts.students-by-grade');

        // Log de erros JavaScript
        Route::post('/log-js-error', function (Illuminate\Http\Request $request) {
            Log::error('JavaScript Error', $request->all());

            return response()->json(['status' => 'logged']);
        })->name('log-js-error');
    });
});

/*
|--------------------------------------------------------------------------
| Webhooks (Pagamentos + httpSMS)
|--------------------------------------------------------------------------
*/

Route::prefix('webhooks')->name('webhooks.')->group(function () {

    // MPesa
    Route::post('/mpesa', [App\Http\Controllers\PaymentController::class, 'webhookMpesa'])->name('mpesa');

    // eMola
    Route::post('/emola', [App\Http\Controllers\PaymentController::class, 'webhookEmola'])->name('emola');

    // Multicaixa
    Route::post('/multicaixa', [App\Http\Controllers\PaymentController::class, 'webhookMulticaixa'])->name('multicaixa');

    // httpSMS — CloudEvents 1.0 (mensagens recebidas, entregas, falhas, heartbeat)
    Route::post('/httpsms', [HttpSmsWebhookController::class, 'handle'])->name('httpsms');
});

/*
|--------------------------------------------------------------------------
| AI Assistant
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('ai')->name('ai.')->controller(AiController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/new', 'newConversation')->name('new');

    // Widget AJAX (definido ANTES das rotas com {conversation} para evitar conflitos de binding)
    Route::post('/widget/chat', 'widgetChat')->name('widget.chat');
    Route::post('/widget/reset', 'widgetReset')->name('widget.reset');

    // Conversas persistidas — o where numérico impede que "widget" caia aqui
    Route::get('/{conversation}', 'show')->whereNumber('conversation')->name('show');
    Route::post('/{conversation}/chat', 'chat')->whereNumber('conversation')->name('chat');
    Route::delete('/{conversation}', 'destroy')->whereNumber('conversation')->name('destroy');
});

Route::middleware(['auth'])->prefix('sms')->name('sms.')->controller(SmsLogController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/resend/{log}', 'resend')->name('resend');
    Route::delete('/{log}', 'destroy')->name('destroy');
});

Route::middleware(['auth'])->prefix('sms/inbox')->name('sms.inbox.')->controller(SmsInboxController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{message}', 'show')->name('show');
    Route::post('/{message}/read', 'markRead')->name('read');
    Route::post('/{message}/reply', 'reply')->name('reply');
    Route::post('/mark-all-read', 'markAllRead')->name('mark-all');
});

Route::middleware(['auth'])->prefix('communications/sms')->name('communications.sms.')->controller(SmsController::class)->group(function () {
    Route::post('/send-single', 'sendSingle')->name('send-single');
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return view('errors.404');
});
