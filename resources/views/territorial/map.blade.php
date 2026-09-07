@extends('layouts.app')

@section('title', 'Mapa Oficial de Aproveitamento Pedagógico Território — MINEDH')

@section('content')
<div class="container-fluid py-4" style="background-color: var(--content-bg, #f8fafc);">

    <!-- Header & Actions -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <span class="badge bg-emerald-subtle text-emerald px-3 py-1 fw-semibold mb-2">MINEDH — Regulamento SNE Lei 18/2018</span>
            <h2 class="h3 fw-bold text-dark mb-1">Mapa Geral de Aproveitamento Pedagógico</h2>
            <p class="text-muted text-sm mb-0">Consolidado Território: Ensino Secundário Geral — {{ $term }}º Trimestre / {{ $year }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('territorial.map.pdf', request()->all()) }}" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" target="_blank">
                <i class="fa-solid fa-file-pdf me-1"></i> Exportar PDF Oficial
            </a>
            <a href="{{ route('territorial.map.csv', request()->all()) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Exportar Excel / CSV
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-print me-1"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('territorial.map') }}" class="row g-3 align-items-center">
                <div class="col-md-2">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">Ano Lectivo</label>
                    <select name="year" class="form-select form-select-sm rounded-3">
                        <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">Trimestre</label>
                    <select name="term" class="form-select form-select-sm rounded-3">
                        <option value="1" {{ $term == 1 ? 'selected' : '' }}>1º Trimestre</option>
                        <option value="2" {{ $term == 2 ? 'selected' : '' }}>2º Trimestre</option>
                        <option value="3" {{ $term == 3 ? 'selected' : '' }}>3º Trimestre</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">Província</label>
                    <select name="province_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todas</option>
                        @foreach($provinces as $p)
                            <option value="{{ $p->id }}" {{ $selectedProvinceId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">Distrito</label>
                    <select name="district_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todos</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ $selectedDistrictId == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs text-muted fw-semibold uppercase mb-1">ZIP</label>
                    <select name="zip_id" class="form-select form-select-sm rounded-3">
                        <option value="">Todas</option>
                        @foreach($zips as $z)
                            <option value="{{ $z->id }}" {{ $selectedZipId == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-emerald btn-sm rounded-3 w-100 fw-semibold text-white" style="background-color: #059669;">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> Atualizar Mapa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Official MINEDH Table Sheet Card -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 rounded-top-4 text-center">
            <h5 class="fw-bold mb-1 text-dark">REPÚBLICA DE MOÇAMBIQUE</h5>
            <h6 class="fw-semibold text-muted mb-1">MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO</h6>
            <p class="text-xs text-emerald fw-bold mb-0">MAPA DE APROVEITAMENTO PEDAGÓGICO — ENSINO SECUNDÁRIO GERAL (ESG)</p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0 text-sm">
                    <thead class="bg-light align-middle">
                        <tr>
                            <th rowspan="2" class="ps-3 text-start">CLASSE</th>
                            <th colspan="2">Nº ENTRADAS</th>
                            <th colspan="2">DESISTENTES</th>
                            <th colspan="2">TRANSFERIDOS</th>
                            <th colspan="2">AVALIADOS</th>
                            <th colspan="2">APROVADOS / TRANSITADOS</th>
                            <th colspan="2">% APROVEITAMENTO</th>
                            <th rowspan="2" class="pe-3">% DESIST. (HM)</th>
                        </tr>
                        <tr class="bg-slate-100 text-xs">
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                            <th>HM</th><th>M</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1º CICLO -->
                        <tr class="table-group-divider bg-slate-50"><td colspan="14" class="text-start fw-bold text-emerald ps-3">1º CICLO DO ENSINO SECUNDÁRIO</td></tr>
                        @foreach(['8', '9', '10'] as $lvl)
                            @if(isset($mapData['by_level'][$lvl]))
                                @php $row = $mapData['by_level'][$lvl]; @endphp
                                <tr>
                                    <td class="text-start ps-4 fw-semibold">{{ $row['label'] }}</td>
                                    <td>{{ number_format($row['entries_hm']) }}</td>
                                    <td class="text-primary">{{ number_format($row['entries_m']) }}</td>
                                    <td class="text-danger">{{ number_format($row['dropouts_hm']) }}</td>
                                    <td class="text-danger">{{ number_format($row['dropouts_m']) }}</td>
                                    <td>{{ number_format($row['transfers_hm']) }}</td>
                                    <td>{{ number_format($row['transfers_m']) }}</td>
                                    <td class="fw-semibold">{{ number_format($row['evaluated_hm']) }}</td>
                                    <td class="fw-semibold text-primary">{{ number_format($row['evaluated_m']) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($row['passed_hm']) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($row['passed_m']) }}</td>
                                    <td class="fw-bold text-success">{{ $row['approval_rate_hm'] }}%</td>
                                    <td class="fw-bold text-success">{{ $row['approval_rate_m'] }}%</td>
                                    <td>{{ $row['dropout_rate_hm'] }}%</td>
                                </tr>
                            @endif
                        @endforeach

                        <!-- TOTAL 1º CICLO -->
                        <tr class="fw-bold bg-emerald-subtle text-emerald">
                            <td class="text-start ps-3">SUB-TOTAL 1º CICLO</td>
                            <td>{{ number_format($mapData['ciclo1']['entries_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['entries_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['dropouts_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['dropouts_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['transfers_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['transfers_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['evaluated_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['evaluated_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['passed_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo1']['passed_m']) }}</td>
                            <td>{{ $mapData['ciclo1']['approval_rate_hm'] }}%</td>
                            <td>{{ $mapData['ciclo1']['approval_rate_m'] }}%</td>
                            <td>{{ $mapData['ciclo1']['dropout_rate_hm'] }}%</td>
                        </tr>

                        <!-- 2º CICLO -->
                        <tr class="bg-slate-50"><td colspan="14" class="text-start fw-bold text-emerald ps-3">2º CICLO DO ENSINO SECUNDÁRIO</td></tr>
                        @foreach(['11', '12'] as $lvl)
                            @if(isset($mapData['by_level'][$lvl]))
                                @php $row = $mapData['by_level'][$lvl]; @endphp
                                <tr>
                                    <td class="text-start ps-4 fw-semibold">{{ $row['label'] }}</td>
                                    <td>{{ number_format($row['entries_hm']) }}</td>
                                    <td class="text-primary">{{ number_format($row['entries_m']) }}</td>
                                    <td class="text-danger">{{ number_format($row['dropouts_hm']) }}</td>
                                    <td class="text-danger">{{ number_format($row['dropouts_m']) }}</td>
                                    <td>{{ number_format($row['transfers_hm']) }}</td>
                                    <td>{{ number_format($row['transfers_m']) }}</td>
                                    <td class="fw-semibold">{{ number_format($row['evaluated_hm']) }}</td>
                                    <td class="fw-semibold text-primary">{{ number_format($row['evaluated_m']) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($row['passed_hm']) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($row['passed_m']) }}</td>
                                    <td class="fw-bold text-success">{{ $row['approval_rate_hm'] }}%</td>
                                    <td class="fw-bold text-success">{{ $row['approval_rate_m'] }}%</td>
                                    <td>{{ $row['dropout_rate_hm'] }}%</td>
                                </tr>
                            @endif
                        @endforeach

                        <!-- TOTAL 2º CICLO -->
                        <tr class="fw-bold bg-emerald-subtle text-emerald">
                            <td class="text-start ps-3">SUB-TOTAL 2º CICLO</td>
                            <td>{{ number_format($mapData['ciclo2']['entries_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['entries_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['dropouts_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['dropouts_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['transfers_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['transfers_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['evaluated_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['evaluated_m']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['passed_hm']) }}</td>
                            <td>{{ number_format($mapData['ciclo2']['passed_m']) }}</td>
                            <td>{{ $mapData['ciclo2']['approval_rate_hm'] }}%</td>
                            <td>{{ $mapData['ciclo2']['approval_rate_m'] }}%</td>
                            <td>{{ $mapData['ciclo2']['dropout_rate_hm'] }}%</td>
                        </tr>

                        <!-- TOTAL GERAL -->
                        <tr class="fw-extrabold table-dark fs-6">
                            <td class="text-start ps-3">TOTAL GERAL TERRITÓRIO</td>
                            <td>{{ number_format($mapData['totals']['entries_hm']) }}</td>
                            <td>{{ number_format($mapData['totals']['entries_m']) }}</td>
                            <td>{{ number_format($mapData['totals']['dropouts_hm']) }}</td>
                            <td>{{ number_format($mapData['totals']['dropouts_m']) }}</td>
                            <td>{{ number_format($mapData['totals']['transfers_hm']) }}</td>
                            <td>{{ number_format($mapData['totals']['transfers_m']) }}</td>
                            <td>{{ number_format($mapData['totals']['evaluated_hm']) }}</td>
                            <td>{{ number_format($mapData['totals']['evaluated_m']) }}</td>
                            <td>{{ number_format($mapData['totals']['passed_hm']) }}</td>
                            <td>{{ number_format($mapData['totals']['passed_m']) }}</td>
                            <td>{{ $mapData['totals']['approval_rate_hm'] }}%</td>
                            <td>{{ $mapData['totals']['approval_rate_m'] }}%</td>
                            <td>{{ $mapData['totals']['dropout_rate_hm'] }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
