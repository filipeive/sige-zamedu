@extends('layouts.app')

@section('title', 'Gestão de Notas & Pautas')
@section('page-title', 'Gestão de Notas & Pautas')
@section('page-title-icon', 'fas fa-medal')

@section('content')
<div class="space-y-5">

    {{-- 1. Header Principal --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    <i class="fas fa-school me-1"></i> Sistema de Avaliação MINEDH
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    Ano Lectivo {{ $currentYear }}
                </span>
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-medal text-emerald-600"></i> Gestão de Notas &amp; Pautas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Central de lançamento de notas, acompanhamento do desempenho por turma e emissão de pautas oficiais.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('pautas.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                <i class="fas fa-file-invoice"></i> Consultar Pautas
            </a>
            <a href="{{ route('reports.academic') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-chart-line text-emerald-600"></i> Relatório Académico
            </a>
        </div>
    </div>

    {{-- 2. Filtro por Níveis de Ensino --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-1.5 shadow-2xs flex items-center gap-1.5 overflow-x-auto">
        <a href="{{ route('grades.dashboard', ['level' => 'all', 'year' => $currentYear]) }}"
            class="px-3.5 py-2 rounded-lg text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'all' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="fas fa-layer-group"></i> Todas as Turmas
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $levelStats['all'] }}</span>
        </a>
        <a href="{{ route('grades.dashboard', ['level' => 'primary', 'year' => $currentYear]) }}"
            class="px-3.5 py-2 rounded-lg text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'primary' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="fas fa-child"></i> Ensino Primário
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'primary' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $levelStats['primary'] }}</span>
        </a>
        <a href="{{ route('grades.dashboard', ['level' => 'secondary', 'year' => $currentYear]) }}"
            class="px-3.5 py-2 rounded-lg text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'secondary' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="fas fa-graduation-cap"></i> Ensino Secundário
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'secondary' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $levelStats['secondary'] }}</span>
        </a>
        <a href="{{ route('grades.dashboard', ['level' => 'preschool', 'year' => $currentYear]) }}"
            class="px-3.5 py-2 rounded-lg text-xs font-extrabold transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'preschool' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
            <i class="fas fa-shapes"></i> Pré-Escolar
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $level === 'preschool' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $levelStats['preschool'] }}</span>
        </a>
    </div>

    {{-- 3. Métricas Principais --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-emerald-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Notas Registadas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($totalGrades) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-teal-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Média Institucional</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($average ?? 0, 1) }} <span class="text-xs font-bold text-slate-400">/ 20</span></span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-emerald-300 transition">
            <div class="w-1.5 h-10 rounded-full {{ $passingRate >= 70 ? 'bg-emerald-500' : 'bg-amber-500' }} shrink-0"></div>
            <div class="w-10 h-10 rounded-xl {{ $passingRate >= 70 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-percentage"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Taxa de Aprovação</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $passingRate }}%</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-blue-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas Activas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $classes->count() }}</span>
            </div>
        </div>
    </div>

    {{-- 4. Cartões de Turmas --}}
    @if($classes->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-chalkboard"></i>
            </div>
            <h3 class="text-base font-bold text-slate-700">Nenhuma turma encontrada</h3>
            <p class="text-xs text-slate-500 mt-1">Não existem turmas ativas cadastradas para o nível selecionado neste ano lectivo.</p>
            <a href="{{ route('classes.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 text-white text-xs font-bold shadow-2xs hover:bg-emerald-800 transition no-underline">
                <i class="fas fa-plus-circle"></i> Ir para Gestão de Turmas
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($classes as $class)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        {{-- Class Card Header --}}
                        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60 group-hover:bg-emerald-50/40 transition-colors">
                            <div>
                                <h3 class="text-base font-black text-slate-800 tracking-tight flex items-center gap-2">
                                    {{ $class->name }}
                                </h3>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                                    {{ $class->education_level_name }} · {{ $class->grade_level_name }}
                                </p>
                            </div>
                            @if($class->isSingleTeacher())
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fas fa-user-tie me-1"></i> Regente
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200">
                                    <i class="fas fa-book me-1"></i> Disciplinas
                                </span>
                            @endif
                        </div>

                        {{-- Class Details --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2.5">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-user-tie text-emerald-600"></i> Professor Responsável:
                                </span>
                                <span class="font-bold text-slate-800">
                                    {{ $class->teacher?->full_name ?? 'Não atribuído' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2.5">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-users text-blue-600"></i> Total de Estagiários/Alunos:
                                </span>
                                <span class="font-bold text-slate-800">
                                    {{ $class->active_students_count ?? $class->students->count() }} Alunos
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-book-open text-purple-600"></i> Disciplinas Lecionadas:
                                </span>
                                <span class="font-bold text-slate-800">
                                    {{ $class->classSubjects->count() }} Disciplinas
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Button Row --}}
                    <div class="p-4 bg-slate-50/80 border-t border-slate-100 grid grid-cols-2 gap-2">
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
                            <i class="fas fa-eye"></i> Detalhes Turma
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- 5. Atalhos Rápidos no Rodapé --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
        <a href="{{ route('pautas.index') }}" class="no-underline bg-gradient-to-br from-emerald-600 to-teal-800 text-white rounded-2xl p-5 flex items-center gap-4 hover:shadow-lg transition group">
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-2xl group-hover:scale-110 transition shrink-0">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-base">Pautas Oficiais MINEDH</h3>
                <p class="text-xs text-emerald-100 mt-0.5">Visão consolidada trimestral, anual e pauta final com exportação PDF.</p>
            </div>
        </a>

        <a href="{{ route('reports.academic') }}" class="no-underline bg-gradient-to-br from-slate-800 to-slate-950 text-white rounded-2xl p-5 flex items-center gap-4 hover:shadow-lg transition group">
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-2xl group-hover:scale-110 transition shrink-0">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-base">Relatório de Desempenho</h3>
                <p class="text-xs text-slate-300 mt-0.5">Estatísticas detalhadas por disciplina, aprovação e gráficos.</p>
            </div>
        </a>

        <a href="{{ route('classes.index') }}" class="no-underline bg-gradient-to-br from-teal-700 to-slate-800 text-white rounded-2xl p-5 flex items-center gap-4 hover:shadow-lg transition group">
            <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-2xl group-hover:scale-110 transition shrink-0">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-base">Gestão de Turmas</h3>
                <p class="text-xs text-teal-100 mt-0.5">Atribuição de disciplinas, professores regentes e horários.</p>
            </div>
        </a>
    </div>

</div>
@endsection