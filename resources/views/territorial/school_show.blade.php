@extends('layouts.app')

@section('title', 'Territorial - ' . $school->name)
@section('page-title', 'Territorial -' . $school->name)
@section('page-title-icon', 'fas fa-map-location-dot')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Territorial</li>
@endsection

@section('title', 'Inspeção Escolar — ' . $school->name)

@section('content')
<!-- Leaflet CSS & JS for Interactive School Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="w-full px-4 py-6">

    <!-- Breadcrumb Nav -->
    <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
        <nav aria-label="breadcrumb">
            <ol class="flex items-center space-x-2 text-xs font-semibold">
                <li><a href="{{ route('territorial.dashboard') }}" class="text-emerald-600 hover:text-emerald-700">Painel Territorial</a></li>
                <li class="text-slate-400">/</li>
                <li class="text-slate-700 font-bold" aria-current="page">{{ $school->name }}</li>
            </ol>
        </nav>

        <a href="{{ route('territorial.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-sm transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
        </a>
    </div>

    <!-- Main Header Card & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl flex-shrink-0">
                    <i class="fa-solid fa-school text-3xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight">{{ $school->name }}</h2>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold 
                            {{ $school->type === 'Pública' ? 'bg-sky-100 text-sky-800' : ($school->type === 'Privada' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                            {{ $school->type_label ?? $school->type }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 text-xs font-mono">Código: {{ $school->code }}</span>
                        @if($school->is_active)
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold inline-flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Ativa
                            </span>
                        @endif
                    </div>
                    <p class="text-xs md:text-sm text-slate-600 mb-1">
                        <i class="fa-solid fa-map-pin text-rose-500 mr-1"></i>
                        Província: <strong class="text-slate-800">{{ $school->province->name ?? 'N/A' }}</strong> |
                        Distrito: <strong class="text-slate-800">{{ $school->district->name ?? 'N/A' }}</strong> |
                        ZIP: <strong class="text-slate-800">{{ $school->zip->name ?? 'Sem ZIP' }}</strong>
                    </p>
                    <div class="text-xs text-slate-500 flex flex-wrap items-center gap-2">
                        <span><i class="fa-solid fa-user-tie text-slate-400 mr-1"></i> Directoria: <strong class="text-slate-700">{{ $school->principal_name ?? 'Não especificado' }}</strong></span>
                        @if($school->phone) <span>| <i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $school->phone }}</span> @endif
                        @if($school->email) <span>| <i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $school->email }}</span> @endif
                    </div>
                </div>
            </div>

            <!-- Shortcuts / Actions -->
            <div class="flex items-center gap-2 flex-wrap">
                @if(auth()->user()->hasRole('super_admin'))
                    <form action="{{ route('super-admin.impersonate', $school->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-user-shield"></i> Modo Suporte
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.territory.index', ['tab' => 'schools', 'search' => $school->name]) }}" class="px-4 py-2 rounded-xl border border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-semibold text-xs transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-gear"></i> Configurar Escola
                </a>

                <a href="{{ route('territorial.map', ['school_id' => $school->id]) }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-800 font-semibold text-xs transition inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice text-emerald-600"></i> Mapa Pedagógico MINEDH
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Summary Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Alunos (HM)</span>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1">{{ number_format($studentsHM) }}</h3>
            <div class="flex items-center justify-between text-xs text-slate-500">
                <span>Mulheres (M): <strong class="text-sky-700">{{ number_format($studentsM) }}</strong></span>
                <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 font-semibold">{{ $femaleStudentRatio }}% F</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Corpo Docente</span>
                <div class="p-2 bg-sky-50 text-sky-600 rounded-xl">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1">{{ number_format($teachersCount) }}</h3>
            <span class="text-xs text-slate-500">Rácio Aluno/Prof: <strong class="text-slate-800">{{ $studentTeacherRatio }} hab.</strong></span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Turmas Ativas</span>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-xl">
                    <i class="fa-solid fa-door-open"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1">{{ number_format($classesCount) }}</h3>
            <span class="text-xs text-slate-500">Média Alunos/Turma: <strong class="text-slate-800">{{ $studentClassRatio }}</strong></span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Taxa Aproveitamento</span>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black mb-1 {{ $approvalRateHM >= 75 ? 'text-emerald-600' : ($approvalRateHM >= 50 ? 'text-amber-600' : 'text-rose-600') }}">{{ $approvalRateHM }}%</h3>
            <span class="text-xs text-slate-500">Mulheres (M): <strong class="text-slate-800">{{ $approvalRateM }}%</strong></span>
        </div>
    </div>

    <!-- Row 2: Location Interactive Map & Metadata -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
        <!-- Interactive Map -->
        <div class="md:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-emerald-600"></i> Localização Geográfica & Mapa da Escola
                </h5>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $coordinates['lat'] }},{{ $coordinates['lng'] }}" target="_blank" class="px-3 py-1 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-semibold transition inline-flex items-center gap-1">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Google Maps
                </a>
            </div>
            <div class="p-0 flex-1">
                <div id="schoolLocationMap" style="height: 320px; width: 100%;"></div>
            </div>
        </div>

        <!-- Location Metadata Card -->
        <div class="md:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h5 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-circle-info text-sky-600"></i> Ficha Técnica Territorial
                </h5>
                <ul class="divide-y divide-slate-100 text-xs space-y-0">
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Coordenadas GPS</span>
                        <span class="font-mono text-slate-900 font-semibold">{{ number_format($coordinates['lat'], 5) }}, {{ number_format($coordinates['lng'], 5) }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Província</span>
                        <span class="font-semibold text-slate-900">{{ $school->province->name ?? 'N/A' }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Distrito (SDEJT)</span>
                        <span class="font-semibold text-slate-900">{{ $school->district->name ?? 'N/A' }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Zona Pedagógica (ZIP)</span>
                        <span class="font-semibold text-emerald-700">{{ $school->zip->name ?? 'Sem ZIP' }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between items-center">
                        <span class="text-slate-500 font-bold uppercase">Escola Sede ZIP</span>
                        <span class="text-slate-600">{{ $school->zip->headquarters ?? 'Não especificada' }}</span>
                    </li>
                    <li class="py-2.5">
                        <span class="text-slate-500 font-bold uppercase block mb-1">Endereço Físico</span>
                        <span class="text-slate-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                            {{ $school->address ?? 'Endereço não cadastrado no sistema.' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Row 3: MINEDH Pedagogical Rendimento Matrix -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
            <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-600"></i> Rendimento Escolar Regulamentar MINEDH (Lei 18/2018)
            </h5>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Indicador Pedagógico</th>
                        <th class="py-3 px-4 text-center">Total Homens & Mulheres (HM)</th>
                        <th class="py-3 px-4 text-center">Mulheres / Feminino (M)</th>
                        <th class="py-3 px-6 text-right">Percentual (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-6 font-semibold text-slate-900">Alunos Avaliados no Ciclo</td>
                        <td class="py-3 px-4 text-center font-bold text-slate-900 text-sm">{{ number_format($evaluatedHM) }}</td>
                        <td class="py-3 px-4 text-center font-bold text-sky-700 text-sm">{{ number_format($evaluatedM) }}</td>
                        <td class="py-3 px-6 text-right text-slate-500 font-semibold">100.0%</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-6 font-semibold text-emerald-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i> Notas Positivas (≥ 9.5 / 10.0 val.)
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-emerald-700 text-sm">{{ number_format($positiveHM) }}</td>
                        <td class="py-3 px-4 text-center font-bold text-emerald-700 text-sm">{{ number_format($positiveM) }}</td>
                        <td class="py-3 px-6 text-right font-black text-emerald-700 text-sm">{{ $approvalRateHM }}%</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-6 font-semibold text-rose-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-xmark"></i> Notas Negativas (< 9.5 val.)
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-rose-700 text-sm">{{ number_format($evaluatedHM - $positiveHM) }}</td>
                        <td class="py-3 px-4 text-center font-bold text-rose-700 text-sm">{{ number_format($evaluatedM - $positiveM) }}</td>
                        <td class="py-3 px-6 text-right font-black text-rose-700 text-sm">{{ $evaluatedHM > 0 ? round((($evaluatedHM - $positiveHM) / $evaluatedHM) * 100, 1) : 0 }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Row 4: Turmas & Corpo Docente Alocado -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Turmas -->
        <div class="md:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-door-open text-purple-600"></i> Turmas em Funcionamento ({{ count($classes) }})
                </h5>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Turma / Classe</th>
                            <th class="py-3 px-4">Turno</th>
                            <th class="py-3 px-4">Director de Turma / Professor</th>
                            <th class="py-3 px-6 text-center">Alunos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($classes as $cls)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-6 font-bold text-slate-900">
                                    {{ $cls->name }}
                                    <div class="text-xs text-slate-500 font-normal">{{ $cls->grade_level }}ª Classe</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs border border-slate-200">
                                        {{ $cls->shift === 'morning' ? 'Manhã' : ($cls->shift === 'afternoon' ? 'Tarde' : 'Noite') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-medium text-slate-800">
                                    {{ $cls->teacher->name ?? 'Não Atribuído' }}
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold text-xs">
                                        {{ $cls->students_count }} Alunos
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-slate-400 font-medium">
                                    Nenhuma turma registada nesta escola.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Corpo Docente -->
        <div class="md:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-chalkboard-user text-sky-600"></i> Corpo Docente Alocado ({{ count($teachers) }})
                </h5>
            </div>
            <div class="p-0 flex-1">
                <ul class="divide-y divide-slate-100 text-xs">
                    @forelse($teachers as $t)
                        <li class="px-6 py-3 flex items-center justify-between hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-slate-100 text-slate-500 rounded-full">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">{{ $t->name }}</div>
                                    <div class="text-slate-500 text-xs flex items-center gap-2">
                                        @if($t->phone) <span><i class="fa-solid fa-phone mr-1"></i>{{ $t->phone }}</span> @endif
                                        @if($t->email) <span><i class="fa-solid fa-envelope mr-1"></i>{{ $t->email }}</span> @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-6 text-center text-slate-400 font-medium">
                            Nenhum professor registado nesta escola.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Leaflet Map Initialization Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var lat = {{ $coordinates['lat'] }};
        var lng = {{ $coordinates['lng'] }};

        var map = L.map('schoolLocationMap').setView([lat, lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap | MINEDH SIGE-ZAMEDU'
        }).addTo(map);

        var schoolIcon = L.divIcon({
            className: 'custom-map-icon',
            html: '<div style="background-color: #059669; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px rgba(0,0,0,0.3); border: 2px solid white;"><i class="fa-solid fa-school" style="font-size: 16px;"></i></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });

        var marker = L.marker([lat, lng], { icon: schoolIcon }).addTo(map);

        var popupContent = `
            <div style="font-family: system-ui, -apple-system, sans-serif; padding: 4px; max-width: 240px;">
                <div style="font-weight: 700; color: #0f172a; font-size: 14px; margin-bottom: 4px;">{{ addslashes($school->name) }}</div>
                <div style="font-size: 12px; color: #475569; margin-bottom: 6px;">
                    <i class="fa-solid fa-location-dot" style="color: #ef4444;"></i> {{ addslashes($school->province->name ?? '') }} — {{ addslashes($school->district->name ?? '') }}
                </div>
                <div style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
                    Director: <strong>{{ addslashes($school->principal_name ?? 'N/A') }}</strong><br>
                    Alunos: <strong>{{ number_format($studentsHM) }}</strong> ({{ $approvalRateHM }}% Aprov.)
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" target="_blank" 
                   style="display: inline-block; background-color: #059669; color: white; padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 12px; text-decoration: none;">
                   Ver no Google Maps <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        `;

        marker.bindPopup(popupContent).openPopup();
    });
</script>
@endsection
