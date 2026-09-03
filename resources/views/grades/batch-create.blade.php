@extends('layouts.app')

@section('title', 'Lançamento de Notas em Lote')
@section('page-title', 'Lançamento em Lote')
@section('page-title-icon', 'fas fa-layer-group')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('grades.dashboard') }}">Gestão de Notas</a></li>
    <li class="breadcrumb-item active">Notas em Lote</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-layer-group text-emerald-600"></i> Lançamento de Notas em Lote
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Lançamento rápido de pautas de avaliação para turmas inteiras.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('grades.dashboard') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="{{ route('grades.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-table"></i> Pauta
            </a>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
        <form action="{{ route('grades.batch-create') }}" method="GET" id="batchFilterForm" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Turma *</label>
                <select name="class_id" required onchange="this.form.submit()"
                        class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Selecione a Turma...</option>
                    @foreach($classes as $c)
                        <option value="{{ optional($c)->id }}" {{ $classId == optional($c)->id ? 'selected' : '' }}>
                            {{ optional($c)->name }} ({{ optional($c)->grade_level_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Disciplina *</label>
                <select name="subject_id" required onchange="this.form.submit()"
                        class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Selecione a Disciplina...</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub->id }}" {{ $subjectId == $sub->id ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Trimestre</label>
                <select name="term" onchange="this.form.submit()"
                        class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="1" {{ $term == 1 ? 'selected' : '' }}>1º Trimestre</option>
                    <option value="2" {{ $term == 2 ? 'selected' : '' }}>2º Trimestre</option>
                    <option value="3" {{ $term == 3 ? 'selected' : '' }}>3º Trimestre</option>
                </select>
            </div>

            <div class="sm:col-span-4">
                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Tipo de Avaliação</label>
                <select name="assessment_type" onchange="this.form.submit()"
                        class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="ACS1" {{ $assessmentType == 'ACS1' ? 'selected' : '' }}>ACS 1 (Contínua 1)</option>
                    <option value="ACS2" {{ $assessmentType == 'ACS2' ? 'selected' : '' }}>ACS 2 (Contínua 2)</option>
                    <option value="ACS3" {{ $assessmentType == 'ACS3' ? 'selected' : '' }}>ACS 3 (Contínua 3)</option>
                    <option value="ACP" {{ $assessmentType == 'ACP' ? 'selected' : '' }}>ACP (Parcial)</option>
                    <option value="ACF" {{ $assessmentType == 'ACF' ? 'selected' : '' }}>ACF / Exame</option>
                </select>
                <input type="hidden" name="year" value="{{ $year }}">
            </div>
        </form>
    </div>

    @if($classId && $subjectId && count($students) > 0)
        <!-- Batch Form -->
        <form method="POST" action="{{ route('grades.batch-store') }}" id="batchGradesForm" class="space-y-4">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="subject_id" value="{{ $subjectId }}">
            <input type="hidden" name="term" value="{{ $term }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="assessment_type" value="{{ $assessmentType }}">

            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-edit text-amber-500"></i> Lançamento de Notas: {{ $assessmentType }}
                        </h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Digite as notas de 0 a 20 valores. Pressione Enter para navegar para o próximo aluno.</p>
                    </div>

                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                        <i class="fas fa-save"></i> Guardar Pauta de Notas
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                <th class="py-2.5 px-4 w-12 text-center">#</th>
                                <th class="py-2.5 px-3">Aluno</th>
                                <th class="py-2.5 px-3 text-center w-32">Nota Gravada</th>
                                <th class="py-2.5 px-3 text-center w-44">Nova Nota (0 - 20)</th>
                                <th class="py-2.5 px-4">Observações / Comentários</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($students as $index => $student)
                                @php $existingGrade = $existingGrades->get($student->id); @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-2.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-800">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span>{{ $student->first_name }} {{ $student->last_name }}</span>
                                                <span class="block text-[10px] text-slate-400 font-mono font-normal">N.º {{ $student->student_number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @if($existingGrade)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $existingGrade->grade >= 10 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                                {{ number_format($existingGrade->grade, 1) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[10px]">Pendente</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <input type="hidden" name="grades[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                        <input type="number" name="grades[{{ $student->id }}][grade]" min="0" max="20" step="0.1"
                                               value="{{ old('grades.' . $student->id . '.grade', isset($existingGrade->grade) ? number_format($existingGrade->grade, 1, '.', '') : '') }}"
                                               placeholder="0 - 20"
                                               class="w-full text-center rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-2 text-sm font-black focus:border-emerald-500 focus:ring-emerald-500 grade-input">
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <input type="text" name="grades[{{ $student->id }}][comments]"
                                               value="{{ old('grades.' . $student->id . '.comments', $existingGrade->comments ?? '') }}"
                                               placeholder="Observação individual..."
                                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1 px-2 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">Total: <strong>{{ count($students) }}</strong> alunos nesta turma</span>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                        <i class="fas fa-save"></i> Guardar Pauta de Notas
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-8 text-center text-slate-400">
            <i class="fas fa-layer-group text-3xl text-slate-300 mb-2"></i>
            <h4 class="text-sm font-bold text-slate-700">Selecione a Turma e a Disciplina</h4>
            <p class="text-xs text-slate-500 mt-1">Escolha uma turma e disciplina no painel acima para iniciar o lançamento de notas.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.grade-input');
    inputs.forEach((input, idx) => {
        input.addEventListener('input', function() {
            const val = parseFloat(this.value);
            if (!isNaN(val)) {
                if (val >= 10) {
                    this.classList.add('border-emerald-500', 'text-emerald-700');
                    this.classList.remove('border-rose-500', 'text-rose-600');
                } else {
                    this.classList.add('border-rose-500', 'text-rose-600');
                    this.classList.remove('border-emerald-500', 'text-emerald-700');
                }
            } else {
                this.classList.remove('border-emerald-500', 'text-emerald-700', 'border-rose-500', 'text-rose-600');
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (inputs[idx + 1]) {
                    inputs[idx + 1].focus();
                    inputs[idx + 1].select();
                }
            }
        });
    });
});
</script>
@endpush
