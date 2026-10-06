@extends('layouts.app')

@section('title', 'Dashboard Pedagógico')
@section('page-title', 'Dashboard Pedagógico')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Dashboard Pedagógico</li>
@endsection

@section('content')
<div class="w-full mx-auto px-4 py-6 space-y-6">

    <!-- Header Contextual -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 mb-1">{{ $greetingData['school_name'] }} · Ano Lectivo {{ $greetingData['school_year'] }}</p>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $greetingData['greeting'] }}</h1>
        </div>
        <div class="text-right">
            <p class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl inline-block">{{ $greetingData['current_date'] }}</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Alunos</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_students']) }}</p>
                <span class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $stats['total_classes'] }} turmas
                </span>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <i class="fas fa-user-graduate text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Professores</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_teachers']) }}</p>
                <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                    {{ $stats['total_subjects'] }} disciplinas
                </span>
            </div>
            <div class="p-3 bg-sky-50 text-sky-600 rounded-xl">
                <i class="fas fa-chalkboard-user text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Presença Média</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['average_attendance'] }}%</p>
                <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                    Este mês
                </span>
            </div>
            <div class="p-3 bg-teal-50 text-teal-600 rounded-xl">
                <i class="fas fa-clipboard-check text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-start justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Média Global</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['class_performance_avg'] }} <span class="text-xs font-semibold text-slate-400">/ 20</span></p>
                <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-medium">
                    Desempenho
                </span>
            </div>
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <i class="fas fa-award text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Desempenho por Turma -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-900">Desempenho por Turma</h2>
            <p class="text-xs text-slate-500">Média geral e progresso dos alunos por classe</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Turma</th>
                        <th class="py-3 px-4">Professor / Director</th>
                        <th class="py-3 px-4">Alunos</th>
                        <th class="py-3 px-4">Média</th>
                        <th class="py-3 px-6 text-right" style="width: 180px;">Progresso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($classPerformance as $class)
                        @php
                            $cName = $class['name'] ?? $class['class_name'] ?? 'Turma';
                            $tName = $class['teacher_name'] ?? 'N/A';
                            $sCount = $class['student_count'] ?? $class['total_students'] ?? 0;
                            $avgGrade = $class['avg_grade'] ?? $class['average_grade'] ?? 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-6 font-bold text-slate-900">{{ $cName }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $tName }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $sCount }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs">
                                    {{ number_format($avgGrade, 1) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, ($avgGrade / 20) * 100) }}%;"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Próximos Exames & Calendário -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-6 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 mb-1">Próximos Exames</h2>
                <p class="text-xs text-slate-500 mb-4">Avaliações e exames nacionais agendados</p>

                <div class="space-y-3">
                    @forelse($upcomingExams as $exam)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div>
                                <p class="font-bold text-xs text-slate-900">{{ $exam->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    <i class="far fa-clock mr-1"></i> {{ $exam->event_date->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs">Exame</span>
                        </div>
                    @empty
                        <p class="text-center py-6 text-xs text-slate-400 font-medium">Nenhum exame agendado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-6">
            @include('partials.calendar')
        </div>
    </div>

</div>
@endsection
