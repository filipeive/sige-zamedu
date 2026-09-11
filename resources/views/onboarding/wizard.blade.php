@extends('layouts.app')

@section('title', 'Onboarding Multi-Escola — Registo de Nova Instituição')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Banner Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-2xl p-6 shadow-md text-white mb-6 flex items-center gap-4">
        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md text-white">
            <i class="fa-solid fa-school-flag text-3xl"></i>
        </div>
        <div>
            <h2 class="text-xl md:text-2xl font-bold tracking-tight">Wizard de Onboarding Multi-Escola</h2>
            <p class="text-xs md:text-sm text-emerald-100 mt-0.5">Integração de Novas Instituições de Ensino no Ecossistema SIGE / ZAMEDU</p>
        </div>
    </div>

    <!-- Error Feedback -->
    @if ($errors->any())
        <div class="p-4 mb-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
            <h6 class="text-xs font-bold uppercase tracking-wider mb-1 text-rose-900 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Por favor, corrija os erros abaixo:
            </h6>
            <ul class="text-xs list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Onboarding Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('onboarding.school.store') }}" class="space-y-6">
            @csrf

            <!-- STEP 1: Enquadramento Territorial -->
            <div class="pb-6 border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                    Enquadramento Territorial MINEDH
                </h3>
                <p class="text-xs text-slate-500 mb-4 ml-8">Selecione a jurisdição geográfica oficial da escola na hierarquia territorial.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Província *</label>
                        <select name="province_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" required>
                            <option value="">Selecione a Província...</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p->id }}" {{ old('province_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Distrito *</label>
                        <select name="district_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" required>
                            <option value="">Selecione o Distrito...</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">ZIP (Zona de Influência) *</label>
                        <select name="zip_id" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" required>
                            <option value="">Selecione a ZIP...</option>
                            @foreach($zips as $z)
                                <option value="{{ $z->id }}" {{ old('zip_id') == $z->id ? 'selected' : '' }}>{{ $z->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Identificação da Escola -->
            <div class="pb-6 border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                    Dados de Identificação da Escola
                </h3>
                <p class="text-xs text-slate-500 mb-4 ml-8">Informações cadastrais principais da instituição de ensino.</p>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-6">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nome da Escola *</label>
                        <input type="text" name="school_name" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" placeholder="Ex: Escola Secundária de Malei" value="{{ old('school_name') }}" required>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Código Oficial (Opcional)</label>
                        <input type="text" name="school_code" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" placeholder="Ex: ESG-MAL-01" value="{{ old('school_code') }}">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Tipo de Instituição *</label>
                        <select name="type" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" required>
                            <option value="public" {{ old('type') == 'public' ? 'selected' : '' }}>Escola Pública</option>
                            <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>Escola Privada</option>
                            <option value="community" {{ old('type') == 'community' ? 'selected' : '' }}>Escola Comunitária</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">E-mail Institucional *</label>
                        <input type="email" name="email" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" placeholder="escola@minedh.gov.mz" value="{{ old('email') }}" required>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Contacto Telefónico *</label>
                        <input type="text" name="phone" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" placeholder="+258 84 000 0000" value="{{ old('phone') }}" required>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Endereço Físico</label>
                        <input type="text" name="address" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" placeholder="Bairro Central, Namacurra" value="{{ old('address') }}">
                    </div>
                </div>
            </div>

            <!-- STEP 3: Administrador da Escola -->
            <div class="pb-6 border-b border-slate-200">
                <h3 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">3</span>
                    Conta do Diretor / Administrador Inicial
                </h3>
                <p class="text-xs text-slate-500 mb-4 ml-8">Credenciais de acesso para o primeiro utilizador com perfil gestor de escola.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nome Completo do Diretor *</label>
                        <input type="text" name="admin_name" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" placeholder="Dr. Armindo Sitoe" value="{{ old('admin_name') }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">E-mail do Administrador *</label>
                        <input type="email" name="admin_email" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" placeholder="diretor@escola.co.mz" value="{{ old('admin_email') }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Palavra-passe *</label>
                        <input type="password" name="password" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Confirmar Palavra-passe *</label>
                        <input type="password" name="password_confirmation" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" required>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Configuração Inicial -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">4</span>
                    Configurações Iniciais do Sistema
                </h3>
                <p class="text-xs text-slate-500 mb-4 ml-8">Parâmetros pedagógicos de arranque.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Ano Lectivo Inicial</label>
                        <input type="text" name="academic_year" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" value="2026" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Limiar de Aprovação (Norma MINEDH)</label>
                        <input type="number" step="0.1" name="passing_grade" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 p-2.5 focus:ring-2 focus:ring-emerald-500" value="9.5" required>
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('territorial.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Concluir Onboarding da Escola
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
