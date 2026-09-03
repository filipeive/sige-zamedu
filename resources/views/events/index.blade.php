@extends('layouts.app')

@section('title', 'Gestão de Eventos & Calendário')
@section('page-title', 'Gestão de Eventos')
@section('title-icon', 'fas fa-calendar-alt')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Eventos</li>
@endsection

@section('content')
@php
    $typeLabels = [
        'meeting' => 'Reunião',
        'celebration' => 'Celebração / Festa',
        'exam' => 'Exame / Avaliação',
        'activity' => 'Atividade Extracurricular',
    ];
    $audienceLabels = [
        'all' => 'Geral (Todos)',
        'students' => 'Alunos',
        'parents' => 'Pais & Encarregados',
        'teachers' => 'Professores',
    ];
@endphp

<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-calendar-alt text-emerald-600"></i> Gestão de Eventos
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Calendário escolar, reuniões, exames e festividades.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('events.calendar') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-calendar-day"></i> Ver Calendário
            </a>
            @can('create_events')
                <a href="{{ route('events.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-plus"></i> Novo Evento
                </a>
            @endcan
        </div>
    </div>

    <!-- Stat KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Próximos Eventos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Event::upcoming()->count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Eventos Hoje</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Event::today()->count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Esta Semana</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Event::thisWeek()->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form action="{{ route('events.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-center">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full rounded-lg border-slate-200 py-1.5 px-3 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                       placeholder="Título do evento...">
            </div>

            <div>
                <select name="type" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Tipos</option>
                    @foreach ($typeLabels as $key => $label)
                        <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="audience" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Públicos</option>
                    @foreach ($audienceLabels as $key => $label)
                        <option value="{{ $key }}" @selected(request('audience') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
                @if (request()->anyFilled(['search', 'type', 'audience']))
                    <a href="{{ route('events.index') }}" class="no-underline inline-flex justify-center items-center rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 p-1.5 text-xs text-slate-600 transition" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-calendar-alt text-emerald-600"></i> Lista de Eventos
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Data & Hora</th>
                        <th class="py-2.5 px-3">Evento</th>
                        <th class="py-2.5 px-3">Tipo</th>
                        <th class="py-2.5 px-3">Público-Alvo</th>
                        <th class="py-2.5 px-3">Organizador</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $event)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                <span>{{ $event->event_date->format('d/m/Y') }}</span>
                                <span class="block text-[10px] text-slate-400 font-normal"><i class="far fa-clock mr-1"></i> {{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}</span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="font-bold text-slate-800 block">{{ $event->title }}</span>
                                <span class="text-[10px] text-slate-400 block truncate max-w-xs">{{ $event->description }}</span>
                            </td>
                            <td class="py-2.5 px-3">
                                @php
                                    $badgeClass = match($event->type) {
                                        'meeting' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        'celebration' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        'exam' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                        default => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">
                                    {{ $typeLabels[$event->type] ?? ucfirst($event->type) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $audienceLabels[$event->target_audience] ?? $event->target_audience }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">{{ $event->createdBy->name ?? 'Sistema' }}</td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('events.show', $event) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @can('edit_events')
                                        <a href="{{ route('events.edit', $event) }}" class="no-underline h-7 w-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 flex items-center justify-center transition" title="Editar">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @endcan
                                    @can('delete_events')
                                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline" data-confirm="Deseja excluir este evento?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-7 w-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Excluir">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">
                                <i class="fas fa-calendar-times text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum evento encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($events->hasPages())
            <div class="px-4 py-2 border-t border-slate-100">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
