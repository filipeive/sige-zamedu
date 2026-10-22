@extends('layouts.app')

@section('title', 'Alunos da Turma - ' . ($class->name ?? 'Turma'))
@section('page-title', 'Alunos — ' . ($class->name ?? 'Turma'))
@section('page-title-icon', 'fas fa-users')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}" class="text-slate-400 hover:text-slate-600">Minhas Turmas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Alunos</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-1">
                    <i class="fas fa-users"></i> Pauta da Turma
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Alunos &bull; {{ $class->name }}</h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    {{ $class->grade_level_name ?? ($class->grade_level . 'ª Classe') }} &bull; Total: {{ $students->count() }} alunos matriculados
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.classes.detail', $class->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-xs border border-white/10 transition-all">
                    <i class="fas fa-arrow-left"></i>
                    Voltar à Turma
                </a>
            </div>
        </div>
    </div>

    <!-- Student List Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-xs"></i>
                </span>
                Lista Nominal dos Alunos
            </h2>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                {{ $students->count() }} alunos
            </span>
        </div>

        @if($students->isEmpty())
            <div class="text-center py-12 text-slate-400">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <p class="text-xs font-medium text-slate-600">Nenhum aluno registado nesta turma.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Aluno</th>
                            <th class="py-3 px-4">Nº Matrícula</th>
                            <th class="py-3 px-4">Turma</th>
                            <th class="py-3 px-4 text-center">Ações Rápidas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $student)
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $student->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono font-medium text-slate-700">
                                    {{ $student->student_number ?? '—' }}
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-800">
                                    {{ $class->name }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('teacher.attendance.class', $class->id) }}"
                                           class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 flex items-center justify-center transition-colors"
                                           title="Presenças">
                                            <i class="fas fa-clipboard-check text-xs"></i>
                                        </a>
                                        <a href="{{ route('teacher.gradebook', $class->id) }}?student_id={{ $student->id }}"
                                           class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200 flex items-center justify-center transition-colors"
                                           title="Notas">
                                            <i class="fas fa-tasks text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
