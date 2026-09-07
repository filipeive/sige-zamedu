@extends('layouts.app')

@section('title', 'Gestão de Encarregados de Educação')
@section('page-title', 'Encarregados de Educação')
@section('page-title-icon', 'fas fa-users-cog')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Início</a></li>
    <li class="breadcrumb-item active">Encarregados</li>
@endsection

@section('content')
<div class="space-y-5">

    {{-- 1. Header Principal --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                    <i class="fas fa-users-cog me-1"></i> Comunidade Escolar
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                    Total: {{ number_format($totalParents) }} Encarregados
                </span>
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-users-cog text-emerald-600"></i> Gestão de Encarregados de Educação
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Registo de pais, tutores e encarregados, acompanhamento de educandos e gestão de acesso ao portal.</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @can('create_students')
                <a href="{{ route('parents.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-plus"></i> Registar Encarregado
                </a>
            @endcan
        </div>
    </div>

    {{-- 2. Métricas KPI Principais --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        {{-- Card 1: Total Encarregados --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-emerald-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Encarregados</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($totalParents) }}</span>
            </div>
        </div>

        {{-- Card 2: Educandos Associados --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-teal-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Educandos Associados</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">
                    {{ number_format(\App\Models\Student::whereNotNull('parent_id')->count()) }}
                </span>
            </div>
        </div>

        {{-- Card 3: Contas do Portal Ativas --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-sky-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-sky-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Portal dos Pais</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">
                    {{ number_format(\App\Models\User::role('parent')->count()) }}
                    <span class="text-xs font-bold text-slate-400">contas</span>
                </span>
            </div>
        </div>

        {{-- Card 4: Média Educandos / Encarregado --}}
        <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-2xs flex items-center gap-3.5 hover:border-amber-300 transition">
            <div class="w-1.5 h-10 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold shrink-0">
                <i class="fas fa-hands-helping"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Média Educandos</span>
                @php
                    $assocStudentsCount = \App\Models\Student::whereNotNull('parent_id')->count();
                    $ratio = $totalParents > 0 ? round($assocStudentsCount / $totalParents, 1) : 0;
                @endphp
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $ratio }} <span class="text-xs font-bold text-slate-400">/ tutor</span></span>
            </div>
        </div>
    </div>

    {{-- 3. Barra de Pesquisa e Filtros --}}
    <div class="bg-white rounded-xl border border-slate-200/80 p-3.5 shadow-2xs">
        <form action="{{ route('parents.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200/90 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500 bg-slate-50/50"
                    placeholder="Pesquisar por nome do encarregado, e‑mail, telefone ou N.º de BI..." />
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-2xs transition">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                @if(request()->filled('search'))
                    <a href="{{ route('parents.index') }}" class="no-underline inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition" title="Limpar Pesquisa">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. Tabela Principal de Encarregados --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-users-cog text-emerald-600"></i> Lista de Encarregados de Educação
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $parents->total() }}
                </span>
            </h3>

            @can('create_students')
                <a href="{{ route('parents.create') }}" class="no-underline inline-flex items-center gap-1 text-xs font-extrabold text-emerald-700 hover:text-emerald-800">
                    <i class="fas fa-user-plus text-[11px]"></i> Novo Encarregado
                </a>
            @endcan
        </div>

        <div>
            @if($parents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/60 text-slate-500 font-black uppercase tracking-wider text-[10px] border-b border-slate-200/80">
                                <th class="py-3 px-4">Encarregado(a)</th>
                                <th class="py-3 px-3">Contacto / E-mail</th>
                                <th class="py-3 px-3">Profissão / Trabalho</th>
                                <th class="py-3 px-4">Educando(s) Vinculados</th>
                                <th class="py-3 px-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($parents as $parent)
                            <tr class="hover:bg-slate-50/80 transition">
                                {{-- Nome e BI --}}
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($parent->first_name, 0, 1) . substr($parent->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('parents.show', $parent->user_id) }}" class="no-underline font-extrabold text-slate-800 hover:text-emerald-700 block text-xs">
                                                {{ $parent->full_name }}
                                            </a>
                                            <span class="text-[10px] text-slate-500 font-medium block mt-0.5">
                                                BI: <code class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded text-[9px] font-mono border border-slate-200">{{ $parent->bi_number ?? 'N/A' }}</code>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Contactos --}}
                                <td class="py-3.5 px-3 text-slate-700">
                                    <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                        <i class="fas fa-phone text-[10px] text-emerald-600"></i> {{ $parent->phone }}
                                    </div>
                                    @if($parent->email)
                                        <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1.5 mt-0.5">
                                            <i class="fas fa-envelope text-[9px] text-slate-400"></i> {{ $parent->email }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Profissão --}}
                                <td class="py-3.5 px-3 text-slate-700">
                                    <span class="font-extrabold text-slate-800 block text-xs">{{ $parent->profession ?? 'Não informada' }}</span>
                                    @if($parent->workplace)
                                        <span class="text-[10px] text-slate-500 font-medium block mt-0.5">{{ $parent->workplace }}</span>
                                    @endif
                                </td>

                                {{-- Educandos --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($parent->students as $st)
                                            <a href="{{ route('students.show', $st->id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200/80 text-xs font-bold transition shadow-2xs">
                                                <i class="fas fa-user-graduate text-[9px] text-emerald-600"></i>
                                                {{ $st->first_name }} {{ $st->last_name }}
                                            </a>
                                        @empty
                                            <span class="text-[11px] text-slate-400 font-semibold italic">Sem educandos associados</span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Ações --}}
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('parents.show', $parent->user_id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[10px] transition" title="Ver Perfil">
                                            <i class="fas fa-eye text-[10px]"></i> Perfil
                                        </a>

                                        @can('edit_students')
                                            <a href="{{ route('parents.edit', $parent->user_id) }}" class="no-underline inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/70 font-extrabold text-[10px] transition" title="Editar Encarregado">
                                                <i class="fas fa-edit text-[10px]"></i> Editar
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($parents->hasPages())
                    <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
                        {{ $parents->links() }}
                    </div>
                @endif
            @else
                <div class="p-8 text-center text-slate-400 space-y-3">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h5 class="text-xs font-black text-slate-700 uppercase tracking-wider">Nenhum Encarregado Encontrado</h5>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Não foram encontrados registos correspondentes aos filtros ou à pesquisa inserida.</p>
                    @if(request()->filled('search'))
                        <a href="{{ route('parents.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition mt-2">
                            <i class="fas fa-redo"></i> Limpar Filtros de Pesquisa
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
