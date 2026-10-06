@extends('layouts.app')

@section('title', 'Desempenho Académico')
@section('page-title', 'Relatório de Desempenho')
@section('page-title-icon', 'fas fa-chart-line')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.academic') }}">Académico</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Desempenho</li>
@endsection

@section('page-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.export.grades') }}" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-bold text-white shadow-md transition"
           style="background: var(--primary);">
            <i class="fas fa-file-excel"></i> Exportar Pauta (CSV)
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-full border border-[var(--border-color)] bg-[var(--card-bg)] px-4 py-2 text-xs font-bold text-[var(--text-primary)] shadow-xs hover:bg-[var(--surface-bg)] transition">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
    <div class="space-y-6 bg-[var(--content-bg)] -m-4 p-6 rounded-3xl min-h-screen">

        <!-- Filter Card MOPHY Style -->
        <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)]">
            <form action="{{ route('reports.academic.performance') }}" method="GET" class="grid gap-4 md:grid-cols-3 items-end">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Turma</label>
                    <select name="class_id" class="w-full rounded-2xl border border-[var(--border-color)] bg-[var(--surface-bg)] px-4 py-2.5 text-xs font-bold text-[var(--text-primary)] focus:bg-[var(--card-bg)] focus:outline-none">
                        <option value="">Todas as Turmas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Trimestre</label>
                    <select name="term" class="w-full rounded-2xl border border-[var(--border-color)] bg-[var(--surface-bg)] px-4 py-2.5 text-xs font-bold text-[var(--text-primary)] focus:bg-[var(--card-bg)] focus:outline-none">
                        <option value="">Todos os Trimestres</option>
                        <option value="1" {{ request('term') == '1' ? 'selected' : '' }}>1º Trimestre</option>
                        <option value="2" {{ request('term') == '2' ? 'selected' : '' }}>2º Trimestre</option>
                        <option value="3" {{ request('term') == '3' ? 'selected' : '' }}>3º Trimestre</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full rounded-2xl py-2.5 text-xs font-extrabold text-white shadow-md transition"
                            style="background: var(--primary);">
                        <i class="fas fa-filter me-1"></i> Aplicar Filtro
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card MOPHY Style -->
        <div class="rounded-3xl bg-[var(--card-bg)] p-6 shadow-xs border border-[var(--border-color)]">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-[var(--border-color)]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shrink-0 shadow-xs"
                         style="background: var(--primary-soft); color: var(--primary);">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-[var(--text-primary)] font-heading">Notas e Avaliações Registadas</h3>
                        <p class="text-xs text-[var(--text-muted)]">Pauta completa da instituição</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--surface-bg)] text-[var(--text-muted)] font-bold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Aluno</th>
                            <th class="px-4 py-3 text-left">Turma</th>
                            <th class="px-4 py-3 text-left">Disciplina</th>
                            <th class="px-4 py-3 text-left">Avaliação</th>
                            <th class="px-4 py-3 text-left">Trimestre</th>
                            <th class="px-4 py-3 text-left">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        @forelse($grades as $grade)
                            <tr class="hover:bg-[var(--surface-bg)] transition">
                                <td class="px-4 py-3.5 font-bold text-[var(--text-primary)]">
                                    {{ $grade->student ? ($grade->student->first_name . ' ' . $grade->student->last_name) : 'N/A' }}
                                </td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-medium">{{ $grade->class ? $grade->class->name : 'N/A' }}</td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-semibold">{{ $grade->subject ? $grade->subject->name : 'N/A' }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block rounded-full bg-[var(--surface-bg)] px-3 py-0.5 text-[11px] font-bold text-[var(--text-secondary)] uppercase border border-[var(--border-color)]">
                                        {{ $grade->assessment_type ?? $grade->fee_type ?? 'ACS' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-[var(--text-secondary)] font-bold">{{ $grade->term ?? $grade->trimester ?? '1' }}º</td>
                                <td class="px-4 py-3.5">
                                    @php $val = $grade->grade ?? $grade->final_grade ?? 0; @endphp
                                    <span class="inline-block rounded-full px-3 py-1 text-xs font-black
                                        {{ $val >= 10 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ number_format($val, 1) }} / 20
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-[var(--text-muted)]">Nenhum registo de nota encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $grades->links() }}
            </div>
        </div>

    </div>
@endsection