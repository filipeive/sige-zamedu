@extends('layouts.app')

@section('title', 'Users - Show')
@section('page-title', 'Users - Show')
@section('page-title-icon', 'fas fa-users-cog')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Users</li>
@endsection

@section('title', 'Detalhes do Utilizador — ' . $user->name)

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 py-6 space-y-6">

    <!-- Breadcrumb Nav -->
    <div class="flex items-center justify-between flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="flex items-center space-x-2 text-xs font-semibold">
                <li><a href="{{ route('admin.users.index') }}" class="text-emerald-600 hover:text-emerald-700">Gestão de Utilizadores</a></li>
                <li class="text-slate-400">/</li>
                <li class="text-slate-700 font-bold" aria-current="page">{{ $user->name }}</li>
            </ol>
        </nav>

        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-sm transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- Header User Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="relative">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-500 shadow-md">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 text-white flex items-center justify-center text-3xl font-black shadow-md border-2 border-emerald-500">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}" title="{{ $user->status === 'active' ? 'Ativo' : 'Inativo' }}"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h1 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        @foreach($user->roles as $role)
                            <span class="px-3 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                                {{ $role->name }}
                            </span>
                        @endforeach
                        @if($user->status === 'active')
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                                <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Ativo
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 text-xs font-semibold border border-rose-200">
                                <i class="fa-solid fa-ban text-rose-500 mr-1"></i> Inativo
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 flex items-center gap-3 flex-wrap">
                        <span><i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $user->email }}</span>
                        @if($user->phone) <span>| <i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $user->phone }}</span> @endif
                        <span>| <i class="fa-solid fa-calendar-check text-slate-400 mr-1"></i> Registado em {{ $user->created_at->format('d/m/Y') }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Editar Utilizador
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Rows: Identificação de Escola & Preferências de Cookies -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        <!-- 1. IDENTIFICAÇÃO DE ESCOLA & TENANCY -->
        <div class="md:col-span-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-school text-emerald-600"></i> Identificação Institucional & Escola Alocada
                </h3>

                @if($user->school)
                    <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100 mb-4 flex items-start gap-3">
                        <div class="p-2.5 bg-emerald-600 text-white rounded-xl">
                            <i class="fa-solid fa-building-columns text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900">{{ $user->school->name }}</h4>
                            <p class="text-xs text-slate-600">Código MINEDH: <strong class="font-mono text-slate-800">{{ $user->school->code }}</strong> | Tipo: <strong>{{ $user->school->type_label ?? $user->school->type }}</strong></p>
                        </div>
                    </div>

                    <ul class="divide-y divide-slate-100 text-xs space-y-0">
                        <li class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-500 font-bold uppercase">Província</span>
                            <span class="font-semibold text-slate-900">{{ $user->school->province->name ?? $user->province->name ?? 'N/A' }}</span>
                        </li>
                        <li class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-500 font-bold uppercase">Distrito (SDEJT)</span>
                            <span class="font-semibold text-slate-900">{{ $user->school->district->name ?? $user->district->name ?? 'N/A' }}</span>
                        </li>
                        <li class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-500 font-bold uppercase">Zona Pedagógica (ZIP)</span>
                            <span class="font-semibold text-emerald-700">{{ $user->school->zip->name ?? $user->zip->name ?? 'Sem ZIP' }}</span>
                        </li>
                        <li class="py-2.5 flex justify-between items-center">
                            <span class="text-slate-500 font-bold uppercase">Directoria da Escola</span>
                            <span class="text-slate-700 font-medium">{{ $user->school->principal_name ?? 'Não especificada' }}</span>
                        </li>
                    </ul>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-xs">
                        <i class="fa-solid fa-circle-info text-sky-600 mr-1"></i>
                        @if($user->hasRole('super_admin'))
                            <strong>Super Administrador Central</strong>: Acesso global a todas as escolas e províncias do ecossistema.
                        @elseif($user->province_id || $user->district_id || $user->zip_id)
                            <strong>Utilizador Territorial</strong>:
                            Província: <strong>{{ $user->province->name ?? 'N/A' }}</strong> |
                            Distrito: <strong>{{ $user->district->name ?? 'N/A' }}</strong> |
                            ZIP: <strong>{{ $user->zip->name ?? 'N/A' }}</strong>
                        @else
                            Utilizador sem escola associada (Nível Global/Super).
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. SESSÃO & INSPECÇÃO DE COOKIES / PRIVACIDADE -->
        <div class="md:col-span-6 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-cookie-bite text-amber-600"></i> Sessão Ativa, Cookies & Privacidade
                    </span>
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-mono font-bold">Cookies RGPD</span>
                </h3>

                <ul class="divide-y divide-slate-100 text-xs space-y-0 mb-4">
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Consentimento de Cookies</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs">
                            <i class="fa-solid fa-shield-check mr-1"></i> Aceite (Essencial + Analytics)
                        </span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Último Acesso Registado</span>
                        <span class="font-mono text-slate-900 font-semibold">{{ $user->last_login ? $user->last_login->format('d/m/Y H:i:s') : 'N/A' }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Cookie de Sessão Ativa</span>
                        <span class="font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded">zamedu_session_token</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Proteção CSRF Cookie</span>
                        <span class="font-mono text-slate-600 bg-slate-100 px-2 py-0.5 rounded">XSRF-TOKEN (Lax)</span>
                    </li>
                </ul>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-500 space-y-1">
                    <div class="font-bold text-slate-700"><i class="fa-solid fa-laptop text-slate-400 mr-1"></i> Informações do Navegador / IP:</div>
                    <div class="truncate font-mono text-[10px] text-slate-600">{{ request()->userAgent() }}</div>
                </div>
            </div>
        </div>

    </div>

    <!-- RASTREABILIDADE & AUDITORIA DE ATIVIDADES -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i> Histórico de Auditoria & Rastreabilidade do Utilizador
            </h3>
            <span class="text-xs font-semibold text-slate-500">Últimas 10 Operações</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Data / Hora</th>
                        <th class="py-3 px-4">Tipo de Ação</th>
                        <th class="py-3 px-4">Descrição da Operação</th>
                        <th class="py-3 px-6 text-right">Endereço IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-6 font-mono text-slate-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase
                                    {{ $log->event === 'created' ? 'bg-emerald-100 text-emerald-800' : ($log->event === 'updated' ? 'bg-amber-100 text-amber-800' : ($log->event === 'deleted' ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800')) }}">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $log->description }}</td>
                            <td class="py-3 px-6 text-right font-mono text-slate-500">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-slate-400 font-medium">
                                Nenhum registo de auditoria recente para este utilizador.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection