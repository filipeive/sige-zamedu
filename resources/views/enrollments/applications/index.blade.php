@extends('layouts.app')

@section('title', 'Pedidos de Matrícula')
@section('page-title', 'Pedidos de Matrícula')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item">Applications</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Pedidos de Matrícula</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="school-card">
                <div class="school-card-header d-flex justify-content-between align-items-center">
                    <h3 class="school-card-title">
                        <i class="fas fa-list me-2"></i> Lista de Pedidos (Pré-Inscrições e Renovações)
                    </h3>
                    <div class="d-flex gap-2">
                        <a href="{{ route('enrollments.applications.index', ['status' => 'PENDING']) }}"
                            class="btn btn-sm btn-outline-warning">Pendentes</a>
                        <a href="{{ route('enrollments.applications.index', ['type' => 'NEW']) }}"
                            class="btn btn-sm btn-outline-primary">Novos Ingressos</a>
                        <a href="{{ route('enrollments.applications.index', ['type' => 'RENEWAL']) }}"
                            class="btn btn-sm btn-outline-info">Renovações</a>
                    </div>
                </div>
                <div class="school-card-body">
                    <div class="table-responsive">
                        <table class="table table-school table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Aluno</th>
                                    <th>Encarregado</th>
                                    <th>Valor</th>
                                    <th>Pagamento</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $app)
                                    <tr>
                                        <td>{{ $app->submitted_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $app->type === 'NEW' ? 'primary' : 'info' }}">
                                                {{ $app->type === 'NEW' ? 'Novo Ingresso' : 'Renovação' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $app->student_data['first_name'] }} {{ $app->student_data['last_name'] }}
                                            @if($app->type === 'RENEWAL')
                                                <br><small class="text-muted">ID: {{ $app->student_id }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $app->parent_data['first_name'] }} {{ $app->parent_data['last_name'] }}
                                            <br><small class="text-muted">{{ $app->parent_data['phone'] }}</small>
                                        </td>
                                        <td>{{ number_format($app->total_amount, 2, ',', '.') }} MT</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $app->payment_status === 'PAID' ? 'success' : 'secondary' }}">
                                                {{ $app->payment_status === 'PAID' ? 'Pago' : 'Pendente' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'PENDING' => 'warning',
                                                    'IN_REVIEW' => 'info',
                                                    'DOCUMENT_DELIVERED' => 'primary',
                                                    'APPROVED' => 'success',
                                                    'REJECTED' => 'danger',
                                                    'ENROLLED' => 'dark'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$app->status] ?? 'secondary' }}">
                                                {{ $app->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('enrollments.applications.show', $app->id) }}"
                                                class="btn btn-sm btn-primary-school">
                                                <i class="fas fa-eye"></i> Analisar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Nenhum pedido encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection