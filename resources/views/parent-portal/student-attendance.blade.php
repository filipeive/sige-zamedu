@extends('layouts.app')

@section('title', 'Registo de Presenças - ' . $student->full_name)
@section('page-title', 'Registo de Presenças')

@section('content')
<div class="space-y-6">

    {{-- Cabecalho do Educando --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            @if ($student->photo_url)
                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-sky-500">
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-sky-50 text-sky-600 font-bold text-xl">
                    <i class="fas fa-user-graduate"></i>
                </div>
            @endif
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">{{ $student->full_name }}</h1>
                <p class="text-xs text-slate-500 font-medium">
                    <i class="fas fa-school text-slate-400 mr-1"></i> {{ $student->currentEnrollment->class->name ?? 'Sem Turma' }} | 
                    <i class="fas fa-id-card text-slate-400 mr-1"></i> N.º {{ $student->student_number }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('parent.student-details', $student) }}" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i> Voltar ao Perfil
            </a>
        </div>
    </div>

    {{-- KPI Cards Assiduidade --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <p class="text-xs font-bold text-slate-400 uppercase">Assiduidade</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $assiduityPercentage }}%</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <p class="text-xs font-bold text-slate-400 uppercase">Presenças</p>
            <p class="text-2xl font-black text-emerald-700 mt-1">{{ $totalPresent }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <p class="text-xs font-bold text-slate-400 uppercase">Atrasos</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $totalLate }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <p class="text-xs font-bold text-slate-400 uppercase">Justificadas</p>
            <p class="text-2xl font-black text-sky-600 mt-1">{{ $totalJustified }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <p class="text-xs font-bold text-slate-400 uppercase">Faltas</p>
            <p class="text-2xl font-black text-rose-600 mt-1">{{ $totalAbsent }}</p>
        </div>
    </div>

    {{-- Tabela de Historico de Presencas --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-calendar-check text-sky-600"></i> Histórico de Assiduidade na Escola
            </h2>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                Total Registado: {{ $totalClasses }} aulas/dias
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-4">Dia da Semana</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                        <th class="py-3 px-4">Observação / Justificação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $att->attendance_date?->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-medium">
                                {{ $att->attendance_date ? ucfirst($att->attendance_date->locale('pt')->dayName) : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($att->status === 'present')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full font-bold">
                                        <i class="fas fa-check-circle"></i> Presente
                                    </span>
                                @elseif($att->status === 'late')
                                    <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full font-bold">
                                        <i class="fas fa-clock"></i> Atraso
                                    </span>
                                @elseif($att->status === 'justified')
                                    <span class="inline-flex items-center gap-1 text-sky-700 bg-sky-50 px-2.5 py-1 rounded-full font-bold">
                                        <i class="fas fa-info-circle"></i> Justificada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full font-bold">
                                        <i class="fas fa-times-circle"></i> Ausente (Falta)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">
                                {{ $att->remarks ?? $att->reason ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500 font-medium">
                                Nenhum registo de assiduidade encontrado para este aluno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
