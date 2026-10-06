@extends('layouts.app')

@section('title', 'Pagamentos com Multa')
@section('page-title', 'Pagamentos com Multa Aplicada')
@section('page-title-icon', 'fas fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}" class="no-underline"><i class="fas fa-wallet me-1"></i>Pagamentos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Com Multa</li>
@endsection

@section('content')
    <div class="payments-penalties space-y-6" x-data="penaltiesPage()">
        <section class="grid gap-4 md:grid-cols-3">
            <article class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-center"><p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Total em Multas</p><p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($totalPenalties, 2, ',', '.') }} MT</p></article>
            <article class="rounded-xl border border-rose-200 bg-rose-50 p-5 text-center"><p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Pagamentos com Multa</p><p class="mt-2 text-3xl font-bold text-rose-700">{{ $payments->total() }}</p></article>
            <article class="rounded-xl border border-sky-200 bg-sky-50 p-5 text-center"><p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Data de Consulta</p><p class="mt-2 text-xl font-bold text-sky-700">{{ now()->format('d/m/Y') }}</p></article>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Pagamentos com Multa Aplicada</h3>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $payments->total() }} registros</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Referencia</th>
                            <th class="px-3 py-2 text-left font-semibold">Aluno</th>
                            <th class="px-3 py-2 text-left font-semibold">Turma</th>
                            <th class="px-3 py-2 text-right font-semibold">Valor Original</th>
                            <th class="px-3 py-2 text-left font-semibold">Multa</th>
                            <th class="px-3 py-2 text-right font-semibold">Total</th>
                            <th class="px-3 py-2 text-center font-semibold">Dias em Atraso</th>
                            <th class="px-3 py-2 text-left font-semibold">Vencimento</th>
                            <th class="px-3 py-2 text-center font-semibold">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $payment)
                            <tr class="{{ $payment->is_blocked ? 'bg-rose-50/50' : '' }}">
                                <td class="px-3 py-2 font-mono text-xs font-semibold text-slate-800">{{ $payment->reference_number }}</td>
                                <td class="px-3 py-2"><p class="text-sm font-semibold text-slate-900">{{ $payment->student->full_name }}</p><p class="text-xs text-slate-500">{{ $payment->student->student_number }}</p></td>
                                <td class="px-3 py-2 text-slate-700">{{ $payment->enrollment?->class?->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 text-right text-slate-700">{{ number_format($payment->original_amount, 2, ',', '.') }} MT</td>
                                <td class="px-3 py-2"><span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">{{ $payment->penalty_percentage }}% ({{ number_format($payment->penalty_amount, 2, ',', '.') }} MT)</span></td>
                                <td class="px-3 py-2 text-right font-semibold text-slate-900">{{ number_format($payment->total_amount, 2, ',', '.') }} MT</td>
                                <td class="px-3 py-2 text-center">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $payment->days_late > 30 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">{{ $payment->days_late }} dias</span>
                                    @if($payment->is_blocked)
                                        <p class="mt-1 text-[10px] font-semibold text-rose-700">BLOQUEADO</p>
                                    @endif
                                </td>
                                <td class="px-3 py-2 font-semibold text-rose-700">{{ $payment->due_date->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-center">
                                    <a href="{{ route('payments.show', $payment) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-eye me-1"></i>Ver</a>
                                    @if($payment->penalty_amount > 0)
                                        <button type="button" class="ml-1 inline-flex items-center rounded-lg border border-amber-300 px-2 py-1 text-xs text-amber-700 hover:bg-amber-50" @click="openRemovePenalty({{ $payment->id }}, '{{ $payment->reference_number }}', '{{ $payment->student->full_name }}', '{{ $payment->penalty_percentage }}', '{{ number_format($payment->penalty_amount, 2, ',', '.') }}')"><i class="fas fa-eraser me-1"></i>Remover</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-3 py-8 text-center text-sm text-slate-500">Nenhum pagamento com multa encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">{{ $payments->links() }}</div>
            @endif
        </section>

        <div x-show="removeOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display:none;">
            <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl" @click.outside="removeOpen = false">
                <h4 class="text-base font-semibold text-slate-900">Remover Multa</h4>
                <div class="mt-3 rounded-lg bg-slate-50 p-3 text-sm text-slate-700" x-html="removeInfo"></div>
                <form :action="removeAction" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Motivo da Remocao *</label>
                        <textarea name="reason" rows="3" required class="w-full rounded-lg border-slate-300 text-sm" placeholder="Explique o motivo..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="removeOpen = false" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white">Remover Multa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    [data-bs-theme="dark"] .payments-penalties .bg-white { background-color: var(--card-bg) !important; }
    [data-bs-theme="dark"] .payments-penalties .bg-slate-50,
    [data-bs-theme="dark"] .payments-penalties .bg-slate-100 { background-color: rgba(148, 163, 184, 0.08) !important; }
    [data-bs-theme="dark"] .payments-penalties .border-slate-200,
    [data-bs-theme="dark"] .payments-penalties .border-slate-300 { border-color: var(--border-color) !important; }
    [data-bs-theme="dark"] .payments-penalties .text-slate-900,
    [data-bs-theme="dark"] .payments-penalties .text-slate-800,
    [data-bs-theme="dark"] .payments-penalties .text-slate-700,
    [data-bs-theme="dark"] .payments-penalties .text-slate-600,
    [data-bs-theme="dark"] .payments-penalties .text-slate-500 { color: var(--text-secondary) !important; }
</style>
@endpush

@push('scripts')
<script>
function penaltiesPage() {
    return {
        removeOpen: false,
        removeAction: '',
        removeInfo: '',
        openRemovePenalty(paymentId, reference, student, percentage, amount) {
            this.removeAction = `/payments/${paymentId}/remove-penalty`;
            this.removeInfo = `
                <strong>Referencia:</strong> ${reference}<br>
                <strong>Aluno:</strong> ${student}<br>
                <strong>Multa Atual:</strong> ${percentage}% (${amount} MT)
            `;
            this.removeOpen = true;
        }
    };
}
</script>
@endpush
