@extends('layouts.app')

@section('title', 'Relatorios Financeiros')
@section('page-title', 'Relatorios Financeiros')
@section('page-title-icon', 'fas fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}" class="no-underline"><i class="fas fa-wallet me-1"></i>Pagamentos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Relatorios</li>
@endsection

@section('content')
    <div class="payments-reports space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" class="grid gap-3 md:grid-cols-2 md:items-end">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Ano de Referencia</label>
                    <select name="year" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" onchange="this.form.submit()">
                        @for($y = current_school_year() - 2; $y <= current_school_year(); $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="md:text-right">
                    <button type="button" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 no-underline hover:bg-slate-50" onclick="exportReport('pdf')"><i class="fas fa-file-pdf me-2"></i>Exportar PDF</button>
                    <button type="button" class="ml-2 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white no-underline hover:bg-emerald-700" onclick="exportReport('excel')"><i class="fas fa-file-excel me-2"></i>Exportar Excel</button>
                </div>
            </form>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recebidos em {{ $year }}</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($stats['total_year'], 2, ',', '.') }} MT</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total em Divida</p>
                <p class="mt-2 text-3xl font-bold text-rose-700">{{ number_format($stats['total_pending'], 2, ',', '.') }} MT</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Alunos com Divida</p>
                <p class="mt-2 text-3xl font-bold text-amber-700">{{ $stats['total_students_debt'] }}</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <article class="xl:col-span-2 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">Receita Mensal - {{ $year }}</h3>
                <div class="h-[320px]"><canvas id="monthlyRevenueChart"></canvas></div>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">Receita por Tipo</h3>
                <div class="h-[320px]"><canvas id="revenueByTypeChart"></canvas></div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-5 py-4"><h3 class="text-sm font-semibold text-slate-900">Resumo Mensal</h3></header>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Mes</th>
                                <th class="px-3 py-2 text-right font-semibold">Valor Recebido</th>
                                <th class="px-3 py-2 text-center font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $meses = ['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
                            @endphp
                            @foreach($meses as $i => $mes)
                                @php $value = $monthlyRevenue[$i + 1] ?? 0; @endphp
                                <tr>
                                    <td class="px-3 py-2 text-slate-700">{{ $mes }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-slate-900">{{ number_format($value, 2, ',', '.') }} MT</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($value > 0)
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Recebido</span>
                                        @elseif($i + 1 <= date('n') && $year == current_school_year())
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Pendente</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-900">TOTAL</th>
                                <th class="px-3 py-2 text-right font-semibold text-slate-900">{{ number_format(array_sum($monthlyRevenue), 2, ',', '.') }} MT</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-5 py-4"><h3 class="text-sm font-semibold text-slate-900">Inadimplencia por Turma</h3></header>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Turma</th>
                                <th class="px-3 py-2 text-center font-semibold">Alunos</th>
                                <th class="px-3 py-2 text-right font-semibold">Valor em Divida</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($defaultersByClass as $item)
                                <tr>
                                    <td class="px-3 py-2 text-slate-700">{{ $item->name }}</td>
                                    <td class="px-3 py-2 text-center"><span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">{{ $item->count }}</span></td>
                                    <td class="px-3 py-2 text-right font-semibold text-rose-700">{{ number_format($item->total, 2, ',', '.') }} MT</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-3 py-6 text-center text-sm text-emerald-700">Nenhuma inadimplencia registrada.</td></tr>
                            @endforelse
                        </tbody>
                        @if($defaultersByClass->count() > 0)
                            <tfoot class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-900">TOTAL</th>
                                    <th class="px-3 py-2 text-center font-semibold text-slate-900">{{ $defaultersByClass->sum('count') }}</th>
                                    <th class="px-3 py-2 text-right font-semibold text-rose-700">{{ number_format($defaultersByClass->sum('total'), 2, ',', '.') }} MT</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </article>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-3 text-sm font-semibold text-slate-900">Acoes Rapidas</h3>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <a href="{{ route('payments.overdue') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-3 text-center text-sm text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-triangle-exclamation me-2 text-amber-500"></i>Pagamentos em Atraso</a>
                <a href="{{ route('payments.references') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-3 text-center text-sm text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-barcode me-2 text-sky-600"></i>Gerar Referencias</a>
                <a href="{{ route('payments.index', ['status' => 'paid']) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-3 text-center text-sm text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-circle-check me-2 text-emerald-600"></i>Pagamentos Confirmados</a>
                <a href="{{ route('reports.export.payments') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-3 text-center text-sm text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-download me-2 text-slate-500"></i>Exportar Dados</a>
            </div>
        </section>
    </div>
@endsection

@push('styles')
<style>
    [data-bs-theme="dark"] .payments-reports .bg-white { background-color: var(--card-bg) !important; }
    [data-bs-theme="dark"] .payments-reports .bg-slate-50 { background-color: rgba(148, 163, 184, 0.08) !important; }
    [data-bs-theme="dark"] .payments-reports .border-slate-100,
    [data-bs-theme="dark"] .payments-reports .border-slate-200,
    [data-bs-theme="dark"] .payments-reports .border-slate-300 { border-color: var(--border-color) !important; }
    [data-bs-theme="dark"] .payments-reports .text-slate-900,
    [data-bs-theme="dark"] .payments-reports .text-slate-800,
    [data-bs-theme="dark"] .payments-reports .text-slate-700,
    [data-bs-theme="dark"] .payments-reports .text-slate-600,
    [data-bs-theme="dark"] .payments-reports .text-slate-500 { color: var(--text-secondary) !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js indisponivel para payments.reports');
            return;
        }

        const monthlyData = @json($monthlyRevenue);
        const typeData = @json($revenueByType);
        const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        const monthCanvas = document.getElementById('monthlyRevenueChart');
        const typeCanvas = document.getElementById('revenueByTypeChart');
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const gridColor = isDark ? 'rgba(148,163,184,0.25)' : 'rgba(15,23,42,0.12)';
        const tickColor = isDark ? '#cbd5e1' : '#334155';

        if (monthCanvas) {
            new Chart(monthCanvas, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Receita (MT)',
                        data: months.map((_, i) => monthlyData[i + 1] || 0),
                        backgroundColor: 'rgba(25, 67, 124, 0.8)',
                        borderColor: '#19437C',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: tickColor }, grid: { color: gridColor } },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: tickColor,
                                callback: function(value) {
                                    return value.toLocaleString('pt-MZ') + ' MT';
                                }
                            },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        }

        const typeLabels = {
            matricula: 'Matricula',
            mensalidade: 'Mensalidade',
            material: 'Material',
            uniforme: 'Uniforme',
            outro: 'Outro'
        };

        if (typeCanvas) {
            const keys = Object.keys(typeData || {});
            const values = Object.values(typeData || {});
            const hasData = values.some(v => Number(v) > 0);

            new Chart(typeCanvas, {
                type: 'doughnut',
                data: {
                    labels: hasData ? keys.map(k => typeLabels[k] || k) : ['Sem dados'],
                    datasets: [{
                        data: hasData ? values : [1],
                        backgroundColor: hasData ? ['#19437C', '#4BA83C', '#F9A825', '#17a2b8', '#6c757d'] : ['#94a3b8'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 15, color: tickColor }
                        }
                    }
                }
            });
        }
    });

    function exportReport(format) {
        const year = document.querySelector('select[name="year"]').value;
        const target = format === 'pdf'
            ? `/reports/export/payments?year=${year}&format=pdf`
            : `/reports/export/payments?year=${year}&format=excel`;
        window.location.href = target;
    }
</script>
@endpush
