@extends('layouts.app')

@section('title', 'Registrar Presenças')
@section('page-title', 'Registrar Presenças')
@section('page-title-icon', 'fas fa-clipboard-check')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}">Minhas Turmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.detail', $class->id) }}">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Presenças</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Cabeçalho -->
        <div class="school-card mb-4">
            <div class="school-card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3>Registrar Presenças</h3>
                        <p class="text-muted mb-0">
                            Turma: <strong>{{ $class->name }}</strong> | 
                            {{ $class->grade_level_name }} | 
                            {{ $class->students_count }} alunos
                        </p>
                    </div>
                    <div class="col-md-4">
                        <form method="GET" class="d-flex gap-2">
                            <input type="date" name="date" class="form-control" value="{{ $attendanceDate }}" required>
                            <button type="submit" class="btn btn-primary-school">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Presenças -->
        <div class="school-card">
            <div class="school-card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-alt"></i>
                    Presenças para {{ \Carbon\Carbon::parse($attendanceDate)->format('d/m/Y') }}
                </div>
                <div class="attendance-stats">
                    <span class="badge bg-success me-2" id="present-count">0 Presentes</span>
                    <span class="badge bg-danger me-2" id="absent-count">0 Ausentes</span>
                    <span class="badge bg-warning" id="late-count">0 Atrasados</span>
                </div>
            </div>
            <div class="school-card-body">
                <form action="{{ route('teacher.attendance.store', $class->id) }}" method="POST" id="attendance-form">
                    @csrf
                    <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

                    @if($class->students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-attendance">
                                <thead>
                                    <tr>
                                        <th width="50%">Aluno</th>
                                        <th width="30%">Situação</th>
                                        <th width="20%">Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($class->students as $student)
                                        @php
                                            $existing = $existingAttendance[$student->id] ?? null;
                                        @endphp
                                        <tr class="attendance-row">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar-sm me-3">
                                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $student->student_number }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-outline-success attendance-btn {{ ($existing && $existing->status == 'present') ? 'active' : '' }}">
                                                        <input type="radio" name="attendances[{{ $student->id }}][status]" 
                                                               value="present" 
                                                               {{ ($existing && $existing->status == 'present') ? 'checked' : 'checked' }}>
                                                        <i class="fas fa-check"></i> Presente
                                                    </label>
                                                    <label class="btn btn-outline-danger attendance-btn {{ ($existing && $existing->status == 'absent') ? 'active' : '' }}">
                                                        <input type="radio" name="attendances[{{ $student->id }}][status]" 
                                                               value="absent"
                                                               {{ ($existing && $existing->status == 'absent') ? 'checked' : '' }}>
                                                        <i class="fas fa-times"></i> Ausente
                                                    </label>
                                                    <label class="btn btn-outline-warning attendance-btn {{ ($existing && $existing->status == 'late') ? 'active' : '' }}">
                                                        <input type="radio" name="attendances[{{ $student->id }}][status]" 
                                                               value="late"
                                                               {{ ($existing && $existing->status == 'late') ? 'checked' : '' }}>
                                                        <i class="fas fa-clock"></i> Atrasado
                                                    </label>
                                                </div>
                                                <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="attendances[{{ $student->id }}][notes]" 
                                                       class="form-control form-control-sm" 
                                                       placeholder="Observações..."
                                                       value="{{ $existing->notes ?? '' }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('teacher.classes.detail', $class->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <div>
                                <button type="button" class="btn btn-warning me-2" id="mark-all-present">
                                    <i class="fas fa-check-double"></i> Todos Presentes
                                </button>
                                <button type="submit" class="btn btn-primary-school">
                                    <i class="fas fa-save"></i> Salvar Presenças
                                </button>
                            </div>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                            Nenhum aluno matriculado nesta turma
                        </p>
                    @endif
                </form>
            </div>
        </div>

        <!-- Resumo do Dia -->
        <div class="school-card mt-4">
            <div class="school-card-header">
                <i class="fas fa-chart-pie"></i>
                Resumo do Dia
            </div>
            <div class="school-card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="stat-present">
                            <div class="stat-number text-success" id="summary-present">0</div>
                            <div class="stat-label">Presentes</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-absent">
                            <div class="stat-number text-danger" id="summary-absent">0</div>
                            <div class="stat-label">Ausentes</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-late">
                            <div class="stat-number text-warning" id="summary-late">0</div>
                            <div class="stat-label">Atrasados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-attendance tbody tr {
    transition: background-color 0.2s ease;
}

.table-attendance tbody tr:hover {
    background-color: rgba(var(--primary-rgb), 0.05);
}

.attendance-btn {
    border-radius: 4px !important;
    margin: 2px;
    font-size: 12px;
    padding: 6px 10px;
}

.attendance-btn.active {
    color: white !important;
}

.btn-outline-success.active {
    background-color: var(--success);
    border-color: var(--success);
}

.btn-outline-danger.active {
    background-color: var(--danger);
    border-color: var(--danger);
}

.btn-outline-warning.active {
    background-color: var(--warning);
    border-color: var(--warning);
    color: white !important;
}

.user-avatar-sm {
    width: 35px;
    height: 35px;
    background: var(--accent);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: 600;
}

.stat-present, .stat-absent, .stat-late {
    padding: 20px;
}

.stat-number {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-muted);
    text-transform: uppercase;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contadores de status
    function updateCounters() {
        let present = 0, absent = 0, late = 0;
        
        document.querySelectorAll('.attendance-row').forEach(row => {
            const selected = row.querySelector('input[type="radio"]:checked');
            if (selected) {
                switch(selected.value) {
                    case 'present': present++; break;
                    case 'absent': absent++; break;
                    case 'late': late++; break;
                }
            }
        });

        document.getElementById('present-count').textContent = present + ' Presentes';
        document.getElementById('absent-count').textContent = absent + ' Ausentes';
        document.getElementById('late-count').textContent = late + ' Atrasados';
        
        document.getElementById('summary-present').textContent = present;
        document.getElementById('summary-absent').textContent = absent;
        document.getElementById('summary-late').textContent = late;
    }

    // Marcar todos como presentes
    document.getElementById('mark-all-present').addEventListener('click', function() {
        document.querySelectorAll('.attendance-row').forEach(row => {
            const presentBtn = row.querySelector('input[value="present"]');
            if (presentBtn) {
                presentBtn.checked = true;
                presentBtn.closest('label').classList.add('active');
                // Remover active dos outros botões
                row.querySelectorAll('.attendance-btn').forEach(btn => {
                    if (btn !== presentBtn.closest('label')) {
                        btn.classList.remove('active');
                    }
                });
            }
        });
        updateCounters();
    });

    // Atualizar contadores quando mudar status
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Atualizar classes active
            const parentLabel = this.closest('label');
            parentLabel.closest('.btn-group').querySelectorAll('.attendance-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            parentLabel.classList.add('active');
            
            updateCounters();
        });
    });

    // Contador inicial
    updateCounters();

    // Prevenir envio duplo do formulário
    document.getElementById('attendance-form').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    });
});
</script>
@endsection