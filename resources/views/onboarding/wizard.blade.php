@extends('layouts.app')

@section('title', 'Onboarding Multi-Escola — Registo de Nova Instituição')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    <!-- Top Banner Header -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 text-white p-4" 
         style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-white bg-opacity-20 rounded-3 text-white">
                <i class="fa-solid fa-school-flag fa-2x"></i>
            </div>
            <div>
                <h2 class="h3 fw-bold mb-1">Wizard de Onboarding Multi-Escola</h2>
                <p class="mb-0 text-white-50 text-sm">Integração de Novas Instituições de Ensino no Ecossistema SIGE / ZAMEDU</p>
            </div>
        </div>
    </div>

    <!-- Error Feedback -->
    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
            <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Por favor, corrija os erros abaixo:</h6>
            <ul class="mb-0 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Onboarding Form Card -->
    <div class="card border-0 rounded-4 shadow-sm p-4">
        <form method="POST" action="{{ route('onboarding.school.store') }}">
            @csrf

            <!-- STEP 1: Enquadramento Territorial -->
            <div class="border-bottom pb-4 mb-4">
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <span class="badge bg-emerald text-white rounded-circle">1</span> 1. Enquadramento Territorial MINEDH
                </h5>
                <p class="text-muted text-xs mb-3">Selecione a jurisdição geográfica oficial da escola na hierarquia territorial.</p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Província *</label>
                        <select name="province_id" class="form-select rounded-3" required>
                            <option value="">Selecione a Província...</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p->id }}" {{ old('province_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Distrito *</label>
                        <select name="district_id" class="form-select rounded-3" required>
                            <option value="">Selecione o Distrito...</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">ZIP (Zona de Influência) *</label>
                        <select name="zip_id" class="form-select rounded-3" required>
                            <option value="">Selecione a ZIP...</option>
                            @foreach($zips as $z)
                                <option value="{{ $z->id }}" {{ old('zip_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Identificação da Escola -->
            <div class="border-bottom pb-4 mb-4">
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <span class="badge bg-emerald text-white rounded-circle">2</span> 2. Dados de Identificação da Escola
                </h5>
                <p class="text-muted text-xs mb-3">Informações cadastrais principais da instituição de ensino.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Nome da Escola *</label>
                        <input type="text" name="school_name" class="form-control rounded-3" placeholder="Ex: Escola Secundária de Malei" value="{{ old('school_name') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Código Oficial (Opcional)</label>
                        <input type="text" name="school_code" class="form-control rounded-3" placeholder="Ex: ESG-MAL-01" value="{{ old('school_code') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Tipo de Instituição *</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="public" {{ old('type') == 'public' ? 'selected' : '' }}>Escola Pública</option>
                            <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>Escola Privada</option>
                            <option value="community" {{ old('type') == 'community' ? 'selected' : '' }}>Escola Comunitária</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">E-mail Institucional *</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="escola@minedh.gov.mz" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Contacto Telefónico *</label>
                        <input type="text" name="phone" class="form-control rounded-3" placeholder="+258 84 000 0000" value="{{ old('phone') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Endereço Físico</label>
                        <input type="text" name="address" class="form-control rounded-3" placeholder="Bairro Central, Namacurra" value="{{ old('address') }}">
                    </div>
                </div>
            </div>

            <!-- STEP 3: Administrador da Escola -->
            <div class="border-bottom pb-4 mb-4">
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <span class="badge bg-emerald text-white rounded-circle">3</span> 3. Conta do Diretor / Administrador Inicial
                </h5>
                <p class="text-muted text-xs mb-3">Credenciais de acesso para o primeiro utilizador com perfil gestor de escola.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Nome Completo do Diretor *</label>
                        <input type="text" name="admin_name" class="form-control rounded-3" placeholder="Dr. Armindo Sitoe" value="{{ old('admin_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">E-mail do Administrador *</label>
                        <input type="email" name="admin_email" class="form-control rounded-3" placeholder="diretor@escola.co.mz" value="{{ old('admin_email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Palavra-passe *</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Confirmar Palavra-passe *</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-3" required>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Configuração Inicial -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <span class="badge bg-emerald text-white rounded-circle">4</span> 4. Configurações Iniciais do Sistema
                </h5>
                <p class="text-muted text-xs mb-3">Parâmetros pedagógicos de arranque.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Ano Lectivo Inicial</label>
                        <input type="text" name="academic_year" class="form-control rounded-3" value="2026" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold uppercase text-muted">Limiar de Aprovação (Norma MINEDH)</label>
                        <input type="number" step="0.1" name="passing_grade" class="form-control rounded-3" value="9.5" required>
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('territorial.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                <button type="submit" class="btn btn-emerald text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #059669;">
                    <i class="fa-solid fa-check me-1"></i> Concluir Onboarding da Escola
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
