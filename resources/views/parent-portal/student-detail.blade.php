@extends('layouts.app')

@section('title', 'Caderneta Escolar Digital')
@section('page-title', 'Caderneta Escolar Digital')
@section('page-title-icon', 'fas fa-user-graduate')

@section('breadcrumbs')
    <li class="breadcrumb-item">Parent-portal</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Caderneta Escolar Digital</li>
@endsection

@section('title', 'Caderneta do Aluno - ' . $student->full_name)
@section('content')
<div class="space-y-6">

    {{-- Top Profile Header Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-5">
                @if ($student->photo_url)
                    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="h-20 w-20 rounded-full object-cover ring-4 ring-emerald-500/20 shadow-sm">
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 font-bold text-2xl ring-4 ring-emerald-500/20">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-black text-slate-900 sm:text-2xl">{{ $student->full_name }}</h1>
                        <span class="rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-800">
                            {{ $student->currentEnrollment->class->name ?? 'Sem Turma' }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500 font-medium flex items-center gap-3 flex-wrap">
                        <span><i class="fas fa-id-card text-slate-400"></i> Número: <strong>{{ $student->student_number }}</strong></span>
                        <span>•</span>
                        <span><i class="fas fa-calendar-alt text-slate-400"></i> Nascimento: <strong>{{ $student->birthdate?->format('d/m/Y') ?? 'N/A' }}</strong> ({{ $student->age }} anos)</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('parent.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    <i class="fas fa-arrow-left"></i> Painel Principal
                </a>
            </div>
        </div>

        {{-- Bar of Direct Actions --}}
        <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('parent.student-grades', $student) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                    <i class="fas fa-file-invoice"></i> Ver Boletim de Notas Completo
                </a>
                <a href="{{ route('parent.student-attendance', $student) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-sky-700 transition-colors">
                    <i class="fas fa-calendar-check"></i> Histórico de Presenças
                </a>
                @if(is_private_school())
                <a href="{{ route('parent.student-payments', $student) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-credit-card"></i> Extrato de Propinas
                </a>
                @endif
            </div>

            @if ($student->is_eligible_for_renewal)
                <a href="{{ route('parent.student-renewal', $student) }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-amber-600 transition-colors">
                    <i class="fas fa-sync-alt"></i> Renovar Matrícula
                </a>
            @endif
        </div>
    </div>

    {{-- Grid 2 colunas: Presencas Recentes vs Ultimas Notas --}}
    <div class="grid gap-6 md:grid-cols-2">

        {{-- Presencas Recentes --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-sky-600"></i> Presenças Recentes
                </h3>
                <a href="{{ route('parent.student-attendance', $student) }}" class="text-xs font-bold text-sky-600 hover:text-sky-700">Ver Todas</a>
            </div>

            <div class="divide-y divide-slate-100 p-5">
                @forelse($student->attendances()->latest()->take(5)->get() as $attendance)
                    <div class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800">{{ $attendance->attendance_date?->format('d/m/Y') ?? 'N/A' }}</span>
                            <span class="text-slate-400 font-medium">({{ $attendance->attendance_date ? ucfirst($attendance->attendance_date->locale('pt')->dayName) : '' }})</span>
                        </div>
                        <div>
                            @if($attendance->status === 'present')
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-3xs font-bold text-emerald-800">Presente</span>
                            @elseif($attendance->status === 'late')
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-3xs font-bold text-amber-800">Atraso</span>
                            @elseif($attendance->status === 'justified')
                                <span class="rounded-full bg-sky-100 px-3 py-1 text-3xs font-bold text-sky-800">Justificada</span>
                            @else
                                <span class="rounded-full bg-rose-100 px-3 py-1 text-3xs font-bold text-rose-800">Falta</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 p-6 text-center text-xs text-slate-500 font-medium">Nenhum registo de presença recente.</p>
                @endforelse
            </div>
        </div>

        {{-- Ultimas Avaliacoes / Notas --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-emerald-600"></i> Últimas Avaliações
                </h3>
                <a href="{{ route('parent.student-grades', $student) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Ver Boletim</a>
            </div>

            <div class="divide-y divide-slate-100 p-5">
                @forelse($student->grades()->latest()->take(5)->get() as $grade)
                    <div class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0 text-xs">
                        <div>
                            <p class="font-bold text-slate-900">{{ $grade->subject->name ?? 'Disciplina' }}</p>
                            <p class="text-3xs text-slate-500 font-medium">{{ strtoupper($grade->assessment_type) }} • {{ $grade->term }}º Trimestre</p>
                        </div>
                        <span class="text-base font-black px-2.5 py-1 rounded-lg {{ $grade->grade >= 9.5 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                            {{ number_format($grade->grade, 1, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 p-6 text-center text-xs text-slate-500 font-medium">Nenhuma nota registada recentemente.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
