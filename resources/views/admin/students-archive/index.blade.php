@extends('layouts.app')

@section('title', 'Estudantes Arquivados')
@section('page-title', 'Alunos Arquivados')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item">Students-archive</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Alunos Arquivados</li>
@endsection

@section('content')
<div class="w-full px-4 py-6" x-data="{ activeModal: null }">

    <!-- Stat KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-slate-500 shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-slate-100 text-slate-600 rounded-2xl">
                <i class="fa-solid fa-box-archive text-xl"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Arquivados</div>
                <h4 class="text-2xl font-extrabold text-slate-900">{{ $stats['total'] }}</h4>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-sky-500 shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-sky-50 text-sky-600 rounded-2xl">
                <i class="fa-solid fa-right-from-bracket text-xl"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Transferidos</div>
                <h4 class="text-2xl font-extrabold text-sky-700">{{ $stats['transferred'] }}</h4>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-emerald-600 shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                <i class="fa-solid fa-award text-xl"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Formados</div>
                <h4 class="text-2xl font-extrabold text-emerald-700">{{ $stats['graduated'] }}</h4>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 border-l-4 border-l-rose-500 shadow-sm p-4 flex items-center gap-4">
            <div class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                <i class="fa-solid fa-user-slash text-xl"></i>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Desistiram</div>
                <h4 class="text-2xl font-extrabold text-rose-700">{{ $stats['inactive'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Filtros Harmonizados -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <form action="{{ route('admin.students-archive.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-5">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pesquisar Estudante</label>
                <input type="text" name="search" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white"
                    placeholder="Pesquisar por nome ou nº de estudante..." value="{{ request('search') }}">
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Motivo de Arquivo</label>
                <select name="exit_status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Todos os Status</option>
                    <option value="transferred" {{ request('exit_status') == 'transferred' ? 'selected' : '' }}>Transferidos</option>
                    <option value="graduated" {{ request('exit_status') == 'graduated' ? 'selected' : '' }}>Formados</option>
                    <option value="inactive" {{ request('exit_status') == 'inactive' ? 'selected' : '' }}>Desistiram</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm inline-flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </div>
            <div class="md:col-span-2">
                <a href="{{ route('admin.enrollments.renewals') }}" class="w-full px-4 py-2 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-rotate"></i> Renovações
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela de Estudantes Arquivados -->
    @if($students->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
            <i class="fa-solid fa-box-archive text-5xl text-slate-300 mb-3 block"></i>
            <h5 class="text-base font-bold text-slate-800">Nenhum estudante arquivado</h5>
            <p class="text-xs text-slate-500 mt-1">Não foram encontrados registos de transferência, formatura ou inatividade.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-4 md:p-6 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-slate-500"></i> Lista de Estudantes Arquivados
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-extrabold">{{ $students->total() }}</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600 divide-y divide-slate-200">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Estudante</th>
                            <th class="px-4 py-3">Contacto</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Última Matrícula</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 font-medium">
                        @foreach($students as $student)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $student->full_name }}</div>
                                            <code class="text-xs text-slate-500">{{ $student->student_number ?? 'N/A' }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    <i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $student->emergency_phone ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusSubtle = [
                                            'transferred' => 'bg-sky-50 text-sky-700 border-sky-200',
                                            'graduated' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'inactive' => 'bg-slate-100 text-slate-600 border-slate-200'
                                        ];
                                        $statusLabels = [
                                            'transferred' => 'Transferido',
                                            'graduated' => 'Formado',
                                            'inactive' => 'Desistente'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold {{ $statusSubtle[$student->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $statusLabels[$student->status] ?? ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @php
                                        $lastEnrollment = $student->enrollments->last();
                                    @endphp
                                    @if($lastEnrollment)
                                        <div class="font-bold text-slate-800">{{ $lastEnrollment->class->name ?? 'N/A' }}</div>
                                        <div class="text-slate-400">Ano {{ $lastEnrollment->school_year }}</div>
                                    @else
                                        <span class="text-slate-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.students-archive.show', $student) }}"
                                            class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-sm" title="Ver Detalhes">
                                            <i class="fa-solid fa-eye text-emerald-600"></i>
                                        </a>
                                        <button type="button" @click="activeModal = 'reactivate{{ $student->id }}'" class="px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold transition shadow-sm" title="Reativar Estudante">
                                            <i class="fa-solid fa-rotate-right"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de Reativação Alpine.js -->
                                    <div x-show="activeModal === 'reactivate{{ $student->id }}'" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                        <div @click.away="activeModal = null" class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full overflow-hidden text-left">
                                            <form action="{{ route('admin.students-archive.reactivate', $student) }}" method="POST">
                                                @csrf
                                                <div class="p-5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                                    <h5 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                                        <i class="fa-solid fa-rotate-right text-emerald-600"></i> Reativar: {{ $student->full_name }}
                                                    </h5>
                                                    <button type="button" @click="activeModal = null" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
                                                </div>
                                                <div class="p-6 space-y-4">
                                                    <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl p-3.5 text-xs flex items-center gap-2">
                                                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base"></i>
                                                        <span>O aluno será reativado no ano letivo <strong>{{ current_school_year() }}</strong>.</span>
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nova Turma *</label>
                                                        <select name="class_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white" required>
                                                            <option value="">Selecione...</option>
                                                            @foreach(\App\Models\ClassRoom::where('school_year', current_school_year())->where('is_active', true)->get() as $class)
                                                                <option value="{{ $class->id }}">
                                                                    {{ $class->name }} ({{ $class->grade_level_name }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Mensalidade (MT) *</label>
                                                            <input type="number" name="monthly_fee" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white"
                                                                value="{{ $student->monthly_fee ?? 0 }}" step="0.01" required>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Observações</label>
                                                            <input type="text" name="reason" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 bg-white"
                                                                placeholder="Motivo da reativação">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
                                                    <button type="button" @click="activeModal = null" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">Cancelar</button>
                                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5">
                                                        <i class="fa-solid fa-check"></i> Confirmar Reativação
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection