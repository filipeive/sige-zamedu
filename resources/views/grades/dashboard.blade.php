@extends('layouts.app')

@section('title', 'Gestão de Notas & Pautas')
@section('page-title', 'Gestão de Notas & Pautas')
@section('page-title-icon', 'fas fa-medal')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline text-slate-500">Início</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Notas &amp; Pautas</li>
@endsection

@section('content')
<div class="space-y-5" x-data="{ searchQuery: '', selectedTerm: '{{ $term }}' }">

    {{-- 1. Hero Header Executivo --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white p-6 shadow-md border border-slate-700/50">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 uppercase tracking-wider">
                        <i class="fas fa-graduation-cap me-1"></i> Sistema Oficial de Avaliação &amp; Pautas
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-white/10 text-white border border-white/15">
                        Ano Lectivo {{ $currentYear }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400/20 text-amber-200 border border-amber-400/30">
                        {{ $term }}º Trimestre
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    <i class="fas fa-medal text-emerald-400"></i> Gestão de Notas &amp; Pautas
                </h1>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Central de controlo académico: lançamento rápido em lote por turma, acompanhamento do rendimento escolar e emissão de pautas oficiais consolidadas.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <a href="{{ route('pautas.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-file-invoice"></i> Pautas Consolidadas
                </a>
                <a href="{{ route('reports.academic') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition">
                    <i class="fas fa-chart-line text-emerald-300"></i> Relatório de Desempenho
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Barra de Filtros, Níveis e Busca --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-3 shadow-2xs space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            {{-- Tabs de Níveis --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                <a href="{{ route('grades.dashboard', ['level' => 'all', 'year' => $currentYear, 'term' => $term]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                        {{ $level === 'all' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 bg-slate-50 hover:bg-slate-100' }}">
                    <i class="fas fa-layer-group"></i> Todas as Turmas
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $levelStats['all'] }}</span>
                </a>
                <a href="{{ route('grades.dashboard', ['level' => 'primary', 'year' => $currentYear, 'term' => $term]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                        {{ $level === 'primary' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 bg-slate-50 hover:bg-slate-100' }}">
                    <i class="fas fa-child"></i> Ensino Primário
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'primary' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $levelStats['primary'] }}</span>
                </a>
                <a href="{{ route('grades.dashboard', ['level' => 'secondary', 'year' => $currentYear, 'term' => $term]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                        {{ $level === 'secondary' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 bg-slate-50 hover:bg-slate-100' }}">
                    <i class="fas fa-graduation-cap"></i> Ensino Secundário
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'secondary' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $levelStats['secondary'] }}</span>
                </a>
                <a href="{{ route('grades.dashboard', ['level' => 'preschool', 'year' => $currentYear, 'term' => $term]) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                        {{ $level === 'preschool' ? 'bg-emerald-700 text-white shadow-2xs' : 'text-slate-600 bg-slate-50 hover:bg-slate-100' }}">
                    <i class="fas fa-shapes"></i> Pré-Escolar
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'preschool' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $levelStats['preschool'] }}</span>
                </a>
            </div>

            {{-- Busca Rápida e Seletor de Trimestre --}}
            <div class="flex items-center gap-2 shrink-0">
                <div class="relative w-full sm:w-64">
                    <input type="text" x-model="searchQuery"
                           class="w-full rounded-xl border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50/70"
                           placeholder="Pesquisar turma ou regente...">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>

                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl shrink-0">
                    <a href="{{ route('grades.dashboard', ['level' => $level, 'year' => $currentYear, 'term' => 1]) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold no-underline transition {{ $term == 1 ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                       1º Trim
                    </a>
                    <a href="{{ route('grades.dashboard', ['level' => $level, 'year' => $currentYear, 'term' => 2]) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold no-underline transition {{ $term == 2 ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                       2º Trim
                    </a>
                    <a href="{{ route('grades.dashboard', ['level' => $level, 'year' => $currentYear, 'term' => 3]) }}"
                       class="px-2.5 py-1 rounded-lg text-xs font-bold no-underline transition {{ $term == 3 ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                       3º Trim
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Métricas Principais --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Notas Lançadas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($totalGrades) }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-pen-nib"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-teal-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Média Geral</span>
                <span class="text-xl font-black text-teal-800 mt-0.5 block">{{ number_format($average ?? 0, 1) }} <span class="text-xs font-bold text-slate-400">/ 20</span></span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 {{ $passingRate >= 70 ? 'border-l-emerald-500' : 'border-l-amber-500' }} shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Taxa de Aproveitamento</span>
                <span class="text-xl font-black {{ $passingRate >= 70 ? 'text-emerald-700' : 'text-amber-700' }} mt-0.5 block">{{ $passingRate }}%</span>
            </div>
            <div class="w-9 h-9 rounded-lg {{ $passingRate >= 70 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-percent"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-blue-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas Activas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $classes->count() }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>
    </div>

    {{-- 4. Cartões de Turmas Redesenhados --}}
    @if($classes->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-chalkboard"></i>
            </div>
            <h3 class="text-base font-bold text-slate-700">Nenhuma turma encontrada</h3>
            <p class="text-xs text-slate-500 mt-1">Não existem turmas ativas cadastradas para o nível selecionado neste ano lectivo.</p>
            @if(auth()->user()->can('create_classes') || auth()->user()->hasAnyRole(['admin', 'director', 'super_admin']))
                <a href="{{ route('classes.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 text-white text-xs font-bold shadow-2xs hover:bg-emerald-800 transition no-underline">
                    <i class="fas fa-plus-circle"></i> Criar Nova Turma
                </a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($classes as $class)
                <div x-show="searchQuery === '' || '{{ strtolower($class->name . ' ' . ($class->teacher?->full_name ?? '') . ' ' . $class->grade_level_name) }}'.includes(searchQuery.toLowerCase())"
                     class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden hover:border-emerald-300 hover:shadow-md transition-all flex flex-col justify-between group">
                    <div>
                        {{-- Class Card Header --}}
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70 group-hover:bg-emerald-50/30 transition-colors">
                            <div class="min-w-0">
                                <h3 class="text-base font-black text-slate-800 tracking-tight flex items-center gap-2 truncate">
                                    <i class="fas fa-chalkboard text-emerald-600 text-sm shrink-0"></i> {{ $class->name }}
                                </h3>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5 truncate">
                                    {{ $class->education_level_name }} · {{ $class->grade_level_name }}
                                </p>
                            </div>
                            @if($class->isSingleTeacher())
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200 shrink-0">
                                    <i class="fas fa-user-tie me-1"></i> Regente
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200 shrink-0">
                                    <i class="fas fa-book me-1"></i> Disciplinas
                                </span>
                            @endif
                        </div>

                        {{-- Class Details Grid --}}
                        <div class="p-5 space-y-2.5">
                            <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-user-tie text-emerald-600"></i> Responsável:
                                </span>
                                <span class="font-bold text-slate-800 truncate max-w-[180px]">
                                    {{ $class->teacher?->full_name ?? 'Não atribuído' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-users text-blue-600"></i> Alunos Matriculados:
                                </span>
                                <span class="font-bold text-slate-800">
                                    {{ $class->active_students_count ?? $class->students->count() }} Alunos
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-book-open text-purple-600"></i> Disciplinas:
                                </span>
                                <span class="font-bold text-slate-800">
                                    {{ $class->classSubjects->count() }} Disciplinas
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Button Row --}}
                    <div class="p-3.5 bg-slate-50/80 border-t border-slate-100 grid grid-cols-2 gap-2">
                        @can('create_grades')
                            <a href="{{ route('grades.batch-create', ['class_id' => $class->id, 'term' => $term]) }}"
                                class="no-underline inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-2xs transition">
                                <i class="fas fa-pen-to-square"></i> Lançar Notas
                            </a>
                        @endcan
                        <a href="{{ route('pautas.trimestral', $class->id) }}"
                            class="no-underline inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-2xs transition">
                            <i class="fas fa-file-invoice"></i> Pauta Oficial
                        </a>
                        <a href="{{ route('grades.class-report', $class->id) }}"
                            class="no-underline inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold shadow-2xs transition">
                            <i class="fas fa-chart-line text-emerald-600"></i> Aproveitamento
                        </a>
                        <a href="{{ route('classes.show', $class->id) }}"
                            class="no-underline inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold shadow-2xs transition">
                            <i class="fas fa-eye"></i> Detalhes
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- 5. Atalhos Rápidos no Rodapé --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
        <a href="{{ route('pautas.index') }}" class="no-underline bg-gradient-to-br from-emerald-700 to-teal-900 text-white rounded-2xl p-5 flex items-center gap-4 hover:shadow-lg transition group border border-emerald-600/30">
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl group-hover:scale-110 transition shrink-0">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-sm">Pautas Oficiais</h3>
                <p class="text-xs text-emerald-100/90 mt-0.5">Visão consolidada trimestral, anual e pauta final com exportação.</p>
            </div>
        </a>

        <a href="{{ route('reports.academic') }}" class="no-underline bg-gradient-to-br from-slate-800 to-slate-950 text-white rounded-2xl p-5 flex items-center gap-4 hover:shadow-lg transition group border border-slate-700/50">
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl group-hover:scale-110 transition shrink-0">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-sm">Relatório Académico</h3>
                <p class="text-xs text-slate-300 mt-0.5">Estatísticas detalhadas por disciplina, aprovação e gráficos.</p>
            </div>
        </a>

        <a href="{{ route('classes.index') }}" class="no-underline bg-gradient-to-br from-teal-800 to-slate-900 text-white rounded-2xl p-5 flex items-center gap-4 hover:shadow-lg transition group border border-teal-700/30">
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-xl group-hover:scale-110 transition shrink-0">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-sm">Gestão de Turmas</h3>
                <p class="text-xs text-teal-100/90 mt-0.5">Adicionar turmas, atribuição de professores e disciplinas.</p>
            </div>
        </a>
    </div>

</div>
@endsection