@extends('layouts.app')

@section('title', 'Gestão de Licenças & Ausências')
@section('page-title', 'Licenças & Ausências')
@section('page-title-icon', 'fas fa-calendar-times')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline">Início</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Licenças</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-calendar-times text-emerald-600"></i> Pedidos de Licenças & Ausências
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestão e aprovação de dispensas, licenças médicas e férias de docentes.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('staff-leave-requests.export.csv', request()->query()) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-file-csv text-emerald-600"></i> CSV
            </a>
            <a href="{{ route('staff-leave-requests.export.pdf', request()->query()) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-file-pdf text-rose-600"></i> PDF
            </a>
        </div>
    </div>

    <!-- 2. Border-Accent Colored Stat Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Pedidos (Blue Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-blue-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total de Pedidos</span>
                <span class="text-2xl font-black text-slate-800 mt-0.5">{{ $stats['total'] }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-list"></i>
            </div>
        </div>

        <!-- Pendentes (Amber Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-amber-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Pendentes</span>
                <span class="text-2xl font-black text-slate-800 mt-0.5">{{ $stats['pending'] }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        <!-- Aprovadas (Emerald Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Aprovadas</span>
                <span class="text-2xl font-black text-slate-800 mt-0.5">{{ $stats['approved'] }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <!-- Rejeitadas (Rose Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-rose-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Rejeitadas</span>
                <span class="text-2xl font-black text-slate-800 mt-0.5">{{ $stats['rejected'] }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('staff-leave-requests.index') }}" class="grid gap-2 sm:grid-cols-2 md:grid-cols-4 items-center">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="teacher" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" 
                           placeholder="Pesquisar por nome do docente..." value="{{ request('teacher') }}">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pendente</option>
                    <option value="approved" @selected(request('status') === 'approved')>Aprovada</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejeitada</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['teacher', 'status', 'leave_type']))
                    <a href="{{ route('staff-leave-requests.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
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
                <i class="fas fa-calendar-times text-emerald-600"></i> Pedidos Registados ({{ $leaveRequests->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Docente</th>
                        <th class="py-2.5 px-3">Tipo de Licença</th>
                        <th class="py-2.5 px-3">Período</th>
                        <th class="py-2.5 px-3">Dias</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leaveRequests as $req)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                {{ $req->teacher->full_name ?? 'Docente' }}
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-700">
                                {{ ucfirst($req->leave_type) }}
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                {{ $req->start_date->format('d/m/Y') }} a {{ $req->end_date->format('d/m/Y') }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">
                                {{ $req->days_count }} dia(s)
                            </td>
                            <td class="py-2.5 px-3">
                                @php
                                    $statusBadge = match($req->status) {
                                        'approved' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'rejected' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusBadge }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('staff-leave-requests.show', $req) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">
                                <i class="fas fa-calendar-times text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum pedido de licença encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaveRequests->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
