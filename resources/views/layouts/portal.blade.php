<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ZAMEDU - SIGE — Portal Público MINEDH')</title>

    <!-- Favicon & Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <meta name="theme-color" content="#007A3D">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        :root {
            --green-950: #02381B;
            --green-900: #045F30;
            --green-800: #007A3D;
            --green-700: #059669;
            --green-600: #10B981;
            --green-100: #D1FAE5;
            --green-50: #F0FDF4;
            --yellow-500: #FCD116;
            --yellow-400: #FDE047;
            --red-600: #CE1126;
            --gray-900: #0F172A;
            --gray-800: #1E293B;
            --gray-700: #334155;
            --gray-600: #475569;
            --gray-500: #64748B;
            --gray-200: #E2E8F0;
            --gray-100: #F1F5F9;
            --gray-50: #F8FAFC;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--gray-900);
            background-color: #F8FAFC;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, .heading {
            font-family: 'Poppins', sans-serif;
        }

        /* National Flag Stripe */
        .moz-stripe-top {
            display: flex;
            height: 5px;
            width: 100%;
        }
        .moz-stripe-top .s-green { flex: 4; background-color: var(--green-800); }
        .moz-stripe-top .s-black { flex: 3; background-color: #000000; }
        .moz-stripe-top .s-yellow { flex: 3; background-color: var(--yellow-500); }
        .moz-stripe-top .s-red { flex: 2; background-color: var(--red-600); }
        .moz-stripe-top .s-white { flex: 1; background-color: var(--white); }

        .portal-navbar {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .portal-nav-container {
            max-width: 1480px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
            gap: 16px;
            flex-wrap: nowrap;
        }

        .portal-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .portal-links {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .portal-link-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--gray-700);
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .portal-link-item:hover, .portal-link-item.active {
            color: var(--green-800);
            background: var(--green-50);
        }

        .portal-link-item.active {
            font-weight: 700;
            border-bottom: 2px solid var(--green-800);
            border-radius: 8px 8px 0 0;
        }

        .portal-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .portal-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--green-800);
            color: var(--white);
            border: 1.5px solid var(--green-800);
            border-radius: 9px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(0, 122, 61, 0.2);
            white-space: nowrap;
        }

        .portal-btn-primary:hover {
            background: var(--green-900);
            color: var(--white);
        }

        .portal-btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: var(--green-50);
            color: var(--green-800);
            border: 1.5px solid var(--green-800);
            border-radius: 9px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .portal-btn-outline:hover {
            background: var(--green-100);
            color: var(--green-900);
        }

        .portal-main {
            flex-grow: 1;
        }

        .portal-footer {
            background: #0F172A;
            color: #94A3B8;
            padding: 30px 0 20px 0;
            margin-top: 60px;
            border-top: 4px solid var(--green-800);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Faixa Oficial da Bandeira de Moçambique -->
    <div class="moz-stripe-top">
        <div class="s-green"></div>
        <div class="s-black"></div>
        <div class="s-yellow"></div>
        <div class="s-red"></div>
        <div class="s-white"></div>
    </div>

    <!-- Barra de Navegação Pública -->
    <header class="portal-navbar">
        <div class="portal-nav-container">
            <!-- Identidade Visual Institucional -->
            <a href="{{ route('welcome') }}" class="portal-brand">
                <img src="{{ asset('images/emblem.png') }}" alt="República de Moçambique" style="height: 44px; width: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));">
                <img src="{{ asset('images/logo.png') }}" alt="ZamEdu" style="height: 34px; width: auto;">
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 9.5px; font-weight: 800; color: #007A3D; text-transform: uppercase; letter-spacing: 0.5px;">REPÚBLICA DE MOÇAMBIQUE · MINEDH</span>
                    <span style="font-size: 17.5px; font-weight: 900; color: #0F172A; line-height: 1.15; letter-spacing: -0.3px;">ZAMEDU - SIGE</span>
                    <span style="font-size: 10.5px; font-weight: 600; color: #64748B;">Sistema Integrado de Gestão Escolar</span>
                </div>
            </a>

            <!-- Menu de Links Públicos -->
            <nav class="portal-links">
                <a href="{{ route('welcome') }}" class="portal-link-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
                    <i class="fas fa-home text-emerald-700"></i> Início
                </a>

                <a href="{{ route('public.allocations') }}" class="portal-link-item {{ request()->routeIs('public.allocations*') ? 'active' : '' }}">
                    <i class="fas fa-search-location text-emerald-700"></i> Afetações 2027
                </a>

                <a href="{{ route('public.vacancies') }}" class="portal-link-item {{ request()->routeIs('public.vacancies*') ? 'active' : '' }}">
                    <i class="fas fa-chair text-emerald-700"></i> Vagas 2027
                </a>

                <a href="{{ route('public.pre-enrollment') }}" class="portal-link-item {{ request()->routeIs('public.pre-enrollment*') ? 'active' : '' }}">
                    <i class="fas fa-file-signature text-emerald-700"></i> Pré-Inscrição 2027
                </a>
            </nav>

            <!-- Ações Direitas -->
            <div class="portal-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="portal-btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Aceder ao Painel
                    </a>
                @else
                    <a href="{{ route('public.pre-enrollment') }}" class="portal-btn-outline">
                        <i class="fas fa-user-plus"></i> Pré-Inscrição 2027
                    </a>
                    <a href="{{ route('login') }}" class="portal-btn-primary">
                        <i class="fas fa-right-to-bracket"></i> Aceder ao Sistema
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="portal-main">
        @yield('content')
    </main>

    <!-- Rodapé Governamental Oficial -->
    <footer class="portal-footer">
        <div style="max-width: 1480px; margin: 0 auto; padding: 0 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; padding-bottom: 20px; border-bottom: 1px solid #1E293B;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ asset('images/emblem.png') }}" alt="Emblema Oficial" style="height: 38px; width: auto;">
                    <div>
                        <div style="font-weight: 800; color: white; font-size: 14px;">REPÚBLICA DE MOÇAMBIQUE</div>
                        <div style="font-size: 12px; color: #94A3B8;">Ministério da Educação e Desenvolvimento Humano (MINEDH) &bull; Lei 18/2018 (SNE)</div>
                    </div>
                </div>

                <div style="display: flex; gap: 20px; font-size: 12.5px;">
                    <a href="{{ route('welcome') }}" style="color: #94A3B8; text-decoration: none;" class="hover:text-white">Início</a>
                    <a href="{{ route('public.allocations') }}" style="color: #94A3B8; text-decoration: none;" class="hover:text-white">Afetações 2027</a>
                    <a href="{{ route('public.vacancies') }}" style="color: #94A3B8; text-decoration: none;" class="hover:text-white">Vagas Escolares 2027</a>
                    <a href="{{ route('public.pre-enrollment') }}" style="color: #94A3B8; text-decoration: none;" class="hover:text-white">Pré-Inscrições Online</a>
                    <a href="{{ route('login') }}" style="color: #10B981; font-weight: 700; text-decoration: none;">Portal de Gestão</a>
                </div>
            </div>

            <div style="text-align: center; padding-top: 16px; font-size: 12px; color: #64748B;">
                &copy; {{ date('Y') }} <strong>ZAMEDU - SIGE</strong> &bull; Desenvolvido por <a href="http://146.235.224.99/" target="_blank" style="color: #10B981; font-weight: 700; text-decoration: none;">Filipe dos Santos</a>
            </div>
        </div>
    </footer>

    {{-- Widget de IA Atendimento Público --}}
    @include('partials.ai-widget-public')

    @stack('scripts')
</body>
</html>

