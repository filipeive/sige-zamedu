@extends('layouts.app')

@section('title', 'Análise de Pedido de Matrícula')
@section('page-title', 'Análise de Pedido')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item">Applications</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Análise de Pedido</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Application Details -->
            <div class="school-card mb-4">
                <div class="school-card-header">
                    <h3 class="school-card-title"><i class="fas fa-info-circle me-2"></i> Detalhes do Pedido
                        #{{ $application->id }}</h3>
                </div>
                <div class="school-card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Tipo de Pedido</h6>
                            <p class="fw-bold fs-5">{{ $application->type === 'NEW' ? 'Novo Ingresso' : 'Renovação' }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6 class="text-muted mb-1">Data de Submissão</h6>
                            <p class="fw-bold fs-5">{{ $application->submitted_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Student Data -->
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3 text-primary-school">Dados do Aluno</h5>
                            <p class="mb-1"><strong>Nome:</strong> {{ $application->student_data['first_name'] }}
                                {{ $application->student_data['last_name'] }}</p>
                            <p class="mb-1"><strong>Nascimento:</strong>
                                {{ \Carbon\Carbon::parse($application->student_data['birth_date'])->format('d/m/Y') }}</p>
                            <p class="mb-1"><strong>Sexo:</strong>
                                {{ $application->student_data['gender'] === 'male' ? 'Masculino' : 'Feminino' }}</p>
                            <p class="mb-1"><strong>Classe:</strong> {{ $application->student_data['grade_level'] }}</p>
                            @if(isset($application->student_data['special_needs']))
                                <p class="mb-1 text-danger"><strong>Necessidades:</strong>
                                    {{ $application->student_data['special_needs'] }}</p>
                            @endif
                        </div>

                        <!-- Parent Data -->
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3 text-primary-school">Dados do Encarregado</h5>
                            <p class="mb-1"><strong>Nome:</strong> {{ $application->parent_data['first_name'] }}
                                {{ $application->parent_data['last_name'] }}</p>
                            <p class="mb-1"><strong>Telefone:</strong> {{ $application->parent_data['phone'] }}</p>
                            <p class="mb-1"><strong>E-mail:</strong> {{ $application->parent_data['email'] ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Parentesco:</strong>
                                {{ ucfirst($application->parent_data['relationship'] ?? 'N/A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="school-card">
                <div class="school-card-header">
                    <h3 class="school-card-title"><i class="fas fa-file-alt me-2"></i> Documentos Enviados</h3>
                </div>
                <div class="school-card-body">
                    <div class="row g-3">
                        @forelse($application->documents as $doc)
                            <div class="col-md-4">
                                <div class="card border h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                        <h6 class="mb-2">{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}</h6>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Visualizar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">Nenhum documento digital enviado.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status Management -->
            <div class="school-card mb-4">
                <div class="school-card-header">
                    <h3 class="school-card-title"><i class="fas fa-tasks me-2"></i> Gestão de Status</h3>
                </div>
                <div class="school-card-body">
                    <div class="mb-4 text-center">
                        <h6 class="text-muted mb-2">Status Atual</h6>
                        <span
                            class="badge bg-{{ $application->status === 'ENROLLED' ? 'success' : 'warning' }} fs-5 px-4 py-2">
                            {{ $application->status }}
                        </span>
                    </div>

                    <form action="{{ route('enrollments.applications.update-status', $application->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Alterar Status</label>
                            <select name="status" class="form-select">
                                <option value="PENDING" {{ $application->status === 'PENDING' ? 'selected' : '' }}>PENDENTE
                                </option>
                                <option value="IN_REVIEW" {{ $application->status === 'IN_REVIEW' ? 'selected' : '' }}>EM
                                    ANÁLISE</option>
                                <option value="DOCUMENT_DELIVERED" {{ $application->status === 'DOCUMENT_DELIVERED' ? 'selected' : '' }}>DOCS ENTREGUES</option>
                                <option value="APPROVED" {{ $application->status === 'APPROVED' ? 'selected' : '' }}>APROVADO
                                </option>
                                <option value="REJECTED" {{ $application->status === 'REJECTED' ? 'selected' : '' }}>REJEITADO
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações Internas</label>
                            <textarea name="admin_notes" class="form-control"
                                rows="3">{{ $application->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-school w-100">Atualizar Status</button>
                    </form>
                </div>
            </div>

            <!-- Payment Confirmation -->
            <div class="school-card">
                <div class="school-card-header">
                    <h3 class="school-card-title"><i class="fas fa-money-bill-wave me-2"></i> Confirmação de Pagamento</h3>
                </div>
                <div class="school-card-body">
                    <div class="mb-4 text-center">
                        <h6 class="text-muted mb-1">Total a Pagar</h6>
                        <h3 class="fw-bold">{{ number_format($application->total_amount, 2, ',', '.') }} MT</h3>
                        <span class="badge bg-{{ $application->payment_status === 'PAID' ? 'success' : 'danger' }}">
                            {{ $application->payment_status === 'PAID' ? 'PAGO' : 'AGUARDANDO PAGAMENTO' }}
                        </span>
                    </div>

                    @if($application->payment_status !== 'PAID')
                        <form action="{{ route('enrollments.applications.confirm-payment', $application->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Referência do Depósito</label>
                                <input type="text" name="payment_reference" class="form-control" required
                                    placeholder="Ex: DEP-12345">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Data do Depósito</label>
                                <input type="date" name="payment_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Comprovativo (Opcional)</label>
                                <input type="file" name="payment_proof" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Confirmar Pagamento</button>
                        </form>
                    @else
                        <div class="alert alert-success">
                            <p class="mb-1"><strong>Ref:</strong> {{ $application->payment_reference }}</p>
                            <p class="mb-0"><strong>Data:</strong> {{ $application->payment_date->format('d/m/Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Finalize Enrollment (Only if Approved & Paid) -->
            @if($application->status === 'APPROVED' && $application->payment_status === 'PAID')
            <div class="school-card mt-4 border-success">
                <div class="school-card-header bg-success text-white">
                    <h3 class="school-card-title"><i class="fas fa-user-check me-2"></i> Finalizar Matrícula</h3>
                </div>
                <div class="school-card-body">
                    <form action="{{ route('enrollments.applications.finalize', $application->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Atribuir Turma <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Selecione a turma...</option>
                                @foreach(\App\Models\ClassRoom::all() as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade_level }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fs-5 py-2">
                            <i class="fas fa-check-double me-2"></i> Converter em Aluno Ativo
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection