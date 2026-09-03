<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar Escola — {{ setting('school_name', config('app.name', 'ZamEdu')) }}</title>
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --green-900: #1B5E20; --green-800: #2E7D32; --green-700: #388E3C;
            --yellow-400: #FFC107; --gray-900: #212121; --gray-50: #FAFAFA;
        }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%); color: var(--gray-900); min-height: 100vh; }
        h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; }

        /* Wizard Steps */
        .wizard-step { display: none; animation: fadeInUp 0.4s ease-out; }
        .wizard-step.active { display: block; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Step Indicator */
        .step-indicator { display: flex; justify-content: center; gap: 8px; margin-bottom: 2rem; }
        .step-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; transition: all 0.3s ease;
            background: #e2e8f0; color: #94a3b8; border: 2px solid #e2e8f0; }
        .step-dot.active { background: var(--green-800); color: white; border-color: var(--green-800); box-shadow: 0 0 0 4px rgba(46,125,50,0.2); }
        .step-dot.completed { background: #10b981; color: white; border-color: #10b981; }
        .step-line { width: 60px; height: 2px; background: #e2e8f0; align-self: center; transition: background 0.3s; }
        .step-line.completed { background: #10b981; }

        /* Institution Type Cards */
        .type-card { cursor: pointer; border: 2px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; transition: all 0.3s ease; position: relative; }
        .type-card:hover { border-color: #a7f3d0; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .type-card.selected { border-color: var(--green-800); background: #f0fdf4; box-shadow: 0 0 0 3px rgba(46,125,50,0.15); }
        .type-card .check-mark { position: absolute; top: 12px; right: 12px; width: 24px; height: 24px; border-radius: 50%;
            background: var(--green-800); color: white; display: none; align-items: center; justify-content: center; font-size: 12px; }
        .type-card.selected .check-mark { display: flex; }

        .input-field { width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0;
            font-size: 0.875rem; transition: all 0.2s; outline: none; background: white; }
        .input-field:focus { border-color: var(--green-800); box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
        .input-label { display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; margin-bottom: 0.5rem; }

        .btn-primary { background: var(--green-800); color: white; padding: 0.875rem 2rem; border-radius: 0.75rem; font-weight: 700;
            font-size: 0.875rem; border: none; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary:hover { background: var(--green-700); transform: translateY(-1px); box-shadow: 0 4px 15px rgba(46,125,50,0.3); }
        .btn-secondary { background: white; color: #475569; padding: 0.875rem 2rem; border-radius: 0.75rem; font-weight: 600;
            font-size: 0.875rem; border: 1.5px solid #e2e8f0; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; }
    </style>
</head>
<body>
    <!-- TOP BAR -->
    <header class="bg-white border-b border-gray-200 py-3 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-800 flex items-center justify-center text-white text-xl font-bold shadow">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-gray-900 leading-tight">SIGE</h1>
                    <p class="text-[10px] text-gray-500 font-medium">Sistema Integrado de Gestão Escolar</p>
                </div>
            </a>
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-emerald-800 text-white text-sm font-semibold hover:bg-emerald-700 transition shadow-sm">
                <i class="fas fa-right-to-bracket mr-2"></i>Já tenho Conta
            </a>
        </div>
    </header>

    <div class="h-1 bg-gradient-to-r from-amber-400 via-emerald-500 to-emerald-700"></div>

    <!-- HERO -->
    <section class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-emerald-900 text-white py-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-emerald-950"></div>
        <div class="max-w-3xl mx-auto px-4 text-center relative z-10">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400 text-gray-900 text-xs font-bold uppercase tracking-wider mb-3">
                <i class="fas fa-rocket"></i> Activação Instantânea — Sem Cartão de Crédito
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">Registe a Sua Escola em 3 Passos</h1>
            <p class="text-emerald-100 text-sm">Crie o seu ambiente de gestão escolar completo em menos de 2 minutos.</p>
        </div>
    </section>

    <!-- WIZARD -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <div class="font-bold mb-1"><i class="fas fa-exclamation-circle me-2"></i>Por favor, verifique os erros abaixo:</div>
                <ul class="list-disc list-inside space-y-1">
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
                <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-emerald-900 flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="fas fa-school text-emerald-700 text-sm"></i></div>
                        Dados da Instituição Escolar
                    </h3>

                    <div class="space-y-5">
                        <div>
                            <label class="input-label">Nome Completo da Escola *</label>
                            <input type="text" name="school_name" value="{{ old('school_name') }}" required class="input-field" placeholder="Ex.: Escola Secundária Visionários de Maputo">
                        </div>

                        <!-- Institution Type Cards -->
                        <div>
                            <label class="input-label">Tipo de Instituição *</label>
                            <input type="hidden" name="institution_type" id="institutionTypeInput" value="{{ old('institution_type', 'escola_privada') }}" required>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="type-card {{ old('institution_type', 'escola_privada') === 'escola_privada' ? 'selected' : '' }}" onclick="selectType('escola_privada')">
                                    <div class="check-mark"><i class="fas fa-check"></i></div>
                                    <div class="mb-2"><i class="fas fa-school text-emerald-700 text-3xl"></i></div>
                                    <h4 class="font-bold text-gray-900 text-sm">Escola Privada</h4>
                                    <p class="text-xs text-gray-500 mt-1">Todos os módulos activos incluindo Mensalidades, Propinas e Gestão Financeira.</p>
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-semibold">Propinas</span>
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-semibold">Financeiro</span>
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-semibold">Académico</span>
                                    </div>
                                </div>
                                <div class="type-card {{ old('institution_type') === 'escola_publica' ? 'selected' : '' }}" onclick="selectType('escola_publica')">
                                    <div class="check-mark"><i class="fas fa-check"></i></div>
                                    <div class="mb-2"><i class="fas fa-landmark text-blue-700 text-3xl"></i></div>
                                    <h4 class="font-bold text-gray-900 text-sm">Escola Pública</h4>
                                    <p class="text-xs text-gray-500 mt-1">Módulo financeiro desactivado. Focado em gestão académica, presenças e pautas.</p>
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-semibold">Académico</span>
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-semibold">Presenças</span>
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-semibold line-through">Propinas</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Província *</label>
                                <select name="province" required class="input-field">
                                    <option value="Maputo Cidade" {{ old('province') == 'Maputo Cidade' ? 'selected' : '' }}>Maputo Cidade</option>
                                    <option value="Maputo Província" {{ old('province') == 'Maputo Província' ? 'selected' : '' }}>Maputo Província</option>
                                    <option value="Gaza" {{ old('province') == 'Gaza' ? 'selected' : '' }}>Gaza</option>
                                    <option value="Inhambane" {{ old('province') == 'Inhambane' ? 'selected' : '' }}>Inhambane</option>
                                    <option value="Sofala" {{ old('province') == 'Sofala' ? 'selected' : '' }}>Sofala</option>
                                    <option value="Manica" {{ old('province') == 'Manica' ? 'selected' : '' }}>Manica</option>
                                    <option value="Tete" {{ old('province') == 'Tete' ? 'selected' : '' }}>Tete</option>
                                    <option value="Zambézia" {{ old('province') == 'Zambézia' ? 'selected' : '' }}>Zambézia</option>
                                    <option value="Nampula" {{ old('province') == 'Nampula' ? 'selected' : '' }}>Nampula</option>
                                    <option value="Cabo Delgado" {{ old('province') == 'Cabo Delgado' ? 'selected' : '' }}>Cabo Delgado</option>
                                    <option value="Niassa" {{ old('province') == 'Niassa' ? 'selected' : '' }}>Niassa</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">Distrito / Bairro</label>
                                <input type="text" name="district" value="{{ old('district') }}" class="input-field" placeholder="Ex.: KaMpfumo / Central">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Nível de Ensino</label>
                                <select name="education_level" class="input-field">
                                    <option value="primario" {{ old('education_level') == 'primario' ? 'selected' : '' }}>Ensino Primário (1ª - 7ª Classe)</option>
                                    <option value="secundario" {{ old('education_level', 'secundario') == 'secundario' ? 'selected' : '' }}>Ensino Secundário (8ª - 12ª Classe)</option>
                                    <option value="primario_secundario" {{ old('education_level') == 'primario_secundario' ? 'selected' : '' }}>Primário e Secundário</option>
                                    <option value="tecnico" {{ old('education_level') == 'tecnico' ? 'selected' : '' }}>Ensino Técnico Profissional</option>
                                </select>
                            </div>
                            <div>
                                <label class="input-label">N.º Estimado de Alunos</label>
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
                        <button type="button" class="btn-primary" onclick="goToStep(2)">
                            Próximo <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============ STEP 2: Conta do Administrador ============ -->
            <div class="wizard-step" id="step2">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-emerald-900 flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="fas fa-user-shield text-emerald-700 text-sm"></i></div>
                        Conta do Administrador
                    </h3>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Nome do Responsável *</label>
                                <input type="text" name="director_name" value="{{ old('director_name') }}" required class="input-field" placeholder="Ex.: Prof. Filipe Domingos">
                            </div>
                            <div>
                                <label class="input-label">Telefone / WhatsApp *</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required class="input-field" placeholder="+258 84 000 0000">
                            </div>
                        </div>

                        <div>
                            <label class="input-label">E-mail do Administrador (Login) *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="input-field" placeholder="direcao@suaescola.co.mz">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Palavra-passe *</label>
                                <input type="password" name="password" required class="input-field" placeholder="Mínimo de 6 caracteres">
                            </div>
                            <div>
                                <label class="input-label">Confirmar Palavra-passe *</label>
                                <input type="password" name="password_confirmation" required class="input-field" placeholder="Repita a palavra-passe">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" class="btn-secondary" onclick="goToStep(1)">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn-primary" onclick="goToStep(3)">
                            Próximo <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============ STEP 3: Configuração Inicial ============ -->
            <div class="wizard-step" id="step3">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h3 class="text-lg font-bold text-emerald-900 flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="fas fa-cog text-emerald-700 text-sm"></i></div>
                        Configuração Inicial
                    </h3>
                    <p class="text-xs text-gray-500 mb-6">Estas definições podem ser alteradas depois nas Configurações do sistema.</p>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Ano Lectivo Actual</label>
                                <input type="text" name="academic_year" value="{{ old('academic_year', date('Y')) }}" class="input-field" placeholder="{{ date('Y') }}">
                            </div>
                            <div>
                                <label class="input-label">Escala de Avaliação</label>
                                <select name="grading_scale" class="input-field">
                                    <option value="0_20" selected>0 a 20 valores (Moçambique)</option>
                                    <option value="0_10">0 a 10 valores</option>
                                    <option value="A_F">Letras (A - F)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Hora de Atraso — Turno da Manhã</label>
                                <input type="time" name="shift_morning_late" value="{{ old('shift_morning_late', '07:30') }}" class="input-field">
                            </div>
                            <div>
                                <label class="input-label">Hora de Atraso — Turno da Tarde</label>
                                <input type="time" name="shift_afternoon_late" value="{{ old('shift_afternoon_late', '13:00') }}" class="input-field">
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mt-4">
                            <h4 class="text-xs font-bold text-emerald-800 uppercase mb-2"><i class="fas fa-check-circle me-1"></i> Resumo do Registo</h4>
                            <div class="text-xs text-emerald-700 space-y-1" id="summaryContent">
                                <p>• Licença de avaliação gratuita de <strong>15 dias</strong></p>
                                <p>• Sem necessidade de cartão de crédito</p>
                                <p>• Acesso completo a todos os módulos aplicáveis</p>
                                <p>• Login automático após a criação</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" class="btn-secondary" onclick="goToStep(2)">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #1B5E20, #2E7D32); padding: 1rem 2.5rem; font-size: 1rem;">
                            <i class="fas fa-rocket text-amber-300"></i> Criar Escola & Iniciar Teste Grátis
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Trust Badges -->
        <div class="mt-8 text-center">
            <div class="flex flex-wrap justify-center gap-6 text-xs text-gray-500">
                <span><i class="fas fa-lock text-emerald-600 me-1"></i> Dados encriptados</span>
                <span><i class="fas fa-server text-emerald-600 me-1"></i> Servidor em Moçambique</span>
                <span><i class="fas fa-headset text-emerald-600 me-1"></i> Suporte técnico incluído</span>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-400 py-6 border-t border-gray-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs">
            &copy; {{ date('Y') }} SIGE — Sistema Integrado de Gestão Escolar. Todos os direitos reservados.
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
            // Basic validation before advancing
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

            // Hide all steps
            document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');

            // Update indicators
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
