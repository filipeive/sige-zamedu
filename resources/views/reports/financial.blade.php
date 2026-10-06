@extends('layouts.app')

@section('title', 'Relatórios Financeiros')
@section('page-title', 'Visão Geral Financeira')
@section('page-title-icon', 'fas fa-wallet')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Financeiro</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.payments') }}" class="btn text-white font-bold rounded-xl shadow-xs" style="background: var(--primary);">
            <i class="fas fa-file-excel me-1"></i> Exportar CSV
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary rounded-xl">
            <i class="fas fa-print me-1"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <!-- Stat KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-[var(--card-bg)] p-3 border-start border-4 border-success rounded-xl">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success-subtle text-success rounded-circle p-3 me-3">
                            <i class="fas fa-arrow-trend-up fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase font-weight-bold">Receitas Totais</div>
                            <h4 class="mb-0 text-success font-weight-bold">{{ number_format($totalPaidAmount, 2, ',', '.') }} <small class="text-xs text-muted">MT</small></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-[var(--card-bg)] p-3 border-start border-4 border-danger rounded-xl">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger-subtle text-danger rounded-circle p-3 me-3">
                            <i class="fas fa-arrow-trend-down fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase font-weight-bold">Despesas Totais</div>
                            <h4 class="mb-0 text-danger font-weight-bold">{{ number_format($totalExpenses, 2, ',', '.') }} <small class="text-xs text-muted">MT</small></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-[var(--card-bg)] p-3 border-start border-4 rounded-xl {{ $netResult >= 0 ? 'border-primary' : 'border-danger' }}">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-circle p-3 me-3" style="background: var(--primary-soft); color: var(--primary);">
                            <i class="fas fa-scale-balanced fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase font-weight-bold">Resultado Líquido</div>
                            <h4 class="mb-0 font-weight-bold" style="color: {{ $netResult >= 0 ? 'var(--primary)' : 'var(--danger)' }};">
                                {{ number_format($netResult, 2, ',', '.') }} <small class="text-xs text-muted">MT</small>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-[var(--card-bg)] p-3 border-start border-4 border-warning rounded-xl">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning-subtle text-warning rounded-circle p-3 me-3">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase font-weight-bold">Inadimplência</div>
                            <h4 class="mb-0 text-warning font-weight-bold">{{ number_format($overdueTotal, 2, ',', '.') }} <small class="text-xs text-muted">MT</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Executive Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-3 rounded-2xl border-start border-4 border-success" style="background: var(--primary-soft);">
                    <div class="d-flex align-items-center gap-2 font-weight-bold mb-2" style="color: var(--primary);">
                        <i class="fas fa-chart-line"></i> O que aconteceu?
                    </div>
                    <p class="text-xs text-[var(--text-primary)] mb-0 leading-relaxed font-medium">
                        {{ $financialSummary['what_happened'] }}
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-3 rounded-2xl border-start border-4 border-info" style="background: var(--secondary-soft);">
                    <div class="d-flex align-items-center gap-2 font-weight-bold mb-2" style="color: var(--secondary);">
                        <i class="fas fa-arrow-trend-up"></i> Qual a tendência?
                    </div>
                    <p class="text-xs text-[var(--text-primary)] mb-0 leading-relaxed font-medium">
                        {{ $financialSummary['trend'] }}
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 p-3 rounded-2xl border-start border-4 border-warning" style="background: var(--accent-light);">
                    <div class="d-flex align-items-center gap-2 font-weight-bold mb-2" style="color: var(--accent);">
                        <i class="fas fa-triangle-exclamation"></i> Recomendações
                    </div>
                    <p class="text-xs text-[var(--text-primary)] mb-0 leading-relaxed font-medium">
                        {{ $financialSummary['attention'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Tabela de Últimos Pagamentos Efetuados -->
            <div class="col-lg-8">
                <div class="school-table-container">
                    <div class="school-table-header">
                        <h3 class="school-table-title">
                            <i class="fas fa-history me-2" style="color: var(--primary);"></i>
                            Últimos Pagamentos Efetuados
                        </h3>
                        <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-primary rounded-xl">
                            Ver Todos os Pagamentos
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-school">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-[var(--text-primary)]">
                                                {{ $payment->student->first_name }} {{ $payment->student->last_name }}
                                            </div>
                                            <small class="text-muted"><code>{{ $payment->student->student_number }}</code></small>
                                        </td>
                                        <td>
                                            {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td>
                                            <strong style="color: var(--success);">{{ number_format($payment->amount, 2, ',', '.') }} MT</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $payment->payment_method ?? 'M-Pesa' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($payment->status === 'paid')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                                    <i class="fas fa-check me-1"></i> Pago
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1">
                                                    <i class="fas fa-clock me-1"></i> {{ ucfirst($payment->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            Nenhum pagamento registrado recentemente.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral de Resumos Mensais -->
            <div class="col-lg-4 space-y-4">
                <!-- Receita por Mês -->
                <div class="school-card">
                    <div class="school-card-header">
                        <i class="fas fa-calendar-alt text-success me-2"></i> Receita por Mês
                    </div>
                    <div class="school-card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($monthlyRevenue as $revenue)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <span class="fw-semibold text-[var(--text-primary)]">
                                        {{ \Carbon\Carbon::parse($revenue->month . '-01')->format('F Y') }}
                                    </span>
                                    <span class="fw-bold" style="color: var(--success);">
                                        {{ number_format($revenue->total, 2, ',', '.') }} MT
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Despesas por Mês -->
                <div class="school-card">
                    <div class="school-card-header">
                        <i class="fas fa-receipt text-danger me-2"></i> Despesas por Mês
                    </div>
                    <div class="school-card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($monthlyExpenses as $expense)
                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <span class="fw-semibold text-[var(--text-primary)]">
                                        {{ \Carbon\Carbon::parse($expense->month . '-01')->format('F Y') }}
                                    </span>
                                    <span class="fw-bold text-danger">
                                        {{ number_format($expense->total, 2, ',', '.') }} MT
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted p-3">Sem despesas registradas.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Alerta de Inadimplência -->
                <div class="card border-0 shadow-sm bg-warning-subtle border-start border-4 border-warning rounded-2xl">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-warning-emphasis mb-2">
                            <i class="fas fa-exclamation-triangle me-1"></i> Inadimplência
                        </h6>
                        <p class="text-xs text-dark mb-3">
                            Existem <strong>{{ $overdueCount }}</strong> mensalidades pendentes no valor total de
                            <strong>{{ number_format($overdueTotal, 2, ',', '.') }} MT</strong>.
                        </p>
                        <a href="{{ route('reports.financial.defaulters') }}" class="btn btn-warning btn-sm w-100 fw-bold rounded-xl">
                            <i class="fas fa-users-slash me-1"></i> Alunos Devedores
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection