@extends('layouts.app')

@section('title', 'Relatório de Inadimplentes')
@section('page-title', 'Relatório de Inadimplentes')
@section('page-title-icon', 'fas fa-user-times')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.financial') }}">Financeiro</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Inadimplentes</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-2">
        <button type="button" onclick="showToast('Lembretes de cobrança enviados com sucesso!', 'success')" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-bold text-white shadow-md transition"
                style="background: var(--warning-dark);">
            <i class="fas fa-paper-plane"></i> Notificar Todos por SMS
        </button>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-full border border-[var(--border-color)] bg-[var(--card-bg)] px-4 py-2 text-xs font-bold text-[var(--text-primary)] shadow-xs hover:bg-[var(--surface-bg)] transition">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-6 bg-[var(--content-bg)] -m-4 p-6 rounded-3xl min-h-screen">

        <!-- Banner Info Alert -->
        <div class="rounded-2xl border p-4 shadow-xs flex items-center gap-3"
             style="background: var(--warning-soft); border-color: var(--warning);">
            <div class="rounded-xl p-2.5 text-white text-base shrink-0" style="background: var(--warning);">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <p class="text-xs font-medium leading-relaxed" style="color: var(--warning-dark);">
                Este relatório compila os alunos matriculados com propinas e mensalidades pendentes para o mês de 
                <strong>{{ now()->translatedFormat('F Y') }}</strong>.
            </p>
        </div>

        <!-- Table Card MOPHY Style -->
        <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)]">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-[var(--border-color)]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shrink-0 shadow-xs"
                         style="background: var(--warning-soft); color: var(--warning-dark);">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[var(--text-primary)] font-heading">Alunos Inadimplentes</h3>
                        <p class="text-xs text-[var(--text-muted)]">Total de {{ $defaulters->count() }} registos em atraso</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--surface-bg)] text-[var(--text-muted)] font-bold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Aluno</th>
                            <th class="px-4 py-3 text-left">Turma</th>
                            <th class="px-4 py-3 text-left">Encarregado de Educação</th>
                            <th class="px-4 py-3 text-left">Contacto</th>
                            <th class="px-4 py-3 text-left">Valor Propinas</th>
                            <th class="px-4 py-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        @forelse($defaulters as $enrollment)
                            <tr class="hover:bg-[var(--surface-bg)] transition">
                                <td class="px-4 py-3.5 font-bold text-[var(--text-primary)]">
                                    {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
                                    <span class="block text-[11px] font-medium text-[var(--text-muted)]">{{ $enrollment->student->student_number }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-medium">{{ $enrollment->class->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-semibold">{{ $enrollment->student->parent->first_name ?? 'N/A' }}</td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-bold">{{ $enrollment->student->parent->phone ?? 'N/A' }}</td>
                                <td class="px-4 py-3.5 font-black text-rose-600">
                                    {{ number_format($enrollment->monthly_fee, 2, ',', '.') }} MT
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('students.show', $enrollment->student_id) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-[var(--surface-bg)] text-[var(--text-secondary)] hover:bg-[var(--primary-soft)] hover:text-[var(--primary)] transition border border-[var(--border-color)]">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-[var(--text-muted)]">
                                    <i class="fas fa-circle-check text-emerald-500 text-3xl mb-2 block"></i>
                                    Parabéns! Não existem alunos inadimplentes neste período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection