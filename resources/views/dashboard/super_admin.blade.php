@extends('layouts.app')

@section('title', 'Painel do Desenvolvedor & Gestão de Licenças — SaaS')
@section('page-title', 'Consola do Provedor SaaS')
@section('page-title-icon', 'fas fa-server')

@section('breadcrumbs')
    
    <li class="breadcrumb-item active text-slate-600 font-medium">Gestão de Licenças & Provedor SaaS</li>
@endsection

@section('content')
<div class="space-y-8 p-4 sm:p-6 lg:p-8">

    <!-- HERO SAAS PROVIDER & LICENSE GOVERNANCE -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 p-6 sm:p-8 text-white shadow-2xl border-b-4 border-emerald-500">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-emerald-950/80 p-3 shadow-xl border-2 border-emerald-500/60 flex items-center justify-center shrink-0">
                    <i class="fas fa-server text-3xl text-emerald-400"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-[11px] font-black uppercase tracking-wider mb-1 backdrop-blur-sm">
                        <i class="fas fa-code text-emerald-400"></i> ZAMEDU SAAS · PROVEDOR DO PRODUTO & GESTÃO DE LICENÇAS
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black font-heading text-white tracking-tight">Painel do Desenvolvedor & Gestor da Plataforma</h1>
                    <p class="text-xs sm:text-sm text-slate-300 font-medium mt-1">
                        <strong>Filipe dos Santos</strong> · Monitoramento técnico de clientes parceiros (Escola dos Visionários), emissão de licenças de software e suporte do sistema.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('onboarding.school') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs font-extrabold hover:from-emerald-400 hover:to-emerald-500 transition shadow-lg flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Registar Nova Escola Parceira
                </a>
                <a href="{{ route('admin.license') }}" class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 text-white text-xs font-bold transition border border-white/20 backdrop-blur-sm flex items-center gap-2">
                    <i class="fas fa-key text-amber-300"></i> Gestão de Licenças
                </a>
            </div>
        </div>
    </div>

    <!-- METRICS KPSI GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Metric 1: Total Escolas -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Escolas</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold">
                    <i class="fas fa-school"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">{{ number_format($totalSchools) }}</div>
            <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-2">
                <span class="text-emerald-600 font-bold">{{ $publicSchoolsCount }} Públicas</span> · <span>{{ $privateSchoolsCount }} Privadas</span>
            </div>
        </div>

        <!-- Metric 2: Total Alunos -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alunos Registados</span>
                <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">{{ number_format($totalStudents) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Total acumulado em todas as escolas</div>
        </div>

        <!-- Metric 3: Total Professores -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Corpo Docente</span>
                <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">{{ number_format($totalTeachers) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Professores ativos no sistema</div>
        </div>

        <!-- Metric 4: Cobertura Territorial -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Território SNE</span>
                <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">{{ $totalProvinces }} Prov.</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $totalDistricts }} Distritos · {{ $totalZips }} ZIPs</div>
        </div>

        <!-- Metric 5: Licenças Ativas -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Licenciamento</span>
                <div class="w-9 h-9 rounded-xl bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold">
                    <i class="fas fa-certificate"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-heading">{{ $activeLicenses }} Ativas</div>
            <div class="text-[11px] text-slate-500 mt-1 font-medium">{{ $totalUsers }} Utilizadores no Portal</div>
        </div>
    </div>

    <!-- MAIN MULTI-TENANT SCHOOLS CONTROL TABLE -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white font-heading flex items-center gap-2">
                    <i class="fas fa-building-columns text-emerald-700 dark:text-emerald-400"></i>
                    Gestão Geral de Escolas & Modo Suporte
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Clique em <strong>"Entrar no Modo Suporte"</strong> para aceder ao ambiente operacional de qualquer escola com 1 clique.</p>
            </div>
            <div class="text-xs font-bold px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300">
                <i class="fas fa-shield-check me-1"></i> Isolamento Tenant Garantido (`SchoolScope`)
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 font-extrabold uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                        <th class="p-3.5 rounded-l-xl">ID / Código</th>
                        <th class="p-3.5">Nome da Escola</th>
                        <th class="p-3.5">Regime / Tipo</th>
                        <th class="p-3.5">Localização Territorial</th>
                        <th class="p-3.5 text-center">Alunos</th>
                        <th class="p-3.5 text-center">Professores</th>
                        <th class="p-3.5 text-center">Estado da Licença</th>
                        <th class="p-3.5 text-right rounded-r-xl">Ação de Suporte</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($schools as $school)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="p-3.5 text-slate-500 font-mono font-bold">
                                #{{ $school->id }}
                                @if($school->code)
                                    <span class="block text-[10px] text-slate-400">{{ $school->code }}</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ $school->name }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span><i class="fas fa-envelope text-slate-400 me-1"></i>{{ $school->email ?? 'Sem email' }}</span>
                                    @if($school->phone)
                                        <span>· <i class="fas fa-phone text-slate-400 me-1"></i>{{ $school->phone }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                @if(in_array($school->type, ['publica', 'public']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 text-[11px] font-bold border border-blue-200 dark:border-blue-800">
                                        <i class="fas fa-landmark text-blue-600"></i> Pública (SNE)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 text-[11px] font-bold border border-emerald-200 dark:border-emerald-800">
                                        <i class="fas fa-building text-emerald-600"></i> Privada / Comunitária
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $school->province?->name ?? 'Zambézia' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $school->district?->name ?? 'Namacurra' }} · ZIP: {{ $school->zip?->name ?? 'Sede' }}</div>
                            </td>
                            <td class="p-3.5 text-center font-extrabold text-slate-800 dark:text-slate-200 text-sm">
                                {{ number_format($school->students_count) }}
                            </td>
                            <td class="p-3.5 text-center font-extrabold text-slate-800 dark:text-slate-200 text-sm">
                                {{ number_format($school->teachers_count) }}
                            </td>
                            <td class="p-3.5 text-center">
                                @php
                                    $lic = $school->latestLicense;
                                    $licStatus = $lic?->status ?? 'active';
                                @endphp
                                @if($licStatus === 'active')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-extrabold text-[11px] border border-emerald-300">
                                        Ativa (100%)
                                    </span>
                                @elseif($licStatus === 'grace_period')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-extrabold text-[11px] border border-amber-300">
                                        Tolerância
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 font-extrabold text-[11px] border border-rose-300">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right">
                                <form action="{{ route('super-admin.impersonate', $school->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-300 font-extrabold text-xs transition border border-slate-700 shadow-sm flex items-center gap-1.5 ms-auto" title="Operar no ambiente desta escola">
                                        <i class="fas fa-headset text-amber-400"></i> Entrar no Modo Suporte
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">
                                <i class="fas fa-school-circle-xmark text-4xl mb-3 text-slate-300 block"></i>
                                Nenhuma escola registada de momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $schools->links() }}
        </div>
    </div>
</div>
@endsection
