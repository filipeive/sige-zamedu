@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')
@section('page-title-icon', 'fas fa-user-cog')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" class="text-slate-400 hover:text-slate-600">Portal do Professor</a></li>
    <li class="breadcrumb-item active text-slate-600 font-medium">Meu Perfil</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Hero Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white text-xl font-bold shadow-md">
                    {{ substr($teacher->first_name, 0, 1) }}{{ substr($teacher->last_name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $teacher->first_name }} {{ $teacher->last_name }}</h1>
                    <p class="text-xs text-emerald-300 mt-0.5 flex items-center gap-2">
                        <i class="fas fa-graduation-cap"></i>
                        <span>{{ $teacher->specialization ?? 'Docente' }} &bull; Código: {{ $teacher->teacher_code ?? 'DOC-' . $teacher->id }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-xs border border-white/10 transition-all">
                    <i class="fas fa-arrow-left"></i>
                    Voltar ao Portal
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Summary & Contact Cards (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Stat Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5">
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-2xl font-bold font-mono text-emerald-700">{{ $teacher->classes()->count() }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Turmas Ativas</div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-2xl font-bold font-mono text-slate-800">{{ $teacher->years_experience ?? 0 }}</div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Anos Experiência</div>
                    </div>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-address-card text-xs"></i>
                        </span>
                        Contactos Registados
                    </h2>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-envelope text-xs"></i>
                        </span>
                        <div class="min-w-0">
                            <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Email Oficial</div>
                            <div class="font-medium text-slate-800 truncate">{{ $teacher->email }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-phone text-xs"></i>
                        </span>
                        <div>
                            <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Telefone</div>
                            <div class="font-medium text-slate-800">{{ $teacher->phone }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-map-marker-alt text-xs"></i>
                        </span>
                        <div>
                            <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Endereço Residencial</div>
                            <div class="font-medium text-slate-800">{{ $teacher->address ?? 'Não informado' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security & Account Info -->
            @if($teacher->user)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-emerald-600"></i>
                        Segurança da Conta
                    </h3>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Estado:</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-check text-[9px]"></i> Ativa
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Último Acesso:</span>
                            <span class="text-slate-700 font-medium">{{ $teacher->user->last_login_at ? $teacher->user->last_login_at->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Para alterar a senha de acesso ao portal ou dados civis, contacte a secretaria administrativa da escola.
                    </p>
                </div>
            @endif

        </div>

        <!-- Right Column: Edit Profile Form (8 cols) -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-edit text-xs"></i>
                        </span>
                        Atualização de Dados
                    </h2>
                    <span class="text-xs text-slate-400 font-medium">* Campos editáveis pelo professor</span>
                </div>

                <form action="{{ route('teacher.update-profile') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <!-- Dados Pessoais (ReadOnly + Editable) -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-1.5 pb-2 border-b border-slate-100">
                            <i class="fas fa-user text-emerald-600"></i>
                            Identificação Pessoal
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Primeiro Nome</label>
                                <input type="text" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 cursor-not-allowed font-medium" value="{{ $teacher->first_name }}" readonly>
                                <p class="text-[10px] text-slate-400 mt-1">Alterações de nome exigem validação da administração.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Último Nome</label>
                                <input type="text" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 cursor-not-allowed font-medium" value="{{ $teacher->last_name }}" readonly>
                                <p class="text-[10px] text-slate-400 mt-1">Alterações de nome exigem validação da administração.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Oficial</label>
                                <input type="email" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 cursor-not-allowed font-medium" value="{{ $teacher->email }}" readonly>
                                <p class="text-[10px] text-slate-400 mt-1">O email institucional está associado ao login.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Telefone <span class="text-rose-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">
                                @error('phone')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Endereço Residencial <span class="text-rose-500">*</span></label>
                                <textarea name="address" rows="2" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800">{{ old('address', $teacher->address) }}</textarea>
                                @error('address')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dados Académicos & Profissionais -->
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-1.5 pb-2 border-b border-slate-100">
                            <i class="fas fa-briefcase text-emerald-600"></i>
                            Informações Profissionais
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Qualificação Académica</label>
                                <input type="text" name="qualification" value="{{ old('qualification', $teacher->qualification) }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" placeholder="Ex: Licenciatura em Ensino de...">
                                @error('qualification')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Área de Especialização</label>
                                <input type="text" name="specialization" value="{{ old('specialization', $teacher->specialization) }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-slate-800" placeholder="Ex: Matemática, Física, Português...">
                                @error('specialization')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Data de Início de Funções</label>
                                <input type="text" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 cursor-not-allowed font-medium" value="{{ $teacher->hire_date ? $teacher->hire_date->format('d/m/Y') : '-' }}" readonly>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Remuneração Base</label>
                                <input type="text" class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 cursor-not-allowed font-mono font-medium" value="{{ number_format($teacher->salary, 2, ',', '.') }} MZN" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('teacher.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Voltar
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Atualizar Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
