@extends('layouts.app')

@section('title', 'Gestão de Turmas')
@section('page-title', 'Turmas')
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Turmas</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Directório de Turmas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Organização de turmas, professores titulares e capacidades.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('create_classes')
                <a href="{{ route('classes.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Nova Turma
                </a>
            @endcan
        </div>
    </div>

    <!-- 2. Stat Cards (Pílula de Cor na Esquerda) -->
    @php
        $totalCapacity = \App\Models\ClassRoom::sum('max_students');
        $usedCapacity = \App\Models\Enrollment::where('status', 'active')->count();
        $capacityPercentage = $totalCapacity > 0 ? round(($usedCapacity / $totalCapacity) * 100, 1) : 0;
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Turmas (Emerald Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Turmas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\ClassRoom::count() }}</span>
            </div>
        </div>

        <!-- Turmas Ativas (Blue Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Turmas Ativas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\ClassRoom::active()->count() }}</span>
            </div>
        </div>

        <!-- Alunos Ativos (Teal Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos Ativos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Enrollment::where('status', 'active')->count() }}</span>
            </div>
        </div>

        <!-- Ocupação Média (Purple Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-purple-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Ocupação Média</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $capacityPercentage }}%</span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('classes.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-5 items-center">
            <div>
                <select name="grade_level" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Níveis</option>
                    @foreach($gradeLevels as $key => $level)
                        <option value="{{ $key }}" {{ request('grade_level') == $key ? 'selected' : '' }}>
                            {{ $level }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="teacher_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Professores</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="number" name="school_year" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                       value="{{ request('school_year', $currentYear) }}" min="2020" max="2030">
            </div>

            <div>
                <select name="is_active" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Turmas</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Ativas</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inativas</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['grade_level', 'teacher_id', 'school_year', 'is_active']))
                    <a href="{{ route('classes.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
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
                <i class="fas fa-chalkboard text-emerald-600"></i> Turmas Registadas ({{ $classes->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Turma</th>
                        <th class="py-2.5 px-3">Turno</th>
                        <th class="py-2.5 px-3">Nível</th>
                        <th class="py-2.5 px-3">Professor Titular</th>
                        <th class="py-2.5 px-3">Capacidade</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($classes as $class)
                        @php
                            $activeStudentsCount = $class->active_students_count ?? 0;
                            $capacityPercentage = $class->max_students > 0 ? round(($activeStudentsCount / $class->max_students) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                <a href="{{ route('classes.show', $class->id) }}" class="no-underline hover:no-underline font-bold text-slate-800 hover:text-emerald-700 transition">
                                    {{ $class->name }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $class->shift_label }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">
                                {{ $class->grade_level_name }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-800 font-semibold">
                                {{ $class->teacher->full_name ?? 'Não atribuído' }}
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $capacityPercentage > 90 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $capacityPercentage) }}%;"></div>
                                    </div>
                                    <span class="font-bold text-[11px] text-slate-700">{{ $activeStudentsCount }}/{{ $class->max_students }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $class->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $class->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('classes.show', $class->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">
                                <i class="fas fa-chalkboard text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma turma registrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $classes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection