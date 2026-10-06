@extends('layouts.app')

@section('title', 'Payments - Index')
@section('page-title', 'Payments - Index')
@section('page-title-icon', 'fas fa-money-bill-wave')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Pagamentos</li>
@endsection

@section('title', is_public_school() ? 'Gestão de Taxas Escolares' : 'Gestão de Pagamentos')
@section('page-title', is_public_school() ? 'Taxas de Matrícula & Exames' : 'Pagamentos & Propinas')
@section('content')
<div class="space-y-4">

    <!-- 1. Compact Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-emerald-600"></i> {{ is_public_school() ? 'Taxas de Matrícula & Exames' : 'Pagamentos & Propinas' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestão de receitas, emissão de comprovantes e controlo de liquidação.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('create_payments')
                <a href="{{ route('payments.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> {{ is_public_school() ? 'Registrar Taxa' : 'Novo Pagamento' }}
                </a>
            @endcan
        </div>
    </div>

    @include('partials.super-admin-banner')

    <!-- 2. Border-Accent Colored Stat Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Total Recebido (Emerald Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">{{ is_public_school() ? 'Taxas Recebidas' : 'Total Recebido' }}</span>
                <span class="text-xl font-black text-emerald-700 mt-0.5">{{ number_format($stats['paid'], 2, ',', '.') }} MT</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-arrow-trend-up"></i>
            </div>
        </div>

        <!-- Pendente (Amber Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-amber-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Pendente</span>
                <span class="text-xl font-black text-slate-800 mt-0.5">{{ number_format($stats['pending'], 2, ',', '.') }} MT</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
        </div>

        <!-- Em Atraso (Rose Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-rose-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Em Atraso</span>
                <span class="text-xl font-black text-rose-700 mt-0.5">{{ number_format($stats['overdue'], 2, ',', '.') }} MT</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>

        <!-- Total Lançamentos (Blue Accent) -->
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-blue-500 shadow-2xs flex items-center justify-between hover:shadow-sm transition">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Lançamentos</span>
                <span class="text-2xl font-black text-slate-800 mt-0.5">{{ $payments->total() }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('payments.index') }}" class="grid gap-2 sm:grid-cols-2 md:grid-cols-5 items-center">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Nome, número ou referência...">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
            <div>
                <select name="school_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas as Escolas</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}" {{ request('school_id') == $sch->id ? 'selected' : '' }}>
                            {{ $sch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endhasanyrole

            <div>
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="pending" @selected(request('status') == 'pending')>Pendente</option>
                    <option value="paid" @selected(request('status') == 'paid')>Pago</option>
                    <option value="overdue" @selected(request('status') == 'overdue')>Em Atraso</option>
                </select>
            </div>

            <div>
                <select name="type" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Tipos</option>
                    <option value="matricula" @selected(request('type') == 'matricula')>Matrícula</option>
                    @if(is_private_school())
                        <option value="mensalidade" @selected(request('type') == 'mensalidade')>Mensalidade / Propina</option>
                    @endif
                    <option value="exame" @selected(request('type') == 'exame')>Exame</option>
                    <option value="certificado" @selected(request('type') == 'certificado')>Certificados</option>
                    <option value="outro" @selected(request('type') == 'outro')>Outro</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 py-1.5 px-3 text-xs font-bold text-white transition shadow-2xs">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'status', 'type', 'month', 'school_id']))
                    <a href="{{ route('payments.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-600"></i> Histórico de Pagamentos ({{ $payments->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Referência</th>
                        <th class="py-2.5 px-3">Estudante</th>
                        <th class="py-2.5 px-3">Descrição / Tipo</th>
                        <th class="py-2.5 px-3">Valor</th>
                        <th class="py-2.5 px-3">Vencimento</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-700">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 font-mono text-[11px]">
                                    {{ $payment->reference_number }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-bold text-slate-800">
                                <a href="{{ route('students.show', $payment->student_id) }}" class="no-underline font-bold text-slate-800 hover:text-emerald-700 hover:no-underline transition">
                                    {{ $payment->student->full_name ?? 'N/I' }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-700">
                                {{ ucfirst($payment->type) }}
                                @if($payment->month)
                                    <span class="text-slate-400 font-normal text-[10px]">({{ $payment->month }}/{{ $payment->year }})</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 font-extrabold text-emerald-700">
                                {{ number_format($payment->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-2.5 px-3 text-slate-700">
                                {{ $payment->due_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3">
                                @php
                                    $statusBadge = match($payment->status) {
                                        'paid' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'overdue' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusBadge }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('payments.show', $payment) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">
                                <i class="fas fa-file-invoice-dollar text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum pagamento registrado.</p>
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

</div>
@endsection
