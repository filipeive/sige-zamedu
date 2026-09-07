@extends('layouts.app')

@section('title', 'Mapa de Aproveitamento Pedagógico')
@section('page-title', 'Mapa de Aproveitamento Pedagógico (MINEDH)')
@section('page-title-icon', 'fas fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Início</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.academic') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Mapa Pedagógico</li>
@endsection

@section('content')
<div class="space-y-5">

    {{-- 1. Header Principal --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    <i class="fas fa-file-invoice me-1"></i> Documento Oficial de Inspeção
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    Ano Lectivo {{ $year }}
                </span>
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-chart-pie text-emerald-600"></i> Mapa de Aproveitamento Pedagógico por Género (HM / M)
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Estatísticas consolidadas por disciplina e por gênero para homologação pedagógica e inspeção escolar.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($selectedClass)
                <a href="{{ route('reports.pedagogical-map.export', ['class_id' => $selectedClass->id, 'term' => $term, 'year' => $year]) }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-file-excel"></i> Exportar Mapa Excel
                </a>
            @endif
            <a href="{{ route('reports.academic') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- 2. Filtro de Seleção --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-3.5 shadow-2xs">
        <form action="{{ route('reports.pedagogical-map') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                    <i class="fas fa-chalkboard text-emerald-600 me-1"></i> Selecionar Turma
                </label>
                <select name="class_id" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected($selectedClass && $selectedClass->id === $c->id)>
                            {{ $c->name }} ({{ $c->grade_level_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                    <i class="fas fa-clock text-emerald-600 me-1"></i> Trimestre
                </label>
                <select name="term" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="1" @selected($term == 1)>1º Trimestre</option>
                    <option value="2" @selected($term == 2)>2º Trimestre</option>
                    <option value="3" @selected($term == 3)>3º Trimestre</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-1">
                    <i class="fas fa-calendar text-emerald-600 me-1"></i> Ano Lectivo
                </label>
                <select name="year" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="2026" @selected($year == 2026)>2026 (Ano Atual)</option>
                    <option value="2025" @selected($year == 2025)>2025 (Histórico)</option>
                </select>
            </div>
        </form>
    </div>

    {{-- 3. Tabela do Mapa de Aproveitamento Pedagógico --}}
    @if($selectedClass)
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-table text-emerald-600"></i> Turma {{ $selectedClass->name }} — Mapa do {{ $term }}º Trimestre
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                Limiar Mínimo: {{ $passingThreshold }} valores
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white font-black uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4" rowspan="2">Disciplina</th>
                        <th class="py-2 px-3 text-center border-l border-slate-700" colspan="2">Avaliados</th>
                        <th class="py-2 px-3 text-center border-l border-slate-700" colspan="2">Positivas / Aprovados</th>
                        <th class="py-2 px-3 text-center border-l border-slate-700" colspan="2">Negativas / Retidos</th>
                        <th class="py-2 px-3 text-center border-l border-slate-700 bg-emerald-900" colspan="2">% Aproveitamento</th>
                    </tr>
                    <tr class="bg-slate-700 text-white font-extrabold text-[9px] border-b border-slate-600">
                        <th class="py-1.5 px-3 text-center border-l border-slate-600">HM (Total)</th>
                        <th class="py-1.5 px-3 text-center text-purple-300">M (Mulheres)</th>
                        <th class="py-1.5 px-3 text-center border-l border-slate-600 text-emerald-300">HM (Total)</th>
                        <th class="py-1.5 px-3 text-center text-purple-300">M (Mulheres)</th>
                        <th class="py-1.5 px-3 text-center border-l border-slate-600 text-rose-300">HM (Total)</th>
                        <th class="py-1.5 px-3 text-center text-purple-300">M (Mulheres)</th>
                        <th class="py-1.5 px-3 text-center border-l border-slate-600 bg-emerald-800 text-emerald-200">HM %</th>
                        <th class="py-1.5 px-3 text-center bg-emerald-800 text-purple-200">M %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mapData as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-black text-slate-800">
                                {{ $item['subject']->name }}
                                <span class="text-[10px] text-slate-400 font-mono">({{ $item['subject']->code ?? '—' }})</span>
                            </td>
                            <td class="py-3 px-3 text-center font-extrabold text-slate-800 border-l border-slate-100">{{ $item['hm_evaluated'] }}</td>
                            <td class="py-3 px-3 text-center font-extrabold text-purple-700 bg-purple-50/30">{{ $item['m_evaluated'] }}</td>
                            
                            <td class="py-3 px-3 text-center font-black text-emerald-700 bg-emerald-50/30 border-l border-slate-100">{{ $item['hm_positives'] }}</td>
                            <td class="py-3 px-3 text-center font-black text-purple-700 bg-purple-50/30">{{ $item['m_positives'] }}</td>

                            <td class="py-3 px-3 text-center font-black text-rose-600 bg-rose-50/30 border-l border-slate-100">{{ $item['hm_negatives'] }}</td>
                            <td class="py-3 px-3 text-center font-black text-purple-700 bg-purple-50/30">{{ $item['m_negatives'] }}</td>

                            <td class="py-3 px-3 text-center font-black text-emerald-800 bg-emerald-100/50 border-l border-slate-100">{{ $item['hm_perc'] }}%</td>
                            <td class="py-3 px-3 text-center font-black text-purple-800 bg-purple-100/50">{{ $item['m_perc'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                <i class="fas fa-folder-open text-2xl mb-2 text-slate-300"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma avaliação registada para esta turma.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
