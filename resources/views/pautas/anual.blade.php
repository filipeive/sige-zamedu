@extends('layouts.app')

@section('title', 'Pautas - Anual')
@section('page-title', 'Pautas - Anual')
@section('page-title-icon', 'fas fa-table')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Pautas</li>
@endsection

@section('title', 'Pauta Anual - ' . $class->name)

@section('content')
<div class="space-y-5">
    {{-- 1. Header Principal com Taxonomia MINEDH Completa --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    <i class="fas fa-layer-group me-1"></i> Nível: {{ $class->education_level_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    <i class="fas fa-route me-1"></i> {{ $class->cycle_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $class->grade_level_name }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-calendar-alt me-1"></i> Ano Lectivo {{ $year }}
                </span>
                @if($isExamClass)
                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                        <i class="fas fa-file-signature text-rose-600 me-1"></i> Exame Nacional MINEDH
                    </span>
                @endif
                @if($class->isBasicEducation())
                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fas fa-graduation-cap text-blue-600 me-1"></i> Educação Básica (Obrigatória)
                    </span>
                @endif
            </div>

            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-calendar-alt text-emerald-600"></i> Pauta Anual Consolidada: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Apuramento de médias anuais de frequência (MAF) e notas finais de disciplina (NFD) — MINEDH Moçambique (Lei 18/2018).</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="inline-flex rounded-lg bg-slate-100 p-1 border border-slate-200">
                <a href="{{ route('pautas.trimestral', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-bold text-slate-700 hover:text-emerald-700">
                    <i class="fas fa-list-alt me-1"></i> Trimestral
                </a>
                <a href="{{ route('pautas.anual', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-extrabold bg-emerald-700 text-white shadow-2xs">
                    <i class="fas fa-calendar-alt me-1"></i> Anual
                </a>
                <a href="{{ route('pautas.final', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-bold text-slate-700 hover:text-emerald-700">
                    <i class="fas fa-graduation-cap me-1"></i> Final
                </a>
                @if($isExamClass)
                    <a href="{{ route('pautas.exame', $class->id) }}" class="no-underline px-3 py-1 rounded-md text-xs font-bold text-rose-700 hover:text-rose-800">
                        <i class="fas fa-file-signature me-1"></i> Exames &amp; Júri
                    </a>
                @endif
            </div>

            <a href="{{ route('pautas.pdf', ['class' => $class->id, 'type' => 'anual', 'year' => $year]) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-700 hover:bg-rose-800 text-xs font-bold text-white shadow-2xs transition">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('classes.show', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- 2. Área de Filtros Organizada --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-filter text-emerald-600 text-xs"></i>
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Filtros de Seleção &amp; Consulta</span>
        </div>
        <form method="GET" action="{{ route('pautas.anual', $class->id) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                    <i class="fas fa-users text-emerald-600 me-1"></i> Turma
                </label>
                <select name="class_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    @foreach($availableClasses as $c)
                        <option value="{{ $c->id }}" @selected($c->id === $class->id)>
                            {{ $c->name }} ({{ $c->grade_level_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                    <i class="fas fa-book text-emerald-600 me-1"></i> Disciplina
                </label>
                <select name="subject_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">{{ $isTeacher ? 'Todas (que lecciono)' : 'Todas as Disciplinas' }}</option>
                    @foreach($availableSubjects as $s)
                        <option value="{{ $s->id }}" @selected($selectedSubjectId === $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-2xs font-bold text-slate-500 uppercase mb-1">
                    <i class="fas fa-calendar text-emerald-600 me-1"></i> Ano Lectivo
                </label>
                <select name="year" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026 (Ano Letivo Atual)</option>
                    <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025 (Histórico)</option>
                </select>
            </div>
        </form>
    </div>

    {{-- 3. Tabela Principal Anual --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-center border-collapse">
                <thead class="bg-slate-800 text-white font-black uppercase tracking-wider">
                    <tr>
                        <th rowspan="2" class="py-3 px-4 text-left border-r border-slate-700 bg-slate-900 min-w-[240px]">Nome do Aluno</th>
                        @foreach($subjects as $subject)
                            <th colspan="{{ $isExamClass ? 6 : 4 }}" class="py-2 px-2 text-center border-r border-slate-700 bg-slate-900 border-b border-slate-700 text-sky-300 font-black">{{ $subject->code ?? $subject->name }}</th>
                        @endforeach
                        <th rowspan="2" class="py-3 px-3 text-center bg-emerald-800 text-white border-r border-emerald-900">Média Geral</th>
                        <th rowspan="2" class="py-3 px-3 text-center">Resultado Final</th>
                    </tr>
                    <tr class="bg-slate-700 text-slate-200 text-3xs uppercase tracking-wider">
                        @foreach($subjects as $subject)
                            <th class="py-1.5 px-2 bg-slate-700 font-semibold border-r border-slate-600">MT 1</th>
                            <th class="py-1.5 px-2 bg-slate-700 font-semibold border-r border-slate-600">MT 2</th>
                            <th class="py-1.5 px-2 bg-slate-700 font-semibold border-r border-slate-600">MT 3</th>
                            <th class="py-1.5 px-2 bg-amber-900 text-amber-200 font-bold border-r border-slate-600">MAF</th>
                            @if($isExamClass)
                                <th class="py-1.5 px-2 bg-rose-900 text-rose-200 font-bold border-r border-slate-600">NE</th>
                                <th class="py-1.5 px-2 bg-emerald-900 text-emerald-200 font-black border-r border-slate-600">NFD</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                    @forelse($matrix as $row)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 text-left font-black text-slate-900 border-r border-slate-100">
                                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
                                <div class="text-slate-400 text-2xs font-mono font-normal">N.º {{ $row['student']->student_number ?? 'N/A' }}</div>
                            </td>
                            @foreach($subjects as $subject)
                                @php 
                                    $subData = $row['subjects'][$subject->id] ?? []; 
                                    $nfd = $subData['mfd'] ?? '-';
                                    $nfdVal = is_numeric($nfd) ? (float) $nfd : null;
                                @endphp
                                <td class="py-3 px-2 text-slate-500 border-r border-slate-100">{{ $subData['mt1'] ?? '-' }}</td>
                                <td class="py-3 px-2 text-slate-500 border-r border-slate-100">{{ $subData['mt2'] ?? '-' }}</td>
                                <td class="py-3 px-2 text-slate-500 border-r border-slate-100">{{ $subData['mt3'] ?? '-' }}</td>
                                <td class="py-3 px-2 font-bold text-amber-800 bg-amber-50/70 border-r border-slate-100">{{ $subData['mf'] ?? '-' }}</td>
                                @if($isExamClass)
                                    <td class="py-3 px-2 font-bold text-rose-700 bg-rose-50/50 border-r border-slate-100">{{ $subData['exam'] ?? '-' }}</td>
                                    <td class="py-3 px-2 font-black border-r border-slate-100 {{ $nfdVal !== null ? ($nfdVal >= 9.5 ? 'text-emerald-700 bg-emerald-50/80' : 'text-rose-700 bg-rose-50/80') : '' }}">
                                        {{ $nfd }}
                                    </td>
                                @endif
                            @endforeach
                            <td class="py-3 px-3 font-black text-emerald-800 bg-emerald-50/50 border-r border-slate-100">
                                {{ $row['overall_average'] > 0 ? number_format($row['overall_average'], 1) : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($row['final_status'] === 'Aprovado')
                                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">Aprovado</span>
                                @elseif($row['final_status'] === 'Reprovado')
                                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-black bg-rose-100 text-rose-800 border border-rose-200">Reprovado</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-3xs font-black bg-amber-100 text-amber-800 border border-amber-200">Em Curso</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (count($subjects) * ($isExamClass ? 6 : 4)) + 3 }}" class="py-10 text-center text-slate-400">
                                <i class="fas fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                <span>Sem registos de notas anuais para esta turma.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Assinaturas Oficiais MINEDH (DAP & DAE) --}}
    <x-official-signatures :year="$year" />
</div>
@endsection
