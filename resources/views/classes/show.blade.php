@extends('layouts.app')

@section('title', $class->name)
@section('page-title', $class->name)
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Turmas</a></li>
    <li class="breadcrumb-item active">{{ $class->name }}</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Turma: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Detalhes da turma, estatísticas de alunos e ações rápidas.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('edit_classes')
                <a href="{{ route('classes.edit', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-edit"></i> Editar Turma
                </a>
            @endcan
            <a href="{{ route('classes.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Sidebar Info & Stats (1/3) -->
        <div class="space-y-4">
            <!-- Info Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i> Informações da Turma
                    </h3>
                </div>
                <div class="p-4 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Nome:</span>
                        <span class="text-slate-800 font-semibold">{{ $class->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Nível:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $class->grade_level_name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Turno:</span>
                        @php
                            $shiftClass = match($class->shift) {
                                'afternoon' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                'night' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                default => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $shiftClass }}">{{ $class->shift_label }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Ano Letivo:</span>
                        <span class="text-slate-800 font-semibold">{{ $class->school_year }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-700">Sala:</span>
                        <span class="text-slate-800 font-semibold">{{ $class->classroom ?? 'N/A' }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-700 block mb-1">Modo de Ensino:</span>
                        @if($class->isSingleTeacher())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-user-tie text-[9px]"></i> Professor Regente
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200">
                                <i class="fas fa-book text-[9px]"></i> Por Disciplina
                            </span>
                        @endif
                    </div>
                    @if($class->isSingleTeacher())
                    <div class="pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-700 block mb-1">Professor Regente:</span>
                        @if($class->teacher)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                    {{ substr($class->teacher->first_name, 0, 1) }}{{ substr($class->teacher->last_name, 0, 1) }}
                                </div>
                                <span class="font-bold text-slate-800">{{ $class->teacher->first_name }} {{ $class->teacher->last_name }}</span>
                            </div>
                        @else
                            <span class="text-slate-400 italic">Não atribuído</span>
                        @endif
                    </div>
                    @if($class->assistantTeacher)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-700 block mb-1">Professor Assistente:</span>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-blue-500 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ substr($class->assistantTeacher->first_name, 0, 1) }}{{ substr($class->assistantTeacher->last_name, 0, 1) }}
                            </div>
                            <span class="font-bold text-slate-800">{{ $class->assistantTeacher->first_name }} {{ $class->assistantTeacher->last_name }}</span>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-700 block mb-1">Professores:</span>
                        @php $distinctTeachers = $class->classSubjects->pluck('teacher_id')->filter()->unique(); @endphp
                        <span class="text-[11px] text-slate-700 font-semibold">{{ $distinctTeachers->count() }} atribuído(s) por disciplina</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-700">Status:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $class->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $class->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Modo de Ensino (painel contextual) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        @if($class->isSingleTeacher())
                            <i class="fas fa-user-tie text-blue-600"></i> Professor Regente &amp; Assistente
                        @else
                            <i class="fas fa-book text-purple-600"></i> Professores por Disciplina
                        @endif
                    </h3>
                </div>
                <div class="p-4 space-y-3 text-xs">
                    @if($class->isSingleTeacher())
                        {{-- PRIMÁRIO: 1 regente + 1 assistente --}}
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-slate-700">Professor Regente:</span>
                            @if($class->teacher)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                        {{ substr($class->teacher->first_name, 0, 1) }}{{ substr($class->teacher->last_name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-800">{{ $class->teacher->full_name }}</span>
                                </div>
                            @else
                                <span class="text-rose-600 font-bold italic">Não atribuído</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('classes.update-assistant', $class) }}" class="pt-2 border-t border-slate-100">
                            @csrf
                            <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Professor Assistente (opcional)</label>
                            <div class="flex items-center gap-1.5">
                                <select name="assistant_teacher_id" class="flex-1 rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">— Sem assistente —</option>
                                    @foreach($availableTeachers ?? [] as $t)
                                        @if(!$class->teacher || $t->id !== $class->teacher->id)
                                            <option value="{{ $t->id }}" @selected($class->assistant_teacher_id == $t->id)>{{ $t->full_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-[10px] font-extrabold transition">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </form>
                    @else
                        {{-- SECUNDÁRIO: matriz professor por disciplina --}}
                        <form method="POST" action="{{ route('classes.assign-subjects', $class) }}">
                            @csrf
                            <div class="space-y-1.5">
                                @forelse($class->classSubjects as $cs)
                                    <div class="flex items-center gap-2 p-1.5 rounded-lg border border-slate-200/80">
                                        <span class="font-bold text-slate-800 text-[11px] w-32 truncate" title="{{ $cs->subject?->name }}">{{ $cs->subject?->name ?? '—' }}</span>
                                        <select name="assignments[{{ $loop->index }}][class_subject_id]" class="hidden">{{-- id holder --}}
                                            <option value="{{ $cs->id }}" selected>{{ $cs->id }}</option>
                                        </select>
                                        <input type="hidden" name="assignments[{{ $loop->index }}][class_subject_id]" value="{{ $cs->id }}">
                                        <select name="assignments[{{ $loop->index }}][teacher_id]" class="flex-1 rounded border-slate-200 py-1 text-[11px] font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                                            <option value="">— Sem professor —</option>
                                            @foreach($availableTeachers ?? [] as $t)
                                                <option value="{{ $t->id }}" @selected($cs->teacher_id == $t->id)>{{ $t->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @empty
                                    <p class="text-slate-400 italic">Nenhuma disciplina atribuída a esta turma.</p>
                                @endforelse
                            </div>
                            @if($class->classSubjects->count() > 0)
                                <button type="submit" class="mt-3 w-full inline-flex justify-center items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-700 hover:bg-purple-800 text-white text-xs font-extrabold transition">
                                    <i class="fas fa-save"></i> Actualizar Atribuições
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-bar text-emerald-600"></i> Estatísticas
                    </h3>
                </div>
                <div class="p-4 space-y-3 text-xs">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-slate-700">Capacidade:</span>
                            <span class="text-slate-500 font-semibold">{{ $stats['total_students'] }}/{{ $class->max_students }} alunos ({{ $stats['capacity_percentage'] }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $stats['capacity_percentage'] > 80 ? 'bg-rose-500' : ($stats['capacity_percentage'] > 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $stats['capacity_percentage'] }}%"></div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-700">Gênero:</span>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200"><i class="fas fa-mars mr-1"></i> {{ $stats['male_students'] }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-pink-100 text-pink-800 border border-pink-200"><i class="fas fa-venus mr-1"></i> {{ $stats['female_students'] }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-700">Idade Média:</span>
                        <span class="font-extrabold text-slate-800">{{ $stats['average_age'] }} anos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content (2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Quick Actions Bar -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <a href="{{ route('classes.students', $class->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 transition">
                        <i class="fas fa-users text-lg mb-1"></i>
                        <span class="text-xs font-bold">Alunos</span>
                    </a>
                    <a href="{{ route('attendances.mark-by-class', $class->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/80 transition">
                        <i class="fas fa-clipboard-check text-lg mb-1"></i>
                        <span class="text-xs font-bold">Presenças</span>
                    </a>
                    @can('create_grades')
                    <a href="{{ route('grades.index', ['class_id' => $class->id]) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 transition">
                        <i class="fas fa-edit text-lg mb-1"></i>
                        <span class="text-xs font-bold">Lançar Notas</span>
                    </a>
                    @endcan
                    @can('view_grades')
                    <a href="{{ route('pautas.trimestral', $class->id) }}" class="no-underline flex flex-col items-center justify-center p-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 transition">
                        <i class="fas fa-table text-lg mb-1"></i>
                        <span class="text-xs font-bold">Pauta Trimestral</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Students List Preview -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-users text-emerald-600"></i> Alunos da Turma ({{ $stats['total_students'] }})
                    </h3>
                    <a href="{{ route('classes.students', $class->id) }}" class="no-underline text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Ver Todos <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                    </a>
                </div>
                <div>
                    @if($class->students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                        <th class="py-2.5 px-4">Nome</th>
                                        <th class="py-2.5 px-3">Número</th>
                                        <th class="py-2.5 px-3">Gênero</th>
                                        <th class="py-2.5 px-3">Idade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($class->students->take(5) as $student)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 px-4 font-bold text-slate-800">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </div>
                                                <span>{{ $student->first_name }} {{ $student->last_name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-600">{{ $student->student_number }}</td>
                                        <td class="py-2.5 px-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $student->gender == 'male' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-pink-100 text-pink-800 border border-pink-200' }}">
                                                <i class="fas {{ $student->gender == 'male' ? 'fa-mars' : 'fa-venus' }}"></i>
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-600">{{ $student->birthdate ? $student->birthdate->diffInYears(now()) : 'N/A' }} anos</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($class->students->count() > 5)
                            <div class="p-3 text-center border-t border-slate-100">
                                <a href="{{ route('classes.students', $class->id) }}" class="no-underline text-xs font-bold text-emerald-700 hover:text-emerald-800">
                                    Ver mais {{ $class->students->count() - 5 }} alunos
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="p-6 text-center text-slate-400">
                            <i class="fas fa-users text-2xl text-slate-300 mb-2"></i>
                            <p class="text-xs font-semibold text-slate-600 mb-3">Nenhum aluno matriculado nesta turma.</p>
                            <a href="{{ route('classes.students', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-user-plus"></i> Adicionar Alunos
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Birthdays -->
            @if($upcomingBirthdays->count() > 0)
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-birthday-cake text-amber-500"></i> Próximos Aniversários
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($upcomingBirthdays as $student)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-amber-50/60 border border-amber-200/60">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center shrink-0">
                                    <i class="fas fa-birthday-cake text-xs"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</h4>
                                    <p class="text-[10px] text-slate-500">
                                        {{ $student->birthdate->format('d/m') }} ({{ $student->birthdate->diffInYears(now()) + 1 }} anos)
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('students.show', $student->id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-bold shadow-2xs transition">
                                <i class="fas fa-user"></i> Ver Perfil
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
