@extends('layouts.app')

@section('title', 'Pauta Trimestral - ' . $class->name)

@section('content')
<div class="container-fluid py-4">
@php
    $launchGradesUrl = auth()->user()->hasRole('teacher')
        ? route('teacher.gradebook', $class->id)
        : (Route::has('grades.batch-create') 
            ? route('grades.batch-create', ['class_id' => $class->id, 'subject_id' => $selectedSubjectId ?? null, 'term' => $term ?? 1, 'year' => $year ?? 2026]) 
            : route('grades.dashboard'));
@endphp

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success fw-bold">
                    {{ $class->education_level_name }}
                </span>
                <span class="badge bg-secondary-subtle text-secondary fw-bold">
                    {{ $class->grade_level_name }}
                </span>
                <span class="badge bg-primary-subtle text-primary fw-bold">
                    Ano Lectivo {{ $year }}
                </span>
                @if($isExamClass)
                    <span class="badge bg-danger text-white fw-bold">
                        <i class="fas fa-exclamation-circle me-1"></i> Classe de Exame (9ª/10ª/12ª)
                    </span>
                @endif
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-0">Pauta Trimestral: {{ $class->name }}</h1>
            <p class="text-muted small mb-0">Visão consolidada de avaliações contínuas (ACS), parciais (ACP) e média trimestral (MT) conforme diretivas MINEDH.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-dark btn-sm font-weight-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#gradeCalculationModal">
                <i class="fas fa-calculator me-1 text-success"></i> Regras de Cálculo
            </button>

            <a href="{{ $launchGradesUrl }}" class="btn btn-success btn-sm font-weight-bold shadow-sm px-3">
                <i class="fas fa-edit me-1"></i> Lançar / Registar Notas
            </a>

            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('pautas.trimestral', $class->id) }}" class="btn btn-primary active fw-bold">
                    <i class="fas fa-list-alt me-1"></i> Trimestral
                </a>
                <a href="{{ route('pautas.anual', $class->id) }}" class="btn btn-outline-primary fw-bold">
                    <i class="fas fa-calendar-alt me-1"></i> Anual
                </a>
                <a href="{{ route('pautas.final', $class->id) }}" class="btn btn-outline-warning text-dark fw-bold">
                    <i class="fas fa-graduation-cap me-1"></i> Final
                </a>
                @if($isExamClass)
                    <a href="{{ route('pautas.exame', $class->id) }}" class="btn btn-outline-danger fw-bold">
                        <i class="fas fa-file-signature me-1"></i> Exames & Júri
                    </a>
                @endif
            </div>

            <a href="{{ route('pautas.pdf', ['class' => $class->id, 'type' => 'trimestral', 'term' => $term, 'year' => $year]) }}" class="btn btn-danger btn-sm shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('classes.show', $class->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Area de Filtros Organizada -->
    <div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-filter text-success me-2"></i>
                <span class="text-xs font-weight-bold text-uppercase text-secondary tracking-wider">Filtros de Seleção &amp; Consulta</span>
            </div>
            <form method="GET" action="{{ route('pautas.trimestral', $class->id) }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-xs font-weight-bold text-muted mb-1">
                            <i class="fas fa-users text-success me-1"></i> Turma
                        </label>
                        <select name="class_id" class="form-select form-select-sm border-secondary-subtle" onchange="this.form.submit()">
                            @foreach($availableClasses as $c)
                                <option value="{{ $c->id }}" @selected($c->id === $class->id)>
                                    {{ $c->name }} ({{ $c->grade_level_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-xs font-weight-bold text-muted mb-1">
                            <i class="fas fa-book text-success me-1"></i> Disciplina
                        </label>
                        <select name="subject_id" class="form-select form-select-sm border-secondary-subtle" onchange="this.form.submit()">
                            <option value="">{{ $isTeacher ? 'Todas (que lecciono)' : 'Todas as Disciplinas' }}</option>
                            @foreach($availableSubjects as $s)
                                <option value="{{ $s->id }}" @selected($selectedSubjectId === $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-xs font-weight-bold text-muted mb-1">
                            <i class="fas fa-calendar text-success me-1"></i> Ano Lectivo
                        </label>
                        <select name="year" class="form-select form-select-sm border-secondary-subtle" onchange="this.form.submit()">
                            <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026 (Ano Letivo Atual)</option>
                            <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025 (Histórico)</option>
                            <option value="2024" {{ $year == 2024 ? 'selected' : '' }}>2024 (Histórico)</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-xs font-weight-bold text-muted mb-1">
                            <i class="fas fa-clock text-success me-1"></i> Trimestre
                        </label>
                        <select name="term" class="form-select form-select-sm border-secondary-subtle" onchange="this.form.submit()">
                            <option value="1" {{ $term == 1 ? 'selected' : '' }}>1º Trimestre</option>
                            <option value="2" {{ $term == 2 ? 'selected' : '' }}>2º Trimestre</option>
                            <option value="3" {{ $term == 3 ? 'selected' : '' }}>3º Trimestre</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela Matriz da Pauta -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 text-dark font-weight-bold">
                    <i class="fas fa-table text-success me-2"></i> Pauta do {{ $term }}º Trimestre - Ano Lectivo {{ $year }}
                </h5>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge bg-success-subtle text-success border border-success font-mono" style="font-size: 0.7rem;">
                        <i class="fas fa-square-root-variable me-1"></i> MT = ({{ $calculationRules['macs_weight'] ?? 2 }}&times;MACS + {{ $calculationRules['acp_weight_in_mt'] ?? 1 }}&times;ACP) / {{ ($calculationRules['macs_weight'] ?? 2) + ($calculationRules['acp_weight_in_mt'] ?? 1) }}
                    </span>
                    <span class="text-muted text-3xs font-weight-bold">
                        &bull; MACS: Média Aritmética ACS
                    </span>
                    <span class="text-muted text-3xs font-weight-bold">
                        &bull; Limiar: &ge; {{ $calculationRules['passing_grade'] ?? 9.5 }} v.
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-success btn-xs fw-bold px-2 py-1" data-bs-toggle="modal" data-bs-target="#gradeCalculationModal">
                    <i class="fas fa-sliders-h me-1"></i> Configurar Regras
                </button>
                <span class="badge bg-light text-dark border">Total: {{ count($matrix) }} Alunos</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 0.88rem;">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" class="align-middle text-start" style="min-width: 220px;">Nome do Aluno</th>
                            @foreach($subjects as $subject)
                                <th colspan="6" class="text-center border-bottom-0">{{ $subject->code ?? $subject->name }}</th>
                            @endforeach
                            <th rowspan="2" class="align-middle bg-primary text-white">Média Geral</th>
                            <th rowspan="2" class="align-middle">Situação</th>
                        </tr>
                        <tr>
                            @foreach($subjects as $subject)
                                <th class="small fw-normal bg-secondary">ACS 1</th>
                                <th class="small fw-normal bg-secondary">ACS 2</th>
                                <th class="small fw-normal bg-secondary">ACS 3</th>
                                <th class="small fw-bold bg-dark text-info">MACS</th>
                                <th class="small fw-normal bg-secondary">ACP</th>
                                <th class="small fw-bold bg-dark text-warning">MT</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matrix as $item)
                            <tr>
                                <td class="text-start font-weight-bold text-dark">
                                    {{ $item['student']->first_name }} {{ $item['student']->last_name }}
                                    <div class="text-muted small" style="font-size: 0.75rem;">N.º {{ $item['student']->student_number ?? 'N/A' }}</div>
                                </td>
                                @foreach($subjects as $subject)
                                    @php
                                        $subData = $item['subjects'][$subject->id] ?? [];
                                        $mtVal = is_numeric($subData['mt'] ?? null) ? (float)$subData['mt'] : null;
                                        $passingGradeThreshold = $calculationRules['passing_grade'] ?? 9.5;
                                    @endphp
                                    <td class="text-muted">{{ $subData['acs1'] ?? '-' }}</td>
                                    <td class="text-muted">{{ $subData['acs2'] ?? '-' }}</td>
                                    <td class="text-muted">{{ $subData['acs3'] ?? '-' }}</td>
                                    <td class="fw-bold text-info">{{ $subData['macs'] ?? '-' }}</td>
                                    <td class="text-muted">{{ $subData['acp'] ?? '-' }}</td>
                                    <td class="fw-bold {{ $mtVal !== null ? ($mtVal >= $passingGradeThreshold ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle') : '' }}">
                                        {{ $subData['mt'] ?? '-' }}
                                    </td>
                                @endforeach
                                <td class="fw-bold bg-light text-primary">
                                    {{ $item['overall_average'] > 0 ? number_format($item['overall_average'], 1) : '-' }}
                                </td>
                                <td>
                                    @if($item['final_status'] === 'Aprovado')
                                        <span class="badge bg-success">Positivo</span>
                                    @elseif($item['final_status'] === 'Retido')
                                        <span class="badge bg-danger">Atenção</span>
                                    @else
                                        <span class="badge bg-secondary">Em Curso</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($subjects) * 6 + 3 }}" class="py-5 text-center text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary"></i>
                                    <p class="mb-0">Nenhuma nota registada para esta turma no {{ $term }}º Trimestre.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('pautas.partials.calculation-modal')
@endsection
