@extends('layouts.app')

@section('title', 'Completar Perfil de Professor')
@section('page-title', 'Completar Perfil')
@section('page-title-icon', 'fas fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item text-slate-400">Portal do Professor</li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Completar Perfil</li>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="flex items-center gap-3.5">
            <span class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-emerald-300 text-xl">
                <i class="fas fa-chalkboard-teacher"></i>
            </span>
            <div>
                <h1 class="text-xl font-bold text-white">Bem-vindo(a), Docente!</h1>
                <p class="text-xs text-slate-300 mt-0.5">
                    Complete as informações cadastrais abaixo para ativar e aceder plenamente ao seu painel pedagógico.
                </p>
            </div>
        </div>
    </div>

    <!-- Alert Note -->
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center gap-3">
        <i class="fas fa-info-circle text-emerald-600 text-base shrink-0"></i>
        <span>Estes dados são essenciais para atribuição de turmas, emissão de pautas e registos no diário de turma.</span>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-id-card text-xs"></i>
                </span>
                Ficha Cadastral do Docente
            </h2>
            <span class="text-xs text-slate-400 font-medium">* Campos obrigatórios</span>
        </div>

        <form action="{{ route('teacher.store-profile') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Identificação Pessoal -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-1.5 pb-2 border-b border-slate-100">
                    <i class="fas fa-user text-emerald-600"></i>
                    Identificação Pessoal
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nome Completo</label>
                        <input type="text" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium cursor-not-allowed" value="{{ $user->name }}" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email de Acesso</label>
                        <input type="email" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium cursor-not-allowed" value="{{ $user->email }}" readonly>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contacto Telefónico <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('phone') ? 'border-rose-400' : 'border-slate-200' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800"
                            value="{{ old('phone') }}" required placeholder="Ex: +258 84 123 4567">
                        @error('phone')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Data de Nascimento</label>
                        <input type="date" name="birth_date" class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('birth_date') ? 'border-rose-400' : 'border-slate-200' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800"
                            value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Género</label>
                        <select name="gender" class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('gender') ? 'border-rose-400' : 'border-slate-200' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                            <option value="">Selecione...</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Feminino</option>
                        </select>
                        @error('gender')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Número de BI / Documento</label>
                        <input type="text" name="bi_number" class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('bi_number') ? 'border-rose-400' : 'border-slate-200' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800"
                            value="{{ old('bi_number') }}" placeholder="Ex: 110100234567A">
                        @error('bi_number')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Endereço Residencial <span class="text-rose-500">*</span></label>
                        <textarea name="address" rows="2" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('address') ? 'border-rose-400' : 'border-slate-200' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" placeholder="Cidade, Bairro, Rua / Avenida, Nº...">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informações Profissionais -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-1.5 pb-2 border-b border-slate-100">
                    <i class="fas fa-briefcase text-emerald-600"></i>
                    Informações Académicas e Profissionais
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Qualificação Académica</label>
                        <input type="text" name="qualification" class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('qualification') ? 'border-rose-400' : 'border-slate-200' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800"
                            value="{{ old('qualification') }}" placeholder="Ex: Licenciatura em Ensino de...">
                        @error('qualification')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Área de Especialização</label>
                        <input type="text" name="specialization" class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('specialization') ? 'border-rose-400' : 'border-slate-200' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800"
                            value="{{ old('specialization') }}" placeholder="Ex: Matemática, Física, Português...">
                        @error('specialization')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Criar Perfil e Aceder ao Portal
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
