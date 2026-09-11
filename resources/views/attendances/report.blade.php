@extends('layouts.app')

@section('title', 'Relatório de Presenças')
@section('page-title', 'Relatório de Presenças')

@section('content')
<div class="w-full mx-auto px-4 py-6 space-y-6">

    <!-- Filtros de Relatório -->
    <div class="w-full mx-auto bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
            <i class="fa-solid fa-filter text-emerald-600"></i> Parâmetros do Relatório
        </h3>
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Turma</label>
                    <select name="class_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="">Todas as turmas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Período</label>
                    <select name="period" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white">
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hoje</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Esta Semana</option>
                        <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Este Mês</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Gerar Relatório
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Gráficos de Presenças -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-line text-emerald-600"></i> Frequência por Dia
                </h3>
                <div class="relative w-full" style="height: 280px;">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-pie text-emerald-600"></i> Distribuição Geral
                </h3>
                <div class="relative w-full flex items-center justify-center" style="height: 280px;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de linha
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex'],
            datasets: [{
                label: 'Presentes',
                data: [95, 92, 96, 90, 88],
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                tension: 0.4
            }, {
                label: 'Ausentes',
                data: [5, 8, 4, 10, 12],
                borderColor: '#e11d48',
                backgroundColor: 'rgba(225, 29, 72, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de Pizza
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Presentes', 'Ausentes', 'Atrasados'],
            datasets: [{
                data: [85, 10, 5],
                backgroundColor: ['#059669', '#e11d48', '#d97706']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endpush