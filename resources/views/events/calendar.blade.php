@extends('layouts.app')

@section('title', 'Calendario de Eventos')
@section('page-title', 'Calendario de Eventos')
@section('page-title-icon', 'fas fa-calendar-alt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Eventos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Calendario</li>
@endsection

@section('title-icon', 'fas fa-calendar-alt')

@section('content')
    <div class="events-calendar space-y-4">
        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Calendario Escolar</h3>
                <div class="flex gap-2">
                    <a href="{{ route('events.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Ver em Lista</a>
                    @can('create_events')
                        <a href="{{ route('events.create') }}" class="rounded-lg bg-sky-700 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-800">Novo Evento</a>
                    @endcan
                </div>
            </header>
            <div class="p-5">
                <div id="calendar"></div>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-center gap-4 text-sm">
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-cyan-500"></span>Reuniao</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-amber-500"></span>Celebracao</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-rose-500"></span>Exame</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-500"></span>Atividade</span>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <style>
        .events-calendar .fc-event {
            cursor: pointer;
        }

        .events-calendar .event-meeting {
            background-color: #06b6d4;
            border-color: #06b6d4;
        }

        .events-calendar .event-celebration {
            background-color: #f59e0b;
            border-color: #f59e0b;
            color: #111827;
        }

        .events-calendar .event-exam {
            background-color: #ef4444;
            border-color: #ef4444;
        }

        .events-calendar .event-activity {
            background-color: #10b981;
            border-color: #10b981;
        }

        [data-bs-theme="dark"] .events-calendar .bg-white {
            background-color: var(--card-bg) !important;
        }

        [data-bs-theme="dark"] .events-calendar .border-slate-200,
        [data-bs-theme="dark"] .events-calendar .border-slate-300 {
            border-color: var(--border-color) !important;
        }

        [data-bs-theme="dark"] .events-calendar .text-slate-900 {
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .events-calendar .text-slate-700 {
            color: var(--text-secondary) !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                events: @json($events),
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                }
            });
            calendar.render();
        });
    </script>
@endpush
