@extends('layouts.app')

@section('title', 'Portal do Encarregado')
@section('page-title', 'Painel do Encarregado de Educação')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item">Parent-portal</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Painel do Encarregado de Educação</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- 1. Hero Banner de Boas-Vindas --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 text-white shadow-md">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-400/30 mb-2">
                    <i class="fas fa-shield-alt"></i> Área Reservada do Encarregado
                </span>
                <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl">
                    Olá, {{ $parent ? $parent->full_name : Auth::user()->name }}!
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-emerald-100/90 max-w-xl">
                    Acompanhe em tempo real as notas, assiduidade, boletins e mensalidades dos seus educandos.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/10 px-3 py-2 text-xs font-bold text-white backdrop-blur-sm border border-white/10">
                    <i class="fas fa-calendar-alt text-emerald-300"></i> Ano Lectivo {{ now()->year }}
                </span>
                <a href="{{ route('parent.communications') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 px-3.5 py-2 text-xs font-black text-slate-900 shadow-sm transition-all">
                    <i class="fas fa-bullhorn"></i> Comunicados ({{ $recentCommunications->count() }})
                </a>
            </div>
        </div>
        <div class="absolute -right-6 -bottom-10 opacity-10 pointer-events-none">
            <i class="fas fa-user-graduate text-9xl"></i>
        </div>
    </div>

    {{-- 2. KPI Summary Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @if(is_private_school())
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Situação Financeira</p>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $totalPendingPayments > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                    <i class="fas {{ $totalPendingPayments > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }} text-lg"></i>
                </div>
            </div>
            <p class="mt-3 text-2xl font-black tracking-tight {{ $totalPendingPayments > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ number_format($totalPendingPayments, 2, ',', '.') }} MT
            </p>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    {{ $totalPendingPayments > 0 ? 'Valores com liquidação pendente' : 'Todas as propinas em dia' }}
                </span>
                @if ($totalPendingPayments > 0)
                    <a href="{{ route('parent.payments') }}" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-700">
                        Regularizar <i class="fas fa-arrow-right text-3xs"></i>
                    </a>
                @else
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-3xs font-bold text-emerald-700">Em dia</span>
                @endif
            </div>
        </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Educandos Agregados</p>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <i class="fas fa-child text-lg"></i>
                </div>
            </div>
            <p class="mt-3 text-2xl font-black tracking-tight text-slate-800">{{ $children->count() }}</p>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-slate-500">Alunos sob sua responsabilidade</span>
                <a href="{{ route('parent.children') }}" class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 hover:text-sky-700">
                    Ver todos <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Avisos & Comunicados</p>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i class="fas fa-bell text-lg"></i>
                </div>
            </div>
            <p class="mt-3 text-2xl font-black tracking-tight text-slate-800">{{ $recentCommunications->count() }}</p>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-slate-500">Mensagens enviadas pela escola</span>
                <a href="{{ route('parent.communications') }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-700">
                    Ver avisos <i class="fas fa-arrow-right text-3xs"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- 3. Seccao Meus Educandos --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg text-sm">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Meus Educandos</h3>
                    <p class="text-xs text-slate-500">Selecione um educando para ver a caderneta, notas e faltas</p>
                </div>
            </div>
            <a href="{{ route('parent.children') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                Ver em Grade <i class="fas fa-th-large ml-1"></i>
            </a>
        </div>

        <div class="divide-y divide-slate-100 p-6 space-y-6">
            @forelse($children as $child)
                <div class="rounded-xl border border-slate-200 p-5 bg-white transition-all hover:border-emerald-300 hover:shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        {{-- Avatar e Info Basica --}}
                        <div class="flex items-start gap-4">
                            @if ($child->photo_url)
                                <img src="{{ $child->photo_url }}" alt="{{ $child->first_name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-emerald-500/30">
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 font-bold text-xl ring-2 ring-emerald-500/20">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-base font-black text-slate-900">{{ $child->full_name }}</h4>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600 border border-slate-200">
                                        N.º {{ $child->student_number }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs font-medium text-slate-500 flex items-center gap-3">
                                    <span><i class="fas fa-school text-slate-400"></i> {{ $child->currentEnrollment->class->name ?? 'Sem Turma' }}</span>
                                    <span>•</span>
                                    <span><i class="fas fa-birthday-cake text-slate-400"></i> {{ $child->age }} anos</span>
                                </p>

                                {{-- Acoes Rapidas --}}
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('parent.student-grades', $child) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-file-invoice"></i> Caderneta de Notas
                                    </a>
                                    <a href="{{ route('parent.student-attendance', $child) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-sky-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-sky-700 transition-colors">
                                        <i class="fas fa-calendar-check"></i> Registo de Presenças
                                    </a>
                                    @if(is_private_school())
                                    <a href="{{ route('parent.student-payments', $child) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                                        <i class="fas fa-credit-card"></i> Propinas
                                    </a>
                                    @endif
                                    <a href="{{ route('parent.student-details', $child) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-200 transition-colors">
                                        <i class="fas fa-user-circle"></i> Perfil Completo
                                    </a>

                                    @if ($child->is_eligible_for_renewal)
                                        @if ($child->renewal_application)
                                            @if ($child->renewal_application->status === 'PENDING')
                                                <span class="inline-flex items-center gap-1 rounded-lg bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-bold text-amber-700">
                                                    <i class="fas fa-clock"></i> Renovação Pendente
                                                </span>
                                            @elseif($child->renewal_application->status === 'APPROVED')
                                                <span class="inline-flex items-center gap-1 rounded-lg bg-sky-50 border border-sky-200 px-3 py-1.5 text-xs font-bold text-sky-700">
                                                    <i class="fas fa-check"></i> Aprovada, Aguardando Pagamento
                                                </span>
                                            @endif
                                        @else
                                            <a href="{{ route('parent.student-renewal', $child) }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-600 shadow-sm transition-colors">
                                                <i class="fas fa-sync-alt"></i> Renovar Matrícula
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Resumo Mini de Notas & Presencas --}}
                        <div class="flex items-center gap-3 border-t md:border-t-0 md:border-l border-slate-100 pt-3 md:pt-0 md:pl-5">
                            <div class="text-center p-3 bg-slate-50 rounded-xl min-w-[90px]">
                                <span class="block text-3xs font-bold uppercase text-slate-400">Presenças</span>
                                <span class="text-base font-black text-emerald-600">
                                    {{ $child->attendances ? $child->attendances->where('status', 'present')->count() : 0 }}
                                </span>
                                <span class="block text-3xs text-slate-500">Dias</span>
                            </div>

                            <div class="text-center p-3 bg-slate-50 rounded-xl min-w-[90px]">
                                <span class="block text-3xs font-bold uppercase text-slate-400">Avaliações</span>
                                <span class="text-base font-black text-sky-600">
                                    {{ $child->grades ? $child->grades->count() : 0 }}
                                </span>
                                <span class="block text-3xs text-slate-500">Registadas</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-xl bg-slate-50 p-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-200 text-slate-500 mb-3">
                        <i class="fas fa-child text-xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Nenhum educando encontrado</p>
                    <p class="text-xs text-slate-500 mt-1">Se considera que isto é um erro, entre em contato com a secretaria da escola.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 4. Secao Comunicados Recentes --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <div class="p-2 bg-indigo-100 text-indigo-700 rounded-lg text-sm">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Comunicados Recentes da Escola</h3>
            </div>
            <a href="{{ route('parent.communications') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">
                Ver Todos Comunicados <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="divide-y divide-slate-100 p-6">
            @forelse($recentCommunications as $comm)
                <article class="py-4 first:pt-0 last:pb-0">
                    <div class="flex items-start justify-between gap-4">
                        <h4 class="text-sm font-bold text-slate-800 hover:text-emerald-700 transition-colors">{{ $comm->title }}</h4>
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-3xs font-semibold text-slate-600 whitespace-nowrap">
                            <i class="fas fa-clock text-slate-400"></i> {{ $comm->created_at?->format('d/m/Y') ?? 'N/A' }}
                        </span>
                    </div>
                    <p class="mt-2 line-clamp-2 text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $comm->message }}</p>
                </article>
            @empty
                <p class="rounded-xl bg-slate-50 p-6 text-center text-xs text-slate-500 font-medium">Nenhum comunicado recente registado.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
