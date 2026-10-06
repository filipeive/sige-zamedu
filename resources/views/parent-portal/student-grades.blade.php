@extends('layouts.app')

@section('title', 'Boletim de Notas')
@section('page-title', 'Boletim de Notas')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item">Parent-portal</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Boletim de Notas</li>
@endsection

@section('title', 'Boletim de Notas - ' . $student->full_name)
@section('content')
<div class="space-y-6">

    {{-- Cabecalho do Educando --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            @if ($student->photo_url)
                <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-emerald-500">
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 font-bold text-xl">
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
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition-colors">
                <i class="fas fa-print"></i> Imprimir Boletim
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl text-2xl font-bold">
                <i class="fas fa-calculator"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Média Geral</p>
                <p class="text-2xl font-black text-slate-800">
                    {{ $overallAverage !== null ? number_format($overallAverage, 1, ',', '.') : 'N/A' }}
                </p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-sky-50 text-sky-600 rounded-xl text-2xl font-bold">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Disciplinas</p>
                <p class="text-2xl font-black text-slate-800">{{ count($subjectGrades) }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl text-2xl font-bold">
                <i class="fas fa-award"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase">Situação Académica</p>
                <p class="text-base font-black {{ $academicStatus === 'Aprovado' ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $academicStatus }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tabela do Boletim --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-file-invoice text-emerald-600"></i> Notas por Disciplina (Ano Lectivo {{ now()->year }})
            </h2>
            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                Média de Passagem: 9.5
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <th class="py-3 px-4">Disciplina</th>
                        <th class="py-3 px-4 text-center">1.º Trimestre</th>
                        <th class="py-3 px-4 text-center">2.º Trimestre</th>
                        <th class="py-3 px-4 text-center">3.º Trimestre</th>
                        <th class="py-3 px-4 text-center">Média Final</th>
                        <th class="py-3 px-4 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subjectGrades as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ !$row['has_any_grade'] ? 'opacity-60' : '' }}">
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $row['subject']->name }}
                                @if(!$row['has_any_grade'])
                                    <span class="ml-2 inline-block text-[9px] font-extrabold uppercase text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                                        Sem notas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                @if($row['t1'] !== null)
                                    <span class="px-2 py-1 rounded {{ $row['t1'] >= 9.5 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ number_format($row['t1'], 1, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                @if($row['t2'] !== null)
                                    <span class="px-2 py-1 rounded {{ $row['t2'] >= 9.5 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ number_format($row['t2'], 1, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                @if($row['t3'] !== null)
                                    <span class="px-2 py-1 rounded {{ $row['t3'] >= 9.5 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ number_format($row['t3'], 1, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-black">
                                @if($row['final_media'] !== null)
                                    <span class="text-sm px-2.5 py-1 rounded-lg {{ $row['final_media'] >= 9.5 ? 'bg-emerald-100 text-emerald-800' : ($row['final_media'] >= 7.0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ number_format($row['final_media'], 1, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($row['status'] === 'Aprovado')
                                    <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full font-bold">
                                        <i class="fas fa-check-circle"></i> Aprovado
                                    </span>
                                @elseif($row['status'] === 'Admitido ao Exame')
                                    <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full font-bold">
                                        <i class="fas fa-exclamation-circle"></i> Exame
                                    </span>
                                @elseif($row['status'] === 'Reprovado')
                                    <span class="inline-flex items-center gap-1 text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full font-bold">
                                        <i class="fas fa-times-circle"></i> Reprovado
                                    </span>
                                @elseif($row['status'] === '—')
                                    <span class="inline-flex items-center gap-1 text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full font-medium">
                                        <i class="fas fa-hourglass-half"></i> Aguardando
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full font-medium">
                                        <i class="fas fa-spinner"></i> Em Curso
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 font-medium">
                                Nenhuma avaliação ou disciplina registada para este aluno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
