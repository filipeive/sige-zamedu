@extends('layouts.app')

@section('title', 'Inspeção Escolar — ' . $school->name)

@section('content')
<div class="container-fluid py-4" style="background-color: var(--content-bg, #f8fafc);">

    <!-- Breadcrumb Nav -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('territorial.dashboard') }}" class="text-emerald fw-semibold">Painel Territorial</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $school->name }}</li>
        </ol>
    </nav>

    <!-- School Header -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-emerald-subtle text-emerald rounded-4">
                        <i class="fa-solid fa-school fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="h4 fw-bold mb-1 text-dark">{{ $school->name }}</h2>
                        <p class="mb-0 text-muted text-sm">
                            <i class="fa-solid fa-location-dot me-1"></i>
                            Província: <strong>{{ $school->province->name ?? 'N/A' }}</strong> |
                            Distrito: <strong>{{ $school->district->name ?? 'N/A' }}</strong> |
                            ZIP: <strong>{{ $school->zip->name ?? 'N/A' }}</strong>
                        </p>
                    </div>
                </div>
                <div>
                    <span class="badge bg-success-subtle text-success px-3 py-2 fs-6 rounded-pill">
                        <i class="fa-solid fa-circle-check me-1"></i> Escola Ativa sob Inspeção
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-xs text-muted fw-semibold uppercase">Total de Alunos (HM)</span>
                <h3 class="fw-bold fs-3 mb-0 text-dark">{{ number_format($studentsHM) }}</h3>
                <span class="text-xs text-primary">Mulheres (M): <strong>{{ number_format($studentsM) }}</strong></span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-xs text-muted fw-semibold uppercase">Professores Alocados</span>
                <h3 class="fw-bold fs-3 mb-0 text-dark">{{ number_format($teachersCount) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-xs text-muted fw-semibold uppercase">Turmas em Funcionamento</span>
                <h3 class="fw-bold fs-3 mb-0 text-dark">{{ number_format($classesCount) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-xs text-muted fw-semibold uppercase">Taxa de Aprovação (HM)</span>
                <h3 class="fw-bold fs-3 mb-0 {{ $approvalRateHM >= 75 ? 'text-success' : 'text-warning' }}">{{ $approvalRateHM }}%</h3>
                <span class="text-xs text-muted">Mulheres (M): <strong>{{ $approvalRateM }}%</strong></span>
            </div>
        </div>
    </div>

    <!-- Detailed Evaluation Table -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-white py-3 border-0 rounded-top-4">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chart-pie text-emerald me-2"></i> Rendimento Escolar Regulamentar MINEDH</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light text-xs text-muted uppercase">
                    <tr>
                        <th class="ps-4">Indicador</th>
                        <th class="text-center">Total (HM)</th>
                        <th class="text-center">Mulheres (M)</th>
                        <th class="pe-4 text-end">Percentual (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 fw-semibold">Alunos Avaliados</td>
                        <td class="text-center fw-bold">{{ number_format($evaluatedHM) }}</td>
                        <td class="text-center text-primary fw-bold">{{ number_format($evaluatedM) }}</td>
                        <td class="pe-4 text-end">100.0%</td>
                    </tr>
                    <tr>
                        <td class="ps-4 text-success fw-semibold">Notas Positivas ($\ge 9.5$)</td>
                        <td class="text-center text-success fw-bold">{{ number_format($positiveHM) }}</td>
                        <td class="text-center text-success fw-bold">{{ number_format($positiveM) }}</td>
                        <td class="pe-4 text-end fw-bold text-success">{{ $approvalRateHM }}%</td>
                    </tr>
                    <tr>
                        <td class="ps-4 text-danger fw-semibold">Notas Negativas ($< 9.5$)</td>
                        <td class="text-center text-danger fw-bold">{{ number_format($evaluatedHM - $positiveHM) }}</td>
                        <td class="text-center text-danger fw-bold">{{ number_format($evaluatedM - $positiveM) }}</td>
                        <td class="pe-4 text-end fw-bold text-danger">{{ $evaluatedHM > 0 ? round((($evaluatedHM - $positiveHM) / $evaluatedHM) * 100, 1) : 0 }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
