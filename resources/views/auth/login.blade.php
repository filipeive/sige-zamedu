<x-guest-layout>
    <style>
        :root {
            --green-900: #064E3B;
            --green-800: #047857;
            --green-700: #059669;
            --green-600: #10B981;
            --green-100: #D1FAE5;
            --green-50: #ECFDF5;
            --yellow-500: #F59E0B;
            --yellow-400: #FBBF24;
            --gray-900: #0F172A;
            --gray-800: #1E293B;
            --gray-700: #334155;
            --gray-500: #64748B;
            --gray-200: #E2E8F0;
            --gray-100: #F1F5F9;
            --gray-50: #F8FAFC;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            min-height: 100vh;
        }

        .split-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ===== LEFT VISUAL BANNER ===== */
        .banner-side {
            flex: 1;
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.93), rgba(4, 120, 87, 0.88)),
                        url('https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
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
            gap: 16px;
        }

        .banner-brand-icon {
            width: 52px;
            height: 52px;
            background: var(--white);
            color: var(--green-900);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }

        .banner-brand-text h1 {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.1;
            color: var(--white);
            letter-spacing: 0.5px;
        }

        .banner-brand-text span {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .banner-center-content {
            max-width: 500px;
        }

        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            backdrop-filter: blur(4px);
            color: #FDE68A;
        }

        .banner-headline {
            font-size: 34px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 18px;
            color: var(--white);
        }

        .banner-headline .highlight {
            color: #FBBF24;
        }

        .banner-description {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 28px;
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
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
        }

        .banner-feature-item i {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: #FBBF24;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .banner-footer {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
        }

        .banner-footer a {
            color: #FBBF24;
            font-weight: 700;
            text-decoration: underline;
        }

        /* ===== RIGHT FORM SIDE ===== */
        .form-side {
            width: 520px;
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            position: relative;
        }

        .form-top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .mobile-brand {
            display: none;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .mobile-brand-icon {
            width: 38px;
            height: 38px;
            background: var(--green-800);
            color: var(--white);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .mobile-brand-title {
            font-size: 16px;
            font-weight: 800;
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
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-left: auto;
        }

        .btn-back-home:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }

        .form-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }

        .form-header {
            margin-bottom: 28px;
            text-align: center;
        }

        .form-header-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--green-800);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 16px auto;
            box-shadow: 0 4px 15px rgba(4, 120, 87, 0.25);
        }

        .form-header h2 {
            font-size: 24px;
            font-weight: 900;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .form-header p {
            font-size: 13px;
            color: var(--gray-500);
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            font-weight: 600;
        }

        .form-input:focus {
            background: var(--white);
            border-color: var(--green-700);
            box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.15);
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
            margin-bottom: 22px;
            font-size: 13px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-700);
            cursor: pointer;
            font-weight: 600;
        }

        .remember-checkbox {
            accent-color: var(--green-800);
            width: 16px;
            height: 16px;
        }

        .forgot-link {
            color: var(--green-800);
            font-weight: 700;
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
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(4, 120, 87, 0.25);
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-danger-custom {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
        }

        .alert-success-custom {
            background: var(--green-50);
            border: 1px solid var(--green-100);
            color: var(--green-900);
        }

        .form-footer {
            text-align: center;
            font-size: 11px;
            color: var(--gray-500);
            margin-top: 20px;
            font-weight: 500;
        }

        .form-footer a {
            color: var(--green-800);
            font-weight: 700;
            text-decoration: none;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .banner-side {
                display: none;
            }
            .form-side {
                width: 100%;
                min-height: 100vh;
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
                <div class="banner-brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="banner-brand-text">
                    <h1>ZAMEDU - SIGE</h1>
                    <span>Sistema Integrado de Gestão Escolar</span>
                </div>
            </div>

            <!-- Center Content -->
            <div class="banner-center-content">
                <div class="banner-badge">
                    <i class="fas fa-landmark"></i>
                    <span>REPÚBLICA DE MOÇAMBIQUE · MINEDH</span>
                </div>

                <h2 class="banner-headline">
                    Gestão Escolar com <span class="highlight">Eficiência, Transparência e Inovação</span>
                </h2>

                <p class="banner-description">
                    Plataforma oficial para gestão académica de escolas públicas e privadas. Otimizada para eliminação de filas na secretaria e conformidade total com o Sistema Nacional de Educação.
                </p>

                <div class="banner-features">
                    <div class="banner-feature-item">
                        <i class="fas fa-check"></i>
                        <span>Onboarding para Escola Pública & Privada</span>
                    </div>
                    <div class="banner-feature-item">
                        <i class="fas fa-check"></i>
                        <span>Pré-Inscrição Inteligente (Fim das Filas)</span>
                    </div>
                    <div class="banner-feature-item">
                        <i class="fas fa-check"></i>
                        <span>Pautas, Boletins e Certificados Oficiais MINEDH</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Login Form Side -->
        <div class="form-side">

            <div class="form-top-nav">
                <!-- Mobile Only Brand -->
                <a href="{{ route('welcome') }}" class="mobile-brand">
                    <div class="mobile-brand-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="mobile-brand-title">ZAMEDU - SIGE</span>
                </a>

                <a href="{{ route('welcome') }}" class="btn-back-home">
                    <i class="fas fa-arrow-left"></i>
                    <span>Início</span>
                </a>
            </div>

            <div class="form-container">

                <div class="form-header">
                    <div class="form-header-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2>Portal de Acesso</h2>
                    <p>Autenticação do Sistema Integrado de Gestão Escolar</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert-custom alert-success-custom">
                        <i class="fas fa-check-circle" style="margin-top: 2px;"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert-custom alert-danger-custom">
                        <i class="fas fa-exclamation-circle" style="margin-top: 2px;"></i>
                        <div>
                            @if ($errors->count() === 1)
                                {{ $errors->first() }}
                            @else
                                <ul style="margin: 0; padding-left: 16px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
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
                                placeholder="utilizador@escola.gov.mz">
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
                                name="remember" 
                                {{ old('remember') ? 'checked' : '' }}>
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

            </div>

            <div class="form-footer">
                &copy; {{ date('Y') }} <strong>ZAMEDU - SIGE</strong> · <a href="http://146.235.224.99/" target="_blank">Filipe dos Santos</a>
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
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>A autenticar...</span>';
            }
        });
    </script>
</x-guest-layout>
