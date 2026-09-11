@extends('layouts.app')

@section('title', 'Frequências - ' . $class->name)
@section('page-title', 'Frequências da Turma')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar Info & Stats -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Informações da Turma -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i> Informações da Turma
                    </h3>
                    <a href="{{ route('attendances.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i> Voltar
                    </a>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center">
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
                    @can('mark_attendances')
                        <a href="{{ route('attendances.mark-by-class', $class) }}?date={{ $selectedDate }}" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-pen-to-square"></i> Marcar Frequências
                        </a>
                    @endcan
                    <a href="{{ route('attendances.class-report', $class) }}" class="w-full py-2.5 px-4 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-chart-pie"></i> Ver Relatório
                    </a>
                </div>
            </div>

            <!-- Estatísticas do Dia -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-pie text-emerald-600"></i> Frequência do Dia
                </h3>

                @php
                    $total = $students->count();
                    $present = $students->sum(fn($s) => ($s['attendance_status'] ?? null) === 'present' ? 1 : 0);
                    $absent = $students->sum(fn($s) => ($s['attendance_status'] ?? null) === 'absent' ? 1 : 0);
                    $late = $students->sum(fn($s) => ($s['attendance_status'] ?? null) === 'late' ? 1 : 0);
                    $excused = $students->sum(fn($s) => ($s['attendance_status'] ?? null) === 'excused' ? 1 : 0);
                    $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                @endphp

                <div class="text-center mb-6">
                    <div class="text-4xl font-extrabold tracking-tight {{ $rate >= 90 ? 'text-emerald-600' : ($rate >= 75 ? 'text-amber-600' : 'text-rose-600') }}">
                        {{ $rate }}%
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1 block">Taxa de Presença</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Presentes:</span>
                        <strong class="text-emerald-700 font-bold text-sm">{{ $present }}</strong>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Ausentes:</span>
                        <strong class="text-rose-700 font-bold text-sm">{{ $absent }}</strong>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Atrasados:</span>
                        <strong class="text-amber-600 font-bold text-sm">{{ $late }}</strong>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Justificados:</span>
                        <strong class="text-sky-700 font-bold text-sm">{{ $excused }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Alunos e Frequências -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 md:p-6 bg-slate-50/50 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-users text-emerald-600"></i> Lista de Alunos
                    </h3>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Data:</label>
                        <input type="date" class="rounded-xl border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white" value="{{ $selectedDate }}"
                               onchange="window.location.href='{{ route('attendances.class-attendances', $class) }}?date=' + this.value">
                    </div>
                </div>

                @if($students->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-600 divide-y divide-slate-200">
                            <thead class="bg-slate-50 uppercase font-semibold text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Aluno</th>
                                    <th class="px-4 py-3">Nº</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Hora Chegada</th>
                                    <th class="px-4 py-3">Marcado Por</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 font-medium">
                                @foreach($students as $item)
                                    @php
                                        $student = $item['student'];
                                        $attendance = $item['attendance'];
                                        $status = $item['attendance_status'];
                                        $arrivalTime = $item['arrival_time'];
                                        $markedBy = $item['marked_by'];
                                        $statusColors = [
                                            'present' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'absent' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            'late' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            'excused' => 'bg-sky-100 text-sky-800 border-sky-200'
                                        ];
                                        $statusIcons = [
                                            'present' => 'fa-check',
                                            'absent' => 'fa-xmark',
                                            'late' => 'fa-clock',
                                            'excused' => 'fa-comment'
                                        ];
                                        $statusNames = [
                                            'present' => 'Presente',
                                            'absent' => 'Ausente',
                                            'late' => 'Atrasado',
                                            'excused' => 'Justificado'
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 font-bold text-slate-900">
                                            {{ $student->full_name }}
                                        </td>
                                        <td class="px-4 py-3 font-mono text-slate-500">{{ $student->student_number }}</td>
                                        <td class="px-4 py-3">
                                            @if($status)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-xs font-bold {{ $statusColors[$status] }}">
                                                    <i class="fa-solid {{ $statusIcons[$status] }}"></i> {{ $statusNames[$status] }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200 text-xs font-medium">
                                                    Não marcado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 font-mono">
                                            @if($arrivalTime)
                                                {{ $arrivalTime->format('H:i') }}
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-slate-600">
                                            @if($markedBy)
                                                <span>{{ $markedBy->name }}</span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
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

