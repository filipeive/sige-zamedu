@extends('layouts.app')

@section('title', 'Editar Professor')
@section('page-title', 'Editar Professor')
@section('page-title-icon', 'fas fa-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Professores</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teachers.show', $teacher->id) }}">Detalhes</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Editar</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-edit text-amber-600"></i> Editar Professor
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atualize os dados de {{ $teacher->first_name }} {{ $teacher->last_name }}.</p>
        </div>
        <a href="{{ route('teachers.show', $teacher->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-amber-500 shadow-2xs p-5">
        <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Primeiro Nome *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('first_name') border-rose-500 @enderror">
                    @error('first_name')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Último Nome *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('last_name') border-rose-500 @enderror">
                    @error('last_name')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $teacher->email) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('email') border-rose-500 @enderror">
                    @error('email')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Telefone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('phone') border-rose-500 @enderror">
                    @error('phone')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Número do BI *</label>
                    <input type="text" name="bi_number" value="{{ old('bi_number', $teacher->bi_number) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('bi_number') border-rose-500 @enderror">
                    @error('bi_number')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Gênero *</label>
                    <select name="gender" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('gender') border-rose-500 @enderror">
                        <option value="">Selecione</option>
                        <option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Masculino</option>
                        <option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>Feminino</option>
                    </select>
                    @error('gender')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data de Nascimento *</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : '') }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('birth_date') border-rose-500 @enderror">
                    @error('birth_date')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data de Contratação *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $teacher->hire_date ? $teacher->hire_date->format('Y-m-d') : '') }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('hire_date') border-rose-500 @enderror">
                    @error('hire_date')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Qualificação *</label>
                    <input type="text" name="qualification" value="{{ old('qualification', $teacher->qualification) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('qualification') border-rose-500 @enderror">
                    @error('qualification')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Especialização *</label>
                    <input type="text" name="specialization" value="{{ old('specialization', $teacher->specialization) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('specialization') border-rose-500 @enderror">
                    @error('specialization')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Salário (MZN) *</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary', $teacher->salary) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('salary') border-rose-500 @enderror">
                    @error('salary')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                    <select name="status" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('status') border-rose-500 @enderror">
                        <option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Endereço *</label>
                <textarea name="address" rows="3" required
                          class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('address') border-rose-500 @enderror">{{ old('address', $teacher->address) }}</textarea>
                @error('address')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <!-- Audit Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                <p class="text-[10px] text-slate-400"><i class="fas fa-calendar-plus mr-1"></i> Criado em: {{ $teacher->created_at->format('d/m/Y H:i') }}</p>
                <p class="text-[10px] text-slate-400 sm:text-right"><i class="fas fa-calendar-check mr-1"></i> Última atualização: {{ $teacher->updated_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('teachers.show', $teacher->id) }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Atualizar Professor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
