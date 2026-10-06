@extends('layouts.app')

@section('title', 'Lista de Materiais Escolares')
@section('page-title', 'Lista de Materiais Escolares')
@section('page-title-icon', 'fas fa-folder-open')

@section('breadcrumbs')
    <li class="breadcrumb-item">Public</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Lista de Materiais Escolares</li>
@endsection

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary-school">Lista de Materiais Escolares</h1>
            <p class="lead text-muted">Ano Letivo {{ next_school_year() }}</p>
        </div>

        <div class="row g-4">
            @foreach($materialLists as $list)
                <div class="col-md-6">
                    <div class="school-card h-100 shadow-sm border-0">
                        <div class="school-card-header bg-primary-school text-white">
                            <h3 class="mb-0"><i class="fas fa-book me-2"></i>
                                {{ $list->grade_level === 'pre-school' ? 'Pré-Escolar' : 'Ensino Primário (1ª-6ª)' }}</h3>
                        </div>
                        <div class="school-card-body">
                            <table class="table table-borderless">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-end">Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($list->items as $item)
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <td class="text-end fw-bold">{{ $item['quantity'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($list->notes)
                                <div class="mt-3 p-3 bg-light rounded">
                                    <small class="text-muted"><strong>Observações:</strong> {{ $list->notes }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-0 text-center pb-4">
                            <button class="btn btn-outline-primary-school btn-sm">
                                <i class="fas fa-download me-1"></i> Baixar PDF
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 text-center">
            <div class="alert alert-info d-inline-block px-5">
                <i class="fas fa-info-circle me-2"></i>
                Os manuais escolares podem ser adquiridos diretamente na secretaria da escola.
            </div>
        </div>
    </div>
@endsection