@extends('layouts.app')

@section('title', 'Propinas e Recibos')
@section('page-title', 'Extrato de Pagamentos & Propinas')
@section('page-title-icon', 'fas fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item">Parent-portal</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Extrato de Pagamentos & Propinas</li>
@endsection

@section('content')
<div x-data="parentPayments()" class="space-y-6">

    {{-- Top Banner Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-indigo-100 text-indigo-700 rounded-xl text-xl font-bold">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Propinas & Recibos</h1>
                <p class="text-xs text-slate-500 font-medium">Consulte e efetue o pagamento de propinas online via M-Pesa ou e-Mola</p>
            </div>
        </div>

        <a href="{{ route('parent.dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i> Painel Principal
        </a>
    </div>

    {{-- Tabela de Pagamentos --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list-alt text-indigo-600"></i> Histórico Geral de Cobranças
            </h3>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                Total: {{ $payments->total() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <th class="py-3.5 px-4">Referência</th>
                        <th class="py-3.5 px-4">Educando</th>
                        <th class="py-3.5 px-4">Descrição</th>
                        <th class="py-3.5 px-4">Vencimento</th>
                        <th class="py-3.5 px-4">Valor Total</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-xs font-bold text-slate-700">
                                {{ $payment->reference_number }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $payment->student->full_name }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">
                                {{ ucfirst($payment->type) }}
                                @if ($payment->month)
                                    - {{ $payment->month_name }}/{{ $payment->year }}
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium {{ $payment->due_date < now() && $payment->status == 'pending' ? 'font-bold text-rose-600' : 'text-slate-600' }}">
                                {{ $payment->due_date?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900">
                                {{ number_format($payment->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($payment->status === 'paid')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-3xs font-bold text-emerald-700">
                                        <i class="fas fa-check-circle"></i> Pago
                                    </span>
                                @elseif($payment->status === 'overdue')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 border border-rose-200 px-3 py-1 text-3xs font-bold text-rose-700">
                                        <i class="fas fa-exclamation-triangle"></i> Vencido
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-3xs font-bold text-amber-700">
                                        <i class="fas fa-clock"></i> Pendente
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                @if ($payment->status == 'pending' || $payment->status == 'overdue')
                                    <button type="button"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors"
                                        @click="openPayment({{ $payment->id }}, '{{ $payment->reference_number }}', {{ $payment->total_amount }})">
                                        <i class="fas fa-mobile-alt"></i> Pagar M-Pesa
                                    </button>
                                @else
                                    <span class="text-xs font-semibold text-slate-400">
                                        <i class="fas fa-check text-emerald-500 mr-1"></i> Liquidado
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500 font-medium">
                                Nenhum pagamento registado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Pagamento Online --}}
    <div x-show="showPayment" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display: none;">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-100" @click.outside="showPayment = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <h4 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-mobile-alt text-emerald-600"></i> Pagamento Móvel
                </h4>
                <button @click="showPayment = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <p class="text-xs text-slate-600 mb-4">
                Pagamento da referência <strong class="font-mono text-slate-800" x-text="reference"></strong> no valor de <strong class="text-emerald-700 font-black" x-text="formattedAmount"></strong>.
            </p>

            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-700">Carteira Móvel</label>
                    <select x-model="provider" class="w-full rounded-xl border-slate-300 text-xs font-bold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="mpesa">M-Pesa (Vodacom)</option>
                        <option value="emola">e-Mola (Movitel)</option>
                        <option value="mkesh">mKesh (Tmcel)</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-slate-700">Número de Telefone</label>
                    <input x-model="phone" type="text" class="w-full rounded-xl border-slate-300 text-xs font-bold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ex: 841234567">
                </div>
                <p x-show="message" :class="messageClass" class="rounded-xl px-3.5 py-2 text-xs font-bold" x-text="message"></p>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" @click="showPayment = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Cancelar</button>
                <button type="button" @click="processPayment()" :disabled="loading"
                    class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-black text-white hover:bg-emerald-700 shadow-sm disabled:opacity-60 transition-colors">
                    <span x-show="!loading"><i class="fas fa-check mr-1"></i> Confirmar Pagamento</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-1"></i> Processando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function parentPayments() {
            return {
                showPayment: false,
                paymentId: null,
                reference: '',
                amount: 0,
                provider: 'mpesa',
                phone: '',
                loading: false,
                message: '',
                messageClass: 'bg-slate-100 text-slate-700',
                get formattedAmount() {
                    return new Intl.NumberFormat('pt-MZ', {
                        style: 'currency',
                        currency: 'MZN'
                    }).format(this.amount || 0);
                },
                openPayment(id, reference, amount) {
                    this.paymentId = id;
                    this.reference = reference;
                    this.amount = amount;
                    this.phone = '';
                    this.message = '';
                    this.showPayment = true;
                },
                async processPayment() {
                    if (!this.phone) {
                        this.messageClass = 'bg-rose-100 text-rose-700';
                        this.message = 'Por favor, insira o número de telefone.';
                        return;
                    }

                    this.loading = true;
                    this.message = '';

                    try {
                        const response = await fetch(`/parent/payments/${this.paymentId}/pay-online`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                phone_number: this.phone,
                                provider: this.provider
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.messageClass = 'bg-emerald-100 text-emerald-700';
                            this.message = data.message;
                            setTimeout(() => window.location.reload(), 2500);
                        } else {
                            this.messageClass = 'bg-rose-100 text-rose-700';
                            this.message = data.message || 'Erro ao processar pagamento.';
                        }
                    } catch (error) {
                        this.messageClass = 'bg-rose-100 text-rose-700';
                        this.message = 'Erro ao processar pagamento. Tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endpush
