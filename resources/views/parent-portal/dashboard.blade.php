@extends('layouts.app')

@section('title', 'Portal dos Pais')
@section('page-title', 'Portal dos Pais')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-3">
            @if(is_private_school())
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total a Pagar</p>
                <p class="mt-2 text-3xl font-bold {{ $totalPendingPayments > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                    {{ number_format($totalPendingPayments, 2, ',', '.') }} MT
                </p>
                <div class="mt-3">
                    @if ($totalPendingPayments > 0)
                        <a href="{{ route('parent.payments') }}"
                            class="inline-flex rounded-lg bg-rose-50 px-3 py-1.5 text-sm font-semibold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-100">
                            Ver detalhes
                        </a>
                    @else
                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Em dia</span>
                    @endif
                </div>
            </article>
            @endif

            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Filhos Matriculados</p>
                <p class="mt-2 text-3xl font-bold text-sky-700">{{ $children->count() }}</p>
            </article>

            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Novos Comunicados</p>
                <p class="mt-2 text-3xl font-bold text-indigo-700">{{ $recentCommunications->count() }}</p>
            </article>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Meus Filhos</h3>
            </header>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                @forelse($children as $child)
                    <article class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-start gap-3">
                            @if ($child->photo_url)
                                <img src="{{ $child->photo_url }}" alt="{{ $child->first_name }}"
                                    class="h-14 w-14 rounded-full object-cover ring-2 ring-slate-200">
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-900">{{ $child->full_name }}</p>
                                <p class="text-xs text-slate-500">{{ $child->currentEnrollment->class->name ?? 'Sem turma' }}</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a href="{{ route('parent.student-details', $child) }}"
                                        class="rounded-lg bg-sky-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-800">
                                        Detalhes
                                    </a>
                                    @if(is_private_school())
                                    <a href="{{ route('parent.student-payments', $child) }}"
                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                        Pagamentos
                                    </a>
                                    @endif

                                    @if ($child->is_eligible_for_renewal)
                                        @if ($child->renewal_application)
                                            @if ($child->renewal_application->status === 'PENDING')
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Aguardando confirmacao</span>
                                            @elseif($child->renewal_application->status === 'APPROVED')
                                                <span class="inline-flex items-center rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800">Aprovada, aguardando pagamento</span>
                                            @endif
                                        @else
                                            <a href="{{ route('parent.student-renewal', $child) }}"
                                                class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600">
                                                Renovar matricula
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="col-span-full rounded-lg bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Nenhum filho encontrado.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Comunicados Recentes</h3>
                <a href="{{ route('parent.communications') }}" class="text-xs font-semibold text-sky-700 hover:text-sky-800">Ver todos</a>
            </header>
            <div class="divide-y divide-slate-100 p-5">
                @forelse($recentCommunications as $comm)
                    <article class="py-3 first:pt-0 last:pb-0">
                        <div class="flex items-start justify-between gap-3">
                            <h4 class="text-sm font-semibold text-slate-900">{{ $comm->title }}</h4>
                            <span class="whitespace-nowrap text-xs text-slate-500">{{ $comm->created_at?->format('d/m/Y') ?? 'N/A' }}</span>
                        </div>
                        <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $comm->message }}</p>
                    </article>
                @empty
                    <p class="rounded-lg bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Nenhum comunicado recente.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
