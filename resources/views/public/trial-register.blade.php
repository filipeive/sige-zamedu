<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar Escola — ZAMEDU - SIGE | MINEDH Moçambique</title>
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --mz-green-dark: #034522;
            --mz-green-primary: #007A3D;
            --mz-green-light: #059669;
            --mz-gold: #FCD116;
            --mz-gold-dark: #D97706;
            --mz-red: #CE1126;
            --mz-navy: #0F172A;
            --mz-slate: #1E293B;
            --mz-bg: #F8FAFC;
        }

        body { font-family: 'Inter', sans-serif; background: #F8FAFC; color: var(--mz-navy); min-height: 100vh; display: flex; flex-direction: column; }
        h1, h2, h3, h4, .font-heading { font-family: 'Poppins', sans-serif; }

        /* Top Header */
        .top-header {
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 12px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        /* Wizard Steps */
        .wizard-step { display: none; animation: fadeInUp 0.35s cubic-bezier(0.16, 1, 0.3, 1); }
        .wizard-step.active { display: block; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

        /* Step Indicator */
        .step-indicator { display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 2rem; }
        .step-dot { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 15px; transition: all 0.3s ease;
            background: #E2E8F0; color: #64748B; border: 2px solid #E2E8F0; }
        .step-dot.active { background: var(--mz-green-primary); color: #FFFFFF; border-color: var(--mz-gold); box-shadow: 0 0 0 4px rgba(0, 122, 61, 0.2); }
        .step-dot.completed { background: #10B981; color: #FFFFFF; border-color: #10B981; }
        .step-line { width: 64px; height: 3px; background: #E2E8F0; align-self: center; transition: background 0.3s; }
        .step-line.completed { background: #10B981; }

        /* Institution Type Cards */
        .type-card { cursor: pointer; border: 2px solid #E2E8F0; border-radius: 1.25rem; padding: 1.5rem; transition: all 0.3s ease; position: relative; background: #FFFFFF; }
        .type-card:hover { border-color: var(--mz-green-light); transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .type-card.selected { border-color: var(--mz-green-primary); background: #F0FDF4; box-shadow: 0 0 0 3px rgba(0, 122, 61, 0.15); }
        .type-card .check-mark { position: absolute; top: 14px; right: 14px; width: 26px; height: 26px; border-radius: 50%;
            background: var(--mz-green-primary); color: #FFFFFF; display: none; align-items: center; justify-content: center; font-size: 13px; }
        .type-card.selected .check-mark { display: flex; }

        .input-field { width: 100%; padding: 0.85rem 1.1rem; border-radius: 0.75rem; border: 1.5px solid #E2E8F0;
            font-size: 0.9rem; font-weight: 600; color: var(--mz-navy); transition: all 0.2s; outline: none; background: #F8FAFC; }
        .input-field:focus { background: #FFFFFF; border-color: var(--mz-green-primary); box-shadow: 0 0 0 4px rgba(0, 122, 61, 0.12); }
        .input-label { display: flex; align-items: center; gap: 6px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--mz-slate); margin-bottom: 0.5rem; }

        .btn-primary-wizard { background: linear-gradient(135deg, var(--mz-green-primary) 0%, var(--mz-green-dark) 100%); color: #FFFFFF; padding: 0.875rem 2.25rem; border-radius: 0.85rem; font-weight: 800;
            font-size: 0.9rem; border: none; cursor: pointer; transition: all 0.25s ease; display: inline-flex; align-items: center; gap: 0.6rem; box-shadow: 0 4px 15px rgba(0, 122, 61, 0.25); }
        .btn-primary-wizard:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 122, 61, 0.35); }
        .btn-secondary-wizard { background: #FFFFFF; color: var(--mz-slate); padding: 0.875rem 2rem; border-radius: 0.85rem; font-weight: 700;
            font-size: 0.9rem; border: 1.5px solid #E2E8F0; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-secondary-wizard:hover { background: #F8FAFC; border-color: #CBD5E1; }
    </style>
</head>
<body>
    <!-- TOP BAR -->
    <header class="top-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 no-underline">
                <div class="bg-white p-1 rounded-xl border-2 border-amber-400 shadow-sm flex items-center justify-center">
                    <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" class="h-9 w-auto">
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-widest block">AQUI SE PREPARA A NOVA GERAÇÃO</span>
                    <h1 class="text-lg font-black text-slate-900 leading-none">ESCOLA DOS VISIONÁRIOS</h1>
                </div>
            </a>
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-emerald-800 text-white text-xs font-bold hover:bg-emerald-900 transition shadow-sm flex items-center gap-2">
                <i class="fas fa-right-to-bracket"></i> Aceder ao Sistema
            </a>
        </div>
    </header>

    <div class="h-1 bg-gradient-to-r from-red-600 via-amber-400 to-emerald-700"></div>

    <!-- HERO HEADER -->
    <section style="background: linear-gradient(135deg, #034522 0%, #007A3D 100%); color: #ffffff;" class="py-10 relative overflow-hidden shadow-inner border-b-4 border-amber-400">
        <div class="max-w-3xl mx-auto px-4 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-900/80 border border-amber-400/50 text-amber-300 text-xs font-extrabold uppercase tracking-wider mb-3 backdrop-blur-sm">
                <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" class="h-4 w-auto me-1">
                ATIVAÇÃO E REGISTO DE INSTITUIÇÃO
            </div>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight mb-2 font-heading drop-shadow-sm" style="color: #ffffff !important;">Registe a Sua Escola no SIGE</h1>
            <p class="text-emerald-100 text-sm max-w-xl mx-auto font-medium" style="color: #e2e8f0 !important;">Crie o seu ambiente de gestão escolar em conformidade integral com o Sistema Nacional de Educação (SNE - Lei 18/2018).</p>
        </div>
    </section>

    <!-- WIZARD CONTAINER -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow">

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <div class="font-bold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle text-rose-600"></i> Verifique os erros abaixo:</div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Step Indicator -->
        <div class="step-indicator" id="stepIndicator">
            <div class="step-dot active" data-step="1">1</div>
            <div class="step-line"></div>
            <div class="step-dot" data-step="2">2</div>
            <div class="step-line"></div>
            <div class="step-dot" data-step="3">3</div>
        </div>

        <form action="{{ route('public.trial-register.store') }}" method="POST" id="wizardForm">
            @csrf

            <!-- ============ STEP 1: Dados da Escola ============ -->
            <div class="wizard-step active" id="step1">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-lg">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3 mb-6 font-heading border-b pb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold"><i class="fas fa-school"></i></div>
                        1. Dados da Instituição Escolar
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label class="input-label"><i class="fas fa-signature text-emerald-700"></i> Nome Completo da Escola *</label>
                            <input type="text" name="school_name" value="{{ old('school_name') }}" required class="input-field" placeholder="Ex.: Escola Secundária de Namacurra Sede">
                        </div>

                        <!-- Institution Type Cards -->
                        <div>
                            <label class="input-label"><i class="fas fa-layer-group text-emerald-700"></i> Tipo de Instituição *</label>
                            <input type="hidden" name="institution_type" id="institutionTypeInput" value="{{ old('institution_type', 'escola_privada') }}" required>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="type-card {{ old('institution_type', 'escola_privada') === 'escola_privada' ? 'selected' : '' }}" onclick="selectType('escola_privada')">
                                    <div class="check-mark"><i class="fas fa-check"></i></div>
                                    <div class="mb-3"><i class="fas fa-coins text-emerald-700 text-3xl"></i></div>
                                    <h4 class="font-bold text-slate-900 text-sm font-heading">Escola Privada</h4>
                                    <p class="text-xs text-slate-500 mt-1">Gestão académica completa + Tesouraria (Propinas, Recibos PDF e Pagamentos MPesa/eMola).</p>
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Propinas</span>
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Financeiro</span>
                                    </div>
                                </div>

                                <div class="type-card {{ old('institution_type') === 'escola_publica' ? 'selected' : '' }}" onclick="selectType('escola_publica')">
                                    <div class="check-mark"><i class="fas fa-check"></i></div>
                                    <div class="mb-3"><i class="fas fa-landmark text-amber-600 text-3xl"></i></div>
                                    <h4 class="font-bold text-slate-900 text-sm font-heading">Escola Pública</h4>
                                    <p class="text-xs text-slate-500 mt-1">Modo isenção de propinas ativado. Foco em Pautas MINEDH, presenças, estatísticas por género (HM/M).</p>
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-bold">Isenção</span>
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Pautas MINEDH</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label"><i class="fas fa-map-marked-alt text-emerald-700"></i> Província *</label>
                                <select name="province" required class="input-field">
                                    <option value="Zambézia" {{ old('province', 'Zambézia') == 'Zambézia' ? 'selected' : '' }}>Zambézia</option>
                                    <option value="Maputo Cidade" {{ old('province') == 'Maputo Cidade' ? 'selected' : '' }}>Maputo Cidade</option>
                                    <option value="Maputo Província" {{ old('province') == 'Maputo Província' ? 'selected' : '' }}>Maputo Província</option>
                                    <option value="Gaza" {{ old('province') == 'Gaza' ? 'selected' : '' }}>Gaza</option>
                                    <option value="Inhambane" {{ old('province') == 'Inhambane' ? 'selected' : '' }}>Inhambane</option>
                                    <option value="Sofala" {{ old('province') == 'Sofala' ? 'selected' : '' }}>Sofala</option>
                                    <option value="Manica" {{ old('province') == 'Manica' ? 'selected' : '' }}>Manica</option>
                                    <option value="Tete" {{ old('province') == 'Tete' ? 'selected' : '' }}>Tete</option>
                                    <option value="Nampula" {{ old('province') == 'Nampula' ? 'selected' : '' }}>Nampula</option>
                                    <option value="Cabo Delgado" {{ old('province') == 'Cabo Delgado' ? 'selected' : '' }}>Cabo Delgado</option>
                                    <option value="Niassa" {{ old('province') == 'Niassa' ? 'selected' : '' }}>Niassa</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label"><i class="fas fa-city text-emerald-700"></i> Distrito / Bairro</label>
                                <input type="text" name="district" value="{{ old('district', 'Namacurra') }}" class="input-field" placeholder="Ex.: Namacurra Sede">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label"><i class="fas fa-graduation-cap text-emerald-700"></i> Nível de Ensino</label>
                                <select name="education_level" class="input-field">
                                    <option value="secundario" {{ old('education_level', 'secundario') == 'secundario' ? 'selected' : '' }}>Ensino Secundário Geral (8ª - 12ª Classe)</option>
                                    <option value="primario" {{ old('education_level') == 'primario' ? 'selected' : '' }}>Ensino Primário (1ª - 7ª Classe)</option>
                                    <option value="primario_secundario" {{ old('education_level') == 'primario_secundario' ? 'selected' : '' }}>Primário e Secundário Completo</option>
                                    <option value="tecnico" {{ old('education_level') == 'tecnico' ? 'selected' : '' }}>Ensino Técnico Profissional</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label"><i class="fas fa-users text-emerald-700"></i> N.º Estimado de Alunos</label>
                                <select name="estimated_students" class="input-field">
                                    <option value="150">Até 150 alunos</option>
                                    <option value="400" selected>151 a 400 alunos</option>
                                    <option value="800">401 a 800 alunos</option>
                                    <option value="1500">Mais de 800 alunos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" class="btn-primary-wizard" onclick="goToStep(2)">
                            Próximo Passo <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============ STEP 2: Conta do Administrador ============ -->
            <div class="wizard-step" id="step2">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-lg">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3 mb-6 font-heading border-b pb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold"><i class="fas fa-user-shield"></i></div>
                        2. Conta do Diretor / Administrador Inicial
                    </h3>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label"><i class="fas fa-user text-emerald-700"></i> Nome Completo do Diretor *</label>
                                <input type="text" name="director_name" value="{{ old('director_name') }}" required class="input-field" placeholder="Ex.: Dr. Armindo Sitoe">
                            </div>
                            <div>
                                <label class="input-label"><i class="fas fa-phone text-emerald-700"></i> Telefone / WhatsApp *</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required class="input-field" placeholder="+258 84 000 0000">
                            </div>
                        </div>

                        <div>
                            <label class="input-label"><i class="fas fa-envelope text-emerald-700"></i> E-mail de Login do Administrador *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="input-field" placeholder="direcao@escola.gov.mz">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label"><i class="fas fa-lock text-emerald-700"></i> Palavra-Passe *</label>
                                <input type="password" name="password" required class="input-field" placeholder="Mínimo de 6 caracteres">
                            </div>
                            <div>
                                <label class="input-label"><i class="fas fa-check-double text-emerald-700"></i> Confirmar Palavra-Passe *</label>
                                <input type="password" name="password_confirmation" required class="input-field" placeholder="Repita a palavra-passe">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" class="btn-secondary-wizard" onclick="goToStep(1)">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn-primary-wizard" onclick="goToStep(3)">
                            Próximo Passo <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============ STEP 3: Configuração Inicial ============ -->
            <div class="wizard-step" id="step3">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-lg">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3 mb-2 font-heading">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold"><i class="fas fa-sliders"></i></div>
                        3. Configurações Académicas Iniciais
                    </h3>
                    <p class="text-xs text-slate-500 mb-6 border-b pb-4">Definições base do Sistema Nacional de Educação (alteráveis a qualquer momento nas configurações).</p>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label"><i class="fas fa-calendar-alt text-emerald-700"></i> Ano Lectivo</label>
                                <input type="text" name="academic_year" value="{{ old('academic_year', date('Y')) }}" class="input-field">
                            </div>
                            <div>
                                <label class="input-label"><i class="fas fa-star text-emerald-700"></i> Escala de Avaliação</label>
                                <select name="grading_scale" class="input-field">
                                    <option value="0_20" selected>0.0 a 20.0 valores (Moçambique SNE)</option>
                                    <option value="0_10">0 a 10 valores</option>
                                    <option value="A_F">Letras (A - F)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mt-4">
                            <h4 class="text-xs font-black text-emerald-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <i class="fas fa-shield-check text-emerald-700 text-sm"></i> Resumo da Ativação Escolar
                            </h4>
                            <div class="text-xs text-emerald-800 space-y-1 font-medium">
                                <p>✓ Período de avaliação de <strong>15 dias</strong> com todas as funcionalidades ativas</p>
                                <p>✓ Isolamento multi-tenant de dados da escola (`BelongsToSchool` / `SchoolScope`)</p>
                                <p>✓ Suporte técnico garantido pela equipa ZAMEDU - SIGE</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" class="btn-secondary-wizard" onclick="goToStep(2)">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="submit" class="btn-primary-wizard" style="background: linear-gradient(135deg, #007A3D 0%, #034522 100%); padding: 1rem 2.5rem; font-size: 0.95rem;">
                            <i class="fas fa-rocket text-amber-300"></i> Criar Escola & Iniciar Teste Grátis
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-950 text-slate-400 py-6 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs">
            &copy; {{ date('Y') }} <strong>ZAMEDU - SIGE</strong> · Todos os direitos reservados. · Desenvolvido por <a href="http://146.235.224.99/" target="_blank" class="text-emerald-400 font-bold hover:underline">Filipe dos Santos</a>
        </div>
    </footer>

    <script>
        let currentStep = 1;

        function selectType(type) {
            document.getElementById('institutionTypeInput').value = type;
            document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
        }

        function goToStep(step) {
            if (step > currentStep) {
                const currentStepEl = document.getElementById('step' + currentStep);
                const requiredFields = currentStepEl.querySelectorAll('[required]');
                let valid = true;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#ef4444';
                        field.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.1)';
                        valid = false;
                        setTimeout(() => { field.style.borderColor = ''; field.style.boxShadow = ''; }, 2000);
                    }
                });
                if (!valid) return;
            }

            document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');

            document.querySelectorAll('.step-dot').forEach(dot => {
                const dotStep = parseInt(dot.dataset.step);
                dot.classList.remove('active', 'completed');
                if (dotStep === step) dot.classList.add('active');
                else if (dotStep < step) dot.classList.add('completed');
            });
            document.querySelectorAll('.step-line').forEach((line, i) => {
                line.classList.toggle('completed', i < step - 1);
            });

            currentStep = step;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
