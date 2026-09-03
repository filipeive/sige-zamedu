@extends('layouts.app')

@section('title', (is_public_school() ? 'Taxas & Recibos — ' : 'Pagamentos — ') . $student->full_name)
@section('page-title', is_public_school() ? 'Gestão de Taxas & Recibos' : 'Histórico Financeiro do Aluno')
@section('page-title-icon', 'fas fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}" class="no-underline">Alunos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}" class="no-underline">{{ $student->first_name }}</a></li>
    <li class="breadcrumb-item active">{{ is_public_school() ? 'Taxas & Recibos' : 'Pagamentos' }}</li>
@endsection

@section('content')
<div class="space-y-6 bg-[#F8FAFC] -m-4 p-6 rounded-3xl min-h-screen">

    <!-- 1. Header Banner MOPHY Style -->
    <div class="rounded-3xl bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 p-6 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        
        <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.2) 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="flex items-center gap-4 relative z-10">
            @if ($student->photo_url)
                <img src="{{ $student->photo_url }}" alt="{{ $student->first_name }}" class="h-16 w-16 rounded-2xl object-cover ring-4 ring-white/20 shadow-md">
            @else
                <div class="h-16 w-16 rounded-2xl bg-emerald-700/80 border border-white/20 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                    <i class="fas fa-user-graduate"></i>
                </div>
            @endif
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 backdrop-blur-md px-3 py-0.5 text-xs font-bold text-amber-300 border border-white/20">
                        <i class="fas fa-id-badge"></i> Nº {{ $student->student_number }}
                    </span>
                    @if(is_public_school())
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-500/30 backdrop-blur-md px-3 py-0.5 text-xs font-bold text-blue-200 border border-blue-400/30">
                            <i class="fas fa-landmark"></i> Escola Pública
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/30 backdrop-blur-md px-3 py-0.5 text-xs font-bold text-emerald-200 border border-emerald-400/30">
                            <i class="fas fa-school"></i> Escola Privada
                        </span>
                    @endif
                </div>
                <h2 class="text-2xl font-black font-heading text-white tracking-tight">{{ $student->full_name }}</h2>
                <p class="text-xs text-emerald-100 mt-0.5 flex flex-wrap items-center gap-3">
                    <span><i class="fas fa-layer-group me-1 text-amber-300"></i>Turma: <strong>{{ $student->enrollments->where('status', 'active')->first()->class->name ?? 'Sem Turma' }}</strong></span>
                    @if(is_private_school())
                        <span><i class="fas fa-wallet me-1 text-amber-300"></i>Mensalidade: <strong>{{ number_format($student->monthly_fee, 2, ',', '.') }} MT</strong></span>
                    @endif
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 relative z-10 w-full md:w-auto">
            <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-2 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-md px-4 py-2.5 text-xs font-bold text-white border border-white/20 transition shadow-sm">
                <i class="fas fa-arrow-left"></i> Perfil do Aluno
            </a>
            <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-amber-400 text-gray-900 hover:bg-amber-300 px-5 py-2.5 text-xs font-black shadow-md transition transform hover:-translate-y-0.5" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="fas fa-plus-circle text-emerald-900"></i> {{ is_public_school() ? 'Registrar Taxa' : 'Novo Pagamento' }}
            </button>
        </div>
    </div>

    <!-- 2. Stat Summary Cards Grid -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1: Total Pago -->
        <div class="rounded-2xl bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.03)] border border-slate-100 flex items-center gap-4 hover:shadow-md transition">
            <div class="rounded-2xl bg-emerald-50 p-3.5 text-emerald-600 text-xl shrink-0">
                <i class="fas fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ is_public_school() ? 'Taxas Pagas' : 'Total Recebido' }}</span>
                <p class="text-xl font-black text-slate-800 mt-0.5">{{ number_format($paymentStats['total_paid'], 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span></p>
            </div>
        </div>

        <!-- Card 2: Pendente -->
        <div class="rounded-2xl bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.03)] border border-slate-100 flex items-center gap-4 hover:shadow-md transition">
            <div class="rounded-2xl bg-amber-50 p-3.5 text-amber-600 text-xl shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pendente</span>
                <p class="text-xl font-black text-slate-800 mt-0.5">{{ number_format($paymentStats['total_pending'], 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span></p>
            </div>
        </div>

        <!-- Card 3: Em Atraso -->
        <div class="rounded-2xl bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.03)] border border-slate-100 flex items-center gap-4 hover:shadow-md transition">
            <div class="rounded-2xl bg-rose-50 p-3.5 text-rose-600 text-xl shrink-0">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Em Atraso</span>
                <p class="text-xl font-black text-rose-700 mt-0.5">{{ number_format($paymentStats['total_overdue'], 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span></p>
            </div>
        </div>

        <!-- Card 4: Cumprimento -->
        @php
            $total = $paymentStats['total_paid'] + $paymentStats['total_pending'] + $paymentStats['total_overdue'];
            $paidPercentage = $total > 0 ? round(($paymentStats['total_paid'] / $total) * 100) : 100;
        @endphp
        <div class="rounded-2xl bg-white p-5 shadow-[0_10px_25px_rgba(0,0,0,0.03)] border border-slate-100 flex items-center gap-4 hover:shadow-md transition">
            <div class="rounded-2xl bg-indigo-50 p-3.5 text-indigo-600 text-xl shrink-0">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="w-full min-w-0">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cumprimento</span>
                <div class="flex items-center justify-between mt-0.5">
                    <p class="text-xl font-black text-slate-800">{{ $paidPercentage }}%</p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $paidPercentage >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $paidPercentage >= 80 ? 'Em Dia' : 'Pendente' }}
                    </span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-1.5 overflow-hidden">
                    <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $paidPercentage }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar -->
    <div class="rounded-2xl bg-white p-4 shadow-sm border border-slate-100">
        <form method="GET" class="grid gap-3 sm:grid-cols-2 md:grid-cols-5 items-end">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Mês</label>
                <select name="month" class="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Meses</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Ano Lectivo</label>
                <select name="year" class="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    @for($i = current_school_year() - 2; $i <= current_school_year() + 1; $i++)
                        <option value="{{ $i }}" {{ request('year', current_school_year()) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status</label>
                <select name="status" class="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pago</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Em Atraso</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tipo de Taxa</label>
                <select name="type" class="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Tipos</option>
                    <option value="matricula" {{ request('type') == 'matricula' ? 'selected' : '' }}>Taxa de Matrícula</option>
                    @if(is_private_school())
                        <option value="mensalidade" {{ request('type') == 'mensalidade' ? 'selected' : '' }}>Mensalidade / Propina</option>
                    @endif
                    <option value="exame" {{ request('type') == 'exame' ? 'selected' : '' }}>Taxa de Exame</option>
                    <option value="certificado" {{ request('type') == 'certificado' ? 'selected' : '' }}>Certificado</option>
                    <option value="material" {{ request('type') == 'material' ? 'selected' : '' }}>Material</option>
                    <option value="uniforme" {{ request('type') == 'uniforme' ? 'selected' : '' }}>Uniforme</option>
                </select>
            </div>
            <div>
                <a href="{{ route('students.payments', $student) }}" class="w-full inline-flex justify-center items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 py-2.5 px-3 text-xs font-bold text-slate-600 transition">
                    <i class="fas fa-redo text-slate-400"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>

    <!-- 4. Content Grid: Table (Left) + Analytics & Upcoming (Right) -->
    <div class="grid gap-6 lg:grid-cols-12">

        <!-- Table Column -->
        <div class="lg:col-span-8 space-y-6">
            <div class="rounded-3xl bg-white shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-receipt text-emerald-600"></i>
                            {{ is_public_school() ? 'Histórico de Taxas Registadas' : 'Histórico de Pagamentos & Propinas' }}
                        </h3>
                        <p class="text-xs text-slate-400">Lista completa de lançamentos financeiros deste aluno</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                        {{ $student->payments->count() }} registos
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                                <th class="p-3.5 ps-5">Referência</th>
                                <th class="p-3.5">Descrição</th>
                                <th class="p-3.5">Valor</th>
                                <th class="p-3.5">Vencimento</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5 text-end pe-5">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($student->payments as $payment)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3.5 ps-5 font-mono font-bold text-slate-700">
                                        <span class="inline-block px-2 py-1 rounded-lg bg-slate-100 text-slate-800 font-mono text-[11px]">
                                            {{ $payment->reference_number }}
                                        </span>
                                    </td>
                                    <td class="p-3.5">
                                        <div class="font-bold text-slate-800">
                                            @switch($payment->type)
                                                @case('matricula') Taxa de Matrícula @break
                                                @case('mensalidade') Mensalidade @break
                                                @case('exame') Taxa de Exame @break
                                                @case('certificado') Certificado @break
                                                @case('material') Material Escolar @break
                                                @case('uniforme') Uniforme @break
                                                @default {{ ucfirst($payment->type) }}
                                            @endswitch
                                        </div>
                                        @if($payment->month)
                                            <div class="text-[11px] text-slate-400 font-medium">
                                                Mês {{ $payment->month }}/{{ $payment->year }}
                                                @if($payment->notes) · {{ Str::limit($payment->notes, 20) }} @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        <div class="font-extrabold text-slate-900">
                                            {{ number_format($payment->amount, 2, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">MT</span>
                                        </div>
                                        @if($payment->discount > 0)
                                            <div class="text-[10px] text-rose-500 font-semibold">
                                                Desc: -{{ number_format($payment->discount, 2, ',', '.') }} MT
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        <div class="font-semibold text-slate-700">
                                            {{ $payment->due_date?->format('d/m/Y') ?? '-' }}
                                        </div>
                                        @if($payment->due_date && $payment->due_date->isPast() && $payment->status !== 'paid')
                                            <span class="text-[10px] font-bold text-rose-600 flex items-center gap-1 mt-0.5">
                                                <i class="fas fa-exclamation-circle text-[9px]"></i> Vencido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        @switch($payment->status)
                                            @case('paid')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-800">
                                                    <i class="fas fa-check-circle"></i> Pago
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-800">
                                                    <i class="fas fa-clock"></i> Pendente
                                                </span>
                                                @break
                                            @case('overdue')
                                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-bold text-rose-800">
                                                    <i class="fas fa-exclamation-triangle"></i> Atrasado
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="p-3.5 text-end pe-5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition"
                                                    onclick="openPaymentDetailsModal('{{ $payment->id }}', '{{ $payment->reference_number }}', '{{ ucfirst($payment->type) }}', '{{ $payment->month ? $payment->month.'/'.$payment->year : '-' }}', '{{ number_format($payment->amount, 2, ',', '.') }}', '{{ number_format($payment->discount, 2, ',', '.') }}', '{{ $payment->due_date?->format('d/m/Y') ?? '-' }}', '{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }}', '{{ $payment->status }}', '{{ $payment->payment_method ?? '-' }}', '{{ $payment->notes ?? 'Sem observações' }}')"
                                                    title="Ver Detalhes do Recibo">
                                                <i class="fas fa-eye text-xs"></i>
                                            </button>
                                            @if($payment->status === 'paid')
                                                <button type="button" class="h-8 w-8 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 flex items-center justify-center transition"
                                                        onclick="openPaymentDetailsModal('{{ $payment->id }}', '{{ $payment->reference_number }}', '{{ ucfirst($payment->type) }}', '{{ $payment->month ? $payment->month.'/'.$payment->year : '-' }}', '{{ number_format($payment->amount, 2, ',', '.') }}', '{{ number_format($payment->discount, 2, ',', '.') }}', '{{ $payment->due_date?->format('d/m/Y') ?? '-' }}', '{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }}', '{{ $payment->status }}', '{{ $payment->payment_method ?? '-' }}', '{{ $payment->notes ?? 'Sem observações' }}')"
                                                        title="Imprimir Recibo">
                                                    <i class="fas fa-print text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mx-auto mb-3">
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-slate-600">Nenhum registo de taxa ou pagamento encontrado.</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Utilize o botão acima para registar um novo pagamento.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Right Column (Analytics & Upcoming) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Chart Card -->
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-indigo-600"></i>
                        Distribuição por Status
                    </h3>
                </div>
                <div style="height: 200px;" class="relative flex items-center justify-center">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>

            <!-- Próximos Vencimentos Card -->
            <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-amber-500"></i>
                        Taxas Pendentes
                    </h3>
                </div>

                @php
                    $pendingItems = $student->payments
                        ->filter(fn($p) => is_private_school() || $p->type !== 'mensalidade')
                        ->where('status', 'pending')
                        ->take(5);
                @endphp

                <div class="space-y-3">
                    @forelse($pendingItems as $pItem)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                            <div>
                                <p class="text-xs font-bold text-slate-800">
                                    @switch($pItem->type)
                                        @case('matricula') Taxa de Matrícula @break
                                        @case('exame') Taxa de Exame @break
                                        @case('certificado') Certificado @break
                                        @default {{ ucfirst($pItem->type) }}
                                    @endswitch
                                </p>
                                <p class="text-[10px] text-slate-400">Vencimento: {{ $pItem->due_date?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div class="text-end">
                                <span class="text-xs font-extrabold text-slate-900 block">{{ number_format($pItem->amount, 2, ',', '.') }} MT</span>
                                <span class="inline-block px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Pendente</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-2 text-sm">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">Tudo em Dia!</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Nenhuma taxa pendente registada.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL NOVO PAGAMENTO ================= -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-emerald-900 to-teal-800 text-white p-5 border-0">
                <h5 class="modal-title font-extrabold text-base flex items-center gap-2">
                    <i class="fas fa-plus-circle text-amber-400"></i>
                    {{ is_public_school() ? 'Registrar Nova Taxa Administrativa' : 'Registrar Novo Pagamento' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div class="modal-body p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tipo de Taxa / Pagamento *</label>
                            <select name="type" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" required>
                                <option value="matricula">Taxa de Matrícula</option>
                                <option value="exame">Taxa de Exame</option>
                                <option value="certificado">Emissão de Certificado / Declaração</option>
                                @if(is_private_school())
                                    <option value="mensalidade">Mensalidade / Propina</option>
                                @endif
                                <option value="material">Material Escolar</option>
                                <option value="uniforme">Uniforme</option>
                                <option value="outro">Outra Taxa</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Valor (MT) *</label>
                            <input type="number" step="0.01" name="amount" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ex.: 500.00" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Mês de Referência</label>
                            <select name="month" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Não aplicável</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Ano Lectivo *</label>
                            <input type="number" name="year" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" value="{{ current_school_year() }}" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Data de Vencimento *</label>
                            <input type="date" name="due_date" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Método de Pagamento</label>
                            <select name="payment_method" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="cash">Numerário / Caixas Escolares</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="emola">e-Mola</option>
                                <option value="bank">Transferência Bancária</option>
                                <option value="pos">POS / TPA</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Observações</label>
                        <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-300 text-xs font-medium focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ex.: Recibo de emissão de certificado do 2º trimestre..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50 p-4 border-t border-slate-100 flex justify-between">
                    <button type="button" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-100 transition" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-800 text-white text-xs font-black hover:bg-emerald-700 transition shadow-md flex items-center gap-2">
                        <i class="fas fa-save"></i> Guardar Registos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL VER RECIBO / DETALHES ================= -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-slate-900 text-white p-5 border-0 flex justify-between items-center">
                <h5 class="modal-title font-extrabold text-sm flex items-center gap-2">
                    <i class="fas fa-receipt text-emerald-400"></i> Comprovativo de Taxa / Recibo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-6 space-y-4">
                <div class="text-center pb-4 border-b border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Número de Referência</span>
                    <h3 class="font-mono text-xl font-black text-slate-900 mt-0.5" id="detailRef">---</h3>
                    <span id="detailStatusBadge" class="inline-block mt-2 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">---</span>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Tipo de Lançamento:</span>
                        <strong id="detailType" class="text-slate-800 font-bold">---</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Período Referência:</span>
                        <strong id="detailPeriod" class="text-slate-800 font-bold">---</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Valor Pago:</span>
                        <strong id="detailAmount" class="text-emerald-700 font-black text-sm">--- MT</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Data de Vencimento:</span>
                        <strong id="detailDueDate" class="text-slate-800">---</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Data de Liquidação:</span>
                        <strong id="detailPayDate" class="text-slate-800">---</strong>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Método de Pagamento:</span>
                        <strong id="detailMethod" class="text-slate-800">---</strong>
                    </div>
                    <div class="pt-2">
                        <span class="text-slate-400 block mb-1 font-semibold">Observações:</span>
                        <p id="detailNotes" class="bg-slate-50 p-3 rounded-xl text-slate-600 text-xs border border-slate-100">---</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-slate-50 p-4 border-t border-slate-100 flex justify-between">
                <button type="button" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-100 transition" data-bs-dismiss="modal">Fechar</button>
                <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-emerald-800 text-white text-xs font-black hover:bg-emerald-700 transition shadow-md flex items-center gap-2">
                    <i class="fas fa-print"></i> Imprimir Recibo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paidCount = {{ $paymentStats['total_paid'] }};
    const pendingCount = {{ $paymentStats['total_pending'] }};
    const overdueCount = {{ $paymentStats['total_overdue'] }};
    const totalPayments = paidCount + pendingCount + overdueCount;

    const ctx = document.getElementById('paymentStatusChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: totalPayments > 0 ? ['Pago', 'Pendente', 'Em Atraso'] : ['Sem Registos'],
                datasets: [{
                    data: totalPayments > 0 ? [paidCount, pendingCount, overdueCount] : [1],
                    backgroundColor: totalPayments > 0 ? ['#10b981', '#f59e0b', '#f43f5e'] : ['#cbd5e1'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11, weight: '600' } } }
                },
                cutout: '70%'
            }
        });
    }

    window.openPaymentDetailsModal = function(id, ref, type, period, amount, discount, dueDate, payDate, status, method, notes) {
        document.getElementById('detailRef').textContent = ref;
        document.getElementById('detailType').textContent = type;
        document.getElementById('detailPeriod').textContent = period;
        document.getElementById('detailAmount').textContent = amount + ' MT';
        document.getElementById('detailDueDate').textContent = dueDate;
        document.getElementById('detailPayDate').textContent = payDate;
        document.getElementById('detailMethod').textContent = method;
        document.getElementById('detailNotes').textContent = notes;

        const badge = document.getElementById('detailStatusBadge');
        const statusMap = {
            paid: { label: 'Pago', class: 'bg-emerald-100 text-emerald-800' },
            pending: { label: 'Pendente', class: 'bg-amber-100 text-amber-800' },
            overdue: { label: 'Em Atraso', class: 'bg-rose-100 text-rose-800' },
            cancelled: { label: 'Cancelado', class: 'bg-slate-100 text-slate-800' }
        };
        const s = statusMap[status] || { label: status, class: 'bg-slate-100 text-slate-800' };
        badge.textContent = s.label;
        badge.className = 'inline-block mt-2 px-3 py-0.5 rounded-full text-xs font-bold ' + s.class;

        new bootstrap.Modal(document.getElementById('paymentDetailModal')).show();
    };
});
</script>
@endpush