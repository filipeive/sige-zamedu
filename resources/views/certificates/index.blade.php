@extends('layouts.app')

@section('title', 'Emissão de Certidões e Certificados')
@section('page-title', 'Centro de Certidões & Certificados')
@section('page-title-icon', 'fas fa-certificate')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline">Início</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Certidões & Certificados</li>
@endsection

@section('content')
<div class="max-w-full mx-auto space-y-4">

    <!-- 1. Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-certificate text-emerald-600"></i> Centro de Emissão de Certidões & Certificados
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Validação automática de elegibilidade com base no histórico de notas e aprovações do MINEDH.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-chart-bar text-slate-500"></i> Relatórios
            </a>
        </div>
    </div>

    <!-- 2. KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total de Alunos</span>
                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="text-xl font-black text-slate-800 mt-1">{{ number_format($totalStudents) }}</div>
            <span class="text-[10px] text-slate-400 font-medium">Registados no sistema</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aptos p/ Certidão</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-file-pdf"></i>
                </div>
            </div>
            <div class="text-xl font-black text-blue-700 mt-1">{{ number_format($studentsWithGrades) }}</div>
            <span class="text-[10px] text-blue-600/80 font-medium">Com notas lançadas</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Concluintes / Aprovados</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-award"></i>
                </div>
            </div>
            <div class="text-xl font-black text-emerald-700 mt-1">{{ number_format($approvedStudents) }}</div>
            <span class="text-[10px] text-emerald-600/80 font-medium">Aptos a Certificado</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pendentes (Sem Notas)</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="text-xl font-black text-amber-700 mt-1">{{ number_format($pendingStudents) }}</div>
            <span class="text-[10px] text-amber-600/80 font-medium">Aguardando avaliações</span>
        </div>
    </div>

    <!-- 3. Filters & Search Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
        <form method="GET" action="{{ route('certificates.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div class="sm:col-span-1">
                <label for="search" class="block text-xs font-bold text-slate-700 mb-1">Pesquisar Estudante</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Nome ou Nº de Estudante..."
                           class="w-full rounded-lg border border-slate-200 pl-8 pr-3 py-1.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label for="class_id" class="block text-xs font-bold text-slate-700 mb-1">Turma</label>
                <select id="class_id" name="class_id" class="w-full rounded-lg border border-slate-200 py-1.5 px-3 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Todas as Turmas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->grade_level_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="eligibility" class="block text-xs font-bold text-slate-700 mb-1">Status de Elegibilidade</label>
                <select id="eligibility" name="eligibility" class="w-full rounded-lg border border-slate-200 py-1.5 px-3 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Todos os Alunos</option>
                    <option value="with_grades" {{ request('eligibility') == 'with_grades' ? 'selected' : '' }}>Aptos (Com Notas)</option>
                    <option value="approved" {{ request('eligibility') == 'approved' ? 'selected' : '' }}>Concluintes (Aprovados)</option>
                    <option value="without_grades" {{ request('eligibility') == 'without_grades' ? 'selected' : '' }}>Pendentes (Sem Notas)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'class_id', 'eligibility']))
                    <a href="{{ route('certificates.index') }}" class="no-underline px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-600 transition" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Students Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list-check text-emerald-600"></i> Lista de Alunos & Elegibilidade de Emissão
            </h3>
            <span class="text-[11px] font-bold text-slate-500">Exibindo {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} de {{ $students->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100/70 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <th class="py-2.5 px-4">Estudante</th>
                        <th class="py-2.5 px-3">Nº Estudante</th>
                        <th class="py-2.5 px-3">Turma Actual</th>
                        <th class="py-2.5 px-3 text-center">Histórico de Notas</th>
                        <th class="py-2.5 px-3 text-center">Elegibilidade</th>
                        <th class="py-2.5 px-4 text-end">Ações de Emissão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        @php
                            $hasGrades = $student->grades_count > 0;
                            $avgGrade = $student->grades_avg_grade ? round($student->grades_avg_grade, 1) : null;
                            $isApproved = $student->status == 'graduated' || ($avgGrade !== null && $avgGrade >= 10.0);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        @if($student->passport_photo)
                                            <img src="{{ Storage::url($student->passport_photo) }}" alt="{{ $student->full_name }}" class="w-full h-full object-cover rounded-full">
                                        @else
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('students.show', $student) }}" class="no-underline font-bold text-slate-800 hover:text-emerald-700 transition">
                                            {{ $student->full_name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[11px]">
                                    {{ $student->student_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 font-semibold">
                                {{ $student->currentEnrollment?->class?->name ?? 'Sem Turma' }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($hasGrades)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fas fa-check-circle text-blue-500"></i>
                                        {{ $student->grades_count }} {{ $student->grades_count == 1 ? 'nota' : 'notas' }} (Média {{ number_format($avgGrade, 1) }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-exclamation-triangle text-amber-500"></i> Sem notas
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($isApproved)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-award"></i> Concluinte / Aprovado
                                    </span>
                                @elseif($hasGrades)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-file-signature"></i> Elegível p/ Certidão
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        <i class="fas fa-hourglass-start"></i> Aguardando Notas
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Botão Certidão de Habilitações -->
                                    @if($hasGrades)
                                        <a href="{{ route('certificates.habilidades', $student->id) }}" target="_blank"
                                           class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition shadow-2xs">
                                            <i class="fas fa-file-pdf"></i> Certidão Habilitações
                                        </a>
                                    @else
                                        <button disabled title="Lance notas antes de emitir a certidão"
                                                class="opacity-40 cursor-not-allowed inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 text-xs font-bold">
                                            <i class="fas fa-file-pdf"></i> Certidão (Sem Notas)
                                        </button>
                                    @endif

                                    <!-- Botão Certificado de Conclusão -->
                                    @if($hasGrades)
                                        <a href="{{ route('certificates.conclusao', $student->id) }}" target="_blank"
                                           class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition shadow-2xs">
                                            <i class="fas fa-certificate"></i> Certificado Conclusão
                                        </a>
                                        <a href="{{ route('certificates.official', $student->id) }}" target="_blank"
                                           class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold transition shadow-2xs"
                                           title="Certificado Oficial MINEDH com notas por extenso">
                                            <i class="fas fa-award text-amber-600"></i> Oficial MINEDH
                                        </a>
                                    @else
                                        <button disabled title="Lance avaliações para habilitar diploma de conclusão"
                                                class="opacity-40 cursor-not-allowed inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 text-xs font-bold">
                                            <i class="fas fa-certificate"></i> Diploma (Pendente)
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                <i class="fas fa-certificate text-3xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum aluno encontrado para a seleção de filtros efetuada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
