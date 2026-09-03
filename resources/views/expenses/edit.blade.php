@extends('layouts.app')

@section('title', 'Editar Despesa')
@section('page-title', 'Editar Despesa')
@section('page-title-icon', 'fas fa-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Início</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-edit text-amber-600"></i> Editar Despesa
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Altere os dados da saída financeira.</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-amber-500 shadow-2xs p-5">
        <form action="{{ route('expenses.update', $expense) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Categoria *</label>
                    <select name="category_id" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('category_id') border-rose-500 @enderror">
                        <option value="">Selecione...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data *</label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('expense_date') border-rose-500 @enderror">
                    @error('expense_date')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Descrição *</label>
                <input type="text" name="description" value="{{ old('description', $expense->description) }}" required
                       class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('description') border-rose-500 @enderror">
                @error('description')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Valor (MT) *</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('amount') border-rose-500 @enderror">
                    @error('amount')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Método de Pagamento</label>
                    <select name="payment_method"
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('payment_method') border-rose-500 @enderror">
                        <option value="">Selecione...</option>
                        <option value="cash" {{ old('payment_method', $expense->payment_method) == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="bank" {{ old('payment_method', $expense->payment_method) == 'bank' ? 'selected' : '' }}>Transferência Bancária</option>
                        <option value="mpesa" {{ old('payment_method', $expense->payment_method) == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="emola" {{ old('payment_method', $expense->payment_method) == 'emola' ? 'selected' : '' }}>eMola</option>
                        <option value="multicaixa" {{ old('payment_method', $expense->payment_method) == 'multicaixa' ? 'selected' : '' }}>Multicaixa</option>
                    </select>
                    @error('payment_method')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Número do Recibo</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number', $expense->receipt_number) }}"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('receipt_number') border-rose-500 @enderror">
                    @error('receipt_number')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Fornecedor</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $expense->supplier) }}"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('supplier') border-rose-500 @enderror">
                    @error('supplier')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Observações</label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('notes') border-rose-500 @enderror">{{ old('notes', $expense->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('expenses.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Atualizar Despesa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
