@extends('layouts.app')

@section('title', 'Relatório de Frequências')
@section('page-title', 'Relatório de Frequências')
@section('page-title-icon', 'fas fa-chalkboard-user')

@section('breadcrumbs')
    <li class="breadcrumb-item">Presenças</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Relatório de Frequências</li>
@endsection

@section('title', 'Relatório de Frequências - ' . $class->name)
@section('content')
<div class="w-full max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar Info & Resumo Mensal -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Informações da Turma -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i> Informações da Turma
                    </h3>
                    <a href="{{ route('attendances.class-attendances', $class) }}" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Nome:</span>
                        <strong class="text-slate-900 font-bold">{{ $class->name }}</strong>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-2">
                        <span class="text-slate-500 font-medium">Nível:</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">{{ $class->grade_level_name }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-2">
                        <span class="text-slate-500 font-medium">Turno:</span>
                        @php
                            $shiftBadge = match($class->shift) {
                                'afternoon' => 'bg-amber-100 text-amber-800',
                                'night' => 'bg-sky-100 text-sky-800',
                                default => 'bg-emerald-100 text-emerald-800',
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full font-bold {{ $shiftBadge }}">{{ $class->shift_label }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-2">
                        <span class="text-slate-500 font-medium">Total Alunos:</span>
                        <strong class="text-slate-900 font-bold text-sm">{{ $students->count() }}</strong>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-2">
                        <span class="text-slate-500 font-medium">Professor:</span>
                        @if($class->teacher)
                            <strong class="text-slate-900 font-bold">{{ $class->teacher->first_name }} {{ $class->teacher->last_name }}</strong>
                        @else
                            <span class="text-slate-400">Não atribuído</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-2 mt-6">
                    <a href="{{ route('attendances.class-attendances', $class) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-sm transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-list"></i> Ver Frequências
                    </a>
                    @can('mark_attendances')
                        <a href="{{ route('attendances.mark-by-class', $class) }}" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-pen-to-square"></i> Marcar Frequências
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Resumo Mensal -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-pie text-emerald-600"></i> Resumo Mensal
                </h3>

                @php
                    $totalDays = $students->sum('total_days');
                    $totalPresent = $students->sum('present');
                    $totalAbsent = $students->sum('absent');
                    $totalLate = $students->sum('late');
                    $totalExcused = $students->sum('excused');
                    $avgRate = $students->count() > 0 ? round($students->avg('attendance_rate'), 1) : 0;
                @endphp

                <div class="text-center mb-6">
                    <div class="text-4xl font-extrabold tracking-tight {{ $avgRate >= 90 ? 'text-emerald-600' : ($avgRate >= 75 ? 'text-amber-600' : 'text-rose-600') }}">
                        {{ $avgRate }}%
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1 block">Média de Presença</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Total Dias:</span>
                        <strong class="text-slate-800 font-bold text-sm">{{ $totalDays }}</strong>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Presentes:</span>
                        <strong class="text-emerald-700 font-bold text-sm">{{ $totalPresent }}</strong>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Ausentes:</span>
                        <strong class="text-rose-700 font-bold text-sm">{{ $totalAbsent }}</strong>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Atrasados:</span>
                        <strong class="text-amber-600 font-bold text-sm">{{ $totalLate }}</strong>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center col-span-2">
                        <span class="text-slate-500 font-medium">Justificados:</span>
                        <strong class="text-sky-700 font-bold text-sm">{{ $totalExcused }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relatório por Aluno -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 bg-slate-50/50 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-users text-emerald-600"></i> Relatório Individual
                    </h3>
                </div>

                @if($students->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600 divide-y divide-slate-200">
                            <thead class="bg-slate-50 uppercase font-semibold text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Aluno</th>
                                    <th class="px-4 py-3">Nº</th>
                                    <th class="px-4 py-3">Dias</th>
                                    <th class="px-4 py-3">Presentes</th>
                                    <th class="px-4 py-3">Ausentes</th>
                                    <th class="px-4 py-3">Atrasados</th>
                                    <th class="px-4 py-3">Justificados</th>
                                    <th class="px-4 py-3">Taxa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-medium">
                                @foreach($students as $item)
                                    @php
                                        $student = $item['student'];
                                        $rate = $item['attendance_rate'];
                                        $rateColor = $rate >= 90 ? 'bg-emerald-100 text-emerald-800' : ($rate >= 75 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800');
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 font-bold text-slate-900">
                                            {{ $student->full_name }}
                                        </td>
                                        <td class="px-4 py-3 font-mono text-slate-500">{{ $student->student_number }}</td>
                                        <td class="px-4 py-3 font-bold text-slate-700">{{ $item['total_days'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">{{ $item['present'] }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-extrabold text-[10px]">{{ $item['absent'] }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px]">{{ $item['late'] }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 font-extrabold text-[10px]">{{ $item['excused'] }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full font-black text-xs {{ $rateColor }}">
                                                {{ $rate }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center text-slate-400">
                        <i class="fa-solid fa-users text-4xl mb-3 text-slate-300 block"></i>
                        <p class="text-xs text-slate-500 font-medium">Nenhum aluno matriculado nesta turma.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

