@extends('layouts.app')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações do Sistema')
@section('page-title-icon', 'fas fa-cog')

@section('breadcrumbs')
    <li class="breadcrumb-item active text-slate-600 font-medium">Configurações</li>
@endsection

@php
    $activeTab = request('tab', 'escola');
@endphp

@section('content')
<div class="space-y-4" x-data="{ currentTab: '{{ $activeTab }}' }">

    <!-- 1. Header Row (Compact & Clean) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-200 dark:border-slate-700">
        <div>
            <h1 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight flex items-center gap-2">
                <i class="fas fa-cog text-emerald-600"></i> Configurações & Parâmetros do Sistema
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gestão integral da marca, estrutura académica, permissões RBAC, backups e comunicação.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" x-show="currentTab !== 'acesso'" onclick="document.getElementById('settings-main-form').submit();" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition cursor-pointer">
                <i class="fas fa-check"></i> Guardar Alterações
            </button>
            <button type="button" x-show="currentTab === 'acesso'" onclick="document.getElementById('rbac-permissions-form').submit();" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-purple-700 hover:bg-purple-800 text-xs font-bold text-white shadow-sm transition cursor-pointer">
                <i class="fas fa-shield-alt"></i> Guardar Matriz RBAC
            </button>
        </div>
    </div>

    <!-- 2. Stat Summary Cards (Pílula Vertical de Cor na Esquerda + Ícone Circular) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Tipo de Instituição (Emerald Pill Accent) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas {{ is_public_school() ? 'fa-landmark' : 'fa-building-columns' }}"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Tipo de Instituição</span>
                <span class="text-xs font-black text-slate-800 dark:text-slate-100 mt-0.5 block">
                    {{ is_public_school() ? 'Escola Pública' : 'Escola Privada' }}
                </span>
            </div>
        </div>

        <!-- Ano Lectivo Activo (Blue Pill Accent) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Ano Lectivo Activo</span>
                <span class="text-xs font-black text-slate-800 dark:text-slate-100 mt-0.5 block">
                    {{ setting('academic_year', date('Y')) }} · {{ setting('academic_regime', 'Trimestral') }}
                </span>
            </div>
        </div>

        <!-- Logótipo da Marca (Amber Pill Accent) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-image"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Logótipo da Marca</span>
                <span class="text-xs font-black text-slate-800 dark:text-slate-100 mt-0.5 block">
                    {{ setting('school_logo') ? 'Personalizado' : 'Logótipo Padrão' }}
                </span>
            </div>
        </div>

        <!-- Perfis RBAC (Purple Pill Accent) -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700 shadow-2xs flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-1.5 h-8 rounded-full bg-purple-500 shrink-0"></div>
            <div class="w-9 h-9 rounded-full bg-purple-50 dark:bg-purple-950 text-purple-600 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Controle RBAC</span>
                <span class="text-xs font-black text-slate-800 dark:text-slate-100 mt-0.5 block">
                    {{ count($roles) }} Perfis Ativos
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Pure Tailwind Tab Navigation Bar -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-1.5 shadow-2xs overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max">
            <button type="button" @click="currentTab = 'escola'" 
                    :class="currentTab === 'escola' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-school text-xs"></i> 1. Escola & Marca
            </button>

            <button type="button" @click="currentTab = 'ano-letivo'" 
                    :class="currentTab === 'ano-letivo' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-calendar-alt text-xs"></i> 2. Ano Letivo
            </button>

            <button type="button" @click="currentTab = 'estrutura'" 
                    :class="currentTab === 'estrutura' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-graduation-cap text-xs"></i> 3. Estrutura Académica
            </button>

            <button type="button" @click="currentTab = 'utilizadores'" 
                    :class="currentTab === 'utilizadores' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-users-cog text-xs"></i> 4. Utilizadores & Auditoria
            </button>

            <button type="button" @click="currentTab = 'comunicacao'" 
                    :class="currentTab === 'comunicacao' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-paper-plane text-xs"></i> 5. Comunicação
            </button>

            <button type="button" @click="currentTab = 'system'" 
                    :class="currentTab === 'system' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-server text-xs"></i> 6. Sistema & Backups
            </button>

            <button type="button" @click="currentTab = 'avaliacao'" 
                    :class="currentTab === 'avaliacao' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-calculator text-xs"></i> 7. Avaliação & Notas
            </button>

            <button type="button" @click="currentTab = 'tema'" 
                    :class="currentTab === 'tema' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-palette text-xs"></i> 8. Cores & Ícones
            </button>

            <button type="button" @click="currentTab = 'acesso'" 
                    :class="currentTab === 'acesso' ? 'bg-purple-700 text-white shadow-xs' : 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    class="px-3.5 py-2 rounded-lg text-xs font-extrabold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fas fa-user-shield text-xs"></i> 9. Controle RBAC
            </button>
        </div>
    </div>

    <!-- 4. Form Container Card (Tabs 1-7) -->
    <div x-show="currentTab !== 'acesso'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs p-5">
        <form id="settings-main-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- TAB 1: ESCOLA & MARCA -->
            <div x-show="currentTab === 'escola'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-school text-emerald-600"></i> Identidade & Marca da Instituição
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Informações oficiais da escola, logótipo, endereço e contactos.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nome Completo da Escola *</label>
                        <input type="text" name="settings[school_name]" value="{{ setting('school_name', 'Escola Primária e Secundária dos Visionários') }}" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nome Curto / Sigla</label>
                        <input type="text" name="settings[school_short_name]" value="{{ setting('school_short_name', 'ZamEdu') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tipo de Instituição de Ensino *</label>
                        <input type="hidden" name="settings[institution_type]" id="institution_type_input" value="{{ setting('institution_type', 'escola_privada') }}">
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ selectedType: '{{ setting('institution_type', 'escola_privada') }}' }">
                            <!-- Card Escola Privada -->
                            <div @click="selectedType = 'escola_privada'; document.getElementById('institution_type_input').value = 'escola_privada';"
                                 :class="selectedType === 'escola_privada' ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/40 ring-2 ring-blue-500/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-slate-300'"
                                 class="p-4 rounded-xl border transition cursor-pointer flex items-start gap-3.5 relative shadow-2xs">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg font-bold shrink-0">
                                    <i class="fas fa-building-columns"></i>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-slate-800 dark:text-slate-100">Escola Privada</span>
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-extrabold">Módulo Propinas Ativo</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">Gestão financeira completa com cobrança de mensalidades, faturas, recibos e relatórios de propinas.</p>
                                </div>
                                <div x-show="selectedType === 'escola_privada'" class="absolute top-3 right-3 text-blue-600 text-sm">
                                    <i class="fas fa-circle-check"></i>
                                </div>
                            </div>

                            <!-- Card Escola Pública -->
                            <div @click="selectedType = 'escola_publica'; document.getElementById('institution_type_input').value = 'escola_publica';"
                                 :class="selectedType === 'escola_publica' ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/40 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 hover:border-slate-300'"
                                 class="p-4 rounded-xl border transition cursor-pointer flex items-start gap-3.5 relative shadow-2xs">
                                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg font-bold shrink-0">
                                    <i class="fas fa-landmark"></i>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-slate-800 dark:text-slate-100">Escola Pública</span>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-extrabold">Isenta de Propinas</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">Módulo de mensalidades desativado. Foco em taxas administrativas, despesas operacionais e boletins estatais.</p>
                                </div>
                                <div x-show="selectedType === 'escola_publica'" class="absolute top-3 right-3 text-emerald-600 text-sm">
                                    <i class="fas fa-circle-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Logótipo da Escola com Botão de Remoção -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Logótipo da Escola & Marca</label>
                        <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-50 dark:bg-slate-900/60 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div class="w-20 h-20 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2 shadow-2xs shrink-0 overflow-hidden">
                                @if(setting('school_logo'))
                                    <img src="{{ setting('school_logo') }}" alt="Logo" class="max-h-full max-w-full object-contain">
                                @else
                                    <i class="fas fa-graduation-cap text-3xl text-emerald-600"></i>
                                @endif
                            </div>
                            <div class="flex-grow space-y-2">
                                <input type="file" name="school_logo_file" class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-700 file:text-white hover:file:bg-emerald-800" accept="image/*">
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Formatos aceites: PNG, JPG, SVG ou WebP (máx: 2MB). Utilizado no cabeçalho, boletins e recibos.</p>
                                @if(setting('school_logo'))
                                    <div class="pt-1">
                                        <button type="button" onclick="if(confirm('Tem certeza que deseja remover o logótipo da escola?')) document.getElementById('remove-logo-form').submit();"
                                                class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 text-xs font-bold text-rose-700 transition cursor-pointer">
                                            <i class="fas fa-trash-alt text-xs"></i> Remover Logótipo da Escola
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Endereço Principal</label>
                        <input type="text" name="settings[school_address]" value="{{ setting('school_address', 'Namacurra, Zambézia, Moçambique') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Província</label>
                        <input type="text" name="settings[school_province]" value="{{ setting('school_province', setting('province', 'Zambézia')) }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ex: Zambézia">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Distrito / Cidade</label>
                        <input type="text" name="settings[school_district]" value="{{ setting('school_district', setting('district', 'Namacurra')) }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ex: Namacurra">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">E-mail Institucional</label>
                        <input type="email" name="settings[school_email]" value="{{ setting('school_email', 'contacto@escola.co.mz') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Telefone Principal</label>
                        <input type="text" name="settings[school_phone]" value="{{ setting('school_phone', '+258 84 123 4567') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">WhatsApp / Contacto Secundário</label>
                        <input type="text" name="settings[school_phone_secondary]" value="{{ setting('school_phone_secondary', '+258 82 987 6543') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Diretor Geral</label>
                        <input type="text" name="settings[school_director]" value="{{ setting('school_director', 'Prof. Dr. António Silva') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">NIF / NUIT (Número Fiscal)</label>
                        <input type="text" name="settings[school_nif]" value="{{ setting('school_nif', '400123456') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- TAB 2: ANO LECTIVO -->
            <div x-show="currentTab === 'ano-letivo'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-emerald-600"></i> Gestão do Ano Lectivo & Trimestres
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Parâmetros de encerramento e transição de ano académico.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ano Lectivo Activo *</label>
                        <input type="number" name="settings[academic_year]" value="{{ setting('academic_year', date('Y')) }}" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Regime Lectivo *</label>
                        <select name="settings[academic_regime]"
                                class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="trimestral" {{ setting('academic_regime', 'trimestral') === 'trimestral' ? 'selected' : '' }}>Trimestral (3 Trimestres)</option>
                            <option value="semestral" {{ setting('academic_regime') === 'semestral' ? 'selected' : '' }}>Semestral (2 Semestres)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Data de Abertura do Ano Lectivo</label>
                        <input type="date" name="settings[academic_year_start]" value="{{ setting('academic_year_start', date('Y-02-01')) }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Data de Encerramento do Ano Lectivo</label>
                        <input type="date" name="settings[academic_year_end]" value="{{ setting('academic_year_end', date('Y-11-30')) }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- TAB 3: ESTRUTURA ACADÉMICA -->
            <div x-show="currentTab === 'estrutura'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-graduation-cap text-emerald-600"></i> Estrutura Académica & Alunos
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Definições globais de limites de alunos e matrículas.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Capacidade Máxima por Turma *</label>
                        <input type="number" name="settings[default_class_capacity]" value="{{ setting('default_class_capacity', 40) }}" min="1" max="100" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Média Mínima para Aprovação *</label>
                        <input type="number" name="settings[passing_grade]" value="{{ setting('passing_grade', 10) }}" min="0" max="20" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Prefixo do Número de Estudante</label>
                        <input type="text" name="settings[student_number_prefix]" value="{{ setting('student_number_prefix', 'ZAM') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Limite de Faltas Não Justificadas</label>
                        <input type="number" name="settings[max_foul_limit]" value="{{ setting('max_foul_limit', 15) }}" min="1" max="100"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- TAB 4: UTILIZADORES & AUDITORIA -->
            <div x-show="currentTab === 'utilizadores'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-users-cog text-emerald-600"></i> Auditoria de Acessos & Logs do Sistema
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Registo de atividades de utilizadores e acessos recentes.</p>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 border border-slate-200 dark:border-slate-700 max-h-96 overflow-y-auto font-mono text-[11px]">
                    @forelse($logs as $log)
                        <div class="py-1 border-b border-slate-200/50 dark:border-slate-800 text-slate-700 dark:text-slate-300">
                            {{ $log }}
                        </div>
                    @empty
                        <p class="text-slate-400">Nenhum registo de log disponível.</p>
                    @endforelse
                </div>
            </div>

            <!-- TAB 5: COMUNICAÇÃO -->
            <div x-show="currentTab === 'comunicacao'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-paper-plane text-emerald-600"></i> Canais de Comunicação & Notificações
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Definições de SMS, gateway WhatsApp e email.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Gateway SMS Activa</label>
                        <select name="settings[sms_gateway]"
                                class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="disabled" {{ setting('sms_gateway', 'disabled') === 'disabled' ? 'selected' : '' }}>Desativada (Modo Simulação)</option>
                            <option value="twilio" {{ setting('sms_gateway') === 'twilio' ? 'selected' : '' }}>Twilio SMS Gateway</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email de Remetente (SMTP)</label>
                        <input type="email" name="settings[smtp_from_address]" value="{{ setting('smtp_from_address', 'nao-responder@escola.co.mz') }}"
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- TAB 6: SISTEMA & BACKUPS -->
            <div x-show="currentTab === 'system'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-server text-emerald-600"></i> Manutenção & Gestão de Backups
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Cópias de segurança da base de dados e preferências de moeda.</p>
                    </div>
                    <a href="{{ route('admin.backup.create') }}" onclick="event.preventDefault(); document.getElementById('create-backup-form').submit();"
                       class="no-underline inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white shadow-2xs transition">
                        <i class="fas fa-download"></i> Criar Backup Agora
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Moeda Oficial *</label>
                        <input type="text" name="settings[currency]" value="{{ setting('currency', 'MT') }}" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Fuso Horário *</label>
                        <input type="text" name="settings[timezone]" value="{{ setting('timezone', 'Africa/Maputo') }}" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- Tabela de Backups -->
                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-800 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-2.5 px-4">Ficheiro</th>
                                <th class="py-2.5 px-3">Tamanho</th>
                                <th class="py-2.5 px-3">Data de Criação</th>
                                <th class="py-2.5 px-3">Acções</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($backups as $b)
                                <tr>
                                    <td class="py-2.5 px-4 font-mono text-slate-800 dark:text-slate-200 font-bold">{{ $b['filename'] }}</td>
                                    <td class="py-2.5 px-3 text-slate-600 dark:text-slate-400">{{ $b['size'] }}</td>
                                    <td class="py-2.5 px-3 text-slate-600 dark:text-slate-400">{{ $b['date'] }}</td>
                                    <td>
                                        <a href="{{ route('admin.backup.download', $b['filename']) }}"
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-600">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('admin.backup.delete', $b['filename']) }}"
                                           class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-600">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-slate-400">Nenhum backup encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 7: AVALIAÇÃO & NOTAS -->
            <div x-show="currentTab === 'avaliacao'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-calculator text-emerald-600"></i> Ponderações & Fórmula Académica
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Definição dos pesos das Avaliações Contínuas (MACS) e Exames (ACP).</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Peso das MACS (%) *</label>
                        <input type="number" name="settings[macs_weight]" value="{{ setting('macs_weight', 40) }}" min="0" max="100" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Peso da ACP (%) *</label>
                        <input type="number" name="settings[acp_weight]" value="{{ setting('acp_weight', 60) }}" min="0" max="100" required
                               class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- TAB 8: TEMA, CORES & ÍCONES -->
            <div x-show="currentTab === 'tema'" class="space-y-6">
                <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-palette text-emerald-600"></i> Personalização de Cores & Ícones do Sistema
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Defina a cor primária da marca, o estilo de ícones e o tema visual do dashboard.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Seletor de Cores da Marca -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Cor Primária da Marca (Primary Accent) *</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="primary_color_picker" value="{{ setting('primary_color', '#047857') }}"
                                   onchange="document.getElementById('primary_color_input').value = this.value"
                                   class="h-9 w-12 rounded-lg border border-slate-200 cursor-pointer p-0.5 bg-white">
                            <input type="text" id="primary_color_input" name="settings[primary_color]" value="{{ setting('primary_color', '#047857') }}" required
                                   onchange="document.getElementById('primary_color_picker').value = this.value"
                                   class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold uppercase font-mono">
                        </div>
                        
                        <!-- Paletas Pré-definidas -->
                        <div class="pt-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Paletas Rápidas</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="document.getElementById('primary_color_input').value='#047857'; document.getElementById('primary_color_picker').value='#047857';"
                                        class="h-7 px-2.5 rounded-md text-[10px] font-bold text-white bg-[#047857] shadow-2xs flex items-center gap-1">
                                    <i class="fas fa-circle text-[8px]"></i> Esmeralda
                                </button>
                                <button type="button" onclick="document.getElementById('primary_color_input').value='#1d4ed8'; document.getElementById('primary_color_picker').value='#1d4ed8';"
                                        class="h-7 px-2.5 rounded-md text-[10px] font-bold text-white bg-[#1d4ed8] shadow-2xs flex items-center gap-1">
                                    <i class="fas fa-circle text-[8px]"></i> Azul Royal
                                </button>
                                <button type="button" onclick="document.getElementById('primary_color_input').value='#4f46e5'; document.getElementById('primary_color_picker').value='#4f46e5';"
                                        class="h-7 px-2.5 rounded-md text-[10px] font-bold text-white bg-[#4f46e5] shadow-2xs flex items-center gap-1">
                                    <i class="fas fa-circle text-[8px]"></i> Índigo
                                </button>
                                <button type="button" onclick="document.getElementById('primary_color_input').value='#7e22ce'; document.getElementById('primary_color_picker').value='#7e22ce';"
                                        class="h-7 px-2.5 rounded-md text-[10px] font-bold text-white bg-[#7e22ce] shadow-2xs flex items-center gap-1">
                                    <i class="fas fa-circle text-[8px]"></i> Roxo
                                </button>
                                <button type="button" onclick="document.getElementById('primary_color_input').value='#e11d48'; document.getElementById('primary_color_picker').value='#e11d48';"
                                        class="h-7 px-2.5 rounded-md text-[10px] font-bold text-white bg-[#e11d48] shadow-2xs flex items-center gap-1">
                                    <i class="fas fa-circle text-[8px]"></i> Rosa
                                </button>
                                <button type="button" onclick="document.getElementById('primary_color_input').value='#d97706'; document.getElementById('primary_color_picker').value='#d97706';"
                                        class="h-7 px-2.5 rounded-md text-[10px] font-bold text-white bg-[#d97706] shadow-2xs flex items-center gap-1">
                                    <i class="fas fa-circle text-[8px]"></i> Âmbar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Estilo de Ícones do Sistema -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Estilo de Ícones do Sistema *</label>
                        <select name="settings[icon_style]"
                                class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="fontawesome_pro" {{ setting('icon_style', 'fontawesome_pro') === 'fontawesome_pro' ? 'selected' : '' }}>FontAwesome 6 Modern Solid (Recomendado)</option>
                            <option value="duotone" {{ setting('icon_style') === 'duotone' ? 'selected' : '' }}>Soft Duotone (Duas Cores)</option>
                            <option value="regular" {{ setting('icon_style') === 'regular' ? 'selected' : '' }}>Minimalista Outline (Linhas Finas)</option>
                        </select>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">O estilo de ícones é aplicado a todos os botões, cartões estatísticos e menus de navegação.</p>
                    </div>

                    <!-- Tema da Barra Lateral -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tema da Barra Lateral (Sidebar)</label>
                        <select name="settings[sidebar_theme]"
                                class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="light" {{ setting('sidebar_theme', 'light') === 'light' ? 'selected' : '' }}>Branco Puro / Claro (Padrão Alunos)</option>
                            <option value="dark_slate" {{ setting('sidebar_theme') === 'dark_slate' ? 'selected' : '' }}>Slate Escuro Executivo</option>
                            <option value="deep_emerald" {{ setting('sidebar_theme') === 'deep_emerald' ? 'selected' : '' }}>Esmeralda Profundo Institucional</option>
                            <option value="midnight_indigo" {{ setting('sidebar_theme') === 'midnight_indigo' ? 'selected' : '' }}>Índigo Meia-Noite</option>
                        </select>
                    </div>

                    <!-- Arredondamento dos Cantos -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Arredondamento de Cartões & Botões (Border Radius)</label>
                        <select name="settings[border_radius]"
                                class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2 px-3 text-xs font-semibold focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="12px" {{ setting('border_radius', '12px') === '12px' ? 'selected' : '' }}>12px — Arredondado Moderno (Recomendado)</option>
                            <option value="8px" {{ setting('border_radius') === '8px' ? 'selected' : '' }}>8px — Compacto / Suave</option>
                            <option value="16px" {{ setting('border_radius') === '16px' ? 'selected' : '' }}>16px — Arredondado Amplo</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação de Rodapé -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white shadow-sm transition cursor-pointer">
                    <i class="fas fa-check"></i> Guardar Alterações
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 8: CONTROLE DE ACESSO (RBAC COMPLETO - FORMULÁRIO DEDICADO) -->
    <div x-show="currentTab === 'acesso'" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-2xs p-5">
        <form id="rbac-permissions-form" action="{{ route('admin.settings.permissions.update') }}" method="POST">
            @csrf
            <div class="pb-3 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-shield text-purple-600"></i> Matriz de Controlo de Acesso & Permissões (RBAC)
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Defina exatamente o que cada perfil (Secretaria, Pedagogia, Professor, etc.) pode visualizar e gerir.</p>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-purple-700 hover:bg-purple-800 text-xs font-bold text-white shadow-sm transition cursor-pointer">
                    <i class="fas fa-shield-alt"></i> Guardar Matriz RBAC
                </button>
            </div>

            <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200 dark:border-slate-700">
                            <th class="py-3 px-4 w-1/3">Módulo / Permissão</th>
                            @foreach($roles as $role)
                                <th class="py-3 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300 text-[10px] font-bold">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($permissions as $groupName => $groupPermissions)
                            <!-- Group Category Row -->
                            <tr class="bg-slate-50/80 dark:bg-slate-900/60 font-bold text-slate-800 dark:text-slate-200">
                                <td colspan="{{ count($roles) + 1 }}" class="py-2 px-4 text-xs font-black tracking-wider text-emerald-700 dark:text-emerald-400 uppercase bg-slate-100/50 dark:bg-slate-800/50">
                                    <i class="fas fa-layer-group me-1.5"></i> {{ $groupName }}
                                </td>
                            </tr>
                            @foreach($groupPermissions as $permission)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <td class="py-2 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                        {{ $permission->name }}
                                    </td>
                                    @foreach($roles as $role)
                                        <td class="py-2 px-3 text-center">
                                            <input type="checkbox" 
                                                   name="role_permissions[{{ $role->id }}][{{ $permission->name }}]" 
                                                   value="1" 
                                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                   class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-purple-600 focus:ring-purple-500 cursor-pointer">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-slate-100 dark:border-slate-700 mt-4">
                <span class="text-xs text-slate-500 dark:text-slate-400">As permissões têm efeito imediato após guardar a matriz.</span>
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-purple-700 hover:bg-purple-800 text-xs font-bold text-white shadow-sm transition cursor-pointer">
                    <i class="fas fa-shield-alt"></i> Guardar Matriz RBAC
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Formulário Oculto para Remoção do Logótipo -->
<form id="remove-logo-form" action="{{ route('admin.settings.remove-logo') }}" method="POST" class="hidden">
    @csrf
</form>

<!-- Formulário Oculto para Criação de Backup -->
<form id="create-backup-form" action="{{ route('admin.backup.create') }}" method="POST" class="hidden">
    @csrf
</form>
@endsection