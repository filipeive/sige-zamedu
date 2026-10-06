@extends('layouts.app')

@section('title', 'Parent-portal - Student-payments')
@section('page-title', 'Parent-portal - Student-payments')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item">Parent-portal</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Parent</li>
@endsection

@section('title', 'Pagamentos: ' . $student->first_name)
@section('page-title', 'Pagamentos: ' . $student->first_name)

@section('content')
    <div x-data="studentPayments()" class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total em Divida</p>
                    <p class="mt-1 text-3xl font-bold {{ $totalDebt > 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ number_format($totalDebt, 2, ',', '.') }} MT</p>
                </div>
                <button type="button" @click="showGenerate = true"
                    class="inline-flex items-center rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">
                    Gerar nova referencia
                </button>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Historico de Pagamentos</h3>
            </header>
            <div class="p-5">

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Referencia</th>
                                <th class="px-3 py-2 text-left font-semibold">Descricao</th>
                                <th class="px-3 py-2 text-left font-semibold">Vencimento</th>
                                <th class="px-3 py-2 text-left font-semibold">Valor</th>
                                <th class="px-3 py-2 text-left font-semibold">Status</th>
                                <th class="px-3 py-2 text-right font-semibold">Acoes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="px-3 py-2 font-mono text-xs text-slate-700">{{ $payment->reference_number }}</td>
                                    <td class="px-3 py-2 text-slate-700">
                                        {{ ucfirst($payment->type) }}
                                        @if ($payment->month)
                                            - {{ $payment->month_name }}/{{ $payment->year }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 {{ $payment->due_date && $payment->due_date < now() && $payment->status == 'pending' ? 'font-semibold text-rose-700' : 'text-slate-700' }}">
                                        {{ $payment->due_date?->format('d/m/Y') ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-2 font-semibold text-slate-900">{{ number_format($payment->total_amount, 2, ',', '.') }} MT</td>
                                    <td class="px-3 py-2">
                                        @php
                                            $statusClass = $payment->status === 'paid'
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : ($payment->status === 'overdue' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700');
                                        @endphp
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        @if ($payment->status == 'pending' || $payment->status == 'overdue')
                                            <button type="button"
                                                class="rounded-lg bg-sky-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-800"
                                                @click="openPayment({{ $payment->id }}, '{{ $payment->reference_number }}', {{ $payment->total_amount }})">
                                                Pagar
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-500">Pago</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500">Nenhum pagamento encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $payments->links() }}</div>
            </div>
        </section>

        <div x-show="showGenerate" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display: none;">
            <div class="w-full max-w-lg rounded-xl bg-white p-5 shadow-xl" @click.outside="showGenerate = false">
                <h4 class="text-base font-semibold text-slate-900">Gerar Nova Referencia</h4>
                <form action="{{ route('parent.generate-payment-reference') }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div>
                        <label for="type" class="mb-1 block text-xs font-semibold text-slate-600">Tipo de Pagamento</label>
                        <select name="type" id="type" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="mensalidade">Mensalidade</option>
                            <option value="material">Material Escolar</option>
                            <option value="uniforme">Uniforme</option>
                            <option value="outro">Outros</option>
                        </select>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label for="month" class="mb-1 block text-xs font-semibold text-slate-600">Mes (Opcional)</label>
                            <select name="month" id="month" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Selecione...</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="year" class="mb-1 block text-xs font-semibold text-slate-600">Ano</label>
                            <input type="number" name="year" id="year" value="{{ current_school_year() }}"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showGenerate = false" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">Gerar referencia</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showPayment" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display: none;">
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl" @click.outside="showPayment = false">
                <h4 class="text-base font-semibold text-slate-900">Pagamento Online</h4>
                <p class="mt-1 text-sm text-slate-600">Referencia <strong x-text="reference"></strong> no valor de <strong x-text="formattedAmount"></strong>.</p>

                <div class="mt-4 space-y-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Metodo</label>
                        <select x-model="provider" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="mkesh">mKesh</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Telefone</label>
                        <input x-model="phone" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="841234567">
                    </div>
                    <p x-show="message" :class="messageClass" class="rounded-lg px-3 py-2 text-sm" x-text="message"></p>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="showPayment = false" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancelar</button>
                    <button type="button" @click="processPayment()" :disabled="loading"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">
                        <span x-show="!loading">Confirmar</span>
                        <span x-show="loading">Processando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function studentPayments() {
            return {
                showGenerate: false,
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
                        this.message = 'Por favor, insira o numero de telefone.';
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
