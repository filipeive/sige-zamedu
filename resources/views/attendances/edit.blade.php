@extends('layouts.app')

@section('title', 'Editar Presença')
@section('page-title', 'Editar Presença')
@section('page-title-icon', 'fas fa-calendar-check')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('attendances.index') }}" class="hover:text-emerald-600 transition">Presenças</a></li>
    <li class="breadcrumb-item"><a href="{{ route('attendances.show', $attendance) }}" class="hover:text-emerald-600 transition">Detalhes</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Editar</li>
@endsection

@php
    $titleIcon = 'fas fa-edit';
@endphp

@section('content')
<div class="w-full space-y-4">
    <form action="{{ route('attendances.update', $attendance) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="px-5 py-4 bg-slate-900 text-white font-bold text-sm flex items-center gap-2">
                <i class="fas fa-edit text-emerald-400"></i>
                <span>Editando presença de {{ $attendance->student->full_name }} ({{ $attendance->attendance_date->format('d/m/Y') }})</span>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Aluno</label>
                        <input type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-slate-800 py-2 px-3 text-xs font-semibold" value="{{ $attendance->student->full_name }}" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Turma</label>
                        <input type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-slate-800 py-2 px-3 text-xs font-semibold" value="{{ $attendance->class->name }}" readonly>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Status da Presença <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ $attendance->status == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold text-slate-700 mb-1">Notas / Justificativa</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full rounded-xl border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-medium focus:border-emerald-500 focus:ring-emerald-500">{{ old('notes', $attendance->notes) }}</textarea>
                    <p class="text-[11px] text-slate-500 mt-1">Adicione uma justificativa para ausência ou atraso, se aplicável.</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('attendances.show', $attendance) }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-2xs">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
            </div>
        </div>
    </form>
</div>
@endsection