@extends('layouts.app')

@section('title', 'Painel Territorial de Inspeção & Analytics MINEDH')

@section('content')
<div class="container-fluid py-4" style="background-color: var(--content-bg, #f8fafc);">
    
    <!-- Top Header Banner -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 bg-gradient-emerald text-white p-4" 
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
                            <span class="badge bg-light text-emerald fw-semibold uppercase px-3 py-1 me-2">
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
                    <div class="p-3 bg-emerald-subtle text-emerald rounded-4">
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

    <!-- MINEDH Pedagogical Evaluation Card (Lei 18/2018 Standard Table) -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-award text-emerald"></i> Quadro Geral de Aproveitamento Pedagógico (Regulamentar MINEDH)
            </h5>
            <span class="badge bg-emerald-subtle text-emerald fw-semibold px-3 py-1">Escala 0.0 - 20.0 Valores</span>
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
                            <td class="ps-4 text-success fw-semibold">Notas Positivas ($\ge 9.5$ / $10$)</td>
                            <td class="text-center text-success fw-bold">{{ number_format($positiveHM) }}</td>
                            <td class="text-center text-success fw-bold">{{ number_format($positiveM) }}</td>
                            <td class="text-center fw-bold text-success">{{ $approvalRateHM }}%</td>
                            <td class="pe-4 text-end"><span class="badge bg-success-subtle text-success fw-semibold">Aprovados</span></td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-danger fw-semibold">Notas Negativas ($< 9.5$)</td>
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

    <!-- Comparative Table of Sub-Entities (Districts / ZIPs / Schools) -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 border-0 rounded-top-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald"></i> Desagregação e Comparativo por Unidade Territorial
            </h5>
            <span class="text-xs text-muted">Clique na unidade para realizar Drill-Down</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-xs text-muted uppercase">
                        <tr>
                            <th class="ps-4">Unidade Territorial</th>
                            <th>Jurisdição / Pertence A</th>
                            <th class="text-center">Escolas</th>
                            <th class="text-center">Alunos (HM)</th>
                            <th class="text-center">Alunas (M)</th>
                            <th class="text-center">Avaliados</th>
                            <th class="text-center">Aproveitamento (HM)</th>
                            <th class="pe-4 text-end">Acção</th>
                        </tr>
                    </thead>
                    <tbody class="fs-6">
                        @forelse($subEntityBreakdown as $entity)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="fa-solid {{ $entity['type'] === 'school' ? 'fa-school text-emerald' : ($entity['type'] === 'zip' ? 'fa-network-wired text-info' : 'fa-city text-primary') }} me-2"></i>
                                    {{ $entity['name'] }}
                                </td>
                                <td class="text-muted text-sm">{{ $entity['parent_name'] ?: 'Âmbito Territorial' }}</td>
                                <td class="text-center font-mono">{{ $entity['schools_count'] }}</td>
                                <td class="text-center fw-medium">{{ number_format($entity['students_hm']) }}</td>
                                <td class="text-center text-primary">{{ number_format($entity['students_m']) }}</td>
                                <td class="text-center">{{ number_format($entity['evaluated_hm']) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $entity['approval_rate'] >= 75 ? 'bg-success' : ($entity['approval_rate'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3 py-1">
                                        {{ $entity['approval_rate'] }}%
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    @if($entity['type'] === 'school')
                                        <a href="{{ route('territorial.school', $entity['id']) }}" class="btn btn-sm btn-outline-emerald rounded-pill">
                                            Inspeção <i class="fa-solid fa-chevron-right ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-xs text-muted">Agregado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Nenhum registo territorial encontrado para o filtro selecionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
