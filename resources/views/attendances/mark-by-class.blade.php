@extends('layouts.app')

@section('title', 'Marcar Frequências')
@section('page-title', 'Marcar Frequências')
@section('page-title-icon', 'fas fa-calendar-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('attendances.index') }}">Frequências</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendances.class-attendances', $class) }}?date={{ $date }}">{{ $class->name }}</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Marcar</li>
@endsection

@section('title', 'Marcar Frequências - ' . $class->name)
@section('content')
<form action="{{ route('attendances.store-mark-by-class', $class) }}" method="POST" id="attendance-form" class="space-y-4">
    @csrf

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-calendar-plus text-emerald-600"></i> Marcar Frequências: {{ $class->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Registo diário de presenças e ausências para a turma.</p>
        </div>
        <a href="{{ route('attendances.class-attendances', $class) }}?date={{ $date }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Sidebar Config & Summary (1/3) -->
        <div class="space-y-4">
            <!-- Config Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-cog text-emerald-600"></i> Configurações
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Turma</label>
                        <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 text-slate-800 py-1.5 px-3 text-xs font-semibold" value="{{ $class->name }} ({{ $class->grade_level_name }})" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Data *</label>
                        <input type="date" name="attendance_date" value="{{ $date }}" required max="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="p-3 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-800 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        <span>Selecione o status de frequência para cada aluno.</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                        <button type="button" onclick="markAllPresent()" class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                            <i class="fas fa-check-double"></i> Todos Presentes
                        </button>
                        <button type="button" onclick="markAllAbsent()" class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-bold text-white shadow-2xs transition">
                            <i class="fas fa-times-circle"></i> Todos Ausentes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-chart-pie text-emerald-600"></i> Resumo
                    </h3>
                </div>
                <div class="p-4 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Presentes:</span>
                        <strong class="text-emerald-700 font-extrabold" id="count-present">0</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Ausentes:</span>
                        <strong class="text-rose-600 font-extrabold" id="count-absent">0</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Atrasados:</span>
                        <strong class="text-amber-600 font-extrabold" id="count-late">0</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Justificados:</span>
                        <strong class="text-blue-600 font-extrabold" id="count-excused">0</strong>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-800">Total:</span>
                        <strong class="font-extrabold text-slate-900" id="count-total">0</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content: Student List (2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-users text-emerald-600"></i> Lista de Alunos ({{ $students->count() }})
                    </h3>
                </div>

                @if($students->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                                    <th class="py-2.5 px-4">Aluno</th>
                                    <th class="py-2.5 px-3">Nº</th>
                                    <th class="py-2.5 px-3">Status Anterior</th>
                                    <th class="py-2.5 px-3 text-center">Presente</th>
                                    <th class="py-2.5 px-3 text-center">Ausente</th>
                                    <th class="py-2.5 px-3 text-center">Atrasado</th>
                                    <th class="py-2.5 px-3 text-center">Justificado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($students as $student)
                                    @php
                                        $existingStatus = $existingAttendances[$student->id] ?? null;
                                        $statusClasses = [
                                            'present' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                            'absent' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                            'late' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                            'excused' => 'bg-blue-100 text-blue-800 border border-blue-200'
                                        ];
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 px-4 font-bold text-slate-800">{{ $student->full_name }}</td>
                                        <td class="py-2.5 px-3 text-slate-600 font-mono">{{ $student->student_number }}</td>
                                        <td class="py-2.5 px-3">
                                            @if($existingStatus)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClasses[$existingStatus] ?? 'bg-slate-100 text-slate-600' }}">
                                                    {{ ucfirst($existingStatus) }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <label class="cursor-pointer inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white hover:bg-emerald-50 text-slate-400 has-[:checked]:bg-emerald-700 has-[:checked]:text-white has-[:checked]:border-emerald-700 transition">
                                                <input type="radio" class="sr-only" name="attendance[{{ $student->id }}]" value="present"
                                                       {{ $existingStatus == 'present' ? 'checked' : '' }} onchange="updateCounts()">
                                                <i class="fas fa-check text-xs"></i>
                                            </label>
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <label class="cursor-pointer inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 text-slate-400 has-[:checked]:bg-rose-600 has-[:checked]:text-white has-[:checked]:border-rose-600 transition">
                                                <input type="radio" class="sr-only" name="attendance[{{ $student->id }}]" value="absent"
                                                       {{ $existingStatus == 'absent' ? 'checked' : '' }} onchange="updateCounts()">
                                                <i class="fas fa-times text-xs"></i>
                                            </label>
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <label class="cursor-pointer inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white hover:bg-amber-50 text-slate-400 has-[:checked]:bg-amber-600 has-[:checked]:text-white has-[:checked]:border-amber-600 transition">
                                                <input type="radio" class="sr-only" name="attendance[{{ $student->id }}]" value="late"
                                                       {{ $existingStatus == 'late' ? 'checked' : '' }} onchange="updateCounts()">
                                                <i class="fas fa-clock text-xs"></i>
                                            </label>
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <label class="cursor-pointer inline-flex items-center justify-center h-7 w-7 rounded-lg border border-slate-200 bg-white hover:bg-blue-50 text-slate-400 has-[:checked]:bg-blue-600 has-[:checked]:text-white has-[:checked]:border-blue-600 transition">
                                                <input type="radio" class="sr-only" name="attendance[{{ $student->id }}]" value="excused"
                                                       {{ $existingStatus == 'excused' ? 'checked' : '' }} onchange="updateCounts()">
                                                <i class="fas fa-comment text-xs"></i>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                            <i class="fas fa-save"></i> Salvar Frequências
                        </button>
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400">
                        <i class="fas fa-users text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-semibold text-slate-600">Nenhum aluno matriculado nesta turma.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function markAllPresent() {
    document.querySelectorAll('input[type="radio"][value="present"]').forEach(radio => {
        radio.checked = true;
    });
    updateCounts();
}

function markAllAbsent() {
    document.querySelectorAll('input[type="radio"][value="absent"]').forEach(radio => {
        radio.checked = true;
    });
    updateCounts();
}

function updateCounts() {
    const present = document.querySelectorAll('input[type="radio"][value="present"]:checked').length;
    const absent = document.querySelectorAll('input[type="radio"][value="absent"]:checked').length;
    const late = document.querySelectorAll('input[type="radio"][value="late"]:checked').length;
    const excused = document.querySelectorAll('input[type="radio"][value="excused"]:checked').length;
    const total = present + absent + late + excused;

    document.getElementById('count-present').textContent = present;
    document.getElementById('count-absent').textContent = absent;
    document.getElementById('count-late').textContent = late;
    document.getElementById('count-excused').textContent = excused;
    document.getElementById('count-total').textContent = total;
}

document.addEventListener('DOMContentLoaded', function() {
    updateCounts();
});
</script>
@endpush
