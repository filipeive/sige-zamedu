@extends('layouts.app')

@section('title', 'Gestão de Matrículas')
@section('page-title', 'Matrículas & Inscrições')
@section('page-title-icon', 'fas fa-clipboard-list')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Matrículas</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-id-card text-emerald-600"></i> Matrículas & Inscrições
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestão de registos de matrículas e ano lectivo {{ $selectedYear }}.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Year Selector Pills -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-200">
                @foreach($availableYears as $year)
                    <a href="{{ route('enrollments.index', ['year' => $year]) }}"
                       class="no-underline px-2.5 py-1 rounded text-xs font-bold transition {{ $selectedYear == $year ? 'bg-emerald-800 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-200' }}">
                        {{ $year }}
                    </a>
                @endforeach
            </div>

            @can('manage_enrollments')
                <a href="{{ route('admin.enrollments.renewals') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                    <i class="fas fa-sync-alt text-amber-500"></i> Renovações
                </a>
            @endcan
            @can('create_enrollments')
                <a href="{{ route('enrollments.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Nova Matrícula
                </a>
            @endcan
        </div>
    </div>

    <!-- 2. Stat Cards (Pílula de Cor na Esquerda) -->
    @php
        $yearEnrollments = \App\Models\Enrollment::where('school_year', $selectedYear);
        $activeCount = (clone $yearEnrollments)->where('status', 'active')->count();
        $pendingCount = (clone $yearEnrollments)->where('status', 'pending')->count();
        $totalYearCount = (clone $yearEnrollments)->count();
        $totalRevenue = (clone $yearEnrollments)->where('status', 'active')->sum('monthly_fee');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Matrículas Ativas (Emerald Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Matrículas Ativas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $activeCount }}</span>
            </div>
        </div>

        <!-- Pendentes (Amber Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Pendentes</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $pendingCount }}</span>
            </div>
        </div>

        <!-- Total Ano (Blue Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Ano {{ $selectedYear }}</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $totalYearCount }}</span>
            </div>
        </div>

        <!-- Modo Escola / Receita (Teal Pill Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas {{ is_public_school() ? 'fa-landmark' : 'fa-wallet' }}"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">{{ is_public_school() ? 'Modo Escola' : 'Receita Mensal' }}</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">
                    @if(is_public_school())
                        Pública
                    @else
                        {{ number_format($totalRevenue, 0, ',', '.') }} MT
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('enrollments.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-4 items-center">
            <input type="hidden" name="year" value="{{ $selectedYear }}">
            
            <div>
                <div class="relative">
                    <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" 
                           placeholder="Pesquisar por nome ou número..." value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativas</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="pending_renewal" {{ request('status') == 'pending_renewal' ? 'selected' : '' }}>Renovação</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Concluídas</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativas</option>
                </select>
            </div>

            <div>
                <select name="class_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Turmas</option>
                    @foreach($classes as $class)
                        <option value="{{ optional($class)->id }}" {{ request('class_id') == optional($class)->id ? 'selected' : '' }}>
                            {{ optional($class)->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'status', 'class_id']))
                    <a href="{{ route('enrollments.index', ['year' => $selectedYear]) }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
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
                <i class="fas fa-clipboard-list text-emerald-600"></i> Lista de Matrículas — {{ $selectedYear }} ({{ $enrollments->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Nº Estudante</th>
                        <th class="py-2.5 px-3">Aluno</th>
                        <th class="py-2.5 px-3">Turma</th>
                        <th class="py-2.5 px-3">Data Matrícula</th>
                        @if(is_private_school())
                            <th class="py-2.5 px-3">Mensalidade</th>
                        @endif
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-700">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 font-mono text-[11px]">
                                    {{ $enrollment->student->student_number ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">
                                <a href="{{ route('students.show', $enrollment->student_id) }}" class="no-underline font-bold text-slate-800 hover:text-emerald-700 hover:no-underline transition">
                                    {{ $enrollment->student->full_name ?? 'Estudante' }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                    {{ $enrollment->class->name }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-700">
                                {{ $enrollment->enrollment_date?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            @if(is_private_school())
                                <td class="py-2.5 px-3 font-bold text-emerald-700">
                                    {{ number_format($enrollment->monthly_fee, 2, ',', '.') }} MT
                                </td>
                            @endif
                            <td class="py-2.5 px-3">
                                @php
                                    $statusBadge = match($enrollment->status) {
                                        'active' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'completed' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                        'inactive', 'cancelled' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusBadge }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('enrollments.show', $enrollment->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">
                                <i class="fas fa-clipboard-list text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma matrícula registrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $enrollments->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection