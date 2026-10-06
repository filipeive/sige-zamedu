@extends('layouts.app')

@section('title', 'Turmas da Disciplina')
@section('page-title', 'Turmas da Disciplina')
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Disciplinas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.show', $subject->id) }}">{{ $subject->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Turmas</li>
@endsection

@section('title', 'Turmas - ' . $subject->name)
@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard text-emerald-600"></i> Turmas de {{ $subject->name }} ({{ $subject->code }})
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Nível: <strong>{{ $gradeLevels[$subject->grade_level] ?? $subject->grade_level }}</strong> | 
                Horas: <strong>{{ $subject->weekly_hours }}h/semana</strong> | 
                Turmas Associadas: <strong>{{ $subject->classSubjects->count() }}</strong>
            </p>
        </div>
        <a href="{{ route('subjects.show', $subject->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Disciplina
        </a>
    </div>

    <!-- Associate Form -->
    @can('manage_subjects')
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="fas fa-link text-emerald-600"></i> Associar a Nova Turma
        </h3>
        <form action="{{ route('subjects.assign-to-class', $subject->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            @csrf
            <div class="sm:col-span-5">
                <label for="class_id" class="block text-xs font-bold text-slate-700 mb-1">Turma *</label>
                <select name="class_id" id="class_id" required
                        class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('class_id') border-rose-500 @enderror">
                    <option value="">Selecione a turma...</option>
                    @foreach($availableClasses as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} ({{ $class->grade_level_name }})
                        </option>
                    @endforeach
                </select>
                @error('class_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-5">
                <label for="teacher_id" class="block text-xs font-bold text-slate-700 mb-1">Professor *</label>
                <select name="teacher_id" id="teacher_id" required
                        class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('teacher_id') border-rose-500 @enderror">
                    <option value="">Selecione o professor...</option>
                    @foreach($availableTeachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Associar
                </button>
            </div>
        </form>
    </div>
    @endcan

    <!-- Associated Classes Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Turmas Associadas ({{ $subject->classSubjects->count() }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Turma</th>
                        <th class="py-2.5 px-3">Nível</th>
                        <th class="py-2.5 px-3">Professor</th>
                        <th class="py-2.5 px-3">Ano Letivo</th>
                        <th class="py-2.5 px-3">Alunos</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subject->classSubjects as $classSubject)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                <span>{{ $classSubject->class->name }}</span>
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $classSubject->class->classroom ?? 'Sala não definida' }}</span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $classSubject->class->grade_level_name }}</span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                @if($classSubject->teacher)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-emerald-700 text-white font-bold text-[9px] flex items-center justify-center shrink-0">
                                            {{ substr($classSubject->teacher->first_name, 0, 1) }}{{ substr($classSubject->teacher->last_name, 0, 1) }}
                                        </div>
                                        <span>{{ $classSubject->teacher->first_name }} {{ $classSubject->teacher->last_name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">Não atribuído</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $classSubject->class->school_year }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">{{ $classSubject->class->students_count }}</span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $classSubject->class->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $classSubject->class->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('classes.show', $classSubject->class->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Turma">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @can('manage_subjects')
                                    <form action="{{ route('subjects.remove-from-class', [$subject->id, $classSubject->class->id]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="h-7 w-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Remover Associação" data-confirm="Tem certeza que deseja remover esta disciplina da turma?">
                                            <i class="fas fa-unlink text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">
                                <i class="fas fa-chalkboard text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Esta disciplina não está associada a nenhuma turma.</p>
                                @can('manage_subjects')
                                <p class="text-[10px] text-slate-400">Use o formulário acima para associar a disciplina a uma turma.</p>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Class Performance Cards -->
    @if($subject->classSubjects->count() > 0)
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="fas fa-chart-bar text-emerald-600"></i> Desempenho por Turma
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach($subject->classSubjects as $classSubject)
                @php
                    $classGrades = $subject->grades->where('class_id', $classSubject->class_id);
                    $average = $classGrades->avg('grade') ?? 0;
                    $count = $classGrades->count();
                @endphp
                <div class="p-3 rounded-lg border border-slate-200/80 bg-slate-50/50 space-y-2">
                    <h4 class="text-xs font-bold text-slate-800">{{ $classSubject->class->name }}</h4>
                    <div class="flex justify-between items-center text-xs">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $average >= 10 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            Média: {{ number_format($average, 1) }}
                        </span>
                        <span class="text-[10px] text-slate-400">{{ $count }} notas</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $average >= 10 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: {{ min($average * 5, 100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400">Professor: {{ $classSubject->teacher_name }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
