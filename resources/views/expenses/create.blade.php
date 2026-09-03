@extends('layouts.app')

@section('title', 'Nova Despesa Operacional')
@section('page-title', 'Registar Despesa')
@section('page-title-icon', 'fas fa-plus-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}" class="no-underline">Despesas</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Nova</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-4">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-receipt text-rose-600"></i> Registar Nova Despesa
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Informe a categoria, valor e método de pagamento da saída financeira.</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar às Despesas
        </a>
    </div>

    <!-- Form Container Card with Rose Accent -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-rose-600 shadow-2xs p-5">
        <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Categoria de Despesa *</label>
                    <select name="category_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500 @error('category_id') border-rose-500 @enderror" required>
                        <option value="">Selecione a categoria...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data da Despesa *</label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500 @error('expense_date') border-rose-500 @enderror">
                    @error('expense_date')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Descrição *</label>
                    <input type="text" name="description" value="{{ old('description') }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500 @error('description') border-rose-500 @enderror"
                           placeholder="Ex: Compra de resmas de papel e tintas de impressora">
                    @error('description')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Valor da Despesa (MT) *</label>
                    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" required
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500 @error('amount') border-rose-500 @enderror" placeholder="0.00">
                    @error('amount')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Método de Pagamento</label>
                    <select name="payment_method" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500 @error('payment_method') border-rose-500 @enderror">
                        <option value="">Selecione...</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro / Numerário</option>
                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Transferência Bancária</option>
                        <option value="mpesa" {{ old('payment_method') == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="emola" {{ old('payment_method') == 'emola' ? 'selected' : '' }}>eMola</option>
                        <option value="multicaixa" {{ old('payment_method') == 'multicaixa' ? 'selected' : '' }}>Multicaixa</option>
                    </select>
                    @error('payment_method')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Número do Recibo / Fatura</label>
                    <input type="text" name="receipt_number" value="{{ old('receipt_number') }}"
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500" placeholder="Ex: FT 2026/001">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Fornecedor / Entidade</label>
                    <input type="text" name="supplier" value="{{ old('supplier') }}"
                           class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500" placeholder="Ex: Papelaria Confiante Lda">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Observações Adicionais</label>
                <textarea name="notes" rows="2"
                          class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500" placeholder="Informações adicionais...">{{ old('notes') }}</textarea>
            </div>

            <!-- Botões de Ação -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('expenses.index') }}" class="no-underline px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-check"></i> Registar Despesa
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
