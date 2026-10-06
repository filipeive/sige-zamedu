@extends('layouts.app')

@section('title', 'Minhas Licenças')
@section('page-title', 'Minhas Licenças')
@section('page-title-icon', 'fas fa-calendar-times')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Licenças</li>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-primary text-white">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $statistics['total'] }}</div>
                    <div class="stat-label">Total de Pedidos</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-warning text-white">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $statistics['pending'] }}</div>
                    <div class="stat-label">Pendentes</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-success text-white">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $statistics['approved'] }}</div>
                    <div class="stat-label">Aprovados</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-danger text-white">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ $statistics['rejected'] }}</div>
                    <div class="stat-label">Rejeitados</div>
                </div>
            </div>
        </div>
    </div>

    <div class="school-card">
        <div class="school-card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i>Histórico de Licenças</span>
            <a href="{{ route('teacher.leave-requests.create') }}" class="btn btn-sm btn-light text-primary fw-bold">
                <i class="fas fa-plus me-1"></i> Nova Solicitação
            </a>
        </div>
        <div class="school-card-body">
            @if($leaveRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-school table-hover">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Período</th>
                                <th>Duração</th>
                                <th>Status</th>
                                <th>Data Solicitação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $request)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $request->leave_type }}</span>
                                        @if($request->reason)
                                            <small class="d-block text-muted text-truncate" style="max-width: 200px;">
                                                {{ $request->reason }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}
                                        <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                        {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1 }}
                                        dias
                                    </td>
                                    <td>
                                        @if($request->status === 'approved')
                                            <span class="badge bg-success">Aprovado</span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge bg-danger">Rejeitado</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $leaveRequests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-calendar-times fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted">Nenhuma solicitação encontrada</h5>
                    <p class="text-muted small">Você ainda não fez nenhuma solicitação de licença.</p>
                    <a href="{{ route('teacher.leave-requests.create') }}" class="btn btn-primary-school mt-2">
                        <i class="fas fa-plus me-2"></i>Solicitar Licença
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection