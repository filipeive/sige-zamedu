@extends('layouts.app')

@section('title', 'Gestão de Frequências')
@section('page-title', 'Gestão de Frequências')
@section('page-title-icon', 'fas fa-calendar-check')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Frequências</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-calendar-check text-emerald-600"></i> Assiduidade & Frequências
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Controlo diário de presenças e faltas por turma.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('attendances.index') }}" method="GET" class="flex items-center gap-1.5">
                <input type="date" name="date" class="rounded-lg border-slate-200 text-xs font-semibold text-slate-700 py-1.5 px-2.5 focus:ring-emerald-500"
                       value="{{ $selectedDate }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()">
            </form>
            <a href="{{ route('attendances.reports') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-chart-bar text-emerald-600"></i> Relatórios
            </a>
        </div>
    </div>

    <!-- 2. Stat Cards (Pílula de Cor na Esquerda) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <!-- Total Alunos (Slate Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-slate-600 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Alunos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $globalStats['total_students'] }}</span>
            </div>
        </div>

        <!-- Presentes Hoje (Emerald Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Presentes Hoje</span>
                <span class="text-xl font-black text-emerald-700 mt-0.5 block">{{ $globalStats['total_present'] }}</span>
            </div>
        </div>

        <!-- Ausentes Hoje (Rose Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-rose-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-times"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Ausentes Hoje</span>
                <span class="text-xl font-black text-rose-700 mt-0.5 block">{{ $globalStats['total_absent'] }}</span>
            </div>
        </div>

        <!-- Atrasados Hoje (Amber Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Atrasados Hoje</span>
                <span class="text-xl font-black text-amber-700 mt-0.5 block">{{ $globalStats['total_late'] }}</span>
            </div>
        </div>

        <!-- Por Marcar (Blue Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Por Marcar</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $globalStats['total_unmarked'] }}</span>
            </div>
        </div>
    </div>

    <!-- 3. Turmas Cards Grid -->
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($classes as $classData)
            @php
                $class = $classData['class'];
                $attendanceRate = $classData['attendance_rate'];
                $rateBadge = $attendanceRate >= 90 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($attendanceRate >= 75 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-rose-100 text-rose-800 border border-rose-200');
            @endphp
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-2xs space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800">{{ $class->name }}</h4>
                        <p class="text-[11px] text-slate-400 font-medium">{{ $class->grade_level_name }} • {{ $class->shift_label }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $rateBadge }}">
                        {{ $attendanceRate }}%
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                    <div class="flex justify-between p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-slate-500">Presentes:</span>
                        <strong class="text-emerald-700 font-bold">{{ $classData['present'] }}</strong>
                    </div>
                    <div class="flex justify-between p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-slate-500">Ausentes:</span>
                        <strong class="text-rose-700 font-bold">{{ $classData['absent'] }}</strong>
                    </div>
                </div>

                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $attendanceRate >= 90 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $attendanceRate }}%;"></div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <a href="{{ route('attendances.class-attendances', $class) }}?date={{ $selectedDate }}" class="no-underline inline-flex justify-center items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        <i class="fas fa-eye text-[10px]"></i> Ver
                    </a>
                    @can('mark_attendances')
                        <a href="{{ route('attendances.mark-by-class', $class) }}?date={{ $selectedDate }}" class="no-underline inline-flex justify-center items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
                            <i class="fas fa-edit text-[10px]"></i> Marcar
                        </a>
                    @endcan
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl p-8 text-center border border-slate-200 shadow-2xs">
                <i class="fas fa-calendar-times text-3xl text-slate-300 mb-2"></i>
                <p class="text-xs font-semibold text-slate-600">Nenhuma turma ativa encontrada.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
