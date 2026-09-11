@extends('layouts.app')

@section('title', 'Inspeção Distrito — ' . $district->name)

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="w-full px-4 py-6">
    <!-- Breadcrumb Nav -->
    <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="flex items-center space-x-2 text-xs font-semibold">
                <li><a href="{{ route('admin.territory.index', ['tab' => 'districts']) }}" class="text-emerald-600 hover:text-emerald-700">Gestão Territorial</a></li>
                <li class="text-slate-400">/</li>
                <li class="text-slate-700 font-bold" aria-current="page">{{ $district->name }}</li>
            </ol>
        </nav>
        <a href="{{ route('admin.territory.index', ['tab' => 'districts']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-sm transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Main Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-purple-50 text-purple-600 rounded-2xl flex-shrink-0">
                    <i class="fa-solid fa-landmark text-3xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">{{ $district->name }}</h2>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-xs font-mono">Código: {{ $district->code }}</span>
                        @if($district->is_active)
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold inline-flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Ativo
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 text-xs font-semibold inline-flex items-center gap-1">
                                <i class="fa-solid fa-circle-xmark"></i> Inativo
                            </span>
                        @endif
                    </div>
                    <p class="text-xs md:text-sm text-slate-600 mb-1">
                        <i class="fa-solid fa-map-pin text-rose-500 mr-1"></i>
                        Província: <strong class="text-slate-800">{{ $district->province->name ?? 'N/A' }}</strong>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.territory.index', ['tab' => 'districts']) }}" class="px-4 py-2 rounded-xl border border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-semibold text-xs transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-gear"></i> Editar Distrito
            </a>
        </div>
    </div>

    <!-- Metrics Summary Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total ZIPs</span>
                <div class="p-2 bg-sky-50 text-sky-600 rounded-xl">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1">{{ number_format($district->zips->count()) }}</h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Escolas</span>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i class="fa-solid fa-school"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1">{{ number_format($district->schools->count()) }}</h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Escolas Públicas</span>
                <div class="p-2 bg-sky-50 text-sky-600 rounded-xl">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1">{{ $district->schools->where('type', 'Pública')->count() }}</h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Escolas Privadas/Comunitárias</span>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1">{{ $district->schools->whereIn('type', ['Privada', 'Comunitária'])->count() }}</h3>
        </div>
    </div>

    <!-- Row 2: Interactive Map & Metadata -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
        <!-- Interactive Map -->
        <div class="md:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-purple-600"></i> Localização Geográfica do Distrito
                </h5>
            </div>
            <div class="p-0 flex-1">
                <div id="districtLocationMap" style="height: 320px; width: 100%;"></div>
            </div>
        </div>

        <!-- Metadata Card -->
        <div class="md:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h5 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-circle-info text-purple-600"></i> Ficha Técnica do Distrito
                </h5>
                <ul class="divide-y divide-slate-100 text-xs space-y-0">
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Província</span>
                        <span class="font-semibold text-slate-900">{{ $district->province->name ?? 'N/A' }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Total de ZIPs</span>
                        <span class="font-bold text-sky-700 text-lg">{{ number_format($district->zips->count()) }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Total de Escolas</span>
                        <span class="font-bold text-emerald-700 text-lg">{{ number_format($district->schools->count()) }}</span>
                    </li>
                    <li class="py-2.5">
                        <span class="text-slate-500 font-bold uppercase block mb-1">Estado</span>
                        <span class="text-slate-800 flex items-center gap-1.5">
                            @if($district->is_active)
                                <i class="fa-solid fa-circle-check text-emerald-500"></i> Ativo
                            @else
                                <i class="fa-solid fa-circle-xmark text-rose-500"></i> Inativo
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ZIPs do Distrito -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-network-wired text-sky-600"></i> ZIPs neste Distrito ({{ $district->zips->count() }})
            </h5>
        </div>
        <div class="p-4 divide-y divide-slate-100">
            @forelse($district->zips as $zip)
                <div class="py-3 flex items-center justify-between hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-network-wired text-sky-600"></i>
                        <div>
                            <div class="font-bold text-slate-900">{{ $zip->name }}</div>
                            <div class="text-slate-500 text-xs">{{ $zip->schools->count() }} escolas · Sede: {{ $zip->headquarters ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $zip->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $zip->is_active ? 'Ativa' : 'Inativa' }}
                    </span>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 font-medium">
                    Nenhuma ZIP registada neste distrito.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Escolas do Distrito -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-school text-emerald-600"></i> Escolas neste Distrito ({{ $district->schools->count() }})
            </h5>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Nome da Escola</th>
                        <th class="py-3 px-4">Código</th>
                        <th class="py-3 px-4">ZIP</th>
                        <th class="py-3 px-4">Tipo</th>
                        <th class="py-3 px-4">Director</th>
                        <th class="py-3 px-4">Contacto</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($district->schools as $school)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-6 font-bold text-slate-900">
                                {{ $school->name }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $school->code }}</td>
                            <td class="py-3 px-4">
                                @if($school->zip)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-medium">
                                        <i class="fa-solid fa-network-wired text-sky-600 mr-1"></i> {{ $school->zip->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-xs">Sem ZIP</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $school->type === 'Pública' ? 'bg-sky-100 text-sky-800' : ($school->type === 'Privada' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $school->type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $school->principal_name ?? 'Não Atribuído' }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $school->phone ?? $school->email ?? '—' }}</td>
                            <td class="py-3 px-6 text-center">
                                @if($school->is_active)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-semibold text-xs">Ativa</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-semibold text-xs">Inativa</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-slate-400 font-medium">
                                Nenhuma escola registada neste distrito.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var lat = {{ $district->province->latitude ?? -25.9655 }};
        var lng = {{ $district->province->longitude ?? 32.5832 }};

        var map = L.map('districtLocationMap').setView([lat, lng], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap | MINEDH SIGE-ZAMEDU'
        }).addTo(map);

        var districtIcon = L.divIcon({
            className: 'custom-map-icon',
            html: '<div style="background-color: #9333ea; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.3); border: 2px solid white;"><i class="fa-solid fa-landmark" style="font-size: 16px;"></i></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });

        var marker = L.marker([lat, lng], { icon: districtIcon }).addTo(map);

        var popupContent = `
            <div style="font-family: system-ui, -apple-system, sans-serif; padding: 4px; max-width: 240px;">
                <div style="font-weight: 700; color: #0f172a; font-size: 14px; margin-bottom: 4px;">{{ addslashes($district->name) }}</div>
                <div style="font-size: 12px; color: #475569; margin-bottom: 6px;">
                    <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i> {{ addslashes($district->province->name ?? '') }}
                </div>
                <div style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                    ZIPs: <strong>{{ number_format($district->zips->count()) }}</strong><br>
                    Escolas: <strong>{{ number_format($district->schools->count()) }}</strong>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent).openPopup();
    });
</script>
@endsection