@extends('layouts.app')

@section('title', 'Caderno de Notas')
@section('page-title', 'Caderno de Notas')
@section('page-title-icon', 'fas fa-tasks')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}">Minhas Turmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.detail', $class->id) }}">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active">Notas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Cabeçalho -->
        <div class="school-card mb-4">
            <div class="school-card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3>Caderno de Notas</h3>
                        <p class="text-muted mb-0">
                            Turma: <strong>{{ $class->name }}</strong> | 
                            {{ $class->grade_level_name ?? 'N/A' }} | 
                            {{ $class->students->count() }} alunos
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select name="subject_id" class="form-select" onchange="updateSubject(this.value)">
                                    <option value="">— Disciplinas que lecciono —</option>
                                    @forelse($teacherSubjects as $subject)
                                        <option value="{{ $subject->id }}" {{ (string)$selectedSubject === (string)$subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Não lecciona nenhuma disciplina nesta turma.</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="term" class="form-select" onchange="updateTerm(this.value)">
                                    <option value="1" {{ $selectedTerm == 1 ? 'selected' : '' }}>1º Trimestre</option>
                                    <option value="2" {{ $selectedTerm == 2 ? 'selected' : '' }}>2º Trimestre</option>
                                    <option value="3" {{ $selectedTerm == 3 ? 'selected' : '' }}>3º Trimestre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar de Conexão com Pautas Oficiais -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 bg-white border rounded shadow-sm">
            <div class="d-flex items-center gap-2">
                <span class="badge bg-success text-white font-bold px-3 py-2">
                    <i class="fas fa-school me-1"></i> Pautas & Avaliação MINEDH
                </span>
                <span class="text-xs text-muted font-medium">
                    Consulte as pautas oficiais consolidadas desta turma:
                </span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('pautas.trimestral', ['class' => $class->id, 'term' => $selectedTerm]) }}" class="btn btn-sm btn-outline-success font-bold">
                    <i class="fas fa-file-invoice me-1"></i> Pauta Trimestral
                </a>
                <a href="{{ route('pautas.anual', ['class' => $class->id]) }}" class="btn btn-sm btn-outline-primary font-bold">
                    <i class="fas fa-chart-line me-1"></i> Pauta Anual
                </a>
                <a href="{{ route('pautas.final', ['class' => $class->id]) }}" class="btn btn-sm btn-outline-dark font-bold">
                    <i class="fas fa-award me-1"></i> Pauta Final
                </a>
                <a href="{{ route('grades.batch-create', ['class_id' => $class->id]) }}" class="btn btn-sm btn-success font-bold">
                    <i class="fas fa-table me-1"></i> Lançamento em Lote
                </a>
            </div>
        </div>

        @if($selectedSubject)
            <!-- Formulário de Notas -->
            <div class="school-card">
                <div class="school-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-book"></i>
                        Lançamento de Notas - {{ $teacherSubjects->find($selectedSubject)?->name ?? 'Disciplina' }}
                        <span class="badge bg-info text-white ms-2">{{ $selectedTerm }}º Trimestre</span>
                    </div>
                    <button type="button" class="btn btn-school btn-primary-school btn-sm" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                        <i class="fas fa-plus"></i> Nova Nota
                    </button>
                </div>
                <div class="school-card-body">
                    @if($class->students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-grades">
                                <thead>
                                    <tr>
                                        <th width="22%">Aluno</th>
                                        <th width="9%">ACS 1</th>
                                        <th width="9%">ACS 2</th>
                                        <th width="9%">ACS 3</th>
                                        <th width="9%">ACP</th>
                                        <th width="9%">ACF / Exame</th>
                                        <th width="9%">MT</th>
                                        <th width="9%">Estado</th>
                                        <th width="14%">Acções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($class->students as $student)
                                        @php
                                            $studentGrades = $grades[$student->id] ?? [];
                                            $get = function($type) use ($studentGrades) {
                                                return isset($studentGrades[$type]) && $studentGrades[$type]->isNotEmpty()
                                                    ? round($studentGrades[$type]->first()->grade, 1)
                                                    : null;
                                            };
                                            $acs1 = $get('ACS1');
                                            $acs2 = $get('ACS2');
                                            $acs3 = $get('ACS3');
                                            $acp  = $get('ACP');
                                            $acf  = $get('ACF');
                                            
                                            // Média Trimestral MINEDH: média das ACS (até 3) com peso 40% + ACP 20% + ACF 40%
                                            $mt = null;
                                            $acsValues = array_filter([$acs1, $acs2, $acs3], fn($v) => $v !== null);
                                            $acsAvg = count($acsValues) > 0 ? array_sum($acsValues) / count($acsValues) : null;
                                            if ($acsAvg !== null && $acp !== null && $acf !== null) {
                                                $mt = round($acsAvg * 0.4 + $acp * 0.2 + $acf * 0.4, 1);
                                            } elseif ($acsAvg !== null && $acf !== null) {
                                                // Se só temos ACS e ACF
                                                $mt = round($acsAvg * 0.5 + $acf * 0.5, 1);
                                            } elseif ($acsAvg !== null) {
                                                $mt = round($acsAvg, 1);
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar-sm me-3">
                                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $student->student_number ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="grade-badge {{ $acs1 ? 'has-grade' : 'no-grade' }}" 
                                                      onclick="editGrade({{ $student->id }}, 'ACS1', {{ $acs1 ?? 'null' }})">
                                                    {{ $acs1 ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="grade-badge {{ $acs2 ? 'has-grade' : 'no-grade' }}"
                                                      onclick="editGrade({{ $student->id }}, 'ACS2', {{ $acs2 ?? 'null' }})">
                                                    {{ $acs2 ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="grade-badge {{ $acs3 ? 'has-grade' : 'no-grade' }}"
                                                      onclick="editGrade({{ $student->id }}, 'ACS3', {{ $acs3 ?? 'null' }})">
                                                    {{ $acs3 ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="grade-badge {{ $acp ? 'has-grade' : 'no-grade' }}"
                                                      onclick="editGrade({{ $student->id }}, 'ACP', {{ $acp ?? 'null' }})">
                                                    {{ $acp ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="grade-badge {{ $acf ? 'has-grade' : 'no-grade' }}"
                                                      onclick="editGrade({{ $student->id }}, 'ACF', {{ $acf ?? 'null' }})">
                                                    {{ $acf ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($mt)
                                                    <span class="average-badge grade-{{ $mt >= 10 ? 'success' : ($mt >= 7 ? 'warning' : 'danger') }}">
                                                        <strong>{{ $mt }}</strong>
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mt === null)
                                                    <span class="badge bg-secondary">Pendente</span>
                                                @elseif($mt >= 10)
                                                    <span class="badge bg-success">Aprovado</span>
                                                @elseif($mt >= 7)
                                                    <span class="badge bg-warning text-dark">Recuperação</span>
                                                @else
                                                    <span class="badge bg-danger">Reprovado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editGrade({{ $student->id }}, 'ACS1', {{ $acs1 ?? 'null' }})" title="Lançar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                            Nenhum aluno matriculado nesta turma
                        </p>
                    @endif
                </div>
            </div>

            <!-- Resumo das Notas -->
            <div class="school-card mt-4">
                <div class="school-card-header">
                    <i class="fas fa-chart-bar"></i>
                    Estatísticas das Notas
                </div>
                <div class="school-card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="grade-stat">
                                <div class="stat-number text-primary" id="total-grades">0</div>
                                <div class="stat-label">Notas Lançadas</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="grade-stat">
                                <div class="stat-number text-success" id="approved-count">0</div>
                                <div class="stat-label">Aprovados</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="grade-stat">
                                <div class="stat-number text-warning" id="recovery-count">0</div>
                                <div class="stat-label">Recuperação</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="grade-stat">
                                <div class="stat-number text-danger" id="failed-count">0</div>
                                <div class="stat-label">Reprovados</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="school-card">
                <div class="school-card-body text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Selecione uma Disciplina</h4>
                    <p class="text-muted">Escolha uma disciplina para visualizar e lançar notas.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal para Adicionar/Editar Nota -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalTitle">Lançar Nova Nota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('teacher.grades.store') }}" method="POST" id="grade-form">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="subject_id" value="{{ $selectedSubject }}">
                <input type="hidden" name="term" value="{{ $selectedTerm }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Aluno</label>
                        <select name="student_id" class="form-select" required id="student-select">
                            <option value="">Selecione o aluno</option>
                            @foreach($class->students as $student)
                                <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Avaliação (MINEDH)</label>
                        <select name="assessment_type" class="form-select" required id="assessment-type">
                            <option value="ACS1">ACS 1 — Avaliação Contínua 1</option>
                            <option value="ACS2">ACS 2 — Avaliação Contínua 2</option>
                            <option value="ACS3">ACS 3 — Avaliação Contínua 3</option>
                            <option value="ACP">ACP — Avaliação de Controlo Parcial</option>
                            <option value="ACF">ACF / Exame — Avaliação Contínua Final</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nota (0-20)</label>
                        <input type="number" name="grade" class="form-control" min="0" max="20" step="0.1" required id="grade-input">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comentários (Opcional)</label>
                        <textarea name="comments" class="form-control" rows="3" id="grade-comments"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-school btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-school btn-primary-school">Salvar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.grade-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 40px;
    text-align: center;
}

.grade-badge.has-grade {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #1976d2;
}

.grade-badge.no-grade {
    background: #f5f5f5;
    color: #757575;
    border: 1px dashed #bdbdbd;
}

.grade-badge:hover {
    transform: scale(1.05);
}

.average-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 14px;
}

.grade-success {
    background: #e8f5e8;
    color: #2e7d32;
    border: 1px solid #2e7d32;
}

.grade-danger {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #c62828;
}

.grade-stat {
    padding: 15px;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
}

.user-avatar-sm {
    width: 35px;
    height: 35px;
    background: #e74c3c;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: 600;
}
</style>

<script>
function updateSubject(subjectId) {
    const url = new URL(window.location.href);
    url.searchParams.set('subject_id', subjectId);
    window.location.href = url.toString();
}

function updateTerm(term) {
    const url = new URL(window.location.href);
    url.searchParams.set('term', term);
    window.location.href = url.toString();
}

function editGrade(studentId, assessmentType, currentGrade) {
    document.getElementById('student-select').value = studentId;
    document.getElementById('assessment-type').value = assessmentType;
    
    // CORREÇÃO: Lidar com valores null corretamente
    const gradeValue = (currentGrade && currentGrade !== 'null') ? currentGrade : '';
    document.getElementById('grade-input').value = gradeValue;
    
    document.getElementById('grade-comments').value = '';
    document.getElementById('gradeModalTitle').textContent = gradeValue ? 'Editar Nota' : 'Lançar Nova Nota';
    
    const modal = new bootstrap.Modal(document.getElementById('addGradeModal'));
    modal.show();
}

// Calcular estatísticas
document.addEventListener('DOMContentLoaded', function() {
    function calculateStatistics() {
        let totalGrades = 0;
        let approved = 0;
        let recovery = 0;
        let failed = 0;

        document.querySelectorAll('.average-badge').forEach(badge => {
            const grade = parseFloat(badge.textContent);
            if (!isNaN(grade)) {
                totalGrades++;
                if (grade >= 10) approved++;
                else if (grade >= 8) recovery++;
                else failed++;
            }
        });

        document.getElementById('total-grades').textContent = totalGrades;
        document.getElementById('approved-count').textContent = approved;
        document.getElementById('recovery-count').textContent = recovery;
        document.getElementById('failed-count').textContent = failed;
    }

    calculateStatistics();
});
</script>
@endsection