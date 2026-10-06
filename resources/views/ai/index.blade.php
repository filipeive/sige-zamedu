@extends('layouts.app')

@section('title', 'Assistente IA')
@section('page-title', 'Assistente IA')
@section('page-title-icon', 'fas fa-folder-open')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Assistente IA</li>
@endsection

@section('title-icon', 'fas fa-robot')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-[var(--border-color,theme('colors.slate.200'))]">
        <div>
            <h1 class="text-xl font-black text-[var(--text-primary)] tracking-tight flex items-center gap-2">
                <i class="fas fa-robot text-[var(--primary)]"></i> Assistente IA
            </h1>
            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                Driver: <span class="font-bold">{{ $driver }}</span> · Modelo: <span class="font-bold">{{ $model }}</span>
                @if ($enabled)
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 rounded-full bg-[var(--success-soft)] text-[var(--success-dark)] text-[10px] font-extrabold">
                        <i class="fas fa-circle text-[6px]"></i> Activo
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 ml-2 px-2 py-0.5 rounded-full bg-[var(--warning-soft)] text-[var(--warning-dark)] text-[10px] font-extrabold">
                        <i class="fas fa-circle text-[6px]"></i> Desactivado (defina AI_ENABLED=true)
                    </span>
                @endif
            </p>
        </div>
        <form action="{{ route('ai.new') }}" method="POST">
            @csrf
            <button class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-xs font-bold text-white shadow-sm transition">
                <i class="fas fa-plus"></i> Nova Conversa
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        {{-- Sidebar: histórico --}}
        <div class="lg:col-span-3">
            <div class="bg-[var(--card-bg)] rounded-xl border border-[var(--border-color)] shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-[var(--border-color)] bg-[var(--surface-bg)]">
                    <h3 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <i class="fas fa-clock-rotate-left text-[var(--primary)]"></i> Conversas Recentes
                    </h3>
                </div>
                <div class="p-2 max-h-[600px] overflow-y-auto">
                    @forelse ($conversations as $conv)
                        <a href="{{ route('ai.show', $conv) }}"
                            class="block rounded-lg px-3 py-2 mb-1 transition border
                                {{ ($activeConversation && $activeConversation->id === $conv->id)
                                    ? 'bg-[var(--primary-soft)] border-[var(--primary)]'
                                    : 'hover:bg-[var(--surface-bg)] border-transparent' }}">
                            <div class="text-xs font-bold text-[var(--text-primary)] truncate">{{ $conv->title }}</div>
                            <small class="text-[10px] text-[var(--text-muted)]">{{ $conv->updated_at->diffForHumans() }}</small>
                        </a>
                    @empty
                        <p class="text-xs text-[var(--text-muted)] text-center py-4">Sem conversas ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Conversa activa --}}
        <div class="lg:col-span-9">
            <div class="bg-[var(--card-bg)] rounded-xl border border-[var(--border-color)] shadow-sm overflow-hidden flex flex-col" style="height: 70vh;">
                <div class="px-4 py-3 border-b border-[var(--border-color)] bg-[var(--surface-bg)] flex items-center justify-between">
                    <h3 class="text-sm font-bold text-[var(--text-primary)] truncate">
                        {{ $activeConversation?->title ?? 'Nenhuma conversa seleccionada' }}
                    </h3>
                    @if ($activeConversation)
                        <form action="{{ route('ai.destroy', $activeConversation) }}" method="POST"
                            onsubmit="return confirm('Apagar esta conversa?')">
                            @csrf @method('DELETE')
                            <button class="text-[var(--danger)] hover:text-[var(--danger-dark)] text-xs font-bold">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-[var(--surface-bg)]/30">
                    @forelse ($messages as $msg)
                        <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed
                                {{ $msg['role'] === 'user'
                                    ? 'bg-[var(--primary)] text-white rounded-br-sm'
                                    : 'bg-[var(--card-bg)] border border-[var(--border-color)] text-[var(--text-primary)] rounded-bl-sm shadow-xs' }}">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider mb-1
                                    {{ $msg['role'] === 'user' ? 'text-white/80' : 'text-[var(--primary)]' }}">
                                    {{ $msg['role'] === 'user' ? 'Tu' : 'Assistente IA' }}
                                </div>
                                <div class="whitespace-pre-wrap">{{ $msg['content'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-xs text-[var(--text-muted)]">
                            <i class="fas fa-robot fa-3x text-[var(--primary)] opacity-30 mb-3"></i>
                            <p class="mb-0">Inicia uma nova conversa para falar com o assistente.</p>
                            <p class="mt-2 text-[11px] text-[var(--text-muted)]">Sugestões: gere uma mensagem SMS para um encarregado, resuma o aproveitamento de uma turma, ou peça ideias para uma actividade escolar.</p>
                        </div>
                    @endforelse
                </div>

                @if ($activeConversation)
                    <form action="{{ route('ai.chat', $activeConversation) }}" method="POST" class="border-t border-[var(--border-color)] p-3 bg-[var(--card-bg)]">
                        @csrf
                        <div class="flex gap-2">
                            <textarea name="message" rows="2" required
                                class="flex-1 rounded-lg border-[var(--border-color)] text-sm shadow-xs focus:border-[var(--primary)] focus:ring-[var(--primary)]"
                                style="--tw-ring-color: color-mix(in srgb, var(--primary) 30%, transparent);"
                                placeholder="Escreve a tua mensagem...">{{ old('message') }}</textarea>
                            <button class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-xs font-bold text-white shadow-sm transition">
                                <i class="fas fa-paper-plane"></i> Enviar
                            </button>
                        </div>
                        @error('message')
                            <div class="text-[11px] text-[var(--danger)] mt-1 font-semibold">{{ $message }}</div>
                        @enderror
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection