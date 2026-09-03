@extends('layouts.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')
@section('page-title-icon', 'fas fa-user-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Editar {{ $user->name }}</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-user-edit text-amber-600"></i> Editar Usuário: {{ $user->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Atualize as informações do perfil e permissões do usuário.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-amber-500 shadow-2xs p-5 max-w-4xl">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nome Completo *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('name') border-rose-500 @enderror">
                    @error('name')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('email') border-rose-500 @enderror">
                    @error('email')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Nova Senha</label>
                    <input type="password" name="password" id="password" placeholder="Deixe em branco para manter a atual"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('password') border-rose-500 @enderror">
                    <p class="mt-1 text-[10px] text-slate-400">Mínimo 8 caracteres</p>
                    @error('password')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 mb-1">Perfil *</label>
                    <select name="role" id="role" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('role') border-rose-500 @enderror">
                        <option value="">Selecione um perfil...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role', $current_role) == $role->name) ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                    <select name="status" id="status" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('status') border-rose-500 @enderror">
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Telefone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500 @error('phone') border-rose-500 @enderror">
                    @error('phone')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Specific fields for teachers -->
            <div id="teacher-fields" style="display: {{ $current_role == 'teacher' ? 'block' : 'none' }};" class="pt-3 border-t border-slate-100 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Informações do Professor
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="qualification" class="block text-xs font-bold text-slate-700 mb-1">Qualificação</label>
                        <input type="text" name="qualification" id="qualification" value="{{ old('qualification', $teacher_data->qualification ?? '') }}" placeholder="Ex: Licenciatura em Matemática"
                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="specialization" class="block text-xs font-bold text-slate-700 mb-1">Especialização</label>
                        <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $teacher_data->specialization ?? '') }}" placeholder="Ex: Matemática e Física"
                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Atualizar Usuário
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('role').addEventListener('change', function() {
    const teacherFields = document.getElementById('teacher-fields');
    if (this.value === 'teacher') {
        teacherFields.style.display = 'block';
    } else {
        teacherFields.style.display = 'none';
    }
});
</script>
@endpush
