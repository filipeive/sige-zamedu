@extends('layouts.app')

@section('title', 'Referencias de Pagamento')
@section('page-title', 'Referencias de Pagamento')
@section('page-title-icon', 'fas fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item">Parent-portal</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Referencias de Pagamento</li>
@endsection

@section('page-title')
    Referencias: {{ $student->first_name }}
@endsection

@section('content')
    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-5 py-4">
            <h3 class="text-sm font-semibold text-slate-900">Referencias Pendentes</h3>
        </header>
        <div class="overflow-x-auto p-5">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Referencia</th>
                        <th class="px-3 py-2 text-left font-semibold">Descricao</th>
                        <th class="px-3 py-2 text-left font-semibold">Valor</th>
                        <th class="px-3 py-2 text-left font-semibold">Vencimento</th>
                        <th class="px-3 py-2 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($references as $reference)
                        <tr>
                            <td class="px-3 py-2 font-mono text-xs font-semibold text-slate-800">{{ $reference->reference_number }}</td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ ucfirst($reference->type) }}
                                @if ($reference->month)
                                    - {{ $reference->month_name }}/{{ $reference->year }}
                                @endif
                            </td>
                            <td class="px-3 py-2 font-semibold text-slate-900">{{ number_format($reference->amount, 2, ',', '.') }} MT</td>
                            <td class="px-3 py-2 text-slate-700">{{ $reference->due_date->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                @php
                                    $statusClass = $reference->status === 'paid'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : ($reference->status === 'overdue' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700');
                                @endphp
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($reference->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500">Nenhuma referencia encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                <a href="{{ route('parent.student-payments', $student) }}"
                    class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    Voltar aos pagamentos
                </a>
            </div>
        </div>
    </section>
@endsection
