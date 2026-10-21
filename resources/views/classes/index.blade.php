@extends('layouts.app')

@section('title', 'Gestão de Turmas por Nível de Ensino')
@section('page-title', 'Turmas')
@section('page-title-icon', 'fas fa-chalkboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Turmas</li>
@endsection

@section('content')
<div class="space-y-4">

    {{-- 1. Compact Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Turmas por Nível de Ensino
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pré-Escolar &amp; Primário: <b>1 Professor Regente</b> (+ opcional Assistente).
                Secundário: <b>professor atribuído por disciplina</b>.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->can('create_classes') || auth()->user()->hasAnyRole(['admin', 'director', 'super_admin']))
                <a href="{{ route('classes.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Nova Turma
                </a>
            @endif
        </div>
    </div>

    {{-- 2. Tabs por Nível de Ensino --}}
    <div class="border-b border-slate-200 flex items-center gap-1 overflow-x-auto">
        <a href="{{ route('classes.index', ['level' => 'all']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'all' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-layer-group"></i> Todas
            <span class="px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-extrabold">{{ $levelStats['all'] }}</span>
        </a>
        <a href="{{ route('classes.index', ['level' => 'preschool']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'preschool' ? 'border-pink-500 text-pink-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-shapes"></i> Pré-Escolar
            <span class="px-1.5 py-0.5 rounded-full bg-pink-50 text-pink-700 text-[10px] font-extrabold">{{ $levelStats['preschool'] }}</span>
        </a>
        {{-- Ensino Primário com subciclos --}}
        <a href="{{ route('classes.index', ['level' => 'primary']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'primary' ? 'border-blue-500 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-child"></i> Ensino Primário
            <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-extrabold">{{ $levelStats['primary'] }}</span>
        </a>
        <a href="{{ route('classes.index', ['level' => 'ep1']) }}"
            class="px-3 py-2.5 text-[11px] font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                {{ $level === 'ep1' ? 'border-blue-400 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
            1.ºCiclo EP
            <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-500 text-[10px] font-extrabold">{{ $levelStats['ep1'] }}</span>
        </a>
        <a href="{{ route('classes.index', ['level' => 'ep2']) }}"
            class="px-3 py-2.5 text-[11px] font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                {{ $level === 'ep2' ? 'border-blue-400 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
            2.ºCiclo EP
            <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-500 text-[10px] font-extrabold">{{ $levelStats['ep2'] }}</span>
        </a>
        {{-- Ensino Secundário com subciclos --}}
        <a href="{{ route('classes.index', ['level' => 'secondary']) }}"
            class="px-4 py-2.5 text-xs font-bold border-b-2 transition flex items-center gap-2 whitespace-nowrap no-underline
                {{ $level === 'secondary' ? 'border-purple-500 text-purple-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-graduation-cap"></i> Ensino Secundário
            <span class="px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-700 text-[10px] font-extrabold">{{ $levelStats['secondary'] }}</span>
        </a>
        <a href="{{ route('classes.index', ['level' => 'esg1']) }}"
            class="px-3 py-2.5 text-[11px] font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                {{ $level === 'esg1' ? 'border-purple-400 text-purple-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
            ESG1 <span class="text-[9px]">(7ª–9ª)</span>
            <span class="px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-500 text-[10px] font-extrabold">{{ $levelStats['esg1'] }}</span>
        </a>
        <a href="{{ route('classes.index', ['level' => 'esg2']) }}"
            class="px-3 py-2.5 text-[11px] font-bold border-b-2 transition flex items-center gap-1.5 whitespace-nowrap no-underline
                {{ $level === 'esg2' ? 'border-purple-400 text-purple-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
            ESG2 <span class="text-[9px]">(10ª–12ª)</span>
            <span class="px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-500 text-[10px] font-extrabold">{{ $levelStats['esg2'] }}</span>
        </a>
    </div>

    @include('partials.super-admin-banner')

    {{-- 3. Stat Cards --}}
    @php
        $totalCapacity = \App\Models\ClassRoom::sum('max_students');
        $usedCapacity = \App\Models\Enrollment::where('status', 'active')->count();
        $capacityPercentage = $totalCapacity > 0 ? round(($usedCapacity / $totalCapacity) * 100, 1) : 0;
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Turmas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $levelStats['all'] }}</span>
            </div>
        </div>

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

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos Ativos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $usedCapacity }}</span>
            </div>
        </div>

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

    {{-- 4. Filter Bar --}}
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('classes.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-5 items-center">
            <input type="hidden" name="level" value="{{ $level }}">

            @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
            <div>
                <select name="school_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Escolas</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}" {{ request('school_id') == $sch->id ? 'selected' : '' }}>
                            {{ $sch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endhasanyrole
            
                <select name="grade_level" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Classes</option>
                    @foreach($gradeLevels as $key => $label)
                        <option value="{{ $key }}" {{ request('grade_level') == $key ? 'selected' : '' }}>
                            {{ $label }}
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
                <select name="teaching_mode" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Modos</option>
                    <option value="single_teacher" {{ request('teaching_mode') === 'single_teacher' ? 'selected' : '' }}><i class="fas fa-user me-1"></i> Professor Regente</option>
                    <option value="subject_based" {{ request('teaching_mode') === 'subject_based' ? 'selected' : '' }}><i class="fas fa-book me-1"></i> Por Disciplina</option>
                </select>
            </div>

            <div>
                <input type="text" name="search" placeholder="Pesquisar por nome ou sala..."
                    class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                    value="{{ request('search') }}">
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['grade_level', 'teacher_id', 'teaching_mode', 'search', 'school_id']))
                    <a href="{{ route('classes.index', ['level' => $level]) }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 5. Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-chalkboard text-emerald-600"></i>
                @if($level === 'all') Todas as Turmas
                @elseif($level === 'preschool') Turmas do Pré-Escolar &amp; Infantil
                @elseif($level === 'primary') Turmas do Ensino Primário
                @elseif($level === 'secondary') Turmas do Ensino Secundário
                @endif
                <span class="text-slate-500 font-bold normal-case">({{ $classes->total() }})</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Turma</th>
                        <th class="py-2.5 px-3">Nível / Classe</th>
                        <th class="py-2.5 px-3">Modo de Ensino</th>
                        <th class="py-2.5 px-3">Professor(es)</th>
                        <th class="py-2.5 px-3">Disciplinas</th>
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
                                <small class="block text-[10px] text-slate-400 font-mono">{{ $class->classroom ?? '—' }} · {{ $class->shift_label }}</small>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700 font-medium">
                                {{ $class->grade_level_name }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if($class->isSingleTeacher())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fas fa-user-tie text-[9px]"></i> Professor Regente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="fas fa-book text-[9px]"></i> Por Disciplina
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-slate-800">
                                @if($class->isSingleTeacher())
                                    <div class="font-semibold text-[11px]">
                                        @if($class->teacher)
                                            <i class="fas fa-user-tie text-emerald-500"></i> {{ $class->teacher->full_name }}
                                        @else
                                            <span class="text-rose-600 font-bold">Não atribuído</span>
                                        @endif
                                    </div>
                                    @if($class->assistantTeacher)
                                        <div class="font-semibold text-[11px] text-slate-500">
                                            <i class="fas fa-user-friends text-blue-400"></i> Assistente: {{ $class->assistantTeacher->full_name }}
                                        </div>
                                    @else
                                        <div class="text-[10px] text-slate-400 italic">Sem assistente</div>
                                    @endif
                                @else
                                    @php $distinctTeachers = $class->classSubjects->pluck('teacher_id')->filter()->unique()->count(); @endphp
                                    <div class="font-semibold text-[11px]">
                                        <i class="fas fa-chalkboard-teacher text-purple-500"></i>
                                        {{ $distinctTeachers }} professor{{ $distinctTeachers !== 1 ? 'es' : '' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500">
                                        Atribuídos por disciplina
                                    </div>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 font-mono text-[11px]">
                                {{ $class->subjects_count ?? $class->classSubjects->count() }} disc.
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
                                    @if(auth()->user()->can('edit_classes') || auth()->user()->hasAnyRole(['admin', 'director', 'super_admin']))
                                        <a href="{{ route('classes.edit', $class->id) }}" class="no-underline h-7 w-7 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 flex items-center justify-center transition" title="Editar">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->can('delete_classes') || auth()->user()->hasAnyRole(['admin', 'director', 'super_admin']))
                                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja eliminar a turma {{ $class->name }}? Esta ação não pode ser desfeita.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-7 w-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center transition border-0 cursor-pointer" title="Eliminar Turma">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400">
                                <i class="fas fa-chalkboard text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma turma encontrada para este nível.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $classes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection