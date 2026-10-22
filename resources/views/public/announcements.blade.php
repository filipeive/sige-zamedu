@extends('layouts.app')

@section('title', 'Comunicados Oficiais')
@section('page-title', 'Comunicados Oficiais')
@section('page-title-icon', 'fas fa-bullhorn')

@section('breadcrumbs')
    <li class="breadcrumb-item text-slate-400">Portal Público</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Comunicados</li>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Header Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="flex items-center gap-3.5">
            <span class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-emerald-300 text-xl">
                <i class="fas fa-bullhorn"></i>
            </span>
            <div>
                <h1 class="text-xl font-bold text-white">Comunicados Oficiais</h1>
                <p class="text-xs text-slate-300 mt-0.5">Informações, circulares e orientações à comunidade escolar</p>
            </div>
        </div>
    </div>

    <!-- Feed -->
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs border-l-4 border-l-emerald-600 p-6 transition-all hover:shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 mb-1.5">
                            {{ $announcement->target_audience_name ?? 'Comunidade Escolar' }}
                        </span>
                        <h2 class="text-base font-bold text-slate-800">{{ $announcement->title }}</h2>
                    </div>
                    <div class="text-[11px] text-slate-400 shrink-0">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        {{ $announcement->created_at->format('d/m/Y') }}
                    </div>
                </div>

                <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line mb-4">
                    {!! nl2br(e($announcement->message)) !!}
                </div>

                @if($announcement->attachments && count($announcement->attachments) > 0)
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap gap-2">
                        @foreach($announcement->attachments as $attachment)
                            <a href="{{ Storage::url($attachment) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 transition-colors">
                                <i class="fas fa-paperclip text-emerald-600 text-xs"></i>
                                Ver Anexo
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                <i class="fas fa-bullhorn text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs font-medium text-slate-500">Não há comunicados públicos registados no momento.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
