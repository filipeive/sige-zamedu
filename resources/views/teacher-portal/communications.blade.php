@extends('layouts.app')

@section('title', 'Comunicados')
@section('page-title', 'Comunicados')
@section('page-title-icon', 'fas fa-bullhorn')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Comunicados</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="school-card">
            <div class="school-card-header">
                <i class="fas fa-bullhorn"></i>
                Comunicados
                <span class="badge bg-primary ms-2">{{ $communications->total() }}</span>
            </div>
            <div class="school-card-body">
                @if($communications->count() > 0)
                    @foreach($communications as $communication)
                    <div class="communication-item border-start border-4 border-{{ $communication->priority_color }} bg-light rounded p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-1">{{ $communication->title }}</h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-{{ $communication->priority_color }}">
                                    {{ ucfirst($communication->priority) }}
                                </span>
                                <span class="badge bg-secondary">
                                    {{ $communication->target_audience_name }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="communication-message mb-3">
                            {!! nl2br(e($communication->message)) !!}
                        </div>

                        @if($communication->attachments && count($communication->attachments) > 0)
                        <div class="communication-attachments mb-3">
                            <strong><i class="fas fa-paperclip me-1"></i> Anexos:</strong>
                            <div class="mt-1">
                                @foreach($communication->attachments as $attachment)
                                <a href="{{ Storage::url($attachment) }}" 
                                   class="btn btn-sm btn-outline-secondary me-2 mb-1"
                                   target="_blank">
                                    <i class="fas fa-download me-1"></i>
                                    {{ basename($attachment) }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="communication-footer d-flex justify-content-between align-items-center text-muted">
                            <div>
                                <small>
                                    <i class="fas fa-user me-1"></i>
                                    Por: {{ $communication->createdBy->name ?? 'Sistema' }}
                                </small>
                            </div>
                            <div>
                                <small>
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $communication->created_at->format('d/m/Y H:i') }}
                                    @if($communication->publish_at)
                                    • Publicado em: {{ $communication->publish_at->format('d/m/Y') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Paginação -->
                    @if($communications->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Mostrando {{ $communications->firstItem() }} a {{ $communications->lastItem() }} de {{ $communications->total() }} comunicados
                        </div>
                        <nav>
                            {{ $communications->links() }}
                        </nav>
                    </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Nenhum Comunicado</h4>
                        <p class="text-muted">Não há comunicados no momento.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.communication-item {
    transition: all 0.2s ease;
}

.communication-item:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.communication-message {
    line-height: 1.6;
    white-space: pre-wrap;
}
</style>
@endsection