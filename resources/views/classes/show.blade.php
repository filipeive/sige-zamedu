@extends('layouts.app')

@section('title', 'Classes - Show')
@section('page-title', 'Classes - Show')
@section('page-title-icon', 'fas fa-chalkboard-user')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}" class="no-underline">Turmas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">{{ $class->name }}</li>
@endsection

@section('title', 'Turma ' . $class->name)
@section('page-title', 'Turma ' . $class->name)
@section('content')
<div class="space-y-4" x-data="{ tab: 'resumo', birthdays: false }">

    {{-- ═══ HERO HEADER ═══ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white p-5 shadow-lg">
        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -left-6 -bottom-10 w-32 h-32 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-1.5 mb-2">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 text-[10px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-layer-group me-1"></i>{{ $class->grade_level_name }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-slate-100 border border-white/15 text-[10px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-clock me-1"></i>{{ $class->shift_label }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-slate-100 border border-white/15 text-[10px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-calendar me-1"></i>{{ $class->school_year }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider border
                        {{ $class->is_active
                            ? 'bg-emerald-500/20 text-emerald-200 border-emerald-400/30'
                            : 'bg-slate-500/20 text-slate-300 border-slate-400/30' }}">
                        <i class="fa-solid fa-circle text-[6px] me-1 align-middle"></i>{{ $class->is_active ? 'Activa' : 'Inactiva' }}
                    </span>
                    @if($class->isNationalExamGrade())
                        <span class="px-2 py-0.5 rounded-md bg-amber-400/20 text-amber-200 border border-amber-400/30 text-[10px] font-extrabold uppercase tracking-wider">
                            <i class="fa-solid fa-file-signature me-1"></i>Exame Nacional
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-black tracking-tight">{{ $class->name }}</h1>
                <p class="text-xs text-slate-300 mt-1">
                    @if($class->isSingleTeacher())
                        <i class="fa-solid fa-user-tie me-1 text-sky-300"></i>Regime de Professor Regente
                    @else
                        <i class="fa-solid fa-book-open me-1 text-purple-300"></i>Regime de Atribuição por Disciplina
                    @endif
                    <span class="mx-1 text-slate-500">|</span>
                    <i class="fa-solid fa-door-open me-1"></i>{{ $class->classroom ?? 'Sala por definir' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @can('create_grades')
                    <a href="{{ route('grades.index', ['class_id' => $class->id]) }}"
                       class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-md transition">
                        <i class="fa-solid fa-pen-to-square"></i> Lançar Notas
                    </a>
                @endcan
                @can('view_grades')
                    <a href="{{ route('pautas.trimestral', $class->id) }}"
                       class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition">
                        <i class="fa-solid fa-table-list"></i> Pauta
                    </a>
                @endcan
                @can('edit_classes')
                    <a href="{{ route('classes.edit', $class->id) }}"
                       class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-xs font-bold text-slate-900 shadow-md transition">
                        <i class="fa-solid fa-sliders"></i> Configurar
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- ═══ MÉTRICAS ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ocupação</span>
                <i class="fa-solid fa-users text-emerald-500 text-xs"></i>
            </div>
            <div class="mt-1.5 flex items-baseline gap-1">
                <span class="text-2xl font-black text-slate-800">{{ $stats['total_students'] }}</span>
                <span class="text-[11px] font-bold text-slate-400">/ {{ $class->max_students }}</span>
            </div>
            <div class="mt-2 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $stats['capacity_percentage'] > 85 ? 'bg-rose-500' : ($stats['capacity_percentage'] > 65 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                     style="width: {{ min(100, $stats['capacity_percentage']) }}%"></div>
            </div>
            <p class="mt-1 text-[10px] font-semibold text-slate-400">
                {{ $class->available_slots }} vaga(s) disponível(is)
            </p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Docentes</span>
                <i class="fa-solid fa-chalkboard-user text-purple-500 text-xs"></i>
            </div>
            <div class="mt-1.5 text-2xl font-black text-slate-800">
                {{ $class->isSingleTeacher() ? (($class->teacher ? 1 : 0) + ($class->assistantTeacher ? 1 : 0)) : $class->classSubjects->pluck('teacher_id')->filter()->unique()->count() }}
            </div>
            <p class="mt-1 text-[10px] font-semibold text-slate-400">
                {{ $class->isSingleTeacher() ? 'Regente + Assistente' : 'Atribuídos por disciplina' }}
            </p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Distribuição</span>
                <i class="fa-solid fa-venus-mars text-sky-500 text-xs"></i>
            </div>
            <div class="mt-1.5 flex items-center gap-1.5">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200 text-xs font-black">
                    <i class="fa-solid fa-mars text-[10px]"></i> {{ $stats['male_students'] }}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-pink-50 text-pink-700 border border-pink-200 text-xs font-black">
                    <i class="fa-solid fa-venus text-[10px]"></i> {{ $stats['female_students'] }}
                </span>
            </div>
            <p class="mt-1 text-[10px] font-semibold text-slate-400">Idade média: {{ (int) round($stats['average_age']) }} anos</p>
        </div>

        <button type="button" @click="birthdays = true"
                class="text-left bg-white rounded-xl border border-amber-200 p-4 shadow-xs hover:border-amber-400 hover:shadow-sm transition relative">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700">Aniversariantes</span>
                <i class="fa-solid fa-cake-candles text-amber-500 text-xs"></i>
            </div>
            <div class="mt-1.5 text-2xl font-black text-slate-800">{{ $upcomingBirthdays->count() }}</div>
            <p class="mt-1 text-[10px] font-semibold text-slate-400">Próximos 30 dias</p>
            @if($upcomingBirthdays->count() > 0)
                <span class="absolute top-3 right-3 w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            @endif
        </button>
    </div>

    {{-- ═══ ABAS ═══ --}}
    <div class="border-b border-slate-200">
        <nav class="flex gap-1 -mb-px overflow-x-auto" role="tablist">
            @php
                $tabs = [
                    'resumo'    => ['Resumo', 'fa-solid fa-gauge-high'],
                    'alunos'    => ['Alunos (' . $stats['total_students'] . ')', 'fa-solid fa-user-graduate'],
                    'disciplinas'=> ['Disciplinas (' . $class->classSubjects->count() . ')', 'fa-solid fa-book-open'],
                ];
            @endphp
            @foreach($tabs as $key => [$label, $icon])
                <button type="button" @click="tab = '{{ $key }}'" role="tab"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold whitespace-nowrap border-b-2 transition"
                        :class="tab === '{{ $key }}'
                            ? 'border-emerald-600 text-emerald-700'
                            : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'">
                    <i class="{{ $icon }}"></i> {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- ═══ TAB: RESUMO ═══ --}}
    <div x-show="tab === 'resumo'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i> Dados da Turma
                    </h3>
                </div>
                <dl class="p-4 space-y-0 text-xs divide-y divide-slate-100">
                    @foreach([
                        ['Nome oficial', $class->name, 'fa-solid fa-tag'],
                        ['Nível', $class->grade_level_name, 'fa-solid fa-layer-group'],
                        ['Ciclo', $class->cycle_name, 'fa-solid fa-diagram-project'],
                        ['Turno', $class->shift_label, 'fa-solid fa-clock'],
                        ['Ano lectivo', $class->school_year, 'fa-solid fa-calendar'],
                        ['Sala', $class->classroom ?? 'Por definir', 'fa-solid fa-door-open'],
                        ['Capacidade', $class->max_students . ' alunos', 'fa-solid fa-user-group'],
                    ] as [$label, $value, $icon])
                        <div class="flex items-center justify-between gap-3 py-2">
                            <dt class="flex items-center gap-1.5 text-slate-500 font-semibold">
                                <i class="{{ $icon }} text-slate-400 text-[10px] w-3"></i> {{ $label }}
                            </dt>
                            <dd class="font-black text-slate-800 text-end truncate">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-book text-purple-600"></i> Disciplinas
                    </h3>
                </div>
                <div class="p-4 flex flex-wrap gap-1.5">
                    @forelse($class->subjects as $sub)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-50 border border-slate-200 text-[11px] font-bold text-slate-700">
                            <i class="fa-solid fa-book text-[9px] text-emerald-600"></i> {{ $sub->name }}
                        </span>
                    @empty
                        <p class="text-[11px] text-slate-400 italic w-full">Nenhuma disciplina associada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            {{-- Regente --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-chalkboard-user text-emerald-600"></i> Corpo Docente
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black border
                        {{ $class->isSingleTeacher() ? 'bg-sky-100 text-sky-800 border-sky-200' : 'bg-purple-100 text-purple-800 border-purple-200' }}">
                        {{ $class->isSingleTeacher() ? 'Regente' : 'Por Disciplina' }}
                    </span>
                </div>

                @if($class->isSingleTeacher())
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach([
                            ['Regente', $class->teacher, 'bg-emerald-600', true],
                            ['Assistente', $class->assistantTeacher, 'bg-sky-600', false],
                        ] as [$role, $teacher, $avatarClass, $required])
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/60">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">{{ $role }}</span>
                                    @if($required && ! $teacher)
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-amber-100 text-amber-700">Obrigatório</span>
                                    @endif
                                </div>
                                @if($teacher)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-lg {{ $avatarClass }} text-white font-black text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(mb_substr($teacher->first_name ?? '?', 0, 1) . mb_substr($teacher->last_name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('teachers.show', $teacher->id) }}"
                                               class="no-underline font-black text-xs text-slate-800 hover:text-emerald-700 block truncate">
                                                {{ $teacher->full_name }}
                                            </a>
                                            <p class="text-[10px] text-slate-500 truncate">{{ $teacher->email ?? 'Sem email' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-[11px] text-slate-400 italic">Não atribuído</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @forelse($class->classSubjects as $cs)
                            <div class="px-4 py-2.5 flex items-center gap-3">
                                <i class="fa-solid fa-book text-emerald-600 text-xs w-4 shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $cs->subject?->name ?? '—' }}</p>
                                    <p class="text-[10px] font-mono text-slate-400">{{ $cs->subject?->code ?? '—' }}</p>
                                </div>
                                @if($cs->teacher)
                                    <a href="{{ route('teachers.show', $cs->teacher->id) }}"
                                       class="no-underline inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-700 hover:text-emerald-700">
                                        <span class="w-5 h-5 rounded bg-emerald-700 text-white text-[9px] font-black flex items-center justify-center">
                                            {{ strtoupper(mb_substr($cs->teacher->first_name ?? '?', 0, 1) . mb_substr($cs->teacher->last_name ?? '?', 0, 1)) }}
                                        </span>
                                        <span class="truncate max-w-[10rem]">{{ $cs->teacher->full_name }}</span>
                                    </a>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-black">
                                        Pendente
                                    </span>
                                @endif
                            </div>
                        @empty
                            <p class="p-6 text-center text-[11px] text-slate-400 italic">Nenhuma disciplina associada.</p>
                        @endforelse
                    </div>
                @endif
            </div>

            {{-- Acções rápidas --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach([
                    ['classes.students', $class->id, 'Lista de Alunos', 'fa-solid fa-user-graduate', 'text-emerald-700 bg-emerald-50 border-emerald-200'],
                    ['attendances.mark-by-class', $class->id, 'Presenças', 'fa-solid fa-clipboard-check', 'text-sky-700 bg-sky-50 border-sky-200'],
                    ['pautas.trimestral', $class->id, 'Pauta Trimestral', 'fa-solid fa-table-list', 'text-teal-700 bg-teal-50 border-teal-200'],
                ] as [$route, $param, $label, $icon, $classes])
                    <a href="{{ route($route, $param) }}"
                       class="no-underline flex flex-col items-center gap-1.5 p-3.5 rounded-xl border {{ $classes }} hover:shadow-sm transition">
                        <i class="{{ $icon }} text-base"></i>
                        <span class="text-[11px] font-black text-center leading-tight">{{ $label }}</span>
                    </a>
                @endforeach
                <a href="{{ route('classes.edit', $class->id) }}"
                   class="no-underline flex flex-col items-center gap-1.5 p-3.5 rounded-xl border text-amber-700 bg-amber-50 border-amber-200 hover:shadow-sm transition">
                    <i class="fa-solid fa-sliders text-base"></i>
                    <span class="text-[11px] font-black text-center leading-tight">Configurar</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ TAB: ALUNOS ═══ --}}
    <div x-show="tab === 'alunos'" x-cloak class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-user-graduate text-emerald-600"></i> Alunos Matriculados
            </h3>
            <a href="{{ route('classes.students', $class->id) }}" class="no-underline text-[11px] font-bold text-emerald-700 hover:text-emerald-800">
                Gerir matrículas <i class="fa-solid fa-arrow-right text-[9px]"></i>
            </a>
        </div>

        @if($stats['total_students'] > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/60 text-slate-500 font-black uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <th class="py-2.5 px-4">Aluno</th>
                            <th class="py-2.5 px-3">Nº Processo</th>
                            <th class="py-2.5 px-3">Género</th>
                            <th class="py-2.5 px-3">Idade</th>
                            <th class="py-2.5 px-4 text-end">Ficha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($class->students->take(12) as $student)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-black text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(mb_substr($student->first_name ?? '?', 0, 1) . mb_substr($student->last_name ?? '?', 0, 1)) }}
                                        </div>
                                        <a href="{{ route('students.show', $student->id) }}"
                                           class="no-underline font-bold text-slate-800 hover:text-emerald-700">
                                            {{ $student->full_name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 font-mono font-semibold text-slate-600">{{ $student->student_number ?? '—' }}</td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black border
                                        {{ $student->gender == 'male' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-pink-50 text-pink-700 border-pink-200' }}">
                                        <i class="fa-solid {{ $student->gender == 'male' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                                        {{ $student->gender == 'male' ? 'M' : 'F' }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 font-bold text-slate-700">
                                    {{ $student->birthdate ? $student->age . ' anos' : 'N/I' }}
                                </td>
                                <td class="py-2.5 px-4 text-end">
                                    <a href="{{ route('students.show', $student->id) }}"
                                       class="no-underline inline-flex items-center gap-1 px-2 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold">
                                        <i class="fa-regular fa-id-card text-[9px]"></i> Abrir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($class->students->count() > 12)
                <div class="px-4 py-2.5 border-t border-slate-100 text-center bg-slate-50/50">
                    <a href="{{ route('classes.students', $class->id) }}" class="no-underline text-[11px] font-bold text-emerald-700 hover:text-emerald-800">
                        Ver mais {{ $class->students->count() - 12 }} aluno(s)
                    </a>
                </div>
            @endif
        @else
            <div class="p-10 text-center">
                <i class="fa-solid fa-user-graduate text-3xl text-slate-200"></i>
                <p class="mt-2 text-xs font-bold text-slate-600">Nenhum aluno matriculado nesta turma.</p>
                <a href="{{ route('classes.students', $class->id) }}"
                   class="no-underline mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white">
                    <i class="fa-solid fa-user-plus"></i> Matricular Alunos
                </a>
            </div>
        @endif
    </div>

    {{-- ═══ TAB: DISCIPLINAS (matriz de atribuição) ═══ --}}
    <div x-show="tab === 'disciplinas'" x-cloak class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-book-open text-purple-600"></i> Matriz de Disciplinas
            </h3>
            @can('edit_classes')
                <a href="{{ route('classes.edit', $class->id) }}" class="no-underline text-[11px] font-bold text-purple-700 hover:text-purple-800">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                </a>
            @endcan
        </div>

        @can('edit_classes')
            <form method="POST" action="{{ route('classes.assign-subjects', $class) }}" class="p-4 space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($class->classSubjects as $index => $cs)
                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-black text-slate-800 truncate">{{ $cs->subject?->name ?? '—' }}</span>
                                <span class="text-[9px] font-mono text-slate-400 shrink-0">{{ $cs->subject?->code ?? '' }}</span>
                            </div>
                            <input type="hidden" name="assignments[{{ $index }}][class_subject_id]" value="{{ $cs->id }}">
                            <select name="assignments[{{ $index }}][teacher_id]"
                                    class="w-full rounded-lg border-slate-300 py-1.5 text-[11px] font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">— Sem docente —</option>
                                @foreach($availableTeachers ?? [] as $t)
                                    <option value="{{ $t->id }}" @selected($cs->teacher_id == $t->id)>{{ $t->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @empty
                        <p class="col-span-full py-6 text-center text-[11px] text-slate-400 italic">
                            Nenhuma disciplina associada a esta turma.
                        </p>
                    @endforelse
                </div>
                @if($class->classSubjects->count() > 0)
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-purple-700 hover:bg-purple-800 text-xs font-black text-white transition">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Atribuições
                    </button>
                @endif
            </form>
        @else
            <div class="p-4 text-[11px] text-slate-400">Sem permissão para editar atribuições.</div>
        @endcan
    </div>

    {{-- ═══ MODAL ANIVERSARIANTES ═══ --}}
    <div x-show="birthdays" x-cloak x-transition.opacity.duration.200ms
         class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div x-show="birthdays" x-transition.scale.origin.center.duration.200ms
             @click.outside="birthdays = false"
             class="max-w-md mx-auto mt-10 bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="px-5 py-3.5 bg-gradient-to-r from-amber-400 to-amber-500 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-cake-candles text-lg"></i>
                    <div>
                        <h3 class="text-sm font-black">Aniversariantes</h3>
                        <p class="text-[10px] text-amber-50 font-semibold">{{ $upcomingBirthdays->count() }} aluno(s) nos próximos 30 dias</p>
                    </div>
                </div>
                <button type="button" @click="birthdays = false" class="text-white/80 hover:text-white transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-4 max-h-80 overflow-y-auto space-y-2">
                @forelse($upcomingBirthdays as $student)
                    <a href="{{ route('students.show', $student->id) }}"
                       class="no-underline flex items-center justify-between gap-3 p-3 rounded-xl bg-amber-50/70 border border-amber-200 hover:border-amber-400 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-amber-500 text-white font-black text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(mb_substr($student->first_name ?? '?', 0, 1) . mb_substr($student->last_name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-black text-slate-800 truncate">{{ $student->full_name }}</p>
                                <p class="text-[10px] font-semibold text-slate-500">
                                    <i class="fa-solid fa-cake-candles text-amber-600 me-1"></i>
                                    {{ $student->birthdate?->format('d/m') }} · {{ $student->age }} anos
                                </p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 text-[10px] shrink-0"></i>
                    </a>
                @empty
                    <div class="py-8 text-center">
                        <i class="fa-solid fa-calendar-check text-2xl text-emerald-300"></i>
                        <p class="mt-2 text-xs font-bold text-slate-600">Sem aniversários nos próximos 30 dias.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
