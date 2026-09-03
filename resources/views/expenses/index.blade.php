@extends('layouts.app')

@section('title', 'Gestão de Despesas')
@section('page-title', 'Gestão de Despesas')
@section('page-title-icon', 'fas fa-receipt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline">Início</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Despesas</li>
@endsection

@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-600"></i> Despesas Operacionais
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Registo de custos administrativos, compras e fornecedores.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('create', \App\Models\Expense::class)
                <a href="{{ route('expenses.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Nova Despesa
                </a>
            @endcan
        </div>
    </div>

    <!-- 2. Border-Accent Colored Stat Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Despesas (Rose Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-rose-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Despesas</span>
                <span class="text-xl font-black text-rose-700 mt-0.5">{{ number_format($stats['total'], 2, ',', '.') }} MT</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-arrow-trend-down"></i>
            </div>
        </div>

        <!-- Despesas do Mês (Amber Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-amber-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Despesas do Mês</span>
                <span class="text-xl font-black text-slate-800 mt-0.5">{{ number_format($stats['this_month'], 2, ',', '.') }} MT</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <!-- Pendentes (Blue Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-blue-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Pendentes</span>
                <span class="text-2xl font-black text-slate-800 mt-0.5">{{ number_format($stats['pending_approval']) }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        <!-- Categorias (Emerald Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Categorias</span>
                <span class="text-2xl font-black text-slate-800 mt-0.5">{{ $categories->count() }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('expenses.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-4 items-center">
            <div>
                <select name="category_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Categorias</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="date" name="date_from" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" value="{{ request('date_from') }}">
            </div>

            <div>
                <input type="date" name="date_to" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" value="{{ request('date_to') }}">
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['category_id', 'date_from', 'date_to']))
                    <a href="{{ route('expenses.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
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
                <i class="fas fa-receipt text-emerald-600"></i> Despesas Registadas ({{ $expenses->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Data</th>
                        <th class="py-2.5 px-3">Categoria</th>
                        <th class="py-2.5 px-3">Descrição</th>
                        <th class="py-2.5 px-3">Valor</th>
                        <th class="py-2.5 px-3">Método</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                {{ $expense->expense_date->format('d/m/Y') }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                      style="background: {{ $expense->category->color ?? '#64748b' }}15; color: {{ $expense->category->color ?? '#1e293b' }}; border: 1px solid {{ $expense->category->color ?? '#cbd5e1' }}40;">
                                    {{ $expense->category->name }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">
                                {{ $expense->description }}
                            </td>
                            <td class="py-2.5 px-3 font-black text-rose-700">
                                {{ number_format($expense->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-2.5 px-3 text-slate-700">
                                {{ $expense->payment_method ?? 'N/A' }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if($expense->approved_at)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Aprovada
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('expenses.show', $expense) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">
                                <i class="fas fa-receipt text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma despesa registada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $expenses->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
