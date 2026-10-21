@extends('layouts.app')

@section('title', 'Dashboard Executivo & Analytics')
@section('page-title', 'Dashboard Executivo')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Dashboard Executivo</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-2 sm:gap-3">
        @if(is_public_school())
            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 px-3 py-1.5 text-xs font-black">
                <i class="fas fa-landmark text-blue-600"></i> Escola Pública (SNE)
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-3 py-1.5 text-xs font-black">
                <i class="fas fa-school text-emerald-600"></i> Escola Privada / Comunitária
            </span>
        @endif
        <a href="{{ route('admin.settings.index') }}?tab=escola" 
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 shadow-2xs hover:bg-slate-50 transition no-underline">
            <i class="fas fa-map-location-dot text-emerald-600"></i>
            Mapa & Definições
        </a>
        <button type="button" onclick="location.reload()"
            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white px-3.5 py-2 shadow-sm transition cursor-pointer">
            <i class="fas fa-sync-alt"></i>
            Atualizar
        </button>
    </div>
@endsection

@push('styles')
<style>
    :root {
        --dash-emerald: #007A3D;
        --dash-emerald-dark: #034522;
        --dash-gold: #FCD116;
        --dash-radius: 1.25rem;
    }

    @keyframes dashFadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dash-animate {
        animation: dashFadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .dash-card-hero {
        background: linear-gradient(135deg, #034522 0%, #007A3D 60%, #046c37 100%);
        box-shadow: 0 10px 30px -10px rgba(0, 122, 61, 0.3);
    }

    .dash-kpi-card {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .dash-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
    }

    .dash-chart-box {
        position: relative;
        height: 260px;
        width: 100%;
    }

    .dash-rank-item {
        transition: all 0.2s ease;
    }
    .dash-rank-item:hover {
        transform: translateX(3px);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- HERO HEADER CONTEXTUAL (ESCOLA DOS VISIONÁRIOS BRANDING) -->
    <div class="dash-card-hero rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden dash-animate">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white p-1.5 shadow-xl border-2 border-amber-400 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" class="h-13 w-auto object-contain">
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-amber-300 text-[11px] font-black uppercase tracking-wider mb-1.5 backdrop-blur-sm">
                        <i class="fas fa-building-columns text-amber-400"></i> {{ $greetingData['school_name'] }} · Ano Lectivo {{ $greetingData['school_year'] }}
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black font-heading text-white tracking-tight">
                        {{ $greetingData['greeting'] }}
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/90 font-medium mt-1">
                        Escola dos Visionários — <em>Aqui se Prepara a Nova Geração</em>
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:items-end gap-3 shrink-0">
                @if(isset($branches) && $branches->count() > 1)
                <div class="bg-black/20 backdrop-blur-md border border-white/20 rounded-2xl p-2.5 shadow-lg">
                    <label class="text-[10px] font-extrabold uppercase text-amber-300 block mb-1">
                        <i class="fas fa-sitemap me-1"></i> Alternar Filial / Rede:
                    </label>
                    <div class="flex items-center gap-1.5">
                        <select onchange="window.location.href=this.value" class="bg-slate-900/90 text-white font-bold text-xs rounded-xl px-3 py-2 border border-white/20 focus:ring-2 focus:ring-amber-400 cursor-pointer shadow-inner">
                            @foreach($branches as $b)
                                <option value="{{ route('branch.switch', $b->id) }}" {{ ($currentBranchId == $b->id && !$isConsolidated) ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                            <option value="{{ route('branch.switch', 'all') }}" {{ $isConsolidated ? 'selected' : '' }}>
                                Toda a Rede (Consolidado Visionários)
                            </option>
                        </select>
                        <a href="{{ route('owner.branches.index') }}" class="no-underline inline-flex items-center justify-center h-8 px-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition border border-white/20" title="Gerir Filiais da Rede">
                            <i class="fas fa-cog"></i>
                        </a>
                    </div>
                </div>
                @endif

                <div class="text-left sm:text-right bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-3 shrink-0">
                    <div class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Data de Hoje</div>
                    <div class="text-xs font-extrabold text-white mt-0.5">{{ $greetingData['current_date'] }}</div>
                    <div class="text-[11px] font-bold text-amber-300 mt-0.5 flex items-center gap-1 sm:justify-end">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Sistema Ativo
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS TOOLBAR -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-2xs dash-animate">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i> Ações Rápidas Executivas
            </h3>
            <span class="text-[11px] text-slate-400 font-bold">Atalhos Operacionais</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('students.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-sm transition">
                <i class="fas fa-user-plus text-amber-300"></i> Novo Aluno
            </a>
            <a href="{{ route('parents.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs shadow-sm transition">
                <i class="fas fa-users"></i> Novo Encarregado
            </a>
            @if(is_public_school())
                <a href="{{ route('teachers.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs shadow-sm transition">
                    <i class="fas fa-chalkboard-teacher"></i> Novo Professor
                </a>
            @endif
            @if(is_private_school())
                <a href="{{ route('payments.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition">
                    <i class="fas fa-file-invoice-dollar text-emerald-200"></i> Registrar Pagamento
                </a>
            @endif
            <a href="{{ route('enrollments.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-extrabold text-xs shadow-sm transition">
                <i class="fas fa-id-card"></i> Nova Matrícula
            </a>
            <a href="{{ route('grades.index') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-sm transition">
                <i class="fas fa-pen-nib"></i> Lançar Notas
            </a>
            <a href="{{ route('expenses.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-700 hover:bg-rose-800 text-white font-extrabold text-xs shadow-sm transition">
                <i class="fas fa-receipt"></i> Nova Despesa
            </a>
            <a href="{{ route('communications.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-extrabold text-xs shadow-sm transition">
                <i class="fas fa-bullhorn"></i> Comunicado
            </a>
        </div>
    </div>

    <!-- SMART AI INSIGHTS & ALERTS -->
    @if (!empty($smartInsights))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 dash-animate">
            @foreach ($smartInsights as $insight)
                <div class="rounded-2xl p-4 border flex items-start gap-3.5 shadow-2xs transition hover:shadow-sm" 
                    style="background: {{ $insight['type'] === 'success' ? '#f0fdf4' : ($insight['type'] === 'info' ? '#eff6ff' : '#fffbeb') }}; 
                    border-color: {{ $insight['type'] === 'success' ? '#bbf7d0' : ($insight['type'] === 'info' ? '#bfdbfe' : '#fde68a') }};">
                    <div class="rounded-xl p-2.5 text-base shrink-0 font-bold"
                        style="background: {{ $insight['type'] === 'success' ? '#dcfce7' : ($insight['type'] === 'info' ? '#dbeafe' : '#fef3c7') }}; 
                        color: {{ $insight['type'] === 'success' ? '#166534' : ($insight['type'] === 'info' ? '#1e40af' : '#92400e') }};">
                        <i class="fas {{ $insight['icon'] }}"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-900 mb-1">{{ $insight['title'] }}</h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $insight['message'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- EXECUTIVE KPIS GRID (INCORPORATES HM/M MINEDH STATS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 dash-animate">
        
        <!-- KPI 1: Alunos Registados (HM & M MINEDH Breakdown) -->
        <div class="dash-kpi-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xs relative overflow-hidden">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-600"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Alunos (SNE)</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">
                {{ number_format($stats['total_students']) }}
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500 flex items-center justify-between">
                <span><i class="fas fa-users text-emerald-600 me-1"></i> HM: {{ number_format($stats['total_students']) }}</span>
                <span><i class="fas fa-venus text-pink-500 me-1"></i> M: {{ number_format($stats['female_students'] ?? 0) }}</span>
            </div>
        </div>

        <!-- KPI 2: Receita Mês / Assiduidade -->
        @if(is_private_school())
            <div class="dash-kpi-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xs relative overflow-hidden">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-blue-600"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Receita do Mês</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-xs">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">
                    {{ number_format($stats['monthly_revenue'], 0, ',', '.') }} MT
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500 flex items-center justify-between">
                    <span>Mês Anterior: {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}</span>
                    @if($stats['revenue_change'] >= 0)
                        <span class="text-emerald-600"><i class="fas fa-arrow-up text-[9px]"></i> +{{ $stats['revenue_change'] }}%</span>
                    @else
                        <span class="text-rose-600"><i class="fas fa-arrow-down text-[9px]"></i> {{ $stats['revenue_change'] }}%</span>
                    @endif
                </div>
            </div>
        @else
            <div class="dash-kpi-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xs relative overflow-hidden">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-blue-600"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Taxa de Assiduidade</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-xs">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-700 dark:text-emerald-400 font-heading">
                    {{ $stats['overall_attendance_rate'] }}%
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500">
                    <i class="fas fa-check-circle text-emerald-500 me-1"></i> Frequência Global das Turmas
                </div>
            </div>
        @endif

        <!-- KPI 3: Média Geral Académica -->
        <div class="dash-kpi-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xs relative overflow-hidden">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-amber-500"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Média Académica</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xs">
                    <i class="fas fa-award"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">
                {{ $stats['global_grade_avg'] }} <span class="text-xs text-slate-400 font-normal">/20</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500">
                <i class="fas fa-graduation-cap text-amber-500 me-1"></i> Escala Oficial (Aprovação: 10.0 valores)
            </div>
        </div>

        <!-- KPI 4: Corpo Docente & Turmas -->
        <div class="dash-kpi-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xs relative overflow-hidden">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-purple-600"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Corpo Docente</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-xs">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-heading">
                {{ number_format($stats['total_teachers']) }}
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500">
                <i class="fas fa-chalkboard text-purple-500 me-1"></i> {{ $stats['total_classes'] }} Turmas Activas
            </div>
        </div>

        <!-- KPI 5: Despesas do Mês -->
        <div class="dash-kpi-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-2xs relative overflow-hidden">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-600"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Despesas do Mês</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 flex items-center justify-center font-bold text-xs">
                    <i class="fas fa-arrow-trend-down"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-rose-700 dark:text-rose-400 font-heading">
                {{ number_format($stats['this_month_expenses'] ?? 0, 0, ',', '.') }} MT
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500">
                <i class="fas fa-receipt text-rose-500 me-1"></i> Saídas Financeiras
            </div>
        </div>

    </div>

    <!-- SCHOOL LOCATION & MAP SUMMARY WIDGET -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-6 shadow-2xs dash-animate">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-map-location-dot text-emerald-600"></i> Localização Geográfica & Mapa da Instituição
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ setting('school_address', 'Namacurra, Zambézia, Moçambique') }} · Coordenadas GPS
                </p>
            </div>
            <a href="{{ route('admin.settings.index') }}?tab=escola" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#007A3D] dark:bg-emerald-950 dark:text-emerald-300 text-xs font-bold transition no-underline">
                <i class="fas fa-gear text-amber-500"></i> Configurar Mapa em Definições
            </a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
            <div class="lg:col-span-8 h-56 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 relative bg-slate-100 dark:bg-slate-800">
                <iframe class="w-full h-full border-0" 
                        src="https://maps.google.com/maps?q={{ urlencode(setting('school_latitude', '-17.8764')) }},{{ urlencode(setting('school_longitude', '36.8791')) }}&z=15&output=embed" 
                        allowfullscreen="" loading="lazy"></iframe>
            </div>
            <div class="lg:col-span-4 space-y-3">
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Província & Distrito</div>
                    <div class="text-xs font-black text-slate-800 dark:text-slate-100 mt-0.5">
                        {{ setting('school_province', 'Zambézia') }} · {{ setting('school_district', 'Namacurra') }}
                    </div>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Coordenadas GPS Oficiais</div>
                    <div class="text-xs font-mono font-bold text-emerald-700 dark:text-emerald-400 mt-0.5">
                        Lat: {{ setting('school_latitude', '-17.8764') }} | Lng: {{ setting('school_longitude', '36.8791') }}
                    </div>
                </div>
                @if(setting('school_google_maps_url'))
                    <a href="{{ setting('school_google_maps_url') }}" target="_blank" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-300 font-extrabold text-xs flex items-center justify-center gap-2 transition no-underline">
                        <i class="fas fa-up-right-from-square"></i> Abrir no Google Maps Navegador
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- CHARTS & ANALYTICS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 dash-animate">
        
        <!-- CHART 1: REVENUE / ATTENDANCE TREND -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-line text-emerald-600"></i> Evolução Financeira & Assiduidade
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Histórico de arrecadação mensal em Meticais</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800">
                    6 Meses
                </span>
            </div>
            <div class="dash-chart-box">
                @if(is_private_school())
                    <canvas id="revenueChart"></canvas>
                @else
                    <canvas id="academicAttendanceChart"></canvas>
                @endif
            </div>
        </div>

        <!-- CHART 2: GENDER DISTRIBUTION (HM / M MINEDH) -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-venus-mars text-purple-600"></i> Distribuição por Género (HM/M)
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Relatório de Equidade de Género</p>
                </div>
            </div>
            <div class="dash-chart-box" style="height:210px;">
                <canvas id="genderChart"></canvas>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-center">
                <div class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-800">
                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 block">Homens</span>
                    <span class="text-sm font-black text-blue-900 dark:text-blue-200">{{ number_format($stats['male_students'] ?? 0) }}</span>
                </div>
                <div class="p-2 rounded-xl bg-pink-50 dark:bg-pink-950/40 border border-pink-100 dark:border-pink-800">
                    <span class="text-[10px] font-bold text-pink-600 dark:text-pink-400 block">Mulheres (M)</span>
                    <span class="text-sm font-black text-pink-900 dark:text-pink-200">{{ number_format($stats['female_students'] ?? 0) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- RECENT ACTIVITIES & PENDING ACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 dash-animate">
        
        <!-- RECENT ACTIVITIES -->
        <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-emerald-600"></i> Atividade Recente da Escola
                </h3>
                <span class="text-[10px] font-bold text-slate-400">Tempo Real</span>
            </div>
            <div class="space-y-3.5">
                @forelse($recentActivities as $activity)
                    <div class="flex items-start gap-3 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-extrabold text-xs text-slate-900 dark:text-white">{{ $activity->title }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ $activity->description }}</div>
                            <div class="text-[10px] font-bold text-slate-400 mt-1 flex items-center gap-1">
                                <i class="far fa-clock"></i> {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-inbox text-3xl mb-2 block text-slate-300"></i>
                        <p class="text-xs font-medium">Sem atividade recente registada.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- PENDING ACTIONS -->
        <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-list-check text-amber-500"></i> Ações & Validações Pendentes
                </h3>
                @if ($stats['pending_actions'] > 0)
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] border border-amber-300">
                        {{ $stats['pending_actions'] }} Requerem Atenção
                    </span>
                @endif
            </div>

            <div class="space-y-3">
                @if (is_private_school() && $stats['overdue_payments'] > 0)
                    <div class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-900 text-rose-700 dark:text-rose-300 flex items-center justify-center font-bold shrink-0">
                                <i class="fas fa-circle-exclamation"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-xs text-rose-900 dark:text-rose-200">{{ $stats['overdue_payments'] }} Mensalidades em Atraso</div>
                                <div class="text-[11px] text-rose-700 dark:text-rose-300">Mora total: {{ number_format($stats['overdue_amount'], 2, ',', '.') }} MT</div>
                            </div>
                        </div>
                        <a href="{{ route('payments.index') }}?status=overdue" class="px-3 py-1.5 rounded-xl bg-rose-700 hover:bg-rose-800 text-white font-extrabold text-xs shadow-2xs no-underline shrink-0">
                            Resolver
                        </a>
                    </div>
                @endif

                @if ($stats['pending_enrollments'] > 0)
                    <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold shrink-0">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-xs text-amber-900 dark:text-amber-200">{{ $stats['pending_enrollments'] }} Pré-Matrículas Pendentes</div>
                                <div class="text-[11px] text-amber-700 dark:text-amber-300">Aguardando aprovação da secretaria</div>
                            </div>
                        </div>
                        <a href="{{ route('enrollments.index') }}?status=pending" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-2xs no-underline shrink-0">
                            Aprovar
                        </a>
                    </div>
                @endif

                @if ($stats['pending_leave_requests'] > 0)
                    <div class="p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold shrink-0">
                                <i class="fas fa-calendar-xmark"></i>
                            </div>
                            <div>
                                <div class="font-extrabold text-xs text-blue-900 dark:text-blue-200">{{ $stats['pending_leave_requests'] }} Licenças Pendentes</div>
                                <div class="text-[11px] text-blue-700 dark:text-blue-300">Aguardando decisão da Direção</div>
                            </div>
                        </div>
                        <a href="{{ route('leave-requests.index') }}" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-2xs no-underline shrink-0">
                            Ver
                        </a>
                    </div>
                @endif

                @if ($stats['pending_actions'] === 0)
                    <div class="p-6 text-center rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800">
                        <i class="fas fa-circle-check text-emerald-600 text-3xl mb-2 block"></i>
                        <p class="font-extrabold text-xs text-emerald-900 dark:text-emerald-200">Todas as tarefas estão em dia!</p>
                        <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-0.5">Não há pendências a requerer intervenção imediata.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- HONOR ROLL & CLASS RANKINGS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 dash-animate">
        
        <!-- QUADRO DE HONRA -->
        <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-trophy text-amber-500"></i> Quadro de Honra da Escola
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Melhores alunos por média curricular (Escala 0 a 20)</p>
                </div>
            </div>
            <div class="space-y-2.5">
                @forelse($topStudents as $index => $student)
                    <div class="dash-rank-item p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black text-white shrink-0
                                {{ $index === 0 ? 'bg-amber-500 shadow-md' : ($index === 1 ? 'bg-slate-400' : ($index === 2 ? 'bg-amber-700' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200')) }}">
                                {{ $index + 1 }}º
                            </div>
                            <div>
                                <div class="font-extrabold text-xs text-slate-900 dark:text-white">{{ $student->first_name }} {{ $student->last_name }}</div>
                                <div class="text-[10px] font-bold text-slate-400">Nº {{ $student->student_number }}</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-black text-xs border border-emerald-200">
                            {{ number_format($student->average_grade, 1) }} /20
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-graduation-cap text-3xl mb-2 block text-slate-300"></i>
                        <p class="text-xs font-medium">Nenhum aluno classificado ainda.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RANKING DE TURMAS -->
        <div class="lg:col-span-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-ranking-star text-emerald-600"></i> Ranking de Turmas por Desempenho
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Média ponderada por turma</p>
                </div>
            </div>
            <div class="space-y-2.5">
                @forelse($topClasses as $index => $class)
                    <div class="dash-rank-item p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="font-extrabold text-xs text-slate-400">#{{ $index + 1 }}</span>
                            <div>
                                <div class="font-extrabold text-xs text-slate-900 dark:text-white">{{ $class->name }}</div>
                                <div class="text-[10px] font-bold text-slate-400">{{ $class->current_students }} Alunos Matriculados</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-600 rounded-full" style="width: {{ min(($class->average_grade / 20) * 100, 100) }}%"></div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300 font-black text-xs border border-teal-200">
                                {{ number_format($class->average_grade, 1) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-school text-3xl mb-2 block text-slate-300"></i>
                        <p class="text-xs font-medium">Nenhuma turma classificada de momento.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#64748b';

            // Chart 1: Revenue or Attendance
            const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
            if (revenueCtx) {
                const revenueChartData = @json($revenueData ?? []);
                const months = (revenueChartData.months || []);
                const amounts = (revenueChartData.amounts || []);

                const grad = revenueCtx.createLinearGradient(0, 0, 0, 240);
                grad.addColorStop(0, 'rgba(0, 122, 61, 0.25)');
                grad.addColorStop(1, 'rgba(0, 122, 61, 0)');

                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Receita (MT)',
                            data: amounts,
                            borderColor: '#007A3D',
                            backgroundColor: grad,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#007A3D',
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(226, 232, 240, 0.5)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            const attendanceCtx = document.getElementById('academicAttendanceChart')?.getContext('2d');
            if (attendanceCtx) {
                new Chart(attendanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                        datasets: [{
                            label: 'Assiduidade (%)',
                            data: [92, 95, 94, 96, 93, 97],
                            borderColor: '#3b82f6',
                            borderWidth: 3,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { min: 80, max: 100 } }
                    }
                });
            }

            // Chart 2: Gender Doughnut Chart (HM / M MINEDH)
            const genderCtx = document.getElementById('genderChart')?.getContext('2d');
            if (genderCtx) {
                const femaleCount = {{ $stats['female_students'] ?? 0 }};
                const maleCount = {{ $stats['male_students'] ?? 0 }};

                new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Homens (H)', 'Mulheres (M)'],
                        datasets: [{
                            data: [maleCount, femaleCount],
                            backgroundColor: ['#3b82f6', '#ec4899'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { size: 11, weight: '700' } } }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
@endpush
