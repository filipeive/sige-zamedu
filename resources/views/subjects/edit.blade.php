@extends('layouts.app')

@section('title', 'Editar Disciplina - ' . $subject->name)
@section('page-title', 'Editar Disciplina')
@section('page-title-icon', 'fas fa-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}" class="no-underline">Disciplinas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.show', $subject) }}" class="no-underline">{{ $subject->name }}</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-edit text-emerald-600"></i> Editar Disciplina: {{ $subject->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atualize as informações da disciplina curricular.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('subjects.show', $subject) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-eye"></i> Ver Detalhes
            </a>
            <a href="{{ route('subjects.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar à Lista
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600 shadow-2xs p-5">
        <form action="{{ route('subjects.update', $subject) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nome da Disciplina *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-rose-500 @enderror">
                    @error('name')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="code" class="block text-xs font-bold text-slate-700 mb-1">Código *</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $subject->code) }}" placeholder="Ex: MAT, LP, CN" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('code') border-rose-500 @enderror">
                    <p class="mt-1 text-[10px] text-slate-400">Código único para identificar a disciplina</p>
                    @error('code')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 mb-1">Descrição</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('description') border-rose-500 @enderror">{{ old('description', $subject->description) }}</textarea>
                @error('description')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="grade_level" class="block text-xs font-bold text-slate-700 mb-1">Nível de Ensino *</label>
                    <select name="grade_level" id="grade_level" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('grade_level') border-rose-500 @enderror">
                        <option value="">Selecione o nível...</option>
                        @foreach($gradeLevels as $key => $level)
                            <option value="{{ $key }}" {{ old('grade_level', $subject->grade_level) == $key ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                    @error('grade_level')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="weekly_hours" class="block text-xs font-bold text-slate-700 mb-1">Horas Semanais *</label>
                    <input type="number" name="weekly_hours" id="weekly_hours" value="{{ old('weekly_hours', $subject->weekly_hours ?? 2) }}" min="1" max="20" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('weekly_hours') border-rose-500 @enderror">
                    @error('weekly_hours')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $subject->is_active ?? true) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span>Disciplina Ativa</span>
                    </label>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('subjects.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Guardar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
