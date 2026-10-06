@extends('layouts.app')

@section('title', 'Comunicados Oficiais')
@section('page-title', 'Comunicados Oficiais')
@section('page-title-icon', 'fas fa-folder-open')

@section('breadcrumbs')
    <li class="breadcrumb-item">Public</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Comunicados Oficiais</li>
@endsection

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary-school">Comunicados Oficiais</h1>
            <p class="lead text-muted">Fique por dentro das novidades e avisos da nossa escola.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                @forelse($announcements as $announcement)
                    <div class="school-card mb-4 shadow-sm border-0">
                        <div class="school-card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-primary-school mb-2">{{ $announcement->target_audience_name }}</span>
                                    <h3 class="fw-bold mb-0">{{ $announcement->title }}</h3>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">{{ $announcement->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                            <div class="announcement-content">
                                {!! nl2br(e($announcement->message)) !!}
                            </div>
                            @if($announcement->attachments && count($announcement->attachments) > 0)
                                <div class="mt-4 pt-3 border-top">
                                    @foreach($announcement->attachments as $attachment)
                                        <a href="{{ Storage::url($attachment) }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary me-2">
                                            <i class="fas fa-paperclip me-1"></i> Ver Anexo
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bullhorn fa-4x text-light mb-3"></i>
                        <p class="text-muted fs-5">Não há comunicados públicos no momento.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection