@extends('layouts.app')

@section('title', 'Referencias de Pagamento')
@section('page-title', 'Gerar Referencias')
@section('page-title-icon', 'fas fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('payments.index') }}" class="no-underline"><i class="fas fa-wallet me-1"></i>Pagamentos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Referencias</li>
@endsection

@section('content')
    <div class="payments-references grid gap-6 xl:grid-cols-12">
        <aside class="xl:col-span-4 space-y-6">
            <section class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-emerald-900">Gerar Propinas do Mês (Secretaria)</h3>
                <p class="mb-3 text-xs text-emerald-700">Fluxo recomendado: gerar no dia <strong>20</strong> e vencimento automático no dia <strong>5</strong> do mês seguinte.</p>
                <form action="{{ route('payments.generate-monthly-fees') }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-emerald-800">Mês de Referência</label>
                            <select name="month" class="w-full rounded-lg border-emerald-300 bg-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach(['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $mes)
                                    <option value="{{ $i + 1 }}" {{ date('n') == $i + 1 ? 'selected' : '' }}>{{ $mes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-emerald-800">Ano Letivo</label>
                            <select name="school_year" class="w-full rounded-lg border-emerald-300 bg-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                @for($y = current_school_year() - 1; $y <= current_school_year() + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == current_school_year() ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="calendar_year" value="{{ date('Y') }}">
                    <label class="inline-flex items-center gap-2 text-xs text-emerald-800">
                        <input type="hidden" name="notify_parents" value="0">
                        <input type="checkbox" name="notify_parents" value="1" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500" checked>
                        Notificar encarregados automaticamente
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                        <i class="fas fa-bolt me-2"></i>Gerar Propinas Agora
                    </button>
                </form>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold text-slate-900">Gerar Nova Referencia</h3>
                <form action="{{ route('payments.generate-reference') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Aluno *</label>
                        <select name="student_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="">Selecione o aluno...</option>
                            @foreach(\App\Models\Student::active()->with('currentEnrollment.class')->whereHas('currentEnrollment')->orderBy('first_name')->get() as $student)
                                <option value="{{ $student->id }}">{{ $student->student_number }} - {{ $student->full_name }} ({{ $student->currentEnrollment?->class?->name ?? 'Sem turma' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Tipo *</label>
                        <select name="type" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="mensalidade">Mensalidade</option>
                            <option value="matricula">Taxa de Matricula</option>
                            <option value="material">Material Escolar</option>
                            <option value="uniforme">Uniforme</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Mes</label>
                            <select name="month" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach(['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $mes)
                                    <option value="{{ $i + 1 }}" {{ date('n') == $i + 1 ? 'selected' : '' }}>{{ $mes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Ano *</label>
                            <select name="year" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                                @for($y = current_school_year() - 1; $y <= current_school_year() + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == current_school_year() ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800"><i class="fas fa-barcode me-2"></i>Gerar Referencia</button>
                </form>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-2 text-sm font-semibold text-slate-900">Geracao em Massa</h3>
                <p class="mb-4 text-xs text-slate-500">Gera referencias de mensalidade para uma turma inteira.</p>
                <form action="{{ route('payments.generate-reference') }}" method="POST" id="bulk-form" class="space-y-3">
                    @csrf
                    <input type="hidden" name="bulk" value="1">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Turma</label>
                        <select name="class_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <option value="">Selecione a turma...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Mes</label>
                            <select name="bulk_month" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach(['Janeiro','Fevereiro','Marco','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'] as $i => $mes)
                                    <option value="{{ $i + 1 }}" {{ date('n') == $i + 1 ? 'selected' : '' }}>{{ $mes }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Ano</label>
                            <select name="bulk_year" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                                @for($y = current_school_year() - 1; $y <= current_school_year() + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == current_school_year() ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-users me-2"></i>Gerar para Turma</button>
                </form>
            </section>
        </aside>

        <section class="xl:col-span-8 space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" class="grid gap-3 md:grid-cols-2 md:items-end">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Filtrar por Turma</label>
                        <select name="class_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" onchange="this.form.submit()">
                            <option value="">Todas as turmas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:text-right">
                        <button type="button" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 no-underline hover:bg-slate-50" onclick="printSelected()"><i class="fas fa-print me-2"></i>Imprimir Selecionados</button>
                    </div>
                </form>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Referencias Pendentes</h3>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $references->total() }} registros</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left"><input type="checkbox" id="select-all" class="rounded border-slate-300"></th>
                                <th class="px-3 py-2 text-left font-semibold">Referencia</th>
                                <th class="px-3 py-2 text-left font-semibold">Aluno</th>
                                <th class="px-3 py-2 text-left font-semibold">Turma</th>
                                <th class="px-3 py-2 text-left font-semibold">Tipo</th>
                                <th class="px-3 py-2 text-left font-semibold">Periodo</th>
                                <th class="px-3 py-2 text-right font-semibold">Valor</th>
                                <th class="px-3 py-2 text-left font-semibold">Vencimento</th>
                                <th class="px-3 py-2 text-center font-semibold">Acoes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($references as $ref)
                                <tr>
                                    <td class="px-3 py-2"><input type="checkbox" class="ref-checkbox rounded border-slate-300" value="{{ $ref->id }}"></td>
                                    <td class="px-3 py-2 font-mono text-xs font-semibold text-slate-800">{{ $ref->reference_number }}</td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm font-semibold text-slate-900">{{ $ref->student->full_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $ref->student->student_number }}</p>
                                    </td>
                                    <td class="px-3 py-2 text-slate-700">{{ $ref->enrollment?->class?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $ref->type == 'mensalidade' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">{{ ucfirst($ref->type) }}</span></td>
                                    <td class="px-3 py-2 text-slate-700">@if($ref->month){{ $ref->month_name }}/{{ $ref->year }}@else{{ $ref->year }}@endif</td>
                                    <td class="px-3 py-2 text-right font-semibold text-slate-900">{{ number_format($ref->total_amount, 2, ',', '.') }} MT</td>
                                    <td class="px-3 py-2 {{ $ref->due_date < now() ? 'font-semibold text-rose-700' : 'text-slate-700' }}">{{ $ref->due_date->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <a href="{{ route('payments.show', $ref) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-eye me-1"></i>Ver</a>
                                        <a href="{{ route('payments.download-reference', $ref) }}" target="_blank" class="ml-1 inline-flex items-center rounded-lg border border-slate-300 px-2 py-1 text-xs text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-print me-1"></i>Imprimir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-8 text-center text-sm text-slate-500">Nenhuma referencia pendente encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($references->hasPages())
                    <div class="border-t border-slate-200 px-5 py-4">{{ $references->links() }}</div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('styles')
<style>
    [data-bs-theme="dark"] .payments-references .bg-white { background-color: var(--card-bg) !important; }
    [data-bs-theme="dark"] .payments-references .bg-slate-50 { background-color: rgba(148, 163, 184, 0.08) !important; }
    [data-bs-theme="dark"] .payments-references .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.14) !important; }
    [data-bs-theme="dark"] .payments-references .border-slate-100,
    [data-bs-theme="dark"] .payments-references .border-slate-200,
    [data-bs-theme="dark"] .payments-references .border-slate-300 { border-color: var(--border-color) !important; }
    [data-bs-theme="dark"] .payments-references .border-emerald-200,
    [data-bs-theme="dark"] .payments-references .border-emerald-300 { border-color: rgba(16, 185, 129, 0.35) !important; }
    [data-bs-theme="dark"] .payments-references .text-slate-900,
    [data-bs-theme="dark"] .payments-references .text-slate-800,
    [data-bs-theme="dark"] .payments-references .text-slate-700,
    [data-bs-theme="dark"] .payments-references .text-slate-600,
    [data-bs-theme="dark"] .payments-references .text-slate-500 { color: var(--text-secondary) !important; }
    [data-bs-theme="dark"] .payments-references .text-emerald-900,
    [data-bs-theme="dark"] .payments-references .text-emerald-800,
    [data-bs-theme="dark"] .payments-references .text-emerald-700 { color: #86efac !important; }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('select-all')?.addEventListener('change', function() {
        document.querySelectorAll('.ref-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function printSelected() {
        const selected = Array.from(document.querySelectorAll('.ref-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            window.VisionariosSchool?.showToast?.('Selecione pelo menos uma referencia', 'warning');
            return;
        }
        window.open(`/payments/print-bulk?ids=${selected.join(',')}`, '_blank');
    }
</script>
@endpush
