@extends('layouts.app')

@section('title', 'Detalhe da Despesa')
@section('page-title', 'Detalhe da Despesa')
@section('page-title-icon', 'fas fa-receipt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">#{{ $expense->id }}</li>
@endsection

@section('title', 'Despesa #' . $expense->id)
@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-600"></i> Despesa #{{ $expense->id }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Detalhes da saída financeira registada.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(!$expense->approved_at)
                @can('approve', $expense)
                    <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline" data-confirm="Aprovar esta despesa?">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                            <i class="fas fa-check"></i> Aprovar Despesa
                        </button>
                    </form>
                @endcan
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Aprovada por {{ $expense->approver?->name }}
                </span>
            @endif

            @can('update', $expense)
                <a href="{{ route('expenses.edit', $expense) }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
            <a href="{{ route('expenses.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Categoria</span>
                <span class="font-bold text-slate-800 text-sm">{{ $expense->category->name }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Data</span>
                <span class="font-bold text-slate-800 text-sm">{{ $expense->expense_date->format('d/m/Y') }}</span>
            </div>

            <div class="sm:col-span-2">
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Descrição</span>
                <span class="font-bold text-slate-800 leading-relaxed">{{ $expense->description }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Valor</span>
                <span class="font-black text-rose-600 text-lg">{{ number_format($expense->amount, 2, ',', '.') }} MT</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Método de Pagamento</span>
                <span class="font-semibold text-slate-700">{{ $expense->payment_method ?? '-' }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Número do Recibo</span>
                <span class="font-mono font-semibold text-slate-700">{{ $expense->receipt_number ?? '-' }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Fornecedor</span>
                <span class="font-semibold text-slate-700">{{ $expense->supplier ?? '-' }}</span>
            </div>

            <div class="sm:col-span-2">
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Observações</span>
                <span class="text-slate-600 leading-relaxed">{{ $expense->notes ?? '-' }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Registado por</span>
                <span class="font-semibold text-slate-700">{{ $expense->creator?->name ?? '-' }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block text-[10px] mb-0.5">Criado em</span>
                <span class="font-semibold text-slate-700">{{ $expense->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
