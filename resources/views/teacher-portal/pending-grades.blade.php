@extends('layouts.app')

@section('title', 'Avaliações Pendentes')
@section('page-title', 'Avaliações Pendentes')
@section('page-title-icon', 'fas fa-clock')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Avaliações Pendentes</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30 mb-1">
                    <i class="fas fa-clock"></i> Controlo Pedagógico
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Avaliações Pendentes</h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Acompanhamento de prazos e preenchimento de pautas de avaliação
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.classes.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-xs border border-white/10 transition-all">
                    <i class="fas fa-chalkboard"></i>
                    Minhas Turmas
                </a>
            </div>
        </div>
    </div>

    <!-- 3 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs border-l-4 border-l-amber-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pendentes</div>
                <div class="text-3xl font-bold font-mono text-amber-600 mt-1">
                    {{ $upcomingAssessments->count() + $overdueAssessments->count() }}
                </div>
                <div class="text-[11px] text-rose-600 font-medium mt-0.5 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    {{ $overdueAssessments->count() }} em atraso
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alunos sob Avaliação</div>
                <div class="text-3xl font-bold font-mono text-emerald-700 mt-1">
                    {{ $teacher->classes->sum('students_count') }}
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                    Em {{ $teacher->classes->count() }} turma(s) ativas
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs border-l-4 border-l-blue-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Próximos Prazos</div>
                <div class="text-3xl font-bold font-mono text-blue-600 mt-1">
                    {{ $upcomingAssessments->count() }}
                </div>
                <div class="text-[11px] text-blue-600 mt-0.5">
                    Nos próximos 7 dias
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
    </div>

    <!-- Avaliações Vencidas (se existirem) -->
    @if($overdueAssessments->count() > 0)
        <div class="bg-white rounded-2xl border border-rose-200 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-rose-100 bg-rose-50/60 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-xs"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-rose-900">Avaliações com Prazo Vencido</h2>
                        <span class="text-[10px] text-rose-600">Requerem lançamento prioritário de notas</span>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-600 text-white">
                    {{ $overdueAssessments->count() }} vencida(s)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Avaliação</th>
                            <th class="py-3 px-4">Turma / Disciplina</th>
                            <th class="py-3 px-4">Prazo Limite</th>
                            <th class="py-3 px-4">Progresso</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($overdueAssessments as $assessment)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800">{{ $assessment->title }}</div>
                                    <div class="text-[10px] text-slate-400 capitalize">{{ $assessment->type ?? 'Avaliação' }}</div>
                                    @if($assessment->description)
                                        <div class="text-[10px] text-slate-500 mt-0.5 line-clamp-1">{{ $assessment->description }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-800">{{ $assessment->class->name ?? 'Turma N/A' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $assessment->subject->name ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="text-rose-600 font-semibold flex items-center gap-1">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        {{ $assessment->due_date ? $assessment->due_date->diffForHumans() : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $assessment->due_date ? $assessment->due_date->format('d/m/Y') : '' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $totalStudents = $assessment->class->students_count ?? 0;
                                        $gradedStudents = 0;
                                        $percentage = $totalStudents > 0 ? round(($gradedStudents / $totalStudents) * 100) : 0;
                                    @endphp
                                    <div class="w-32">
                                        <div class="flex justify-between text-[10px] font-mono mb-1">
                                            <span class="text-slate-500">{{ $gradedStudents }}/{{ $totalStudents }}</span>
                                            <span class="text-rose-600 font-bold">{{ $percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Vencida
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($assessment->class)
                                        <a href="{{ route('teacher.gradebook', $assessment->class_id) }}?subject_id={{ $assessment->subject_id }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-2xs transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                            Lançar Notas
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Próximas Avaliações -->
    @if($upcomingAssessments->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-xs"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Próximas Avaliações nos Próximos 7 Dias</h2>
                        <span class="text-[10px] text-slate-400">Prazos de submissão a aproximar</span>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $upcomingAssessments->count() }} avaliação(ões)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Avaliação</th>
                            <th class="py-3 px-4">Turma / Disciplina</th>
                            <th class="py-3 px-4">Prazo Limite</th>
                            <th class="py-3 px-4">Progresso</th>
                            <th class="py-3 px-4 text-center">Urgência</th>
                            <th class="py-3 px-4 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($upcomingAssessments as $assessment)
                            @php
                                $daysUntilDue = $assessment->due_date ? $assessment->due_date->diffInDays(now()) : 99;
                                $statusCls = $daysUntilDue <= 1 ? 'bg-rose-50 text-rose-700 border-rose-200' : ($daysUntilDue <= 3 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200');
                                $statusText = $daysUntilDue <= 1 ? 'Urgente (amanhã)' : ($daysUntilDue <= 3 ? $daysUntilDue . ' dias' : 'Em dia');
                            @endphp
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800">{{ $assessment->title }}</div>
                                    <div class="text-[10px] text-slate-400 capitalize">{{ $assessment->type ?? 'Avaliação' }}</div>
                                    @if($assessment->description)
                                        <div class="text-[10px] text-slate-500 mt-0.5 line-clamp-1">{{ $assessment->description }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-800">{{ $assessment->class->name ?? 'Turma N/A' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $assessment->subject->name ?? 'N/A' }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="text-amber-700 font-semibold flex items-center gap-1">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        {{ $assessment->due_date ? $assessment->due_date->diffForHumans() : '-' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $assessment->due_date ? $assessment->due_date->format('d/m/Y') : '' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $totalStudents = $assessment->class->students_count ?? 0;
                                        $gradedStudents = 0;
                                        $percentage = $totalStudents > 0 ? round(($gradedStudents / $totalStudents) * 100) : 0;
                                    @endphp
                                    <div class="w-32">
                                        <div class="flex justify-between text-[10px] font-mono mb-1">
                                            <span class="text-slate-500">{{ $gradedStudents }}/{{ $totalStudents }}</span>
                                            <span class="text-amber-700 font-bold">{{ $percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusCls }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($assessment->class)
                                        <a href="{{ route('teacher.gradebook', $assessment->class_id) }}?subject_id={{ $assessment->subject_id }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200 font-semibold text-xs transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                            Lançar Notas
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($upcomingAssessments->count() == 0 && $overdueAssessments->count() == 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Tudo em Dia!</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto mb-4">
                Não existem avaliações com prazos pendentes ou atrasados neste momento.
            </p>
            <a href="{{ route('teacher.classes.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition-colors">
                <i class="fas fa-chalkboard"></i>
                Ver Minhas Turmas
            </a>
        </div>
    @endif

</div>
@endsection
