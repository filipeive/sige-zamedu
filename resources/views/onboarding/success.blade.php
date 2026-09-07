@extends('layouts.app')

@section('title', 'Onboarding Concluído com Sucesso — ' . $school->name)

@section('content')
<div class="container py-5" style="max-width: 800px;">
    <div class="card border-0 rounded-4 shadow-sm text-center p-5">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center w-20 h-20 bg-success-subtle text-success rounded-circle p-4">
                <i class="fa-solid fa-circle-check fa-4x"></i>
            </div>
        </div>

        <h2 class="fw-bold text-dark mb-2">Escola Integrada com Sucesso!</h2>
        <p class="text-muted fs-5 mb-4">A instituição <strong class="text-emerald">{{ $school->name }}</strong> já se encontra ativa no ecossistema SIGE / ZAMEDU.</p>

        <!-- Credentials Card -->
        <div class="card border-0 bg-light rounded-4 text-start p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-key text-emerald me-2"></i> Dados de Acesso Inicial do Administrador:</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <span class="text-xs text-muted fw-semibold uppercase d-block">Código Único da Escola</span>
                    <span class="fs-5 fw-bold text-dark font-mono">{{ $school->code }}</span>
                </div>
                <div class="col-md-6">
                    <span class="text-xs text-muted fw-semibold uppercase d-block">E-mail do Administrador</span>
                    <span class="fs-5 fw-bold text-dark">{{ $adminUser->email ?? 'N/A' }}</span>
                </div>
                <div class="col-md-6">
                    <span class="text-xs text-muted fw-semibold uppercase d-block">Enquadramento Geográfico</span>
                    <span class="text-dark fw-medium">{{ $school->province->name ?? '' }} $\rightarrow$ {{ $school->district->name ?? '' }} $\rightarrow$ {{ $school->zip->name ?? '' }}</span>
                </div>
                <div class="col-md-6">
                    <span class="text-xs text-muted fw-semibold uppercase d-block">Estado da Licença</span>
                    <span class="badge bg-success-subtle text-success fw-bold">Ativa (2000 Alunos)</span>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('territorial.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-map-location-dot me-1"></i> Voltar ao Painel Territorial
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-emerald text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #059669;">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Ir para o Dashboard da Escola
            </a>
        </div>
    </div>
</div>
@endsection
