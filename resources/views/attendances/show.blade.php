@extends('layouts.app')

@section('title', 'Detalhes da Presença')
@section('page-title', 'Detalhes da Presença')
@section('page-title-icon', 'fas fa-calendar-check')

@section('breadcrumbs')
    <li class="breadcrumb-item">Presenças</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Detalhes da Presença</li>
@endsection

@section('content')
<div class="w-full space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <div class="p-6 bg-slate-50/50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-calendar-check text-emerald-600"></i> Registo de {{ $attendance->student->full_name }}
            </h3>
            @can('mark_attendances')
                <a href="{{ route('attendances.edit', $attendance) }}" class="px-3.5 py-1.5 rounded-xl border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Editar
                </a>
            @endcan
        </div>

        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-4 text-center border-b md:border-b-0 md:border-r border-slate-200 pb-6 md:pb-0 md:pr-6">
                    <img src="{{ $attendance->student->photo_url }}" 
                         alt="{{ $attendance->student->full_name }}" 
                         class="w-28 h-28 rounded-full object-cover mx-auto shadow-md border-4 border-slate-100 mb-3">
                    <h5 class="text-base font-extrabold text-slate-900">{{ $attendance->student->first_name }}</h5>
                    <p class="text-xs font-mono text-slate-500 mt-0.5">{{ $attendance->student->student_number }}</p>
                </div>

                <div class="md:col-span-8 space-y-3 text-xs">
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Data da Presença</span>
                        <strong class="text-slate-800 font-bold text-sm">{{ $attendance->attendance_date->format('d/m/Y') }}</strong>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Status</span>
                        @php
                            $statusColors = ['present' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'absent' => 'bg-rose-100 text-rose-800 border-rose-200', 'late' => 'bg-amber-100 text-amber-800 border-amber-200', 'excused' => 'bg-sky-100 text-sky-800 border-sky-200'];
                            $statusNames = ['present' => 'Presente', 'absent' => 'Ausente', 'late' => 'Atrasado', 'excused' => 'Justificado'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full border font-bold {{ $statusColors[$attendance->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                            {{ $statusNames[$attendance->status] ?? 'N/D' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Turma</span>
                        <a href="{{ route('classes.show', $attendance->class) }}" class="text-emerald-700 font-bold hover:underline">{{ $attendance->class->name }}</a>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Hora de Chegada</span>
                        <span class="font-mono text-slate-800 font-bold">{{ $attendance->arrival_time ? $attendance->arrival_time->format('H:i:s') : 'Não registrada' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Marcado Por</span>
                        <span class="text-slate-800 font-semibold">{{ $attendance->markedBy->name ?? 'Sistema' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Notas</span>
                        <span class="text-slate-700 font-medium">{{ $attendance->notes ?? 'Nenhuma nota.' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1.5">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Registado em</span>
                        <span class="text-slate-600 font-medium">{{ $attendance->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-200 text-right">
            <a href="{{ url()->previous() }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>
@endsection