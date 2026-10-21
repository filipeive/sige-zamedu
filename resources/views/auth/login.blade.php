<x-guest-layout>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --green-900: #1B5E20;
            --green-800: #2E7D32;
            --green-700: #388E3C;
            --green-600: #43A047;
            --green-100: #E8F5E9;
            --green-50: #F1F8E9;
            --yellow-500: #FFB300;
            --yellow-400: #FFC107;
            --gray-900: #212121;
            --gray-800: #424242;
            --gray-700: #616161;
            --gray-500: #9E9E9E;
            --gray-200: #EEEEEE;
            --gray-100: #F5F5F5;
            --gray-50: #FAFAFA;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--white);
            color: var(--gray-900);
            min-height: 100vh;
        }

        body.bg-slate-950 {
            background-color: var(--white);
        }

        .split-layout {
            display: flex;
            width: min(100%, 960px);
            min-height: 100vh;
            margin: 0 auto;
            overflow: hidden;
        }

        /* ===== LEFT VISUAL BANNER ===== */
        .banner-side {
            flex: 1;
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.92), rgba(46, 125, 50, 0.85)),
                        url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 50px 60px;
            color: var(--white);
            position: relative;
        }

        .banner-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .banner-brand-icon {
            width: 52px;
            height: 52px;
            background: var(--white);
            color: var(--green-900);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .banner-brand-text h1 {
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
            color: var(--white);
        }

        .banner-brand-text span {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 400;
        }

        .banner-center-content {
            max-width: 480px;
        }

        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            backdrop-filter: blur(4px);
        }

        .banner-badge i {
            color: var(--yellow-400);
        }

        .banner-headline {
            font-size: 36px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 18px;
            color: var(--white);
        }

        .banner-headline .highlight {
            color: var(--yellow-400);
        }

        .banner-description {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .banner-features {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .banner-feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
        }

        .banner-feature-item i {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: var(--yellow-400);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .banner-footer {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .banner-footer strong {
            color: var(--white);
        }

        /* ===== RIGHT FORM SIDE ===== */
        .form-side {
            width: 46%;
            max-width: 530px;
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 30px 45px;
            position: relative;
        }

        .form-top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .mobile-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--green-800);
            color: var(--white);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .mobile-brand-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--gray-100);
            color: var(--gray-700);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-left: auto;
        }

        .btn-back-home:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }

        .form-container {
            max-width: 410px;
            width: 100%;
            margin: auto auto;
        }

        .form-header {
            margin-bottom: 22px;
            text-align: center;
        }

        .auth-emblem {
            height: 52px;
            width: auto;
            display: inline-block;
            margin-bottom: 10px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.08));
        }

        .form-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .form-header p {
            font-size: 13px;
            color: var(--gray-700);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 6px;
            text-align: left;
        }

        .form-label i {
            color: var(--green-800);
            margin-right: 6px;
        }

        .input-group-custom {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 12px 42px 12px 42px;
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            font-size: 14px;
            color: var(--gray-900);
            background: var(--gray-50);
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            background: var(--white);
            border-color: var(--green-600);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.15);
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 15px;
        }

        .btn-toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            font-size: 14px;
            padding: 4px;
        }

        .btn-toggle-password:hover {
            color: var(--gray-800);
        }

        .form-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-700);
            cursor: pointer;
        }

        .remember-checkbox {
            accent-color: var(--green-800);
            width: 16px;
            height: 16px;
        }

        .forgot-link {
            color: var(--green-800);
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: var(--green-900);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--green-800);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.25);
        }

        .btn-submit:hover {
            background: var(--green-900);
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .alert-custom {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            text-align: left;
        }

        .alert-danger-custom {
            background: #FFEBEE;
            border: 1px solid #FFCDD2;
            color: #C62828;
        }

        .alert-success-custom {
            background: var(--green-50);
            border: 1px solid var(--green-100);
            color: var(--green-900);
        }

        /* ===== ATALHOS DE ACESSO ===== */
        .demo-shortcuts-box {
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px dashed #E2E8F0;
        }

        .demo-shortcuts-box summary {
            cursor: pointer;
            list-style-position: inside;
        }

        .demo-shortcuts-title {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-shortcuts-box[open] .demo-shortcuts-title {
            margin-bottom: 14px;
        }

        .demo-badges-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .demo-role-group + .demo-role-group {
            margin-top: 14px;
        }

        .demo-role-heading {
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 12px;
            font-weight: 700;
        }

        .demo-pill-btn {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 7px 9px;
            border-radius: 6px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .demo-pill-btn:hover {
            background: #FFFFFF;
            border-color: #059669;
            color: #065F46;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
        }

        .form-footer {
            text-align: center;
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 16px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .split-layout {
                width: 100%;
                min-height: 100vh;
                margin: 0;
            }
            .banner-side {
                display: none;
            }
            .form-side {
                width: 100%;
                min-height: 100vh;
                padding: 24px;
            }
            .mobile-brand {
                display: flex;
            }
        }
    </style>

    <div class="split-layout">

        <!-- Left Visual Side -->
        <div class="banner-side">
            <!-- Brand Top Header -->
            <div class="banner-brand">
                <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" style="height: 60px; width: auto; background: #ffffff; padding: 4px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.18);">
                <div class="banner-brand-text">
                    <h1>Escola dos Visionários</h1>
                    <span>Aqui se Prepara a Nova Geração</span>
                </div>
            </div>

            <!-- Center Content -->
            <div class="banner-center-content">
                <div class="banner-badge">
                    <i class="fas fa-sun text-yellow-400"></i>
                    <span>Inscrições Abertas 2027</span>
                </div>

                <h2 class="banner-headline">
                    Excelência pedagógica e <span class="highlight">gestão inteligente.</span>
                </h2>

                <p class="banner-description">
                    Plataforma oficial da Escola dos Visionários para as filiais de Quelimane e Alto Molócuè. Gestão unificada para direcção, tesouraria, professores e encarregados de educação.
                </p>

                <div class="banner-features">
                    <div class="banner-feature-item">
                        <i class="fas fa-check"></i>
                        <span>Controlo de propinas, multas diárias e M-Pesa / eMola</span>
                    </div>
                    <div class="banner-feature-item">
                        <i class="fas fa-check"></i>
                        <span>Pautas oficiais e boletins de notas (SNE - Lei 18/2018)</span>
                    </div>
                    <div class="banner-feature-item">
                        <i class="fas fa-check"></i>
                        <span>Portal do encarregado com acompanhamento em tempo real</span>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="banner-footer">
                &copy; {{ date('Y') }} <strong>Escola dos Visionários</strong> · Quelimane & Alto Molócuè.
            </div>
        </div>

        <!-- Right Login Form Side -->
        <div class="form-side">

            <div class="form-top-nav">
                <!-- Mobile Only Brand -->
                <a href="{{ route('welcome') }}" class="mobile-brand">
                    <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" style="height: 36px; width: auto; margin-right: 8px;">
                    <span class="mobile-brand-title">Escola dos Visionários</span>
                </a>

                <a href="{{ route('welcome') }}" class="btn-back-home">
                    <i class="fas fa-arrow-left"></i>
                    <span>Voltar ao Início</span>
                </a>
            </div>

            <div class="form-container">

                <div class="form-header">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" style="height: 72px; width: auto; object-fit: contain; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));">
                    </div>
                    <h2>Portal de Acesso</h2>
                    <p><strong>Escola dos Visionários</strong> · Aqui se Prepara a Nova Geração</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert-custom alert-success-custom">
                        <i class="fas fa-check-circle"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert-custom alert-danger-custom">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Endereço de Email
                        </label>
                        <div class="input-group-custom">
                            <i class="fas fa-at input-icon-left"></i>
                            <input 
                                id="email" 
                                type="email" 
                                class="form-input" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="email" 
                                placeholder="seu.email@escola.co.mz">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Palavra-passe
                        </label>
                        <div class="input-group-custom">
                            <i class="fas fa-key input-icon-left"></i>
                            <input 
                                id="password" 
                                type="password" 
                                class="form-input" 
                                name="password" 
                                required 
                                autocomplete="current-password" 
                                placeholder="••••••••">
                            <button type="button" class="btn-toggle-password" id="togglePasswordBtn" title="Mostrar/ocultar senha">
                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="form-flex">
                        <label class="remember-label">
                            <input 
                                type="checkbox" 
                                class="remember-checkbox" 
                                id="remember" 
                                name="remember">
                            Lembrar-me
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                Esqueceu a senha?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="loginBtn">
                        <i class="fas fa-right-to-bracket"></i>
                        <span>Entrar no Sistema</span>
                    </button>
                </form>

                <!-- Atalhos de Acessos Rápidos (Flexibilidade de Demonstração e Testes) -->
                <details open class="demo-shortcuts-box">
                    <summary class="demo-shortcuts-title">
                        <i class="fas fa-users text-amber-500"></i> Perfis de Demonstração (1-Clique)
                    </summary>

                    <div class="demo-role-group">
                        <h3 class="demo-role-heading">Escola dos Visionários (Quelimane & Alto Molócuè)</h3>
                        <div class="demo-badges-grid">
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('superadmin@visionarios.co.mz', 'superadmin123')"><i class="fas fa-crown text-amber-500"></i> Dono / Mantenedor</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('admin@visionarios.co.mz', 'admin123')"><i class="fas fa-school text-emerald-700"></i> Director Geral</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('secretaria@visionarios.co.mz', 'secretaria123')"><i class="fas fa-coins text-amber-600"></i> Tesouraria / Propinas</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('pedagogia@visionarios.co.mz', 'pedagogia123')"><i class="fas fa-book-open-reader text-emerald-600"></i> Pedagógico</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('professor@visionarios.co.mz', 'professor123')"><i class="fas fa-chalkboard-user text-blue-600"></i> Professor</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('pai@visionarios.co.mz', 'pai123')"><i class="fas fa-users text-teal-600"></i> Encarregado</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('aluno@visionarios.co.mz', 'aluno123')"><i class="fas fa-user-graduate text-cyan-600"></i> Aluno</button>
                        </div>
                    </div>

                    <div class="demo-role-group" style="margin-top: 10px; opacity: 0.85;">
                        <h3 class="demo-role-heading">Supervisão Territorial (MINEDH)</h3>
                        <div class="demo-badges-grid">
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('inspector.zambezia@minedh.gov.mz', 'provincia123')"><i class="fas fa-building-columns text-sky-600"></i> Insp. Provincial</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('director.namacurra@minedh.gov.mz', 'distrito123')"><i class="fas fa-map-location-dot text-indigo-600"></i> Dir. Distrital</button>
                            <button type="button" class="demo-pill-btn" onclick="fillDemo('zip.namacurra@minedh.gov.mz', 'zip123')"><i class="fas fa-sitemap text-teal-600"></i> Coord. ZIP</button>
                        </div>
                    </div>
                </details>

            </div>

            <div class="form-footer">
                &copy; {{ date('Y') }} Escola dos Visionários — Quelimane & Alto Molócuè, Zambézia
            </div>

        </div>

    </div>

    <script>
        // Password toggle
        document.getElementById('togglePasswordBtn')?.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput && icon) {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            }
        });

        // Form submission loading indicator
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>A entrar...</span>';
            }
        });

        // Helper for quick demo fill
        function fillDemo(email, pwd) {
            const emailInput = document.getElementById('email');
            const pwdInput = document.getElementById('password');
            if (emailInput && pwdInput) {
                emailInput.value = email;
                pwdInput.value = pwd;
            }
        }
    </script>
</x-guest-layout>
