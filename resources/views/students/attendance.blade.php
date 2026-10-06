@extends('layouts.app')

@section('title', 'Registro de Presenças')
@section('page-title', 'Registro de Presenças')
@section('page-title-icon', 'fas fa-calendar-check')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Alunos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}">{{ $student->first_name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Presenças</li>
@endsection

@section('title', 'Presenças - ' . $student->first_name)
@section('content')
<div class="row">
    <div class="col-12">
        <!-- Informações do Aluno -->
        <div class="school-card mb-4">
            <div class="school-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $student->first_name }} {{ $student->last_name }}</h4>
                        <p class="text-muted mb-0">
                            <strong>Número:</strong> {{ $student->student_number }} |
                            <strong>Turma:</strong> {{ $student->enrollments->where('status', 'active')->first()->class->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('students.show', $student) }}" class="btn btn-secondary-school">
                            <i class="fas fa-arrow-left"></i> Voltar ao Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas de Presença -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon students">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $attendanceStats['attendance_rate'] }}%</div>
                        <div class="stat-label">Taxa de Presença</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon teachers">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $attendanceStats['present'] }}</div>
                        <div class="stat-label">Presente</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon payments">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $attendanceStats['absent'] }}</div>
                        <div class="stat-label">Ausente</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon events">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $attendanceStats['late'] }}</div>
                        <div class="stat-label">Atrasos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="school-card mb-4">
            <div class="school-card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Mês</label>
                        <select name="month" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos os meses</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ano</label>
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            @for($i = current_school_year() - 2; $i <= current_school_year(); $i++)
                                <option value="{{ $i }}" {{ request('year', current_school_year()) == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Todos</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Presente</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Ausente</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Atrasado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('students.attendance', $student) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabela de Presenças -->
        <div class="school-table-container">
            <div class="school-table-header">
                <h3 class="school-table-title">
                    <i class="fas fa-list"></i>
                    Histórico de Presenças
                </h3>
            </div>

            <div class="table-responsive">
                <table class="table table-school">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Dia da Semana</th>
                            <th>Turma</th>
                            <th>Status</th>
                            <th>Hora de Chegada</th>
                            <th>Registrado Por</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->attendances as $attendance)
                            <tr>
                                <td>
                                    <strong>{{ $attendance->attendance_date->format('d/m/Y') }}</strong>
                                </td>
                                <td>
                                    {{ $attendance->attendance_date->translatedFormat('l') }}
                                </td>
                                <td>
                                    {{ $attendance->class->name ?? 'N/A' }}
                                </td>
                                <td>
                                    @switch($attendance->status)
                                        @case('present')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Presente
                                            </span>
                                            @break
                                        @case('absent')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> Ausente
                                            </span>
                                            @break
                                        @case('late')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> Atrasado
                                            </span>
                                            @break
                                        @case('excused')
                                            <span class="badge bg-info">
                                                <i class="fas fa-umbrella"></i> Justificado
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($attendance->arrival_time)
                                        {{ $attendance->arrival_time->format('H:i') }}
                                        @if(str_contains($attendance->notes ?? '', 'Portaria Digital'))
                                            <span class="badge bg-success-subtle text-success rounded-pill ms-1" style="font-size:0.65rem;">
                                                <i class="fas fa-shield-alt me-1"></i>Portaria
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->markedBy)
                                        {{ $attendance->markedBy->name }}
                                    @else
                                        <span class="text-muted">Sistema</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $attendance->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum registro de presença encontrado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($student->attendances->count() > 10)
                <div class="school-card-body border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ min($student->attendances->count(), 10) }} de {{ $student->attendances->count() }} registros
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="loadMoreAttendances()">
                                <i class="fas fa-plus"></i> Carregar Mais
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Gráfico de Presenças (Mensal) -->
        <div class="school-card mt-4">
            <div class="school-card-header">
                <h3 class="school-card-title">
                    <i class="fas fa-chart-bar"></i>
                    Estatísticas Mensais
                </h3>
            </div>
            <div class="school-card-body">
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="attendanceChart" width="400" height="200"></canvas>
                    </div>
                    <div class="col-md-4">
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Dias Letivos no Mês
                                <span class="badge bg-primary rounded-pill">{{ $attendanceStats['total_classes'] }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Faltas Justificadas
                                <span class="badge bg-info rounded-pill">0</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Taxa de Presença Mínima
                                <span class="badge bg-{{ $attendanceStats['attendance_rate'] >= 75 ? 'success' : 'danger' }} rounded-pill">
                                    75%
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Situação
                                <span class="badge bg-{{ $attendanceStats['attendance_rate'] >= 75 ? 'success' : 'warning' }} rounded-pill">
                                    {{ $attendanceStats['attendance_rate'] >= 75 ? 'Regular' : 'Atenção' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const presentCount = {{ $attendanceStats['present'] }};
    const absentCount = {{ $attendanceStats['absent'] }};
    const lateCount = {{ $attendanceStats['late'] }};
    const totalCount = presentCount + absentCount + lateCount;

    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: totalCount > 0 ? ['Presente', 'Ausente', 'Atrasado'] : ['Sem Lançamentos'],
            datasets: [{
                data: totalCount > 0 ? [presentCount, absentCount, lateCount] : [1],
                backgroundColor: totalCount > 0 ? ['#28a745', '#dc3545', '#ffc107'] : ['#cbd5e1'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: totalCount > 0 ? 'Distribuição de Presenças' : 'Aguardando Lançamentos de Assiduidade'
                }
            }
        }
        }
    });

    // Função para carregar mais presenças
    window.loadMoreAttendances = function() {
        // Implementar carregamento AJAX para mais registros
        showToast('Funcionalidade de carregar mais registros será implementada em breve.', 'info');
    };
});
</script>
@endpush