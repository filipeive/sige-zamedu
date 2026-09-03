@extends('layouts.app')

@section('title', 'Gestão de Disciplinas')
@section('page-title', 'Disciplinas & Matriz Curricular')
@section('page-title-icon', 'fas fa-book')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Disciplinas</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-book text-emerald-600"></i> Matriz Curricular & Disciplinas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Catálogo de disciplinas lectivas, códigos e carga horária.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('create_subjects')
                <a href="{{ route('subjects.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Nova Disciplina
                </a>
            @endcan
        </div>
    </div>

    <!-- 2. Stat Cards (Pílula de Cor na Esquerda) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Disciplinas (Emerald Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Disciplinas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $totalSubjects }}</span>
            </div>
        </div>

        <!-- Disciplinas Ativas (Blue Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Disciplinas Ativas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $activeSubjects }}</span>
            </div>
        </div>

        <!-- Turmas Atribuídas (Teal Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas Atribuídas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\ClassSubject::distinct('class_id')->count() }}</span>
            </div>
        </div>

        <!-- Inativas (Amber Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Inativas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $totalSubjects - $activeSubjects }}</span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('subjects.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-4 items-center">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" 
                           placeholder="Pesquisar por nome ou código..." value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativas</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativas</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('subjects.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-book text-emerald-600"></i> Lista de Disciplinas ({{ $subjects->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Código</th>
                        <th class="py-2.5 px-3">Nome da Disciplina</th>
                        <th class="py-2.5 px-3">Nível</th>
                        <th class="py-2.5 px-3">Carga Horária</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-700">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 font-mono text-[11px]">
                                    {{ $subject->code ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">
                                <a href="{{ route('subjects.show', $subject->id) }}" class="no-underline font-bold text-slate-800 hover:text-emerald-700 hover:no-underline transition">
                                    {{ $subject->name }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">
                                {{ $subject->education_level_label ?? 'Geral' }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 font-semibold">
                                {{ $subject->weekly_workload ? $subject->weekly_workload . 'h/semana' : '-' }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $subject->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $subject->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('subjects.show', $subject->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">
                                <i class="fas fa-book text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma disciplina registrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $subjects->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection