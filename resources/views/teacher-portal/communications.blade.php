@extends('layouts.app')

@section('title', 'Comunicados')
@section('page-title', 'Comunicados')
@section('page-title-icon', 'fas fa-bullhorn')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Comunicados</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-1">
                    <i class="fas fa-bullhorn"></i> Avisos Institucionais
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Comunicados da Escola</h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Notificações, circulares e orientações da direção para o corpo docente
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-white/10 text-white text-xs font-semibold backdrop-blur-xs border border-white/10">
                    {{ $communications->total() }} comunicado(s)
                </span>
            </div>
        </div>
    </div>

    <!-- Communications Feed -->
    @if($communications->count() > 0)
        <div class="space-y-4">
            @foreach($communications as $communication)
                @php
                    $priorityMap = [
                        'urgent' => ['border' => 'border-l-rose-500', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Urgente'],
                        'high' => ['border' => 'border-l-amber-500', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Alta Prioridade'],
                        'medium' => ['border' => 'border-l-blue-500', 'badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Média'],
                        'low' => ['border' => 'border-l-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Geral'],
                    ];
                    $prio = $priorityMap[$communication->priority] ?? ['border' => 'border-l-slate-400', 'badge' => 'bg-slate-50 text-slate-700 border-slate-200', 'label' => ucfirst($communication->priority)];
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs border-l-4 {{ $prio['border'] }} p-6 transition-all hover:shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <h2 class="text-base font-bold text-slate-800">{{ $communication->title }}</h2>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $prio['badge'] }}">
                                {{ $prio['label'] }}
                            </span>
                            @if($communication->target_audience_name)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $communication->target_audience_name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line mb-4">
                        {!! nl2br(e($communication->message)) !!}
                    </div>

                    @if($communication->attachments && count($communication->attachments) > 0)
                        <div class="pt-3 border-t border-slate-100 mb-4">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                                <i class="fas fa-paperclip"></i>
                                Documentos Anexados ({{ count($communication->attachments) }})
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($communication->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment) }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 transition-colors">
                                        <i class="fas fa-file-download text-emerald-600 text-xs"></i>
                                        <span>{{ basename($attachment) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-[10px]">
                                <i class="fas fa-user"></i>
                            </span>
                            <span>Publicado por: <strong class="text-slate-700 font-semibold">{{ $communication->createdBy->name ?? 'Direção' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span><i class="fas fa-clock mr-1"></i> {{ $communication->created_at->format('d/m/Y H:i') }}</span>
                            @if($communication->publish_at)
                                <span>&bull; Em vigor: {{ $communication->publish_at->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if($communications->hasPages())
                <div class="p-4 bg-white rounded-2xl border border-slate-200">
                    {{ $communications->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-bullhorn text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Nenhum Comunicado Ativo</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                Não existem avisos ou circulares institucionais dirigidos a si no momento.
            </p>
        </div>
    @endif

</div>
@endsection
