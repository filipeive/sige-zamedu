@extends('layouts.app')

@section('title', 'Relatório Financeiro')
@section('page-title', 'Visão Geral Financeira')
@section('page-title-icon', 'fas fa-wallet')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="no-underline text-slate-500">Relatórios</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Financeiro</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- 1. Hero Header Executivo com Ações --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white p-6 shadow-md border border-slate-700/50">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 uppercase tracking-wider">
                        <i class="fas fa-sack-dollar me-1"></i> Inteligência Financeira &amp; Controladoria
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-white/10 text-white border border-white/15">
                        {{ now()->translatedFormat('F Y') }}
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    <i class="fas fa-wallet text-emerald-400"></i> Relatório &amp; Balanço Financeiro
                </h1>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Acompanhamento consolidado de receitas arrecadadas, controlo de despesas operacionais, cálculo de resultado líquido e monitorização de liquidação de propinas.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <a href="{{ route('reports.export.payments') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-file-excel"></i> Exportar CSV
                </a>
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition cursor-pointer">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Navegação Rápida entre Módulos Financeiros --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-2 shadow-2xs flex items-center gap-1.5 overflow-x-auto">
        <a href="{{ route('reports.financial') }}"
            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline bg-emerald-700 text-white shadow-2xs">
            <i class="fas fa-chart-pie"></i> Balanço Geral
        </a>
        <a href="{{ route('reports.financial.revenue') }}"
            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline text-slate-600 bg-slate-50 hover:bg-slate-100">
            <i class="fas fa-hand-holding-usd text-emerald-600"></i> Detalhe de Receitas
        </a>
        <a href="{{ route('reports.financial.defaulters') }}"
            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline text-slate-600 bg-slate-50 hover:bg-slate-100">
            <i class="fas fa-user-times text-amber-600"></i> Inadimplência &amp; Devedores
            @if($overdueCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900">{{ $overdueCount }}</span>
            @endif
        </a>
        <a href="{{ route('payments.index') }}"
            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline text-slate-600 bg-slate-50 hover:bg-slate-100">
            <i class="fas fa-receipt text-slate-500"></i> Todos os Pagamentos
        </a>
        @can('manage_expenses')
        <a href="{{ route('expenses.index') }}"
            class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline text-slate-600 bg-slate-50 hover:bg-slate-100">
            <i class="fas fa-receipt text-rose-500"></i> Gestão de Despesas
        </a>
        @endcan
    </div>

    {{-- 3. Stat KPI Cards com Destaque de Borda --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        {{-- Receitas Totais --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Receitas Totais</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-700 mt-0.5 block">
                    {{ number_format($totalPaidAmount, 2, ',', '.') }} <span class="text-xs font-bold text-slate-400">MT</span>
                </span>
                <span class="text-[11px] font-semibold text-emerald-600/90 flex items-center gap-1 mt-0.5">
                    <i class="fas fa-circle-check text-[10px]"></i> {{ $paidCount }} liquidados
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-arrow-trend-up"></i>
            </div>
        </div>

        {{-- Despesas Totais --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 border-l-rose-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Despesas Totais</span>
                <span class="text-xl sm:text-2xl font-black text-rose-700 mt-0.5 block">
                    {{ number_format($totalExpenses, 2, ',', '.') }} <span class="text-xs font-bold text-slate-400">MT</span>
                </span>
                <span class="text-[11px] font-semibold text-rose-600/90 flex items-center gap-1 mt-0.5">
                    <i class="fas fa-arrow-down text-[10px]"></i> Saídas operacionais
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-arrow-trend-down"></i>
            </div>
        </div>

        {{-- Resultado Líquido --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 {{ $netResult >= 0 ? 'border-l-teal-500' : 'border-l-rose-500' }} shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Resultado Líquido</span>
                <span class="text-xl sm:text-2xl font-black {{ $netResult >= 0 ? 'text-teal-800' : 'text-rose-700' }} mt-0.5 block">
                    {{ number_format($netResult, 2, ',', '.') }} <span class="text-xs font-bold text-slate-400">MT</span>
                </span>
                <span class="text-[11px] font-semibold {{ $netResult >= 0 ? 'text-teal-600' : 'text-rose-600' }} flex items-center gap-1 mt-0.5">
                    <i class="fas {{ $netResult >= 0 ? 'fa-scale-balanced' : 'fa-circle-exclamation' }} text-[10px]"></i>
                    {{ $netResult >= 0 ? 'Superávit no período' : 'Défice no período' }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl {{ $netResult >= 0 ? 'bg-teal-50 text-teal-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-scale-balanced"></i>
            </div>
        </div>

        {{-- Inadimplência --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 border-l-amber-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Inadimplência Total</span>
                <span class="text-xl sm:text-2xl font-black text-amber-700 mt-0.5 block">
                    {{ number_format($overdueTotal, 2, ',', '.') }} <span class="text-xs font-bold text-slate-400">MT</span>
                </span>
                <span class="text-[11px] font-semibold text-amber-600/90 flex items-center gap-1 mt-0.5">
                    <i class="fas fa-clock text-[10px]"></i> {{ $overdueCount }} mensalidades pendentes
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    {{-- 4. Cartões de Resumo Executivo / Insights --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- O que aconteceu? --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex flex-col justify-between hover:shadow-sm transition">
            <div>
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-emerald-800 mb-2">
                    <i class="fas fa-chart-line text-emerald-600"></i> O que aconteceu?
                </div>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    {{ $financialSummary['what_happened'] }}
                </p>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-semibold">
                <span>Liquidações Confirmadas</span>
                <span class="text-emerald-700 font-bold">{{ $paidCount }} transações</span>
            </div>
        </div>

        {{-- Qual a tendência? --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 border-l-4 border-l-blue-500 shadow-2xs flex flex-col justify-between hover:shadow-sm transition">
            <div>
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-blue-800 mb-2">
                    <i class="fas fa-arrow-trend-up text-blue-600"></i> Qual a tendência?
                </div>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    {{ $financialSummary['trend'] }}
                </p>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-semibold">
                <span>Margem Operacional</span>
                <span class="text-blue-700 font-bold">
                    {{ $totalPaidAmount > 0 ? round(($netResult / $totalPaidAmount) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>

        {{-- Recomendações --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 border-l-4 border-l-amber-500 shadow-2xs flex flex-col justify-between hover:shadow-sm transition">
            <div>
                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-amber-800 mb-2">
                    <i class="fas fa-triangle-exclamation text-amber-600"></i> Recomendações
                </div>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    {{ $financialSummary['attention'] }}
                </p>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-semibold">
                <span>Ação de Cobrança</span>
                <a href="{{ route('reports.financial.defaulters') }}" class="no-underline text-amber-700 hover:text-amber-800 font-bold">
                    Notificar Alunos <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- 5. Conteúdo Principal: Tabela de Últimos Pagamentos & Coluna Lateral --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Coluna Esquerda: Tabela de Últimos Pagamentos Efetuados (8 cols) --}}
        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs font-bold">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Últimos Pagamentos Registados</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Histórico recente de liquidação de propinas e taxas</p>
                        </div>
                    </div>
                    <a href="{{ route('payments.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                        Ver Todos <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                <th class="py-3 px-4">Aluno / Número</th>
                                <th class="py-3 px-3">Data</th>
                                <th class="py-3 px-3">Valor</th>
                                <th class="py-3 px-3">Método</th>
                                <th class="py-3 px-3">Estado</th>
                                <th class="py-3 px-4 text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentPayments as $payment)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800">
                                            <a href="{{ route('students.show', $payment->student_id) }}" class="no-underline text-slate-800 hover:text-emerald-700 transition">
                                                {{ $payment->student ? ($payment->student->first_name . ' ' . $payment->student->last_name) : 'N/I' }}
                                            </a>
                                        </div>
                                        <span class="font-mono text-[10px] text-slate-400 block mt-0.5">
                                            {{ $payment->student?->student_number ?? 'S/N' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-600 font-medium">
                                        {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') : ($payment->created_at ? $payment->created_at->format('d/m/Y') : '-') }}
                                    </td>
                                    <td class="py-3 px-3 font-black text-emerald-700">
                                        {{ number_format($payment->amount, 2, ',', '.') }} MT
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200">
                                            <i class="fas fa-wallet text-[9px] text-slate-400"></i>
                                            {{ $payment->payment_method ?? 'M-Pesa' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        @php
                                            $statusBadge = match($payment->status) {
                                                'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'overdue' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                                            };
                                            $statusLabel = match($payment->status) {
                                                'paid' => 'Pago',
                                                'pending' => 'Pendente',
                                                'overdue' => 'Em Atraso',
                                                'cancelled' => 'Cancelado',
                                                default => ucfirst($payment->status),
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusBadge }}">
                                            @if($payment->status === 'paid')
                                                <i class="fas fa-check text-[9px]"></i>
                                            @elseif($payment->status === 'overdue')
                                                <i class="fas fa-exclamation text-[9px]"></i>
                                            @else
                                                <i class="fas fa-clock text-[9px]"></i>
                                            @endif
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-end">
                                        <a href="{{ route('payments.show', $payment->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 inline-flex items-center justify-center transition" title="Ver Detalhes">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        <i class="fas fa-file-invoice text-3xl text-slate-300 mb-2"></i>
                                        <p class="text-xs font-semibold text-slate-600">Nenhum pagamento registrado recentemente.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Coluna Direita: Resumos Mensais & Alerta de Inadimplência (4 cols) --}}
        <div class="lg:col-span-4 space-y-4">

            {{-- Card Receitas por Mês --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-emerald-600"></i> Receita por Mês
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400">Últimos 6 meses</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($monthlyRevenue as $revenue)
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-slate-50/60 transition">
                            <span class="text-xs font-bold text-slate-700">
                                {{ \Carbon\Carbon::parse($revenue->month . '-01')->translatedFormat('F Y') }}
                            </span>
                            <span class="text-xs font-black text-emerald-700">
                                {{ number_format($revenue->total, 2, ',', '.') }} MT
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400">Sem histórico de receitas registrado.</div>
                    @endforelse
                </div>
            </div>

            {{-- Card Despesas por Mês --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-receipt text-rose-600"></i> Despesas por Mês
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400">Últimos 6 meses</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($monthlyExpenses as $expense)
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-slate-50/60 transition">
                            <span class="text-xs font-bold text-slate-700">
                                {{ \Carbon\Carbon::parse($expense->month . '-01')->translatedFormat('F Y') }}
                            </span>
                            <span class="text-xs font-black text-rose-700">
                                {{ number_format($expense->total, 2, ',', '.') }} MT
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400">Sem despesas registradas no período.</div>
                    @endforelse
                </div>
            </div>

            {{-- Alerta de Inadimplência em Cartão Especial --}}
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200/80 p-5 shadow-2xs">
                <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-amber-900 mb-2">
                    <i class="fas fa-triangle-exclamation text-amber-600 text-sm"></i>
                    Atenção à Inadimplência
                </div>
                <p class="text-xs text-amber-800/90 leading-relaxed font-medium mb-3">
                    Existem <strong class="text-amber-950 font-black">{{ $overdueCount }}</strong> mensalidades em atraso totalizando
                    <strong class="text-amber-950 font-black">{{ number_format($overdueTotal, 2, ',', '.') }} MT</strong> pendentes de recuperação.
                </p>
                <a href="{{ route('reports.financial.defaulters') }}" class="no-underline inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-users-slash"></i> Ver Alunos Devedores
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
