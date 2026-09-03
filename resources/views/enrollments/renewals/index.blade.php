@extends('layouts.app')

@section('title', 'Renovação de Matrículas')
@section('page-title', 'Renovação de Matrículas')
@section('page-title-icon', 'fas fa-sync-alt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}">Matrículas</a></li>
    <li class="breadcrumb-item active">Renovações</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-sync-alt text-amber-500"></i> Renovação de Matrículas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Renovação anual de matrículas de alunos para o ano letivo {{ current_school_year() }}.</p>
        </div>
        <a href="{{ route('admin.students-archive.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-archive"></i> Ver Arquivados
        </a>
    </div>

    <!-- Stat KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos Pendentes</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $students->total() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Matriculados {{ current_school_year() }}</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Enrollment::where('school_year', current_school_year())->where('status', 'active')->count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas Ativas {{ current_school_year() }}</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\ClassRoom::where('school_year', current_school_year())->where('is_active', true)->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('admin.enrollments.renewals') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
            <div class="flex-grow w-full relative">
                <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                       placeholder="Pesquisar por nome ou nº de estudante..." value="{{ request('search') }}">
                <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
            </div>
            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                <i class="fas fa-filter text-[10px]"></i> Filtrar
            </button>
        </form>
    </div>

    <!-- Table Container -->
    @if($students->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-8 text-center">
            <i class="fas fa-check-circle text-4xl text-emerald-500 mb-3"></i>
            <h3 class="text-sm font-bold text-slate-800">Todas as renovações concluídas!</h3>
            <p class="text-xs text-slate-500 mt-1">Não há alunos pendentes de renovação para o ano letivo {{ current_school_year() }}.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden" x-data="{ activeModal: null }">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-user-clock text-amber-500"></i> Alunos Aguardando Renovação ({{ $students->total() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                            <th class="py-2.5 px-4">Estudante</th>
                            <th class="py-2.5 px-3">Contacto / Endereço</th>
                            <th class="py-2.5 px-3">Mensalidade Anterior</th>
                            <th class="py-2.5 px-4 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $student)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-4 font-bold text-slate-800">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span>{{ $student->full_name }}</span>
                                            <span class="block text-[10px] text-slate-400 font-mono font-normal">{{ $student->student_number ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600">
                                    <div><i class="fas fa-phone mr-1 text-slate-400"></i> {{ $student->emergency_phone ?? 'N/A' }}</div>
                                    <span class="text-[10px] text-slate-400"><i class="fas fa-location-dot mr-1"></i> {{ Str::limit($student->address, 30) }}</span>
                                </td>
                                <td class="py-2.5 px-3 font-bold text-slate-800">
                                    {{ number_format($student->monthly_fee ?? 0, 2, ',', '.') }} MT
                                </td>
                                <td class="py-2.5 px-4 text-end">
                                    <button type="button" @click="activeModal = {{ $student->id }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-2xs transition">
                                        <i class="fas fa-sync"></i> Renovar Matrícula
                                    </button>

                                    <!-- Renewal Modal -->
                                    <div x-show="activeModal === {{ $student->id }}" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display:none;">
                                        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl text-left space-y-4" @click.outside="activeModal = null">
                                            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                                <i class="fas fa-sync text-emerald-600"></i> Renovar Matrícula: {{ $student->full_name }}
                                            </h4>
                                            <div class="p-3 rounded-lg bg-blue-50 border border-blue-200 text-xs text-blue-800 font-semibold">
                                                Ano Letivo de Destino: <strong>{{ current_school_year() }}</strong>
                                            </div>
                                            <form action="{{ route('admin.enrollments.renew', $student) }}" method="POST" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nova Turma *</label>
                                                    <select name="class_id" required class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                                                        <option value="">Selecione a turma...</option>
                                                        @foreach($classes as $class)
                                                            <option value="{{ $class->id }}">
                                                                {{ $class->name }} ({{ $class->grade_level_name }}) - {{ $class->students_count ?? 0 }} alunos
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @if(is_private_school())
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 mb-1">Nova Mensalidade (MT) *</label>
                                                            <input type="number" name="monthly_fee" value="{{ $student->monthly_fee ?? 0 }}" step="0.01" required class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 mb-1">Dia de Pagamento</label>
                                                            <input type="number" name="payment_day" value="5" min="1" max="28" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                                                        </div>
                                                    </div>
                                                @else
                                                    <input type="hidden" name="monthly_fee" value="0">
                                                    <input type="hidden" name="payment_day" value="5">
                                                @endif

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Taxa de Renovação / Matrícula (MT)</label>
                                                    <input type="number" name="enrollment_fee" value="0" step="0.01" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                                                    <p class="mt-1 text-[10px] text-slate-400">Deixe 0 se não houver taxa.</p>
                                                </div>

                                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                                    <button type="button" @click="activeModal = null" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">Cancelar</button>
                                                    <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">Confirmar Renovação</button>
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
                <div class="px-4 py-2 border-t border-slate-100">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
