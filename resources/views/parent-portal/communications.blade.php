@extends('layouts.app')

@section('title', 'Comunicados & Notificações')
@section('page-title', 'Central de Comunicados')

@section('content')
<div class="space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-amber-100 text-amber-700 rounded-xl text-xl font-bold">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Comunicados & Notificações</h1>
                <p class="text-xs text-slate-500 font-medium">Avisos da direção da escola, convocações e histórico de SMS</p>
            </div>
        </div>

        <a href="{{ route('parent.dashboard') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i> Painel Principal
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Lista de Comunicados --}}
        <div class="xl:col-span-2 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-inbox text-amber-600"></i> Últimas Mensagens & Avisos
                    </h3>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                        {{ $communications->total() }} comunicados
                    </span>
                </div>

                <div class="divide-y divide-slate-100 p-6 space-y-4">
                    @forelse($communications as $comm)
                        <article class="rounded-xl border border-slate-200 p-5 bg-white transition-all hover:border-amber-300 hover:shadow-sm">
                            <div class="flex items-start justify-between gap-4">
                                <h4 class="text-base font-bold text-slate-900">{{ $comm->title }}</h4>
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-3xs font-semibold text-slate-600 whitespace-nowrap">
                                    <i class="fas fa-clock text-slate-400"></i> {{ $comm->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                </span>
                            </div>
                            <p class="mt-3 text-xs sm:text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $comm->message }}</p>
                        </article>
                    @empty
                        <div class="rounded-xl bg-slate-50 p-8 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-200 text-slate-400 mb-3">
                                <i class="fas fa-bell-slash text-xl"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Nenhum comunicado encontrado</p>
                            <p class="text-xs text-slate-500 mt-1">Ainda não existem avisos publicados para encarregados.</p>
                        </div>
                    @endforelse

                    @if($communications->hasPages())
                        <div class="pt-4 border-t border-slate-100">
                            {{ $communications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Formulario Fale Conosco --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden h-fit">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center gap-2">
                <div class="p-2 bg-sky-100 text-sky-700 rounded-lg text-xs">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Enviar Mensagem à Escola</h3>
            </div>

            <div class="p-6">
                <form action="{{ route('parent.send-message') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="subject" class="mb-1 block text-xs font-bold text-slate-700">Assunto</label>
                        <input type="text" name="subject" id="subject" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-sky-500 focus:ring-sky-500" placeholder="Ex: Dúvida sobre boletim ou falta" required>
                    </div>
                    <div>
                        <label for="message" class="mb-1 block text-xs font-bold text-slate-700">Mensagem</label>
                        <textarea name="message" id="message" rows="5" class="w-full rounded-xl border-slate-300 text-xs font-semibold focus:border-sky-500 focus:ring-sky-500" placeholder="Escreva a sua mensagem para a administração..." required></textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-sky-700 transition-colors">
                        <i class="fas fa-paper-plane"></i> Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
