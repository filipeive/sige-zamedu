@extends('layouts.app')

@section('title', 'Portal do Professor')
@section('page-title', 'Meu Portal')
@section('page-title-icon', 'fas fa-chalkboard-teacher')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Dashboard</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Boas-vindas -->
            <div class="school-card mb-4">
                <div class="school-card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3>Bem-vindo(a), {{ $teacher->first_name }}!</h3>
                            <p class="text-muted mb-0">
                                <i class="fas fa-graduation-cap"></i> {{ $teacher->qualification }} em
                                {{ $teacher->specialization }} |
                                <i class="fas fa-clock"></i> {{ $teacher->years_experience }} anos de experiência |
                                <i class="fas fa-chalkboard"></i> {{ $stats['total_classes'] }} turmas
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="user-avatar mx-auto"
                                style="width: 80px; height: 80px; font-size: 24px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white;">
                                {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="row mb-4 g-3">
                <!-- Minhas Turmas -->
                <div class="col-md-2 col-6">
                    <div class="school-card h-100 p-2 position-relative">
                        <div class="school-card-body text-center p-2">
                            <div class="text-primary mb-1">
                                <i class="fas fa-chalkboard fa-lg"></i>
                            </div>
                            <h5 class="mb-0">{{ $stats['total_classes'] }}</h5>
                            <a href="{{ route('teacher.classes.index') }}" class="btn btn-school btn-primary btn-xs mt-1">
                                Ver todas
                            </a>
                        </div>
                        <span class="badge bg-primary position-absolute" style="top: 12px; right: 8px; font-size: 0.85em;">
                            Turmas
                        </span>
                    </div>
                </div>
                <!-- Total de Alunos -->
                <div class="col-md-2 col-6">
                    <div class="school-card h-100 p-2">
                        <div class="school-card-body text-center p-2">
                            <div class="text-success mb-1">
                                <i class="fas fa-user-graduate fa-lg"></i>
                            </div>
                            <h5 class="mb-0">{{ $stats['total_students'] }}</h5>
                            <small class="text-muted">Alunos</small>
                            <span class="small text-success d-block">Sob orientação</span>
                        </div>
                    </div>
                </div>
                <!-- Presenças Hoje -->
                <div class="col-md-2 col-6">
                    <div class="school-card h-100 p-2">
                        <div class="school-card-body text-center p-2">
                            <div class="text-info mb-1">
                                <i class="fas fa-clipboard-check fa-lg"></i>
                            </div>
                            <h5 class="mb-0">{{ $stats['today_attendance'] }}</h5>
                            <small class="text-muted">Presenças</small>
                            <span class="small text-info d-block">Hoje</span>
                        </div>
                    </div>
                </div>
                <!-- Próximos Eventos -->
                <div class="col-md-2 col-6">
                    <div class="school-card h-100 p-2">
                        <div class="school-card-body text-center p-2">
                            <div class="text-primary mb-1">
                                <i class="fas fa-calendar-alt fa-lg"></i>
                            </div>
                            <h5 class="mb-0">{{ $stats['upcoming_events'] }}</h5>
                            <small class="text-muted">Eventos</small>
                            <span class="small text-primary d-block">Agendados</span>
                        </div>
                    </div>
                </div>
                <!-- Avaliações Pendentes -->
                 <div class="col-md-2 col-6">
                    <div class="school-card h-100 p-2 position-relative">
                        <div class="school-card-body text-center p-2">
                            <div class="text-primary mb-1">
                                <i class="fas fa-chalkboard fa-lg"></i>
                            </div>
                              <h5 class="mb-0">{{ $stats['pending_grades'] }}</h5>
                           <a href="{{ route('teacher.grades.pending') }}" class="btn btn-school btn-warning btn-xs mt-1">
                                Detalhes
                            </a>
                        </div>
                        <span class="badge bg-warning position-absolute" style="top: 12px; right: 8px; font-size: 0.85em;">
                            Pendentes
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Minhas Turmas -->
                <div class="col-md-6 mb-4">
                    <div class="school-card h-100">
                        <div class="school-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-chalkboard"></i>
                                Minhas Turmas
                            </div>
                            <a href="{{ route('teacher.classes.index') }}" class="btn btn-sm btn-primary-school">
                                Ver Todas
                            </a>
                        </div>
                        <div class="school-card-body">
                            @if ($myClasses->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach ($myClasses as $class)
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $class->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $class->grade_level_name }} |
                                                        {{ $class->students_count }} alunos
                                                    </small>
                                                </div>
                                                <div class="btn-group">
                                                    <a href="{{ route('teacher.attendance.today', $class->id) }}"
                                                        class="btn btn-sm btn-success" title="Presenças">
                                                        <i class="fas fa-clipboard-check"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.gradebook', $class->id) }}"
                                                        class="btn btn-sm btn-warning" title="Notas">
                                                        <i class="fas fa-tasks"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.classes.detail', $class->id) }}"
                                                        class="btn btn-sm btn-primary" title="Detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted text-center py-3">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i><br>
                                    Nenhuma turma atribuída
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Aniversariantes da Semana -->
                <div class="col-md-6 mb-4">
                    <div class="school-card h-100">
                        <div class="school-card-header">
                            <i class="fas fa-birthday-cake"></i>
                            Aniversariantes da Semana
                        </div>
                        <div class="school-card-body">
                             @forelse($birthdaysThisWeek as $birthday)
                                 <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded" style="background:#f8f9fa;">
                                     <div class="user-avatar-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                                         {{ substr($birthday->first_name, 0, 1) }}{{ substr($birthday->last_name, 0, 1) }}
                                     </div>
                                     <div class="flex-grow-1">
                                         <p class="mb-0 fw-semibold">{{ $birthday->first_name }} {{ $birthday->last_name }}</p>
                                         <small class="text-muted">
                                             <i class="far fa-calendar me-1"></i> {{ $birthday->birthdate->format('d/m') }}
                                         </small>
                                         @if($birthday->currentClass)
                                             <div class="text-muted small">
                                                 <i class="fas fa-chalkboard me-1"></i>{{ $birthday->currentClass->name }}
                                             </div>
                                         @endif
                                     </div>
                                     <a href="{{ route('students.show', $birthday->id) }}" class="btn btn-sm btn-warning">
                                         <i class="fas fa-user me-1"></i>Ver Perfil
                                     </a>
                                 </div>
                             @empty
                                <p class="text-muted text-center py-3">
                                    <i class="fas fa-birthday-cake fa-2x mb-2"></i><br>
                                    Nenhum aniversariante esta semana
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="school-card">
                <div class="school-card-header">
                    <i class="fas fa-bolt"></i>
                    Ações Rápidas
                </div>
                <div class="school-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('teacher.classes.index') }}" class="btn btn-primary-school w-100 h-100 py-3">
                                <i class="fas fa-chalkboard fa-2x mb-2"></i><br>
                                Minhas Turmas
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.attendance.today', $myClasses->first()->id ?? '#') }}"
                                class="btn btn-success-school w-100 h-100 py-3 {{ !$myClasses->count() ? 'disabled' : '' }}">
                                <i class="fas fa-clipboard-check fa-2x mb-2"></i><br>
                                Registrar Presenças
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.gradebook', $myClasses->first()->id ?? '#') }}"
                                class="btn btn-warning-school w-100 h-100 py-3 {{ !$myClasses->count() ? 'disabled' : '' }}">
                                <i class="fas fa-tasks fa-2x mb-2"></i><br>
                                Lançar Notas
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.communications.index') }}" class="btn btn-info-school w-100 h-100 py-3">
                                <i class="fas fa-bullhorn fa-2x mb-2"></i><br>
                                Comunicados
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
