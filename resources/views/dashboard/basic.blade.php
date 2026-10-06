@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Bem-vindo')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Bem</li>
@endsection

@section('content')
    <div class="mx-auto max-w-2xl">
        <article class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                <i class="fas fa-graduation-cap text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">{{ $stats['welcome_message'] }}</h2>
            <p class="mt-2 text-sm text-slate-600">Sistema de Gestao Escolar</p>
            <p class="mt-1 text-xs text-slate-500">Ultimo acesso: {{ $stats['last_login'] }}</p>

            <div class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-900">
                Seu perfil ainda nao foi completamente configurado.
            </div>

            <a href="{{ route('profile.edit') }}"
                class="mt-6 inline-flex items-center gap-2 rounded-lg bg-sky-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-800">
                <i class="fas fa-user-cog"></i>
                Completar Perfil
            </a>
        </article>
    </div>
@endsection
