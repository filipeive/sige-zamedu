@extends('layouts.app')

@section('title', 'Caderno de Notas - ' . $class->name)
@section('page-title', 'Caderno de Notas')
@section('page-title-icon', 'fas fa-tasks')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}" class="text-slate-400 hover:text-slate-600">Minhas Turmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.detail', $class->id) }}" class="text-slate-400 hover:text-slate-600">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Notas</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        {{ $class->education_level_name ?? 'Ensino Geral' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-slate-200">
                        {{ $class->grade_level_name ?? ($class->grade_level . 'ª Classe') }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-slate-200">
                        {{ $class->students->count() }} Alunos
                    </span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Caderno de Notas: {{ $class->name }}</h1>
                <p class="text-xs text-slate-300 mt-1">Registo contínuo de avaliações (ACS1, ACS2, ACS3, ACP, ACF) e cálculo de Média Trimestral.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('grades.batch-create', ['class_id' => $class->id]) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-xs transition-all">
                    <i class="fas fa-table"></i>
                    Lançamento em Lote
                </a>

                <div class="inline-flex rounded-xl bg-white/10 p-1 border border-white/10">
                    <a href="{{ route('pautas.trimestral', ['class' => $class->id, 'term' => $selectedTerm]) }}" 
                       class="px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fas fa-file-invoice mr-1"></i> Pauta Trim.
                    </a>
                    <a href="{{ route('pautas.anual', ['class' => $class->id]) }}" 
                       class="px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fas fa-calendar-alt mr-1"></i> Pauta Anual
                    </a>
                    <a href="{{ route('pautas.final', ['class' => $class->id]) }}" 
                       class="px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fas fa-graduation-cap mr-1"></i> Pauta Final
                    </a>
                </div>

                <a href="{{ route('teacher.classes.detail', $class->id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5">
        <div class="flex items-center gap-2 mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">
            <i class="fas fa-filter text-emerald-600"></i>
            Filtros do Caderno de Avaliação
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Disciplina Leccionada <span class="text-rose-500">*</span>
                </label>
                <select name="subject_id" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" onchange="updateSubject(this.value)">
                    <option value="">— Seleccionar Disciplina —</option>
                    @forelse($teacherSubjects as $subject)
                        <option value="{{ $subject->id }}" {{ (string)$selectedSubject === (string)$subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @empty
                        <option value="" disabled>Não lecciona nenhuma disciplina nesta turma.</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Trimestre de Avaliação <span class="text-rose-500">*</span>
                </label>
                <select name="term" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" onchange="updateTerm(this.value)">
                    <option value="1" {{ $selectedTerm == 1 ? 'selected' : '' }}>1º Trimestre</option>
                    <option value="2" {{ $selectedTerm == 2 ? 'selected' : '' }}>2º Trimestre</option>
                    <option value="3" {{ $selectedTerm == 3 ? 'selected' : '' }}>3º Trimestre</option>
                </select>
            </div>
        </div>
    </div>

    @if($selectedSubject)
        <!-- Gradebook Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-book text-xs"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">
                            {{ $teacherSubjects->find($selectedSubject)?->name ?? 'Disciplina' }}
                        </h2>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">{{ $selectedTerm }}º Trimestre</span>
                    </div>
                </div>

                <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-2xs transition-colors" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                    <i class="fas fa-plus"></i>
                    Nova Nota Individual
                </button>
            </div>

            @if($class->students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4">Aluno</th>
                                <th class="py-3 px-3 text-center">ACS 1</th>
                                <th class="py-3 px-3 text-center">ACS 2</th>
                                <th class="py-3 px-3 text-center">ACS 3</th>
                                <th class="py-3 px-3 text-center">ACP</th>
                                <th class="py-3 px-3 text-center">ACF / Exame</th>
                                <th class="py-3 px-3 text-center">MT</th>
                                <th class="py-3 px-3 text-center">Estado</th>
                                <th class="py-3 px-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($class->students as $student)
                                @php
                                    $studentGrades = $grades[$student->id] ?? [];
                                    $get = function($type) use ($studentGrades) {
                                        return isset($studentGrades[$type]) && $studentGrades[$type]->isNotEmpty()
                                            ? round($studentGrades[$type]->first()->grade, 1)
                                            : null;
                                    };
                                    $acs1 = $get('ACS1');
                                    $acs2 = $get('ACS2');
                                    $acs3 = $get('ACS3');
                                    $acp  = $get('ACP');
                                    $acf  = $get('ACF');
                                    
                                    // Média Trimestral MINEDH: média das ACS (até 3) com peso 40% + ACP 20% + ACF 40%
                                    $mt = null;
                                    $acsValues = array_filter([$acs1, $acs2, $acs3], fn($v) => $v !== null);
                                    $acsAvg = count($acsValues) > 0 ? array_sum($acsValues) / count($acsValues) : null;
                                    if ($acsAvg !== null && $acp !== null && $acf !== null) {
                                        $mt = round($acsAvg * 0.4 + $acp * 0.2 + $acf * 0.4, 1);
                                    } elseif ($acsAvg !== null && $acf !== null) {
                                        $mt = round($acsAvg * 0.5 + $acf * 0.5, 1);
                                    } elseif ($acsAvg !== null) {
                                        $mt = round($acsAvg, 1);
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/75 transition-colors">
                                    <td class="py-2.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px] shrink-0">
                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $student->student_number ?? 'S/N' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- ACS1 --}}
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" onclick="editGrade({{ $student->id }}, 'ACS1', {{ $acs1 ?? 'null' }})"
                                                class="w-10 h-7 rounded-lg font-mono text-xs font-bold transition-all {{ $acs1 !== null ? 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' : 'bg-slate-50 text-slate-400 border border-dashed border-slate-200 hover:bg-slate-100' }}">
                                            {{ $acs1 ?? '-' }}
                                        </button>
                                    </td>

                                    {{-- ACS2 --}}
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" onclick="editGrade({{ $student->id }}, 'ACS2', {{ $acs2 ?? 'null' }})"
                                                class="w-10 h-7 rounded-lg font-mono text-xs font-bold transition-all {{ $acs2 !== null ? 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' : 'bg-slate-50 text-slate-400 border border-dashed border-slate-200 hover:bg-slate-100' }}">
                                            {{ $acs2 ?? '-' }}
                                        </button>
                                    </td>

                                    {{-- ACS3 --}}
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" onclick="editGrade({{ $student->id }}, 'ACS3', {{ $acs3 ?? 'null' }})"
                                                class="w-10 h-7 rounded-lg font-mono text-xs font-bold transition-all {{ $acs3 !== null ? 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' : 'bg-slate-50 text-slate-400 border border-dashed border-slate-200 hover:bg-slate-100' }}">
                                            {{ $acs3 ?? '-' }}
                                        </button>
                                    </td>

                                    {{-- ACP --}}
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" onclick="editGrade({{ $student->id }}, 'ACP', {{ $acp ?? 'null' }})"
                                                class="w-10 h-7 rounded-lg font-mono text-xs font-bold transition-all {{ $acp !== null ? 'bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100' : 'bg-slate-50 text-slate-400 border border-dashed border-slate-200 hover:bg-slate-100' }}">
                                            {{ $acp ?? '-' }}
                                        </button>
                                    </td>

                                    {{-- ACF --}}
                                    <td class="py-2.5 px-3 text-center">
                                        <button type="button" onclick="editGrade({{ $student->id }}, 'ACF', {{ $acf ?? 'null' }})"
                                                class="w-10 h-7 rounded-lg font-mono text-xs font-bold transition-all {{ $acf !== null ? 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' : 'bg-slate-50 text-slate-400 border border-dashed border-slate-200 hover:bg-slate-100' }}">
                                            {{ $acf ?? '-' }}
                                        </button>
                                    </td>

                                    {{-- MT --}}
                                    <td class="py-2.5 px-3 text-center">
                                        @if($mt !== null)
                                            <span class="average-badge font-mono font-bold text-xs px-2.5 py-1 rounded-full {{ $mt >= 10 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($mt >= 7 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                                {{ $mt }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 font-mono">-</span>
                                        @endif
                                    </td>

                                    {{-- Estado --}}
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        @if($mt === null)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                Pendente
                                            </span>
                                        @elseif($mt >= 10)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Aprovado
                                            </span>
                                        @elseif($mt >= 7)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Recuperação
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Reprovado
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Ações --}}
                                    <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                        <button type="button" class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 inline-flex items-center justify-center transition-colors"
                                                onclick="editGrade({{ $student->id }}, 'ACS1', {{ $acs1 ?? 'null' }})" title="Lançar Nota">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-user-graduate text-xl"></i>
                    </div>
                    <p class="text-xs font-medium text-slate-600">Nenhum aluno matriculado nesta turma.</p>
                </div>
            @endif
        </div>

        <!-- Grade Statistics KPI Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-blue-500">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Notas Lançadas</div>
                <div class="text-2xl font-bold font-mono text-slate-800 mt-1" id="total-grades">0</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-emerald-500">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aprovados (&ge; 10)</div>
                <div class="text-2xl font-bold font-mono text-emerald-600 mt-1" id="approved-count">0</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-amber-500">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Recuperação (8 - 9.9)</div>
                <div class="text-2xl font-bold font-mono text-amber-600 mt-1" id="recovery-count">0</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-2xs border-l-4 border-l-rose-500">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Reprovados (&lt; 8)</div>
                <div class="text-2xl font-bold font-mono text-rose-600 mt-1" id="failed-count">0</div>
            </div>
        </div>

    @else
        <!-- No Subject Selected State -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-book-open text-2xl"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Selecione uma Disciplina</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                Escolha a disciplina leccionada no filtro acima para carregar o caderno de notas desta turma.
            </p>
        </div>
    @endif

</div>

<!-- Modal para Adicionar/Editar Nota -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden">
            <div class="bg-slate-900 text-white p-5 flex items-center justify-between">
                <h5 class="text-sm font-bold text-white flex items-center gap-2" id="gradeModalTitle">
                    <i class="fas fa-pen-nib text-emerald-400"></i>
                    Lançar Nova Nota
                </h5>
                <button type="button" class="text-slate-400 hover:text-white" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('teacher.grades.store') }}" method="POST" id="grade-form">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="subject_id" value="{{ $selectedSubject }}">
                <input type="hidden" name="term" value="{{ $selectedTerm }}">
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Aluno <span class="text-rose-500">*</span></label>
                        <select name="student_id" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" required id="student-select">
                            <option value="">Selecione o aluno</option>
                            @foreach($class->students as $student)
                                <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tipo de Avaliação (MINEDH) <span class="text-rose-500">*</span></label>
                        <select name="assessment_type" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" required id="assessment-type">
                            <option value="ACS1">ACS 1 — Avaliação Contínua 1</option>
                            <option value="ACS2">ACS 2 — Avaliação Contínua 2</option>
                            <option value="ACS3">ACS 3 — Avaliação Contínua 3</option>
                            <option value="ACP">ACP — Avaliação de Controlo Parcial</option>
                            <option value="ACF">ACF / Exame — Avaliação Contínua Final</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nota (Escala Oficial 0.0 a 20.0) <span class="text-rose-500">*</span></label>
                        <input type="number" name="grade" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800 font-mono font-bold" min="0" max="20" step="0.1" required id="grade-input" placeholder="Ex: 14.5">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Comentários Pedagógicos (Opcional)</label>
                        <textarea name="comments" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" rows="3" id="grade-comments" placeholder="Observações sobre o desempenho do aluno..."></textarea>
                    </div>
                </div>

                <div class="bg-slate-50 py-3.5 px-6 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white shadow-xs transition">
                        <i class="fas fa-save mr-1"></i> Salvar Nota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateSubject(subjectId) {
    const url = new URL(window.location.href);
    url.searchParams.set('subject_id', subjectId);
    window.location.href = url.toString();
}

function updateTerm(term) {
    const url = new URL(window.location.href);
    url.searchParams.set('term', term);
    window.location.href = url.toString();
}

function editGrade(studentId, assessmentType, currentGrade) {
    document.getElementById('student-select').value = studentId;
    document.getElementById('assessment-type').value = assessmentType;
    
    const gradeValue = (currentGrade && currentGrade !== 'null') ? currentGrade : '';
    document.getElementById('grade-input').value = gradeValue;
    
    document.getElementById('grade-comments').value = '';
    document.getElementById('gradeModalTitle').innerHTML = '<i class="fas fa-pen-nib text-emerald-400"></i> ' + (gradeValue ? 'Editar Nota' : 'Lançar Nova Nota');
    
    const modalEl = document.getElementById('addGradeModal');
    if (window.bootstrap && window.bootstrap.Modal) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    function calculateStatistics() {
        let totalGrades = 0;
        let approved = 0;
        let recovery = 0;
        let failed = 0;

        document.querySelectorAll('.average-badge').forEach(badge => {
            const grade = parseFloat(badge.textContent.trim());
            if (!isNaN(grade)) {
                totalGrades++;
                if (grade >= 10) approved++;
                else if (grade >= 8) recovery++;
                else failed++;
            }
        });

        const totalEl = document.getElementById('total-grades');
        const approvedEl = document.getElementById('approved-count');
        const recoveryEl = document.getElementById('recovery-count');
        const failedEl = document.getElementById('failed-count');

        if (totalEl) totalEl.textContent = totalGrades;
        if (approvedEl) approvedEl.textContent = approved;
        if (recoveryEl) recoveryEl.textContent = recovery;
        if (failedEl) failedEl.textContent = failed;
    }

    calculateStatistics();
});
</script>
@endsection
