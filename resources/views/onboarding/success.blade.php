@extends('layouts.app')

@section('title', 'Onboarding - Success')
@section('page-title', 'Onboarding - Success')
@section('page-title-icon', 'fas fa-folder-open')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Onboarding</li>
@endsection

@section('title', 'Onboarding Concluído com Sucesso — ' . $school->name)

@section('content')
<div class="w-full max-w-3xl mx-auto px-4 py-12">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 md:p-12 text-center">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full p-4 shadow-inner">
                <i class="fa-solid fa-circle-check text-5xl"></i>
            </div>
        </div>

        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Escola Integrada com Sucesso!</h2>
        <p class="text-base text-slate-600 mb-8 max-w-xl mx-auto">
            A instituição <strong class="text-emerald-700 font-bold">{{ $school->name }}</strong> já se encontra totalmente ativa e configurada no ecossistema SIGE / ZAMEDU.
        </p>

        <!-- Credentials Card -->
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 text-left mb-8 shadow-sm">
            <h6 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-key text-emerald-600"></i> Dados de Acesso Inicial do Administrador:
            </h6>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-3.5 rounded-xl border border-slate-200">
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Código Único da Escola</span>
                    <span class="text-lg font-bold text-slate-900 font-mono tracking-wide">{{ $school->code }}</span>
                </div>
                <div class="bg-white p-3.5 rounded-xl border border-slate-200">
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">E-mail do Administrador</span>
                    <span class="text-base font-bold text-slate-900 truncate block">{{ $adminUser->email ?? 'N/A' }}</span>
                </div>
                <div class="bg-white p-3.5 rounded-xl border border-slate-200">
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Enquadramento Geográfico</span>
                    <span class="text-sm font-semibold text-slate-800">
                        {{ $school->province->name ?? '' }} &rarr; {{ $school->district->name ?? '' }} &rarr; {{ $school->zip->name ?? '' }}
                    </span>
                </div>
                <div class="bg-white p-3.5 rounded-xl border border-slate-200">
                    <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Estado da Licença</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">
                        <i class="fa-solid fa-certificate"></i> Ativa (2000 Alunos)
                    </span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('territorial.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm shadow-sm transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-map-location-dot"></i> Voltar ao Painel Territorial
            </a>
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition inline-flex items-center justify-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Ir para o Dashboard da Escola
            </a>
        </div>
    </div>
</div>
@endsection

