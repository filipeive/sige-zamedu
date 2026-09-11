@extends('layouts.app')

@section('title', 'Turma ' . $class->name)
@section('page-title', 'Detalhes da Turma ' . $class->name)
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Turmas</a></li>
    <li class="breadcrumb-item active">{{ $class->name }}</li>
@endsection

@section('content')
<div class="space-y-5" x-data="{ birthdayModalOpen: false }">

    {{-- 1. Header Principal --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    <i class="fas fa-layer-group me-1"></i> Nível: {{ $class->grade_level_name }}
                </span>
                @php
                    $shiftClass = match($class->shift) {
                        'afternoon' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'night' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        default => 'bg-sky-100 text-sky-800 border-sky-200',
                    };
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold border uppercase tracking-wider {{ $shiftClass }}">
                    <i class="fas fa-clock me-1"></i> Turno: {{ $class->shift_label }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    <i class="fas fa-calendar-alt me-1"></i> Ano {{ $class->school_year }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold {{ $class->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                    <i class="fas fa-circle text-[8px] me-1 {{ $class->is_active ? 'text-emerald-500' : 'text-slate-400' }}"></i> {{ $class->is_active ? 'Ativa' : 'Inativa' }}
                </span>
            </div>

            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Turma: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Ficha completa da turma, distribuição docente, acompanhamento de alunos e pauta oficial.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @can('create_grades')
                <a href="{{ route('grades.index', ['class_id' => $class->id]) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-edit"></i> Lançar Notas
                </a>
            @endcan

            @can('view_grades')
                <a href="{{ route('pautas.trimestral', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-teal-700 hover:bg-teal-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-table"></i> Pauta Trimestral
                </a>
                @if($class->isNationalExamGrade())
                    <a href="{{ route('pautas.exame', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-rose-700 hover:bg-rose-800 text-xs font-bold text-white shadow-2xs transition">
                        <i class="fas fa-file-signature"></i> Exames &amp; Júri
                    </a>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200">
                        <i class="fas fa-file-signature text-rose-600 me-1"></i> Exame Nacional MINEDH
                    </span>
                @endif
                @if($class->isBasicEducation())
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">
                        <i class="fas fa-graduation-cap text-blue-600 me-1"></i> Educação Básica
                    </span>
                @endif
            @endcan

            @can('edit_classes')
                <a href="{{ route('classes.edit', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-edit"></i> Editar Turma
                </a>
            @endcan

            <a href="{{ route('classes.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- 2. Cartões de Estatísticas Globais da Turma --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        {{-- Card 1: Vagas / Capacidade --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-emerald-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block truncate">Capacidade &amp; Vagas</span>
                <div class="flex items-baseline gap-1 mt-0.5">
                    <span class="text-xl font-black text-slate-800">{{ $stats['total_students'] }}</span>
                    <span class="text-xs font-bold text-slate-400">/ {{ $class->max_students }} alunos</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full {{ $stats['capacity_percentage'] > 85 ? 'bg-rose-500' : ($stats['capacity_percentage'] > 65 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ min(100, $stats['capacity_percentage']) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Card 2: Modo de Ensino & Regente --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-teal-300 transition">
            <div class="w-1.5 h-10 rounded-full {{ $class->isSingleTeacher() ? 'bg-blue-500' : 'bg-purple-500' }} shrink-0"></div>
            <div class="w-10 h-10 rounded-xl {{ $class->isSingleTeacher() ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }} flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas {{ $class->isSingleTeacher() ? 'fa-user-tie' : 'fa-book-open' }}"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block truncate">Modo de Lecionação</span>
                <span class="text-sm font-black text-slate-800 mt-0.5 block truncate">
                    {{ $class->isSingleTeacher() ? 'Professor Regente' : 'Por Disciplina' }}
                </span>
                <span class="text-[11px] font-semibold text-slate-500 truncate block mt-0.5">
                    @if($class->isSingleTeacher())
                        {{ $class->teacher ? $class->teacher->full_name : 'Sem Regente Atribuído' }}
                    @else
                        {{ $class->classSubjects->pluck('teacher_id')->filter()->unique()->count() }} Docente(s) Atribuído(s)
                    @endif
                </span>
            </div>
        </div>

        {{-- Card 3: Distribuição de Gênero --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-sky-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-sky-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-venus-mars"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Distribuição de Gênero</span>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-md text-xs font-black bg-blue-50 text-blue-700 border border-blue-200/60 flex items-center gap-1">
                        <i class="fas fa-mars text-[10px]"></i> {{ $stats['male_students'] }}
                    </span>
                    <span class="px-2 py-0.5 rounded-md text-xs font-black bg-pink-50 text-pink-700 border border-pink-200/60 flex items-center gap-1">
                        <i class="fas fa-venus text-[10px]"></i> {{ $stats['female_students'] }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 4: Idade Média --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-amber-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Idade Média da Turma</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ (int) round($stats['average_age']) }} <span class="text-xs font-bold text-slate-400">anos</span></span>
            </div>
        </div>
    </div>

    {{-- 3. Barra de Ações Rápidas --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-3 shadow-2xs">
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
            <a href="{{ route('classes.students', $class->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-emerald-50/70 hover:bg-emerald-100/80 text-emerald-800 border border-emerald-200/80 transition group shadow-2xs">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm font-bold mb-1.5 group-hover:scale-105 transition-transform">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-xs font-black">Lista de Alunos</span>
                <span class="text-[10px] text-emerald-600 font-semibold">{{ $stats['total_students'] }} Matriculados</span>
            </a>

            <a href="{{ route('attendances.mark-by-class', $class->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-sky-50/70 hover:bg-sky-100/80 text-sky-800 border border-sky-200/80 transition group shadow-2xs">
                <div class="w-9 h-9 rounded-lg bg-sky-600 text-white flex items-center justify-center text-sm font-bold mb-1.5 group-hover:scale-105 transition-transform">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <span class="text-xs font-black">Presenças</span>
                <span class="text-[10px] text-sky-600 font-semibold">Registo Diário</span>
            </a>

            @can('create_grades')
            <a href="{{ route('grades.index', ['class_id' => $class->id]) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-amber-50/70 hover:bg-amber-100/80 text-amber-800 border border-amber-200/80 transition group shadow-2xs">
                <div class="w-9 h-9 rounded-lg bg-amber-600 text-white flex items-center justify-center text-sm font-bold mb-1.5 group-hover:scale-105 transition-transform">
                    <i class="fas fa-edit"></i>
                </div>
                <span class="text-xs font-black">Lançar Notas</span>
                <span class="text-[10px] text-amber-600 font-semibold">Avaliações</span>
            </a>
            @endcan

            @can('view_grades')
            <a href="{{ route('pautas.trimestral', $class->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-teal-50/70 hover:bg-teal-100/80 text-teal-800 border border-teal-200/80 transition group shadow-2xs">
                <div class="w-9 h-9 rounded-lg bg-teal-600 text-white flex items-center justify-center text-sm font-bold mb-1.5 group-hover:scale-105 transition-transform">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <span class="text-xs font-black">Pauta Trimestral</span>
                <span class="text-[10px] text-teal-600 font-semibold">Matriz Oficial</span>
            </a>
            @endcan

            <button type="button" 
                    @click="birthdayModalOpen = true" 
                    onclick="const m = document.getElementById('birthdayModalContainer'); if(m) { m.style.display = 'flex'; m.classList.remove('hidden'); }" 
                    class="relative flex flex-col items-center justify-center p-3 rounded-xl bg-purple-50/70 hover:bg-purple-100/80 text-purple-800 border border-purple-200/80 transition group shadow-2xs cursor-pointer">
                @if($upcomingBirthdays->count() > 0)
                    <span class="absolute -top-1.5 -right-1.5 px-2 py-0.5 rounded-full bg-amber-500 text-white font-black text-[10px] shadow-sm">
                        {{ $upcomingBirthdays->count() }}
                    </span>
                @endif
                <div class="w-9 h-9 rounded-lg bg-purple-600 text-white flex items-center justify-center text-sm font-bold mb-1.5 group-hover:scale-105 transition-transform">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <span class="text-xs font-black">Aniversariantes</span>
                <span class="text-[10px] text-purple-600 font-semibold">Próximos 30 dias</span>
            </button>
        </div>
    </div>

    {{-- 4. Conteúdo Principal Dividido em 2 Colunas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Lateral Esquerda (1/3): Ficha Técnica & Atribuição Docente --}}
        <div class="space-y-4">
            {{-- Card Informações da Turma --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i> Informações da Turma
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ $class->grade_level_name }}
                    </span>
                </div>
                <div class="p-4 space-y-3 text-xs">
                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="font-bold text-slate-600">Nome Oficial:</span>
                        <span class="text-slate-900 font-black text-sm">{{ $class->name }}</span>
                    </div>

                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="font-bold text-slate-600">Turno de Aulas:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $shiftClass }}">
                            {{ $class->shift_label }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="font-bold text-slate-600">Ano Lectivo:</span>
                        <span class="text-slate-800 font-black">{{ $class->school_year }}</span>
                    </div>

                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="font-bold text-slate-600">Sala de Aula:</span>
                        <span class="px-2 py-0.5 rounded-md font-bold bg-slate-100 text-slate-800 border border-slate-200">
                            <i class="fas fa-door-open text-[10px] text-slate-500 me-1"></i>{{ $class->classroom ?? 'Não Definida' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="font-bold text-slate-600">Regime de Ensino:</span>
                        @if($class->isSingleTeacher())
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-user-tie text-[9px]"></i> Professor Regente
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200">
                                <i class="fas fa-book text-[9px]"></i> Por Disciplina
                            </span>
                        @endif
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-600">Estado Operacional:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $class->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $class->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card Atribuição de Docentes Contextual --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        @if($class->isSingleTeacher())
                            <i class="fas fa-user-tie text-blue-600"></i> Corpo Docente da Turma
                        @else
                            <i class="fas fa-book-reader text-purple-600"></i> Atribuição por Disciplina
                        @endif
                    </h3>
                </div>
                <div class="p-4 text-xs space-y-3.5">
                    @if($class->isSingleTeacher())
                        {{-- PRIMÁRIO: Regente + Assistente --}}
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1.5">Professor Regente Principal</span>
                            @if($class->teacher)
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($class->teacher->first_name, 0, 1) . substr($class->teacher->last_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('teachers.show', $class->teacher->id) }}" class="no-underline font-black text-slate-800 hover:text-emerald-700 truncate block">
                                            {{ $class->teacher->full_name }}
                                        </a>
                                        <span class="text-[10px] text-slate-500 block truncate">{{ $class->teacher->email ?? 'Sem email' }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-rose-600 font-bold italic text-xs block">Nenhum professor regente atribuído.</span>
                            @endif
                        </div>

                        {{-- Formulário de Assistente --}}
                        @can('edit_classes')
                        <form method="POST" action="{{ route('classes.update-assistant', $class) }}" class="p-3 rounded-xl bg-sky-50/50 border border-sky-200/60 space-y-2">
                            @csrf
                            <label class="block text-[10px] font-black uppercase tracking-wider text-sky-900">Professor Assistente (Opcional)</label>
                            <div class="flex items-center gap-1.5">
                                <select name="assistant_teacher_id" class="flex-1 rounded-lg border-slate-300 py-1.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                                    <option value="">— Sem assistente —</option>
                                    @foreach($availableTeachers ?? [] as $t)
                                        @if(!$class->teacher || $t->id !== $class->teacher->id)
                                            <option value="{{ $t->id }}" @selected($class->assistant_teacher_id == $t->id)>{{ $t->full_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-sky-700 hover:bg-sky-800 text-white text-[11px] font-bold shadow-2xs transition">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </form>
                        @endcan
                    @else
                        {{-- SECUNDÁRIO: Matriz de disciplinas rápido --}}
                        @can('edit_classes')
                        <form method="POST" action="{{ route('classes.assign-subjects', $class) }}" class="space-y-3">
                            @csrf
                            <div class="max-h-72 overflow-y-auto space-y-2 pr-1">
                                @forelse($class->classSubjects as $cs)
                                    <div class="p-2 rounded-lg border border-slate-200/80 bg-slate-50/60 space-y-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-black text-slate-800 text-xs truncate">{{ $cs->subject?->name ?? '—' }}</span>
                                            <span class="text-[9px] font-mono text-slate-400">({{ $cs->subject?->code ?? '—' }})</span>
                                        </div>
                                        <input type="hidden" name="assignments[{{ $loop->index }}][class_subject_id]" value="{{ $cs->id }}">
                                        <select name="assignments[{{ $loop->index }}][teacher_id]" class="w-full rounded-md border-slate-300 py-1 text-[11px] font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                                            <option value="">— Selecionar Docente —</option>
                                            @foreach($availableTeachers ?? [] as $t)
                                                <option value="{{ $t->id }}" @selected($cs->teacher_id == $t->id)>{{ $t->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @empty
                                    <p class="text-slate-400 italic text-center py-4">Nenhuma disciplina associada a esta turma.</p>
                                @endforelse
                            </div>
                            @if($class->classSubjects->count() > 0)
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 px-3 py-2 rounded-lg bg-purple-700 hover:bg-purple-800 text-white text-xs font-black shadow-2xs transition">
                                    <i class="fas fa-save"></i> Guardar Atribuições
                                </button>
                            @endif
                        </form>
                        @endcan
                    @endif
                </div>
            </div>
        </div>

        {{-- Lateral Direita (2/3): Tabela de Alunos & Matriz Completa de Disciplinas --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- 1. Alunos da Turma --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-graduate text-emerald-600"></i> Alunos Matriculados ({{ $stats['total_students'] }})
                    </h3>
                    <a href="{{ route('classes.students', $class->id) }}" class="no-underline inline-flex items-center gap-1 text-xs font-extrabold text-emerald-700 hover:text-emerald-800">
                        Ver Lista Completa <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div>
                    @if($class->students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/60 text-slate-500 font-black uppercase tracking-wider text-[10px] border-b border-slate-200/80">
                                        <th class="py-3 px-4">Aluno</th>
                                        <th class="py-3 px-3">Processo Nº</th>
                                        <th class="py-3 px-3">Gênero</th>
                                        <th class="py-3 px-3">Idade</th>
                                        <th class="py-3 px-4 text-right">Ação</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($class->students->take(6) as $student)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-4 font-bold text-slate-800">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="font-extrabold text-slate-800 block text-xs">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 font-mono font-semibold text-slate-600">{{ $student->student_number ?? 'STU-' . $student->id }}</td>
                                        <td class="py-3 px-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $student->gender == 'male' ? 'bg-blue-50 text-blue-700 border border-blue-200/60' : 'bg-pink-50 text-pink-700 border border-pink-200/60' }}">
                                                <i class="fas {{ $student->gender == 'male' ? 'fa-mars' : 'fa-venus' }} me-1"></i>
                                                {{ $student->gender == 'male' ? 'Masculino' : 'Feminino' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 font-bold text-slate-700">
                                            {{ $student->birthdate ? (int) $student->birthdate->diffInYears(now()) . ' anos' : 'N/A' }}
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <a href="{{ route('students.show', $student->id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[10px] transition">
                                                <i class="fas fa-user-circle text-[10px]"></i> Ficha
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($class->students->count() > 6)
                            <div class="p-3 text-center border-t border-slate-100 bg-slate-50/50">
                                <a href="{{ route('classes.students', $class->id) }}" class="no-underline text-xs font-extrabold text-emerald-700 hover:text-emerald-800">
                                    Ver mais {{ $class->students->count() - 6 }} aluno(s) desta turma
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-8 text-center text-slate-400 space-y-3">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">Nenhum aluno matriculado nesta turma até ao momento.</p>
                            <a href="{{ route('classes.students', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                                <i class="fas fa-user-plus"></i> Matricular Alunos
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. Corpo Docente & Disciplinas da Turma --}}
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Corpo Docente da Turma &amp; Disciplinas
                    </h3>
                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full {{ $class->isSingleTeacher() ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-purple-100 text-purple-800 border border-purple-200' }}">
                        {{ $class->isSingleTeacher() ? 'Professor Regente' : 'Por Disciplina' }}
                    </span>
                </div>

                <div class="p-4 space-y-4">
                    @if($class->isSingleTeacher())
                        <!-- Primário: Professor Regente e Assistente -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Card Regente -->
                            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Professor Regente</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">Titular</span>
                                </div>
                                @if($class->teacher)
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($class->teacher->first_name, 0, 1) . substr($class->teacher->last_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('teachers.show', $class->teacher->id) }}" class="no-underline font-black text-slate-800 hover:text-emerald-700 text-xs truncate block">
                                                {{ $class->teacher->full_name }}
                                            </a>
                                            <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                                <i class="fas fa-envelope text-[9px] mr-1"></i>{{ $class->teacher->email ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-rose-600 font-bold italic">Nenhum professor regente atribuído.</p>
                                @endif
                            </div>

                            <!-- Card Assistente -->
                            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Professor Assistente</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-sky-100 text-sky-800 border border-sky-200">Apoio</span>
                                </div>
                                @if($class->assistantTeacher)
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-sky-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($class->assistantTeacher->first_name, 0, 1) . substr($class->assistantTeacher->last_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('teachers.show', $class->assistantTeacher->id) }}" class="no-underline font-black text-slate-800 hover:text-sky-700 text-xs truncate block">
                                                {{ $class->assistantTeacher->full_name }}
                                            </a>
                                            <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                                <i class="fas fa-envelope text-[9px] mr-1"></i>{{ $class->assistantTeacher->email ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-slate-400 font-semibold italic">Sem professor assistente atribuído.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Disciplinas Lecionadas -->
                        @if($class->subjects->count() > 0)
                            <div class="pt-3 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Disciplinas Integradas na Turma</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($class->subjects as $sub)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/70 text-xs font-bold shadow-2xs">
                                            <i class="fas fa-book text-[10px] text-emerald-600"></i>
                                            {{ $sub->name }}
                                            <span class="text-[10px] text-emerald-600 font-mono">({{ $sub->code ?? '—' }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Secundário: Matriz de Professores por Disciplina -->
                        @if($class->classSubjects->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50/60 text-slate-500 font-black uppercase tracking-wider text-[10px] border-b border-slate-200/80">
                                            <th class="py-3 px-4">Disciplina</th>
                                            <th class="py-3 px-3">Código</th>
                                            <th class="py-3 px-3">Docente Atribuído</th>
                                            <th class="py-3 px-4 text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($class->classSubjects as $cs)
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-3 px-4 font-extrabold text-slate-800">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                            <i class="fas fa-book"></i>
                                                        </div>
                                                        <span>{{ $cs->subject?->name ?? '—' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-3 font-mono text-slate-500 font-semibold">{{ $cs->subject?->code ?? '—' }}</td>
                                                <td class="py-3 px-3">
                                                    @if($cs->teacher)
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-black text-[10px] shrink-0 shadow-2xs">
                                                                {{ strtoupper(substr($cs->teacher->first_name, 0, 1) . substr($cs->teacher->last_name, 0, 1)) }}
                                                            </div>
                                                            <a href="{{ route('teachers.show', $cs->teacher->id) }}" class="no-underline font-extrabold text-slate-800 hover:text-emerald-700">
                                                                {{ $cs->teacher->full_name }}
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">Pendente de Atribuição</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    @if($cs->teacher)
                                                        <a href="{{ route('teachers.show', $cs->teacher->id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold transition">
                                                            <i class="fas fa-user-tie text-[9px]"></i> Perfil
                                                        </a>
                                                    @else
                                                        <a href="{{ route('classes.edit', $class->id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-purple-100 hover:bg-purple-200 text-purple-800 text-[10px] font-bold transition">
                                                            <i class="fas fa-plus-circle text-[9px]"></i> Atribuir
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8 text-center text-slate-400 space-y-2">
                                <i class="fas fa-book-open text-2xl text-slate-300"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma disciplina associada a esta turma.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- 5. Modal de Aniversariantes (Z-Index Elevado: z-[9999]) --}}
    <div id="birthdayModalContainer"
         x-show="birthdayModalOpen" 
         x-cloak 
         style="display: none;"
         class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xl max-w-md w-full overflow-hidden" @click.away="birthdayModalOpen = false; const m = document.getElementById('birthdayModalContainer'); if(m) m.style.display='none';">
            <div class="px-5 py-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white text-base font-bold shadow-2xs">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black tracking-tight text-white">Aniversariantes da Turma</h3>
                        <p class="text-[10px] text-amber-100 font-medium">{{ $upcomingBirthdays->count() }} aluno(s) celebram aniversário nos próximos 30 dias</p>
                    </div>
                </div>
                <button type="button" @click="birthdayModalOpen = false" onclick="const m = document.getElementById('birthdayModalContainer'); if(m) m.style.display='none';" class="text-white/80 hover:text-white text-lg font-bold cursor-pointer transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 max-h-96 overflow-y-auto space-y-3">
                @forelse($upcomingBirthdays as $student)
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-amber-50/70 border border-amber-200/70 hover:border-amber-300 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-2xs">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</h4>
                                <p class="text-[10px] text-slate-500 font-semibold mt-0.5">
                                    Data: <strong class="text-amber-700 font-extrabold">{{ $student->birthdate ? $student->birthdate->format('d/m') : 'N/A' }}</strong> 
                                    ({{ $student->birthdate ? $student->birthdate->diffInYears(now()) + 1 : '' }} anos)
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('students.show', $student->id) }}" class="no-underline inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-extrabold shadow-2xs transition">
                            <i class="fas fa-user"></i> Perfil
                        </a>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 space-y-2">
                        <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mx-auto text-xl">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <p class="text-xs font-extrabold text-slate-700">Nenhum aniversário nos próximos 30 dias.</p>
                        <p class="text-[11px] text-slate-500">Todos os aniversários da turma estão em dia.</p>
                    </div>
                @endforelse
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-right">
                <button type="button" @click="birthdayModalOpen = false" onclick="const m = document.getElementById('birthdayModalContainer'); if(m) m.style.display='none';" class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-black transition cursor-pointer">
                    Fechar
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
