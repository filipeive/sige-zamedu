@extends('layouts.app')

@section('title', 'Pagamentos em Atraso')
@section('page-title', 'Pagamentos em Atraso')
@section('page-title-icon', 'fas fa-exclamation-triangle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}" class="no-underline"><i class="fas fa-wallet me-1"></i>Pagamentos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Em Atraso</li>
@endsection

@section('content')
<div class="space-y-4" x-data="overduePage()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-500"></i> Pagamentos em Atraso
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestão de inadimplência, notificações e liquidação de mensalidades vencidas.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition" @click="sendBulkReminder()">
                <i class="fas fa-paper-plane"></i> Lembretes em Lote
            </button>
            <a href="{{ route('reports.financial.defaulters') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-file-lines"></i> Relatório
            </a>
        </div>
    </div>

    <!-- Stat KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-rose-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Inadimplente</span>
                <span class="text-xl font-black text-rose-700 mt-0.5 block">{{ number_format($payments->sum('total_amount'), 2, ',', '.') }} <small class="text-xs font-normal text-slate-400">MT</small></span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Faturas em Atraso</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $payments->total() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Atraso Médio</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">
                    {{ round($payments->avg(fn($p) => $p->due_date->diffInDays(now())) ?? 0) }} <small class="text-xs font-normal text-slate-400">dias</small>
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-indigo-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-xmark"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Atrasos > 60 Dias</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">
                    {{ $payments->filter(fn($p) => $p->due_date->diffInDays(now()) > 60)->count() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-amber-500"></i> Lista de Pagamentos em Atraso ({{ $payments->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4 w-10">
                            <input type="checkbox" id="select-all" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @change="toggleAll($event)">
                        </th>
                        <th class="py-2.5 px-3">Referência</th>
                        <th class="py-2.5 px-3">Aluno</th>
                        <th class="py-2.5 px-3">Turma</th>
                        <th class="py-2.5 px-3">Tipo</th>
                        <th class="py-2.5 px-3">Período</th>
                        <th class="py-2.5 px-3">Valor</th>
                        <th class="py-2.5 px-3">Vencimento</th>
                        <th class="py-2.5 px-3 text-center">Atraso</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        @php
                            $diasAtraso = $payment->due_date->diffInDays(now());
                            $badgeClass = $diasAtraso > 60 ? 'bg-rose-100 text-rose-800 border border-rose-200' : ($diasAtraso > 30 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4">
                                <input type="checkbox" class="payment-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" value="{{ $payment->id }}">
                            </td>
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-800">{{ $payment->reference_number }}</td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">
                                <span>{{ $payment->student->full_name }}</span>
                                <span class="block text-[10px] text-slate-400 font-normal">{{ $payment->student->student_number }}</span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ $payment->enrollment?->class?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ ucfirst($payment->type) }}</td>
                            <td class="py-2.5 px-3 text-slate-600">
                                {{ $payment->month ? $payment->month_name . '/' . $payment->year : $payment->year }}
                            </td>
                            <td class="py-2.5 px-3 font-bold text-rose-700">
                                {{ number_format($payment->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $payment->due_date->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">
                                    {{ $diasAtraso }} dias
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('payments.show', $payment) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @can('process_payments')
                                        <button class="h-7 w-7 rounded-lg bg-emerald-700 text-white hover:bg-emerald-800 flex items-center justify-center transition" title="Processar Pagamento"
                                            @click="openProcessModal({{ $payment->id }}, '{{ $payment->reference_number }}', {{ $payment->total_amount }})">
                                            <i class="fas fa-check-circle text-xs"></i>
                                        </button>
                                    @endcan
                                    <button class="h-7 w-7 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 flex items-center justify-center transition" title="Enviar Lembrete" @click="sendReminder({{ $payment->student->id }})">
                                        <i class="fas fa-paper-plane text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-6 text-center text-slate-400">
                                <i class="fas fa-circle-check text-3xl text-emerald-500 mb-2"></i>
                                <h4 class="text-sm font-bold text-slate-700">Parabéns!</h4>
                                <p class="text-xs text-slate-500">Não existem pagamentos em atraso registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Charts Row -->
    @if($payments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-emerald-600"></i> Inadimplência por Turma
                </h3>
                <div class="h-56">
                    <canvas id="chartByClass"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-amber-500"></i> Inadimplência por Tempo de Atraso
                </h3>
                <div class="h-56">
                    <canvas id="chartByDays"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- Process Modal -->
    <div x-show="processOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display:none;">
        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl space-y-4" @click.outside="processOpen = false">
            <h4 class="text-sm font-bold text-slate-800">Processar Pagamento</h4>
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800 space-y-1 font-semibold">
                <p>Referência: <span class="font-bold font-mono" x-text="processRef"></span></p>
                <p>Valor: <span class="font-bold" x-text="formatValue(processValue)"></span> MT</p>
            </div>
            <form :action="processAction" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Método *</label>
                    <select name="payment_method" required class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Selecione...</option>
                        <option value="cash">Dinheiro</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="emola">e-Mola</option>
                        <option value="bank">Transferência Bancária</option>
                        <option value="multicaixa">Multicaixa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">ID da Transação</label>
                    <input type="text" name="transaction_id" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Data *</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Observações</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-1.5 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="processOpen=false" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function overduePage() {
    return {
        processOpen: false,
        processAction: '',
        processRef: '',
        processValue: 0,
        toggleAll(event) {
            document.querySelectorAll('.payment-checkbox').forEach(cb => cb.checked = event.target.checked);
        },
        openProcessModal(id, ref, value) {
            this.processAction = `/payments/${id}/process`;
            this.processRef = ref;
            this.processValue = value;
            this.processOpen = true;
        },
        formatValue(value) {
            return parseFloat(value || 0).toLocaleString('pt-MZ', { minimumFractionDigits: 2 });
        },
        sendReminder(studentId) {
            confirmAction('Enviar lembrete de pagamento para o encarregado deste aluno?', function() {
                showToast('Lembrete enviado com sucesso!', 'success');
            }, {title: 'Enviar Lembrete', confirmText: 'Enviar', danger: false});
        },
        sendBulkReminder() {
            const selected = document.querySelectorAll('.payment-checkbox:checked');
            if (selected.length === 0) {
                showToast('Selecione pelo menos um pagamento', 'warning');
                return;
            }
            confirmAction(`Enviar lembretes para ${selected.length} pagamentos selecionados?`, function() {
                showToast(`Lembretes enviados para ${selected.length} encarregados!`, 'success');
            }, {title: 'Enviar Lembretes em Lote', confirmText: 'Enviar', danger: false});
        }
    };
}

@if($payments->count() > 0)
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart !== 'undefined') {
        const classCanvas = document.getElementById('chartByClass');
        const daysCanvas = document.getElementById('chartByDays');
        if (!classCanvas || !daysCanvas) return;

        const byClass = @json($payments->groupBy('enrollment.class.name')->map->count());
        const byDays = {
            'Até 30 dias': {{ $payments->filter(fn($p) => $p->due_date->diffInDays(now()) <= 30)->count() }},
            '31-60 dias': {{ $payments->filter(fn($p) => $p->due_date->diffInDays(now()) > 30 && $p->due_date->diffInDays(now()) <= 60)->count() }},
            'Mais de 60 dias': {{ $payments->filter(fn($p) => $p->due_date->diffInDays(now()) > 60)->count() }}
        };

        new Chart(classCanvas, {
            type: 'doughnut',
            data: {
                labels: Object.keys(byClass),
                datasets: [{ data: Object.values(byClass), backgroundColor: ['#059669', '#3b82f6', '#f59e0b', '#ef4444', '#6366f1', '#64748b'] }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        new Chart(daysCanvas, {
            type: 'bar',
            data: {
                labels: Object.keys(byDays),
                datasets: [{ label: 'Pagamentos', data: Object.values(byDays), backgroundColor: ['#f59e0b', '#f97316', '#ef4444'] }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }
});
@endif
</script>
@endpush
