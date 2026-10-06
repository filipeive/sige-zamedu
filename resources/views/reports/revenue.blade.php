@extends('layouts.app')

@section('title', 'Relatório de Receitas')
@section('page-title', 'Relatório de Receitas')
@section('page-title-icon', 'fas fa-hand-holding-usd')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.financial') }}">Financeiro</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Receitas</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.export.payments') }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-bold text-white shadow-md transition"
           style="background: var(--primary);">
            <i class="fas fa-file-excel"></i> Exportar Receitas (CSV)
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-full border border-[var(--border-color)] bg-[var(--card-bg)] px-4 py-2 text-xs font-bold text-[var(--text-primary)] shadow-xs hover:bg-[var(--surface-bg)] transition">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-6 bg-[var(--content-bg)] -m-4 p-6 rounded-3xl min-h-screen">

        <!-- Filter Card MOPHY Style -->
        <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)]">
            <form action="{{ route('reports.financial.revenue') }}" method="GET" class="grid gap-4 md:grid-cols-3 items-end">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Data De</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-2xl border border-[var(--border-color)] bg-[var(--surface-bg)] px-4 py-2 text-xs font-bold text-[var(--text-primary)] focus:bg-[var(--card-bg)] focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Data Até</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-2xl border border-[var(--border-color)] bg-[var(--surface-bg)] px-4 py-2 text-xs font-bold text-[var(--text-primary)] focus:bg-[var(--card-bg)] focus:outline-none">
                </div>

                <div>
                    <button type="submit" class="w-full rounded-2xl py-2.5 text-xs font-extrabold text-white shadow-md transition"
                            style="background: var(--primary);">
                        <i class="fas fa-filter me-1"></i> Filtrar Período
                    </button>
                </div>
            </form>
        </div>

        <!-- Total Revenue Highlight Banner -->
        <div class="rounded-3xl p-7 text-white shadow-xl flex items-center justify-between"
             style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-wider opacity-80">Total Arrecadado no Período</span>
                <p class="text-3xl font-black text-white mt-1">{{ number_format($totalRevenue, 2, ',', '.') }} <span class="text-sm font-normal opacity-80">MT</span></p>
            </div>
            <div class="rounded-2xl bg-white/20 p-4 text-white text-2xl shadow-inner">
                <i class="fas fa-sack-dollar"></i>
            </div>
        </div>

        <!-- Table Card MOPHY Style -->
        <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)]">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-[var(--border-color)]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shrink-0 shadow-xs"
                         style="background: var(--primary-soft); color: var(--primary);">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[var(--text-primary)] font-heading">Detalhamento de Transações</h3>
                        <p class="text-xs text-[var(--text-muted)]">Histórico detalhado de liquidações</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--surface-bg)] text-[var(--text-muted)] font-bold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Aluno</th>
                            <th class="px-4 py-3 text-left">Data Liquidação</th>
                            <th class="px-4 py-3 text-left">Método</th>
                            <th class="px-4 py-3 text-left">Nº Referência</th>
                            <th class="px-4 py-3 text-right">Valor Arrecadado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-[var(--surface-bg)] transition">
                                <td class="px-4 py-3.5 font-bold text-[var(--text-primary)]">
                                    {{ $payment->student ? ($payment->student->first_name . ' ' . $payment->student->last_name) : 'N/A' }}
                                </td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-medium">
                                    {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block rounded-full bg-[var(--surface-bg)] px-3 py-0.5 text-[11px] font-bold text-[var(--text-secondary)] uppercase border border-[var(--border-color)]">
                                        {{ $payment->payment_method ?? 'M-Pesa' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-[var(--text-muted)] font-mono text-xs">{{ $payment->reference_number ?? $payment->payment_reference ?? '-' }}</td>
                                <td class="px-4 py-3.5 text-right font-black" style="color: var(--success);">
                                    {{ number_format($payment->amount, 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[var(--text-muted)]">Nenhum pagamento registado neste período.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $payments->links() }}
            </div>
        </div>

    </div>
@endsection