@extends('layouts.app')

@section('title', 'Registro de Presenças — ' . $student->first_name)
@section('page-title', 'Registo de Assiduidade')
@section('page-title-icon', 'fas fa-calendar-check')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}" class="no-underline text-slate-500">Alunos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}" class="no-underline text-slate-500">{{ $student->first_name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Presenças</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- 1. Hero Card do Aluno --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white p-6 shadow-md border border-slate-700/50">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 uppercase tracking-wider">
                        <i class="fas fa-id-badge me-1"></i> Cartão de Assiduidade
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-white/10 text-white border border-white/15">
                        Turma: {{ $student->enrollments->where('status', 'active')->first()->class->name ?? 'N/A' }}
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    <i class="fas fa-calendar-check text-emerald-400"></i> {{ $student->first_name }} {{ $student->last_name }}
                </h1>
                <p class="text-xs text-slate-300 mt-1">
                    Número de Matrícula: <strong class="font-mono text-white">{{ $student->student_number }}</strong> &bull;
                    Controlo de presenças, faltas, atrasos e registos biométricos de portaria.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('students.show', $student) }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition">
                    <i class="fas fa-arrow-left"></i> Voltar ao Perfil
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Estatísticas de Presença (KPI Cards) --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        {{-- Taxa de Presença --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 {{ $attendanceStats['attendance_rate'] >= 75 ? 'border-l-emerald-500' : 'border-l-amber-500' }} shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Taxa de Presença</span>
                <span class="text-xl sm:text-2xl font-black {{ $attendanceStats['attendance_rate'] >= 75 ? 'text-emerald-700' : 'text-amber-700' }} mt-0.5 block">
                    {{ $attendanceStats['attendance_rate'] }}%
                </span>
                <span class="text-[11px] font-semibold {{ $attendanceStats['attendance_rate'] >= 75 ? 'text-emerald-600' : 'text-amber-600' }} block mt-0.5">
                    {{ $attendanceStats['attendance_rate'] >= 75 ? 'Situação Regular' : 'Atenção Necessária' }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl {{ $attendanceStats['attendance_rate'] >= 75 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-percentage"></i>
            </div>
        </div>

        {{-- Presente --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Presenças Confirmadas</span>
                <span class="text-xl sm:text-2xl font-black text-slate-800 mt-0.5 block">{{ $attendanceStats['present'] }}</span>
                <span class="text-[11px] font-semibold text-emerald-600 block mt-0.5">Dias assistidos</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        {{-- Ausente / Faltas --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 border-l-rose-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Faltas Registadas</span>
                <span class="text-xl sm:text-2xl font-black text-rose-700 mt-0.5 block">{{ $attendanceStats['absent'] }}</span>
                <span class="text-[11px] font-semibold text-rose-600 block mt-0.5">Dias ausente</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-user-xmark"></i>
            </div>
        </div>

        {{-- Atrasos --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-200 border-l-4 border-l-amber-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Atrasos de Horário</span>
                <span class="text-xl sm:text-2xl font-black text-amber-700 mt-0.5 block">{{ $attendanceStats['late'] }}</span>
                <span class="text-[11px] font-semibold text-amber-600 block mt-0.5">Entradas tardias</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    {{-- 3. Barra de Filtros --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-2xs">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Mês</label>
                <select name="month" class="w-full rounded-xl border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os meses</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Ano</label>
                <select name="year" class="w-full rounded-xl border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    @for($i = current_school_year() - 2; $i <= current_school_year(); $i++)
                        <option value="{{ $i }}" {{ request('year', current_school_year()) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-2xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">Estado</label>
                <select name="status" class="w-full rounded-xl border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os estados</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Presente</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Ausente</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Atrasado</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                @if(request()->anyFilled(['month', 'year', 'status']))
                    <a href="{{ route('students.attendance', $student) }}" class="no-underline w-full inline-flex justify-center items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 py-2 px-3 text-xs font-bold text-slate-600 transition">
                        <i class="fas fa-times text-[10px]"></i> Limpar Filtros
                    </a>
                @else
                    <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 py-2 px-3 text-xs font-bold text-white transition shadow-2xs border-0 cursor-pointer">
                        <i class="fas fa-filter text-[10px]"></i> Filtrar
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. Tabela de Histórico de Presenças & Gráfico --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Histórico de Presenças (8 cols) --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-list text-emerald-600"></i> Histórico Detalhado de Registos
                    </h3>
                    <span class="text-[11px] font-bold text-slate-500">{{ $student->attendances->count() }} registo(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                <th class="py-3 px-4">Data &amp; Dia</th>
                                <th class="py-3 px-3">Turma</th>
                                <th class="py-3 px-3">Estado</th>
                                <th class="py-3 px-3">Hora / Portaria</th>
                                <th class="py-3 px-3">Registado Por</th>
                                <th class="py-3 px-4">Observações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($student->attendances as $attendance)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-800">{{ $attendance->attendance_date->format('d/m/Y') }}</div>
                                        <span class="text-[10px] text-slate-400 capitalize block">{{ $attendance->attendance_date->translatedFormat('l') }}</span>
                                    </td>
                                    <td class="py-3 px-3 font-semibold text-slate-700">
                                        {{ $attendance->class->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-3">
                                        @switch($attendance->status)
                                            @case('present')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <i class="fas fa-check text-[9px]"></i> Presente
                                                </span>
                                                @break
                                            @case('absent')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                    <i class="fas fa-times text-[9px]"></i> Ausente
                                                </span>
                                                @break
                                            @case('late')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <i class="fas fa-clock text-[9px]"></i> Atrasado
                                                </span>
                                                @break
                                            @case('excused')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                                    <i class="fas fa-umbrella text-[9px]"></i> Justificado
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="py-3 px-3 text-slate-600">
                                        @if($attendance->arrival_time)
                                            <span class="font-bold text-slate-800">{{ $attendance->arrival_time->format('H:i') }}</span>
                                            @if(str_contains($attendance->notes ?? '', 'Portaria Digital'))
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] font-bold ml-1">
                                                    <i class="fas fa-shield-alt text-[8px]"></i> Portaria
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-slate-600 font-medium">
                                        {{ $attendance->markedBy?->name ?? 'Sistema' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 text-[11px]">
                                        {{ $attendance->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        <i class="fas fa-calendar-times text-3xl text-slate-300 mb-2"></i>
                                        <p class="text-xs font-semibold text-slate-600">Nenhum registo de presença encontrado para os filtros selecionados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Gráfico & Painel de Frequência (4 cols) --}}
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <i class="fas fa-chart-pie text-emerald-600"></i> Distribuição Mensal
                </h3>
                <div>
                    <canvas id="attendanceChart" width="300" height="220"></canvas>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Total de Aulas</span>
                        <span class="font-extrabold text-slate-800">{{ $attendanceStats['total_classes'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Taxa Mínima Regulamentar</span>
                        <span class="font-extrabold text-slate-800">75%</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-slate-500 font-medium">Situação Académica</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $attendanceStats['attendance_rate'] >= 75 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                            {{ $attendanceStats['attendance_rate'] >= 75 ? 'Regular' : 'Atenção' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const presentCount = {{ $attendanceStats['present'] }};
    const absentCount = {{ $attendanceStats['absent'] }};
    const lateCount = {{ $attendanceStats['late'] }};
    const totalCount = presentCount + absentCount + lateCount;

    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: totalCount > 0 ? ['Presente', 'Ausente', 'Atrasado'] : ['Sem Registos'],
            datasets: [{
                data: totalCount > 0 ? [presentCount, absentCount, lateCount] : [1],
                backgroundColor: totalCount > 0 ? ['#059669', '#e11d48', '#d97706'] : ['#e2e8f0'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11, weight: 'bold' }
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endpush
