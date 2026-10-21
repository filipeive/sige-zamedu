@extends('layouts.app')

@section('title', 'Mapa de Aproveitamento Pedagógico')
@section('page-title', 'Mapa de Aproveitamento Pedagógico')
@section('page-title-icon', 'fas fa-chart-line')

@section('breadcrumbs')
    <li class="breadcrumb-item">Relatórios</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Mapa de Aproveitamento Pedagógico</li>
@endsection

@section('content')
<style>
    @media print {
        header, sidebar, nav, .no-print, footer { display: none !important; }
        body { background: white !important; font-size: 9pt; color: black; }
        .print-document { border: none !important; shadow: none !important; padding: 0 !important; margin: 0 !important; }
        .table-minedh th, .table-minedh td { border: 1px solid #000 !important; padding: 3px 4px !important; }
        .tab-pane { display: block !important; }
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

    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .nav-tabs .nav-link { border-radius: 0.5rem 0.5rem 0 0; }
    .nav-tabs .nav-link.active { background-color: #0f172a; color: #fff; }
</style>

<div class="container-fluid py-4 space-y-4">

    <!-- Top Selection & Filter Bar (No Print) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs mb-6 no-print">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-filter text-emerald-600 text-xs"></i>
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Parâmetros do Mapa Pedagógico</span>
        </div>
        <form id="pedagogicalMapFilterForm" action="{{ route('reports.pedagogical-map') }}" method="GET" class="space-y-3">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
                @if(!is_private_school())
                <!-- Província -->
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-map-pin text-emerald-600 me-1"></i> Província
                    </label>
                    <select name="province_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="onProvinceChange(this)">
                        <option value="">Todas Províncias</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" @selected($selectedProvinceId == $prov->id)>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Distrito -->
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-city text-emerald-600 me-1"></i> Distrito
                    </label>
                    <select name="district_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="onDistrictChange(this)">
                        <option value="">Todos Distritos</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" @selected($selectedDistrictId == $dist->id)>
                                {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ZIP -->
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-network-wired text-emerald-600 me-1"></i> ZIP
                    </label>
                    <select name="zip_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="onZipChange(this)">
                        <option value="">Todas as ZIPs</option>
                        @foreach($zips as $zip)
                            <option value="{{ $zip->id }}" @selected($selectedZipId == $zip->id)>
                                {{ $zip->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                @endhasanyrole

                <!-- Escola / Filial -->
                <div class="{{ (!auth()->user()->hasAnyRole(['province_admin', 'province_inspector', 'district_admin', 'district_inspector', 'zip_coordinator', 'zip_admin']) || is_private_school()) ? 'sm:col-span-2 md:col-span-4' : '' }}">
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-school text-emerald-600 me-1"></i> Filial / Unidade de Ensino
                    </label>
                    <select name="school_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="onSchoolChange(this)">
                        @foreach($schools as $sc)
                            <option value="{{ $sc->id }}" @selected($selectedSchool && $selectedSchool->id === $sc->id)>
                                {{ $sc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                <!-- Nível de Ensino -->
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-layer-group text-emerald-600 me-1"></i> Nível
                    </label>
                    <select name="level" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="onLevelChange(this)">
                        <option value="primary" @selected(($educationLevel ?? 'primary') == 'primary')>Ensino Primário (1.ª - 6.ª)</option>
                        <option value="secondary" @selected(($educationLevel ?? '') == 'secondary')>Ensino Secundário (7.ª - 12.ª)</option>
                        <option value="preschool" @selected(($educationLevel ?? '') == 'preschool')>Pré-Escolar</option>
                        <option value="all" @selected(($educationLevel ?? '') == 'all')>Todos os Níveis</option>
                    </select>
                </div>

                <!-- Turma -->
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-chalkboard text-emerald-600 me-1"></i> Turma
                    </label>
                    <select name="class_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @foreach($allClasses as $c)
                            <option value="{{ $c->id }}" @selected($selectedClass && $selectedClass->id === $c->id)>
                                {{ $c->name }} ({{ $c->grade_level_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Trimestre -->
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-clock text-emerald-600 me-1"></i> Trimestre
                    </label>
                    <select name="term" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="1" @selected($term == 1)>1º Trimestre</option>
                        <option value="2" @selected($term == 2)>2º Trimestre</option>
                        <option value="3" @selected($term == 3)>3º Trimestre</option>
                        <option value="4" @selected($term == 4)>Geral Anual</option>
                    </select>
                </div>

                <!-- Ano Lectivo -->
                <div>
                    <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                        <i class="fa-solid fa-calendar text-emerald-600 me-1"></i> Ano Lectivo
                    </label>
                    <select name="year" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="2026" @selected($year == 2026)>2026</option>
                        <option value="2025" @selected($year == 2025)>2025</option>
                    </select>
                </div>

                <!-- Ações -->
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="no-underline inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-slate-800 hover:bg-slate-900 text-xs font-bold text-white shadow-2xs transition cursor-pointer shrink-0">
                        <i class="fa-solid fa-print"></i> Imprimir
                    </button>
                    <a href="{{ route('reports.pedagogical-map.export-pdf', request()->query()) }}" target="_blank" class="no-underline inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-red-600 hover:bg-red-700 text-xs font-bold text-white shadow-2xs transition shrink-0" title="Exportar PDF Oficial (Apenas Mapa e Assinaturas)">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('reports.pedagogical-map.export', request()->query()) }}" class="no-underline inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition shrink-0" title="Exportar Excel (Apenas Mapa e Assinaturas)">
                        <i class="fa-solid fa-file-excel"></i> Excel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab Navigation for Quadro 1 and Quadro 2 -->
    <ul class="nav nav-tabs mb-4 no-print" id="pedagogicalMapTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-rendimento-tab" data-bs-toggle="tab" data-bs-target="#tab-rendimento" type="button" role="tab" aria-controls="tab-rendimento" aria-selected="true">
                <i class="fas fa-chart-bar me-1"></i> I. Quadro de Rendimento Escolar
            </button>
        </li>
        @if($selectedClass)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-disciplinas-tab" data-bs-toggle="tab" data-bs-target="#tab-disciplinas" type="button" role="tab" aria-controls="tab-disciplinas" aria-selected="false">
                <i class="fas fa-book-open me-1"></i> II. Aproveitamento por Disciplina
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="pedagogicalMapTabContent">
        <!-- TAB 1: Quadro 1 - Rendimento Escolar -->
        <div class="tab-pane fade show active" id="tab-rendimento" role="tabpanel" aria-labelledby="tab-rendimento-tab">
            <!-- MAIN OFFICIAL MINEDH PAPER DOCUMENT -->
            <div class="card border border-slate-200 rounded-4 shadow-sm bg-white p-4 p-md-5 print-document">
        
        @php
            $isPrivateInstitution = ($selectedSchool && ($selectedSchool->isPrivate() || $selectedSchool->type === 'private' || str_contains(strtolower($selectedSchool->name), 'visionários')));
        @endphp

        @if($isPrivateInstitution)
        <!-- CABEÇALHO ESCOLA PRIVADA / REDE VISIONÁRIOS -->
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center align-items-center mb-2">
                <img src="{{ asset('images/logo_visionarios.png') }}" alt="{{ $selectedSchool->name }}" style="height: 75px; width: auto;" class="mx-auto d-block">
            </div>
            <h4 class="fw-black text-dark mb-1" style="color: #047857; font-size: 1.35rem; letter-spacing: 0.5px;">
                {{ strtoupper($selectedSchool?->name ?? 'ESCOLA DOS VISIONÁRIOS') }}
            </h4>
            <div class="text-xs fw-bold text-muted uppercase mb-2">
                UNIDADE: <span class="text-dark">{{ strtoupper($selectedSchool?->city ?? $selectedSchool?->district?->name ?? 'Quelimane') }}</span> &bull; NATUREZA: <span class="text-emerald-700 font-extrabold">ENSINO PRIVADO</span>
            </div>

            <div class="d-inline-block px-4 py-1.5 rounded-pill bg-light border border-slate-300 mt-1">
                <strong class="text-dark uppercase text-sm" style="letter-spacing: 0.8px;">
                    MAPA DE APROVEITAMENTO PEDAGÓGICO — {{ $term == 4 ? 'FINAL DO ANO' : $term . 'º TRIMESTRE' }} DE {{ $year }}
                </strong>
            </div>
        </div>
        @else
        <!-- CABEÇALHO OFICIAL GOVERNAMENTAL -->
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
        @endif

        <!-- QUADRO 1: RENDIMENTO ESCOLAR COMPLETO (11 COLUNAS HM / M) -->
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-list-ol text-emerald" style="color: #059669;"></i> I. Quadro Geral de Rendimento Escolar por Classe e Ciclo
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
                            <th colspan="2" style="background-color: #047857;">% TRANS/ APROVADOS</th>
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
                            <th style="background-color: #065f46;">HM %</th><th style="background-color: #065f46;">M %</th>
                            <th style="background-color: #7f1d1d;">HM %</th><th style="background-color: #7f1d1d;">M %</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @if(!empty($preschoolRows) && count($preschoolRows) > 0)
                            <tr class="bg-slate-100 font-extrabold text-slate-900 border-y border-slate-300">
                                <td colspan="19" class="text-start ps-3 py-1.5 bg-slate-200/90 font-black tracking-wider text-uppercase">
                                    PRÉ-ESCOLAR / PRÉ-INFANTIL
                                </td>
                            </tr>
                            @foreach($preschoolRows as $cm)
                                <tr>
                                    <td class="text-start ps-3 fw-bold text-dark">{{ $cm['grade_level_name'] }}</td>
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

                                    <td class="fw-extrabold text-success" style="background-color: #ecfdf5;">{{ $cm['rate_hm'] }}%</td>
                                    <td class="fw-extrabold text-purple" style="background-color: #faf5ff;">{{ $cm['rate_m'] }}%</td>

                                    <td class="fw-semibold text-danger">{{ $cm['desist_rate_hm'] }}%</td>
                                    <td class="fw-semibold text-danger">{{ $cm['desist_rate_m'] }}%</td>
                                </tr>
                            @endforeach
                        @endif

                        @if(!empty($ciclo1Rows) && count($ciclo1Rows) > 0)
                            <!-- CABEÇALHO 1º CICLO -->
                            <tr class="bg-slate-100 font-extrabold text-slate-900 border-y border-slate-300">
                                <td colspan="19" class="text-start ps-3 py-1.5 bg-slate-200/90 font-black tracking-wider text-uppercase">
                                    {{ $ciclo1Data['title'] }}
                                </td>
                            </tr>
                            @foreach($ciclo1Rows as $cm)
                                <tr>
                                    <td class="text-start ps-3 fw-bold text-dark">{{ $cm['grade_level_name'] }}</td>
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

                                    <td class="fw-extrabold text-success" style="background-color: #ecfdf5;">{{ $cm['rate_hm'] }}%</td>
                                    <td class="fw-extrabold text-purple" style="background-color: #faf5ff;">{{ $cm['rate_m'] }}%</td>

                                    <td class="fw-semibold text-danger">{{ $cm['desist_rate_hm'] }}%</td>
                                    <td class="fw-semibold text-danger">{{ $cm['desist_rate_m'] }}%</td>
                                </tr>
                            @endforeach
                            <!-- SUBTOTAL 1º CICLO -->
                            <tr class="bg-ciclo">
                                <td class="text-start ps-3 fw-bold">TOTAL 1º CICLO</td>
                                <td>{{ number_format($ciclo1Data['total_hm']) }}</td><td>{{ number_format($ciclo1Data['total_m']) }}</td>
                                <td>{{ number_format($ciclo1Data['desist_hm']) }}</td><td>{{ number_format($ciclo1Data['desist_m']) }}</td>
                                <td>{{ number_format($ciclo1Data['transf_hm']) }}</td><td>{{ number_format($ciclo1Data['transf_m']) }}</td>
                                <td>{{ number_format($ciclo1Data['anulou_hm']) }}</td><td>{{ number_format($ciclo1Data['anulou_m']) }}</td>
                                <td>{{ number_format($ciclo1Data['ppf_hm']) }}</td><td>{{ number_format($ciclo1Data['ppf_m']) }}</td>
                                <td>{{ number_format($ciclo1Data['eval_hm']) }}</td><td>{{ number_format($ciclo1Data['eval_m']) }}</td>
                                <td class="text-success">{{ number_format($ciclo1Data['passed_hm']) }}</td><td class="text-success">{{ number_format($ciclo1Data['passed_m']) }}</td>
                                <td class="text-success fw-extrabold">{{ $ciclo1Data['rate_hm'] }}%</td><td class="text-purple fw-extrabold">{{ $ciclo1Data['rate_m'] }}%</td>
                                <td class="text-danger">{{ $ciclo1Data['desist_rate_hm'] }}%</td><td class="text-danger">{{ $ciclo1Data['desist_rate_m'] }}%</td>
                            </tr>
                        @endif

                        @if(!empty($ciclo2Rows) && count($ciclo2Rows) > 0)
                            <!-- CABEÇALHO 2º CICLO -->
                            <tr class="bg-slate-100 font-extrabold text-slate-900 border-y border-slate-300">
                                <td colspan="19" class="text-start ps-3 py-1.5 bg-slate-200/90 font-black tracking-wider text-uppercase">
                                    {{ $ciclo2Data['title'] }}
                                </td>
                            </tr>
                            @foreach($ciclo2Rows as $cm)
                                <tr>
                                    <td class="text-start ps-3 fw-bold text-dark">{{ $cm['grade_level_name'] }}</td>
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

                                    <td class="fw-extrabold text-success" style="background-color: #ecfdf5;">{{ $cm['rate_hm'] }}%</td>
                                    <td class="fw-extrabold text-purple" style="background-color: #faf5ff;">{{ $cm['rate_m'] }}%</td>

                                    <td class="fw-semibold text-danger">{{ $cm['desist_rate_hm'] }}%</td>
                                    <td class="fw-semibold text-danger">{{ $cm['desist_rate_m'] }}%</td>
                                </tr>
                            @endforeach
                            <!-- SUBTOTAL 2º CICLO -->
                            <tr class="bg-ciclo">
                                <td class="text-start ps-3 fw-bold">TOTAL 2º CICLO</td>
                                <td>{{ number_format($ciclo2Data['total_hm']) }}</td><td>{{ number_format($ciclo2Data['total_m']) }}</td>
                                <td>{{ number_format($ciclo2Data['desist_hm']) }}</td><td>{{ number_format($ciclo2Data['desist_m']) }}</td>
                                <td>{{ number_format($ciclo2Data['transf_hm']) }}</td><td>{{ number_format($ciclo2Data['transf_m']) }}</td>
                                <td>{{ number_format($ciclo2Data['anulou_hm']) }}</td><td>{{ number_format($ciclo2Data['anulou_m']) }}</td>
                                <td>{{ number_format($ciclo2Data['ppf_hm']) }}</td><td>{{ number_format($ciclo2Data['ppf_m']) }}</td>
                                <td>{{ number_format($ciclo2Data['eval_hm']) }}</td><td>{{ number_format($ciclo2Data['eval_m']) }}</td>
                                <td class="text-success">{{ number_format($ciclo2Data['passed_hm']) }}</td><td class="text-success">{{ number_format($ciclo2Data['passed_m']) }}</td>
                                <td class="text-success fw-extrabold">{{ $ciclo2Data['rate_hm'] }}%</td><td class="text-purple fw-extrabold">{{ $ciclo2Data['rate_m'] }}%</td>
                                <td class="text-danger">{{ $ciclo2Data['desist_rate_hm'] }}%</td><td class="text-danger">{{ $ciclo2Data['desist_rate_m'] }}%</td>
                            </tr>
                        @endif

                        @if(empty($ciclo1Rows) && empty($ciclo2Rows) && empty($preschoolRows))
                            <tr>
                                <td colspan="19" class="text-center py-3 text-muted">Nenhuma classe registada nesta escola para o nível selecionado.</td>
                            </tr>
                        @endif
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
                            <td class="text-success fw-black">{{ $totalGeralData['rate_hm'] }}%</td><td class="text-purple fw-black">{{ $totalGeralData['rate_m'] }}%</td>
                            <td class="text-danger fw-bold">{{ $totalGeralData['desist_rate_hm'] }}%</td><td class="text-danger fw-bold">{{ $totalGeralData['desist_rate_m'] }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- OFFICIAL SIGNATURE BLOCKS (DAP & DAE MINEDH Standard) -->
        <x-official-signatures :school="$selectedSchool" :year="$year" />

    </div>
</div>

        <!-- TAB 2: Quadro 2 - Aproveitamento por Disciplina -->
        @if($selectedClass)
        <div class="tab-pane fade" id="tab-disciplinas" role="tabpanel" aria-labelledby="tab-disciplinas-tab">
            <div class="card border border-slate-200 rounded-4 shadow-sm bg-white p-4 p-md-5 print-document">
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

                <!-- OFFICIAL SIGNATURE BLOCKS (DAP & DAE MINEDH Standard) -->
                <x-official-signatures :school="$selectedSchool" :year="$year" />

            </div>
        </div>
        @endif

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

function onLevelChange(select) {
    var form = document.getElementById('pedagogicalMapFilterForm');
    form.querySelector('[name="class_id"]').value = '';
    form.submit();
}

// Tab persistence
document.addEventListener('DOMContentLoaded', function() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#pedagogicalMapTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function () {
            sessionStorage.setItem('activePedagogicalTab', this.getAttribute('data-bs-target'));
        });
    });

    // Restore active tab on page load
    var activeTab = sessionStorage.getItem('activePedagogicalTab');
    if (activeTab) {
        var tabEl = document.querySelector('#pedagogicalMapTabs button[data-bs-target="' + activeTab + '"]');
        if (tabEl) {
            new bootstrap.Tab(tabEl).show();
        }
    }
});
</script>
@endsection
