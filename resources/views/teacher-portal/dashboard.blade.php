@extends('layouts.app')

@section('title', 'Portal do Professor — Dashboard')
@section('page-title', 'Portal do Professor')
@section('page-title-icon', 'fas fa-chalkboard-user')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-emerald-700 hover:text-emerald-800 transition">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Dashboard</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- 1. Hero / Boas-vindas ao Docente --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 text-white shadow-md">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>
        <div class="absolute right-24 top-2 text-white/5 text-9xl pointer-events-none">
            <i class="fas fa-graduation-cap"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center justify-center font-black text-2xl sm:text-3xl shadow-inner shrink-0">
                    {{ strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-amber-400 text-slate-900 uppercase tracking-wider shadow-2xs">
                            <i class="fas fa-certificate me-1"></i> Docente Oficial
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-3xs font-bold bg-white/15 text-white border border-white/20">
                            Ano Lectivo {{ current_school_year() }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                        Bem-vindo(a), {{ $teacher->first_name }} {{ $teacher->last_name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100 mt-1 flex items-center gap-3 flex-wrap">
                        <span><i class="fas fa-graduation-cap text-amber-300 me-1"></i> {{ $teacher->qualification ?: 'Licenciatura' }} em {{ $teacher->specialization ?: 'Educação' }}</span>
                        @if($teacher->years_experience)
                            <span><i class="fas fa-clock text-emerald-200 me-1"></i> {{ $teacher->years_experience }} anos de carreira</span>
                        @endif
                        @if($teacher->school)
                            <span><i class="fas fa-school text-emerald-200 me-1"></i> {{ $teacher->school->name }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap self-start md:self-center">
                <a href="{{ route('teacher.classes.index') }}" class="no-underline inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-emerald-900 font-extrabold text-xs shadow hover:bg-emerald-50 transition">
                    <i class="fas fa-chalkboard"></i> Minhas Turmas
                </a>
                <a href="{{ route('grades.batch-create') }}" class="no-underline inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-400 text-slate-900 font-extrabold text-xs shadow hover:bg-amber-300 transition">
                    <i class="fas fa-layer-group"></i> Lançar Notas
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Estatísticas Rápidas (Stat Cards) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        {{-- Turmas Ativas --}}
        <a href="{{ route('teacher.classes.index') }}" class="no-underline bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-emerald-300 hover:shadow-sm transition group">
            <div class="w-1.5 h-10 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-base font-bold shrink-0 group-hover:scale-105 transition-transform">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas</span>
                <span class="text-xl font-black text-slate-800 block">{{ $stats['total_classes'] }}</span>
                <span class="text-[10px] font-semibold text-emerald-600 flex items-center gap-1">
                    Ver todas <i class="fas fa-arrow-right text-[8px]"></i>
                </span>
            </div>
        </a>

        {{-- Alunos Matriculados --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5">
            <div class="w-1.5 h-10 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos</span>
                <span class="text-xl font-black text-slate-800 block">{{ $stats['total_students'] }}</span>
                <span class="text-[10px] font-semibold text-slate-500">Sob orientação</span>
            </div>
        </div>

        {{-- Presenças de Hoje --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5">
            <div class="w-1.5 h-10 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Presenças Hoje</span>
                <span class="text-xl font-black text-slate-800 block">{{ $stats['today_attendance'] }}</span>
                <span class="text-[10px] font-semibold text-slate-500">Registos do dia</span>
            </div>
        </div>

        {{-- Avaliações Pendentes --}}
        <a href="{{ route('teacher.grades.pending') }}" class="no-underline bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-amber-300 hover:shadow-sm transition group">
            <div class="w-1.5 h-10 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-base font-bold shrink-0 group-hover:scale-105 transition-transform">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Avaliações</span>
                <span class="text-xl font-black text-slate-800 block">{{ $stats['pending_grades'] }}</span>
                <span class="text-[10px] font-semibold text-amber-600 flex items-center gap-1">
                    Pendentes <i class="fas fa-arrow-right text-[8px]"></i>
                </span>
            </div>
        </a>

        {{-- Eventos Agendados --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 col-span-2 sm:col-span-1">
            <div class="w-1.5 h-10 rounded-full bg-purple-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-calendar-days"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Eventos</span>
                <span class="text-xl font-black text-slate-800 block">{{ $stats['upcoming_events'] }}</span>
                <span class="text-[10px] font-semibold text-slate-500">No calendário</span>
            </div>
        </div>
    </div>

    {{-- 3. Minhas Turmas Atribuídas --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-black text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chalkboard text-emerald-600"></i> Minhas Turmas Leccionadas
                    <span class="px-2 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ $myClasses->count() }}
                    </span>
                </h2>
                <p class="text-xs text-slate-500">Selecione uma turma para registrar presenças, consultar o caderno de notas ou realizar lançamento em lote.</p>
            </div>
            <a href="{{ route('teacher.classes.index') }}" class="no-underline inline-flex items-center gap-1 text-xs font-extrabold text-emerald-700 hover:text-emerald-800 transition">
                Ver Todas <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        @if($myClasses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($myClasses as $class)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md hover:border-emerald-300 transition-all p-5 flex flex-col justify-between group">
                        <div>
                            {{-- Top Header com Badges --}}
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $class->grade_level_name ?: 'Classe ' . $class->grade_level }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    @if($class->shift_label)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-50 text-slate-600 border border-slate-200">
                                            <i class="far fa-sun text-amber-500 me-0.5"></i> {{ $class->shift_label }}
                                        </span>
                                    @endif
                                    @if($class->classroom)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-50 text-slate-600 border border-slate-200">
                                            <i class="fas fa-door-open text-slate-400 me-0.5"></i> {{ $class->classroom }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Nome da Turma --}}
                            <h3 class="text-base font-black text-slate-800 group-hover:text-emerald-700 transition-colors">
                                {{ $class->name }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $class->education_level_name ?: 'Ensino Básico / Secundário' }}
                            </p>

                            {{-- Indicadores Rápidos da Turma --}}
                            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-slate-50 rounded-lg p-2 text-center">
                                    <span class="text-[10px] text-slate-400 uppercase font-extrabold block">Estudantes</span>
                                    <span class="text-sm font-black text-slate-800 flex items-center justify-center gap-1 mt-0.5">
                                        <i class="fas fa-users text-emerald-600 text-xs"></i> {{ $class->students_count }}
                                    </span>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-2 text-center">
                                    <span class="text-[10px] text-slate-400 uppercase font-extrabold block">Modalidade</span>
                                    <span class="text-[11px] font-bold text-slate-700 truncate block mt-0.5" title="{{ $class->teaching_mode_label }}">
                                        {{ $class->isSubjectBased() ? 'Por Disciplina' : 'Regente' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Barra de Ações Rápidas por Turma --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                            <a href="{{ route('teacher.attendance.today', $class->id) }}" 
                               class="no-underline flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-extrabold text-xs transition border border-emerald-200/60"
                               title="Registar Presenças de Hoje">
                                <i class="fas fa-clipboard-check text-emerald-600"></i> Presenças
                            </a>
                            <a href="{{ route('teacher.gradebook', $class->id) }}" 
                               class="no-underline flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-extrabold text-xs transition border border-amber-200/60"
                               title="Abrir Caderno de Notas Oficial">
                                <i class="fas fa-edit text-amber-600"></i> Notas
                            </a>
                            <a href="{{ route('grades.batch-create', ['class_id' => $class->id]) }}" 
                               class="no-underline inline-flex items-center justify-center p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition border border-slate-200"
                               title="Lançamento em Lote desta Turma">
                                <i class="fas fa-layer-group"></i>
                            </a>
                            <a href="{{ route('teacher.classes.detail', $class->id) }}" 
                               class="no-underline inline-flex items-center justify-center p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition border border-slate-200"
                               title="Ver Detalhes da Turma">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-10 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Nenhuma turma atribuída</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Entre em contato com a Direção ou Coordenação Pedagógica para realizar a vinculação das suas turmas e disciplinas.
                </p>
            </div>
        @endif
    </div>

    {{-- 4. Painéis Auxiliares (Eventos, Comunicados e Aniversários) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Próximos Eventos --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-purple-600"></i> Próximos Eventos
                    </h3>
                    <span class="text-3xs font-extrabold px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                        {{ $upcomingEvents->count() }}
                    </span>
                </div>

                @if($upcomingEvents->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($upcomingEvents as $event)
                            <div class="flex items-start gap-3 p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/50 transition border border-slate-100">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-800 flex flex-col items-center justify-center font-black shrink-0 text-center leading-none">
                                    <span class="text-[9px] uppercase font-bold">{{ $event->event_date ? $event->event_date->format('M') : 'EV' }}</span>
                                    <span class="text-sm">{{ $event->event_date ? $event->event_date->format('d') : '--' }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-slate-800 truncate mb-0.5">{{ $event->title }}</h4>
                                    <p class="text-[11px] text-slate-500 flex items-center gap-2">
                                        @if($event->location)
                                            <span class="truncate"><i class="fas fa-location-dot text-slate-400 me-1"></i>{{ $event->location }}</span>
                                        @endif
                                        @if($event->type ?? $event->event_type ?? null)
                                            <span class="px-1.5 py-0.2 rounded bg-white text-slate-600 text-[10px] border border-slate-200">{{ ucfirst($event->type ?? $event->event_type) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-400">
                        <i class="far fa-calendar-check text-2xl mb-1 text-slate-300"></i>
                        <p class="text-xs font-medium">Sem eventos agendados para os próximos dias.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Comunicados & Avisos --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="fas fa-bullhorn text-blue-600"></i> Avisos da Direcção
                    </h3>
                    <a href="{{ route('teacher.communications.index') }}" class="no-underline text-3xs font-extrabold text-blue-600 hover:text-blue-800 transition">
                        Ver todos
                    </a>
                </div>

                @if($recentCommunications->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($recentCommunications as $comm)
                            <div class="p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50/50 transition border border-slate-100">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-3xs font-extrabold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 uppercase">
                                        {{ $comm->target_audience === 'teachers' ? 'Professores' : 'Geral' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $comm->created_at ? $comm->created_at->diffForHumans() : '' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800 mb-0.5 line-clamp-1">{{ $comm->title }}</h4>
                                <p class="text-[11px] text-slate-500 line-clamp-2">{{ Str::limit(strip_tags($comm->message), 80) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-400">
                        <i class="far fa-comment-dots text-2xl mb-1 text-slate-300"></i>
                        <p class="text-xs font-medium">Nenhum comunicado recente publicado.</p>
                    </div>
                @endif
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100">
                <a href="{{ route('teacher.communications.create') }}" class="no-underline w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs transition border border-blue-200/60">
                    <i class="fas fa-paper-plane text-xs"></i> Enviar Mensagem
                </a>
            </div>
        </div>

        {{-- Aniversariantes da Semana --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="fas fa-cake-candles text-amber-500"></i> Aniversariantes da Semana
                    </h3>
                    <span class="text-3xs font-extrabold px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                        {{ $birthdaysThisWeek->count() }}
                    </span>
                </div>

                @if($birthdaysThisWeek->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($birthdaysThisWeek as $birthday)
                            <div class="flex items-center justify-between gap-3 p-2 rounded-xl bg-slate-50 hover:bg-amber-50/40 transition border border-slate-100">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 to-amber-300 text-slate-900 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($birthday->first_name, 0, 1) . substr($birthday->last_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-800 truncate mb-0.5">{{ $birthday->first_name }} {{ $birthday->last_name }}</h4>
                                        <p class="text-[10px] text-slate-400 truncate">
                                            <i class="fas fa-chalkboard text-slate-400 me-0.5"></i> {{ $birthday->currentClass?->name ?: 'Turma não atribuída' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-3xs font-extrabold bg-amber-100 text-amber-900 border border-amber-200 whitespace-nowrap">
                                    {{ $birthday->birthdate ? $birthday->birthdate->format('d/m') : '--' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-400">
                        <i class="fas fa-cake-candles text-2xl mb-1 text-slate-300"></i>
                        <p class="text-xs font-medium">Sem aniversários registrados para esta semana.</p>
                    </div>
                @endif
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 text-center">
                <span class="text-[10px] font-semibold text-slate-400">
                    <i class="fas fa-heart text-rose-400 me-1"></i> Acompanhe e felicite seus alunos
                </span>
            </div>
        </div>
    </div>

    {{-- 5. Barra de Acesso Rápido a Módulos --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5">
        <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-amber-500"></i> Atalhos de Gestão Docente
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
            <a href="{{ route('teacher.classes.index') }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200/80 text-center transition group">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <span class="text-xs font-extrabold text-slate-800 block">Minhas Turmas</span>
                <span class="text-[10px] text-slate-400">Horários e listas</span>
            </a>

            <a href="{{ route('grades.batch-create') }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-amber-50 hover:border-amber-300 border border-slate-200/80 text-center transition group">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="text-xs font-extrabold text-slate-800 block">Pauta em Lote</span>
                <span class="text-[10px] text-slate-400">Lançamento rápido</span>
            </a>

            <a href="{{ route('teacher.grades.pending') }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-rose-50 hover:border-rose-300 border border-slate-200/80 text-center transition group">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <span class="text-xs font-extrabold text-slate-800 block">Pendências</span>
                <span class="text-[10px] text-slate-400">Pautas por lançar</span>
            </a>

            <a href="{{ route('teacher.leave-requests.index') }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-purple-50 hover:border-purple-300 border border-slate-200/80 text-center transition group">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-medical"></i>
                </div>
                <span class="text-xs font-extrabold text-slate-800 block">Licenças</span>
                <span class="text-[10px] text-slate-400">Pedidos de ausência</span>
            </a>

            <a href="{{ route('teacher.communications.index') }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-blue-50 hover:border-blue-300 border border-slate-200/80 text-center transition group">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <span class="text-xs font-extrabold text-slate-800 block">Comunicados</span>
                <span class="text-[10px] text-slate-400">Avisos e mensagens</span>
            </a>

            <a href="{{ route('teacher.profile') }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 hover:bg-teal-50 hover:border-teal-300 border border-slate-200/80 text-center transition group">
                <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-base mb-2 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-gear"></i>
                </div>
                <span class="text-xs font-extrabold text-slate-800 block">Meu Perfil</span>
                <span class="text-[10px] text-slate-400">Dados cadastrais</span>
            </a>
        </div>
    </div>

</div>
@endsection

