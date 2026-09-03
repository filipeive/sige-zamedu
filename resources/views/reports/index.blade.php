@extends('layouts.app')

@section('title', 'Central de Relatórios & Analytics')
@section('page-title', 'Central de Relatórios')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Relatórios</li>
@endsection

@section('content')
    <div class="space-y-6 bg-[#F4F6FA] -m-4 p-6 rounded-3xl min-h-screen">

        <!-- Top Header Banner MOPHY Style -->
        <div class="rounded-3xl bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 backdrop-blur-md px-3 py-1 text-xs font-bold text-amber-300 border border-white/20">
                    <i class="fas fa-file-invoice"></i> Central ZamEdu Analytics
                </span>
                <h2 class="mt-2 text-2xl font-black font-heading text-white">Relatórios Estratégicos</h2>
                <p class="text-xs text-emerald-100 max-w-lg mt-0.5">
                    Aceda a balanços financeiros, pautas académicas, relatórios de assiduidade e ferramentas de exportação em tempo real.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('reports.export.students') }}" class="inline-flex items-center gap-2 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md px-4 py-2 text-xs font-bold text-white border border-white/30 transition">
                    <i class="fas fa-download"></i> Alunos (CSV)
                </a>
                @if(is_private_school())
                <a href="{{ route('reports.export.payments') }}" class="inline-flex items-center gap-2 rounded-full bg-white text-emerald-800 hover:bg-emerald-50 px-4 py-2 text-xs font-black shadow-md transition">
                    <i class="fas fa-file-excel text-emerald-600"></i> Pagamentos (CSV)
                </a>
                @endif
            </div>
        </div>

        <!-- Stat Summary Cards Grid (Pílula Vertical de Cor na Esquerda) -->
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
                <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
                <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos Ativos</span>
                    <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($stats['total_students']) }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
                <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
                <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Professores</span>
                    <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($stats['total_teachers']) }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
                <div class="w-1.5 h-8 rounded-full bg-purple-500 shrink-0"></div>
                <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas fa-school"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas Ativas</span>
                    <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($stats['total_classes']) }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
                <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
                <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                    <i class="fas {{ is_public_school() ? 'fa-landmark' : 'fa-wallet' }}"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">{{ is_public_school() ? 'Modo Gestão' : 'Receita do Mês' }}</span>
                    <span class="text-xl font-black text-slate-800 mt-0.5 block">
                        @if(is_public_school())
                            Pública
                        @else
                            {{ number_format($stats['revenue_this_month'] ?? 0, 0, ',', '.') }} MT
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Dashboard Analítico (Chart.js) -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Gráfico 1: Evolução Financeira -->
            @if(is_private_school())
            <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800"><i class="fas fa-chart-line text-emerald-600 me-2"></i>Evolução de Arrecadação</h3>
                        <p class="text-xs text-slate-400">Receita mensal arrecadada nos últimos 6 meses</p>
                    </div>
                    <span class="badge bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Financeiro</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Gráfico 2: Demografia por Género -->
            <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800"><i class="fas fa-users text-blue-600 me-2"></i>Distribuição por Género</h3>
                        <p class="text-xs text-slate-400">Proporção de estudantes masculinos e femininos</p>
                    </div>
                    <span class="badge bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Estudantes</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <!-- Gráfico 3: Turmas por Classe -->
            <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800"><i class="fas fa-layer-group text-purple-600 me-2"></i>Turmas por Nível de Ensino</h3>
                        <p class="text-xs text-slate-400">Distribuição de turmas ativas da 1ª à 12ª classe</p>
                    </div>
                    <span class="badge bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">Académico</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="gradeDistributionChart"></canvas>
                </div>
            </div>

            <!-- Gráfico 4: Assiduidade Global -->
            <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800"><i class="fas fa-calendar-check text-amber-600 me-2"></i>Taxa Global de Assiduidade</h3>
                        <p class="text-xs text-slate-400">Proporção total de presenças, ausências e atrasos</p>
                    </div>
                    <span class="badge bg-amber-50 text-amber-700 px-3 py-1 rounded-full text-xs font-bold">Frequência</span>
                </div>
                <div style="height: 250px;">
                    <canvas id="globalAttendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- MOPHY Report Category Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            
            <!-- Category 1: Académico -->
            <div class="rounded-3xl bg-white p-6 shadow-[0_10px_30px_rgba(0,0,0,0.03)] border-0 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
                        <div class="rounded-2xl bg-emerald-100/70 p-3 text-emerald-600 text-lg">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 font-heading">Relatórios Académicos</h3>
                            <p class="text-xs text-slate-400">Acompanhamento do rendimento escolar e assiduidade</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('reports.academic') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-emerald-50/70 hover:text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white p-2 text-emerald-600 shadow-sm"><i class="fas fa-list-ul text-xs"></i></span>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-800">Visão Geral Académica</span>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>

                        <a href="{{ route('reports.academic.performance') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-emerald-50/70 hover:text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white p-2 text-emerald-600 shadow-sm"><i class="fas fa-chart-line text-xs"></i></span>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-800">Desempenho por Disciplina & Alunos</span>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>

                        <a href="{{ route('reports.academic.attendance') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-emerald-50/70 hover:text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white p-2 text-teal-600 shadow-sm"><i class="fas fa-calendar-check text-xs"></i></span>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-800">Relatório de Assiduidade</span>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Category 2: Financeiro -->
            @if(is_private_school())
            <div class="rounded-3xl bg-white p-6 shadow-[0_10px_30px_rgba(0,0,0,0.03)] border-0 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
                        <div class="rounded-2xl bg-emerald-100/70 p-3 text-emerald-600 text-lg">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 font-heading">Relatórios Financeiros</h3>
                            <p class="text-xs text-slate-400">Balanço de mensalidades, cobranças e inadimplência</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('reports.financial') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-emerald-50/70 hover:text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white p-2 text-emerald-600 shadow-sm"><i class="fas fa-receipt text-xs"></i></span>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-800">Visão Geral Financeira</span>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>

                        <a href="{{ route('reports.revenue') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-emerald-50/70 hover:text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white p-2 text-emerald-600 shadow-sm"><i class="fas fa-money-bill-wave text-xs"></i></span>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-800">Receita por Categoria & Mês</span>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>

                        <a href="{{ route('reports.defaulters') }}" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 hover:bg-emerald-50/70 hover:text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <span class="rounded-xl bg-white p-2 text-rose-600 shadow-sm"><i class="fas fa-exclamation-triangle text-xs"></i></span>
                                <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-800">Relatório de Inadimplência</span>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Gráfico de Evolução Financeira
    new Chart(document.getElementById('revenueTrendChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($monthsLabels),
            datasets: [{
                label: 'Receita (MT)',
                data: @json($monthlyRevenueTrend),
                borderColor: '#047857',
                backgroundColor: 'rgba(4, 120, 87, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#047857'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 2. Gráfico de Género
    const maleCount = {{ $genderStats['male'] }};
    const femaleCount = {{ $genderStats['female'] }};
    new Chart(document.getElementById('genderChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Feminino'],
            datasets: [{
                data: (maleCount + femaleCount > 0) ? [maleCount, femaleCount] : [1, 1],
                backgroundColor: ['#2563eb', '#ec4899']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 3. Gráfico de Distribuição por Nível de Ensino
    const gradeData = @json($gradeDistribution);
    const gradeLabels = Object.keys(gradeData).map(k => k + 'ª Classe');
    const gradeValues = Object.values(gradeData);

    new Chart(document.getElementById('gradeDistributionChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: gradeLabels.length > 0 ? gradeLabels : ['1ª Classe', '2ª Classe', '3ª Classe'],
            datasets: [{
                label: 'Turmas Ativas',
                data: gradeValues.length > 0 ? gradeValues : [1, 1, 1],
                backgroundColor: '#8b5cf6',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 4. Gráfico de Assiduidade Global
    const attPresent = {{ $attendanceStats['present'] }};
    const attAbsent = {{ $attendanceStats['absent'] }};
    const attLate = {{ $attendanceStats['late'] }};
    const attTotal = attPresent + attAbsent + attLate;

    new Chart(document.getElementById('globalAttendanceChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: attTotal > 0 ? ['Presente', 'Ausente', 'Atrasado'] : ['Sem Registos'],
            datasets: [{
                data: attTotal > 0 ? [attPresent, attAbsent, attLate] : [1],
                backgroundColor: attTotal > 0 ? ['#10b981', '#ef4444', '#f59e0b'] : ['#cbd5e1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
@endpush