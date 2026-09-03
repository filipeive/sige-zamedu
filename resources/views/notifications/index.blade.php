@extends('layouts.app')

@section('title', 'Minhas Notificações')
@section('page-title', 'Minhas Notificações')
@section('page-title-icon', 'fas fa-bell')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Início</a></li>
    <li class="breadcrumb-item active">Notificações</li>
@endsection

@section('content')
@php
    $userNotifs = auth()->user()->notifications;
    $unreadCount = auth()->user()->unreadNotifications->count();
    $totalCount = $userNotifs->count();
    $recentCount = $userNotifs->where('created_at', '>=', now()->subDays(7))->count();
@endphp

<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-bell text-emerald-600"></i> Minhas Notificações
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Central de avisos, alertas financeiros e comunicações.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                        <i class="fas fa-check-double"></i> Marcar todas como lidas
                    </button>
                </form>
            @endif
            @if($totalCount > 0)
                <form action="{{ route('notifications.clear-all') }}" method="POST" data-confirm="Tem certeza que deseja remover todas as notificações?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 text-rose-600 hover:border-rose-200 text-xs font-bold shadow-2xs transition">
                        <i class="fas fa-trash-alt"></i> Limpar tudo
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Stat KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total de Notificações</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $totalCount }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Não Lidas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $unreadCount }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Últimos 7 Dias</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $recentCount }}</span>
            </div>
        </div>
    </div>

    <!-- Notifications List Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list text-emerald-600"></i> Histórico de Notificações ({{ $notifications->total() }})
            </h3>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                @php
                    $isUnread = !$notification->read_at;
                    $iconClass = match($notification->data['type'] ?? 'default') {
                        'payment' => 'fas fa-money-bill-wave text-emerald-600 bg-emerald-50',
                        'warning' => 'fas fa-exclamation-triangle text-amber-600 bg-amber-50',
                        'danger' => 'fas fa-circle-xmark text-rose-600 bg-rose-50',
                        default => 'fas fa-bell text-blue-600 bg-blue-50'
                    };
                @endphp
                <div class="p-4 transition {{ $isUnread ? 'bg-slate-50/80 border-l-4 border-l-emerald-600' : 'opacity-75' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-grow min-w-0">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 text-sm {{ explode(' ', $iconClass)[2] }} {{ explode(' ', $iconClass)[3] }}">
                                <i class="{{ explode(' ', $iconClass)[0] }} {{ explode(' ', $iconClass)[1] }}"></i>
                            </div>
                            <div class="space-y-1 min-w-0 flex-1 overflow-hidden" style="word-break: break-word; overflow-wrap: anywhere;">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-xs font-bold leading-snug break-words max-w-full {{ $isUnread ? 'text-slate-900' : 'text-slate-700' }}" style="word-break: break-word; overflow-wrap: anywhere;">
                                        {{ $notification->data['title'] ?? 'Notificação' }}
                                    </h4>
                                    @if($isUnread)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">Nova</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed font-medium break-words max-w-full" style="word-break: break-word; overflow-wrap: anywhere; overflow-x: hidden;">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <div class="flex items-center gap-3 pt-1 text-[10px] text-slate-400 flex-wrap">
                                    <span><i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}</span>
                                    @if(isset($notification->data['action_url']) && $notification->data['action_url'] !== '#')
                                        <a href="{{ $notification->data['action_url'] }}" class="no-underline font-bold text-emerald-700 hover:text-emerald-800 break-all">
                                            Ver detalhes <i class="fas fa-arrow-right text-[8px] ml-0.5"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($isUnread)
                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="h-7 w-7 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-emerald-600 flex items-center justify-center transition" title="Marcar como lida">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="fas fa-bell-slash text-3xl text-slate-300 mb-2"></i>
                    <h4 class="text-sm font-bold text-slate-700">Nenhuma notificação encontrada</h4>
                    <p class="text-xs text-slate-500 mt-1">Você está em dia com todas as suas notificações.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
