@extends('layouts.app')

@section('title', 'Nova Turma')
@section('page-title', 'Criar Nova Turma')
@section('page-title-icon', 'fas fa-plus-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}" class="no-underline">Turmas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Nova Turma</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard text-emerald-600"></i> Criar Nova Turma
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Defina o nome da turma, nível de ensino, turno e professor titular.</p>
        </div>
        <a href="{{ route('classes.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar às Turmas
        </a>
    </div>

    <!-- Form Container Card with Emerald Accent -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600 shadow-2xs p-5">
        <form action="{{ route('classes.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nome da Turma *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Ex: 8ª Classe A">
                    @error('name')
                        <span class="text-[10px] text-rose-600 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="grade_level" class="block text-xs font-bold text-slate-700 mb-1">Nível de Ensino *</label>
                    <select id="grade_level" name="grade_level" required
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Selecione o nível...</option>
                        @foreach($gradeLevels as $key => $level)
                            <option value="{{ $key }}" {{ old('grade_level') == $key ? 'selected' : '' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                    @error('grade_level')
                        <span class="text-[10px] text-rose-600 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="shift" class="block text-xs font-bold text-slate-700 mb-1">Turno *</label>
                    <select id="shift" name="shift" required
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Manhã</option>
                        <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Tarde</option>
                        <option value="night" {{ old('shift') == 'night' ? 'selected' : '' }}>Noite</option>
                    </select>
                </div>

                <div>
                    <label for="classroom" class="block text-xs font-bold text-slate-700 mb-1">Sala de Aula (Bloco / Sala)</label>
                    <input type="text" id="classroom" name="classroom" value="{{ old('classroom') }}"
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Ex: Sala 04 / Bloco B">
                </div>

                <div>
                    <label for="school_year" class="block text-xs font-bold text-slate-700 mb-1">Ano Lectivo *</label>
                    <input type="number" id="school_year" name="school_year" value="{{ old('school_year', date('Y')) }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" min="2020" max="2030">
                </div>

                <div>
                    <label for="max_students" class="block text-xs font-bold text-slate-700 mb-1">Capacidade Máxima (Alunos) *</label>
                    <input type="number" id="max_students" name="max_students" value="{{ old('max_students', 40) }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" min="1" max="100">
                </div>

                <div class="md:col-span-2">
                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50">
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <b>Modo de ensino:</b> Pré-Escolar &amp; Primário → <span class="font-bold">1 Professor Regente</span> (opcional Assistente). Secundário → professores atribuídos por <span class="font-bold">disciplina</span>.
                        </p>
                    </div>
                </div>

                <div>
                    <label for="teacher_id" class="block text-xs font-bold text-slate-700 mb-1">Professor Regente / Titular</label>
                    <select id="teacher_id" name="teacher_id"
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Não atribuído</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assistant_teacher_id" class="block text-xs font-bold text-slate-700 mb-1">
                        Professor Assistente <span class="text-slate-400 font-normal">(Primário — opcional)</span>
                    </label>
                    <select id="assistant_teacher_id" name="assistant_teacher_id"
                            class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">— Sem assistente —</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('assistant_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('classes.index') }}" class="no-underline px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-check"></i> Criar Turma
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
