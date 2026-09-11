@extends('layouts.app')

@section('title', 'Mapa Oficial de Aproveitamento Pedagógico Território — MINEDH')

@section('content')
<div class="w-full px-4 py-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                <i class="fa-solid fa-certificate text-emerald-600 mr-1.5"></i> MINEDH — Regulamento SNE Lei 18/2018
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Mapa Geral de Aproveitamento Pedagógico</h2>
            <p class="text-sm text-slate-500 mt-1">Consolidado Território: Ensino Secundário Geral — {{ $term }}º Trimestre / {{ $year }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('territorial.map.pdf', request()->all()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition" target="_blank">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF Oficial
            </a>
            <a href="{{ route('territorial.map.csv', request()->all()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-file-csv"></i> Exportar Excel / CSV
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('territorial.map') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 align-items-end">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Ano Lectivo</label>
                <select name="year" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                    <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Trimestre</label>
                <select name="term" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="1" {{ $term == 1 ? 'selected' : '' }}>1º Trimestre</option>
                    <option value="2" {{ $term == 2 ? 'selected' : '' }}>2º Trimestre</option>
                    <option value="3" {{ $term == 3 ? 'selected' : '' }}>3º Trimestre</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Província</label>
                <select name="province_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Todas</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->id }}" {{ $selectedProvinceId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Distrito</label>
                <select name="district_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Todos</option>
                    @foreach($districts as $d)
                        <option value="{{ $d->id }}" {{ $selectedDistrictId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">ZIP</label>
                <select name="zip_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Todas</option>
                    @foreach($zips as $z)
                        <option value="{{ $z->id }}" {{ $selectedZipId == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm inline-flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-arrows-rotate"></i> Atualizar Mapa
                </button>
            </div>
        </form>
    </div>

    <!-- Official MINEDH Table Sheet Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="p-6 bg-slate-50/50 border-b border-slate-200 text-center">
            <h5 class="text-base font-extrabold text-slate-900 tracking-wide uppercase">REPÚBLICA DE MOÇAMBIQUE</h5>
            <h6 class="text-xs font-bold text-slate-600 mt-1 uppercase">MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h6>
            <p class="text-xs font-extrabold text-emerald-700 mt-1">MAPA DE APROVEITAMENTO PEDAGÓGICO — ENSINO SECUNDÁRIO GERAL (ESG)</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-center border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider">
                        <th rowspan="2" class="p-3 text-left border-r border-slate-200">CLASSE</th>
                        <th colspan="2" class="p-2 border-r border-slate-200">Nº ENTRADAS</th>
                        <th colspan="2" class="p-2 border-r border-slate-200">DESISTENTES</th>
                        <th colspan="2" class="p-2 border-r border-slate-200">TRANSFERIDOS</th>
                        <th colspan="2" class="p-2 border-r border-slate-200">AVALIADOS</th>
                        <th colspan="2" class="p-2 border-r border-slate-200">APROVADOS / TRANSITADOS</th>
                        <th colspan="2" class="p-2 border-r border-slate-200">% APROVEITAMENTO</th>
                        <th rowspan="2" class="p-3">% DESIST. (HM)</th>
                    </tr>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                        <th class="p-1.5 border-r border-slate-200">HM</th><th class="p-1.5 border-r border-slate-200">M</th>
                        <th class="p-1.5 border-r border-slate-200">HM</th><th class="p-1.5 border-r border-slate-200">M</th>
                        <th class="p-1.5 border-r border-slate-200">HM</th><th class="p-1.5 border-r border-slate-200">M</th>
                        <th class="p-1.5 border-r border-slate-200">HM</th><th class="p-1.5 border-r border-slate-200">M</th>
                        <th class="p-1.5 border-r border-slate-200">HM</th><th class="p-1.5 border-r border-slate-200">M</th>
                        <th class="p-1.5 border-r border-slate-200">HM</th><th class="p-1.5 border-r border-slate-200">M</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium">
                    <!-- 1º CICLO -->
                    <tr class="bg-emerald-50/50 text-emerald-900 font-bold">
                        <td colspan="14" class="p-2.5 text-left border-b border-slate-200">1º CICLO DO ENSINO SECUNDÁRIO</td>
                    </tr>
                    @foreach(['8', '9', '10'] as $lvl)
                        @if(isset($mapData['by_level'][$lvl]))
                            @php $row = $mapData['by_level'][$lvl]; @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-2.5 text-left font-semibold border-r border-slate-200 text-slate-800">{{ $row['label'] }}</td>
                                <td class="p-2 border-r border-slate-200">{{ number_format($row['entries_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-sky-700 font-semibold">{{ number_format($row['entries_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-rose-600 font-semibold">{{ number_format($row['dropouts_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-rose-600 font-semibold">{{ number_format($row['dropouts_m']) }}</td>
                                <td class="p-2 border-r border-slate-200">{{ number_format($row['transfers_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200">{{ number_format($row['transfers_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 font-semibold">{{ number_format($row['evaluated_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 font-semibold text-sky-700">{{ number_format($row['evaluated_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-emerald-700 font-bold">{{ number_format($row['passed_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-emerald-700 font-bold">{{ number_format($row['passed_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 font-bold text-emerald-700">{{ $row['approval_rate_hm'] }}%</td>
                                <td class="p-2 border-r border-slate-200 font-bold text-emerald-700">{{ $row['approval_rate_m'] }}%</td>
                                <td class="p-2">{{ $row['dropout_rate_hm'] }}%</td>
                            </tr>
                        @endif
                    @endforeach

                    <!-- TOTAL 1º CICLO -->
                    <tr class="font-bold bg-emerald-100/60 text-emerald-950">
                        <td class="p-2.5 text-left border-r border-slate-200">SUB-TOTAL 1º CICLO</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['entries_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['entries_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['dropouts_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['dropouts_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['transfers_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['transfers_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['evaluated_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['evaluated_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['passed_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo1']['passed_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ $mapData['ciclo1']['approval_rate_hm'] }}%</td>
                        <td class="p-2 border-r border-slate-200">{{ $mapData['ciclo1']['approval_rate_m'] }}%</td>
                        <td class="p-2">{{ $mapData['ciclo1']['dropout_rate_hm'] }}%</td>
                    </tr>

                    <!-- 2º CICLO -->
                    <tr class="bg-emerald-50/50 text-emerald-900 font-bold">
                        <td colspan="14" class="p-2.5 text-left border-b border-slate-200">2º CICLO DO ENSINO SECUNDÁRIO</td>
                    </tr>
                    @foreach(['11', '12'] as $lvl)
                        @if(isset($mapData['by_level'][$lvl]))
                            @php $row = $mapData['by_level'][$lvl]; @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-2.5 text-left font-semibold border-r border-slate-200 text-slate-800">{{ $row['label'] }}</td>
                                <td class="p-2 border-r border-slate-200">{{ number_format($row['entries_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-sky-700 font-semibold">{{ number_format($row['entries_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-rose-600 font-semibold">{{ number_format($row['dropouts_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-rose-600 font-semibold">{{ number_format($row['dropouts_m']) }}</td>
                                <td class="p-2 border-r border-slate-200">{{ number_format($row['transfers_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200">{{ number_format($row['transfers_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 font-semibold">{{ number_format($row['evaluated_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 font-semibold text-sky-700">{{ number_format($row['evaluated_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-emerald-700 font-bold">{{ number_format($row['passed_hm']) }}</td>
                                <td class="p-2 border-r border-slate-200 text-emerald-700 font-bold">{{ number_format($row['passed_m']) }}</td>
                                <td class="p-2 border-r border-slate-200 font-bold text-emerald-700">{{ $row['approval_rate_hm'] }}%</td>
                                <td class="p-2 border-r border-slate-200 font-bold text-emerald-700">{{ $row['approval_rate_m'] }}%</td>
                                <td class="p-2">{{ $row['dropout_rate_hm'] }}%</td>
                            </tr>
                        @endif
                    @endforeach

                    <!-- TOTAL 2º CICLO -->
                    <tr class="font-bold bg-emerald-100/60 text-emerald-950">
                        <td class="p-2.5 text-left border-r border-slate-200">SUB-TOTAL 2º CICLO</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['entries_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['entries_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['dropouts_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['dropouts_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['transfers_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['transfers_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['evaluated_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['evaluated_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['passed_hm']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ number_format($mapData['ciclo2']['passed_m']) }}</td>
                        <td class="p-2 border-r border-slate-200">{{ $mapData['ciclo2']['approval_rate_hm'] }}%</td>
                        <td class="p-2 border-r border-slate-200">{{ $mapData['ciclo2']['approval_rate_m'] }}%</td>
                        <td class="p-2">{{ $mapData['ciclo2']['dropout_rate_hm'] }}%</td>
                    </tr>

                    <!-- TOTAL GERAL -->
                    <tr class="font-extrabold bg-slate-900 text-white text-xs">
                        <td class="p-3 text-left border-r border-slate-800">TOTAL GERAL TERRITÓRIO</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['entries_hm']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['entries_m']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['dropouts_hm']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['dropouts_m']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['transfers_hm']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['transfers_m']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['evaluated_hm']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['evaluated_m']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['passed_hm']) }}</td>
                        <td class="p-3 border-r border-slate-800">{{ number_format($mapData['totals']['passed_m']) }}</td>
                        <td class="p-3 border-r border-slate-800 text-emerald-400">{{ $mapData['totals']['approval_rate_hm'] }}%</td>
                        <td class="p-3 border-r border-slate-800 text-emerald-400">{{ $mapData['totals']['approval_rate_m'] }}%</td>
                        <td class="p-3">{{ $mapData['totals']['dropout_rate_hm'] }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

