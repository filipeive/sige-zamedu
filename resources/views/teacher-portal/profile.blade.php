@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')
@section('page-title-icon', 'fas fa-user-cog')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Meu Perfil</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Perfil -->
            <div class="school-card mb-4">
                <div class="school-card-body text-center">
                    <div class="profile-avatar mx-auto mb-3">
                        {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                    </div>
                    <h4>{{ $teacher->first_name }} {{ $teacher->last_name }}</h4>
                    <p class="text-muted">{{ $teacher->specialization }}</p>

                    <div class="profile-stats mt-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat">
                                    <div class="stat-number">{{ $teacher->classes()->count() }}</div>
                                    <div class="stat-label">Turmas</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat">
                                    <div class="stat-number">{{ $teacher->years_experience }}</div>
                                    <div class="stat-label">Anos Exp.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações de Contato -->
            <div class="school-card">
                <div class="school-card-header">
                    <i class="fas fa-address-card"></i>
                    Informações de Contato
                </div>
                <div class="school-card-body">
                    <div class="contact-info">
                        <div class="contact-item mb-3">
                            <i class="fas fa-envelope text-primary"></i>
                            <div>
                                <strong>Email</strong>
                                <div>{{ $teacher->email }}</div>
                            </div>
                        </div>
                        <div class="contact-item mb-3">
                            <i class="fas fa-phone text-success"></i>
                            <div>
                                <strong>Telefone</strong>
                                <div>{{ $teacher->phone }}</div>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <div>
                                <strong>Endereço</strong>
                                <div>{{ $teacher->address }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Formulário de Edição -->
            <div class="school-card">
                <div class="school-card-header">
                    <i class="fas fa-edit"></i>
                    Editar Perfil
                </div>
                <div class="school-card-body">
                    <div class="school-card-body">
                        <form action="{{ route('teacher.update-profile') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Primeiro Nome</label>
                                    <input type="text" class="form-control" value="{{ $teacher->first_name }}" readonly>
                                    <small class="text-muted">Contacte a administração para alterar o nome</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Último Nome</label>
                                    <input type="text" class="form-control" value="{{ $teacher->last_name }}" readonly>
                                    <small class="text-muted">Contacte a administração para alterar o nome</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="{{ $teacher->email }}" readonly>
                                    <small class="text-muted">Email não pode ser alterado</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone *</label>
                                    <input type="text" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $teacher->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Endereço *</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                        rows="3" required>{{ old('address', $teacher->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Informações Profissionais -->
                            <div class="row mt-4 pt-3 border-top">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Qualificação</label>
                                    <input type="text" name="qualification"
                                        class="form-control @error('qualification') is-invalid @enderror"
                                        value="{{ old('qualification', $teacher->qualification) }}">
                                    @error('qualification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Especialização</label>
                                    <input type="text" name="specialization"
                                        class="form-control @error('specialization') is-invalid @enderror"
                                        value="{{ old('specialization', $teacher->specialization) }}">
                                    @error('specialization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data de Contratação</label>
                                    <input type="text" class="form-control"
                                        value="{{ $teacher->hire_date->format('d/m/Y') }}" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Salário</label>
                                    <input type="text" class="form-control"
                                        value="{{ number_format($teacher->salary, 2, ',', '.') }} MZN" readonly>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-primary-school">
                                    <i class="fas fa-save"></i> Atualizar Perfil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alterar Senha -->
                @if($teacher->user)
                    <div class="school-card mt-4">
                        <div class="school-card-header">
                            <i class="fas fa-lock"></i>
                            Segurança da Conta
                        </div>
                        <div class="school-card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Para alterar sua senha, contacte a administração do sistema.
                            </div>

                            <div class="account-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Status da Conta:</strong>
                                        <span class="badge bg-success">Ativa</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Último Login:</strong>
                                        <span>{{ $teacher->user->last_login_at ? $teacher->user->last_login_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <style>
            .profile-avatar {
                width: 120px;
                height: 120px;
                background: linear-gradient(135deg, var(--accent), #0097A7);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 36px;
                font-weight: 700;
            }

            .profile-stats .stat {
                padding: 15px;
            }

            .profile-stats .stat-number {
                font-size: 24px;
                font-weight: 700;
                color: var(--primary);
            }

            .profile-stats .stat-label {
                font-size: 12px;
                color: var(--text-muted);
                text-transform: uppercase;
            }

            .contact-item {
                display: flex;
                align-items: flex-start;
                gap: 15px;
            }

            .contact-item i {
                font-size: 18px;
                margin-top: 2px;
                flex-shrink: 0;
            }

            .account-info {
                padding: 15px;
                background: var(--content-bg);
                border-radius: var(--border-radius);
            }
        </style>
@endsection