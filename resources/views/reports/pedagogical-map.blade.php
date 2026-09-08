@extends('layouts.app')

@section('title', 'Mapa de Aproveitamento Pedagógico MINEDH')

@section('content')
<style>
    @media print {
        header, sidebar, nav, .no-print, footer { display: none !important; }
        body { background: white !important; font-size: 10pt; color: black; }
        .print-document { border: none !important; shadow: none !important; padding: 0 !important; margin: 0 !important; }
        .table-minedh th, .table-minedh td { border: 1px solid #000 !important; padding: 4px 6px !important; }
    }

    .table-minedh {
        border-collapse: collapse;
        width: 100%;
    }
    .table-minedh th, .table-minedh td {
        border: 1px solid #cbd5e1;
        padding: 6px 8px;
    }
    .table-minedh th {
        background-color: #0f172a;
        color: #ffffff;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .table-minedh tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .bg-hm { background-color: #f1f5f9; }
    .bg-m { background-color: #faf5ff; color: #7e22ce; }
    .bg-pos { background-color: #f0fdf4; color: #15803d; }
    .bg-neg { background-color: #fef2f2; color: #b91c1c; }
</style>

<div class="container-fluid py-4 space-y-4">

    <!-- Top Selection & Territorial Filter Bar (No Print) -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 no-print bg-white">
        <div class="card-body p-3">
            <form action="{{ route('reports.pedagogical-map') }}" method="GET" class="row g-3 align-items-end">
                
                <!-- Territorial Filters (Província, Distrito, ZIP) -->
                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-map-pin text-emerald me-1" style="color: #059669;"></i> Província
                    </label>
                    <select name="province_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="this.form.submit()">
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
                    <select name="district_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="this.form.submit()">
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
                    <select name="zip_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="this.form.submit()">
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
                    <select name="school_id" class="form-select form-select-sm rounded-3 fw-semibold text-xs" onchange="this.form.submit()">
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
                <img src="{{ asset('image.png') }}" alt="Emblema da República de Moçambique" style="height: 80px; width: auto;" class="mx-auto d-block">
            </div>
            <h5 class="fw-bold mb-0 text-dark tracking-wide uppercase" style="font-family: serif; letter-spacing: 1px;">REPÚBLICA DE MOÇAMBIQUE</h5>
            <h6 class="fw-semibold text-secondary mb-0 text-xs uppercase">GOVERNO DA PROVÍNCIA DE {{ strtoupper($selectedSchool->province->name ?? 'ZAMBÉZIA') }}</h6>
            <h6 class="fw-semibold text-secondary mb-0 text-xs uppercase">SERVIÇO DISTRITAL DE EDUCAÇÃO, JUVENTUDE E TECNOLOGIA (SDEJT {{ strtoupper($selectedSchool->district->name ?? 'MARRACUA') }})</h6>
            <h5 class="fw-bold text-dark mt-2 mb-1" style="color: #047857;">{{ strtoupper($selectedSchool->name ?? 'ESCOLA SECUNDÁRIA DE MARRACUA') }}</h5>
            
            <div class="d-inline-block px-4 py-1.5 rounded-pill bg-light border border-slate-300 mt-2">
                <strong class="text-dark uppercase text-sm" style="letter-spacing: 0.5px;">
                    MAPA DE APROVEITAMENTO PEDAGÓGICO — {{ $term == 4 ? 'GERAL ANUAL' : $term . 'º TRIMESTRE' }} DE {{ $year }}
                </strong>
            </div>
        </div>

        <!-- QUADRO 1: RENDIMENTO ESCOLAR AGROPADO POR CLASSE (1ª a 12ª Classe) -->
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-list-ol text-emerald" style="color: #059669;"></i> I. Quadro Resumo de Aproveitamento por Classe (HM / M)
                </h6>
                <span class="text-xs text-muted">Agrupado por Classe &bull; Avaliados na escala 0.0 - 20.0 (Limiar: {{ $passingThreshold }} val)</span>
            </div>

            <div class="table-responsive">
                <table class="table-minedh text-center align-middle">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-start ps-3" style="width: 24%;">Classe</th>
                            <th colspan="2">Matrícula (Entradas)</th>
                            <th colspan="2">Avaliados</th>
                            <th colspan="2">Aprovados / Transitados</th>
                            <th colspan="2">Retidos / Negativas</th>
                            <th colspan="2" style="background-color: #047857;">% Aproveitamento</th>
                        </tr>
                        <tr style="font-size: 0.7rem; background-color: #1e293b;">
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th style="background-color: #065f46;">HM %</th><th style="background-color: #065f46;">M %</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @php
                            $totHM = 0; $totM = 0;
                            $totEvalHM = 0; $totEvalM = 0;
                            $totPassHM = 0; $totPassM = 0;
                            $totFailHM = 0; $totFailM = 0;
                        @endphp
                        @forelse($classMatrix as $cm)
                            @php
                                $totHM += $cm['total_hm']; $totM += $cm['total_m'];
                                $totEvalHM += $cm['eval_hm']; $totEvalM += $cm['eval_m'];
                                $totPassHM += $cm['passed_hm']; $totPassM += $cm['passed_m'];
                                $totFailHM += $cm['failed_hm']; $totFailM += $cm['failed_m'];
                            @endphp
                            <tr>
                                <td class="text-start ps-3 fw-bold text-dark">
                                    <i class="fa-solid fa-graduation-cap text-emerald me-1" style="color: #059669;"></i> {{ $cm['grade_level_name'] }}
                                    <span class="text-muted font-normal text-2xs">({{ $cm['turmas_count'] }} {{ $cm['turmas_count'] == 1 ? 'Turma' : 'Turmas' }})</span>
                                </td>
                                <td class="fw-semibold bg-hm">{{ number_format($cm['total_hm']) }}</td>
                                <td class="fw-semibold bg-m">{{ number_format($cm['total_m']) }}</td>
                                
                                <td class="fw-bold">{{ number_format($cm['eval_hm']) }}</td>
                                <td class="fw-bold text-purple">{{ number_format($cm['eval_m']) }}</td>

                                <td class="fw-bold text-success bg-pos">{{ number_format($cm['passed_hm']) }}</td>
                                <td class="fw-bold text-success bg-pos">{{ number_format($cm['passed_m']) }}</td>

                                <td class="fw-bold text-danger bg-neg">{{ number_format($cm['failed_hm']) }}</td>
                                <td class="fw-bold text-danger bg-neg">{{ number_format($cm['failed_m']) }}</td>

                                <td class="fw-extrabold text-success" style="background-color: #ecfdf5;">{{ $cm['rate_hm'] }}%</td>
                                <td class="fw-extrabold text-purple" style="background-color: #faf5ff;">{{ $cm['rate_m'] }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-3 text-muted">Nenhuma classe registada nesta escola.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        @php
                            $totalRateHM = $totEvalHM > 0 ? round(($totPassHM / $totEvalHM) * 100, 1) : 0;
                            $totalRateM = $totEvalM > 0 ? round(($totPassM / $totEvalM) * 100, 1) : 0;
                        @endphp
                        <tr class="fw-bold text-dark" style="background-color: #e2e8f0; font-size: 0.8rem;">
                            <td class="text-start ps-3">TOTAL GERAL DA ESCOLA</td>
                            <td>{{ number_format($totHM) }}</td>
                            <td>{{ number_format($totM) }}</td>
                            <td>{{ number_format($totEvalHM) }}</td>
                            <td>{{ number_format($totEvalM) }}</td>
                            <td class="text-success">{{ number_format($totPassHM) }}</td>
                            <td class="text-success">{{ number_format($totPassM) }}</td>
                            <td class="text-danger">{{ number_format($totFailHM) }}</td>
                            <td class="text-danger">{{ number_format($totFailM) }}</td>
                            <td class="text-success fw-black">{{ $totalRateHM }}%</td>
                            <td class="text-purple fw-black">{{ $totalRateM }}%</td>
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
                {{ $selectedSchool->district->name ?? 'Marracua' }}, {{ date('d') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} de {{ $year }}
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
                    <p class="mb-0 text-xs font-bold text-dark">{{ strtoupper($selectedSchool->principal_name ?? 'Prof. Diretor Geral') }}</p>
                    <p class="text-2xs text-muted">(Assinatura e Carimbo Oficial)</p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
