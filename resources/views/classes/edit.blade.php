@extends('layouts.app')

@section('title', 'Editar Turma - ' . $class->name)
@section('page-title', 'Editar Turma')
@section('page-title-icon', 'fas fa-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Turmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('classes.show', $class->id) }}">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-edit text-amber-600"></i> Editar Turma: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atualize os dados da turma, disciplinas e professor responsável.</p>
        </div>
        <a href="{{ route('classes.show', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-amber-500 shadow-2xs p-5">
        <form action="{{ route('classes.update', $class->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nome da Turma *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $class->name) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('name') border-rose-500 @enderror">
                    @error('name')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nível de Ensino * <span class="text-emerald-600">(SNE — Lei 18/2018)</span></label>
                    <select name="grade_level" id="grade_level" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('grade_level') border-rose-500 @enderror">
                        <option value="">Selecione o nível...</option>
                        @php use App\Helpers\EducationLevel; @endphp
                        @if(auth()->user()?->school?->type !== 'public')
                        <optgroup label="🏫 Pré-Escolar (Privado / Comunitário)">
                            <option value="0" {{ old('grade_level', $class->grade_level) == '0' ? 'selected' : '' }}>Pré-Escolar / Pré-Infantil</option>
                        </optgroup>
                        @endif
                        <optgroup label="📘 Ensino Primário — 1.º Ciclo (1ª – 3ª Classe)">
                            <option value="1" {{ old('grade_level', $class->grade_level) == '1' ? 'selected' : '' }}>1ª Classe</option>
                            <option value="2" {{ old('grade_level', $class->grade_level) == '2' ? 'selected' : '' }}>2ª Classe</option>
                            <option value="3" {{ old('grade_level', $class->grade_level) == '3' ? 'selected' : '' }}>3ª Classe</option>
                        </optgroup>
                        <optgroup label="📗 Ensino Primário — 2.º Ciclo (4ª – 6ª Classe)">
                            <option value="4" {{ old('grade_level', $class->grade_level) == '4' ? 'selected' : '' }}>4ª Classe</option>
                            <option value="5" {{ old('grade_level', $class->grade_level) == '5' ? 'selected' : '' }}>5ª Classe</option>
                            <option value="6" {{ old('grade_level', $class->grade_level) == '6' ? 'selected' : '' }}>6ª Classe</option>
                        </optgroup>
                        <optgroup label="📙 Ensino Secundário — ESG1, 1.º Ciclo (7ª – 9ª)">
                            <option value="7" {{ old('grade_level', $class->grade_level) == '7' ? 'selected' : '' }}>7ª Classe</option>
                            <option value="8" {{ old('grade_level', $class->grade_level) == '8' ? 'selected' : '' }}>8ª Classe</option>
                            <option value="9" {{ old('grade_level', $class->grade_level) == '9' ? 'selected' : '' }}>9ª Classe ⭐ Exame Nacional</option>
                        </optgroup>
                        <optgroup label="📕 Ensino Secundário — ESG2, 2.º Ciclo (10ª – 12ª)">
                            <option value="10" {{ old('grade_level', $class->grade_level) == '10' ? 'selected' : '' }}>10ª Classe ⭐ Exame Nacional</option>
                            <option value="11" {{ old('grade_level', $class->grade_level) == '11' ? 'selected' : '' }}>11ª Classe</option>
                            <option value="12" {{ old('grade_level', $class->grade_level) == '12' ? 'selected' : '' }}>12ª Classe ⭐ Exame Nacional</option>
                        </optgroup>
                    </select>
                    @error('grade_level')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Turno *</label>
                    <select name="shift" id="shift" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('shift') border-rose-500 @enderror">
                        <option value="">Selecione o turno...</option>
                        <option value="morning" {{ old('shift', $class->shift) == 'morning' ? 'selected' : '' }}>Manhã</option>
                        <option value="afternoon" {{ old('shift', $class->shift) == 'afternoon' ? 'selected' : '' }}>Tarde</option>
                        <option value="night" {{ old('shift', $class->shift) == 'night' ? 'selected' : '' }}>Noite</option>
                    </select>
                    @error('shift')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Professor Regente / Responsável</label>
                    <select name="teacher_id" id="teacher_id"
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('teacher_id') border-rose-500 @enderror">
                        <option value="">-- Sem Professor Responsável (Remover) --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-[10px] text-slate-400">Para turmas do Pré-Escolar/Primário: professor regente titular.</p>
                    @error('teacher_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Professor Assistente <span class="text-slate-400 font-normal">(Primário — opcional)</span></label>
                    <select name="assistant_teacher_id" id="assistant_teacher_id"
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('assistant_teacher_id') border-rose-500 @enderror">
                        <option value="">— Sem assistente —</option>
                        @foreach($teachers as $teacher)
                            @if($teacher->id != $class->teacher_id)
                                <option value="{{ $teacher->id }}" {{ old('assistant_teacher_id', $class->assistant_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->first_name }} {{ $teacher->last_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('assistant_teacher_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Capacidade Máxima *</label>
                    <input type="number" name="max_students" id="max_students" value="{{ old('max_students', $class->max_students) }}" min="1" max="50" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('max_students') border-rose-500 @enderror">
                    @error('max_students')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Sala de Aula</label>
                    <input type="text" name="classroom" id="classroom" value="{{ old('classroom', $class->classroom) }}"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('classroom') border-rose-500 @enderror">
                    @error('classroom')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ano Letivo *</label>
                    <input type="number" name="school_year" id="school_year" value="{{ old('school_year', $class->school_year) }}" min="2020" max="2030" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('school_year') border-rose-500 @enderror">
                    @error('school_year')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Subjects -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Disciplinas</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($subjects as $subject)
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                        <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" id="subject_{{ $subject->id }}"
                               {{ in_array($subject->id, old('subjects', $currentSubjectIds)) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        {{ $subject->name }} ({{ $subject->code }})
                    </label>
                    @endforeach
                </div>
                @error('subjects')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <!-- Active Toggle -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $class->is_active) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                <label for="is_active" class="text-xs font-bold text-slate-700">Turma Ativa</label>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('classes.show', $class->id) }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Atualizar Turma
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
