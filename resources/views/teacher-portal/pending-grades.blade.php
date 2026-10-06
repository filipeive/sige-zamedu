@extends('layouts.app')

@section('title', 'Avaliações Pendentes')
@section('page-title', 'Avaliações Pendentes')
@section('page-title-icon', 'fas fa-clock')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Avaliações Pendentes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="school-card h-100">
                    <div class="school-card-body text-center">
                        <div class="text-warning mb-2">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h3 class="text-warning">{{ $upcomingAssessments->count() + $overdueAssessments->count() }}</h3>
                        <p class="mb-1">Avaliações Pendentes</p>
                        <small class="text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $overdueAssessments->count() }} vencidas
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="school-card h-100">
                    <div class="school-card-body text-center">
                        <div class="text-primary mb-2">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                        <h3 class="text-primary">{{ $teacher->classes->sum('students_count') }}</h3>
                        <p class="mb-1">Alunos para Avaliar</p>
                        <small class="text-muted">
                            <i class="fas fa-users"></i>
                            {{ $teacher->classes->count() }} turmas
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="school-card h-100">
                    <div class="school-card-body text-center">
                        <div class="text-info mb-2">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                        <h3 class="text-info">{{ $upcomingAssessments->count() }}</h3>
                        <p class="mb-1">Próximos Prazos</p>
                        <small class="text-info">
                            <i class="fas fa-calendar"></i>
                            Próximos 7 dias
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avaliações Vencidas -->
        @if($overdueAssessments->count() > 0)
        <div class="school-card mb-4">
            <div class="school-card-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle"></i>
                Avaliações com Prazo Vencido
                <span class="badge bg-light text-danger ms-2">{{ $overdueAssessments->count() }}</span>
            </div>
            <div class="school-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="25%">Avaliação</th>
                                <th width="20%">Turma/Disciplina</th>
                                <th width="15%">Prazo</th>
                                <th width="15%">Progresso</th>
                                <th width="15%">Status</th>
                                <th width="10%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdueAssessments as $assessment)
                            <tr>
                                <td>
                                    <strong>{{ $assessment->title }}</strong>
                                    <br>
                                    <small class="text-muted text-capitalize">{{ $assessment->type ?? 'Avaliação' }}</small>
                                    @if($assessment->description)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($assessment->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $assessment->class->name ?? 'Turma não encontrada' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $assessment->subject->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="text-danger">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $assessment->due_date->diffForHumans() }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $assessment->due_date->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @php
                                        // Simulação de progresso - você precisará implementar essa lógica
                                        $totalStudents = $assessment->class->students_count ?? 0;
                                        $gradedStudents = 0; // Implementar lógica real
                                        $percentage = $totalStudents > 0 ? round(($gradedStudents / $totalStudents) * 100) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small>{{ $percentage }}%</small>
                                    </div>
                                    <small class="text-muted">
                                        {{ $gradedStudents }}/{{ $totalStudents }} alunos
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-danger">Vencida</span>
                                </td>
                                <td>
                                    @if($assessment->class)
                                    <a href="{{ route('teacher.gradebook', $assessment->class_id) }}?subject_id={{ $assessment->subject_id }}"
                                       class="btn btn-sm btn-danger"
                                       title="Lançar notas">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Turma não disponível">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Próximas Avaliações -->
        @if($upcomingAssessments->count() > 0)
        <div class="school-card">
            <div class="school-card-header bg-warning text-dark">
                <i class="fas fa-calendar-check"></i>
                Próximas Avaliações (7 dias)
                <span class="badge bg-light text-warning ms-2">{{ $upcomingAssessments->count() }}</span>
            </div>
            <div class="school-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="25%">Avaliação</th>
                                <th width="20%">Turma/Disciplina</th>
                                <th width="15%">Prazo</th>
                                <th width="15%">Progresso</th>
                                <th width="15%">Status</th>
                                <th width="10%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingAssessments as $assessment)
                            <tr>
                                <td>
                                    <strong>{{ $assessment->title }}</strong>
                                    <br>
                                    <small class="text-muted text-capitalize">{{ $assessment->type ?? 'Avaliação' }}</small>
                                    @if($assessment->description)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($assessment->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $assessment->class->name ?? 'Turma não encontrada' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $assessment->subject->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="text-warning">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $assessment->due_date->diffForHumans() }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $assessment->due_date->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @php
                                        // Simulação de progresso - você precisará implementar essa lógica
                                        $totalStudents = $assessment->class->students_count ?? 0;
                                        $gradedStudents = 0; // Implementar lógica real
                                        $percentage = $totalStudents > 0 ? round(($gradedStudents / $totalStudents) * 100) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small>{{ $percentage }}%</small>
                                    </div>
                                    <small class="text-muted">
                                        {{ $gradedStudents }}/{{ $totalStudents }} alunos
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $daysUntilDue = $assessment->due_date->diffInDays(now());
                                        $statusColor = $daysUntilDue <= 1 ? 'danger' : ($daysUntilDue <= 3 ? 'warning' : 'info');
                                        $statusText = $daysUntilDue <= 1 ? 'Urgente' : ($daysUntilDue <= 3 ? 'Próxima' : 'Em dia');
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    @if($assessment->class)
                                    <a href="{{ route('teacher.gradebook', $assessment->class_id) }}?subject_id={{ $assessment->subject_id }}"
                                       class="btn btn-sm btn-warning"
                                       title="Lançar notas">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Turma não disponível">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($upcomingAssessments->count() == 0 && $overdueAssessments->count() == 0)
        <div class="school-card">
            <div class="school-card-body text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h4 class="text-success">Tudo em Dia!</h4>
                <p class="text-muted">Não há avaliações pendentes no momento.</p>
                <a href="{{ route('teacher.classes.index') }}" class="btn btn-primary-school">
                    <i class="fas fa-chalkboard me-2"></i> Ver Minhas Turmas
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.school-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
    border: 1px solid #dee2e6;
}

.school-card-header {
    padding: 1rem 1.5rem;
    border-radius: 8px 8px 0 0;
    font-weight: 600;
}

.school-card-body {
    padding: 1.5rem;
}

.btn-primary-school {
    background: linear-gradient(135deg, #3498db, #2980b9);
    border: none;
    color: white;
}

.btn-primary-school:hover {
    background: linear-gradient(135deg, #2980b9, #3498db);
    color: white;
}

.table-school th {
    border-top: none;
    font-weight: 600;
    color: #2c3e50;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
}
</style>
@endsection