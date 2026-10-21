@extends('layouts.portal')

@section('title', 'Pré-Inscrição Online — Ano Lectivo ' . ($academicYear ?? '2027') . ' — MINEDH | ZAMEDU')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">

    <!-- Top Breadcrumb & Badge -->
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('welcome') }}" class="hover:text-emerald-700 transition">Início</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <a href="{{ route('public.vacancies') }}" class="hover:text-emerald-700 transition">Vagas Escolares</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            <span class="text-emerald-800 font-bold">Pré-Inscrição Online</span>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-bold border border-emerald-200 shadow-xs">
            <i class="fa-solid fa-calendar-check text-emerald-700"></i> Ano Lectivo {{ $academicYear ?? '2027' }}
        </div>
    </div>

    <!-- Banner Institucional MINEDH -->
    <div class="rounded-3xl shadow-md overflow-hidden mb-8 border border-emerald-800/20 text-white" style="background: linear-gradient(135deg, #005A2B 0%, #007A3D 55%, #046A38 100%); border-bottom: 4px solid #FCD116;">
        <div class="p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-5">
                <div class="p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/20 shrink-0">
                    <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" class="h-16 w-auto drop-shadow-md">
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-amber-400 text-slate-900 text-[11px] font-extrabold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-school text-slate-900"></i> ESCOLA DOS VISIONÁRIOS &bull; ADMISSÃO 2027
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                        Reservar Vaga &amp; Pré-Inscrição 2027
                    </h1>
                    <p class="text-emerald-100 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                        Formulário oficial de candidatura e pré-matrícula para o ano lectivo de <strong>{{ $academicYear ?? '2027' }}</strong>. Escolha a sua escola territorial e preencha os dados do educando.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                <a href="{{ route('public.vacancies') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/30 backdrop-blur-xs">
                    <i class="fa-solid fa-chair text-amber-300"></i> Consultar Vagas
                </a>
                <a href="{{ route('public.allocations') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/30 backdrop-blur-xs">
                    <i class="fa-solid fa-search-location text-amber-300"></i> Afetações 6ª &rarr; 7ª
                </a>
            </div>
        </div>
    </div>

    <!-- Banner de Reconhecimento de Afetação Oficial SNE -->
    @if(isset($allocation) && $allocation)
        <div class="mb-8 p-5 sm:p-6 rounded-3xl bg-emerald-50 border-2 border-emerald-500 shadow-sm relative overflow-hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-700 text-white flex items-center justify-center text-xl shrink-0 shadow-xs">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-900 text-[10px] font-black uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-check-circle text-emerald-800"></i> Afetação Oficial SNE Reconhecida
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-emerald-950">
                            {{ $allocation->student_name }}
                        </h3>
                        <p class="text-xs text-emerald-800">
                            Código: <span class="font-mono font-bold">{{ $allocation->student_code }}</span> &bull; 
                            Origem: <strong>{{ $allocation->origin_school_name }}</strong> &rarr; Destino: <strong>{{ $allocation->destinationSchool->name ?? 'Escola Selecionada' }}</strong>
                        </p>
                    </div>
                </div>
                <div class="shrink-0 bg-white/80 backdrop-blur-xs px-3.5 py-2 rounded-2xl border border-emerald-200 text-right">
                    <span class="text-[10px] text-slate-500 font-bold block uppercase">Classe de Ingresso</span>
                    <span class="text-sm font-black text-emerald-800">{{ $allocation->grade_level }}ª Classe</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Feedback Alerts -->
    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 text-sm shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg mt-0.5"></i>
            <div>
                <p class="font-bold">Aviso de Erro</p>
                <p class="mt-0.5 text-xs sm:text-sm text-rose-700">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
            <div class="flex items-center gap-2 font-bold mb-2">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-base"></i>
                <span>Por favor, corrija os erros assinalados no formulário:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 text-rose-700 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Wizard Steps Header -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <!-- Step 1 Indicator -->
            <button type="button" onclick="goToStep(0)" id="step-indicator-0" class="step-indicator group text-left p-3 rounded-xl border transition-all duration-200 flex items-center gap-3 bg-emerald-50 border-emerald-600 text-emerald-950 shadow-xs">
                <div id="step-badge-0" class="w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-xs">
                    1
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Passo 1</p>
                    <p class="text-xs sm:text-sm font-bold truncate text-slate-900">Escola & Aluno</p>
                </div>
            </button>

            <!-- Step 2 Indicator -->
            <button type="button" onclick="goToStep(1)" id="step-indicator-1" class="step-indicator group text-left p-3 rounded-xl border transition-all duration-200 flex items-center gap-3 bg-slate-50 border-slate-200 text-slate-600">
                <div id="step-badge-1" class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-black text-sm shrink-0">
                    2
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Passo 2</p>
                    <p class="text-xs sm:text-sm font-bold truncate text-slate-700">Encarregado</p>
                </div>
            </button>

            <!-- Step 3 Indicator -->
            <button type="button" onclick="goToStep(2)" id="step-indicator-2" class="step-indicator group text-left p-3 rounded-xl border transition-all duration-200 flex items-center gap-3 bg-slate-50 border-slate-200 text-slate-600">
                <div id="step-badge-2" class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-black text-sm shrink-0">
                    3
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Passo 3</p>
                    <p class="text-xs sm:text-sm font-bold truncate text-slate-700">Documentos</p>
                </div>
            </button>

            <!-- Step 4 Indicator -->
            <button type="button" onclick="goToStep(3)" id="step-indicator-3" class="step-indicator group text-left p-3 rounded-xl border transition-all duration-200 flex items-center gap-3 bg-slate-50 border-slate-200 text-slate-600">
                <div id="step-badge-3" class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-black text-sm shrink-0">
                    4
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Passo 4</p>
                    <p class="text-xs sm:text-sm font-bold truncate text-slate-700">Taxas & Termos</p>
                </div>
            </button>
        </div>
    </div>

    <!-- Multi-Step Form Container -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('public.pre-enrollment.store') }}" method="POST" enctype="multipart/form-data" id="enrollmentForm" class="p-6 sm:p-8 md:p-10">
            @csrf
            @if(isset($allocation) && $allocation)
                <input type="hidden" name="allocation_id" value="{{ $allocation->id }}">
            @endif
            @php
                $allocFirstName = '';
                $allocLastName = '';
                if (isset($allocation) && $allocation) {
                    $parts = explode(' ', trim($allocation->student_name));
                    $allocFirstName = array_shift($parts);
                    $allocLastName = implode(' ', $parts);
                }
            @endphp

            <!-- ==========================================
                 PASSO 1: ESCOLA TERRITORIAL & DADOS DO ALUNO
                 ========================================== -->
            <div class="form-step space-y-8" id="step1">
                <div>
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-school-flag"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900">1. Seleção da Escola Pretendida & Dados do Candidato</h2>
                            <p class="text-xs text-slate-500">Filtre por Província e Distrito para localizar a instituição de ensino desejada.</p>
                        </div>
                    </div>
                </div>

                <!-- Territorial Cascade Selector Card -->
                <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border-2 border-emerald-500/30 relative">
                    <div class="flex items-center gap-2 mb-4 text-emerald-950 font-bold text-sm">
                        <i class="fa-solid fa-map-location-dot text-emerald-700 text-base"></i>
                        <span>Enquadramento Territorial da Escola no SNE:</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Província -->
                        <div>
                            <label for="province_select" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Província <span class="text-rose-600">*</span>
                            </label>
                            <select id="province_select" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3 bg-white transition outline-hidden" onchange="filterTerritoryOptions()">
                                <option value="">Todas as Províncias</option>
                                @if(isset($provinces))
                                    @foreach($provinces as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Distrito -->
                        <div>
                            <label for="district_select" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Distrito <span class="text-rose-600">*</span>
                            </label>
                            <select id="district_select" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3 bg-white transition outline-hidden" onchange="filterTerritoryOptions()">
                                <option value="">Todos os Distritos</option>
                                @if(isset($provinces))
                                    @foreach($provinces as $p)
                                        @foreach($p->districts as $d)
                                            <option value="{{ $d->id }}" data-province="{{ $p->id }}">{{ $d->name }} ({{ $p->name }})</option>
                                        @endforeach
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Escola Pretendida -->
                        <div>
                            <label for="school_select" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Escola Pretendida <span class="text-rose-600">*</span>
                            </label>
                            <select name="school_id" id="school_select" required class="w-full text-xs sm:text-sm rounded-xl border border-emerald-500/60 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3 bg-white font-medium text-slate-900 transition outline-hidden" onchange="onSchoolChanged()">
                                <option value="">Selecione a Escola...</option>
                                @if(isset($schools))
                                    @foreach($schools as $s)
                                        @php
                                            $typeLabel = match($s->type) {
                                                'public' => 'Pública',
                                                'private' => 'Privada',
                                                'community', 'comunitaria' => 'Comunitária',
                                                default => ucfirst($s->type ?? 'Geral')
                                            };
                                            $isPreselected = (isset($selectedSchoolId) && $selectedSchoolId == $s->id) || old('school_id') == $s->id;
                                        @endphp
                                        <option value="{{ $s->id }}" 
                                                data-province="{{ $s->province_id }}" 
                                                data-district="{{ $s->district_id }}" 
                                                data-code="{{ $s->code }}"
                                                data-type="{{ $typeLabel }}"
                                                {{ $isPreselected ? 'selected' : '' }}>
                                            {{ $s->name }} ({{ $typeLabel }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Selected School Info Preview Badge -->
                    <div id="school-preview-badge" class="mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 hidden items-center justify-between flex-wrap gap-2 text-xs">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-emerald-600 text-sm"></i>
                            <span class="text-emerald-950 font-bold" id="school-preview-name">Escola Selecionada</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-200 text-emerald-900 font-bold" id="school-preview-type">Pública</span>
                            <span class="text-slate-500" id="school-preview-code">Código: SNE-000</span>
                        </div>
                    </div>
                </div>

                <!-- Student Information Form Fields -->
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-graduate text-emerald-700"></i>
                        Identificação do Aluno / Candidato
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <!-- Nome Próprio -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nome Próprio <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="student[first_name]" value="{{ old('student.first_name', $allocFirstName) }}" required placeholder="Ex: Joaquim" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                        </div>

                        <!-- Apelido -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Apelido <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="student[last_name]" value="{{ old('student.last_name', $allocLastName) }}" required placeholder="Ex: Sitoe" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                        </div>

                        <!-- Data de Nascimento -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Data de Nascimento <span class="text-rose-600">*</span>
                            </label>
                            <input type="date" name="student[birth_date]" value="{{ old('student.birth_date', $allocation->birth_date ?? '') }}" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                        </div>

                        <!-- Sexo -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Sexo / Género <span class="text-rose-600">*</span>
                            </label>
                            <select name="student[gender]" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                                <option value="">Selecione...</option>
                                <option value="male" {{ (old('student.gender') == 'male' || (isset($allocation) && in_array(strtolower($allocation->gender ?? ''), ['male', 'm']))) ? 'selected' : '' }}>Masculino (H)</option>
                                <option value="female" {{ (old('student.gender') == 'female' || (isset($allocation) && in_array(strtolower($allocation->gender ?? ''), ['female', 'f']))) ? 'selected' : '' }}>Feminino (M)</option>
                            </select>
                        </div>

                        <!-- Classe Pretendida -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Classe / Nível Pretendido <span class="text-rose-600">*</span>
                            </label>
                            @php
                                $selectedGrade = old('student.grade_level', $allocation->grade_level ?? '');
                            @endphp
                            <select name="student[grade_level]" required onchange="calculateFees()" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                                <option value="">Selecione a Classe...</option>
                                <optgroup label="Educação Pré-Escolar">
                                    <option value="pre-school" {{ in_array($selectedGrade, ['pre-school', 'pre_escolar']) ? 'selected' : '' }}>Pré-Escolar (Iniciação)</option>
                                </optgroup>
                                <optgroup label="Ensino Primário (1º Grau & 2º Grau)">
                                    <option value="1a_classe" {{ in_array($selectedGrade, ['1', '1a_classe']) ? 'selected' : '' }}>1ª Classe</option>
                                    <option value="2a_classe" {{ in_array($selectedGrade, ['2', '2a_classe']) ? 'selected' : '' }}>2ª Classe</option>
                                    <option value="3a_classe" {{ in_array($selectedGrade, ['3', '3a_classe']) ? 'selected' : '' }}>3ª Classe</option>
                                    <option value="4a_classe" {{ in_array($selectedGrade, ['4', '4a_classe']) ? 'selected' : '' }}>4ª Classe</option>
                                    <option value="5a_classe" {{ in_array($selectedGrade, ['5', '5a_classe']) ? 'selected' : '' }}>5ª Classe</option>
                                    <option value="6a_classe" {{ in_array($selectedGrade, ['6', '6a_classe']) ? 'selected' : '' }}>6ª Classe</option>
                                    <option value="primary" {{ $selectedGrade === 'primary' ? 'selected' : '' }}>Ensino Primário Geral (1ª-6ª)</option>
                                </optgroup>
                                <optgroup label="Ensino Secundário Geral (ESG1 & ESG2)">
                                    <option value="7a_classe" {{ in_array($selectedGrade, ['7', '7a_classe']) ? 'selected' : '' }}>7ª Classe</option>
                                    <option value="8a_classe" {{ in_array($selectedGrade, ['8', '8a_classe']) ? 'selected' : '' }}>8ª Classe</option>
                                    <option value="9a_classe" {{ in_array($selectedGrade, ['9', '9a_classe']) ? 'selected' : '' }}>9ª Classe (Exame Nacional)</option>
                                    <option value="10a_classe" {{ in_array($selectedGrade, ['10', '10a_classe']) ? 'selected' : '' }}>10ª Classe</option>
                                    <option value="11a_classe" {{ in_array($selectedGrade, ['11', '11a_classe']) ? 'selected' : '' }}>11ª Classe</option>
                                    <option value="12a_classe" {{ in_array($selectedGrade, ['12', '12a_classe']) ? 'selected' : '' }}>12ª Classe (Exame Nacional)</option>
                                    <option value="secondary" {{ $selectedGrade === 'secondary' ? 'selected' : '' }}>Ensino Secundário Geral (7ª-12ª)</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Endereço Residencial -->
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Endereço Residencial Completo <span class="text-rose-600">*</span>
                            </label>
                            <textarea name="student[address]" rows="2" required placeholder="Bairro, Quarteirão, Rua, Casa Nº, Cidade..." class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">{{ old('student.address') }}</textarea>
                        </div>

                        <!-- Necessidades Especiais -->
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Necessidades Educativas Especiais (Opcional)
                            </label>
                            <textarea name="student[special_needs_description]" rows="2" placeholder="Se o educando possui necessidades educativas específicas ou cuidados especiais de saúde, descreva aqui..." class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">{{ old('student.special_needs_description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PASSO 2: DADOS DO ENCARREGADO DE EDUCAÇÃO
                 ========================================== -->
            <div class="form-step space-y-8 hidden" id="step2">
                <div>
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-user-group"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900">2. Identificação do Encarregado de Educação</h2>
                            <p class="text-xs text-slate-500">Dados do pai, mãe ou encarregado legal responsável pela matrícula e comunicações.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Nome do Encarregado -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nome do Encarregado <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="parent[first_name]" value="{{ old('parent.first_name') }}" required placeholder="Ex: Maria" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                    </div>

                    <!-- Apelido do Encarregado -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Apelido <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="parent[last_name]" value="{{ old('parent.last_name') }}" required placeholder="Ex: Sitoe" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                    </div>

                    <!-- Grau de Parentesco -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Grau de Parentesco <span class="text-rose-600">*</span>
                        </label>
                        <select name="parent[relationship]" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                            <option value="pai" {{ old('parent.relationship') == 'pai' ? 'selected' : '' }}>Pai</option>
                            <option value="mae" {{ old('parent.relationship') == 'mae' ? 'selected' : '' }}>Mãe</option>
                            <option value="tutor" {{ old('parent.relationship') == 'tutor' ? 'selected' : '' }}>Tutor(a) / Encarregado(a) Legal</option>
                            <option value="outro" {{ old('parent.relationship') == 'outro' ? 'selected' : '' }}>Outro Familiar</option>
                        </select>
                    </div>

                    <!-- Telefone / WhatsApp -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Telefone Principal / WhatsApp <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="text" name="parent[phone]" value="{{ old('parent.phone') }}" required placeholder="84 000 0000 / 82 000 0000" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 pl-9 pr-3.5 bg-white transition outline-hidden">
                        </div>
                    </div>

                    <!-- E-mail -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Endereço de E-mail (Opcional)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" name="parent[email]" value="{{ old('parent.email') }}" placeholder="encarregado@exemplo.com" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 pl-9 pr-3.5 bg-white transition outline-hidden">
                        </div>
                    </div>
                </div>

                <!-- Contactos de Emergência Alternativos -->
                <div class="p-5 rounded-2xl bg-amber-50/60 border border-amber-200">
                    <h3 class="text-xs font-bold text-amber-950 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-phone-volume text-amber-700"></i>
                        Contacto de Emergência Alternativo (Segunda Linha de Contacto)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nome do Contacto de Emergência <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="student[emergency_contact]" value="{{ old('student.emergency_contact') }}" required placeholder="Ex: Manuel Sitoe (Tio)" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Telefone de Emergência <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="student[emergency_phone]" value="{{ old('student.emergency_phone') }}" required placeholder="84 999 9999 / 86 999 9999" class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 py-2.5 px-3.5 bg-white transition outline-hidden">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 PASSO 3: DOCUMENTAÇÃO DIGITAL
                 ========================================== -->
            <div class="form-step space-y-8 hidden" id="step3">
                <div>
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-file-arrow-up"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900">3. Envio de Documentos Digitais</h2>
                            <p class="text-xs text-slate-500">Faça o upload dos ficheiros digitalizados do candidato (formatos aceites: PDF, JPG, PNG — até 5MB por ficheiro).</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- BI / Cédula -->
                    <div class="p-5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/50 transition">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-id-card text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-black text-slate-800 mb-1">
                                    BI ou Certidão de Nascimento / Cédula
                                </label>
                                <p class="text-[11px] text-slate-500 mb-3">Documento de identificação oficial do educando.</p>
                                <input type="file" name="documents[id_card]" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFileName(this, 'file-name-id_card')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-800 file:text-white hover:file:bg-emerald-900 cursor-pointer">
                                <div id="file-name-id_card" class="mt-2 text-xs font-semibold text-emerald-800 hidden"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Foto Tipo Passe -->
                    <div class="p-5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/50 transition">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-camera text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-black text-slate-800 mb-1">
                                    Fotografia Tipo Passe Recente
                                </label>
                                <p class="text-[11px] text-slate-500 mb-3">Foto frontal nítida com fundo claro para o cadastro escolar.</p>
                                <input type="file" name="documents[photo]" accept=".jpg,.jpeg,.png" onchange="previewFileName(this, 'file-name-photo')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-800 file:text-white hover:file:bg-emerald-900 cursor-pointer">
                                <div id="file-name-photo" class="mt-2 text-xs font-semibold text-emerald-800 hidden"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Boletim Anterior / Declaração -->
                    <div class="p-5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/50 transition">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-file-lines text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-black text-slate-800 mb-1">
                                    Boletim de Notas Anterior / Certificado
                                </label>
                                <p class="text-[11px] text-slate-500 mb-3">Obrigatório para alunos transferidos ou em transição de ciclo.</p>
                                <input type="file" name="documents[report_card]" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFileName(this, 'file-name-report_card')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-800 file:text-white hover:file:bg-emerald-900 cursor-pointer">
                                <div id="file-name-report_card" class="mt-2 text-xs font-semibold text-emerald-800 hidden"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Atestado Médico -->
                    <div class="p-5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/50 transition">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-notes-medical text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="block text-xs font-black text-slate-800 mb-1">
                                    Atestado Médico / Cartão de Vacinas
                                </label>
                                <p class="text-[11px] text-slate-500 mb-3">Comprovativo de aptidão física e vacinação atualizada.</p>
                                <input type="file" name="documents[medical]" accept=".pdf,.jpg,.jpeg,.png" onchange="previewFileName(this, 'file-name-medical')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-800 file:text-white hover:file:bg-emerald-900 cursor-pointer">
                                <div id="file-name-medical" class="mt-2 text-xs font-semibold text-emerald-800 hidden"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-blue-600 text-base shrink-0"></i>
                    <span>Caso não possua os documentos digitalizados neste momento, poderá apresentá-los fisicamente na secretaria da escola no ato da validação presencial.</span>
                </div>
            </div>

            <!-- ==========================================
                 PASSO 4: TERMOS, TAXAS & SUBMISSÃO
                 ========================================== -->
            <div class="form-step space-y-8 hidden" id="step4">
                <div>
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900">4. Estimativa de Taxas & Confirmação de Termos</h2>
                            <p class="text-xs text-slate-500">Revise os valores associados à matrícula e aceite os termos do regulamento escolar.</p>
                        </div>
                    </div>
                </div>

                <!-- Fee Simulation Summary Card -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-4 pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                            <i class="fa-solid fa-receipt text-emerald-700"></i>
                            <span>Resumo de Taxas Aplicáveis (Ano {{ $academicYear ?? '2027' }})</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[11px] font-bold">Estimativa Oficial</span>
                    </div>

                    <div id="feeSummary" class="divide-y divide-slate-200 text-xs sm:text-sm">
                        <!-- Populated by JS -->
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-300 flex items-center justify-between text-slate-900">
                        <span class="font-extrabold text-sm sm:text-base">Valor Total Estimado:</span>
                        <div class="text-right">
                            <span class="text-xl sm:text-2xl font-black text-emerald-800" id="totalAmount">0,00</span>
                            <span class="text-xs font-bold text-slate-600 ml-1">MT</span>
                        </div>
                    </div>
                </div>

                <!-- Legal Terms & Checkboxes -->
                <div class="space-y-3.5 p-5 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                    <h3 class="text-xs font-black text-emerald-950 uppercase tracking-wider mb-2">
                        Declarações de Aceite & Compromisso
                    </h3>

                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="terms[responsibility]" required id="term1" class="mt-1 w-4 h-4 rounded text-emerald-700 border-slate-300 focus:ring-emerald-500">
                        <span class="text-xs text-slate-700 leading-relaxed">
                            Declaro, sob compromisso de honra, que todas as informações prestadas são verdadeiras e completas, sujeitas a validação pelos serviços pedagógicos do MINEDH. <span class="text-rose-600 font-bold">*</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="terms[rules]" required id="term3" class="mt-1 w-4 h-4 rounded text-emerald-700 border-slate-300 focus:ring-emerald-500">
                        <span class="text-xs text-slate-700 leading-relaxed">
                            Comprometo-me a cumprir integralmente o <strong>Regulamento Interno da Escola</strong> e as diretrizes do Sistema Nacional de Educação (SNE). <span class="text-rose-600 font-bold">*</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="terms[image_consent]" id="term2" class="mt-1 w-4 h-4 rounded text-emerald-700 border-slate-300 focus:ring-emerald-500">
                        <span class="text-xs text-slate-600 leading-relaxed">
                            Autorizo a captação e utilização de imagem do educando para registo no cartão de estudante e atividades estritamente pedagógicas da escola.
                        </span>
                    </label>
                </div>

                <!-- Payment Advisory Notice -->
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-bold">Procedimento de Pagamento e Efetivação:</p>
                        <p class="mt-0.5 text-amber-800 leading-relaxed">
                            Após a submissão online, será gerada uma referência de pré-inscrição com as coordenadas bancárias da escola. A vaga fica pré-reservada aguardando a validação física dos documentos e confirmação na secretaria.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 WIZARD NAVIGATION BUTTONS
                 ========================================== -->
            <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between gap-4">
                <button type="button" onclick="prevStep()" id="prevBtn" class="hidden inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs sm:text-sm transition shadow-xs">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Voltar</span>
                </button>

                <div class="ml-auto flex items-center gap-3">
                    <button type="button" onclick="nextStep()" id="nextBtn" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs sm:text-sm transition shadow-md hover:shadow-lg">
                        <span>Avançar para Passo Seguinte</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <button type="submit" id="submitBtn" class="hidden inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs sm:text-sm transition shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Submeter Pré-Inscrição Oficial</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const steps = ['step1', 'step2', 'step3', 'step4'];
    let currentStep = 0;
    const fees = @json($fees ?? []);

    function filterTerritoryOptions() {
        const provSelect = document.getElementById('province_select');
        const distSelect = document.getElementById('district_select');
        const schoolSelect = document.getElementById('school_select');

        const provId = provSelect.value;
        const distId = distSelect.value;

        // Filter Districts
        const distOptions = distSelect.querySelectorAll('option[data-province]');
        distOptions.forEach(opt => {
            if (!provId || opt.getAttribute('data-province') === provId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
                if (opt.selected) {
                    distSelect.value = '';
                }
            }
        });

        // Filter Schools
        const schoolOptions = schoolSelect.querySelectorAll('option[data-province]');
        schoolOptions.forEach(opt => {
            const sProv = opt.getAttribute('data-province');
            const sDist = opt.getAttribute('data-district');

            let show = true;
            if (provId && sProv !== provId) show = false;
            if (distId && sDist !== distId) show = false;

            opt.style.display = show ? '' : 'none';
            if (!show && opt.selected) {
                schoolSelect.value = '';
            }
        });

        onSchoolChanged();
    }

    function onSchoolChanged() {
        const schoolSelect = document.getElementById('school_select');
        const selectedOpt = schoolSelect.options[schoolSelect.selectedIndex];
        const previewBadge = document.getElementById('school-preview-badge');

        if (selectedOpt && selectedOpt.value) {
            const name = selectedOpt.text.split('(')[0].trim();
            const type = selectedOpt.getAttribute('data-type') || 'Pública';
            const code = selectedOpt.getAttribute('data-code') || ('SNE-' + selectedOpt.value);

            document.getElementById('school-preview-name').textContent = name;
            document.getElementById('school-preview-type').textContent = type;
            document.getElementById('school-preview-code').textContent = 'Código: ' + code;
            previewBadge.classList.remove('hidden');
            previewBadge.classList.add('flex');

            // Auto-sync province and district selectors if empty
            const provId = selectedOpt.getAttribute('data-province');
            const distId = selectedOpt.getAttribute('data-district');
            const provSelect = document.getElementById('province_select');
            const distSelect = document.getElementById('district_select');

            if (provId && !provSelect.value) {
                provSelect.value = provId;
            }
            if (distId && !distSelect.value) {
                distSelect.value = distId;
            }
        } else {
            previewBadge.classList.add('hidden');
            previewBadge.classList.remove('flex');
        }
    }

    function previewFileName(input, targetId) {
        const target = document.getElementById(targetId);
        if (input.files && input.files[0]) {
            target.innerHTML = `<i class="fa-solid fa-file-check mr-1.5"></i> Ficheiro selecionado: <strong>${input.files[0].name}</strong> (${(input.files[0].size / (1024*1024)).toFixed(2)} MB)`;
            target.classList.remove('hidden');
        } else {
            target.classList.add('hidden');
        }
    }

    function calculateFees() {
        const gradeSelect = document.querySelector('select[name="student[grade_level]"]');
        const gradeLevel = gradeSelect ? gradeSelect.value : '';
        const summary = document.getElementById('feeSummary');
        let total = 0;

        summary.innerHTML = '';

        if (!fees || fees.length === 0) {
            summary.innerHTML = `
                <div class="py-3 text-slate-500 text-center italic">
                    Nenhuma taxa cadastrada para este nível de ensino (Gratuito / Isento no SNE Público).
                </div>
            `;
            document.getElementById('totalAmount').innerText = '0,00';
            return;
        }

        let applicableFees = fees.filter(f => !f.grade_level || f.grade_level === gradeLevel || f.grade_level === 'all');

        if (applicableFees.length === 0) {
            summary.innerHTML = `
                <div class="py-3 text-slate-500 text-center italic">
                    Sem taxas obrigatórias associadas à classe selecionada.
                </div>
            `;
        } else {
            applicableFees.forEach(fee => {
                const amount = parseFloat(fee.amount || 0);
                total += amount;

                const row = document.createElement('div');
                row.className = 'py-2.5 flex items-center justify-between';
                row.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-dot text-[9px] text-emerald-600"></i>
                        <span class="font-bold text-slate-800">${fee.name}</span>
                        ${fee.is_mandatory ? '<span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold">Obrigatória</span>' : ''}
                    </div>
                    <span class="font-black text-slate-900">${amount.toLocaleString('pt-MZ', { minimumFractionDigits: 2 })} MT</span>
                `;
                summary.appendChild(row);
            });
        }

        document.getElementById('totalAmount').innerText = total.toLocaleString('pt-MZ', { minimumFractionDigits: 2 });
    }

    function validateCurrentStep() {
        const currentContainer = document.getElementById(steps[currentStep]);
        const requiredInputs = currentContainer.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            if (!input.checkValidity()) {
                input.reportValidity();
                isValid = false;
                return false;
            }
        });

        return isValid;
    }

    function updateWizardUI() {
        // Toggle step containers
        steps.forEach((stepId, index) => {
            const el = document.getElementById(stepId);
            if (index === currentStep) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }

            // Update Indicator Badges
            const ind = document.getElementById(`step-indicator-${index}`);
            const badge = document.getElementById(`step-badge-${index}`);

            if (index === currentStep) {
                ind.className = 'step-indicator group text-left p-3 rounded-xl border transition-all duration-200 flex items-center gap-3 bg-emerald-50 border-emerald-600 text-emerald-950 shadow-xs ring-2 ring-emerald-500/20';
                badge.className = 'w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-xs';
                badge.innerHTML = (index + 1);
            } else if (index < currentStep) {
                ind.className = 'step-indicator group text-left p-3 rounded-xl border transition-all duration-200 flex items-center gap-3 bg-emerald-50/50 border-emerald-300 text-emerald-900';
                badge.className = 'w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shrink-0';
                badge.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
            } else {
                ind.className = 'step-indicator group text-left p-3 rounded-xl border transition-all duration-200 flex items-center gap-3 bg-slate-50 border-slate-200 text-slate-600';
                badge.className = 'w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-black text-sm shrink-0';
                badge.innerHTML = (index + 1);
            }
        });

        // Toggle buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (currentStep === 0) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }

        if (currentStep === steps.length - 1) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            calculateFees();
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }

        // Scroll to wizard top
        window.scrollTo({ top: document.getElementById('enrollmentForm').offsetTop - 80, behavior: 'smooth' });
    }

    function nextStep() {
        if (validateCurrentStep()) {
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateWizardUI();
            }
        }
    }

    function prevStep() {
        if (currentStep > 0) {
            currentStep--;
            updateWizardUI();
        }
    }

    function goToStep(stepIndex) {
        if (stepIndex < currentStep) {
            currentStep = stepIndex;
            updateWizardUI();
        } else if (stepIndex > currentStep) {
            if (validateCurrentStep()) {
                currentStep = stepIndex;
                updateWizardUI();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        filterTerritoryOptions();
        onSchoolChanged();
        updateWizardUI();
    });
</script>
@endpush
@endsection