@extends('layouts.app')

@section('title', 'Gestão de Encarregados de Educação')
@section('page-title', 'Encarregados de Educação')
@section('page-title-icon', 'fas fa-users-cog')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Início</a></li>
    <li class="breadcrumb-item active">Encarregados</li>
@endsection

@section('content')
<div class="max-w-full mx-auto p-4 space-y-6">
    <!-- Stat KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-emerald-50 border-l-4 border-emerald-600 rounded-xl shadow-sm p-4 flex items-center">
            <div class="flex-shrink-0 bg-emerald-100 text-emerald-600 rounded-full p-3 mr-3">
                <i class="fas fa-users-cog fa-lg"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-emerald-800 uppercase">Total Encarregados</div>
                <h4 class="text-2xl font-bold text-emerald-700">{{ $totalParents }}</h4>
            </div>
        </div>
        <div class="bg-green-50 border-l-4 border-green-600 rounded-xl shadow-sm p-4 flex items-center">
            <div class="flex-shrink-0 bg-green-100 text-green-600 rounded-full p-3 mr-3">
                <i class="fas fa-user-graduate fa-lg"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-green-800 uppercase">Educandos Associados</div>
                <h4 class="text-2xl font-bold text-green-700">{{ \App\Models\Student::whereNotNull('parent_id')->count() }}</h4>
            </div>
        </div>
        <div class="bg-blue-50 border-l-4 border-blue-600 rounded-xl shadow-sm p-4 flex items-center">
            <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-full p-3 mr-3">
                <i class="fas fa-shield-alt fa-lg"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-blue-800 uppercase">Contas do Portal dos Pais</div>
                <h4 class="text-2xl font-bold text-blue-700">{{ \App\Models\User::role('parent')->count() }}</h4>
            </div>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form action="{{ route('parents.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-10">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pesquisar Encarregados</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 py-2 px-3 text-sm"
                       placeholder="Pesquisar por nome, e‑mail, telefone ou N.º de BI..." />
            </div>
            <div class="md:col-span-2 flex space-x-2">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-4 py-2 text-sm flex items-center">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
                @if(request()->filled('search'))
                <a href="{{ route('parents.index') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-lg px-4 py-2 text-sm" title="Limpar">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Parents -->
    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <div class="flex items-center justify-between p-4">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-users text-primary mr-2"></i>
                Lista de Encarregados de Educação
                <span class="badge bg-primary ml-2" style="font-size:0.55em;">{{ $parents->total() }}</span>
            </h3>
            @can('create_students')
            <a href="{{ route('parents.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-4 py-2 text-sm flex items-center">
                <i class="fas fa-plus mr-1"></i> Registar Encarregado
            </a>
            @endcan
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Encarregado(a)</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contacto / E‑mail</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Profissão / Trabalho</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Educando(s)</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200">
                @forelse($parents as $parent)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-2">
                        <div class="flex items-center">
                            <div class="w-9 h-9 flex items-center justify-center rounded-full bg-primary-100 text-primary font-bold mr-3">
                                {{ strtoupper(substr($parent->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">{{ $parent->full_name }}</div>
                                <small class="text-muted">BI: <code>{{ $parent->bi_number ?? 'N/A' }}</code></small>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2">
                        <div class="font-bold text-slate-800"><i class="fas fa-phone mr-1 text-gray-500"></i> {{ $parent->phone }}</div>
                        <small class="text-muted"><i class="fas fa-envelope mr-1"></i> {{ $parent->email }}</small>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-slate-800 font-semibold">{{ $parent->profession ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $parent->workplace ?? '' }}</small>
                    </td>
                    <td class="px-4 py-2">
                        @forelse($parent->students as $st)
                        <span class="bg-primary-100 text-primary border border-primary-100 rounded-full px-2 py-1 text-xs mr-1 mb-1 inline-flex items-center">
                            <i class="fas fa-user-graduate mr-1"></i> {{ $st->first_name }} {{ $st->last_name }}
                        </span>
                        @empty
                        <span class="text-muted text-sm">Sem educandos associados</span>
                        @endforelse
                    </td>
                    <td class="px-4 py-2 text-right">
                        <div class="flex space-x-2 justify-end">
                            <a href="{{ route('parents.show', $parent->user_id) }}" class="text-primary-600 hover:bg-primary-100 rounded-lg p-2" title="Ver Perfil">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('edit_students')
                            <a href="{{ route('parents.edit', $parent->user_id) }}" class="text-amber-600 hover:bg-amber-100 rounded-lg p-2" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-users-slash fa-3x mb-3 text-slate-300"></i>
                        <h5>Nenhum encarregado encontrado</h5>
                        <p class="text-xs">Não foram encontrados registos com os termos pesquisados.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($parents->hasPages())
        <div class="p-4 border-t">
            {{ $parents->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
