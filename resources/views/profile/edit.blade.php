@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil de Utilizador')
@section('page-title-icon', 'fas fa-user-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline text-slate-500">Início</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Meu Perfil</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white p-6 shadow-md border border-slate-700/50">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 border-2 border-emerald-400/40 text-emerald-300 flex items-center justify-center text-2xl font-black shadow-inner">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 uppercase tracking-wider">
                            {{ auth()->user()->role_display ?? 'Utilizador' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-white/10 text-white border border-white/15">
                            {{ auth()->user()->status === 'active' ? 'Conta Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight text-white">{{ auth()->user()->name }}</h1>
                    <p class="text-xs text-slate-300 mt-0.5">{{ auth()->user()->email }} &bull; {{ auth()->user()->school->name ?? 'Rede Escolar' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Coluna Principal (8 cols): Informações e Senha --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Informações do Perfil --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-edit text-emerald-600"></i> Dados Pessoais &amp; Contacto
                    </h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Alterar Senha --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-lock text-amber-600"></i> Segurança &amp; Palavra-Passe
                    </h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>

        {{-- Coluna Lateral (4 cols): Detalhes da Conta --}}
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5 space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i class="fas fa-shield-alt text-emerald-600"></i> Detalhes da Conta
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Endereço de E-mail</span>
                        <span class="font-bold text-slate-800 font-mono">{{ auth()->user()->email }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Perfil de Acesso</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            {{ auth()->user()->role_display ?? 'Utilizador' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Instituição / Escola</span>
                        <span class="font-bold text-slate-800">{{ auth()->user()->school->name ?? 'Rede Central' }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">Última Sessão</span>
                        <span class="font-medium text-slate-600">
                            {{ auth()->user()->last_login ? auth()->user()->last_login->diffForHumans() : 'Sessão atual' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
