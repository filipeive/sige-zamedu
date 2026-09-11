{{-- VAmos estilizar com tailwindcss --}}

@extends('layouts.app')

@section('title', 'Marcar Presenças')
@section('page-title', 'Marcar Presenças')

@section('content')
<form action="{{ route('attendances.store-mark') }}" method="POST" id="attendance-form" class="w-full max-w-7xl mx-auto px-4 py-6">
    @csrf

    <div class="w-full mx-auto px-4 py-6 space-y-6">
        <!-- Seleção de Turma e Data -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-gear text-emerald-600"></i> Configurações
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Turma <span class="text-rose-500">*</span></label>
                        <select name="class_id" id="class_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white" required onchange="loadStudents()">
                            <option value="">Selecione uma turma...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->current_students }} alunos)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Data <span class="text-rose-500">*</span></label>
                        <input type="date" name="attendance_date" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white"
                               value="{{ date('Y-m-d') }}" required max="{{ date('Y-m-d') }}">
                    </div>

                    <div class="bg-sky-50 border border-sky-200 text-sky-900 rounded-xl p-3.5 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-sky-600 text-base"></i>
                        <span>Selecione uma turma para carregar a lista de alunos.</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2">
                        <button type="button" class="px-3 py-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-xs transition inline-flex items-center justify-center gap-1.5" onclick="markAllPresent()">
                            <i class="fa-solid fa-check-double"></i> Todos Presentes
                        </button>
                        <button type="button" class="px-3 py-2 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-xs transition inline-flex items-center justify-center gap-1.5" onclick="markAllAbsent()">
                            <i class="fa-solid fa-circle-xmark"></i> Todos Ausentes
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-chart-pie text-emerald-600"></i> Resumo
                </h3>
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-medium">Presentes:</span>
                        <strong class="text-emerald-700 font-bold text-sm" id="count-present">0</strong>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-2">
                        <span class="text-slate-500 font-medium">Ausentes:</span>
                        <strong class="text-rose-700 font-bold text-sm" id="count-absent">0</strong>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-100 pt-2">
                        <span class="text-slate-500 font-medium">Atrasados:</span>
                        <strong class="text-amber-600 font-bold text-sm" id="count-late">0</strong>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-200 pt-2 font-bold text-slate-900">
                        <span>Total Selecionado:</span>
                        <strong class="text-sm" id="count-total">0</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Alunos -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-users text-emerald-600"></i> Lista de Alunos
                </h3>

                <div id="students-list" class="text-center py-12 text-slate-400">
                    <i class="fa-solid fa-users text-5xl mb-3 text-slate-300 block"></i>
                    <p class="text-sm font-semibold text-slate-500">Selecione uma turma para ver os alunos</p>
                </div>

                <div id="students-attendance" class="space-y-3" style="display: none;">
                    <!-- Preenchido via JavaScript -->
                </div>

                <div class="mt-6 pt-4 border-t border-slate-200 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-md transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Presenças
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function loadStudents() {
    const classId = document.getElementById('class_id').value;

    if (!classId) {
        document.getElementById('students-list').style.display = 'block';
        document.getElementById('students-attendance').style.display = 'none';
        return;
    }

    fetch(`/api/classes/${classId}/students`)
        .then(response => response.json())
        .then(students => {
            const container = document.getElementById('students-attendance');
            container.innerHTML = '';

            students.forEach(student => {
                const studentCard = `
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <img src="${student.photo_url}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                            <div>
                                <strong class="text-sm font-bold text-slate-900 block">${student.full_name}</strong>
                                <span class="text-xs font-mono text-slate-500">${student.student_number}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 w-full sm:w-auto justify-end">
                            <label class="cursor-pointer">
                                <input type="radio" class="hidden peer" name="attendance[${student.id}]" id="present_${student.id}" value="present" onchange="updateCounts()">
                                <span class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-check"></i> Presente
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" class="hidden peer" name="attendance[${student.id}]" id="absent_${student.id}" value="absent" onchange="updateCounts()">
                                <span class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-xmark"></i> Ausente
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" class="hidden peer" name="attendance[${student.id}]" id="late_${student.id}" value="late" onchange="updateCounts()">
                                <span class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-bold peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-clock"></i> Atrasado
                                </span>
                            </label>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', studentCard);
            });

            document.getElementById('students-list').style.display = 'none';
            document.getElementById('students-attendance').style.display = 'block';
            updateCounts();
        })
        .catch(error => {
            console.error('Erro ao carregar alunos:', error);
            showToast('Erro ao carregar alunos. Tente novamente.', 'error');
        });
}

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
    const total = document.querySelectorAll('input[type="radio"]:checked').length;

    document.getElementById('count-present').textContent = present;
    document.getElementById('count-absent').textContent = absent;
    document.getElementById('count-late').textContent = late;
    document.getElementById('count-total').textContent = total;
}
</script>
@endpush