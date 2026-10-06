@extends('layouts.app')

@section('title', 'Completar Perfil')
@section('page-title', 'Completar Perfil de Professor')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item">Teacher-portal</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Completar Perfil de Professor</li>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="school-card">
                    <div class="school-card-header">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Bem-vindo, Professor(a)!
                </div>
                <div class="school-card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Por favor, complete as informações abaixo para configurar seu perfil de professor e acessar o
                        painel.
                    </div>

                    <form action="{{ route('teacher.store-profile') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone *</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" required placeholder="Ex: +258 84 123 4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date"
                                    class="form-control @error('birth_date') is-invalid @enderror"
                                    value="{{ old('birth_date') }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gênero</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">Selecione...</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Feminino</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de BI/Documento</label>
                                <input type="text" name="bi_number"
                                    class="form-control @error('bi_number') is-invalid @enderror"
                                    value="{{ old('bi_number') }}">
                                @error('bi_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Endereço *</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                    rows="2" required placeholder="Seu endereço completo">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <h5 class="mb-3 border-bottom pb-2">Informações Profissionais</h5>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Qualificação Acadêmica</label>
                                <input type="text" name="qualification"
                                    class="form-control @error('qualification') is-invalid @enderror"
                                    value="{{ old('qualification') }}" placeholder="Ex: Licenciatura em Matemática">
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Especialização</label>
                                <input type="text" name="specialization"
                                    class="form-control @error('specialization') is-invalid @enderror"
                                    value="{{ old('specialization') }}" placeholder="Ex: Álgebra, Geometria">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary-school btn-lg">
                                <i class="fas fa-save me-2"></i> Criar Perfil e Acessar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection