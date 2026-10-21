@extends('layouts.app')

@section('title', 'Painel Territorial de Inspeção & Analytics MINEDH')
@section('page-title', 'Painel Territorial de Inspeção & Analytics MINEDH')
@section('page-title-icon', 'fas fa-map-location-dot')

@section('breadcrumbs')
    <li class="breadcrumb-item">Territorial</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Painel Territorial de Inspeção & Analytics MINEDH</li>
@endsection

@section('content')
<!-- Leaflet CSS & JS for Interactive Mozambique Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="w-full px-4 py-6 space-y-6">
    
    <!-- Top Header Banner -->
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-2xl p-6 shadow-md text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="p-3.5 bg-white/20 rounded-2xl backdrop-blur-md text-white shrink-0">
                <i class="fa-solid fa-map-location-dot text-3xl"></i>
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-bold tracking-tight">Painel Territorial de Inspeção MINEDH</h2>
                <p class="text-xs md:text-sm text-emerald-100 mt-0.5 flex items-center gap-2 flex-wrap">
                    <span>Âmbito de Monitoria:</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-white text-emerald-800 font-bold uppercase text-[11px] shadow-2xs">
                        {{ strtoupper($territorialScope['level']) }}
                    </span>
                    <span>| República de Moçambique</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-xl bg-white text-emerald-700 font-semibold text-xs shadow-sm hover:bg-emerald-50 transition inline-flex items-center gap-2">
                <i class="fa-solid fa-file-invoice"></i> Central de Relatórios
            </a>
        </div>
    </div>

    <!-- Territorial Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('territorial.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Província</label>
                <select name="province_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">Todas as Províncias</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ $selectedProvinceId == $prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Distrito</label>
                <select name="district_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">Todos os Distritos</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist->id }}" {{ $selectedDistrictId == $dist->id ? 'selected' : '' }}>
                            {{ $dist->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">ZIP (Zona de Influência)</label>
                <select name="zip_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">Todas as ZIPs</option>
                    @foreach($zips as $zip)
                        <option value="{{ $zip->id }}" {{ $selectedZipId == $zip->id ? 'selected' : '' }}>
                            {{ $zip->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3 flex items-end gap-2 pt-5 lg:pt-0">
                <button type="submit" class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i> Filtrar Âmbito
                </button>
                <a href="{{ route('territorial.dashboard') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition" title="Limpar Filtros">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Executive KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Schools Count -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Escolas Sob Inspeção</span>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($schoolsCount) }}</h3>
                <span class="text-xs text-emerald-600 font-medium flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-school"></i> Redes Primária e Secundária
                </span>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                <i class="fa-solid fa-building-columns text-2xl"></i>
            </div>
        </div>

        <!-- Total Students (HM & M) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alunos Matriculados</span>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($studentsHM) }} <span class="text-xs font-medium text-slate-400">(HM)</span></h3>
                <span class="text-xs text-sky-600 font-semibold flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-venus"></i> {{ number_format($studentsM) }} Alunas (M)
                </span>
            </div>
            <div class="p-3 bg-sky-50 text-sky-600 rounded-2xl shrink-0">
                <i class="fa-solid fa-user-graduate text-2xl"></i>
            </div>
        </div>

        <!-- Teachers Count -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Corpo Docente</span>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($teachersCount) }}</h3>
                <span class="text-xs text-indigo-600 font-semibold flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-chalkboard-user"></i> Professores Alocados
                </span>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                <i class="fa-solid fa-chalkboard-teacher text-2xl"></i>
            </div>
        </div>

        <!-- Pedagogical Approval Rate (HM & M) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aproveitamento (HM)</span>
                <h3 class="text-2xl font-black mt-1 {{ $approvalRateHM >= 75 ? 'text-emerald-600' : ($approvalRateHM >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                    {{ $approvalRateHM }}%
                </h3>
                <span class="text-xs text-slate-500 mt-1 block">Mulheres (M): <strong class="text-slate-800">{{ $approvalRateM }}%</strong></span>
            </div>
            <div class="p-3 {{ $approvalRateHM >= 75 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} rounded-2xl shrink-0">
                <i class="fa-solid fa-chart-line text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- INTERACTIVE MAP OF MOZAMBIQUE SCHOOLS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-earth-africa text-emerald-600"></i> Mapa Geográfico e Localização das Escolas (Moçambique)
            </h3>
            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 inline-flex items-center gap-1.5">
                <i class="fa-solid fa-satellite-dish"></i> {{ count($schoolsDetailed) }} Marcadores Georreferenciados
            </span>
        </div>
        <div class="p-0">
            <div id="mozambique-schools-map" style="height: 440px; width: 100%; z-index: 1;"></div>
        </div>
    </div>

    <!-- TABBED DETAILED SECTIONS FOR ESCOLAS, ZIPS, DISTRITOS, PROVÍNCIAS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ tab: 'schools' }">
        <div class="border-b border-slate-200 px-6 pt-4 bg-slate-50/50 flex flex-wrap gap-2">
            <button @click="tab = 'schools'" :class="tab === 'schools' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 font-bold text-xs rounded-t-xl transition flex items-center gap-2">
                <i class="fa-solid fa-school"></i> Escolas ({{ count($schoolsDetailed) }})
            </button>
            <button @click="tab = 'zips'" :class="tab === 'zips' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 font-bold text-xs rounded-t-xl transition flex items-center gap-2">
                <i class="fa-solid fa-network-wired"></i> ZIPs ({{ count($zipsDetailed) }})
            </button>
            <button @click="tab = 'districts'" :class="tab === 'districts' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 font-bold text-xs rounded-t-xl transition flex items-center gap-2">
                <i class="fa-solid fa-city"></i> Distritos ({{ count($districtsDetailed) }})
            </button>
            <button @click="tab = 'provinces'" :class="tab === 'provinces' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-xs' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2.5 font-bold text-xs rounded-t-xl transition flex items-center gap-2">
                <i class="fa-solid fa-map"></i> Províncias ({{ count($provincesDetailed) }})
            </button>
        </div>

        <div class="p-0">
            <!-- PANE 1: ESCOLAS DETALHADAS -->
            <div x-show="tab === 'schools'" class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Escola / Código</th>
                            <th class="py-3 px-4">Diretor da Escola</th>
                            <th class="py-3 px-4">Contactos</th>
                            <th class="py-3 px-4">Localização (ZIP / Distrito / Província)</th>
                            <th class="py-3 px-4 text-center">Total de Alunos (HM / M)</th>
                            <th class="py-3 px-6 text-right">Acções</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($schoolsDetailed as $sc)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                                            <i class="fa-solid fa-school text-base"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('territorial.school', $sc['id']) }}" class="hover:text-emerald-700 transition">
                                                {{ $sc['name'] }}
                                            </a>
                                            <div class="text-slate-400 font-mono text-xs font-normal">{{ $sc['code'] }} &bull; {{ $sc['type'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    <div>{{ $sc['principal_name'] }}</div>
                                    <div class="text-slate-400 text-xs font-normal"><i class="fa-solid fa-user-tie mr-1"></i> Direção Geral</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $sc['phone'] }}</div>
                                    <div class="text-slate-400 text-xs"><i class="fa-solid fa-envelope mr-1"></i> {{ $sc['email'] }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium inline-block mb-0.5">
                                        <i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i> {{ $sc['zip_name'] }}
                                    </span>
                                    <div class="text-slate-400 text-xs">{{ $sc['district_name'] }} &bull; {{ $sc['province_name'] }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-bold text-slate-900">{{ number_format($sc['students_hm']) }} <span class="text-slate-400 font-normal">(HM)</span></div>
                                    <div class="text-sky-600 font-medium text-xs">{{ number_format($sc['students_m']) }} (M)</div>
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if(auth()->user()->hasRole('super_admin'))
                                            <form method="POST" action="{{ route('super-admin.impersonate', $sc['id']) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold text-xs transition inline-flex items-center gap-1" title="Modo Suporte Técnico">
                                                    <i class="fa-solid fa-headset"></i> Suporte
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('territorial.school', $sc['id']) }}" class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition inline-flex items-center gap-1">
                                            Inspeção <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-400 font-medium">Nenhuma escola cadastrada no âmbito territorial.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PANE 2: ZIPS DETALHADAS -->
            <div x-show="tab === 'zips'" class="overflow-x-auto" x-cloak>
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Zona de Influência Pedagógica (ZIP)</th>
                            <th class="py-3 px-4">Escola Sede ZIP</th>
                            <th class="py-3 px-4">Distrito & Província</th>
                            <th class="py-3 px-4 text-center">Total Escolas</th>
                            <th class="py-3 px-4 text-center">Total de Alunos (HM / M)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($zipsDetailed as $zp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    <div>{{ $zp['name'] }}</div>
                                    <span class="font-mono text-slate-400 font-normal text-xs">{{ $zp['code'] }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    <i class="fa-solid fa-building text-emerald-600 mr-1"></i> {{ $zp['headquarters'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $zp['district_name'] }} &bull; {{ $zp['province_name'] }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">{{ $zp['schools_count'] }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-bold text-slate-900">{{ number_format($zp['students_hm']) }} (HM)</div>
                                    <div class="text-sky-600 font-medium text-xs">{{ number_format($zp['students_m']) }} (M)</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-400 font-medium">Nenhuma ZIP registada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PANE 3: DISTRITOS (SDEJT) DETALHADOS -->
            <div x-show="tab === 'districts'" class="overflow-x-auto" x-cloak>
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Distrito / SDEJT</th>
                            <th class="py-3 px-4">Província</th>
                            <th class="py-3 px-4 text-center">Nº de ZIPs</th>
                            <th class="py-3 px-4 text-center">Total Escolas</th>
                            <th class="py-3 px-4 text-center">Total de Alunos (HM / M)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($districtsDetailed as $dt)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    <div>{{ $dt['name'] }}</div>
                                    <span class="font-mono text-slate-400 font-normal text-xs">{{ $dt['code'] }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $dt['province_name'] }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-indigo-600">{{ $dt['zips_count'] }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">{{ $dt['schools_count'] }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-bold text-slate-900">{{ number_format($dt['students_hm']) }} (HM)</div>
                                    <div class="text-sky-600 font-medium text-xs">{{ number_format($dt['students_m']) }} (M)</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-400 font-medium">Nenhum Distrito registado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PANE 4: PROVÍNCIAS (DPEDH) DETALHADAS -->
            <div x-show="tab === 'provinces'" class="overflow-x-auto" x-cloak>
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Província / DPEDH</th>
                            <th class="py-3 px-4">Código</th>
                            <th class="py-3 px-4 text-center">Nº de Distritos</th>
                            <th class="py-3 px-4 text-center">Total Escolas</th>
                            <th class="py-3 px-4 text-center">Total de Alunos (HM / M)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($provincesDetailed as $pv)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-900">{{ $pv['name'] }}</td>
                                <td class="py-3.5 px-4 font-mono font-semibold text-emerald-700">{{ $pv['code'] }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-600">{{ $pv['districts_count'] }}</td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">{{ $pv['schools_count'] }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-bold text-slate-900">{{ number_format($pv['students_hm']) }} (HM)</div>
                                    <div class="text-sky-600 font-medium text-xs">{{ number_format($pv['students_m']) }} (M)</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-400 font-medium">Nenhuma Província registada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<!-- Leaflet Map Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var map = L.map('mozambique-schools-map').setView([-18.6656, 35.5295], 6);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | MINEDH Moçambique'
    }).addTo(map);

    var schools = @json($schoolsDetailed);
    var bounds = [];
    var isSuperAdmin = {{ auth()->user()->hasRole('super_admin') ? 'true' : 'false' }};
    var csrfToken = '{{ csrf_token() }}';

    schools.forEach(function (school) {
        if (school.lat && school.lng) {
            bounds.push([school.lat, school.lng]);

            var marker = L.marker([school.lat, school.lng]).addTo(map);

            var supportForm = '';
            if (isSuperAdmin) {
                supportForm = `
                    <form method="POST" action="/super-admin/impersonate/${school.id}" style="margin-top: 8px;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" style="width: 100%; background: #f59e0b; color: #fff; border: none; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 11px; cursor: pointer;">
                            <i class="fas fa-headset mr-1"></i> Entrar no Modo Suporte
                        </button>
                    </form>
                `;
            }

            var popupContent = `
                <div style="min-width: 220px; font-family: system-ui, sans-serif;">
                    <div style="font-size: 10px; font-weight: bold; color: #059669; text-transform: uppercase;">MINEDH &bull; ${school.type}</div>
                    <h4 style="margin: 2px 0 6px 0; font-size: 14px; font-weight: 800; color: #1e293b;">${school.name}</h4>
                    <div style="font-size: 11px; color: #475569; margin-bottom: 4px;">
                        <strong>Diretor:</strong> ${school.principal_name}<br>
                        <strong>Contacto:</strong> ${school.phone}<br>
                        <strong>Email:</strong> ${school.email}<br>
                        <strong>Local:</strong> ${school.zip_name}, ${school.district_name}
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 6px; border-top: 1px solid #e2e8f0; font-size: 11px;">
                        <span><strong>${school.students_hm}</strong> Alunos (HM)</span>
                        <span style="background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 10px; font-weight: bold;">
                            ${school.approval_rate}% Aprov.
                        </span>
                    </div>
                    <div style="margin-top: 8px; display: flex; gap: 4px;">
                        <a href="/territorial/school/${school.id}" style="flex: 1; text-align: center; background: #059669; color: white; text-decoration: none; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 11px;">
                            Ver Inspeção &rarr;
                        </a>
                    </div>
                    ${supportForm}
                </div>
            `;

            marker.bindPopup(popupContent);
        }
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
});
</script>
@endsection
