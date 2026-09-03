@extends('layouts.app')

@section('title', 'Novo Usuário')
@section('page-title', 'Criar Novo Usuário')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Novo Usuário</li>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fas fa-user-plus text-emerald-600"></i> Cadastrar Novo Usuário
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Criar conta de usuário com função de acesso específica.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition">
            <i class="fas fa-arrow-left"></i> Voltar à Lista
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600 shadow-2xs p-5 max-w-4xl">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nome Completo *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-rose-500 @enderror">
                    @error('name')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('email') border-rose-500 @enderror">
                    @error('email')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Senha *</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('password') border-rose-500 @enderror">
                    @error('password')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirmar Senha *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 mb-1">Perfil *</label>
                    <select name="role" id="role" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('role') border-rose-500 @enderror">
                        <option value="">Selecione um perfil...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                    <select name="status" id="status" required
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('status') border-rose-500 @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Telefone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500 @error('phone') border-rose-500 @enderror">
                    @error('phone')<p class="mt-1 text-[10px] text-rose-600 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Specific fields by role -->
            <div id="teacher-fields" style="display: none;" class="pt-3 border-t border-slate-100 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-emerald-600"></i> Informações do Professor
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="qualification" class="block text-xs font-bold text-slate-700 mb-1">Qualificação</label>
                        <input type="text" name="qualification" id="qualification" value="{{ old('qualification') }}" placeholder="Ex: Licenciatura em Matemática"
                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label for="specialization" class="block text-xs font-bold text-slate-700 mb-1">Especialização</label>
                        <input type="text" name="specialization" id="specialization" value="{{ old('specialization') }}" placeholder="Ex: Matemática e Física"
                               class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <div id="parent-fields" style="display: none;" class="pt-3 border-t border-slate-100 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-user-friends text-emerald-600"></i> Informações do Encarregado
                </h4>
                <div class="w-full sm:w-1/2">
                    <label for="relationship" class="block text-xs font-bold text-slate-700 mb-1">Grau de Parentesco</label>
                    <select name="relationship" id="relationship"
                            class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="Father">Pai</option>
                        <option value="Mother">Mãe</option>
                        <option value="Uncle">Tio</option>
                        <option value="Aunt">Tia</option>
                        <option value="Grandfather">Avô</option>
                        <option value="Grandmother">Avó</option>
                        <option value="Brother">Irmão</option>
                        <option value="Sister">Irmã</option>
                        <option value="Other" selected>Outro</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="no-underline inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition">
                    <i class="fas fa-save"></i> Criar Usuário
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('role').addEventListener('change', function () {
    const teacherFields = document.getElementById('teacher-fields');
    const parentFields = document.getElementById('parent-fields');

    teacherFields.style.display = 'none';
    parentFields.style.display = 'none';

    if (this.value === 'teacher') {
        teacherFields.style.display = 'block';
    } else if (this.value === 'parent') {
        parentFields.style.display = 'block';
    }
});

document.getElementById('role').dispatchEvent(new Event('change'));
</script>
@endpush
