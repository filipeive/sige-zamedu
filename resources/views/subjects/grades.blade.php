@extends('layouts.app')

@section('title', 'Notas da Disciplina')
@section('page-title', 'Notas da Disciplina')
@section('page-title-icon', 'fas fa-medal')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Disciplinas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.show', $subject->id) }}">{{ $subject->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Notas</li>
@endsection

@section('title', 'Notas - ' . $subject->name)
@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-medal text-emerald-600"></i> Notas de {{ $subject->name }} ({{ $subject->code }})
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Nível: <strong>{{ $gradeLevels[$subject->grade_level] ?? $subject->grade_level }}</strong> | 
                Total de Notas: <strong>{{ $gradeStats['total_grades'] }}</strong> | 
                Taxa de Aprovação: <strong>{{ $gradeStats['approval_rate'] }}%</strong>
            </p>
        </div>
        <a href="{{ route('subjects.show', $subject->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Disciplina
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-calculator"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Média Geral</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($gradeStats['average_grade'], 1) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Taxa Aprovação</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $gradeStats['approval_rate'] }}%</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Nota Máxima</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($gradeStats['max_grade'], 1) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-rose-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Nota Mínima</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($gradeStats['min_grade'], 1) }}</span>
            </div>
        </div>
    </div>

    <!-- Charts & Category Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-chart-pie text-emerald-600"></i> Distribuição de Notas
            </h3>
            <div class="max-w-[280px] mx-auto">
                <canvas id="gradeDistributionChart" width="220" height="220"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Resumo por Categoria
            </h3>
            <div class="divide-y divide-slate-100 text-xs">
                <div class="py-2.5 flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">Excelente (14-20)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">{{ $gradeDistribution['excellent'] }}</span>
                </div>
                <div class="py-2.5 flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">Bom (12-13.9)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">{{ $gradeDistribution['good'] }}</span>
                </div>
                <div class="py-2.5 flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">Suficiente (10-11.9)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">{{ $gradeDistribution['sufficient'] }}</span>
                </div>
                <div class="py-2.5 flex justify-between items-center">
                    <span class="text-slate-600 font-semibold">Insuficiente (0-9.9)</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">{{ $gradeDistribution['insufficient'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Período</label>
                <select name="term" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os períodos</option>
                    <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>1º Período</option>
                    <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>2º Período</option>
                    <option value="3" {{ request('term') == '3' ? 'selected' : '' }}>3º Período</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Ano</label>
                <select name="year" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    @for($i = current_school_year() - 2; $i <= current_school_year(); $i++)
                        <option value="{{ $i }}" {{ request('year', current_school_year()) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Tipo Avaliação</label>
                <select name="assessment_type" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="continuous" {{ request('assessment_type') == 'continuous' ? 'selected' : '' }}>Contínua</option>
                    <option value="test" {{ request('assessment_type') == 'test' ? 'selected' : '' }}>Teste</option>
                    <option value="exam" {{ request('assessment_type') == 'exam' ? 'selected' : '' }}>Exame</option>
                </select>
            </div>
            <div>
                <a href="{{ route('subjects.grades', $subject->id) }}" class="no-underline w-full inline-flex justify-center items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 py-1.5 px-3 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-times"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Histórico de Notas ({{ $subject->grades->count() }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Aluno</th>
                        <th class="py-2.5 px-3">Turma</th>
                        <th class="py-2.5 px-3">Nota</th>
                        <th class="py-2.5 px-3">Tipo</th>
                        <th class="py-2.5 px-3">Período</th>
                        <th class="py-2.5 px-3">Ano</th>
                        <th class="py-2.5 px-3">Data</th>
                        <th class="py-2.5 px-3">Professor</th>
                        <th class="py-2.5 px-3">Observações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subject->grades as $grade)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                <span>{{ $grade->student->first_name }} {{ $grade->student->last_name }}</span>
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $grade->student->student_number }}</span>
                            </td>
                            <td class="py-2.5 px-3">
                                @php $enrollment = $grade->student->enrollments->where('status', 'active')->first(); @endphp
                                @if($enrollment)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $enrollment->class->name }}</span>
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $grade->grade >= 10 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                    {{ number_format($grade->grade, 1) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                @switch($grade->assessment_type)
                                    @case('continuous') Contínua @break
                                    @case('test') Teste @break
                                    @case('exam') Exame @break
                                    @case('final') Final @break
                                    @default {{ $grade->assessment_type }}
                                @endswitch
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $grade->term }}º Período</td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $grade->year }}</td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $grade->date_recorded->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $grade->teacher ? $grade->teacher->first_name . ' ' . $grade->teacher->last_name : 'N/A' }}</td>
                            <td class="py-2.5 px-3 text-slate-500">{{ $grade->comments ? Str::limit($grade->comments, 30) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-6 text-center text-slate-400">
                                <i class="fas fa-medal text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma nota registrada para esta disciplina.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top 10 Best Grades -->
    @if($subject->grades->count() > 0)
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="fas fa-trophy text-amber-500"></i> Top 10 Melhores Notas
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            @foreach($subject->grades->sortByDesc('grade')->take(10) as $index => $grade)
                <div class="p-3 rounded-lg border border-slate-200/80 bg-slate-50/50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $index < 3 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-200 text-slate-700' }}">
                                {{ $index + 1 }}º
                            </span>
                            <h4 class="text-xs font-bold text-slate-800 truncate">{{ $grade->student->first_name }} {{ $grade->student->last_name }}</h4>
                        </div>
                        <p class="text-[10px] text-slate-400">
                            {{ $grade->student->student_number }}
                            @php $enrollment = $grade->student->enrollments->where('status', 'active')->first(); @endphp
                            @if($enrollment) | {{ $enrollment->class->name }} @endif
                        </p>
                    </div>
                    <div class="mt-2 pt-2 border-t border-slate-200/60 flex justify-between items-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            {{ number_format($grade->grade, 1) }}
                        </span>
                        <span class="text-[9px] text-slate-400">{{ $grade->date_recorded->format('d/m/Y') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('gradeDistributionChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Excelente (14-20)', 'Bom (12-13.9)', 'Suficiente (10-11.9)', 'Insuficiente (0-9.9)'],
                datasets: [{
                    data: [
                        {{ $gradeDistribution['excellent'] }},
                        {{ $gradeDistribution['good'] }},
                        {{ $gradeDistribution['sufficient'] }},
                        {{ $gradeDistribution['insufficient'] }}
                    ],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
