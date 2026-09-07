@extends('layouts.app')

@section('title', 'Pauta Anual - ' . $class->name)

@section('content')
<div class="container-fluid py-4">
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
                        <i class="fas fa-exclamation-circle me-1"></i> Classe de Exame Nacional (9ª/10ª/12ª)
                    </span>
                @endif
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-0">Pauta Anual Consolidada: {{ $class->name }}</h1>
            <p class="text-muted small mb-0">Apuramento de médias anuais de frequência (MAF) e notas finais de disciplina (NFD) conforme SNE (Lei 18/2018).</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('pautas.trimestral', $class->id) }}" class="btn btn-outline-primary fw-bold">
                    <i class="fas fa-list-alt me-1"></i> Trimestral
                </a>
                <a href="{{ route('pautas.anual', $class->id) }}" class="btn btn-primary active fw-bold">
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

            <a href="{{ route('pautas.pdf', ['class' => $class->id, 'type' => 'anual', 'year' => $year]) }}" class="btn btn-danger btn-sm shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> Exportar PDF
            </a>
            <a href="{{ route('classes.show', $class->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Area de Filtros Organizada -->
    <div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('pautas.anual', $class->id) }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4 col-sm-6">
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

                    <div class="col-md-4 col-sm-6">
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

                    <div class="col-md-4 col-sm-6">
                        <label class="form-label text-xs font-weight-bold text-muted mb-1">
                            <i class="fas fa-calendar text-success me-1"></i> Ano Lectivo
                        </label>
                        <select name="year" class="form-select form-select-sm border-secondary-subtle" onchange="this.form.submit()">
                            <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026 (Ano Letivo Atual)</option>
                            <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025 (Histórico)</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela Principal Anual -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 0.88rem;">
                <thead class="table-dark text-uppercase tracking-wider">
                    <tr>
                        <th rowspan="2" class="align-middle text-start bg-slate-900" style="min-width: 240px;">Nome do Aluno</th>
                        @foreach($subjects as $subject)
                            <th colspan="{{ $isExamClass ? 6 : 4 }}" class="bg-slate-800 text-info fw-bold">{{ $subject->code ?? $subject->name }}</th>
                        @endforeach
                        <th rowspan="2" class="align-middle bg-primary text-white">Média Geral</th>
                        <th rowspan="2" class="align-middle">Resultado Final</th>
                    </tr>
                    <tr>
                        @foreach($subjects as $subject)
                            <th class="small fw-normal bg-secondary">MT 1</th>
                            <th class="small fw-normal bg-secondary">MT 2</th>
                            <th class="small fw-normal bg-secondary">MT 3</th>
                            <th class="small fw-bold bg-amber-600 text-white">MAF</th>
                            @if($isExamClass)
                                <th class="small fw-bold bg-danger text-white">NE</th>
                                <th class="small fw-bold bg-dark text-warning">NFD</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($matrix as $row)
                        <tr>
                            <td class="text-start font-weight-bold text-dark">
                                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
                                <div class="text-muted small" style="font-size: 0.75rem;">N.º {{ $row['student']->student_number ?? 'N/A' }}</div>
                            </td>
                            @foreach($subjects as $subject)
                                @php 
                                    $subData = $row['subjects'][$subject->id] ?? []; 
                                    $nfd = $subData['mfd'] ?? '-';
                                    $nfdVal = is_numeric($nfd) ? (float) $nfd : null;
                                @endphp
                                <td class="text-muted">{{ $subData['mt1'] ?? '-' }}</td>
                                <td class="text-muted">{{ $subData['mt2'] ?? '-' }}</td>
                                <td class="text-muted">{{ $subData['mt3'] ?? '-' }}</td>
                                <td class="fw-bold bg-amber-50 text-amber-900">{{ $subData['mf'] ?? '-' }}</td>
                                @if($isExamClass)
                                    <td class="bg-danger-subtle text-danger font-bold">{{ $subData['exam'] ?? '-' }}</td>
                                    <td class="fw-bold {{ $nfdVal !== null ? ($nfdVal >= 10 ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle') : '' }}">
                                        {{ $nfd }}
                                    </td>
                                @endif
                            @endforeach
                            <td class="fw-bold bg-light text-primary">
                                {{ $row['overall_average'] > 0 ? number_format($row['overall_average'], 1) : '-' }}
                            </td>
                            <td>
                                <span class="badge {{ $row['final_status'] === 'Aprovado' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $row['final_status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (count($subjects) * ($isExamClass ? 6 : 4)) + 3 }}" class="py-5 text-muted">
                                Sem registos de notas anuais para esta turma.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
