@extends('layouts.app')

@section('title', 'Passagem de Classe')
@section('page-title', 'Passagem de Classe (Promoção)')
@section('page-title-icon', 'fas fa-chalkboard-user')

@section('breadcrumbs')
    <li class="breadcrumb-item">Promotion</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Passagem de Classe (Promoção)</li>
@endsection

@section('content')
<div class="w-full  px-4 py-6">

    <!-- Card de Seleção de Turma & Ações Globais -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form action="{{ route('admin.promotion.index') }}" method="GET" class="flex-grow grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-8">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Selecionar Turma Origem</label>
                    <select name="class_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" onchange="this.form.submit()">
                        <option value="">Selecione a turma...</option>
                        @if(is_iterable($classes))
                            @foreach($classes as $class)
                                <option value="{{ optional($class)->id }}" {{ $classId == optional($class)->id ? 'selected' : '' }}>
                                    {{ optional($class)->name }} ({{ optional($class)->grade_level_name }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="md:col-span-4">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition shadow-sm inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Carregar Turma
                    </button>
                </div>
            </form>

            <!-- Botão de Restauração (Undo) -->
            <form action="{{ route('admin.promotion.reset') }}" method="POST" onsubmit="return confirm('Deseja restaurar todas as matrículas do ano letivo para o estado Ativo?');">
                @csrf
                <button type="submit" class="w-full md:w-auto px-4 py-2 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap" title="Desfazer transição">
                    <i class="fa-solid fa-rotate-left"></i> Restaurar Matrículas
                </button>
            </form>
        </div>
    </div>

    @if($classId && isset($students))
        @php
            $totalStudents = count($students);
            $approvedCount = collect($students)->filter(fn($s) => $s->is_eligible)->count();
            $failedCount = collect($students)->filter(fn($s) => $s->calculated_mf !== null && !$s->is_eligible)->count();
            $noGradesCount = collect($students)->filter(fn($s) => $s->calculated_mf === null && $s->partial_mf === null)->count();
        @endphp

        <!-- Stat KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-emerald-600 shadow-sm p-4 flex items-center gap-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total na Turma</div>
                    <h4 class="text-2xl font-extrabold text-slate-900">{{ $totalStudents }}</h4>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-teal-600 shadow-sm p-4 flex items-center gap-4">
                <div class="p-3 bg-teal-50 text-teal-600 rounded-2xl">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Elegíveis (Aprovados)</div>
                    <h4 class="text-2xl font-extrabold text-teal-700">{{ $approvedCount }}</h4>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-rose-600 shadow-sm p-4 flex items-center gap-4">
                <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                    <i class="fa-solid fa-circle-xmark text-xl"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Reprovados / Risco</div>
                    <h4 class="text-2xl font-extrabold text-rose-700">{{ $failedCount }}</h4>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-amber-500 shadow-sm p-4 flex items-center gap-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sem Avaliação</div>
                    <h4 class="text-2xl font-extrabold text-amber-700">{{ $noGradesCount }}</h4>
                </div>
            </div>
        </div>

        <!-- Tabela de Alunos da Turma -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-4 md:p-6 bg-slate-50/50 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-emerald-600"></i> Alunos da Turma
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">{{ $totalStudents }}</span>
                </h3>
                <a href="{{ route('grades.batch-create', ['class_id' => $classId]) }}" class="px-3.5 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Lançar / Editar Notas
                </a>
            </div>

            <form action="{{ route('admin.promotion.process') }}" method="POST">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-600 divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="p-4 w-10 text-center">
                                    <input type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" id="check-all">
                                </th>
                                <th class="px-4 py-3">Nº Estudante</th>
                                <th class="px-4 py-3">Nome do Aluno</th>
                                <th class="px-4 py-3">Média Final (MF)</th>
                                <th class="px-4 py-3">Resultado Sugerido</th>
                                <th class="px-4 py-3">Status Atual</th>
                                <th class="px-4 py-3 text-right">Ação Rápida</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-medium">
                            @forelse($students as $student)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 text-center">
                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 student-check">
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-700">
                                        {{ $student->student_number }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-900">
                                        {{ $student->full_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($student->calculated_mf !== null)
                                            <span class="font-extrabold text-base {{ $student->is_eligible ? 'text-emerald-700' : 'text-rose-600' }}">
                                                {{ number_format($student->calculated_mf, 1) }}
                                            </span>
                                        @elseif($student->partial_mf !== null)
                                            <span class="font-bold text-amber-600" title="Média parcial disponível">
                                                {{ number_format($student->partial_mf, 1) }} <small class="text-slate-400 text-xs">(Parcial)</small>
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">Sem Notas</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($student->calculated_mf !== null)
                                            @if($student->is_eligible)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                                                    <i class="fa-solid fa-circle-check"></i> Aprovado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-semibold">
                                                    <i class="fa-solid fa-circle-xmark"></i> Reprovado
                                                </span>
                                            @endif
                                        @elseif($student->partial_mf !== null)
                                            @if($student->is_eligible)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                                                    <i class="fa-solid fa-check"></i> Aprovado (Parcial)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold">
                                                    <i class="fa-solid fa-triangle-exclamation"></i> Em Risco
                                                </span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold">
                                                <i class="fa-solid fa-clock"></i> Incompleto
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-xs font-semibold">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('grades.batch-create', ['class_id' => $classId]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-sm" title="Lançar notas">
                                            <i class="fa-solid fa-pen-to-square text-emerald-600"></i> Lançar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-12 text-slate-500">
                                        <i class="fa-solid fa-graduation-cap text-4xl mb-3 text-slate-300 block"></i>
                                        <h5 class="text-base font-bold text-slate-800">Nenhum aluno encontrado</h5>
                                        <p class="text-xs text-slate-500 mt-1">Não existem alunos matriculados nesta turma.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(!empty($students))
                    <div class="p-4 border-t border-slate-200 flex items-center justify-end gap-3 bg-slate-50/50">
                        <button type="submit" name="action" value="retain" class="px-4 py-2 rounded-xl border border-rose-300 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-left"></i> Reter Selecionados
                        </button>
                        <button type="submit" name="action" value="promote" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-arrow-up"></i> Promover / Graduar Selecionados
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check-all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.student-check').forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>
@endpush