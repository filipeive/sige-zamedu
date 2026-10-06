@extends('layouts.app')

@section('title', 'Novo Evento')
@section('page-title', 'Novo Evento')
@section('page-title-icon', 'fas fa-calendar-alt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Eventos</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Novo</li>
@endsection

@section('title-icon', 'fas fa-calendar-plus')

@section('content')
    @php
        $types = [
            'meeting' => 'Reuniao',
            'celebration' => 'Celebracao',
            'exam' => 'Exame',
            'activity' => 'Atividade',
        ];
        $audiences = [
            'all' => 'Geral (Todos)',
            'students' => 'Alunos',
            'parents' => 'Pais',
            'teachers' => 'Professores',
        ];
    @endphp

    <div class="mx-auto max-w-4xl">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <form action="{{ route('events.store') }}" method="POST" class="grid gap-4 md:grid-cols-12">
                @csrf

                <div class="md:col-span-12">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Titulo do Evento *</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 @error('title') border-rose-400 @enderror"
                        required>
                    @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-12">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Descricao *</label>
                    <textarea name="description" rows="4"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 @error('description') border-rose-400 @enderror"
                        required>{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-4">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Data *</label>
                    <input type="date" name="event_date" value="{{ old('event_date') }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 @error('event_date') border-rose-400 @enderror"
                        required>
                    @error('event_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-4">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Hora de Inicio *</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 @error('start_time') border-rose-400 @enderror"
                        required>
                    @error('start_time')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-4">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Hora de Termino *</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 @error('end_time') border-rose-400 @enderror"
                        required>
                    @error('end_time')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-6">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tipo *</label>
                    <select name="type"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 @error('type') border-rose-400 @enderror"
                        required>
                        <option value="">Selecione...</option>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-6">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Publico-Alvo *</label>
                    <select name="target_audience"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500 @error('target_audience') border-rose-400 @enderror"
                        required>
                        <option value="">Selecione...</option>
                        @foreach ($audiences as $key => $label)
                            <option value="{{ $key }}" @selected(old('target_audience') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('target_audience')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-12">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="send_notification" value="1" @checked(old('send_notification'))
                            class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        Enviar notificacao para o publico-alvo
                    </label>
                    <p class="mt-1 text-xs text-slate-500">Envia notificacao no sistema para os usuarios selecionados.</p>
                </div>

                <div class="md:col-span-12 flex items-center justify-between pt-2">
                    <a href="{{ route('events.index') }}"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancelar</a>
                    <button type="submit" class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">Salvar Evento</button>
                </div>
            </form>
        </section>
    </div>
@endsection
