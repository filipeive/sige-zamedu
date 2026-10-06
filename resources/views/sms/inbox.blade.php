@extends('layouts.app')

@section('title', 'Caixa de Entrada')
@section('page-title', 'Caixa de Entrada SMS')
@section('page-title-icon', 'fas fa-comment-dots')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sms.index') }}" class="no-underline">Central de Mensagens</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Caixa de Entrada</li>
@endsection

@section('title-icon', 'fas fa-inbox')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-700 to-teal-600 text-white rounded-2xl p-5 shadow-lg">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-2xl font-black flex items-center gap-2.5">
                    <i class="fas fa-inbox"></i> SMS Recebidas
                </h1>
                <p class="text-emerald-100 text-sm mt-1">
                    Mensagens dos encarregados/alunos respondidas via httpSMS.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sms.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/15 hover:bg-white/25 text-white text-xs font-bold transition border border-white/20">
                    <i class="fas fa-paper-plane"></i> Central de Envio
                </a>
                @if($stats['unread'] > 0)
                    <form method="POST" action="{{ route('sms.inbox.mark-all') }}" data-confirm="Marcar todas as {{ $stats['unread'] }} mensagens como lidas?">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white text-emerald-700 text-xs font-black hover:bg-emerald-50 transition">
                            <i class="fas fa-check-double"></i> Marcar todas como lidas
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-slate-400 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold shrink-0"><i class="fas fa-envelope"></i></div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Recebidas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['total'] }}</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-rose-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold shrink-0"><i class="fas fa-circle-dot"></i></div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Por Ler</span>
                <span class="text-xl font-black text-rose-600 mt-0.5 block">{{ $stats['unread'] }}</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0"><i class="fas fa-user-graduate"></i></div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Com Aluno Match</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['matched'] }}</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0"><i class="fas fa-question"></i></div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Não Identificadas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $stats['orphan'] }}</span>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('sms.inbox.index') }}" method="GET" class="grid gap-2 sm:grid-cols-2 md:grid-cols-4 items-center">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" name="search" class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="Pesquisar telefone, nome, conteúdo..." value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>
            <div>
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <option value="unread" @selected(request('status') === 'unread')>Não lidas</option>
                    <option value="read" @selected(request('status') === 'read')>Lidas</option>
                </select>
            </div>
            <div class="flex gap-1">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('sms.inbox.index') }}" class="inline-flex items-center justify-center px-2 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Lista --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        @forelse($messages as $msg)
            <a href="{{ route('sms.inbox.show', $msg) }}"
                class="block p-4 border-b border-slate-100 hover:bg-slate-50 transition no-underline {{ $msg->is_read ? 'opacity-70' : 'bg-emerald-50/40' }}">
                <div class="flex items-start gap-3">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $msg->student_id ? 'from-emerald-500 to-teal-600' : 'from-slate-400 to-slate-500' }} text-white flex items-center justify-center font-black text-sm shrink-0">
                        @if($msg->student)
                            {{ strtoupper(mb_substr($msg->student->first_name ?? 'A', 0, 1)) }}
                        @elseif($msg->from_name)
                            {{ strtoupper(mb_substr($msg->from_name, 0, 1)) }}
                        @else
                            <i class="fas fa-user text-xs"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="font-bold text-sm text-slate-800 truncate {{ $msg->is_read ? '' : 'font-extrabold' }}">
                                {{ $msg->from_name ?? ($msg->student?->full_name ?? 'Desconhecido') }}
                                @if(! $msg->is_read)
                                    <span class="inline-block w-2 h-2 rounded-full bg-rose-500 align-middle ml-1"></span>
                                @endif
                            </h4>
                            <span class="text-[10px] text-slate-500 font-semibold whitespace-nowrap">
                                {{ $msg->received_at?->diffForHumans() ?? $msg->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-[11px] font-mono text-slate-500">{{ $msg->from_phone }}</p>
                        <p class="text-sm text-slate-700 mt-1 line-clamp-2 {{ $msg->is_read ? 'font-normal' : 'font-semibold' }}">{{ $msg->content }}</p>
                        @if($msg->student)
                            <div class="mt-1.5 flex items-center gap-1.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-extrabold border border-emerald-200">
                                    <i class="fas fa-user-graduate text-[9px]"></i> {{ $msg->student->full_name }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $msg->student->student_number }}</span>
                            </div>
                        @else
                            <div class="mt-1.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-extrabold border border-amber-200">
                                    <i class="fas fa-exclamation-triangle text-[9px]"></i> Sem match
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="p-10 text-center text-slate-400">
                <i class="fas fa-inbox text-5xl mb-3 opacity-30"></i>
                <p class="font-bold">Nenhuma mensagem recebida ainda.</p>
                <p class="text-xs mt-1">Quando o httpSMS receber um SMS, aparecerá aqui automaticamente.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginação --}}
    @if($messages->hasPages())
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-3">
            {{ $messages->links() }}
        </div>
    @endif

</div>
@endsection