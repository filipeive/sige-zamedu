@extends('layouts.app')

@section('title', 'Cadastrar Nova Nota')
@section('page-title', 'Cadastrar Nova Nota')
@section('page-title-icon', 'fas fa-plus-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('grades.index') }}">Notas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Nova Nota</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-plus-circle text-emerald-600"></i> Cadastrar Nova Nota
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Registe uma nova avaliação individual de um estudante.</p>
        </div>
        <a href="{{ route('grades.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600 shadow-2xs p-5 max-w-4xl">
        <form method="POST" action="{{ route('grades.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="student_id" class="block text-xs font-bold text-slate-700 mb-1">Aluno *</label>
                    <select name="student_id" id="student_id" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('student_id') border-rose-500 @enderror">
                        <option value="">Selecione o aluno</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }} - {{ $student->student_number }}
                                @if($student->currentEnrollment)
                                    ({{ $student->currentEnrollment->class->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="subject_id" class="block text-xs font-bold text-slate-700 mb-1">Disciplina *</label>
                    <select name="subject_id" id="subject_id" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('subject_id') border-rose-500 @enderror">
                        <option value="">Selecione a disciplina</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <label for="grade" class="block text-xs font-bold text-slate-700 mb-1">Nota *</label>
                    <input type="number" name="grade" id="grade" min="0" max="20" step="0.1" required placeholder="0.0 - 20.0"
                           value="{{ old('grade') }}"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('grade') border-rose-500 @enderror">
                    @error('grade')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="assessment_type" class="block text-xs font-bold text-slate-700 mb-1">Tipo Avaliação *</label>
                    <select name="assessment_type" id="assessment_type" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('assessment_type') border-rose-500 @enderror">
                        <option value="">Selecione o tipo</option>
                        <option value="ACS1" {{ old('assessment_type') == 'ACS1' ? 'selected' : '' }}>ACS 1</option>
                        <option value="ACS2" {{ old('assessment_type') == 'ACS2' ? 'selected' : '' }}>ACS 2</option>
                        <option value="ACS3" {{ old('assessment_type') == 'ACS3' ? 'selected' : '' }}>ACS 3</option>
                        <option value="ACP" {{ old('assessment_type') == 'ACP' ? 'selected' : '' }}>ACP</option>
                        <option value="ACF" {{ old('assessment_type') == 'ACF' ? 'selected' : '' }}>ACF</option>
                        <option value="behavioral" {{ old('assessment_type') == 'behavioral' ? 'selected' : '' }}>Comportamento</option>
                        <option value="test" {{ old('assessment_type') == 'test' ? 'selected' : '' }}>Teste</option>
                        <option value="exam" {{ old('assessment_type') == 'exam' ? 'selected' : '' }}>Exame Final</option>
                    </select>
                    @error('assessment_type')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="term" class="block text-xs font-bold text-slate-700 mb-1">Trimestre *</label>
                    <select name="term" id="term" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('term') border-rose-500 @enderror">
                        <option value="">Selecione</option>
                        <option value="1" {{ old('term') == '1' ? 'selected' : '' }}>1º Trimestre</option>
                        <option value="2" {{ old('term') == '2' ? 'selected' : '' }}>2º Trimestre</option>
                        <option value="3" {{ old('term') == '3' ? 'selected' : '' }}>3º Trimestre</option>
                    </select>
                    @error('term')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="year" class="block text-xs font-bold text-slate-700 mb-1">Ano *</label>
                    <input type="number" name="year" id="year" value="{{ old('year', $currentYear) }}" min="2020" max="2030" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('year') border-rose-500 @enderror">
                    @error('year')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="date_recorded" class="block text-xs font-bold text-slate-700 mb-1">Data da Avaliação *</label>
                    <input type="date" name="date_recorded" id="date_recorded" value="{{ old('date_recorded', date('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('date_recorded') border-rose-500 @enderror">
                    @error('date_recorded')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="comments" class="block text-xs font-bold text-slate-700 mb-1">Observações</label>
                    <textarea name="comments" id="comments" rows="2" placeholder="Observações sobre a nota..."
                              class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('comments') border-rose-500 @enderror">{{ old('comments') }}</textarea>
                    @error('comments')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('grades.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Salvar Nota
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gradeInput = document.getElementById('grade');
    if (gradeInput) {
        gradeInput.addEventListener('input', function () {
            const value = parseFloat(this.value);
            if (value < 0) this.value = 0;
            if (value > 20) this.value = 20;
        });
    }
});
</script>
@endpush
