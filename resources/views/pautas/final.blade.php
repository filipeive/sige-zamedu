@extends('layouts.app')

@section('title', 'Pautas - Final')
@section('page-title', 'Pautas - Final')
@section('page-title-icon', 'fas fa-table')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Pautas</li>
@endsection

@section('title', 'Pauta Final - ' . $class->name)

@section('content')
<div class="space-y-4">
    <!-- Header com taxonomia MINEDH completa -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $class->education_level_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700 border border-slate-300">
                    {{ $class->cycle_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $class->grade_level_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                    Ano Lectivo {{ $year }}
                </span>
                @if($isExamClass)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-600 text-white shadow-2xs">
                        <i class="fas fa-file-signature mr-1"></i> Exame Nacional MINEDH
                    </span>
                @endif
                @if($class->isBasicEducation())
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fas fa-graduation-cap mr-1"></i> Educação Básica
                    </span>
                @endif
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-graduation-cap text-emerald-600"></i> Resultados Finais de Promoção: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Estatísticas finais de transição de ciclo — MINEDH Moçambique (Lei 18/2018).</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1 shadow-2xs">
                <a href="{{ route('pautas.trimestral', $class->id) }}" class="px-3 py-1.5 rounded-md text-xs font-bold text-slate-600 hover:text-emerald-700 transition no-underline">
                    <i class="fas fa-list-alt mr-1"></i> Trimestral
                </a>
                <a href="{{ route('pautas.anual', $class->id) }}" class="px-3 py-1.5 rounded-md text-xs font-bold text-slate-600 hover:text-emerald-700 transition no-underline">
                    <i class="fas fa-calendar-alt mr-1"></i> Anual
                </a>
                <a href="{{ route('pautas.final', $class->id) }}" class="px-3 py-1.5 rounded-md text-xs font-extrabold bg-amber-500 text-slate-900 shadow-2xs no-underline">
                    <i class="fas fa-graduation-cap mr-1"></i> Final
                </a>
                @if($isExamClass)
                    <a href="{{ route('pautas.exame', $class->id) }}" class="px-3 py-1.5 rounded-md text-xs font-bold text-rose-700 hover:bg-rose-50 transition no-underline">
                        <i class="fas fa-file-signature mr-1"></i> Exames & Júri
                    </a>
                @endif
            </div>

            <a href="{{ route('pautas.pdf', ['class' => $class->id, 'type' => 'final', 'year' => $year]) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-2xs transition">
                <i class="fas fa-file-pdf"></i> PDF Final
            </a>
            <a href="{{ route('classes.show', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Area de Filtros Organizada -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-4">
        <form method="GET" action="{{ route('pautas.final', $class->id) }}">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="fas fa-users text-emerald-600 mr-1"></i> Turma
                    </label>
                    <select name="class_id" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        @foreach($availableClasses as $c)
                            <option value="{{ $c->id }}" @selected($c->id === $class->id)>
                                {{ $c->name }} ({{ $c->grade_level_name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="fas fa-book text-emerald-600 mr-1"></i> Disciplina
                    </label>
                    <select name="subject_id" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="">{{ $isTeacher ? 'Todas (que lecciono)' : 'Todas as Disciplinas' }}</option>
                        @foreach($availableSubjects as $s)
                            <option value="{{ $s->id }}" @selected($selectedSubjectId === $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="fas fa-calendar text-emerald-600 mr-1"></i> Ano Lectivo
                    </label>
                    <select name="year" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                        <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026 (Ano Letivo Atual)</option>
                        <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025 (Histórico)</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabela Matriz da Pauta Final -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold uppercase tracking-wider text-[10px]">
                        <th rowspan="2" class="py-3 px-4 text-left bg-slate-900 border-r border-slate-800 min-w-[220px]">Nome do Aluno</th>
                        @foreach($subjects as $subject)
                            <th colspan="3" class="py-2 px-3 text-center border-r border-b border-slate-800 text-sky-300 font-extrabold">{{ $subject->code ?? $subject->name }}</th>
                        @endforeach
                        <th rowspan="2" class="py-3 px-3 text-center bg-emerald-800 text-white border-r border-slate-800 min-w-[60px]">M.G</th>
                        <th rowspan="2" class="py-3 px-4 text-center min-w-[120px]">Resultado Final</th>
                    </tr>
                    <tr class="bg-slate-800 text-slate-300 font-semibold text-[10px]">
                        @foreach($subjects as $subject)
                            <th class="py-1.5 px-2 text-center border-r border-slate-700">MAF</th>
                            <th class="py-1.5 px-2 text-center border-r border-slate-700">EX</th>
                            <th class="py-1.5 px-2 text-center border-r border-slate-700 font-bold text-amber-300">NFD</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($matrix as $row)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800 border-r border-slate-100">
                                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
                                <div class="text-slate-400 font-mono text-[10px]">N.º {{ $row['student']->student_number ?? 'N/A' }}</div>
                            </td>
                            @foreach($subjects as $subject)
                                @php 
                                    $subData = $row['subjects'][$subject->id] ?? []; 
                                    $nfd = $subData['mfd'] ?? '-';
                                    $nfdVal = is_numeric($nfd) ? (float) $nfd : null;
                                @endphp
                                <td class="py-2.5 px-2 text-center text-slate-500 border-r border-slate-100">{{ $subData['mf'] ?? '-' }}</td>
                                <td class="py-2.5 px-2 text-center text-slate-500 border-r border-slate-100">{{ $subData['exam'] ?? '-' }}</td>
                                <td class="py-2.5 px-2 text-center border-r border-slate-100 font-bold {{ $nfdVal !== null ? ($nfdVal >= 10 ? 'text-emerald-700 bg-emerald-50/60' : 'text-rose-700 bg-rose-50/60') : '' }}">
                                    {{ $nfd }}
                                </td>
                            @endforeach
                            <td class="py-2.5 px-3 text-center font-black bg-slate-50 text-slate-900 border-r border-slate-100">
                                {{ $row['overall_average'] > 0 ? number_format($row['overall_average'], 1) : '-' }}
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $row['final_status'] === 'Aprovado' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                    {{ strtoupper($row['final_status']) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (count($subjects) * 3) + 3 }}" class="py-8 text-center text-slate-400">
                                Sem registos de resultados finais para esta turma.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
        </div>
    </div>

    {{-- Assinaturas Oficiais MINEDH (DAP & DAE) --}}
    <x-official-signatures :year="$year" />
</div>
@endsection

