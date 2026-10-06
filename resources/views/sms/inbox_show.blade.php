@extends('layouts.app')

@section('title', 'Caixa de Entrada')
@section('page-title', 'Caixa de Entrada SMS')
@section('page-title-icon', 'fas fa-comment-dots')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sms.index') }}" class="no-underline">Central de Mensagens</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sms.inbox.index') }}" class="no-underline">Caixa de Entrada</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">{{ $message->from_name ?? $message->student?->full_name ?? 'Remetente Desconhecido' }}</li>
@endsection

@section('title-icon', 'fas fa-comment-sms')

@section('content')
<div class="max-w-full mx-auto space-y-5">

    {{-- Voltar --}}
    <a href="{{ route('sms.inbox.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 no-underline">
        <i class="fas fa-arrow-left"></i> Voltar à inbox
    </a>

    {{-- Mensagem --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-teal-50">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br {{ $message->student_id ? 'from-emerald-500 to-teal-600' : 'from-slate-400 to-slate-500' }} text-white flex items-center justify-center font-black text-xl shrink-0">
                    @if($message->student)
                        {{ strtoupper(mb_substr($message->student->first_name ?? 'A', 0, 1)) }}
                    @elseif($message->from_name)
                        {{ strtoupper(mb_substr($message->from_name, 0, 1)) }}
                    @else
                        <i class="fas fa-user text-base"></i>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-black text-slate-800">
                        {{ $message->from_name ?? $message->student?->full_name ?? 'Remetente Desconhecido' }}
                    </h2>
                    <p class="text-sm font-mono text-slate-600">{{ $message->from_phone }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        <i class="fas fa-clock"></i> Recebido {{ $message->received_at?->format('d/m/Y H:i:s') ?? $message->created_at->format('d/m/Y H:i:s') }}
                        @if($message->sim) · <span class="font-mono">{{ $message->sim }}</span> @endif
                        @if($message->to_phone) · para <span class="font-mono">{{ $message->to_phone }}</span> @endif
                    </p>
                </div>
                @if($message->student)
                    <a href="{{ route('students.show', $message->student) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 no-underline">
                        <i class="fas fa-user-graduate"></i> Ver Aluno
                    </a>
                @endif
            </div>
        </div>

        <div class="p-6">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <p class="text-sm text-slate-800 whitespace-pre-wrap leading-relaxed">{{ $message->content }}</p>
            </div>

            <div class="mt-4 flex items-center gap-2 text-[11px] text-slate-500">
                <span><i class="fas fa-fingerprint"></i> <span class="font-mono">{{ $message->provider_message_id ?? '—' }}</span></span>
                @if($message->is_read)
                    <span class="text-emerald-600"><i class="fas fa-check-double"></i> Lida {{ $message->read_at?->diffForHumans() }} @if($message->reader) por {{ $message->reader->name }} @endif</span>
                @else
                    <span class="text-rose-600"><i class="fas fa-circle"></i> Não lida</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Resposta --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-black text-slate-800 mb-3 flex items-center gap-2">
            <i class="fas fa-reply text-emerald-600"></i> Responder por SMS
        </h3>
        <form method="POST" action="{{ route('sms.inbox.reply', $message) }}">
            @csrf
            <textarea name="message" rows="4" maxlength="480" required
                class="w-full rounded-lg border-slate-200 text-sm font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                placeholder="Escreva a sua resposta (máx. 480 caracteres = ~3 SMS)..."></textarea>
            <div class="flex items-center justify-between mt-3">
                <p class="text-[11px] text-slate-500 font-mono">→ <span class="font-bold">{{ $message->from_phone }}</span></p>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-black shadow-sm transition">
                    <i class="fas fa-paper-plane"></i> Enviar Resposta
                </button>
            </div>
        </form>
    </div>

    {{-- Match info --}}
    @if($message->student || $message->parent)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="text-sm font-black text-slate-800 mb-3 flex items-center gap-2">
                <i class="fas fa-link text-emerald-600"></i> Correspondência Automática
            </h3>
            <dl class="grid grid-cols-2 gap-3 text-xs">
                @if($message->student)
                    <div>
                        <dt class="text-slate-500 font-bold uppercase text-[10px] tracking-wider">Aluno</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $message->student->full_name }}
                            <small class="block font-mono text-[10px] text-slate-400">{{ $message->student->student_number }}</small>
                        </dd>
                    </div>
                @endif
                @if($message->parent)
                    <div>
                        <dt class="text-slate-500 font-bold uppercase text-[10px] tracking-wider">Encarregado</dt>
                        <dd class="font-bold text-slate-800 mt-0.5">{{ $message->parent->user?->name ?? '—' }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    @endif

</div>
@endsection