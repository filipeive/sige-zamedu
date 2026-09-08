@extends('layouts.app')

@section('title', 'Painel Territorial de Inspeção & Analytics MINEDH')

@section('content')
<!-- Leaflet CSS & JS for Interactive Mozambique Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="container-fluid py-4" style="background-color: var(--content-bg, #f8fafc);">
    
    <!-- Top Header Banner -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 text-white p-4" 
         style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-white bg-opacity-20 rounded-3 text-white">
                        <i class="fa-solid fa-map-location-dot fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="h3 fw-bold mb-1">Painel Territorial de Inspeção MINEDH</h2>
                        <p class="mb-0 text-white-50 text-sm">
                            Âmbito de Monitoria: 
                            <span class="badge bg-light text-emerald fw-semibold uppercase px-3 py-1 me-2" style="color: #047857;">
                                {{ strtoupper($territorialScope['level']) }}
                            </span>
                            | Moçambique — República de Moçambique
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm rounded-pill fw-medium text-emerald shadow-sm">
                    <i class="fa-solid fa-file-invoice me-1"></i> Central de Relatórios
                </a>
            </div>
        </div>
    </div>

    <!-- Territorial Filter Bar -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('territorial.dashboard') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">Província</label>
                    <select name="province_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todas as Províncias</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->id }}" {{ $selectedProvinceId == $prov->id ? 'selected' : '' }}>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">Distrito</label>
                    <select name="district_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todos os Distritos</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" {{ $selectedDistrictId == $dist->id ? 'selected' : '' }}>
                                {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">ZIP (Zona de Influência)</label>
                    <select name="zip_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todas as ZIPs</option>
                        @foreach($zips as $zip)
                            <option value="{{ $zip->id }}" {{ $selectedZipId == $zip->id ? 'selected' : '' }}>
                                {{ $zip->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-emerald btn-sm rounded-3 w-100 fw-semibold text-white" style="background-color: #059669;">
                        <i class="fa-solid fa-filter me-1"></i> Filtrar Âmbito
                    </button>
                    <a href="{{ route('territorial.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Executive KPI Grid -->
    <div class="row g-3 mb-4">
        <!-- Schools Count -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xs text-muted fw-semibold uppercase">Escolas Sob Inspeção</span>
                        <h3 class="fw-bold fs-2 mb-0 text-dark">{{ number_format($schoolsCount) }}</h3>
                        <span class="text-xs text-emerald fw-medium"><i class="fa-solid fa-school me-1"></i> Redes Primária e Secundária</span>
                    </div>
                    <div class="p-3 bg-emerald-subtle text-emerald rounded-4" style="background-color: #d1fae5; color: #047857;">
                        <i class="fa-solid fa-building-columns fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Students (HM & M) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xs text-muted fw-semibold uppercase">Alunos Matriculados</span>
                        <h3 class="fw-bold fs-2 mb-0 text-dark">{{ number_format($studentsHM) }} <span class="fs-6 text-muted font-normal">(HM)</span></h3>
                        <span class="text-xs text-primary fw-medium"><i class="fa-solid fa-venus me-1"></i> {{ number_format($studentsM) }} Alunas (M)</span>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-4">
                        <i class="fa-solid fa-user-graduate fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers Count -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xs text-muted fw-semibold uppercase">Corpo Docente</span>
                        <h3 class="fw-bold fs-2 mb-0 text-dark">{{ number_format($teachersCount) }}</h3>
                        <span class="text-xs text-info fw-medium"><i class="fa-solid fa-chalkboard-user me-1"></i> Professores Alocados</span>
                    </div>
                    <div class="p-3 bg-info-subtle text-info rounded-4">
                        <i class="fa-solid fa-chalkboard-teacher fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedagogical Approval Rate (HM & M) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xs text-muted fw-semibold uppercase">Taxa de Aproveitamento (HM)</span>
                        <h3 class="fw-bold fs-2 mb-0 {{ $approvalRateHM >= 75 ? 'text-success' : ($approvalRateHM >= 50 ? 'text-warning' : 'text-danger') }}">
                            {{ $approvalRateHM }}%
                        </h3>
                        <span class="text-xs text-muted fw-medium">Mulheres (M): <strong class="text-dark">{{ $approvalRateM }}%</strong></span>
                    </div>
                    <div class="p-3 {{ $approvalRateHM >= 75 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-4">
                        <i class="fa-solid fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INTERACTIVE MAP OF MOZAMBIQUE SCHOOLS -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-earth-africa text-emerald" style="color: #059669;"></i> Mapa Geográfico e Localização das Escolas (Moçambique)
            </h5>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                <i class="fa-solid fa-satellite-dish me-1"></i> {{ count($schoolsDetailed) }} Marcadores Georreferenciados
            </span>
        </div>
        <div class="card-body p-0">
            <div id="mozambique-schools-map" style="height: 440px; width: 100%; z-index: 1;" class="rounded-bottom-4"></div>
        </div>
    </div>

    <!-- MINEDH Pedagogical Evaluation Summary Card -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-award text-emerald" style="color: #059669;"></i> Quadro Geral de Aproveitamento Pedagógico (MINEDH Lei 18/2018)
            </h5>
            <span class="badge bg-emerald-subtle text-emerald fw-semibold px-3 py-1" style="background-color: #d1fae5; color: #047857;">Escala 0.0 - 20.0 Valores</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-xs text-muted uppercase">
                        <tr>
                            <th class="ps-4">Indicador de Rendimento</th>
                            <th class="text-center">Total (HM)</th>
                            <th class="text-center">Mulheres (M)</th>
                            <th class="text-center">Percentual (%)</th>
                            <th class="pe-4 text-end">Estado Regulamentar</th>
                        </tr>
                    </thead>
                    <tbody class="fs-6">
                        <tr>
                            <td class="ps-4 fw-semibold">Alunos Avaliados</td>
                            <td class="text-center fw-bold">{{ number_format($evaluatedHM) }}</td>
                            <td class="text-center text-primary fw-bold">{{ number_format($evaluatedM) }}</td>
                            <td class="text-center">100.0%</td>
                            <td class="pe-4 text-end"><span class="badge bg-secondary">Base Geral</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-success fw-semibold">Notas Positivas (>= 9.5 valores)</td>
                            <td class="text-center text-success fw-bold">{{ number_format($positiveHM) }}</td>
                            <td class="text-center text-success fw-bold">{{ number_format($positiveM) }}</td>
                            <td class="text-center fw-bold text-success">{{ $approvalRateHM }}%</td>
                            <td class="pe-4 text-end"><span class="badge bg-success-subtle text-success fw-semibold">Aprovados</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-danger fw-semibold">Notas Negativas (< 9.5 valores)</td>
                            <td class="text-center text-danger fw-bold">{{ number_format($negativeHM) }}</td>
                            <td class="text-center text-danger fw-bold">{{ number_format($negativeM) }}</td>
                            <td class="text-center fw-bold text-danger">{{ $evaluatedHM > 0 ? round(($negativeHM / $evaluatedHM) * 100, 1) : 0 }}%</td>
                            <td class="pe-4 text-end"><span class="badge bg-danger-subtle text-danger fw-semibold">Abaixo do Limiar</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TABBED DETAILED SECTIONS FOR ESCOLAS, ZIPS, DISTRITOS, PROVÍNCIAS -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <ul class="nav nav-pills card-header-pills gap-2" id="territorialTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link active rounded-pill px-4 fw-bold" id="schools-tab" data-bs-toggle="tab" data-bs-target="#schools-pane" type="button" role="tab">
                        <i class="fa-solid fa-school me-2"></i> Escolas ({{ count($schoolsDetailed) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link rounded-pill px-4 fw-bold" id="zips-tab" data-bs-toggle="tab" data-bs-target="#zips-pane" type="button" role="tab">
                        <i class="fa-solid fa-network-wired me-2"></i> ZIPs / Zonas Pedagógicas ({{ count($zipsDetailed) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link rounded-pill px-4 fw-bold" id="districts-tab" data-bs-toggle="tab" data-bs-target="#districts-pane" type="button" role="tab">
                        <i class="fa-solid fa-city me-2"></i> Distritos / SDEJT ({{ count($districtsDetailed) }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-item nav-link rounded-pill px-4 fw-bold" id="provinces-tab" data-bs-toggle="tab" data-bs-target="#provinces-pane" type="button" role="tab">
                        <i class="fa-solid fa-map me-2"></i> Províncias / DPEDH ({{ count($provincesDetailed) }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="territorialTabsContent">
                
                <!-- PANE 1: ESCOLAS DETALHADAS -->
                <div class="tab-pane fade show active" id="schools-pane" role="tabpanel" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-xs text-muted uppercase">
                                <tr>
                                    <th class="ps-4">Escola / Código</th>
                                    <th>Diretor da Escola</th>
                                    <th>Contactos</th>
                                    <th>Localização (ZIP / Distrito / Província)</th>
                                    <th class="text-center">Alunos (HM / M)</th>
                                    <th class="text-center">Aproveitamento</th>
                                    <th class="pe-4 text-end">Acções</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6">
                                @forelse($schoolsDetailed as $sc)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="p-2 bg-emerald-subtle rounded-3 text-emerald" style="background-color: #d1fae5; color: #047857;">
                                                    <i class="fa-solid fa-school"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-dark d-block mb-0">{{ $sc['name'] }}</strong>
                                                    <span class="text-xs text-muted font-mono">{{ $sc['code'] }} &bull; {{ $sc['type'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $sc['principal_name'] }}</div>
                                            <span class="text-xs text-muted"><i class="fa-solid fa-user-tie me-1"></i> Diretor Geral</span>
                                        </td>
                                        <td class="text-sm">
                                            <div><i class="fa-solid fa-phone text-muted me-1"></i> {{ $sc['phone'] }}</div>
                                            <div class="text-xs text-muted"><i class="fa-solid fa-envelope me-1"></i> {{ $sc['email'] }}</div>
                                        </td>
                                        <td class="text-sm">
                                            <span class="badge bg-light text-dark me-1"><i class="fa-solid fa-location-dot text-emerald me-1" style="color: #059669;"></i> {{ $sc['zip_name'] }}</span>
                                            <div class="text-xs text-muted">{{ $sc['district_name'] }} &bull; {{ $sc['province_name'] }}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark">{{ number_format($sc['students_hm']) }} <span class="text-xs text-muted">(HM)</span></div>
                                            <div class="text-xs text-primary">{{ number_format($sc['students_m']) }} (M)</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $sc['approval_rate'] >= 75 ? 'bg-success' : ($sc['approval_rate'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1">
                                                {{ $sc['approval_rate'] }}%
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-1">
                                                @if(auth()->user()->hasRole('super_admin'))
                                                    <form method="POST" action="{{ route('super-admin.impersonate', $sc['id']) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2.5" title="Entrar no Modo Suporte Técnico">
                                                            <i class="fa-solid fa-headset me-1"></i> Suporte
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('territorial.school', $sc['id']) }}" class="btn btn-sm btn-outline-emerald rounded-pill px-2.5">
                                                    Inspeção <i class="fa-solid fa-chevron-right ms-1"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Nenhuma escola cadastrada no âmbito territorial.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PANE 2: ZIPS DETALHADAS -->
                <div class="tab-pane fade" id="zips-pane" role="tabpanel" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-xs text-muted uppercase">
                                <tr>
                                    <th class="ps-4">Zona de Influência Pedagógica (ZIP)</th>
                                    <th>Escola Sede ZIP</th>
                                    <th>Distrito & Província</th>
                                    <th class="text-center">Total Escolas</th>
                                    <th class="text-center">Alunos (HM / M)</th>
                                    <th class="text-center">Aproveitamento Média</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6">
                                @forelse($zipsDetailed as $zp)
                                    <tr>
                                        <td class="ps-4">
                                            <strong class="text-dark">{{ $zp['name'] }}</strong>
                                            <span class="text-xs text-muted font-mono d-block">{{ $zp['code'] }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-dark"><i class="fa-solid fa-building me-1 text-emerald" style="color: #059669;"></i> {{ $zp['headquarters'] }}</span>
                                        </td>
                                        <td class="text-sm">
                                            {{ $zp['district_name'] }} &bull; {{ $zp['province_name'] }}
                                        </td>
                                        <td class="text-center font-mono fw-bold">{{ $zp['schools_count'] }}</td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark">{{ number_format($zp['students_hm']) }} (HM)</div>
                                            <div class="text-xs text-primary">{{ number_format($zp['students_m']) }} (M)</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $zp['approval_rate'] >= 75 ? 'bg-success' : ($zp['approval_rate'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1">
                                                {{ $zp['approval_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Nenhuma ZIP registada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PANE 3: DISTRITOS (SDEJT) DETALHADOS -->
                <div class="tab-pane fade" id="districts-pane" role="tabpanel" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-xs text-muted uppercase">
                                <tr>
                                    <th class="ps-4">Distrito / SDEJT</th>
                                    <th>Província</th>
                                    <th class="text-center">Nº de ZIPs</th>
                                    <th class="text-center">Total Escolas</th>
                                    <th class="text-center">Alunos (HM / M)</th>
                                    <th class="text-center">Aproveitamento Distrital</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6">
                                @forelse($districtsDetailed as $dt)
                                    <tr>
                                        <td class="ps-4">
                                            <strong class="text-dark">{{ $dt['name'] }}</strong>
                                            <span class="text-xs text-muted font-mono d-block">{{ $dt['code'] }}</span>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $dt['province_name'] }}</span></td>
                                        <td class="text-center font-mono fw-bold text-info">{{ $dt['zips_count'] }}</td>
                                        <td class="text-center font-mono fw-bold text-dark">{{ $dt['schools_count'] }}</td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark">{{ number_format($dt['students_hm']) }} (HM)</div>
                                            <div class="text-xs text-primary">{{ number_format($dt['students_m']) }} (M)</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $dt['approval_rate'] >= 75 ? 'bg-success' : ($dt['approval_rate'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1">
                                                {{ $dt['approval_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Nenhum Distrito registado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- PANE 4: PROVÍNCIAS (DPEDH) DETALHADAS -->
                <div class="tab-pane fade" id="provinces-pane" role="tabpanel" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-xs text-muted uppercase">
                                <tr>
                                    <th class="ps-4">Província / DPEDH</th>
                                    <th>Código</th>
                                    <th class="text-center">Nº de Distritos</th>
                                    <th class="text-center">Total Escolas</th>
                                    <th class="text-center">Alunos (HM / M)</th>
                                    <th class="text-center">Aproveitamento Provincial</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6">
                                @forelse($provincesDetailed as $pv)
                                    <tr>
                                        <td class="ps-4">
                                            <strong class="text-dark fs-6">{{ $pv['name'] }}</strong>
                                        </td>
                                        <td><span class="badge bg-emerald-subtle text-emerald font-mono">{{ $pv['code'] }}</span></td>
                                        <td class="text-center font-mono fw-bold text-primary">{{ $pv['districts_count'] }}</td>
                                        <td class="text-center font-mono fw-bold text-dark">{{ $pv['schools_count'] }}</td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark">{{ number_format($pv['students_hm']) }} (HM)</div>
                                            <div class="text-xs text-primary">{{ number_format($pv['students_m']) }} (M)</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $pv['approval_rate'] >= 75 ? 'bg-success' : ($pv['approval_rate'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1">
                                                {{ $pv['approval_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Nenhuma Província registada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Leaflet Map Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var map = L.map('mozambique-schools-map').setView([-18.6656, 35.5295], 6);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | MINEDH Moçambique'
    }).addTo(map);

    var schools = @json($schoolsDetailed);
    var bounds = [];
    var isSuperAdmin = {{ auth()->user()->hasRole('super_admin') ? 'true' : 'false' }};
    var csrfToken = '{{ csrf_token() }}';

    schools.forEach(function (school) {
        if (school.lat && school.lng) {
            bounds.push([school.lat, school.lng]);

            var marker = L.marker([school.lat, school.lng]).addTo(map);

            var supportForm = '';
            if (isSuperAdmin) {
                supportForm = `
                    <form method="POST" action="/super-admin/impersonate/${school.id}" style="margin-top: 8px;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" style="width: 100%; background: #f59e0b; color: #fff; border: none; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 11px; cursor: pointer;">
                            🎧 Entrar no Modo Suporte
                        </button>
                    </form>
                `;
            }

            var popupContent = `
                <div style="min-width: 220px; font-family: system-ui, sans-serif;">
                    <div style="font-size: 10px; font-weight: bold; color: #059669; text-transform: uppercase;">MINEDH &bull; ${school.type}</div>
                    <h4 style="margin: 2px 0 6px 0; font-size: 14px; font-weight: 800; color: #1e293b;">${school.name}</h4>
                    <div style="font-size: 11px; color: #475569; margin-bottom: 4px;">
                        <strong>Diretor:</strong> ${school.principal_name}<br>
                        <strong>Contacto:</strong> ${school.phone}<br>
                        <strong>Email:</strong> ${school.email}<br>
                        <strong>Local:</strong> ${school.zip_name}, ${school.district_name}
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 11px;">
                        <span><strong>${school.students_hm}</strong> Alunos (HM)</span>
                        <span style="background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 10px; font-weight: bold;">
                            ${school.approval_rate}% Aprov.
                        </span>
                    </div>
                    <div style="margin-top: 8px; display: flex; gap: 4px;">
                        <a href="/territorial/school/${school.id}" style="flex: 1; text-align: center; background: #059669; color: white; text-decoration: none; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 11px;">
                            Ver Inspeção &rarr;
                        </a>
                    </div>
                    ${supportForm}
                </div>
            `;

            marker.bindPopup(popupContent);
        }
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
});
</script>
@endsection
