@extends('layouts.app')

@section('title', 'Detalhes do Pagamento')
@section('page-title', 'Detalhes do Pagamento')
@section('page-title-icon', 'fas fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}" class="no-underline"><i class="fas fa-wallet me-1"></i>Pagamentos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">{{ $payment->reference_number }}</li>
@endsection

@section('page-title', 'Pagamento #' . $payment->reference_number)

@section('content')
    <div class="payments-show grid gap-6 xl:grid-cols-12" x-data="{ processOpen:false, cancelOpen:false }">
        <section class="xl:col-span-8 space-y-6">
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-mono text-sm font-semibold text-slate-800">{{ $payment->reference_number }}</p>
                        <p class="mt-1 text-xs text-slate-500">Referencia de Pagamento</p>
                    </div>
                    @php
                        $statusClass = $payment->status === 'paid'
                            ? 'bg-emerald-100 text-emerald-700'
                            : ($payment->status === 'overdue'
                                ? 'bg-rose-100 text-rose-700'
                                : ($payment->status === 'cancelled' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700'));
                    @endphp
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ strtoupper($payment->status) }}</span>
                </div>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-5 py-4"><h3 class="text-sm font-semibold text-slate-900">Detalhes do Pagamento</h3></header>
                <div class="grid gap-4 p-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Tipo</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ ucfirst($payment->type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Periodo</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">@if($payment->month){{ $payment->month_name }} / {{ $payment->year }}@else{{ $payment->year }}@endif</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Vencimento</p>
                        <p class="mt-1 text-sm font-semibold {{ $payment->due_date && $payment->due_date < now() && $payment->status != 'paid' ? 'text-rose-700' : 'text-slate-900' }}">{{ $payment->due_date?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Data de Pagamento</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $payment->payment_date?->format('d/m/Y') ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="grid gap-4 border-t border-slate-200 p-5 md:grid-cols-3">
                    <div class="rounded-lg bg-slate-50 p-4 text-center">
                        <p class="text-xs text-slate-500">Valor Base</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ number_format($payment->amount, 2, ',', '.') }} MT</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4 text-center">
                        <p class="text-xs text-slate-500">Desconto</p>
                        <p class="mt-1 text-xl font-semibold text-emerald-700">- {{ number_format($payment->discount, 2, ',', '.') }} MT</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4 text-center">
                        <p class="text-xs text-slate-500">Total</p>
                        <p class="mt-1 text-2xl font-bold text-sky-700">{{ number_format($payment->total_amount, 2, ',', '.') }} MT</p>
                    </div>
                </div>

                @if($payment->payment_method || $payment->notes)
                    <div class="space-y-3 border-t border-slate-200 p-5">
                        @if($payment->payment_method)
                            <p class="text-sm text-slate-700"><strong>Metodo:</strong> {{ strtoupper($payment->payment_method) }} @if($payment->transaction_id)- <code>{{ $payment->transaction_id }}</code>@endif</p>
                        @endif
                        @if($payment->notes)
                            <p class="text-sm text-slate-700"><strong>Observacoes:</strong> {{ $payment->notes }}</p>
                        @endif
                    </div>
                @endif
            </article>

            <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-5 py-4"><h3 class="text-sm font-semibold text-slate-900">Informacoes do Aluno</h3></header>
                <div class="p-5">
                    <div class="mb-4 flex items-center gap-3">
                        @if($payment->student->photo_url)
                            <img src="{{ $payment->student->photo_url }}" class="h-16 w-16 rounded-full object-cover" alt="{{ $payment->student->full_name }}">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-500">{{ strtoupper(substr($payment->student->full_name,0,1)) }}</div>
                        @endif
                        <div>
                            <p class="text-base font-semibold text-slate-900">{{ $payment->student->full_name }}</p>
                            <p class="font-mono text-xs text-slate-500">{{ $payment->student->student_number }}</p>
                        </div>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <p class="text-sm text-slate-700"><strong>Turma:</strong> {{ $payment->enrollment?->class?->name ?? 'N/A' }}</p>
                        <p class="text-sm text-slate-700"><strong>Encarregado:</strong> {{ $payment->student->parent?->first_name ?? 'N/A' }} {{ $payment->student->parent?->last_name ?? '' }}</p>
                        <p class="text-sm text-slate-700"><strong>Contacto:</strong> {{ $payment->student->parent?->phone ?? $payment->student->emergency_phone ?? 'N/A' }}</p>
                        <p class="text-sm text-slate-700"><strong>Mensalidade Base:</strong> {{ number_format($payment->enrollment?->monthly_fee ?? $payment->student->monthly_fee, 2, ',', '.') }} MT</p>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('students.show', $payment->student) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-user me-2"></i>Perfil Completo</a>
                        <a href="{{ route('students.payments', $payment->student) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-xs text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-clock-rotate-left me-2"></i>Historico</a>
                    </div>
                </div>
            </article>
        </section>

        <aside class="xl:col-span-4 space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-5 py-4"><h3 class="text-sm font-semibold text-slate-900">Acoes</h3></header>
                <div class="space-y-2 p-5">
                    @if($payment->status == 'pending' || $payment->status == 'overdue')
                        @can('process_payments')
                            <button type="button" @click="processOpen = true" class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"><i class="fas fa-circle-check me-2"></i>Processar Pagamento</button>
                        @endcan
                        <button type="button" @click="cancelOpen = true" class="inline-flex w-full items-center justify-center rounded-lg border border-rose-300 px-4 py-2 text-sm text-rose-700 hover:bg-rose-50"><i class="fas fa-ban me-2"></i>Cancelar Pagamento</button>
                    @endif
                    <a href="{{ route('payments.download-reference', $payment) }}" target="_blank" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-center text-sm text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-print me-2"></i>Imprimir Referencia</a>
                    <a href="{{ route('payments.index') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-center text-sm text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-5 py-4"><h3 class="text-sm font-semibold text-slate-900">Historico</h3></header>
                <div class="space-y-3 p-5">
                    <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-700">
                        <p class="font-semibold">Pagamento Criado</p>
                        <p class="text-xs text-slate-500">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($payment->status == 'paid')
                        <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">
                            <p class="font-semibold">Pagamento Confirmado</p>
                            <p class="text-xs">{{ $payment->payment_date?->format('d/m/Y') ?? $payment->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @elseif($payment->status == 'cancelled')
                        <div class="rounded-lg bg-slate-100 p-3 text-sm text-slate-800">
                            <p class="font-semibold">Pagamento Cancelado</p>
                            <p class="text-xs">{{ $payment->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </section>
        </aside>

        <div x-show="processOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display:none;">
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl" @click.outside="processOpen = false">
                <h4 class="text-base font-semibold text-slate-900">Processar Pagamento</h4>
                <div class="mt-3 rounded-lg bg-sky-50 p-3 text-sm text-sky-800">Valor a receber: <strong>{{ number_format($payment->total_amount, 2, ',', '.') }} MT</strong></div>
                <form action="{{ route('payments.process', $payment) }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Metodo *</label>
                        <select name="payment_method" class="w-full rounded-lg border-slate-300 text-sm" required>
                            <option value="">Selecione...</option>
                            <option value="cash">Dinheiro</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="bank">Transferencia Bancaria</option>
                            <option value="multicaixa">Multicaixa</option>
                        </select>
                    </div>
                    <div><label class="mb-1 block text-xs font-semibold text-slate-600">ID da Transacao</label><input type="text" name="transaction_id" class="w-full rounded-lg border-slate-300 text-sm"></div>
                    <div><label class="mb-1 block text-xs font-semibold text-slate-600">Data *</label><input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm" required></div>
                    <div><label class="mb-1 block text-xs font-semibold text-slate-600">Observacoes</label><textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 text-sm"></textarea></div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="processOpen = false" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="cancelOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display:none;">
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl" @click.outside="cancelOpen = false">
                <h4 class="text-base font-semibold text-slate-900">Cancelar Pagamento</h4>
                <div class="mt-3 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">Esta acao nao pode ser desfeita.</div>
                <form action="{{ route('payments.cancel', $payment) }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Motivo *</label>
                        <textarea name="reason" rows="3" required class="w-full rounded-lg border-slate-300 text-sm" placeholder="Informe o motivo..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="cancelOpen = false" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700">Voltar</button>
                        <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white">Confirmar Cancelamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        [data-bs-theme="dark"] .payments-show .bg-white { background-color: var(--card-bg) !important; }
        [data-bs-theme="dark"] .payments-show .bg-slate-50,
        [data-bs-theme="dark"] .payments-show .bg-slate-100 { background-color: rgba(148, 163, 184, 0.08) !important; }
        [data-bs-theme="dark"] .payments-show .border-slate-200,
        [data-bs-theme="dark"] .payments-show .border-slate-300 { border-color: var(--border-color) !important; }
        [data-bs-theme="dark"] .payments-show .text-slate-900,
        [data-bs-theme="dark"] .payments-show .text-slate-800,
        [data-bs-theme="dark"] .payments-show .text-slate-700,
        [data-bs-theme="dark"] .payments-show .text-slate-600,
        [data-bs-theme="dark"] .payments-show .text-slate-500 { color: var(--text-secondary) !important; }
    </style>
@endpush
