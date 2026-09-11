@extends('layouts.app')

@section('title', 'Minhas Turmas')
@section('page-title', 'Minhas Turmas')
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="hover:text-emerald-600 transition">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Minhas Turmas</li>
@endsection

@section('content')
<div class="space-y-4">
    @if($classes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($classes as $class)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden flex flex-col justify-between">
                    <div class="px-5 py-3 bg-slate-900 text-white font-bold text-xs flex items-center gap-2">
                        <i class="fas fa-chalkboard text-emerald-400"></i>
                        <span>{{ $class->name }}</span>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-600 to-emerald-800 text-white flex items-center justify-center mx-auto mb-3 shadow-md">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-800">{{ $class->name }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ $class->grade_level_name }}</p>
                        </div>

                        <div class="space-y-2 text-xs border-t border-b border-slate-100 py-3">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Ano Letivo:</span>
                                <strong class="text-slate-800 font-extrabold">{{ $class->school_year }}</strong>
                            </div>
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Alunos:</span>
                                <strong class="text-slate-800 font-extrabold">{{ $class->students_count }}/{{ $class->max_students }}</strong>
                            </div>
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Sala:</span>
                                <strong class="text-slate-800 font-extrabold">{{ $class->classroom ?? 'N/A' }}</strong>
                            </div>

                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-2">
                                <div class="h-full rounded-full {{ $class->capacity_percentage > 80 ? 'bg-rose-500' : ($class->capacity_percentage > 60 ? 'bg-amber-500' : 'bg-emerald-500') }}" 
                                     style="width: {{ $class->capacity_percentage }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2 pt-1">
                            <a href="{{ route('teacher.classes.students', $class->id) }}" 
                               class="no-underline inline-flex justify-center items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
                                <i class="fas fa-user-graduate"></i> Ver Alunos
                            </a>
                            <a href="{{ route('teacher.gradebook', $class->id) }}" 
                               class="no-underline inline-flex justify-center items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                                <i class="fas fa-tasks"></i> Caderno de Notas
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-12 text-center space-y-3">
            <i class="fas fa-chalkboard-teacher text-4xl text-slate-300"></i>
            <h3 class="text-sm font-extrabold text-slate-700">Nenhuma Turma Atribuída</h3>
            <p class="text-xs text-slate-500">Você não tem turmas atribuídas no momento.</p>
        </div>
    @endif
</div>
@endsection