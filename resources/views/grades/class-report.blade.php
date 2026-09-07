@extends('layouts.app')

@section('title', 'Relatório de Aproveitamento — ' . $class->name)
@section('page-title', 'Relatório de Aproveitamento')
@section('page-title-icon', 'fas fa-chart-line')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}" class="no-underline">Turmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('classes.show', $class->id) }}" class="no-underline">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Relatório de Aproveitamento</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200 print:hidden">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-file-alt text-emerald-600"></i> Relatório de Aproveitamento: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Consolidado das Médias Trimestrais (MT1, MT2, MT3) por disciplina (Ano Lectivo: {{ $year }}).</p>
        </div>
        <div class="flex items-center gap-2">
            <!--pautas -->
            <button type="button" onclick="window.location.href='{{ route('pautas.anual', $class->id) }}'" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                <i class="fas fa-print"></i> Imprimir Pautas
            </button>
            <button type="button" onclick="window.print()" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition cursor-pointer">
                <i class="fas fa-print"></i> Imprimir Relatório
            </button>
            <a href="{{ route('classes.show', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar à Turma
            </a>
        </div>
    </div>

    <!-- Table Container Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden print:border-none print:shadow-none">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between print:hidden">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-table text-emerald-600"></i> Matriz de Aproveitamento por Aluno ({{ count($students) }} Estudantes)
            </h3>
            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                {{ $class->grade_level_name }} — {{ $class->shift_label }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse print:text-[10px]">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                        <th rowspan="2" class="p-2.5 ps-4 align-middle border-r border-slate-800" style="min-width: 200px;">Estudante</th>
                        @foreach($subjects as $subject)
                            <th colspan="3" class="p-1.5 text-center border-r border-b border-slate-800">
                                {{ $subject->code ?? substr($subject->name, 0, 10) }}
                            </th>
                        @endforeach
                        <th rowspan="2" class="p-2.5 text-center align-middle bg-emerald-900 text-amber-300 font-extrabold">Média Geral</th>
                    </tr>
                    <tr class="bg-slate-800 text-slate-300 font-semibold text-[9px]">
                        @foreach($subjects as $subject)
                            <th class="p-1 text-center border-r border-slate-700">T1</th>
                            <th class="p-1 text-center border-r border-slate-700">T2</th>
                            <th class="p-1 text-center border-r border-slate-700">T3</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        @php
                            $allMts = [];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-2.5 ps-4 border-r border-slate-100 font-bold text-slate-800">
                                <a href="{{ route('students.show', $student->id) }}" class="no-underline hover:no-underline font-bold text-slate-800 hover:text-emerald-700 transition">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </a>
                                <span class="block text-[10px] text-slate-400 font-mono font-normal">{{ $student->student_number }}</span>
                            </td>

                            @foreach($subjects as $subject)
                                @foreach($terms as $t)
                                    @php
                                        $val = $matrix[$student->id][$subject->id][$t] ?? null;
                                        if ($val !== null) {
                                            $allMts[] = $val;
                                        }
                                    @endphp
                                    <td class="p-1.5 text-center border-r border-slate-100 font-semibold {{ $val !== null ? ($val >= 10 ? 'text-emerald-700' : 'text-rose-600 bg-rose-50/50') : 'text-slate-400' }}">
                                        {{ $val !== null ? number_format($val, 1) : '-' }}
                                    </td>
                                @endforeach
                            @endforeach

                            <td class="p-2 text-center font-black bg-slate-50 text-slate-900">
                                @php
                                    $countMts = count($allMts);
                                    $overallAvg = $countMts > 0 ? round(array_sum($allMts) / $countMts, 1) : null;
                                @endphp
                                <span class="{{ $overallAvg !== null && $overallAvg >= 10 ? 'text-emerald-700' : ($overallAvg !== null ? 'text-rose-600' : 'text-slate-400') }}">
                                    {{ $overallAvg !== null ? number_format($overallAvg, 1) : '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($subjects) * 3 + 2 }}" class="p-6 text-center text-slate-400">
                                <i class="fas fa-users-slash text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum aluno matriculado nesta turma.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection