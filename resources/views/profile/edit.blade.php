{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')
@section('page-title-icon', 'fas fa-user-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Meu Perfil</li>
@endsection

@php
    $titleIcon = 'fas fa-user-circle';
@endphp

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="school-card mb-4">
            <div class="school-card-header">
                <i class="fas fa-user"></i>
                Informações do Perfil
            </div>
            <div class="school-card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="school-card mb-4">
            <div class="school-card-header">
                <i class="fas fa-lock"></i>
                Alterar Senha
            </div>
            <div class="school-card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="school-card">
            <div class="school-card-header">
                <i class="fas fa-info-circle"></i>
                Informações da Conta
            </div>
            <div class="school-card-body">
                <p><strong>Email:</strong><br>{{ auth()->user()->email }}</p>
                <p><strong>Tipo de Conta:</strong><br>{{ auth()->user()->role_display }}</p>
                <p><strong>Status:</strong><br>
                    <span class="badge bg-{{ auth()->user()->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst(auth()->user()->status) }}
                    </span>
                </p>
                <p><strong>Último Acesso:</strong><br>
                    {{ auth()->user()->last_login ? auth()->user()->last_login->diffForHumans() : 'Nunca' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection