@extends('layouts.app')

@section('title', 'Gestão de Professores')
@section('page-title', 'Professores & Corpo Docente')
@section('page-title-icon', 'fas fa-chalkboard-teacher')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Professores</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Corpo Docente & Professores
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestão de professores, disciplinas atribuídas e contactos.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('create_teachers')
                <a href="{{ route('teachers.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Novo Professor
                </a>
            @endcan
        </div>
    </div>

    <!-- 2. Stat Cards (Pílula de Cor na Esquerda) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Professores (Emerald Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Professores</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $teachers->total() }}</span>
            </div>
        </div>

        <!-- Ativos (Blue Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Ativos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Teacher::where('status', 'active')->count() }}</span>
            </div>
        </div>

        <!-- Masculino (Teal Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-mars"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Masculino</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Teacher::where('gender', 'male')->count() }}</span>
            </div>
        </div>

        <!-- Feminino (Pink Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-pink-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-venus"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Feminino</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Teacher::where('gender', 'female')->count() }}</span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('teachers.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-4 items-center">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" 
                           placeholder="Pesquisar por nome, email ou BI..." value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('teachers.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
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
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Professores Registados ({{ $teachers->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Professor</th>
                        <th class="py-2.5 px-3">Contacto</th>
                        <th class="py-2.5 px-3">Email</th>
                        <th class="py-2.5 px-3">Especialidade</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                <div class="flex items-center gap-2">
                                    <div class="h-7 w-7 rounded-lg bg-emerald-700 text-white font-bold text-[10px] flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                                    </div>
                                    <a href="{{ route('teachers.show', $teacher->id) }}" class="no-underline font-bold text-slate-800 hover:text-emerald-700 hover:no-underline transition">
                                        {{ $teacher->full_name }}
                                    </a>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700">
                                {{ $teacher->phone ?? 'N/I' }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                {{ $teacher->email ?? 'N/I' }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[10px] border border-slate-200">
                                    {{ $teacher->specialization ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $teacher->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $teacher->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('teachers.show', $teacher->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher->id) }}" class="no-underline h-7 w-7 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 flex items-center justify-center transition" title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @if($teacher->status === 'active')
                                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="no-underline h-7 w-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center transition" title="Apagar" onclick="return confirm('Tem certeza que deseja apagar este professor?')">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">
                                <i class="fas fa-user-tie text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum professor registrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $teachers->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection