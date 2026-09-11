@extends('errors.layout')

@section('title', 'Acesso Negado')

@section('content')
    <div class="error-icon">
        <i class="fas fa-lock text-danger"></i>
    </div>
    <div class="error-code">403</div>
    <div class="error-title text-danger">Acesso Negado</div>
    <p class="error-message">
        Desculpe, você não tem permissão para acessar esta página.<br>
        Se você acredita que isso é um erro, entre em contato com o suporte.
    </p>
@endsection