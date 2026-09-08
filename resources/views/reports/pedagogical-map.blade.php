@extends('layouts.app')

@section('title', 'Mapa de Aproveitamento Pedagógico MINEDH')

@section('content')
<style>
    @media print {
        header, sidebar, nav, .no-print, footer { display: none !important; }
        body { background: white !important; font-size: 9pt; color: black; }
        .print-document { border: none !important; shadow: none !important; padding: 0 !important; margin: 0 !important; }
        .table-minedh th, .table-minedh td { border: 1px solid #000 !important; padding: 3px 4px !important; }
    }

    .table-minedh {
        border-collapse: collapse;
        width: 100%;
        font-size: 0.72rem;
    }
    .table-minedh th, .table-minedh td {
        border: 1px solid #cbd5e1;
        padding: 5px 6px;
    }
    .table-minedh th {
        background-color: #0f172a;
        color: #ffffff;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.68rem;
    }
    .table-minedh tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .bg-hm { background-color: #f1f5f9; }
    .bg-m { background-color: #faf5ff; color: #7e22ce; }
    .bg-pos { background-color: #f0fdf4; color: #15803d; }
    .bg-neg { background-color: #fef2f2; color: #b91c1c; }
    .bg-ciclo { background-color: #e2e8f0; font-weight: bold; }
</style>

<div class="container-fluid py-4 space-y-4">

    <!-- Top Selection & Territorial Filter Bar (No Print) -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 no-print bg-white">
        <div class="card-body p-3">
            <form id="pedagogicalMapFilterForm" action="{{ route('reports.pedagogical-map') }}" method="GET" class="row g-3 align-items-end">
                
                <!-- Territorial Filters (Província, Distrito, ZIP) -->
                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-map-pin text-emerald me-1" style="color: #059669;"></i> Província
                    </label>
                    <select name="province_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="onProvinceChange(this)">
                        <option value="">Todas Províncias</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" @selected($selectedProvinceId == $prov->id)>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-city text-emerald me-1" style="color: #059669;"></i> Distrito
                    </label>
                    <select name="district_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="onDistrictChange(this)">
                        <option value="">Todos Distritos</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" @selected($selectedDistrictId == $dist->id)>
                                {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-network-wired text-emerald me-1" style="color: #059669;"></i> ZIP
                    </label>
                    <select name="zip_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="onZipChange(this)">
                        <option value="">Todas as ZIPs</option>
                        @foreach($zips as $zip)
                            <option value="{{ $zip->id }}" @selected($selectedZipId == $zip->id)>
                                {{ $zip->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select School -->
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-school text-emerald me-1" style="color: #059669;"></i> Escola
                    </label>
                    <select name="school_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="onSchoolChange(this)">
                        @foreach($schools as $sc)
                            <option value="{{ $sc->id }}" @selected($selectedSchool && $selectedSchool->id === $sc->id)>
                                {{ $sc->name }} ({{ $sc->province->name ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Turma / Disciplinas -->
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-chalkboard text-emerald me-1" style="color: #059669;"></i> Turma (Disciplinas II)
                    </label>
                    <select name="class_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="this.form.submit()">
                        @foreach($allClasses as $c)
                            <option value="{{ $c->id }}" @selected($selectedClass && $selectedClass->id === $c->id)>
                                {{ $c->name }} ({{ $c->grade_level_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Term & Year & Actions -->
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-clock text-emerald me-1" style="color: #059669;"></i> Trimestre
                    </label>
                    <select name="term" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="this.form.submit()">
                        <option value="1" @selected($term == 1)>1º Trimestre</option>
                        <option value="2" @selected($term == 2)>2º Trimestre</option>
                        <option value="3" @selected($term == 3)>3º Trimestre</option>
                        <option value="4" @selected($term == 4)>Geral Anual</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-calendar text-emerald me-1" style="color: #059669;"></i> Ano Lectivo
                    </label>
                    <select name="year" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="this.form.submit()">
                        <option value="2026" @selected($year == 2026)>2026</option>
                        <option value="2025" @selected($year == 2025)>2025</option>
                    </select>
                </div>

                <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                    <button type="button" onclick="window.print()" class="btn btn-emerald btn-sm rounded-3 px-4 fw-semibold text-white" style="background-color: #059669;">
                        <i class="fa-solid fa-print me-1"></i> Imprimir Documento
                    </button>
                    @if($selectedClass)
                        <a href="{{ route('reports.pedagogical-map.export', ['class_id' => $selectedClass->id, 'term' => $term, 'year' => $year]) }}" class="btn btn-outline-success btn-sm rounded-3 px-3" title="Exportar CSV Excel">
                            <i class="fa-solid fa-file-excel me-1"></i> Exportar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN OFFICIAL MINEDH PAPER DOCUMENT -->
    <div class="card border border-slate-200 rounded-4 shadow-sm bg-white p-4 p-md-5 print-document">
        
        <!-- MINEDH OFFICIAL HEADER WITH CENTERED EMBLEM LOGO -->
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center align-items-center mb-2">
                <img src="{{ asset('image.png') }}" alt="Emblema da República de Moçambique" style="height: 85px; width: auto;" class="mx-auto d-block">
            </div>
            <h5 class="fw-bold mb-0 text-dark tracking-wide uppercase" style="font-family: serif; letter-spacing: 1.5px; font-size: 1.1rem;">REPÚBLICA DE MOÇAMBIQUE</h5>
            <h6 class="fw-semibold text-secondary mb-0 text-xs uppercase" style="letter-spacing: 0.8px;">
                GOVERNO DA PROVÍNCIA DE {{ strtoupper($selectedSchool?->province?->name ?? 'PROVÍNCIA') }}
            </h6>
            <h6 class="fw-semibold text-secondary mb-0 text-xs uppercase" style="letter-spacing: 0.8px;">
                SERVIÇO DISTRITAL DE EDUCAÇÃO, JUVENTUDE E TECNOLOGIA (SDEJT {{ strtoupper($selectedSchool?->district?->name ?? 'DISTRITO') }})
            </h6>
            @if($selectedSchool && $selectedSchool->zip)
                <h6 class="fw-semibold text-emerald-800 mb-0 text-xs uppercase" style="color: #047857; letter-spacing: 0.5px;">
                    ZONA DE INFLUÊNCIA PEDAGÓGICA: {{ strtoupper($selectedSchool->zip->name) }} {{ $selectedSchool->zip->headquarters ? '('.strtoupper($selectedSchool->zip->headquarters).')' : '' }}
                </h6>
            @endif
            <h4 class="fw-black text-dark mt-2 mb-1" style="color: #047857; font-size: 1.25rem;">
                {{ strtoupper($selectedSchool?->name ?? 'ESCOLA NÃO SELECIONADA') }}
            </h4>
            <div class="text-xs fw-bold text-muted uppercase mb-2">
                SUB-SISTEMA DE ENSINO: <span class="text-dark">{{ strtoupper($schoolTeachingType ?? 'ENSINO GERAL') }}</span> &bull; NATUREZA: <span class="text-dark">{{ strtoupper($selectedSchool?->type ?? 'Pública') }}</span>
            </div>

            <div class="d-inline-block px-4 py-1.5 rounded-pill bg-light border border-slate-300 mt-1">
                <strong class="text-dark uppercase text-sm" style="letter-spacing: 0.8px;">
                    MAPA DE APROVEITAMENTO PEDAGÓGICO — {{ $term == 4 ? 'FINAL DO ANO' : $term . 'º TRIMESTRE' }} DE {{ $year }}
                </strong>
            </div>
        </div>

        <!-- QUADRO 1: RENDIMENTO ESCOLAR COMPLETO MINEDH (11 COLUNAS HM / M) -->
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-list-ol text-emerald" style="color: #059669;"></i> I. Quadro Geral de Rendimento Escolar por Classe e Ciclo (MINEDH Standard)
                </h6>
                <span class="text-xs text-muted">Avaliados na escala regulamentar 0.0 - 20.0 (Limiar: {{ $passingThreshold }} val)</span>
            </div>

            <div class="table-responsive">
                <table class="table-minedh text-center align-middle">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-start ps-3" style="width: 18%;">Classe</th>
                            <th colspan="2">N/ Entradas (Matrícula)</th>
                            <th colspan="2">Desistentes</th>
                            <th colspan="2">Transf.</th>
                            <th colspan="2">Anulou Matrícula</th>
                            <th colspan="2">PPF / PDF</th>
                            <th colspan="2">Avaliados</th>
                            <th colspan="2">Transitados / Aprovados</th>
                            <th colspan="2">Retidos / Negativas</th>
                            <th colspan="2" style="background-color: #047857;">% Aprovados</th>
                            <th colspan="2" style="background-color: #991b1b;">% Desistentes</th>
                        </tr>
                        <tr style="font-size: 0.65rem; background-color: #1e293b;">
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th style="background-color: #065f46;">HM %</th><th style="background-color: #065f46;">M %</th>
                            <th style="background-color: #7f1d1d;">HM %</th><th style="background-color: #7f1d1d;">M %</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @forelse($classRows as $cm)
                            <tr>
                                <td class="text-start ps-3 fw-bold text-dark">
                                    <i class="fa-solid fa-graduation-cap text-emerald me-1" style="color: #059669;"></i> {{ $cm['grade_level_name'] }}
                                    <span class="text-muted font-normal text-2xs">({{ $cm['turmas_count'] }} {{ $cm['turmas_count'] == 1 ? 'Turma' : 'Turmas' }})</span>
                                </td>
                                <td class="fw-semibold bg-hm">{{ number_format($cm['total_hm']) }}</td>
                                <td class="fw-semibold bg-m">{{ number_format($cm['total_m']) }}</td>

                                <td>{{ number_format($cm['desist_hm']) }}</td>
                                <td class="text-purple">{{ number_format($cm['desist_m']) }}</td>

                                <td>{{ number_format($cm['transf_hm']) }}</td>
                                <td class="text-purple">{{ number_format($cm['transf_m']) }}</td>

                                <td>{{ number_format($cm['anulou_hm']) }}</td>
                                <td class="text-purple">{{ number_format($cm['anulou_m']) }}</td>

                                <td>{{ number_format($cm['ppf_hm']) }}</td>
                                <td class="text-purple">{{ number_format($cm['ppf_m']) }}</td>
                                
                                <td class="fw-bold">{{ number_format($cm['eval_hm']) }}</td>
                                <td class="fw-bold text-purple">{{ number_format($cm['eval_m']) }}</td>

                                <td class="fw-bold text-success bg-pos">{{ number_format($cm['passed_hm']) }}</td>
                                <td class="fw-bold text-success bg-pos">{{ number_format($cm['passed_m']) }}</td>

                                <td class="fw-bold text-danger bg-neg">{{ number_format($cm['failed_hm']) }}</td>
                                <td class="fw-bold text-danger bg-neg">{{ number_format($cm['failed_m']) }}</td>

                                <td class="fw-extrabold text-success" style="background-color: #ecfdf5;">{{ $cm['rate_hm'] }}%</td>
                                <td class="fw-extrabold text-purple" style="background-color: #faf5ff;">{{ $cm['rate_m'] }}%</td>

                                <td class="fw-semibold text-danger">{{ $cm['desist_rate_hm'] }}%</td>
                                <td class="fw-semibold text-danger">{{ $cm['desist_rate_m'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="21" class="text-center py-3 text-muted">Nenhuma classe registada nesta escola.</td>
                            </tr>
                        @endforelse

                        <!-- SUBTOTAL 1º CICLO -->
                        <tr class="bg-ciclo">
                            <td class="text-start ps-3 fw-bold">1º CICLO</td>
                            <td>{{ number_format($ciclo1Data['total_hm']) }}</td><td>{{ number_format($ciclo1Data['total_m']) }}</td>
                            <td>{{ number_format($ciclo1Data['desist_hm']) }}</td><td>{{ number_format($ciclo1Data['desist_m']) }}</td>
                            <td>{{ number_format($ciclo1Data['transf_hm']) }}</td><td>{{ number_format($ciclo1Data['transf_m']) }}</td>
                            <td>{{ number_format($ciclo1Data['anulou_hm']) }}</td><td>{{ number_format($ciclo1Data['anulou_m']) }}</td>
                            <td>{{ number_format($ciclo1Data['ppf_hm']) }}</td><td>{{ number_format($ciclo1Data['ppf_m']) }}</td>
                            <td>{{ number_format($ciclo1Data['eval_hm']) }}</td><td>{{ number_format($ciclo1Data['eval_m']) }}</td>
                            <td class="text-success">{{ number_format($ciclo1Data['passed_hm']) }}</td><td class="text-success">{{ number_format($ciclo1Data['passed_m']) }}</td>
                            <td class="text-danger">{{ number_format($ciclo1Data['failed_hm']) }}</td><td class="text-danger">{{ number_format($ciclo1Data['failed_m']) }}</td>
                            <td class="text-success fw-extrabold">{{ $ciclo1Data['rate_hm'] }}%</td><td class="text-purple fw-extrabold">{{ $ciclo1Data['rate_m'] }}%</td>
                            <td class="text-danger">{{ $ciclo1Data['desist_rate_hm'] }}%</td><td class="text-danger">{{ $ciclo1Data['desist_rate_m'] }}%</td>
                        </tr>

                        <!-- SUBTOTAL 2º CICLO -->
                        <tr class="bg-ciclo">
                            <td class="text-start ps-3 fw-bold">2º CICLO</td>
                            <td>{{ number_format($ciclo2Data['total_hm']) }}</td><td>{{ number_format($ciclo2Data['total_m']) }}</td>
                            <td>{{ number_format($ciclo2Data['desist_hm']) }}</td><td>{{ number_format($ciclo2Data['desist_m']) }}</td>
                            <td>{{ number_format($ciclo2Data['transf_hm']) }}</td><td>{{ number_format($ciclo2Data['transf_m']) }}</td>
                            <td>{{ number_format($ciclo2Data['anulou_hm']) }}</td><td>{{ number_format($ciclo2Data['anulou_m']) }}</td>
                            <td>{{ number_format($ciclo2Data['ppf_hm']) }}</td><td>{{ number_format($ciclo2Data['ppf_m']) }}</td>
                            <td>{{ number_format($ciclo2Data['eval_hm']) }}</td><td>{{ number_format($ciclo2Data['eval_m']) }}</td>
                            <td class="text-success">{{ number_format($ciclo2Data['passed_hm']) }}</td><td class="text-success">{{ number_format($ciclo2Data['passed_m']) }}</td>
                            <td class="text-danger">{{ number_format($ciclo2Data['failed_hm']) }}</td><td class="text-danger">{{ number_format($ciclo2Data['failed_m']) }}</td>
                            <td class="text-success fw-extrabold">{{ $ciclo2Data['rate_hm'] }}%</td><td class="text-purple fw-extrabold">{{ $ciclo2Data['rate_m'] }}%</td>
                            <td class="text-danger">{{ $ciclo2Data['desist_rate_hm'] }}%</td><td class="text-danger">{{ $ciclo2Data['desist_rate_m'] }}%</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold text-dark" style="background-color: #cbd5e1; font-size: 0.8rem;">
                            <td class="text-start ps-3">TOTAL GERAL DA ESCOLA</td>
                            <td>{{ number_format($totalGeralData['total_hm']) }}</td><td>{{ number_format($totalGeralData['total_m']) }}</td>
                            <td>{{ number_format($totalGeralData['desist_hm']) }}</td><td>{{ number_format($totalGeralData['desist_m']) }}</td>
                            <td>{{ number_format($totalGeralData['transf_hm']) }}</td><td>{{ number_format($totalGeralData['transf_m']) }}</td>
                            <td>{{ number_format($totalGeralData['anulou_hm']) }}</td><td>{{ number_format($totalGeralData['anulou_m']) }}</td>
                            <td>{{ number_format($totalGeralData['ppf_hm']) }}</td><td>{{ number_format($totalGeralData['ppf_m']) }}</td>
                            <td>{{ number_format($totalGeralData['eval_hm']) }}</td><td>{{ number_format($totalGeralData['eval_m']) }}</td>
                            <td class="text-success">{{ number_format($totalGeralData['passed_hm']) }}</td><td class="text-success">{{ number_format($totalGeralData['passed_m']) }}</td>
                            <td class="text-danger">{{ number_format($totalGeralData['failed_hm']) }}</td><td class="text-danger">{{ number_format($totalGeralData['failed_m']) }}</td>
                            <td class="text-success fw-black">{{ $totalGeralData['rate_hm'] }}%</td><td class="text-purple fw-black">{{ $totalGeralData['rate_m'] }}%</td>
                            <td class="text-danger fw-bold">{{ $totalGeralData['desist_rate_hm'] }}%</td><td class="text-danger fw-bold">{{ $totalGeralData['desist_rate_m'] }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- QUADRO 2: DESEMPENHO DISCIPLINAR DA TURMA SELECIONADA -->
        @if($selectedClass)
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-book-open text-emerald" style="color: #059669;"></i> II. Aproveitamento por Disciplina — Turma {{ $selectedClass->name }} ({{ $selectedClass->grade_level_name }})
                </h6>
                <span class="badge bg-emerald-subtle text-emerald px-3 py-1" style="background-color: #d1fae5; color: #047857;">{{ count($mapData) }} Disciplinas Avaliadas</span>
            </div>

            <div class="table-responsive">
                <table class="table-minedh text-center align-middle">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-start ps-3" style="width: 25%;">Disciplina</th>
                            <th colspan="2">Avaliados</th>
                            <th colspan="2">Positivas (>= 9.5 val)</th>
                            <th colspan="2">Negativas (< 9.5 val)</th>
                            <th colspan="2" style="background-color: #047857;">% Aproveitamento</th>
                        </tr>
                        <tr style="font-size: 0.7rem; background-color: #1e293b;">
                            <th>HM (Total)</th><th>M (Mulheres)</th>
                            <th>HM (Total)</th><th>M (Mulheres)</th>
                            <th>HM (Total)</th><th>M (Mulheres)</th>
                            <th style="background-color: #065f46;">HM %</th><th style="background-color: #065f46;">M %</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @forelse($mapData as $item)
                            <tr>
                                <td class="text-start ps-3 fw-bold text-dark">
                                    {{ $item['subject']->name }}
                                    <span class="text-muted font-mono text-xs">({{ $item['subject']->code ?? '—' }})</span>
                                </td>
                                <td class="fw-semibold bg-hm">{{ $item['hm_evaluated'] }}</td>
                                <td class="fw-semibold bg-m">{{ $item['m_evaluated'] }}</td>

                                <td class="fw-bold text-success bg-pos">{{ $item['hm_positives'] }}</td>
                                <td class="fw-bold text-success bg-pos">{{ $item['m_positives'] }}</td>

                                <td class="fw-bold text-danger bg-neg">{{ $item['hm_negatives'] }}</td>
                                <td class="fw-bold text-danger bg-neg">{{ $item['m_negatives'] }}</td>

                                <td class="fw-extrabold text-success" style="background-color: #ecfdf5;">{{ $item['hm_perc'] }}%</td>
                                <td class="fw-extrabold text-purple" style="background-color: #faf5ff;">{{ $item['m_perc'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-3 text-muted">Nenhuma avaliação registada para esta turma.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- OFFICIAL SIGNATURE BLOCKS (DAP & DAE MINEDH Standard) -->
        <div class="mt-5 pt-3">
            <div class="text-end mb-4 text-xs font-semibold text-dark">
                {{ $selectedSchool?->district?->name ?? 'DISTRITO' }}, {{ date('d') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} de {{ $year }}
            </div>

            <div class="row text-center pt-2">
                <div class="col-6">
                    <p class="mb-4 text-xs font-bold text-dark uppercase">O Diretor Adjunto Pedagógico (DAP)</p>
                    <div style="border-bottom: 1.5px solid #000; width: 65%; margin: 0 auto 8px auto;"></div>
                    <p class="mb-0 text-xs font-semibold text-muted">_______________________________________</p>
                    <p class="text-2xs text-muted">(Assinatura e Carimbo Oficial)</p>
                </div>
                <div class="col-6">
                    <p class="mb-4 text-xs font-bold text-dark uppercase">O Diretor da Escola (DAE)</p>
                    <div style="border-bottom: 1.5px solid #000; width: 65%; margin: 0 auto 8px auto;"></div>
                    <p class="mb-0 text-xs font-bold text-dark">{{ strtoupper($selectedSchool?->principal_name ?? 'Prof. Diretor Geral') }}</p>
                    <p class="text-2xs text-muted">(Assinatura e Carimbo Oficial)</p>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Filter Cascade Submit Script -->
<script>
function onProvinceChange(select) {
    var form = document.getElementById('pedagogicalMapFilterForm');
    form.querySelector('[name="district_id"]').value = '';
    form.querySelector('[name="zip_id"]').value = '';
    form.querySelector('[name="school_id"]').value = '';
    form.querySelector('[name="class_id"]').value = '';
    form.submit();
}

function onDistrictChange(select) {
    var form = document.getElementById('pedagogicalMapFilterForm');
    form.querySelector('[name="zip_id"]').value = '';
    form.querySelector('[name="school_id"]').value = '';
    form.querySelector('[name="class_id"]').value = '';
    form.submit();
}

function onZipChange(select) {
    var form = document.getElementById('pedagogicalMapFilterForm');
    form.querySelector('[name="school_id"]').value = '';
    form.querySelector('[name="class_id"]').value = '';
    form.submit();
}

function onSchoolChange(select) {
    var form = document.getElementById('pedagogicalMapFilterForm');
    form.querySelector('[name="class_id"]').value = '';
    form.submit();
}
</script>
@endsection
