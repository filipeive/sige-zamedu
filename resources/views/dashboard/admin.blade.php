@extends('layouts.app')

@section('title', 'Dashboard Executivo & Analytics')
@section('page-title', 'Dashboard')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard ZamEdu</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-3">
        @if(is_public_school())
            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700">
                <i class="fas fa-landmark"></i> Escola Pública
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">
                <i class="fas fa-school"></i> Escola Privada
            </span>
        @endif
        <button type="button"
            class="inline-flex items-center gap-2 rounded-full bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-emerald-800"
            onclick="location.reload()">
            <i class="fas fa-sync-alt"></i>
            Atualizar
        </button>
        <button type="button" onclick="window.print()"
            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <i class="fas fa-print"></i>
            Imprimir
        </button>
    </div>
@endsection

@push('styles')
<style>
    /* ===== DESIGN SYSTEM PREMIUM ===== */
    :root {
        --dash-emerald: #059669;
        --dash-emerald-light: #d1fae5;
        --dash-emerald-glow: rgba(5, 150, 105, 0.15);
        --dash-slate-50: #f8fafc;
        --dash-slate-100: #f1f5f9;
        --dash-slate-200: #e2e8f0;
        --dash-slate-400: #94a3b8;
        --dash-slate-500: #64748b;
        --dash-slate-700: #334155;
        --dash-slate-900: #0f172a;
        --dash-radius: 1.25rem;
        --dash-radius-sm: 0.875rem;
        --dash-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
        --dash-shadow-hover: 0 10px 40px -10px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(15, 23, 42, 0.04);
        --dash-shadow-glow: 0 0 0 3px var(--dash-emerald-glow);
    }

    /* Animações de entrada */
    @keyframes dashFadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes dashPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    @keyframes dashSlideIn {
        from { opacity: 0; transform: translateX(-12px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .dash-animate {
        animation: dashFadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .dash-animate-d1 { animation-delay: 0.05s; }
    .dash-animate-d2 { animation-delay: 0.1s; }
    .dash-animate-d3 { animation-delay: 0.15s; }
    .dash-animate-d4 { animation-delay: 0.2s; }
    .dash-animate-d5 { animation-delay: 0.25s; }
    .dash-animate-d6 { animation-delay: 0.3s; }

    /* Cards Premium */
    .dash-card {
        background: #ffffff;
        border: 1px solid var(--dash-slate-200);
        border-radius: var(--dash-radius);
        padding: 1.5rem;
        box-shadow: var(--dash-shadow);
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .dash-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 0;
        background: linear-gradient(90deg, var(--dash-emerald), #10b981);
        transition: height 0.3s ease;
        opacity: 0.08;
    }
    .dash-card:hover {
        box-shadow: var(--dash-shadow-hover);
        transform: translateY(-2px);
        border-color: #cbd5e1;
    }
    .dash-card:hover::before {
        height: 3px;
    }

    .dash-card-flat {
        background: #ffffff;
        border: 1px solid var(--dash-slate-200);
        border-radius: var(--dash-radius);
        padding: 1.5rem;
        box-shadow: none;
    }

    .dash-card-glass {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.95));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: var(--dash-radius);
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(15, 23, 42, 0.04);
    }

    /* Seções */
    .dash-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.875rem;
        border-bottom: 1px solid var(--dash-slate-100);
    }
    .dash-section-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--dash-slate-900);
        margin: 0;
        letter-spacing: -0.01em;
    }
    .dash-section-subtitle {
        font-size: 0.78rem;
        color: var(--dash-slate-500);
        margin: 0.15rem 0 0 0;
        font-weight: 500;
    }

    /* KPIs */
    .dash-kpi-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--dash-slate-900);
        line-height: 1.15;
        letter-spacing: -0.02em;
        font-family: 'Poppins', system-ui, sans-serif;
    }
    .dash-kpi-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--dash-slate-500);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.5rem;
    }
    .dash-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .dash-card:hover .dash-kpi-icon {
        transform: scale(1.08) rotate(-3deg);
    }

    /* Badges */
    .dash-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border-radius: 9999px;
        padding: 0.35rem 0.85rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    /* Timeline Premium */
    .dash-timeline {
        position: relative;
        padding-left: 0.5rem;
    }
    .dash-timeline-item {
        position: relative;
        padding-left: 1.75rem;
        padding-bottom: 1.25rem;
        border-left: 2px solid var(--dash-slate-200);
        animation: dashSlideIn 0.4s ease forwards;
        opacity: 0;
    }
    .dash-timeline-item:nth-child(1) { animation-delay: 0.1s; }
    .dash-timeline-item:nth-child(2) { animation-delay: 0.2s; }
    .dash-timeline-item:nth-child(3) { animation-delay: 0.3s; }
    .dash-timeline-item:nth-child(4) { animation-delay: 0.4s; }
    .dash-timeline-item:last-child {
        border-left-color: transparent;
        padding-bottom: 0;
    }
    .dash-timeline-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--dash-slate-200);
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px var(--dash-slate-200);
        transition: all 0.3s ease;
    }
    .dash-timeline-item:hover::before {
        transform: scale(1.2);
    }
    .dash-timeline-item.type-payment::before {
        background: #10b981;
        box-shadow: 0 0 0 2px #d1fae5;
    }
    .dash-timeline-item.type-enrollment::before {
        background: #3b82f6;
        box-shadow: 0 0 0 2px #dbeafe;
    }
    .dash-timeline-item.type-grade::before {
        background: #f59e0b;
        box-shadow: 0 0 0 2px #fef3c7;
    }

    /* Quick Actions Premium */
    .dash-quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        padding: 1.25rem 0.75rem;
        border-radius: var(--dash-radius-sm);
        border: 1px solid var(--dash-slate-200);
        background: #ffffff;
        color: var(--dash-slate-700);
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .dash-quick-action::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 0;
        background: linear-gradient(90deg, var(--dash-emerald), #10b981);
        transition: height 0.3s ease;
        opacity: 0.1;
    }
    .dash-quick-action:hover {
        background: #ffffff;
        border-color: var(--dash-emerald);
        color: var(--dash-slate-900);
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(5, 150, 105, 0.12);
    }
    .dash-quick-action:hover::after {
        height: 3px;
    }
    .dash-quick-action i {
        font-size: 1.35rem;
        color: var(--dash-emerald);
        transition: transform 0.3s ease;
    }
    .dash-quick-action:hover i {
        transform: scale(1.15);
    }

    /* Collapse Premium */
    .dash-collapse-trigger {
        cursor: pointer;
        user-select: none;
        list-style: none;
        transition: all 0.2s ease;
    }
    .dash-collapse-trigger::-webkit-details-marker { display: none; }
    .dash-collapse-trigger:hover {
        opacity: 0.8;
    }
    details[open] .dash-collapse-trigger i.fa-chevron-down {
        transform: rotate(180deg);
    }
    .dash-collapse-trigger i.fa-chevron-down {
        transition: transform 0.3s ease;
    }
    .dash-collapse-content {
        margin-top: 1.25rem;
        animation: dashFadeUp 0.4s ease;
    }

    /* Header Contextual Premium */
    .dash-header-contextual {
        background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 50%, #ffffff 100%);
        border: 1px solid #d1fae5;
        position: relative;
        overflow: hidden;
    }
    .dash-header-contextual::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(5,150,105,0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Charts Container */
    .dash-chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }

    /* Ranking Items Premium */
    .dash-rank-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-radius: var(--dash-radius-sm);
        padding: 0.875rem 1rem;
        background: var(--dash-slate-50);
        border: 1px solid transparent;
        transition: all 0.25s ease;
    }
    .dash-rank-item:hover {
        background: #ffffff;
        border-color: var(--dash-slate-200);
        box-shadow: var(--dash-shadow);
        transform: translateX(2px);
    }
    .dash-rank-medal {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 900;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .dash-rank-medal.gold {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #78350f;
    }
    .dash-rank-medal.silver {
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        color: #334155;
    }
    .dash-rank-medal.bronze {
        background: linear-gradient(135deg, #fdba74, #ea580c);
        color: #ffffff;
    }
    .dash-rank-medal.default {
        background: var(--dash-slate-100);
        color: var(--dash-slate-500);
    }

    /* Pending Actions Premium */
    .dash-pending-card {
        border-radius: var(--dash-radius-sm);
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        border: 1px solid;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .dash-pending-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .dash-pending-card:hover {
        transform: translateX(3px);
        box-shadow: var(--dash-shadow);
    }
    .dash-pending-card:hover::before {
        opacity: 1;
    }
    .dash-pending-card.danger {
        background: #fef2f2;
        border-color: #fecaca;
    }
    .dash-pending-card.danger::before { background: #dc2626; opacity: 1; }
    .dash-pending-card.warning {
        background: #fffbeb;
        border-color: #fde68a;
    }
    .dash-pending-card.warning::before { background: #d97706; opacity: 1; }
    .dash-pending-card.info {
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .dash-pending-card.info::before { background: #2563eb; opacity: 1; }
    .dash-pending-card.success {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    /* Smart Insights */
    .dash-insight-card {
        padding: 1rem 1.25rem;
        border-radius: var(--dash-radius-sm);
        border: 1px solid;
        transition: all 0.3s ease;
    }
    .dash-insight-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--dash-shadow-hover);
    }

    /* Attendance Ring */
    .dash-ring-container {
        position: relative;
        width: 60px;
        height: 60px;
    }
    .dash-ring-svg {
        transform: rotate(-90deg);
        width: 100%;
        height: 100%;
    }
    .dash-ring-bg {
        fill: none;
        stroke: var(--dash-slate-100);
        stroke-width: 3.5;
    }
    .dash-ring-progress {
        fill: none;
        stroke: #14b8a6;
        stroke-width: 3.5;
        stroke-linecap: round;
        transition: stroke-dasharray 1s ease;
    }
    .dash-ring-label {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 900;
        color: #0f766e;
    }

    /* Responsive */
    @media (max-width: 767.98px) {
        .dash-kpi-value { font-size: 1.4rem; }
        .dash-chart-container { height: 220px; }
    }

    /* Print optimizations */
    @media print {
        .dash-card, .dash-card-flat, .dash-card-glass {
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
            break-inside: avoid;
        }
        .dash-quick-action { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <!-- Bloco 1: Header Contextual Premium -->
    <div class="dash-card-glass dash-header-contextual dash-animate">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <p class="dash-kpi-label" style="margin-bottom:0.35rem; color: var(--dash-emerald);">
                    <i class="fas fa-school me-1" style="font-size:0.65rem;"></i>
                    {{ $greetingData['school_name'] }} · Ano Lectivo {{ $greetingData['school_year'] }}
                </p>
                <h1 class="dash-kpi-value" style="font-size:1.5rem; font-weight:800;">
                    {{ $greetingData['greeting'] }}
                </h1>
            </div>
            <div class="text-end">
                <p class="dash-kpi-label" style="margin-bottom:0.25rem;">Hoje</p>
                <p style="font-size:0.95rem; font-weight:700; color: var(--dash-slate-700); margin:0;">
                    <!-- Em portugues. exemplo  03 Setembro, 2026 -->
                    {{ $greetingData['current_date'] }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bloco 2: Ações Rápidas (No Topo para Acesso Imediato) -->
    <section class="dash-animate dash-animate-d1">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('students.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-sm transition">
                <i class="fas fa-user-plus text-xs"></i> Novo Aluno
            </a>
            <!-- encarregado com escola publica -->
            <a href="{{ route('parents.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-yellow-700 hover:bg-yellow-800 text-white font-bold text-xs shadow-sm transition">
                <i class="fas fa-user-plus text-xs"></i> Registrar Novo Encarregado
            </a>
            @if(is_public_school())
            <a href="{{ route('teachers.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-yellow-700 hover:bg-yellow-800 text-white font-bold text-xs shadow-sm transition">
                <i class="fas fa-user-plus text-xs"></i> Novo Professor
            </a>
            @endif
            @if(is_private_school())
                <a href="{{ route('payments.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition">
                    <i class="fas fa-file-invoice-dollar text-xs"></i> Registrar Pagamento
                </a>
            @endif
            <a href="{{ route('enrollments.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm transition">
                <i class="fas fa-id-card text-xs"></i> Nova Matrícula
            </a>
            <a href="{{ route('grades.index') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition">
                <i class="fas fa-pen-nib text-xs"></i> Lançar Notas
            </a>
            <a href="{{ route('expenses.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition">
                <i class="fas fa-receipt text-xs"></i> Nova Despesa
            </a>
            <a href="{{ route('communications.create') }}" class="no-underline inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition">
                <i class="fas fa-bullhorn text-xs"></i> Comunicado
            </a>
        </div>
    </section>

    <!-- Bloco 3: Alertas Inteligentes -->
    @if (!empty($smartInsights))
        <div class="d-flex flex-wrap gap-3 dash-animate dash-animate-d1">
            @foreach ($smartInsights as $insight)
                <div class="dash-insight-card flex items-start gap-3" 
                    style="flex:1 1 280px; 
                    background: {{ $insight['type'] === 'success' ? '#f0fdf4' : ($insight['type'] === 'info' ? '#eff6ff' : '#fffbeb') }}; 
                    border-color: {{ $insight['type'] === 'success' ? '#bbf7d0' : ($insight['type'] === 'info' ? '#bfdbfe' : '#fde68a') }};">
                    <div class="rounded-xl p-2.5 text-sm flex-shrink-0"
                        style="background: {{ $insight['type'] === 'success' ? '#dcfce7' : ($insight['type'] === 'info' ? '#dbeafe' : '#fef3c7') }}; 
                        color: {{ $insight['type'] === 'success' ? '#166534' : ($insight['type'] === 'info' ? '#1e40af' : '#92400e') }};">
                        <i class="fas {{ $insight['icon'] }}"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-1" style="font-size:0.85rem; color: var(--dash-slate-900);">{{ $insight['title'] }}</p>
                        <p class="mb-0" style="font-size:0.78rem; color: var(--dash-slate-500); line-height:1.4;">{{ $insight['message'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Bloco 4: KPIs Primários Premium (Pílula Vertical de Cor na Esquerda) -->
    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3 dash-animate dash-animate-d2">
        <!-- Receita (apenas escolas privadas) -->
        @if(is_private_school())
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Receita Mês</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($stats['monthly_revenue'], 0, ',', '.') }} MT</span>
            </div>
        </div>
        @endif

        <!-- Alunos -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos Ativos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($stats['total_students']) }}</span>
            </div>
        </div>

        <!-- Assiduidade -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Assiduidade</span>
                <span class="text-xl font-black text-emerald-700 mt-0.5 block">{{ $stats['overall_attendance_rate'] }}%</span>
            </div>
        </div>

        <!-- Média -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-award"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Média Geral</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['global_grade_avg'] }}/20</span>
            </div>
        </div>

        <!-- Despesas -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-rose-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-arrow-trend-down"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Despesas Mês</span>
                <span class="text-xl font-black text-rose-700 mt-0.5 block">{{ number_format($stats['this_month_expenses'] ?? 0, 0, ',', '.') }} MT</span>
            </div>
        </div>
    </section>

    <!-- Bloco 5: Gráficos & Análises (Visto de Primeira para Impacto Executivo) -->
    <section class="dash-animate dash-animate-d3">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-line text-emerald-600"></i> Análises & Gráficos Executivos
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Evolução financeira e distribuição de alunos</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200">
                    6 Meses
                </span>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                @if(is_private_school())
                <div class="lg:col-span-8 bg-slate-50/50 rounded-xl p-3 border border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Evolução da Receita</span>
                            <span class="text-[10px] text-slate-500">Últimos 6 meses em Meticais</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                            <i class="fas fa-arrow-trend-up text-[9px]"></i> Crescimento
                        </span>
                    </div>
                    <div class="dash-chart-container" style="height: 220px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                @else
                <div class="lg:col-span-8 bg-slate-50/50 rounded-xl p-3 border border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Taxa de Assiduidade Escolar</span>
                            <span class="text-[10px] text-slate-500">Frequência média mensal dos alunos</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold">
                            <i class="fas fa-user-check text-[9px]"></i> Frequência Global
                        </span>
                    </div>
                    <div class="dash-chart-container" style="height: 220px;">
                        <canvas id="academicAttendanceChart"></canvas>
                    </div>
                </div>
                @endif
                <div class="lg:col-span-4 bg-slate-50/50 rounded-xl p-3 border border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Alunos por Turma</span>
                            <span class="text-[10px] text-slate-500">Distribuição atual</span>
                        </div>
                    </div>
                    <div class="dash-chart-container" style="height: 220px;">
                        <canvas id="studentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bloco 6: Atividade Recente & Pendentes Premium -->
    <section class="row g-3 dash-animate dash-animate-d5">
        <!-- Atividade Recente -->
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-section">
                    <div>
                        <p class="dash-section-title">
                            <i class="fas fa-clock-rotate-left me-2" style="color: var(--dash-emerald); font-size:0.9rem;"></i>
                            Atividade Recente
                        </p>
                        <p class="dash-section-subtitle">Últimas movimentações do sistema</p>
                    </div>
                </div>
                <div class="dash-timeline">
                    @forelse($recentActivities as $activity)
                        <div class="dash-timeline-item type-{{ $activity->type ?? 'enrollment' }}">
                            <p class="fw-bold mb-1" style="font-size:0.85rem; color: var(--dash-slate-900);">{{ $activity->title }}</p>
                            <p class="mb-1" style="font-size:0.78rem; color: var(--dash-slate-500); line-height:1.4;">{{ $activity->description }}</p>
                            <p class="mb-0" style="font-size:0.7rem; color: var(--dash-slate-400); font-weight:600;">
                                <i class="far fa-clock me-1" style="font-size:0.6rem;"></i>{{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-2" style="width:48px; height:48px; background: var(--dash-slate-100); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                <i class="fas fa-inbox" style="color: var(--dash-slate-400); font-size:1.1rem;"></i>
                            </div>
                            <p class="mb-0" style="font-size:0.85rem; color: var(--dash-slate-400); font-weight:500;">Sem actividade recente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Ações Pendentes -->
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-section">
                    <div>
                        <p class="dash-section-title">
                            <i class="fas fa-list-check me-2" style="color: #d97706; font-size:0.9rem;"></i>
                            Ações Pendentes
                        </p>
                        <p class="dash-section-subtitle">Tarefas que requerem a sua atenção</p>
                    </div>
                    @if ($stats['pending_actions'] > 0)
                        <span class="dash-badge" style="background:#fef3c7; color:#92400e;">
                            <span style="animation: dashPulse 2s infinite;">●</span>
                            {{ $stats['pending_actions'] }} Pendentes
                        </span>
                    @endif
                </div>
                <div>
                    @if (is_private_school() && $stats['overdue_payments'] > 0)
                        <div class="dash-pending-card danger d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-xl p-2 flex-shrink-0" style="background:rgba(220,38,38,0.1); color:#dc2626;">
                                    <i class="fas fa-circle-exclamation"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0" style="font-size:0.85rem; color:#991b1b;">{{ $stats['overdue_payments'] }} Mensalidades em Atraso</p>
                                    <p class="mb-0" style="font-size:0.75rem; color:#b91c1c;">Valor em mora: {{ number_format($stats['overdue_amount'], 2, ',', '.') }} MT</p>
                                </div>
                            </div>
                            <a href="{{ route('payments.index') }}?status=overdue" class="dash-badge" style="background:#dc2626; color:#ffffff; text-decoration:none; flex-shrink:0;">Resolver</a>
                        </div>
                    @endif

                    @if ($stats['pending_enrollments'] > 0)
                        <div class="dash-pending-card warning d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-xl p-2 flex-shrink-0" style="background:rgba(217,119,6,0.1); color:#d97706;">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0" style="font-size:0.85rem; color:#92400e;">{{ $stats['pending_enrollments'] }} Pré-Matrículas Pendentes</p>
                                    <p class="mb-0" style="font-size:0.75rem; color:#b45309;">Aguardando aprovação da secretaria</p>
                                </div>
                            </div>
                            <a href="{{ route('enrollments.index') }}?status=pending" class="dash-badge" style="background:#d97706; color:#ffffff; text-decoration:none; flex-shrink:0;">Aprovar</a>
                        </div>
                    @endif

                    @if ($stats['pending_leave_requests'] > 0)
                        <div class="dash-pending-card info d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-xl p-2 flex-shrink-0" style="background:rgba(37,99,235,0.1); color:#2563eb;">
                                    <i class="fas fa-calendar-xmark"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0" style="font-size:0.85rem; color:#1e40af;">{{ $stats['pending_leave_requests'] }} Licenças Pendentes</p>
                                    <p class="mb-0" style="font-size:0.75rem; color:#2563eb;">Aguardando avaliação</p>
                                </div>
                            </div>
                            <a href="{{ route('leave-requests.index') }}" class="dash-badge" style="background:#2563eb; color:#ffffff; text-decoration:none; flex-shrink:0;">Ver</a>
                        </div>
                    @endif

                    @if ($stats['pending_actions'] === 0)
                        <div class="text-center py-5 dash-pending-card success" style="border-style: dashed;">
                            <div class="mb-2" style="width:56px; height:56px; background: #dcfce7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                                <i class="fas fa-circle-check" style="color: #16a34a; font-size:1.4rem;"></i>
                            </div>
                            <p class="fw-bold mb-1" style="font-size:0.9rem; color:#15803d;">Todas as tarefas estão concluídas!</p>
                            <p class="mb-0" style="font-size:0.78rem; color:#22c55e;">Não há acções pendentes no momento.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>



    <!-- Bloco 9: Rankings Premium -->
    <section class="dash-animate dash-animate-d6">
        <details class="dash-card-flat">
            <summary class="dash-section dash-collapse-trigger" style="border-bottom:0; padding-bottom:0; margin-bottom:0;">
                <div>
                    <p class="dash-section-title">
                        <i class="fas fa-trophy me-2" style="color: #f59e0b; font-size:0.9rem;"></i>
                        Rankings
                    </p>
                    <p class="dash-section-subtitle">Quadro de Honra e desempenho por turma</p>
                </div>
                <i class="fas fa-chevron-down" style="color: var(--dash-slate-400); font-size:0.8rem;"></i>
            </summary>
            <div class="dash-collapse-content">
                <div class="row g-3">
                    <!-- Quadro de Honra -->
                    <div class="col-lg-6">
                        <div class="dash-card-flat" style="background: linear-gradient(180deg, #ffffff 0%, #fffbeb 100%); border-color:#fde68a;">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="rounded-xl p-2.5" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color:#ffffff; box-shadow: 0 4px 12px rgba(245,158,11,0.3);">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0" style="font-size:0.95rem; color: var(--dash-slate-900);">Quadro de Honra</p>
                                    <p class="mb-0" style="font-size:0.78rem; color: var(--dash-slate-500);">Melhores alunos do ano lectivo</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @forelse($topStudents as $index => $student)
                                    <div class="dash-rank-item">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="dash-rank-medal {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'default')) }}">
                                                @if($index < 3)
                                                    <i class="fas fa-medal" style="font-size:0.7rem;"></i>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </span>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width:32px; height:32px; border-radius:50%; background: linear-gradient(135deg, #e2e8f0, #cbd5e1); display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; color: var(--dash-slate-600);">
                                                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="fw-bold mb-0" style="font-size:0.85rem; color: var(--dash-slate-900);">{{ $student->first_name }} {{ $student->last_name }}</p>
                                                    <p class="mb-0" style="font-size:0.72rem; color: var(--dash-slate-400); font-weight:600;">Nº {{ $student->student_number }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="dash-badge" style="background:#dcfce7; color:#166534; font-size:0.8rem;">
                                            {{ number_format($student->average_grade, 1) }} <span style="font-size:0.65rem; opacity:0.7;">/20</span>
                                        </span>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-graduation-cap mb-2" style="color: var(--dash-slate-300); font-size:1.5rem;"></i>
                                        <p class="mb-0" style="font-size:0.85rem; color: var(--dash-slate-400);">Nenhum aluno classificado</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Ranking de Turmas -->
                    <div class="col-lg-6">
                        <div class="dash-card-flat" style="background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%); border-color:#bbf7d0;">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="rounded-xl p-2.5" style="background: linear-gradient(135deg, #34d399, #059669); color:#ffffff; box-shadow: 0 4px 12px rgba(5,150,105,0.3);">
                                    <i class="fas fa-ranking-star"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-0" style="font-size:0.95rem; color: var(--dash-slate-900);">Ranking de Turmas</p>
                                    <p class="mb-0" style="font-size:0.78rem; color: var(--dash-slate-500);">Média geral por turma</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @forelse($topClasses as $index => $class)
                                    <div class="dash-rank-item">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="dash-rank-medal default" style="font-weight:800; font-size:0.75rem;">
                                                #{{ $index + 1 }}
                                            </span>
                                            <div>
                                                <p class="fw-bold mb-0" style="font-size:0.85rem; color: var(--dash-slate-900);">{{ $class->name }}</p>
                                                <p class="mb-0" style="font-size:0.72rem; color: var(--dash-slate-400); font-weight:600;">{{ $class->current_students }} Alunos matriculados</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:60px; height:4px; background: var(--dash-slate-200); border-radius:2px; overflow:hidden;">
                                                <div style="width:{{ min(($class->average_grade / 20) * 100, 100) }}%; height:100%; background: linear-gradient(90deg, #34d399, #059669); border-radius:2px;"></div>
                                            </div>
                                            <span class="dash-badge" style="background:#ccfbf1; color:#115e59; font-size:0.8rem;">
                                                {{ number_format($class->average_grade, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-school mb-2" style="color: var(--dash-slate-300); font-size:1.5rem;"></i>
                                        <p class="mb-0" style="font-size:0.85rem; color: var(--dash-slate-400);">Nenhuma turma classificada</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </details>
    </section>

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const computedStyle = getComputedStyle(document.documentElement);
            const primaryColor = computedStyle.getPropertyValue('--primary').trim() || '#4F46E5';
            const secondaryColor = computedStyle.getPropertyValue('--secondary').trim() || '#06B6D4';
            const accentColor = computedStyle.getPropertyValue('--accent').trim() || '#F59E0B';

            // Chart.js defaults premium
            Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
            Chart.defaults.color = '#64748b';

            const academicAttendanceCtx = document.getElementById('academicAttendanceChart')?.getContext('2d');
            if (academicAttendanceCtx) {
                const gradientFill = academicAttendanceCtx.createLinearGradient(0, 0, 0, 280);
                gradientFill.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                gradientFill.addColorStop(1, 'rgba(16, 185, 129, 0)');

                new Chart(academicAttendanceCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                        datasets: [{
                            label: 'Taxa de Assiduidade (%)',
                            data: [92, 95, 94, 96, 93, 97],
                            borderColor: '#10b981',
                            backgroundColor: gradientFill,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#10b981',
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { min: 80, max: 100 } }
                    }
                });
            }

            const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
            if (revenueCtx) {
                const revenueChartData = @json($revenueData ?? []);
                const months = (revenueChartData.months || []);
                const amounts = (revenueChartData.amounts || []);

                const gradientFill = revenueCtx.createLinearGradient(0, 0, 0, 280);
                gradientFill.addColorStop(0, 'rgba(79, 70, 229, 0.18)');
                gradientFill.addColorStop(0.5, 'rgba(79, 70, 229, 0.05)');
                gradientFill.addColorStop(1, 'rgba(79, 70, 229, 0)');

                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Receita (MT)',
                            data: amounts,
                            borderColor: '#6366f1',
                            backgroundColor: gradientFill,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.45,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#6366f1',
                            pointBorderWidth: 2.5,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBackgroundColor: '#6366f1',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverBorderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: '700' },
                                bodyFont: { size: 12, weight: '600' },
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y.toLocaleString('pt-MZ') + ' MT';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                                ticks: {
                                    font: { size: 11, weight: '600' },
                                    padding: 10,
                                    callback: function(value) {
                                        if (window.innerWidth < 640 && value >= 1000) {
                                            return (value / 1000).toLocaleString('pt-MZ') + 'k';
                                        }
                                        return value.toLocaleString('pt-MZ');
                                    }
                                }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { font: { size: 11, weight: '600' }, padding: 8 }
                            }
                        }
                    }
                });
            }

            const studentsCtx = document.getElementById('studentsChart')?.getContext('2d');
            if (studentsCtx) {
                const studentsDistributionData = @json($studentsDistribution ?? []);
                const labels = Array.isArray(studentsDistributionData)
                    ? studentsDistributionData.map(item => item.label)
                    : (studentsDistributionData.labels || []);
                const values = Array.isArray(studentsDistributionData)
                    ? studentsDistributionData.map(item => item.value)
                    : (studentsDistributionData.data || []);

                const barColors = labels.map((_, i) => {
                    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6'];
                    return colors[i % colors.length];
                });

                new Chart(studentsCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nº de Alunos',
                            data: values,
                            backgroundColor: barColors,
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 32,
                            hoverBackgroundColor: barColors.map(c => c + 'dd')
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleFont: { size: 12, weight: '700' },
                                bodyFont: { size: 12, weight: '600' },
                                padding: 12,
                                cornerRadius: 10,
                                displayColors: true,
                                boxPadding: 4
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                                ticks: { font: { size: 11, weight: '600' }, padding: 10 }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { font: { size: 10, weight: '600' }, padding: 6 }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
