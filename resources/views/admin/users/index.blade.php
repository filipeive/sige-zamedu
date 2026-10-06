@extends('layouts.app')

@section('title', 'Gestão de Usuários')
@section('page-title', 'Gestão de Usuários')
@section('page-title-icon', 'fas fa-users-cog')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Usuários do Sistema</li>
@endsection

@section('title-icon', 'fas fa-users-cog')

@section('content')
<div class="space-y-4" x-data="{ deleteModal: false, deleteUserId: null, deleteUserName: '' }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-users-cog text-emerald-600"></i> Gestão de Usuários
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestão de contas, perfis e permissões de acesso ao sistema.</p>
        </div>
        @can('create_users')
            <a href="{{ route('admin.users.create') }}" class="no-underline inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                <i class="fas fa-plus"></i> Novo Usuário
            </a>
        @endcan
    </div>

    <!-- Stat KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Total Usuários</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ $totalUsers }}</span>
                <span class="text-[10px] text-emerald-600 font-bold"><i class="fas fa-check-circle mr-0.5"></i> {{ $activeUsers }} ativos</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Professores</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\Teacher::count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-teal-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-friends"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Encarregados</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\ParentModel::count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-2xs flex items-center gap-3">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Administrativos</span>
                <span class="text-xl font-black text-slate-800 mt-0.5 block">{{ \App\Models\User::role(['admin', 'secretary', 'pedagogy'])->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-2xs">
        <form method="GET" class="flex flex-col sm:flex-row items-center gap-2">
            <div class="flex-grow w-full relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full rounded-lg border-slate-200 ps-8 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500"
                       placeholder="Pesquisar por nome ou email...">
                <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <div class="w-full sm:w-48">
                <select name="role" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Perfis</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-40">
                <select name="status" class="w-full rounded-lg border-slate-200 py-1.5 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">Todos os Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-2xs transition">
                    <i class="fas fa-filter text-[10px]"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-users-cog text-emerald-600"></i> Usuários do Sistema ({{ $users->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <th class="py-2.5 px-4">Usuário</th>
                        <th class="py-2.5 px-3">Email</th>
                        <th class="py-2.5 px-3">Perfil</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3">Último Login</th>
                        <th class="py-2.5 px-4 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-2.5 px-4 font-bold text-slate-800">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span>{{ $user->name }}</span>
                                        <span class="block text-[10px] text-slate-400 font-mono font-normal">ID: {{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 font-mono">{{ $user->email }}</td>
                            <td class="py-2.5 px-3">
                                @foreach($user->roles as $role)
                                    @php
                                        $roleBadge = match($role->name) {
                                            'admin' => 'bg-rose-100 text-rose-800 border border-rose-200',
                                            'secretary' => 'bg-amber-100 text-amber-800 border border-amber-200',
                                            'pedagogy' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                            'teacher' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                            'parent' => 'bg-teal-100 text-teal-800 border border-teal-200',
                                            default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $roleBadge }}">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                    {{ $user->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600">
                                @if($user->last_login)
                                    <span>{{ $user->last_login->format('d/m/Y H:i') }}</span>
                                    <span class="block text-[10px] text-slate-400">{{ $user->last_login->diffForHumans() }}</span>
                                @else
                                    <span class="text-slate-400">Nunca</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-end">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.users.show', $user) }}" class="no-underline h-7 w-7 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @can('edit_users')
                                        <a href="{{ route('admin.users.edit', $user) }}" class="no-underline h-7 w-7 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 flex items-center justify-center transition" title="Editar">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @endcan
                                    @can('delete_users')
                                        @if(!$user->hasRole('super_admin') && $user->id !== auth()->id())
                                            <button type="button" @click="deleteUserId = {{ $user->id }}; deleteUserName = @json($user->name); deleteModal = true" class="h-7 w-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition" title="Excluir">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">
                                <i class="fas fa-users-slash text-2xl text-slate-300 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-600">Nenhum usuário encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-4 py-2 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">
                    Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} registros
                </span>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display:none;">
        <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl space-y-4 text-left" @click.outside="deleteModal = false">
            <h4 class="text-sm font-bold text-slate-800">Confirmar Exclusão</h4>
            <p class="text-xs text-slate-600">Tem certeza que deseja excluir o usuário <strong x-text="deleteUserName"></strong>?</p>
            <p class="text-[10px] text-rose-600 font-bold">Esta ação não pode ser desfeita.</p>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" @click="deleteModal = false" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">Cancelar</button>
                <form :action="`/admin/users/${deleteUserId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-bold text-white shadow-sm transition">Excluir Usuário</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
