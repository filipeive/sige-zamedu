@extends('layouts.app')

@section('title', 'Pauta de Exames com Júris - ' . $class->name)

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
                <span class="badge bg-danger text-white fw-bold">
                    <i class="fas fa-balance-scale me-1"></i> Exame Nacional & Júri (Lei 18/2018)
                </span>
            </div>
            <h1 class="h3 font-weight-bold text-dark mb-0">Pauta de Exames com Júris: {{ $class->name }}</h1>
            <p class="text-muted small mb-0">Apuramento oficial de Exame ($NE$), Recorrência ($ACF$), $NE\text{ Eficaz}$ e Nota Final da Disciplina ($NFD$) com Júri de Exame MINEDH.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#juryConfigModal">
                <i class="fas fa-user-tie me-1"></i> Atribuir / Editar Júri
            </button>

            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('pautas.trimestral', $class->id) }}" class="btn btn-outline-primary fw-bold">
                    <i class="fas fa-list-alt me-1"></i> Trimestral
                </a>
                <a href="{{ route('pautas.anual', $class->id) }}" class="btn btn-outline-primary fw-bold">
                    <i class="fas fa-calendar-alt me-1"></i> Anual
                </a>
                <a href="{{ route('pautas.final', $class->id) }}" class="btn btn-outline-warning text-dark fw-bold">
                    <i class="fas fa-graduation-cap me-1"></i> Final
                </a>
                <a href="{{ route('pautas.exame', $class->id) }}" class="btn btn-danger active fw-bold">
                    <i class="fas fa-file-signature me-1"></i> Exames & Júri
                </a>
            </div>

            <a href="{{ route('pautas.pdf', ['class' => $class->id, 'type' => 'final', 'year' => $year]) }}" class="btn btn-danger btn-sm shadow-sm font-bold">
                <i class="fas fa-file-pdf me-1"></i> Exportar PDF
            </a>
            <a href="{{ route('classes.show', $class->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Bloco de Informação do Júri -->
    <div class="card shadow-sm border-0 mb-4 bg-gradient text-dark rounded-3" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
        <div class="card-body p-3">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-3 border-end">
                    <span class="text-uppercase text-xs font-weight-bold text-muted d-block mb-1">Júri de Exame</span>
                    <h5 class="fw-bold text-danger mb-0">{{ $juryCode ?: "Júri Nº {$class->name}" }}</h5>
                </div>
                <div class="col-md-3 border-end">
                    <span class="text-uppercase text-xs font-weight-bold text-muted d-block mb-1">Presidente do Júri</span>
                    <strong class="text-dark">{{ $juryPresident ?: 'Não atribuído' }}</strong>
                </div>
                <div class="col-md-3 border-end">
                    <span class="text-uppercase text-xs font-weight-bold text-muted d-block mb-1">1º Vogal / Examinador</span>
                    <strong class="text-dark">{{ $juryExaminer1 ?: 'Não atribuído' }}</strong>
                </div>
                <div class="col-md-3">
                    <span class="text-uppercase text-xs font-weight-bold text-muted d-block mb-1">2º Vogal / Co-Examinador</span>
                    <strong class="text-dark">{{ $juryExaminer2 ?: 'Não atribuído' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Pesquisa -->
    <div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('pautas.exame', $class->id) }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label text-xs font-weight-bold text-muted mb-1">
                            <i class="fas fa-users text-danger me-1"></i> Turma
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
                            <i class="fas fa-book text-danger me-1"></i> Disciplina
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
                            <i class="fas fa-calendar text-danger me-1"></i> Ano Lectivo
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

    <!-- Tabela Matriz de Exame com Júri -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 0.86rem;">
                <thead class="table-dark text-uppercase tracking-wider">
                    <tr>
                        <th rowspan="2" style="width: 45px;" class="align-middle">Nº</th>
                        <th rowspan="2" class="align-middle text-start" style="min-width: 200px;">Nome Completo do Aluno</th>
                        <th rowspan="2" style="width: 50px;" class="align-middle">Sexo</th>
                        @foreach($subjects as $subject)
                            <th colspan="5" class="border-start border-end bg-dark text-white py-2" style="min-width: 250px;">
                                {{ $subject->name }}
                            </th>
                        @endforeach
                        <th rowspan="2" class="align-middle bg-secondary text-white" style="min-width: 140px;">Resultado do Júri</th>
                    </tr>
                    <tr class="bg-secondary text-white text-xs">
                        @foreach($subjects as $subject)
                            <th style="width: 45px;" title="Média Anual de Frequência">MAF</th>
                            <th style="width: 45px;" title="Nota de Exame Época Normal">NE</th>
                            <th style="width: 45px;" title="Nota Recorrência / Média Júri">ACF</th>
                            <th style="width: 45px;" title="NE Eficaz = max(NE, ACF)">NE_E</th>
                            <th style="width: 55px;" class="bg-primary text-white font-weight-bold" title="Nota Final da Disciplina = round((MAF + NE_Eficaz) / 2)">NFD</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($matrix as $index => $row)
                        @php
                            $student = $row['student'];
                            $gender = strtoupper(substr($student->gender ?? 'M', 0, 1));
                        @endphp
                        <tr>
                            <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                            <td class="text-start fw-bold text-dark">
                                <a href="{{ route('students.show', $student->id) }}" class="text-dark text-decoration-none hover-primary">
                                    {{ $student->full_name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $gender === 'F' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}">
                                    {{ $gender }}
                                </span>
                            </td>

                            @foreach($subjects as $subject)
                                @php
                                    $subData = $row['subjects'][$subject->id] ?? null;
                                    $maf = $subData['maf'] ?? '-';
                                    $ne = $subData['ne'] ?? '-';
                                    $acf = $subData['acf'] ?? '-';
                                    $neEficaz = $subData['ne_eficaz'] ?? '-';
                                    $nfd = $subData['nfd'] ?? '-';

                                    $isNfdNumeric = is_numeric($nfd);
                                    $nfdVal = $isNfdNumeric ? (float)$nfd : 0;
                                    $isPositive = $nfdVal >= 9.5;
                                @endphp
                                <td>{{ $maf }}</td>
                                <td>{{ $ne }}</td>
                                <td>{{ $acf }}</td>
                                <td class="fw-bold">{{ $neEficaz }}</td>
                                <td class="fw-bold border-start border-end {{ $isNfdNumeric ? ($isPositive ? 'table-success text-success' : 'table-danger text-danger') : '' }}">
                                    {{ $nfd }}
                                </td>
                            @endforeach

                            <td class="fw-bold">
                                @if($row['final_status'] === 'Aprovado')
                                    <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Aprovado</span>
                                @elseif($row['final_status'] === 'Exame')
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-clock me-1"></i> Recorrência</span>
                                @elseif($row['final_status'] === 'Reprovado')
                                    <span class="badge bg-danger px-2 py-1"><i class="fas fa-times-circle me-1"></i> Reprovado</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">{{ $row['final_status'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + (count($subjects) * 5) }}" class="py-4 text-center text-muted">
                                Nenhuma avaliação ou nota de exame registada para esta turma.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bloco de Assinaturas Oficiais do Júri para Impressão -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark text-uppercase border-bottom pb-2 mb-4">
                <i class="fas fa-signature text-danger me-2"></i> Termo de Homologação e Assinaturas do Júri de Exame
            </h6>
            <div class="row text-center mt-3 g-4">
                <div class="col-md-4">
                    <div class="border-top border-dark pt-2 mx-auto" style="max-width: 250px;">
                        <p class="fw-bold mb-0 text-dark">{{ $juryPresident ?: '________________________' }}</p>
                        <small class="text-muted d-block">Presidente do Júri</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-top border-dark pt-2 mx-auto" style="max-width: 250px;">
                        <p class="fw-bold mb-0 text-dark">{{ $juryExaminer1 ?: '________________________' }}</p>
                        <small class="text-muted d-block">1º Vogal (Examinador)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-top border-dark pt-2 mx-auto" style="max-width: 250px;">
                        <p class="fw-bold mb-0 text-dark">{{ $juryExaminer2 ?: '________________________' }}</p>
                        <small class="text-muted d-block">2º Vogal (Co-Examinador)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Atribuição do Júri -->
<div class="modal fade" id="juryConfigModal" tabindex="-1" aria-labelledby="juryConfigModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold" id="juryConfigModalLabel">
                    <i class="fas fa-user-tie me-2"></i> Atribuir / Editar Júri de Exame
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('pautas.exame.jury', $class->id) }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">Código / Identificação do Júri</label>
                        <input type="text" name="jury_code" class="form-control" value="{{ $juryCode }}" placeholder="Ex: Júri Nº 12 - 12ª Classe A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">Presidente do Júri</label>
                        <input type="text" name="jury_president" class="form-control" value="{{ $juryPresident }}" placeholder="Nome do Presidente do Júri">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">1º Vogal (Examinador)</label>
                        <input type="text" name="jury_examiner1" class="form-control" value="{{ $juryExaminer1 }}" placeholder="Nome do 1º Vogal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">2º Vogal (Co-Examinador)</label>
                        <input type="text" name="jury_examiner2" class="form-control" value="{{ $juryExaminer2 }}" placeholder="Nome do 2º Vogal">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger font-weight-bold">
                        <i class="fas fa-save me-1"></i> Guardar Atribuição
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
