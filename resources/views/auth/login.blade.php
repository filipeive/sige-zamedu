<x-guest-layout>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--mz-bg);
            color: var(--mz-navy);
            min-height: 100vh;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Poppins', sans-serif;
        }

        .split-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ===== LEFT VISUAL BANNER ===== */
        .banner-side {
            flex: 1.1;
            background: linear-gradient(145deg, rgba(3, 69, 34, 0.96) 0%, rgba(0, 122, 61, 0.92) 100%),
                        url('https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 56px;
            color: #FFFFFF;
            position: relative;
            box-shadow: 4px 0 25px rgba(0,0,0,0.15);
        }

        .banner-top {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .emblem-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 12px;
            border-radius: 16px;
            border: 2px solid var(--mz-gold);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .emblem-img {
            height: 54px;
            width: auto;
            object-fit: contain;
        }

        .banner-title-box h1 {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.1;
            color: #FFFFFF;
            letter-spacing: 0.5px;
        }

        .banner-title-box span {
            font-size: 11px;
            color: var(--mz-gold);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .banner-center {
            max-width: 540px;
            margin: 40px 0;
        }

        .gov-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(252, 209, 22, 0.15);
            border: 1px solid rgba(252, 209, 22, 0.4);
            border-radius: 30px;
            font-size: 11px;
            font-weight: 800;
            color: var(--mz-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            backdrop-filter: blur(8px);
        }

        .hero-headline {
            font-size: 34px;
            font-weight: 900;
            line-height: 1.25;
            margin-bottom: 18px;
            color: #FFFFFF;
        }

        .hero-headline .gold-text {
            color: var(--mz-gold);
        }

        .hero-subtext {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Glassmorphism Feature Cards */
        .feature-cards-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .glass-card-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            backdrop-filter: blur(10px);
            transition: all 0.25s ease;
        }

        .glass-card-item:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: var(--mz-gold);
            transform: translateX(4px);
        }

        .glass-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--mz-gold);
            color: var(--mz-green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            font-weight: 800;
        }

        .glass-card-text h4 {
            font-size: 13px;
            font-weight: 800;
            color: #FFFFFF;
            line-height: 1.2;
        }

        .glass-card-text p {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 2px;
        }

        .banner-footer-text {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
        }

        .banner-footer-text a {
            color: var(--mz-gold);
            font-weight: 800;
            text-decoration: underline;
        }

        /* ===== RIGHT LOGIN FORM ===== */
        .form-side {
            width: 540px;
            background: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px 48px;
            position: relative;
        }

        .form-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #F1F5F9;
            color: var(--mz-slate);
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-back-home:hover {
            background: var(--mz-green-primary);
            color: #FFFFFF;
        }

        .form-header-center {
            text-align: center;
            margin-bottom: 30px;
        }

        .emblem-seal-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 14px auto;
            padding: 8px;
            background: #F0FDF4;
            border: 2px dashed var(--mz-green-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 122, 61, 0.12);
        }

        .emblem-seal-img {
            height: 56px;
            width: auto;
            object-fit: contain;
        }

        .form-header-center h2 {
            font-size: 22px;
            font-weight: 900;
            color: var(--mz-navy);
            margin-bottom: 4px;
        }

        .form-header-center p {
            font-size: 12px;
            color: #64748B;
            font-weight: 500;
        }

        .form-field-group {
            margin-bottom: 20px;
        }

        .form-field-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 800;
            color: var(--mz-slate);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-field-label i {
            color: var(--mz-green-primary);
        }

        .input-relative {
            position: relative;
        }

        .custom-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: var(--mz-navy);
            background: #F8FAFC;
            transition: all 0.2s ease;
            outline: none;
        }

        .custom-input:focus {
            background: #FFFFFF;
            border-color: var(--mz-green-primary);
            box-shadow: 0 0 0 4px rgba(0, 122, 61, 0.12);
        }

        .input-left-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 14px;
        }

        .btn-toggle-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94A3B8;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-toggle-eye:hover { color: var(--mz-navy); }

        .form-options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 12px;
        }

        .remember-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--mz-slate);
            cursor: pointer;
        }

        .remember-checkbox-label input {
            accent-color: var(--mz-green-primary);
            width: 16px;
            height: 16px;
        }

        .link-forgot {
            color: var(--mz-green-primary);
            font-weight: 800;
            text-decoration: none;
        }

        .link-forgot:hover {
            text-decoration: underline;
        }

        .btn-submit-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--mz-green-primary) 0%, var(--mz-green-dark) 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 122, 61, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 122, 61, 0.4);
        }

        /* Demo Accounts Box */
        .demo-accounts-card {
            margin-top: 24px;
            padding: 16px;
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 14px;
        }

        .demo-accounts-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--mz-green-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-user-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            color: var(--mz-slate);
            margin: 2px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .demo-user-badge:hover {
            border-color: var(--mz-green-primary);
            color: var(--mz-green-primary);
        }

        .footer-copyright {
            text-align: center;
            font-size: 11px;
            color: #94A3B8;
            margin-top: 16px;
        }

        .footer-copyright a {
            color: var(--mz-green-primary);
            font-weight: 800;
            text-decoration: none;
        }

        @media (max-width: 992px) {
            .banner-side { display: none; }
            .form-side { width: 100%; min-height: 100vh; padding: 32px 24px; }
        }
    </style>

    <div class="split-layout">
        <!-- LEFT VISUAL BANNER -->
        <div class="banner-side">
            <!-- Top Branding Header -->
            <div class="banner-top">
                <div class="emblem-box">
                    <img src="{{ asset('images/emblem.png') }}" alt="Emblema da República de Moçambique" class="emblem-img">
                </div>
                <div class="banner-title-box">
                    <span>REPÚBLICA DE MOÇAMBIQUE · MINEDH</span>
                    <h1>ZAMEDU - SIGE</h1>
                </div>
            </div>

            <!-- Center Content -->
            <div class="banner-center">
                <div class="gov-pill-badge">
                    <i class="fas fa-shield-halved"></i>
                    <span>Plataforma Oficial do Sistema Nacional de Educação</span>
                </div>

                <h2 class="hero-headline font-heading">
                    Gestão Escolar com <span class="gold-text">Eficiência, Transparência & Inovação</span>
                </h2>

                <p class="hero-subtext">
                    Plataforma integrada de Gestão Escolar e Inteligência Educacional para escolas públicas, privadas e comunitárias de Moçambique, em conformidade integral com a Lei 18/2018 do SNE.
                </p>

                <div class="feature-cards-grid">
                    <div class="glass-card-item">
                        <div class="glass-card-icon"><i class="fas fa-landmark"></i></div>
                        <div class="glass-card-text">
                            <h4>Onboarding Autónomo para Escola Pública & Privada</h4>
                            <p>Regimes flexíveis com isenção ou cobrança de propinas e relatórios estatísticos.</p>
                        </div>
                    </div>

                    <div class="glass-card-item">
                        <div class="glass-card-icon"><i class="fas fa-users-slash"></i></div>
                        <div class="glass-card-text">
                            <h4>Pré-Inscrição Inteligente por Região (Fim das Filas)</h4>
                            <p>Submissão remota de candidatos com alocação territorial de vagas.</p>
                        </div>
                    </div>

                    <div class="glass-card-item">
                        <div class="glass-card-icon"><i class="fas fa-award"></i></div>
                        <div class="glass-card-text">
                            <h4>Pautas, Boletins e Certificados Oficiais MINEDH</h4>
                            <p>Motor de avaliação SNE com desagregação por género (HM/M) e Emblema da República.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Text -->
            <div class="banner-footer-text">
                &copy; {{ date('Y') }} <strong>ZAMEDU - SIGE</strong> · Desenvolvido por <a href="http://146.235.224.99/" target="_blank">Filipe dos Santos</a>
            </div>
        </div>

        <!-- RIGHT LOGIN FORM -->
        <div class="form-side">
            <div>
                <div class="form-top-bar">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('images/emblem.png') }}" alt="Emblema Oficial" style="height: 32px; width: auto;">
                        <span class="fw-bold text-success text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">MINEDH · SIGE</span>
                    </div>
                    <a href="{{ route('welcome') }}" class="btn-back-home">
                        <i class="fas fa-arrow-left"></i>
                        <span>Voltar ao Início</span>
                    </a>
                </div>

                <div class="form-header-center">
                    <div class="emblem-seal-wrapper">
                        <img src="{{ asset('images/emblem.png') }}" alt="Emblema da República de Moçambique" class="emblem-seal-img">
                    </div>
                    <span class="badge mb-2" style="background: #E6F4EA; color: #007A3D; font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 5px 12px; border-radius: 20px;">PORTAL GOVERNAMENTAL DE ACESSO</span>
                    <h2 class="font-heading">Portal de Acesso</h2>
                    <p>Autenticação do Sistema Integrado de Gestão Escolar</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="p-3 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="p-3 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                        <div class="font-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i>Erro de Autenticação:</div>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="form-field-group">
                        <label for="email" class="form-field-label">
                            <i class="fas fa-envelope"></i> Endereço de E-mail
                        </label>
                        <div class="input-relative">
                            <i class="fas fa-at input-left-icon"></i>
                            <input id="email" class="custom-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="utilizador@escola.gov.mz">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-field-group">
                        <label for="password" class="form-field-label">
                            <i class="fas fa-key"></i> Palavra-Passe
                        </label>
                        <div class="input-relative">
                            <i class="fas fa-lock input-left-icon"></i>
                            <input id="password" class="custom-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                            <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="form-options-row">
                        <label for="remember_me" class="remember-checkbox-label">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Lembrar-me neste dispositivo</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="link-forgot" href="{{ route('password.request') }}">
                                Esqueceu a senha?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-submit-login">
                        <i class="fas fa-right-to-bracket"></i>
                        <span>ENTRAR NO SISTEMA</span>
                    </button>
                </form>

                <!-- Quick Demo Role Accounts for Testing -->
                <div class="demo-accounts-card">
                    <div class="demo-accounts-title">
                        <i class="fas fa-user-shield text-emerald-700"></i> Contas de Demonstração (Clique para Preencher):
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="demo-user-badge" onclick="fillDemo('superadmin@visionarios.co.mz', 'password')">👑 Super Admin</span>
                        <span class="demo-user-badge" onclick="fillDemo('inspector.provincial@minedh.gov.mz', 'password')">🏛️ Inspector Provincial</span>
                        <span class="demo-user-badge" onclick="fillDemo('diretor.escola@minedh.gov.mz', 'password')">🏫 Director de Escola</span>
                        <span class="demo-user-badge" onclick="fillDemo('professor@visionarios.co.mz', 'password')">👨‍🏫 Professor</span>
                        <span class="demo-user-badge" onclick="fillDemo('secretaria@visionarios.co.mz', 'password')">📑 Secretária</span>
                    </div>
                </div>
            </div>

            <div class="footer-copyright">
                &copy; {{ date('Y') }} <strong>ZAMEDU - SIGE</strong> · Desenvolvido por <a href="http://146.235.224.99/" target="_blank">Filipe dos Santos</a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function fillDemo(email, pwd) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pwd;
        }
    </script>
</x-guest-layout>
