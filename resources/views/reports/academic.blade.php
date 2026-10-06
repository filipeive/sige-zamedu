@extends('layouts.app')

@section('title', 'Relatórios Académicos')
@section('page-title', 'Visão Geral Académica')
@section('page-title-icon', 'fas fa-graduation-cap')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Académico</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.export.students') }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-bold text-white shadow-md transition"
           style="background: var(--primary);">
            <i class="fas fa-file-export"></i> Exportar Alunos (CSV)
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-full border border-[var(--border-color)] bg-[var(--card-bg)] px-4 py-2 text-xs font-bold text-[var(--text-primary)] shadow-xs hover:bg-[var(--surface-bg)] transition">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-6 bg-[var(--content-bg)] -m-4 p-6 rounded-3xl min-h-screen">

        <!-- Executive Summary Diagnóstico MOPHY Style -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border p-4 shadow-xs" style="background: var(--primary-soft); border-color: var(--primary-light);">
                <div class="flex items-center gap-2 font-extrabold text-xs mb-1" style="color: var(--primary);">
                    <i class="fas fa-chart-line"></i> O que aconteceu?
                </div>
                <p class="text-xs font-medium leading-relaxed text-[var(--text-primary)]">
                    {{ $academicSummary['what_happened'] }}
                </p>
            </div>

            <div class="rounded-2xl border p-4 shadow-xs" style="background: var(--secondary-soft); border-color: var(--secondary-light);">
                <div class="flex items-center gap-2 font-extrabold text-xs mb-1" style="color: var(--secondary);">
                    <i class="fas fa-arrow-trend-up"></i> Qual a tendência?
                </div>
                <p class="text-xs font-medium leading-relaxed text-[var(--text-primary)]">
                    {{ $academicSummary['trend'] }}
                </p>
            </div>

            <div class="rounded-2xl border p-4 shadow-xs" style="background: var(--accent-light); border-color: var(--accent);">
                <div class="flex items-center gap-2 font-extrabold text-xs mb-1" style="color: var(--accent);">
                    <i class="fas fa-triangle-exclamation"></i> Recomendações
                </div>
                <p class="text-xs font-medium leading-relaxed text-[var(--text-primary)]">
                    {{ $academicSummary['attention'] }}
                </p>
            </div>
        </div>

        <!-- Table: Class Summary MOPHY Card -->
        <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)]">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-[var(--border-color)]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shrink-0 shadow-xs"
                         style="background: var(--primary-soft); color: var(--primary);">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[var(--text-primary)] font-heading">Resumo das Turmas & Capacidade</h3>
                        <p class="text-xs text-[var(--text-muted)]">Lotação e média por turma</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--surface-bg)] text-[var(--text-muted)] font-bold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Turma</th>
                            <th class="px-4 py-3 text-left">Nível</th>
                            <th class="px-4 py-3 text-left">Director de Turma</th>
                            <th class="px-4 py-3 text-left">Alunos Ativos</th>
                            <th class="px-4 py-3 text-left">Média Turma</th>
                            <th class="px-4 py-3 text-left">Lotação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        @foreach($classes as $class)
                            <tr class="hover:bg-[var(--surface-bg)] transition">
                                <td class="px-4 py-3.5 font-extrabold text-[var(--text-primary)]">{{ $class->name }}</td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-medium">{{ $class->grade_level_name ?? $class->grade_level }}</td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)]">
                                    {{ $class->teacher ? ($class->teacher->first_name . ' ' . $class->teacher->last_name) : 'N/A' }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-[var(--text-primary)]">{{ $class->active_students_count }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block rounded-full px-3 py-0.5 text-xs font-black"
                                          style="background: var(--primary-soft); color: var(--primary);">
                                        {{ number_format($class->average_grade, 1) }} / 20
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @php $percent = $class->capacity > 0 ? ($class->active_students_count / $class->capacity) * 100 : 0; @endphp
                                    <div class="w-32 bg-[var(--surface-bg)] rounded-full h-2 overflow-hidden border border-[var(--border-color)]">
                                        <div class="h-full rounded-full transition-all"
                                             style="width: {{ min(100, $percent) }}%; background: {{ $percent > 90 ? 'var(--danger)' : ($percent > 70 ? 'var(--warning)' : 'var(--success)') }};"></div>
                                    </div>
                                    <span class="text-[11px] font-bold text-[var(--text-muted)] mt-1 block">{{ round($percent) }}% ({{ $class->active_students_count }}/{{ $class->capacity }})</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection