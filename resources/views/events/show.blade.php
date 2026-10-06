@extends('layouts.app')

@section('title', 'Detalhes do Evento')
@section('page-title', 'Detalhes do Evento')
@section('page-title-icon', 'fas fa-calendar-alt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Eventos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Detalhes</li>
@endsection

@section('title-icon', 'fas fa-calendar-day')

@section('content')
    @php
        $typeLabels = [
            'meeting' => 'Reuniao',
            'celebration' => 'Celebracao',
            'exam' => 'Exame',
            'activity' => 'Atividade',
        ];
        $audienceLabels = [
            'all' => 'Geral (Todos)',
            'students' => 'Alunos',
            'parents' => 'Pais',
            'teachers' => 'Professores',
        ];
        $typeClass = $event->type === 'meeting'
            ? 'bg-sky-100 text-sky-700'
            : ($event->type === 'celebration'
                ? 'bg-amber-100 text-amber-700'
                : ($event->type === 'exam'
                    ? 'bg-rose-100 text-rose-700'
                    : 'bg-emerald-100 text-emerald-700'));
    @endphp

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2 rounded-xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-900">{{ $event->title }}</h3>
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">{{ $typeLabels[$event->type] ?? ucfirst($event->type) }}</span>
            </header>

            <div class="space-y-5 p-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Descricao</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $event->description }}</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Data</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $event->event_date->format('d/m/Y') }}</p>
                        <p class="text-xs text-slate-500">{{ $event->event_date->translatedFormat('l') }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Horario</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Publico-Alvo</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $audienceLabels[$event->target_audience] ?? $event->target_audience }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Criado por</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $event->createdBy->name ?? 'Sistema' }}</p>
                        <p class="text-xs text-slate-500">{{ $event->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 pt-4">
                    <a href="{{ route('events.index') }}"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Voltar</a>

                    <div class="flex flex-wrap gap-2">
                        @can('edit_events')
                            <form action="{{ route('events.send-notification', $event) }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Notificar Publico</button>
                            </form>
                            <a href="{{ route('events.edit', $event) }}"
                                class="rounded-lg bg-sky-700 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-800">Editar</a>
                        @endcan
                        @can('delete_events')
                            <form action="{{ route('events.destroy', $event) }}" method="POST"
                                data-confirm="Tem certeza que deseja excluir este evento?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-rose-300 px-3 py-2 text-sm text-rose-700 hover:bg-rose-50">Excluir</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </section>

        <aside class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Proximos Eventos</h3>
            </header>
            <div class="divide-y divide-slate-100">
                @forelse(\App\Models\Event::upcoming()->where('id', '!=', $event->id)->limit(5)->get() as $upcoming)
                    <article class="px-5 py-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $upcoming->title }}</p>
                                <p class="text-xs text-slate-500">{{ $upcoming->event_date->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('events.show', $upcoming) }}" class="text-xs font-semibold text-sky-700 hover:text-sky-800">Ver</a>
                        </div>
                    </article>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-slate-500">Nenhum outro evento proximo.</p>
                @endforelse
            </div>
            <div class="border-t border-slate-200 px-5 py-3 text-center">
                <a href="{{ route('events.index') }}" class="text-xs font-semibold text-sky-700 hover:text-sky-800">Ver todos os eventos</a>
            </div>
        </aside>
    </div>
@endsection
