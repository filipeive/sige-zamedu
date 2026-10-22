@extends('layouts.app')

@section('title', 'Detalhes da Turma - ' . $class->name)
@section('page-title', 'Detalhes da Turma')
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}" class="text-slate-400 hover:text-slate-600">Minhas Turmas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">{{ $class->name }}</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center text-2xl font-bold">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-1">
                        {{ $class->grade_level_name ?? ($class->grade_level . 'ª Classe') }}
                    </div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">{{ $class->name }}</h1>
                    <p class="text-xs text-slate-300 mt-0.5 flex flex-wrap items-center gap-4">
                        <span><i class="fas fa-calendar-alt text-slate-400 mr-1.5"></i> Ano Letivo: <strong>{{ $class->school_year }}</strong></span>
                        <span><i class="fas fa-door-open text-slate-400 mr-1.5"></i> Sala: <strong>{{ $class->classroom ?? 'N/A' }}</strong></span>
                        <span><i class="fas fa-users text-slate-400 mr-1.5"></i> Alunos: <strong>{{ $class->students_count }}</strong></span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('teacher.attendance.class', $class->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-xs transition-all">
                    <i class="fas fa-clipboard-check"></i>
                    Fazer Chamada
                </a>
                <a href="{{ route('teacher.gradebook', $class->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold shadow-xs transition-all">
                    <i class="fas fa-tasks"></i>
                    Lançar Notas
                </a>
                <a href="{{ route('teacher.classes.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-xs border border-white/10 transition-all">
                    <i class="fas fa-arrow-left"></i>
                    Turmas
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid: Stats & Students Table -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Class Stats (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Capacity Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-emerald-600"></i>
                    Ocupação da Turma
                </h2>

                @php
                    $max = $class->max_students ?: 45;
                    $capPct = round(min(100, ($class->students_count / max(1, $max)) * 100));
                @endphp

                <div class="text-center py-2">
                    <div class="text-3xl font-bold font-mono text-slate-900">{{ $capPct }}%</div>
                    <p class="text-xs text-slate-400 mt-0.5">Capacidade Preenchida</p>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 mt-3 overflow-hidden">
                        <div class="h-2.5 rounded-full {{ $capPct > 90 ? 'bg-rose-500' : ($capPct > 70 ? 'bg-emerald-500' : 'bg-blue-500') }} transition-all" style="width: {{ $capPct }}%"></div>
                    </div>
                    <div class="flex justify-between text-[11px] text-slate-400 font-mono mt-1.5">
                        <span>{{ $class->students_count }} matriculados</span>
                        <span>Máx: {{ $max }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-slate-100 text-center">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xl font-bold font-mono text-emerald-700">{{ $class->students_count }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">Alunos</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xl font-bold font-mono text-blue-700">{{ $class->subjects->count() }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">Disciplinas</div>
                    </div>
                </div>
            </div>

            <!-- Disciplinas da Turma -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <i class="fas fa-book text-emerald-600"></i>
                        Disciplinas da Turma
                    </h2>
                    <span class="text-[11px] font-semibold text-slate-400">{{ $class->subjects->count() }}</span>
                </div>

                <div class="p-4 divide-y divide-slate-100 text-xs">
                    @forelse ($class->subjects as $subject)
                        <div class="py-2.5 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-slate-800">{{ $subject->name }}</h3>
                                <div class="text-[10px] text-slate-400">
                                    {{ $subject->code ?? 'N/A' }} &bull; {{ $subject->weekly_hours ?? 0 }}h/sem
                                </div>
                            </div>
                            <a href="{{ route('teacher.gradebook', $class->id) }}?subject_id={{ $subject->id }}"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200 text-[11px] font-semibold transition-colors">
                                <i class="fas fa-edit text-[10px]"></i> Notas
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Nenhuma disciplina atribuída a esta turma.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Student List (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-user-graduate text-xs"></i>
                        </span>
                        Lista de Alunos da Turma
                    </h2>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $class->students_count }} alunos
                    </span>
                </div>

                @if ($class->students->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    <th class="py-3 px-4">Aluno</th>
                                    <th class="py-3 px-4 text-center">Presença Hoje</th>
                                    <th class="py-3 px-4">Contacto</th>
                                    <th class="py-3 px-4 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($class->students as $student)
                                    <tr class="hover:bg-slate-50/75 transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                    <div class="text-[10px] text-slate-400 font-mono">{{ $student->student_number ?? 'S/N' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if (isset($todayAttendance[$student->id]))
                                                @php
                                                    $attendance = $todayAttendance[$student->id];
                                                    $stConfig = [
                                                        'present' => ['label' => 'Presente', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                                        'absent' => ['label' => 'Ausente', 'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
                                                        'late' => ['label' => 'Atrasado', 'cls' => 'bg-amber-50 text-amber-700 border-amber-200'],
                                                    ];
                                                    $cfg = $stConfig[$attendance->status] ?? ['label' => $attendance->status, 'cls' => 'bg-slate-50 text-slate-600 border-slate-200'];
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $cfg['cls'] }}">
                                                    {{ $cfg['label'] }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                    Por registar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-[11px] text-slate-600 whitespace-nowrap">
                                            @if ($student->phone)
                                                <div><i class="fas fa-phone text-slate-400 mr-1 text-[10px]"></i> {{ $student->phone }}</div>
                                            @elseif ($student->emergency_phone)
                                                <div><i class="fas fa-phone-alt text-slate-400 mr-1 text-[10px]"></i> {{ $student->emergency_phone }}</div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="{{ route('teacher.attendance.class', $class->id) }}" 
                                                   class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 flex items-center justify-center transition-colors"
                                                   title="Registar Presença">
                                                    <i class="fas fa-clipboard-check text-xs"></i>
                                                </a>
                                                <a href="{{ route('teacher.gradebook', $class->id) }}?student_id={{ $student->id }}" 
                                                   class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200 flex items-center justify-center transition-colors"
                                                   title="Ver Notas">
                                                    <i class="fas fa-tasks text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 text-slate-400">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-user-graduate text-xl"></i>
                        </div>
                        <p class="text-xs font-medium text-slate-600">Nenhum aluno matriculado nesta turma.</p>
                    </div>
                @endif
            </div>

            <!-- Quick Action Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('teacher.attendance.class', $class->id) }}"
                   class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-emerald-300 hover:shadow-xs transition-all flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                        <i class="fas fa-clipboard-check"></i>
                    </span>
                    <div>
                        <div class="font-bold text-slate-800 text-xs">Fazer Chamada</div>
                        <div class="text-[10px] text-slate-400">Registo de presenças</div>
                    </div>
                </a>

                <a href="{{ route('teacher.gradebook', $class->id) }}"
                   class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-amber-300 hover:shadow-xs transition-all flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0">
                        <i class="fas fa-tasks"></i>
                    </span>
                    <div>
                        <div class="font-bold text-slate-800 text-xs">Lançar Notas</div>
                        <div class="text-[10px] text-slate-400">Pauta e avaliações</div>
                    </div>
                </a>

                <a href="{{ route('teacher.classes.students', $class->id) }}"
                   class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-blue-300 hover:shadow-xs transition-all flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shrink-0">
                        <i class="fas fa-id-badge"></i>
                    </span>
                    <div>
                        <div class="font-bold text-slate-800 text-xs">Fichas dos Alunos</div>
                        <div class="text-[10px] text-slate-400">Perfil individual</div>
                    </div>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
