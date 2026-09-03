@extends('layouts.app')

@section('title', 'Gestão de Notas & Pautas')

@section('content')
<div class="space-y-4">

    {{-- 1. Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-medal text-emerald-600"></i> Gestão de Notas &amp; Pautas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Selecção central de turmas para lançamento de notas, pautas e boletins.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.academic') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-chart-bar"></i> Relatório Académico
            </a>
        </div>
    </div>

    {{-- 2. Tabs por Nível --}}
    <div class="border-b border-slate-200 flex items-center gap-1 overflow-x-auto">
        <a href="{{ route('grades.dashboard', ['level' => 'all']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'all' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-layer-group"></i> Todas
            <span class="px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-extrabold">{{ $levelStats['all'] }}</span>
        </a>
        <a href="{{ route('grades.dashboard', ['level' => 'preschool']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'preschool' ? 'border-pink-500 text-pink-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-shapes"></i> Pré-Escolar
            <span class="px-1.5 py-0.5 rounded-full bg-pink-50 text-pink-700 text-[10px] font-extrabold">{{ $levelStats['preschool'] }}</span>
        </a>
        <a href="{{ route('grades.dashboard', ['level' => 'primary']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'primary' ? 'border-blue-500 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-child"></i> Primário
            <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-extrabold">{{ $levelStats['primary'] }}</span>
        </a>
        <a href="{{ route('grades.dashboard', ['level' => 'secondary']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'secondary' ? 'border-purple-500 text-purple-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-graduation-cap"></i> Secundário
            <span class="px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-700 text-[10px] font-extrabold">{{ $levelStats['secondary'] }}</span>
        </a>
    </div>

    {{-- 3. Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-medal"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Notas Registadas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($totalGrades) }}</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Média Geral</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($average ?? 0, 1) }} / 20</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full {{ $passingRate >= 70 ? 'bg-emerald-500' : 'bg-amber-500' }} shrink-0"></div>
            <div class="w-9 h-9 rounded-full {{ $passingRate >= 70 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-percentage"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Taxa Aprovação</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $passingRate }}%</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas Activas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $classes->count() }}</span>
            </div>
        </div>
    </div>

    {{-- 4. Acções rápidas por turma --}}
    @if($classes->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-10 text-center text-slate-400">
            <i class="fas fa-chalkboard text-4xl mb-3 opacity-30"></i>
            <p class="font-bold text-slate-600">Nenhuma turma activa para este nível.</p>
            <a href="{{ route('classes.index') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:underline">
                <i class="fas fa-arrow-right"></i> Ir para Gestão de Turmas
            </a>
        </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
        @foreach($classes as $class)
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden hover:shadow-md transition">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between
                    @if($class->isSingleTeacher()) bg-blue-50/40 @else bg-purple-50/40 @endif">
                    <div>
                        <h3 class="text-sm font-black text-slate-800">{{ $class->name }}</h3>
                        <p class="text-[11px] text-slate-500">{{ $class->grade_level_name }} · {{ $class->shift_label }}</p>
                    </div>
                    @if($class->isSingleTeacher())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-700 border border-blue-200">
                            <i class="fas fa-user-tie text-[9px]"></i> Regente
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-700 border border-purple-200">
                            <i class="fas fa-book text-[9px]"></i> Disciplinas
                        </span>
                    @endif
                </div>
                <div class="p-4 space-y-2">
                    {{-- Professor(es) --}}
                    @if($class->isSingleTeacher())
                        <p class="text-[11px] text-slate-600">
                            <i class="fas fa-user-tie text-emerald-500"></i>
                            <b>Regente:</b> {{ $class->teacher?->full_name ?? '—' }}
                        </p>
                        @if($class->assistantTeacher)
                            <p class="text-[11px] text-slate-500">
                                <i class="fas fa-user-friends text-blue-400"></i>
                                <b>Assistente:</b> {{ $class->assistantTeacher->full_name }}
                            </p>
                        @endif
                    @else
                        <p class="text-[11px] text-slate-600">
                            <i class="fas fa-chalkboard-teacher text-purple-500"></i>
                            <b>{{ $class->classSubjects->pluck('teacher_id')->filter()->unique()->count() }}</b> professor(es) por disciplina
                        </p>
                    @endif

                    <div class="text-[10px] text-slate-500 font-mono pt-1 border-t border-slate-100">
                        {{ $class->classSubjects->count() }} disciplina(s) · {{ $class->active_students_count ?? '—' }} aluno(s)
                    </div>

                    <div class="grid grid-cols-2 gap-1.5 pt-1">
                        @can('create_grades')
                            <a href="{{ route('grades.batch-create', ['class_id' => $class->id, 'term' => $term]) }}"
                                class="no-underline inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-[10px] font-extrabold transition">
                                <i class="fas fa-edit"></i> Lançar
                            </a>
                        @endcan
                        <a href="{{ route('pautas.trimestral', $class->id) }}"
                            class="no-underline inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-extrabold transition">
                            <i class="fas fa-file-pdf"></i> Pauta
                        </a>
                        <a href="{{ route('grades.class-report', $class->id) }}"
                            class="no-underline inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-extrabold transition">
                            <i class="fas fa-table"></i> Notas
                        </a>
                        <a href="{{ route('classes.show', $class->id) }}"
                            class="no-underline inline-flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[10px] font-extrabold transition">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    {{-- 5. Atalhos inferiores (Pauta Geral, Boletins, etc) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <a href="{{ route('grades.class-report', ['class' => $classes->first()?->id]) }}" class="no-underline bg-gradient-to-br from-rose-500 to-rose-700 text-white rounded-xl p-4 flex items-center gap-3 hover:shadow-lg transition">
            <div class="w-12 h-12 rounded-lg bg-white/15 flex items-center justify-center"><i class="fas fa-file-pdf text-2xl"></i></div>
            <div>
                <h3 class="font-black">Pautas &amp; Boletins</h3>
                <p class="text-[11px] text-rose-100">Gerar PDF, imprimir e partilhar por email.</p>
            </div>
        </a>
        <a href="{{ route('reports.academic') }}" class="no-underline bg-gradient-to-br from-indigo-500 to-indigo-700 text-white rounded-xl p-4 flex items-center gap-3 hover:shadow-lg transition">
            <div class="w-12 h-12 rounded-lg bg-white/15 flex items-center justify-center"><i class="fas fa-chart-bar text-2xl"></i></div>
            <div>
                <h3 class="font-black">Relatório Académico</h3>
                <p class="text-[11px] text-indigo-100">Estatísticas por turma, disciplina e trimestre.</p>
            </div>
        </a>
        <a href="{{ route('classes.index') }}" class="no-underline bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-xl p-4 flex items-center gap-3 hover:shadow-lg transition">
            <div class="w-12 h-12 rounded-lg bg-white/15 flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-2xl"></i></div>
            <div>
                <h3 class="font-black">Gestão de Turmas</h3>
                <p class="text-[11px] text-emerald-100">Professores, atribuições e capacidades.</p>
            </div>
        </a>
    </div>

</div>
@endsection