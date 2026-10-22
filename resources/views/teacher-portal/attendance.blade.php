@extends('layouts.app')

@section('title', 'Registo de Presenças - ' . $class->name)
@section('page-title', 'Registo de Presenças')
@section('page-title-icon', 'fas fa-clipboard-check')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.index') }}" class="text-slate-400 hover:text-slate-600">Minhas Turmas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.classes.detail', $class->id) }}" class="text-slate-400 hover:text-slate-600">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Presenças</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-1">
                    <i class="fas fa-clipboard-check"></i> Registo de Presenças
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">{{ $class->name }}</h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    {{ $class->grade_level_name ?? ($class->grade_level . 'ª Classe') }} &bull; {{ $class->students_count }} alunos matriculados
                </p>
            </div>

            <!-- Date Selector Form -->
            <form method="GET" class="flex items-center gap-2 bg-white/10 p-1.5 rounded-xl border border-white/10">
                <input type="date" name="date" class="px-3 py-1.5 text-xs rounded-lg bg-white/20 text-white border-0 focus:outline-none focus:ring-2 focus:ring-emerald-400" value="{{ $attendanceDate }}" required>
                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <i class="fas fa-calendar-day"></i>
                    Filtrar Data
                </button>
            </form>
        </div>
    </div>

    <!-- Attendance Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-xs"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">
                        Presenças para {{ \Carbon\Carbon::parse($attendanceDate)->format('d/m/Y') }}
                    </h2>
                    <span class="text-[10px] text-slate-400">Marque o estado de assiduidade de cada aluno</span>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs font-bold">
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px]" id="present-count">0 Presentes</span>
                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[11px]" id="absent-count">0 Ausentes</span>
                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[11px]" id="late-count">0 Atrasados</span>
            </div>
        </div>

        <form action="{{ route('teacher.attendance.store', $class->id) }}" method="POST" id="attendance-form" class="p-0">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">

            @if($class->students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4 w-1/3">Aluno</th>
                                <th class="py-3 px-4 w-1/3 text-center">Situação</th>
                                <th class="py-3 px-4 w-1/3">Observações (opcional)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($class->students as $student)
                                @php
                                    $existing = $existingAttendance[$student->id] ?? null;
                                    $status = $existing ? $existing->status : 'present';
                                @endphp
                                <tr class="hover:bg-slate-50/75 transition-colors attendance-row">
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $student->student_number ?? 'S/N' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                        
                                        <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200">
                                            <label class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all {{ $status === 'present' ? 'bg-emerald-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                                <input type="radio" name="attendances[{{ $student->id }}][status]" value="present" {{ $status === 'present' ? 'checked' : '' }} class="hidden">
                                                <i class="fas fa-check text-[10px]"></i> Presente
                                            </label>

                                            <label class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all {{ $status === 'absent' ? 'bg-rose-600 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                                <input type="radio" name="attendances[{{ $student->id }}][status]" value="absent" {{ $status === 'absent' ? 'checked' : '' }} class="hidden">
                                                <i class="fas fa-times text-[10px]"></i> Ausente
                                            </label>

                                            <label class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all {{ $status === 'late' ? 'bg-amber-500 text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                                                <input type="radio" name="attendances[{{ $student->id }}][status]" value="late" {{ $status === 'late' ? 'checked' : '' }} class="hidden">
                                                <i class="fas fa-clock text-[10px]"></i> Atrasado
                                            </label>
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <input type="text" 
                                               name="attendances[{{ $student->id }}][notes]" 
                                               class="w-full px-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800 placeholder-slate-400" 
                                               placeholder="Ex: Justificou consulta médica..."
                                               value="{{ $existing->notes ?? '' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer Actions -->
                <div class="p-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <a href="{{ route('teacher.classes.detail', $class->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition-colors">
                        <i class="fas fa-arrow-left"></i> Voltar à Turma
                    </a>

                    <div class="flex items-center gap-2">
                        <button type="button" id="mark-all-present" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 text-xs font-bold transition-colors">
                            <i class="fas fa-check-double"></i>
                            Marcar Todos Presentes
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all">
                            <i class="fas fa-save"></i>
                            Salvar Presenças
                        </button>
                    </div>
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-user-graduate text-xl"></i>
                    </div>
                    <p class="text-xs font-medium text-slate-600">Nenhum aluno matriculado nesta turma.</p>
                </div>
            @endif
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs border-l-4 border-l-emerald-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total de Presentes</div>
                <div class="text-3xl font-bold font-mono text-emerald-600 mt-1" id="summary-present">0</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fas fa-user-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs border-l-4 border-l-rose-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total de Ausentes</div>
                <div class="text-3xl font-bold font-mono text-rose-600 mt-1" id="summary-absent">0</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                <i class="fas fa-user-times"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs border-l-4 border-l-amber-500 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total de Atrasados</div>
                <div class="text-3xl font-bold font-mono text-amber-600 mt-1" id="summary-late">0</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCounters() {
        let present = 0, absent = 0, late = 0;
        
        document.querySelectorAll('.attendance-row').forEach(row => {
            const selected = row.querySelector('input[type="radio"]:checked');
            if (selected) {
                switch(selected.value) {
                    case 'present': present++; break;
                    case 'absent': absent++; break;
                    case 'late': late++; break;
                }
            }
        });

        const pc = document.getElementById('present-count');
        const ac = document.getElementById('absent-count');
        const lc = document.getElementById('late-count');
        const sp = document.getElementById('summary-present');
        const sa = document.getElementById('summary-absent');
        const sl = document.getElementById('summary-late');

        if (pc) pc.textContent = present + ' Presentes';
        if (ac) ac.textContent = absent + ' Ausentes';
        if (lc) lc.textContent = late + ' Atrasados';
        if (sp) sp.textContent = present;
        if (sa) sa.textContent = absent;
        if (sl) sl.textContent = late;
    }

    function syncRadioStyles(row) {
        row.querySelectorAll('label').forEach(label => {
            const radio = label.querySelector('input[type="radio"]');
            if (!radio) return;
            
            label.className = 'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer transition-all ';
            if (radio.checked) {
                if (radio.value === 'present') {
                    label.className += 'bg-emerald-600 text-white shadow-2xs';
                } else if (radio.value === 'absent') {
                    label.className += 'bg-rose-600 text-white shadow-2xs';
                } else if (radio.value === 'late') {
                    label.className += 'bg-amber-500 text-white shadow-2xs';
                }
            } else {
                label.className += 'text-slate-600 hover:text-slate-900';
            }
        });
    }

    // Marcar todos como presentes
    const markAllBtn = document.getElementById('mark-all-present');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            document.querySelectorAll('.attendance-row').forEach(row => {
                const presentRadio = row.querySelector('input[value="present"]');
                if (presentRadio) {
                    presentRadio.checked = true;
                    syncRadioStyles(row);
                }
            });
            updateCounters();
        });
    }

    // Listener para radios individuais
    document.querySelectorAll('.attendance-row input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const row = this.closest('.attendance-row');
            syncRadioStyles(row);
            updateCounters();
        });
    });

    updateCounters();

    const form = document.getElementById('attendance-form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> A guardar...';
            }
        });
    }
});
</script>
@endsection
