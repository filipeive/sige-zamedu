@extends('layouts.app')

@section('title', 'Gestão da Rede & Filiais')
@section('page-title', 'Gestão da Rede & Filiais')
@section('page-title-icon', 'fas fa-sitemap')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="no-underline text-slate-500">Início</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Rede &amp; Filiais</li>
@endsection

@section('content')
<div class="space-y-5" x-data="{ createModalOpen: false, editModalOpen: false, editBranch: {} }">

    {{-- 1. Hero Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 text-white p-6 shadow-md border border-slate-700/50">
        <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 rounded-full bg-teal-500/10 blur-2xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 uppercase tracking-wider">
                        <i class="fas fa-building-columns me-1"></i> Painel do Proprietário &amp; Direção Geral
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-white/10 text-white border border-white/15">
                        Rede Visionários
                    </span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    <i class="fas fa-sitemap text-emerald-400"></i> Gestão de Escolas &amp; Filiais
                </h1>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                    Adicione novas unidades de ensino à rede, altere responsáveis pedagógicos, configure dados de contacto ou desative filiais com segurança.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <button type="button" @click="createModalOpen = true" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white shadow-sm transition border-0 cursor-pointer">
                    <i class="fas fa-plus"></i> Adicionar Nova Filial
                </button>
                <a href="{{ route('branch.switch', 'all') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-xs font-bold text-white transition">
                    <i class="fas fa-network-wired text-emerald-300"></i> Visão Consolidada
                </a>
            </div>
        </div>
    </div>

    {{-- 2. Estatísticas Consolidadas da Rede --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total de Filiais</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $branches->count() }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-school"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-teal-500 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Unidades Ativas</span>
                <span class="text-xl font-black text-teal-800 mt-0.5 block">{{ $branches->where('is_active', true)->count() }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-blue-500 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Alunos na Rede</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ number_format($branches->sum('active_students_count')) }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200 border-l-4 border-l-purple-500 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total de Turmas</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $branches->sum('classes_count') }}</span>
            </div>
            <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard"></i>
            </div>
        </div>
    </div>

    {{-- 3. Lista de Filiais --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-building-circle-check text-emerald-600"></i> Filiais Registadas da Rede ({{ $branches->count() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-3 px-4">Filial / Escola</th>
                        <th class="py-3 px-3">Localização</th>
                        <th class="py-3 px-3">Contacto / Email</th>
                        <th class="py-3 px-3">Diretor / Responsável</th>
                        <th class="py-3 px-3 text-center">Alunos</th>
                        <th class="py-3 px-3 text-center">Turmas</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($branches as $b)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4">
                                <div class="font-black text-slate-800 text-sm flex items-center gap-2">
                                    <i class="fas fa-school text-emerald-600 text-xs"></i>
                                    {{ $b->name }}
                                </div>
                                <span class="font-mono text-[10px] text-slate-400 block mt-0.5">Código: {{ $b->code }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                <div class="font-semibold text-slate-800">{{ $b->district?->name ?? 'Quelimane' }}</div>
                                <span class="text-[10px] text-slate-400 block">{{ $b->address ?: 'Endereço não informado' }}</span>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                <div class="font-semibold text-slate-800">{{ $b->phone ?: 'Sem telefone' }}</div>
                                <span class="text-[10px] text-slate-400 block truncate max-w-[150px]">{{ $b->email ?: 'Sem email' }}</span>
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-700">
                                {{ $b->principal_name ?: 'Direção Geral' }}
                            </td>
                            <td class="py-3 px-3 text-center font-black text-slate-800">
                                {{ $b->active_students_count }}
                            </td>
                            <td class="py-3 px-3 text-center font-black text-slate-800">
                                {{ $b->classes_count }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $b->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                    {{ $b->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-end">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Alternar para Filial --}}
                                    <a href="{{ route('branch.switch', $b->id) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Aceder a esta Filial">
                                        <i class="fas fa-arrow-right-to-bracket text-xs"></i>
                                    </a>

                                    {{-- Editar Filial --}}
                                    <button type="button"
                                        @click="editBranch = {{ json_encode($b) }}; editModalOpen = true;"
                                        class="h-7 w-7 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 flex items-center justify-center transition border-0 cursor-pointer" title="Editar Informações">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    {{-- Alternar Status --}}
                                    <form action="{{ route('owner.branches.toggle-status', $b->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="h-7 w-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 flex items-center justify-center transition border-0 cursor-pointer" title="{{ $b->is_active ? 'Desativar Filial' : 'Ativar Filial' }}">
                                            <i class="fas {{ $b->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-xs"></i>
                                        </button>
                                    </form>

                                    {{-- Eliminar Filial --}}
                                    <form action="{{ route('owner.branches.destroy', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar/desativar a filial {{ $b->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-7 w-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 flex items-center justify-center transition border-0 cursor-pointer" title="Eliminar Filial">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i class="fas fa-school text-3xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhuma filial registada na sua rede.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: ADICIONAR NOVA FILIAL --}}
    <div x-show="createModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-slate-200" @click.away="createModalOpen = false">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-600"></i> Adicionar Nova Filial à Rede
                </h3>
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 border-0 bg-transparent cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('owner.branches.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Nome da Filial *</label>
                    <input type="text" name="name" required class="w-full rounded-xl border-slate-200 text-xs font-semibold" placeholder="Ex: Escola dos Visionários — Alto Molócuè">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Código (Opcional)</label>
                        <input type="text" name="code" class="w-full rounded-xl border-slate-200 text-xs font-semibold" placeholder="Ex: VIS-MOL">
                    </div>
                    <div>
                        <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Distrito / Cidade</label>
                        <select name="district_id" class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Telefone</label>
                        <input type="text" name="phone" class="w-full rounded-xl border-slate-200 text-xs font-semibold" placeholder="+258 84 ...">
                    </div>
                    <div>
                        <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Email</label>
                        <input type="email" name="email" class="w-full rounded-xl border-slate-200 text-xs font-semibold" placeholder="filial@visionarios.co.mz">
                    </div>
                </div>

                <div>
                    <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Diretor / Responsável da Filial</label>
                    <input type="text" name="principal_name" class="w-full rounded-xl border-slate-200 text-xs font-semibold" placeholder="Nome do Responsável">
                </div>

                <div>
                    <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Endereço Completo</label>
                    <input type="text" name="address" class="w-full rounded-xl border-slate-200 text-xs font-semibold" placeholder="Bairro, Rua, Cidade">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white transition cursor-pointer shadow-2xs border-0">
                        <i class="fas fa-save me-1"></i> Registar Filial
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: EDITAR FILIAL --}}
    <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-slate-200" @click.away="editModalOpen = false">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
                    <i class="fas fa-edit text-blue-600"></i> Editar Informações da Filial
                </h3>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 border-0 bg-transparent cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form :action="'{{ url('owner/branches') }}/' + editBranch.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Nome da Filial *</label>
                    <input type="text" name="name" x-model="editBranch.name" required class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Telefone</label>
                        <input type="text" name="phone" x-model="editBranch.phone" class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Email</label>
                        <input type="email" name="email" x-model="editBranch.email" class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Diretor / Responsável</label>
                    <input type="text" name="principal_name" x-model="editBranch.principal_name" class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Endereço Completo</label>
                    <input type="text" name="address" x-model="editBranch.address" class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                </div>

                <div>
                    <label class="block text-2xs font-extrabold text-slate-500 uppercase mb-1">Status da Filial</label>
                    <select name="is_active" x-model="editBranch.is_active" class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                        <option value="1">Ativa (Operacional)</option>
                        <option value="0">Inativa (Suspensa)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-xs font-bold text-white transition cursor-pointer shadow-2xs border-0">
                        <i class="fas fa-save me-1"></i> Guardar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

