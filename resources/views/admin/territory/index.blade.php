@extends('layouts.app')

@section('title', 'Gestão Territorial & Escolas')
@section('page-title', 'Gestão Territorial & Escolas')
@section('page-title-icon', 'fas fa-map-location-dot')

@section('breadcrumbs')
    <li class="breadcrumb-item">Territory</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Gestão Territorial & Escolas</li>
@endsection

@section('content')
<div class="w-full px-4 py-6">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="text-emerald-500 hover:text-emerald-700" onclick="this.parentElement.remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 mb-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-xl mt-0.5"></i>
                <div>
                    <strong class="text-sm font-bold">Ops! Ocorreram erros na validação:</strong>
                    <ul class="mt-1 text-xs list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-2xl p-6 shadow-md text-white mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md text-white">
                <i class="fa-solid fa-sitemap text-3xl"></i>
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-bold tracking-tight">Centro de Gestão Territorial & Escolas</h2>
                <p class="text-xs md:text-sm text-emerald-100 mt-0.5">
                    República de Moçambique — Ministério da Educação e Desenvolvimento Humano (MINEDH)
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('territorial.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-emerald-700 font-semibold text-xs shadow-sm hover:bg-emerald-50 transition">
                <i class="fa-solid fa-map-location-dot"></i> Mapa & Dashboard
            </a>
            <a href="{{ route('onboarding.school') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-950 text-white font-semibold text-xs shadow-sm hover:bg-emerald-900 transition">
                <i class="fa-solid fa-plus-circle"></i> Onboarding Assistido
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i class="fa-solid fa-school text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Escolas</div>
                <div class="text-2xl font-black text-slate-800">{{ count($schools) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-sky-50 text-sky-600 rounded-xl">
                <i class="fa-solid fa-network-wired text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">ZIPs</div>
                <div class="text-2xl font-black text-slate-800">{{ count($zips) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <i class="fa-solid fa-landmark text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Distritos</div>
                <div class="text-2xl font-black text-slate-800">{{ count($districts) }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <i class="fa-solid fa-map text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">Províncias</div>
                <div class="text-2xl font-black text-slate-800">{{ count($provincesList) }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.territory.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            
            <div class="md:col-span-3 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition" 
                       placeholder="Pesquisar por nome ou código..." value="{{ $search }}">
            </div>

            <div class="md:col-span-3">
                <select name="province_id" id="filter_province_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">Todas as Províncias</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ $selectedProvinceId == $prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <select name="district_id" id="filter_district_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">Todos os Distritos</option>
                    @foreach($districtsDropdown as $dist)
                        <option value="{{ $dist->id }}" data-province="{{ $dist->province_id }}" {{ $selectedDistrictId == $dist->id ? 'selected' : '' }}>
                            {{ $dist->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="zip_id" id="filter_zip_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="">Todas as ZIPs</option>
                    @foreach($zipsDropdown as $z)
                        <option value="{{ $z->id }}" data-district="{{ $z->district_id }}" {{ $selectedZipId == $z->id ? 'selected' : '' }}>
                            {{ $z->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-1 flex items-center gap-1">
                <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center justify-center">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="{{ route('admin.territory.index', ['tab' => $activeTab]) }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition" title="Limpar Filtros">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabs & Content -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="border-b border-slate-200 px-6 pt-4 pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-50/50">
            <div class="flex items-center space-x-2 border-b-2 border-transparent">
                <a class="px-4 py-3 font-semibold text-xs rounded-t-xl transition flex items-center gap-2 {{ $activeTab === 'schools' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}" 
                   href="{{ route('admin.territory.index', array_merge(request()->query(), ['tab' => 'schools'])) }}">
                    <i class="fa-solid fa-school"></i> Escolas ({{ count($schools) }})
                </a>
                <a class="px-4 py-3 font-semibold text-xs rounded-t-xl transition flex items-center gap-2 {{ $activeTab === 'zips' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}" 
                   href="{{ route('admin.territory.index', array_merge(request()->query(), ['tab' => 'zips'])) }}">
                    <i class="fa-solid fa-network-wired"></i> ZIPs ({{ count($zips) }})
                </a>
                <a class="px-4 py-3 font-semibold text-xs rounded-t-xl transition flex items-center gap-2 {{ $activeTab === 'districts' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}" 
                   href="{{ route('admin.territory.index', array_merge(request()->query(), ['tab' => 'districts'])) }}">
                    <i class="fa-solid fa-landmark"></i> Distritos ({{ count($districts) }})
                </a>
                <a class="px-4 py-3 font-semibold text-xs rounded-t-xl transition flex items-center gap-2 {{ $activeTab === 'provinces' ? 'bg-white text-emerald-700 border-t-2 border-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}" 
                   href="{{ route('admin.territory.index', array_merge(request()->query(), ['tab' => 'provinces'])) }}">
                    <i class="fa-solid fa-map"></i> Províncias ({{ count($provincesList) }})
                </a>
            </div>

            <div class="mb-3 sm:mb-0">
                @if($activeTab === 'schools')
                    <button class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCreateSchool" onclick="resetSchoolModal()">
                        <i class="fa-solid fa-plus"></i> Nova Escola
                    </button>
                @elseif($activeTab === 'zips')
                    <button class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCreateZip" onclick="resetZipModal()">
                        <i class="fa-solid fa-plus"></i> Nova ZIP
                    </button>
                @elseif($activeTab === 'districts')
                    <button class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCreateDistrict" onclick="resetDistrictModal()">
                        <i class="fa-solid fa-plus"></i> Novo Distrito
                    </button>
                @elseif($activeTab === 'provinces')
                    <button class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition inline-flex items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalCreateProvince" onclick="resetProvinceModal()">
                        <i class="fa-solid fa-plus"></i> Nova Província
                    </button>
                @endif
            </div>
        </div>

        <div class="p-0">
            
            <!-- TAB 1: ESCOLAS -->
            @if($activeTab === 'schools')
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Código / Escola</th>
                                <th class="py-3 px-4">Tipo</th>
                                <th class="py-3 px-4">Província & Distrito</th>
                                <th class="py-3 px-4">ZIP (Pedagógica)</th>
                                <th class="py-3 px-4">Director / Contacto</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($schools as $s)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-school text-emerald-600"></i>
                                            <div>
                                                <a href="{{ route('territorial.school', $s->id) }}" class="hover:text-emerald-700 transition">
                                                    {{ $s->name }}
                                                </a>
                                                <div class="text-slate-400 text-xs font-normal">Cód: {{ $s->code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                            {{ $s->type === 'Pública' ? 'bg-sky-100 text-sky-800' : ($s->type === 'Privada' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                                            {{ $s->type_label ?? $s->type }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-800">{{ $s->province->name ?? 'N/A' }}</div>
                                        <div class="text-slate-500 text-xs">{{ $s->district->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($s->zip)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs font-medium">
                                                <i class="fa-solid fa-network-wired text-sky-600 mr-1"></i> {{ $s->zip->name }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">Sem ZIP</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-800">{{ $s->principal_name ?? '—' }}</div>
                                        <div class="text-slate-500 text-xs">{{ $s->phone ?? $s->email }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($s->is_active)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-semibold text-xs">Ativa</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-semibold text-xs">Inativa</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('territorial.school', $s->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 transition" title="Inspecionar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" 
                                                    title="Editar Escola"
                                                    onclick="editSchool({{ json_encode($s) }})">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form action="{{ route('admin.territory.schools.toggle-status', $s->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg border {{ $s->is_active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }} transition" 
                                                        title="{{ $s->is_active ? 'Desativar' : 'Ativar' }}">
                                                    <i class="fa-solid {{ $s->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10 text-slate-400">
                                        <i class="fa-solid fa-school text-4xl mb-2 opacity-40"></i>
                                        <p class="font-medium text-slate-600">Nenhuma escola encontrada com os filtros selecionados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            <!-- TAB 2: ZIPS -->
            @elseif($activeTab === 'zips')
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Código / Nome da ZIP</th>
                                <th class="py-3 px-4">Distrito & Província</th>
                                <th class="py-3 px-4">Escola Sede / Coordenação</th>
                                <th class="py-3 px-4 text-center">Escolas Agregadas</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($zips as $z)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <div>{{ $z->name }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-xs">{{ $z->code }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-800">{{ $z->district->name ?? 'N/A' }}</div>
                                        <div class="text-slate-500 text-xs">{{ $z->district->province->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 font-medium">
                                        {{ $z->headquarters ?? 'Não especificada' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 font-semibold text-xs">
                                            {{ $z->schools->count() }} Escolas
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($z->is_active)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-semibold text-xs">Ativa</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-semibold text-xs">Inativa</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('admin.territory.zips.show', $z->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Inspecionar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" 
                                                    title="Editar ZIP"
                                                    onclick="editZip({{ json_encode($z) }})">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form action="{{ route('admin.territory.zips.toggle-status', $z->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg border {{ $z->is_active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }} transition" 
                                                        title="{{ $z->is_active ? 'Desativar ZIP' : 'Ativar ZIP' }}">
                                                    <i class="fa-solid {{ $z->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-slate-400">
                                        <i class="fa-solid fa-network-wired text-4xl mb-2 opacity-40"></i>
                                        <p class="font-medium text-slate-600">Nenhuma ZIP encontrada.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            <!-- TAB 3: DISTRITOS -->
            @elseif($activeTab === 'districts')
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Código / Nome do Distrito</th>
                                <th class="py-3 px-4">Província</th>
                                <th class="py-3 px-4 text-center">Total ZIPs</th>
                                <th class="py-3 px-4 text-center">Total Escolas</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($districts as $dist)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <div>{{ $dist->name }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-xs">{{ $dist->code }}</span>
                                    </td>
                                    <td class="py-3 px-4 font-medium text-slate-800">
                                        {{ $dist->province->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 font-semibold text-xs">
                                            {{ $dist->zips->count() }} ZIPs
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold text-xs">
                                            {{ $dist->schools->count() }} Escolas
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($dist->is_active)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-semibold text-xs">Ativo</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-semibold text-xs">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('admin.territory.districts.show', $dist->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Inspecionar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" 
                                                    title="Editar Distrito"
                                                    onclick="editDistrict({{ json_encode($dist) }})">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <form action="{{ route('admin.territory.districts.toggle-status', $dist->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg border {{ $dist->is_active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }} transition" 
                                                        title="{{ $dist->is_active ? 'Desativar' : 'Ativar' }}">
                                                    <i class="fa-solid {{ $dist->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-slate-400">
                                        <i class="fa-solid fa-landmark text-4xl mb-2 opacity-40"></i>
                                        <p class="font-medium text-slate-600">Nenhum distrito encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            <!-- TAB 4: PROVÍNCIAS -->
            @elseif($activeTab === 'provinces')
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">Código / Nome da Província</th>
                                <th class="py-3 px-4 text-center">Total Distritos</th>
                                <th class="py-3 px-4 text-center">Total Escolas</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($provincesList as $prov)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900">
                                        <div>{{ $prov->name }}</div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-xs">{{ $prov->code }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 font-semibold text-xs">
                                            {{ $prov->districts->count() }} Distritos
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold text-xs">
                                            {{ $prov->schools->count() }} Escolas
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($prov->is_active)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-semibold text-xs">Ativa</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-semibold text-xs">Inativa</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('admin.territory.provinces.show', $prov->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Inspecionar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" 
                                                    title="Editar Província"
                                                    onclick="editProvince({{ json_encode($prov) }})">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <form action="{{ route('admin.territory.provinces.toggle-status', $prov->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-lg border {{ $prov->is_active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }} transition" 
                                                        title="{{ $prov->is_active ? 'Desativar' : 'Ativar' }}">
                                                    <i class="fa-solid {{ $prov->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-slate-400">
                                        <i class="fa-solid fa-map text-4xl mb-2 opacity-40"></i>
                                        <p class="font-medium text-slate-600">Nenhuma província registada.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>

<!-- ==================== MODAIS DE CRIAÇÃO E EDIÇÃO ==================== -->

<!-- 1. MODAL ESCOLA (CRIAR / EDITAR) -->
<div class="modal fade" id="modalCreateSchool" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden">
            <form id="formSchool" method="POST" action="{{ route('admin.territory.schools.store') }}">
                @csrf
                <input type="hidden" id="school_method" name="_method" value="POST">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h5 class="text-base font-bold text-slate-800 flex items-center gap-2" id="modalSchoolTitle">
                        <i class="fa-solid fa-school text-emerald-600"></i> Registar Nova Escola
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600 text-lg" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-8">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nome Oficial da Escola *</label>
                            <input type="text" name="name" id="school_name" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: Escola Secundária de Marracuene">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Código Único MINEDH *</label>
                            <input type="text" name="code" id="school_code" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: SCH-MAR-001">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tipo de Ensino *</label>
                            <select name="type" id="school_type" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required>
                                <option value="Pública">Pública</option>
                                <option value="Privada">Privada</option>
                                <option value="Comunitária">Comunitária</option>
                            </select>
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Província *</label>
                            <select name="province_id" id="school_province_id" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required>
                                <option value="">Selecione...</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Distrito *</label>
                            <select name="district_id" id="school_district_id" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required>
                                <option value="">Selecione...</option>
                                @foreach($districtsDropdown as $dist)
                                    <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ZIP (Pedagógica)</label>
                            <select name="zip_id" id="school_zip_id" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5">
                                <option value="">Nenhuma ZIP (Opcional)</option>
                                @foreach($zipsDropdown as $z)
                                    <option value="{{ $z->id }}">{{ $z->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nome do Director/a</label>
                            <input type="text" name="principal_name" id="school_principal_name" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" placeholder="Ex: Prof. Dr. Armando Mabote">
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Telefone Principal</label>
                            <input type="text" name="phone" id="school_phone" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" placeholder="+258 84 000 0000">
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">E-mail Institucional</label>
                            <input type="email" name="email" id="school_email" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" placeholder="direcao@escola.ac.mz">
                        </div>
                        <div class="md:col-span-12">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Endereço Físico / Localização</label>
                            <textarea name="address" id="school_address" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" rows="2" placeholder="Av. Principal, Bairro 1, Cidade/Distrito"></textarea>
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Latitude (Coordenada GPS)</label>
                            <input type="number" step="any" name="latitude" id="school_latitude" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" placeholder="Ex: -17.878600">
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Longitude (Coordenada GPS)</label>
                            <input type="number" step="any" name="longitude" id="school_longitude" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" placeholder="Ex: 36.888300">
                        </div>
                        <div class="md:col-span-12">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-600 uppercase">Marcar Localização no Mapa</label>
                                <button type="button" onclick="getModalGpsLocation()" class="text-xs text-emerald-600 font-bold hover:underline flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-location-crosshairs text-amber-500"></i> Usar GPS Atual
                                </button>
                            </div>
                            <div class="rounded-xl border border-slate-300 overflow-hidden relative h-52 z-0 shadow-2xs">
                                <div id="modal_school_map" class="w-full h-full"></div>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">
                                <i class="fas fa-hand-pointer text-emerald-600 me-1"></i> Clique no mapa para definir as coordenadas ou arraste o pino para a posição exata da escola.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">Salvar Escola</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL ZIP (CRIAR / EDITAR) -->
<div class="modal fade" id="modalCreateZip" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden">
            <form id="formZip" method="POST" action="{{ route('admin.territory.zips.store') }}">
                @csrf
                <input type="hidden" id="zip_method" name="_method" value="POST">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h5 class="text-base font-bold text-slate-800 flex items-center gap-2" id="modalZipTitle">
                        <i class="fa-solid fa-network-wired text-sky-600"></i> Criar Zona de Influência Pedagógica (ZIP)
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600 text-lg" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-8">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nome da ZIP *</label>
                            <input type="text" name="name" id="zip_name" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: ZIP N.º 01 - Marracuene Sede">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Código *</label>
                            <input type="text" name="code" id="zip_code" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: ZIP-MAR-01">
                        </div>
                        <div class="md:col-span-12">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Distrito *</label>
                            <select name="district_id" id="zip_district_id" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required>
                                <option value="">Selecione o Distrito...</option>
                                @foreach($districtsDropdown as $dist)
                                    <option value="{{ $dist->id }}">{{ $dist->name }} ({{ $dist->province->name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-12">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Escola Sede / Local da Coordenação</label>
                            <input type="text" name="headquarters" id="zip_headquarters" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" placeholder="Ex: EPC 16 de Junho">
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">Salvar ZIP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. MODAL DISTRITO (CRIAR / EDITAR) -->
<div class="modal fade" id="modalCreateDistrict" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden">
            <form id="formDistrict" method="POST" action="{{ route('admin.territory.districts.store') }}">
                @csrf
                <input type="hidden" id="district_method" name="_method" value="POST">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h5 class="text-base font-bold text-slate-800 flex items-center gap-2" id="modalDistrictTitle">
                        <i class="fa-solid fa-landmark text-purple-600"></i> Registar Novo Distrito
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600 text-lg" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-8">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nome do Distrito *</label>
                            <input type="text" name="name" id="district_name" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: Marracuene">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Código *</label>
                            <input type="text" name="code" id="district_code" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: DIST-MAR">
                        </div>
                        <div class="md:col-span-12">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Província *</label>
                            <select name="province_id" id="district_province_id" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required>
                                <option value="">Selecione a Província...</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">Salvar Distrito</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. MODAL PROVÍNCIA (CRIAR / EDITAR) -->
<div class="modal fade" id="modalCreateProvince" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden">
            <form id="formProvince" method="POST" action="{{ route('admin.territory.provinces.store') }}">
                @csrf
                <input type="hidden" id="province_method" name="_method" value="POST">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h5 class="text-base font-bold text-slate-800 flex items-center gap-2" id="modalProvinceTitle">
                        <i class="fa-solid fa-map text-amber-600"></i> Registar Nova Província
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600 text-lg" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-8">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nome da Província *</label>
                            <input type="text" name="name" id="province_name" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: Maputo Província">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Código *</label>
                            <input type="text" name="code" id="province_code" class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 p-2.5" required placeholder="Ex: PRV-MPT">
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold transition" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">Salvar Província</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let modalSchoolMap = null;
    let modalSchoolMarker = null;

    function initModalSchoolMap(initialLat, initialLng) {
        const latInput = document.getElementById('school_latitude');
        const lngInput = document.getElementById('school_longitude');
        const container = document.getElementById('modal_school_map');

        if (!container) return;

        let lat = parseFloat(initialLat || latInput?.value) || -17.8764;
        let lng = parseFloat(initialLng || lngInput?.value) || 36.8791;

        setTimeout(() => {
            if (!modalSchoolMap) {
                modalSchoolMap = L.map('modal_school_map').setView([lat, lng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(modalSchoolMap);

                const pinIcon = L.divIcon({
                    className: 'custom-modal-pin',
                    html: `<div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg border-2 border-white transform -translate-x-1/2 -translate-y-1/2 cursor-grab"><i class="fas fa-school text-xs"></i></div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                modalSchoolMarker = L.marker([lat, lng], { draggable: true, icon: pinIcon }).addTo(modalSchoolMap);

                function syncModalInputs(newLat, newLng) {
                    if (latInput) latInput.value = newLat.toFixed(6);
                    if (lngInput) lngInput.value = newLng.toFixed(6);
                }

                modalSchoolMarker.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    syncModalInputs(pos.lat, pos.lng);
                });

                modalSchoolMap.on('click', function(e) {
                    modalSchoolMarker.setLatLng(e.latlng);
                    syncModalInputs(e.latlng.lat, e.latlng.lng);
                });
            } else {
                modalSchoolMap.setView([lat, lng], 14);
                if (modalSchoolMarker) modalSchoolMarker.setLatLng([lat, lng]);
            }

            modalSchoolMap.invalidateSize();
        }, 250);
    }

    function getModalGpsLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                document.getElementById('school_latitude').value = lat.toFixed(6);
                document.getElementById('school_longitude').value = lng.toFixed(6);
                if (modalSchoolMap && modalSchoolMarker) {
                    modalSchoolMarker.setLatLng([lat, lng]);
                    modalSchoolMap.setView([lat, lng], 16);
                }
            }, function(error) {
                alert('Não foi possível obter a localização GPS: ' + error.message);
            });
        } else {
            alert('Navegação GPS não suportada pelo seu navegador.');
        }
    }

    function resetSchoolModal() {
        document.getElementById('modalSchoolTitle').innerHTML = '<i class="fa-solid fa-school text-emerald-600"></i> Registar Nova Escola';
        document.getElementById('formSchool').action = '{{ route("admin.territory.schools.store") }}';
        document.getElementById('school_method').value = 'POST';
        document.getElementById('formSchool').reset();
        initModalSchoolMap(-17.8764, 36.8791);
    }

    function resetZipModal() {
        document.getElementById('modalZipTitle').innerHTML = '<i class="fa-solid fa-network-wired text-sky-600"></i> Criar Zona de Influência Pedagógica (ZIP)';
        document.getElementById('formZip').action = '{{ route("admin.territory.zips.store") }}';
        document.getElementById('zip_method').value = 'POST';
        document.getElementById('formZip').reset();
    }

    function resetDistrictModal() {
        document.getElementById('modalDistrictTitle').innerHTML = '<i class="fa-solid fa-landmark text-purple-600"></i> Registar Novo Distrito';
        document.getElementById('formDistrict').action = '{{ route("admin.territory.districts.store") }}';
        document.getElementById('district_method').value = 'POST';
        document.getElementById('formDistrict').reset();
    }

    function resetProvinceModal() {
        document.getElementById('modalProvinceTitle').innerHTML = '<i class="fa-solid fa-map text-amber-600"></i> Registar Nova Província';
        document.getElementById('formProvince').action = '{{ route("admin.territory.provinces.store") }}';
        document.getElementById('province_method').value = 'POST';
        document.getElementById('formProvince').reset();
    }

    function editSchool(school) {
        document.getElementById('modalSchoolTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-emerald-600"></i> Editar Escola: ' + school.name;
        document.getElementById('formSchool').action = '/admin/territory/schools/' + school.id;
        document.getElementById('school_method').value = 'PUT';
        document.getElementById('school_name').value = school.name || '';
        document.getElementById('school_code').value = school.code || '';
        document.getElementById('school_type').value = school.type_label || school.type || 'Pública';
        document.getElementById('school_province_id').value = school.province_id || '';
        document.getElementById('school_district_id').value = school.district_id || '';
        document.getElementById('school_zip_id').value = school.zip_id || '';
        document.getElementById('school_principal_name').value = school.principal_name || '';
        document.getElementById('school_phone').value = school.phone || '';
        document.getElementById('school_email').value = school.email || '';
        document.getElementById('school_address').value = school.address || '';
        document.getElementById('school_latitude').value = school.latitude !== null && school.latitude !== undefined ? school.latitude : '';
        document.getElementById('school_longitude').value = school.longitude !== null && school.longitude !== undefined ? school.longitude : '';
        
        initModalSchoolMap(school.latitude, school.longitude);

        var modalEl = document.getElementById('modalCreateSchool');
        if (window.bootstrap && window.bootstrap.Modal) {
            new window.bootstrap.Modal(modalEl).show();
        } else if (modalEl) {
            modalEl.style.display = 'flex';
            modalEl.classList.add('show');
        }
    }

    function editZip(zip) {
        document.getElementById('modalZipTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-sky-600"></i> Editar ZIP: ' + zip.name;
        document.getElementById('formZip').action = '/admin/territory/zips/' + zip.id;
        document.getElementById('zip_method').value = 'PUT';
        document.getElementById('zip_name').value = zip.name || '';
        document.getElementById('zip_code').value = zip.code || '';
        document.getElementById('zip_district_id').value = zip.district_id || '';
        document.getElementById('zip_headquarters').value = zip.headquarters || '';
        
        var modalEl = document.getElementById('modalCreateZip');
        if (window.bootstrap && window.bootstrap.Modal) {
            new window.bootstrap.Modal(modalEl).show();
        } else if (modalEl) {
            modalEl.style.display = 'flex';
            modalEl.classList.add('show');
        }
    }

    function editDistrict(district) {
        document.getElementById('modalDistrictTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-purple-600"></i> Editar Distrito: ' + district.name;
        document.getElementById('formDistrict').action = '/admin/territory/districts/' + district.id;
        document.getElementById('district_method').value = 'PUT';
        document.getElementById('district_name').value = district.name || '';
        document.getElementById('district_code').value = district.code || '';
        document.getElementById('district_province_id').value = district.province_id || '';
        
        var modalEl = document.getElementById('modalCreateDistrict');
        if (window.bootstrap && window.bootstrap.Modal) {
            new window.bootstrap.Modal(modalEl).show();
        } else if (modalEl) {
            modalEl.style.display = 'flex';
            modalEl.classList.add('show');
        }
    }

    function editProvince(province) {
        document.getElementById('modalProvinceTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-amber-600"></i> Editar Província: ' + province.name;
        document.getElementById('formProvince').action = '/admin/territory/provinces/' + province.id;
        document.getElementById('province_method').value = 'PUT';
        document.getElementById('province_name').value = province.name || '';
        document.getElementById('province_code').value = province.code || '';
        
        var modalEl = document.getElementById('modalCreateProvince');
        if (window.bootstrap && window.bootstrap.Modal) {
            new window.bootstrap.Modal(modalEl).show();
        } else if (modalEl) {
            modalEl.style.display = 'flex';
            modalEl.classList.add('show');
        }
    }
</script>
@endsection
