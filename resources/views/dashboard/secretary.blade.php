@extends('layouts.app')

@section('title', 'Dashboard da Secretaria')
@section('page-title', 'Dashboard da Secretaria')
@section('page-title-icon', 'fas fa-user-tie')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('page-actions')
    <button type="button"
        class="inline-flex items-center gap-2 rounded-full bg-sky-700 px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-sky-800 transition"
        onclick="window.location.reload()">
        <i class="fas fa-sync-alt"></i>
        Atualizar
    </button>
@endsection

@push('styles')
<style>
    .dash-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .dash-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }
    .dash-card-flat {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: none;
    }
    .dash-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dash-section-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .dash-section-subtitle {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }
    .dash-kpi-value {
        font-size: 1.6rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
    }
    .dash-kpi-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .dash-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 9999px;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .dash-quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 0.5rem;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .dash-quick-action:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .dash-quick-action i {
        font-size: 1.1rem;
        color: #059669;
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <!-- Header Contextual -->
    <div class="dash-card-flat">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <p class="dash-kpi-label" style="margin-bottom:0.25rem;">{{ $greetingData['school_name'] }} · Ano Lectivo {{ $greetingData['school_year'] }}</p>
                <h1 class="dash-kpi-value" style="font-size:1.4rem;">{{ $greetingData['greeting'] }}</h1>
            </div>
            <div class="text-end">
                <p class="dash-kpi-label">{{ $greetingData['current_date'] }}</p>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <section class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label">Total Alunos</p>
                        <p class="dash-kpi-value">{{ number_format($stats['total_students']) }}</p>
                        <span class="dash-badge mt-2" style="background:#f1f5f9; color:#475569;">
                            <i class="fas fa-arrow-trend-up" style="color:#10b981;"></i> {{ $stats['new_enrollments_month'] }} este mês
                        </span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#e0e7ff; color:#4f46e5;"><i class="fas fa-user-graduate"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label">Matrículas Pendentes</p>
                        <p class="dash-kpi-value">{{ number_format($stats['pending_enrollments']) }}</p>
                        <span class="dash-badge mt-2" style="background:#f1f5f9; color:#475569;">Aguardando revisão</span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#fef3c7; color:#d97706;"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>

        @if(is_private_school())
        <div class="col-md-6 col-xl-3">
            <div class="dash-card" style="background:linear-gradient(135deg,#0f766e,#115e59); color:#ffffff; border-color:#115e59;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label" style="color:#ccfbf1;">Receita Mensal</p>
                        <p class="dash-kpi-value" style="color:#ffffff;">{{ number_format($stats['monthly_revenue'], 0, ',', '.') }} <span style="font-size:0.75rem; font-weight:600; color:#ccfbf1;">MT</span></p>
                        <span class="dash-badge mt-2" style="background:#ffffff20; color:#ffffff;">
                            {{ $stats['todays_payments'] }} pagamentos hoje
                        </span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#ffffff20; color:#ffffff;"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dash-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="dash-kpi-label">Propinas em Atraso</p>
                        <p class="dash-kpi-value" style="color:#dc2626;">{{ number_format($stats['overdue_payments']) }}</p>
                        <span class="dash-badge mt-2" style="background:#fef2f2; color:#991b1b;">
                            {{ number_format($stats['overdue_amount'], 0, ',', '.') }} MT
                        </span>
                    </div>
                    <div class="rounded-lg p-2" style="background:#fee2e2; color:#dc2626;"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>
        @endif
    </section>

    <!-- Ações Rápidas -->
    <section>
        <div class="dash-section" style="border-bottom:0; padding-bottom:0; margin-bottom:0;">
            <div>
                <p class="dash-section-title">Ações Rápidas</p>
                <p class="dash-section-subtitle">Acesso directo às tarefas mais frequentes</p>
            </div>
        </div>
        <div class="row g-2">
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('students.create') }}" class="dash-quick-action">
                    <i class="fas fa-user-plus"></i>
                    <span>Novo Aluno</span>
                </a>
            </div>
            @if(is_private_school())
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('payments.create') }}" class="dash-quick-action">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Pagamento</span>
                </a>
            </div>
            @endif
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('enrollments.create') }}" class="dash-quick-action">
                    <i class="fas fa-id-card"></i>
                    <span>Matrícula</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <a href="{{ route('communications.create') }}" class="dash-quick-action">
                    <i class="fas fa-bullhorn"></i>
                    <span>Comunicado</span>
                </a>
            </div>
        </div>
    </section>

    @if(is_private_school())
    <!-- Tabelas -->
    <section class="row g-3">
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-section">
                    <div>
                        <p class="dash-section-title">Últimos Pagamentos</p>
                        <p class="dash-section-subtitle">Recebidos recentemente</p>
                    </div>
                    <a href="{{ route('payments.index') }}" class="dash-badge" style="background:#f1f5f9; color:#334155; text-decoration:none;">Histórico</a>
                </div>
                <div class="dash-collapse-content">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Data</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td class="fw-semibold">{{ $payment->student->full_name }}</td>
                                        <td class="text-muted">{{ $payment->payment_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                        <td class="text-end fw-bold text-success">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-3 text-muted">Nenhum pagamento registado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-section">
                    <div>
                        <p class="dash-section-title">Pagamentos em Atraso</p>
                        <p class="dash-section-subtitle">Requerem acção</p>
                    </div>
                    <a href="{{ route('payments.index') }}?status=overdue" class="dash-badge" style="background:#fef2f2; color:#991b1b; text-decoration:none;">Ver todos</a>
                </div>
                <div class="dash-collapse-content">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Vencimento</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overduePayments as $payment)
                                    <tr>
                                        <td class="fw-semibold">{{ $payment->student->full_name }}</td>
                                        <td class="text-muted">{{ $payment->due_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-3 text-muted">Nenhum pagamento em atraso.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Matrículas Pendentes -->
    <section>
        <div class="dash-card-flat">
            <div class="dash-section">
                <div>
                    <p class="dash-section-title">Pré-Matrículas Pendentes</p>
                    <p class="dash-section-subtitle">Aguardando aprovação da secretaria</p>
                </div>
                <a href="{{ route('enrollments.index') }}?status=pending" class="dash-badge" style="background:#f1f5f9; color:#334155; text-decoration:none;">Ver todas</a>
            </div>
            <div class="dash-collapse-content">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Data</th>
                                <th class="text-end">Acção</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingEnrollments as $enrollment)
                                <tr>
                                    <td class="fw-semibold">{{ $enrollment->student->full_name }}</td>
                                    <td class="text-muted">{{ $enrollment->class->name ?? 'N/A' }}</td>
                                    <td class="text-muted">{{ $enrollment->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('enrollments.show', $enrollment) }}" class="dash-badge" style="background:#f1f5f9; color:#334155; text-decoration:none;">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted">Nenhuma matrícula pendente.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
