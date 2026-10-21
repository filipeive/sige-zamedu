<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Escola dos Visionários — Quelimane & Alto Molócuè · ZAMEDU - SIGE</title>

    <!-- Favicon Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/emblem.png') }}">
    <meta name="theme-color" content="#007A3D">
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
            --gray-500: #64748B;
            --gray-200: #E2E8F0;
            --gray-100: #F1F5F9;
            --gray-50: #F8FAFC;
            --white: #FFFFFF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--gray-900);
            background: var(--gray-50);
            line-height: 1.6;
        }

        h1, h2, h3, h4, .heading {
            font-family: 'Poppins', sans-serif;
        }

        a { text-decoration: none; color: inherit; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ============ TOP BAR ============ */
        .top-bar {
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 14px 0;
        }

        .top-bar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo-icon {
            width: 46px;
            height: 46px;
            background: var(--green-800);
            color: var(--white);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.25);
            flex-shrink: 0;
        }

        .brand-text h1 {
            font-size: 20px;
            font-weight: 900;
            color: var(--gray-900);
            line-height: 1.1;
            letter-spacing: 0.5px;
        }

        .brand-text span {
            font-size: 11px;
            color: var(--green-800);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-actions .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: var(--green-800);
            color: var(--white);
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.25);
        }

        .top-actions .btn-login:hover {
            background: var(--green-900);
        }

        .top-actions .btn-pre-enrollment {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--green-50);
            color: var(--green-800);
            border: 2px solid var(--green-800);
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
        }

        .top-actions .btn-pre-enrollment:hover {
            background: var(--green-100);
        }

        /* ============ ACCENT BAR ============ */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #D32F2F, #FBC02D, var(--green-800));
        }

        /* ============ HERO SECTION ============ */
        .hero-section {
            background: linear-gradient(135deg, var(--green-900) 0%, var(--green-800) 100%);
            color: var(--white);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: var(--yellow-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 42px;
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 20px;
            color: var(--white);
        }

        .hero-title span {
            color: var(--yellow-400);
        }

        .hero-desc {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: var(--yellow-500);
            color: var(--gray-900);
            border-radius: 12px;
            font-weight: 800;
            font-size: 15px;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }

        .btn-hero-primary:hover {
            background: var(--yellow-400);
            transform: translateY(-2px);
        }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.2s;
            backdrop-filter: blur(4px);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .hero-card {
            background: var(--white);
            border-radius: 24px;
            padding: 36px;
            color: var(--gray-900);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: 1px solid var(--gray-200);
        }

        .hero-card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--gray-200);
        }

        .hero-card-header-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--green-800);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .hero-card-header h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--gray-900);
        }

        /* ============ ONBOARDING SECTION ============ */
        .section-title {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 50px auto;
        }

        .section-title span {
            font-size: 12px;
            font-weight: 800;
            color: var(--green-800);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 8px;
        }

        .section-title h2 {
            font-size: 32px;
            font-weight: 900;
            color: var(--gray-900);
        }

        .onboarding-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 70px;
        }

        .onboarding-card {
            background: var(--white);
            border-radius: 20px;
            padding: 36px;
            border: 2px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
        }

        .onboarding-card:hover {
            border-color: var(--green-800);
            box-shadow: 0 12px 30px rgba(4, 120, 87, 0.12);
            transform: translateY(-4px);
        }

        .onboarding-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .badge-public {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .badge-private {
            background: #D1FAE5;
            color: #065F46;
        }

        .onboarding-card h3 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--gray-900);
        }

        .onboarding-card p {
            font-size: 14px;
            color: var(--gray-700);
            margin-bottom: 20px;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .feature-list li i {
            color: var(--green-700);
        }

        /* ============ PRE-ENROLLMENT MATURE MODULE ============ */
        .pre-enrollment-banner {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            border-radius: 24px;
            padding: 50px;
            color: var(--white);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            margin-bottom: 80px;
            border: 1px solid #334155;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .pre-enrollment-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.4);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            color: #FBBF24;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .pre-enrollment-content h3 {
            font-size: 30px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .pre-enrollment-content p {
            font-size: 14px;
            color: #94A3B8;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        /* ============ FOOTER ============ */
        .footer {
            background: #0F172A;
            color: #94A3B8;
            padding: 60px 0 30px 0;
            border-top: 4px solid var(--green-800);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .footer-brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--green-800);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .footer-brand h4 {
            font-size: 20px;
            font-weight: 900;
            color: var(--white);
        }

        .footer-dev-badge {
            display: inline-block;
            margin-top: 14px;
            padding: 8px 16px;
            background: #1E293B;
            border: 1px solid #334155;
            border-radius: 10px;
            font-size: 12px;
            color: var(--white);
        }

        .footer-dev-badge a {
            color: #FBBF24;
            font-weight: 800;
            text-decoration: underline;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid #1E293B;
            font-size: 12px;
            color: #64748B;
        }

        @media (max-width: 768px) {
            .hero-grid, .onboarding-grid, .pre-enrollment-banner, .footer-grid {
                grid-template-columns: 1fr;
            }
            .hero-title { font-size: 32px; }
            .pre-enrollment-banner { padding: 30px; }
        }
    </style>
</head>

<body>

    <!-- TOP BAR -->
    <header class="top-bar">
        <div class="container">
            <div class="brand d-flex align-items-center gap-3">
                <div class="brand-logo-icon" style="background: linear-gradient(135deg, #047857 0%, #065F46 100%);">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="brand-text">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 d-block" style="font-size: 10px; color: #007A3D; font-weight: 800;">REDE ESCOLAR PRIVADA · REPÚBLICA DE MOÇAMBIQUE · MINEDH · ZAMEDU - SIGE</span>
                    <h1>ESCOLA DOS VISIONÁRIOS</h1>
                    <span>Ensino de Excelência · Quelimane & Alto Molócuè</span>
                </div>
            </div>

            <div class="top-actions">
                <a href="{{ route('public.allocations') }}" class="btn-pre-enrollment">
                    <i class="fas fa-map-location-dot"></i> Afetações 2027
                </a>
                <a href="{{ route('public.vacancies') }}" class="btn-pre-enrollment">
                    <i class="fas fa-chart-pie"></i> Vagas 2027
                </a>
                <a href="{{ route('public.pre-enrollment') }}" class="btn-pre-enrollment">
                    <i class="fas fa-user-plus"></i> Inscrições Online
                </a>
                <a href="{{ route('login') }}" class="btn-login">
                    <i class="fas fa-right-to-bracket"></i> Aceder ao Portal
                </a>
            </div>
        </div>
    </header>

    <div class="accent-bar"></div>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <div class="hero-badge">
                        <i class="fas fa-award text-yellow-400 me-1"></i>
                        ESCOLA DOS VISIONÁRIOS · QUELIMANE & ALTO MOLÓCUÈ
                    </div>
                    <h1 class="hero-title">
                        Educação de Excelência, <span>Rigor e Futuro</span>
                    </h1>
                    <p class="hero-desc">
                        Plataforma oficial da Escola dos Visionários para modernizar a gestão académica, pedagógica e financeira. Acompanhamento em tempo real para pais, pautas oficiais e controlo automatizado de propinas e multas para as filiais de Quelimane e Alto Molócuè.
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <i class="fas fa-right-to-bracket"></i> Entrar no Portal
                        </a>
                        <a href="{{ route('public.pre-enrollment') }}" class="btn-hero-secondary">
                            <i class="fas fa-user-plus"></i> Inscrições Online
                        </a>
                        <a href="{{ route('public.allocations') }}" class="btn-hero-secondary">
                            <i class="fas fa-map-location-dot"></i> Afetações 2027
                        </a>
                        <a href="{{ route('public.vacancies') }}" class="btn-hero-secondary">
                            <i class="fas fa-chart-pie"></i> Vagas 2027
                        </a>
                    </div>

                    <!-- Níveis SNE -->
                    <div class="mt-6 pt-4 border-t border-white/20 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-yellow-400 uppercase tracking-wider">Homologação Oficial (SNE Lei 18/2018):</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">Escola Local</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">ZIP (Micro-Rede)</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">Distrito (SDEJT)</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">Província (DPEDH)</span>
                    </div>
                </div>

                <div class="hero-card">
                    <div class="hero-card-header d-flex align-items-center gap-3">
                        <div class="hero-card-header-icon" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                            <i class="fas fa-school"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">Rede de Filiais Visionários</h3>
                            <span class="text-xs text-slate-500 font-semibold">Província da Zambézia · Gestão Unificada</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-700 text-white flex items-center justify-center font-bold">
                                <i class="fas fa-city"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">Filial Quelimane (Sede)</h4>
                                <p class="text-[11px] text-slate-600">Av. Samora Machel, Bairro Central · Ensino Primário e Secundário.</p>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-700 text-white flex items-center justify-center font-bold">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">Filial Alto Molócuè</h4>
                                <p class="text-[11px] text-slate-600">Av. Eduardo Mondlane · Rigor pedagógico e acompanhamento contínuo.</p>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-700 text-white flex items-center justify-center font-bold">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">Tesouraria Digital & Propinas</h4>
                                <p class="text-[11px] text-slate-600">Emissão de faturas, cálculo automático de multas e M-Pesa/eMola.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
            </div>
        </div>
    </section>

    <!-- ONBOARDING INSTITUCIONAL (PUBLICA VS PRIVADA) -->
    <section class="py-16">
        <div class="container">
            <div class="section-title">
                <span>ONBOARDING FLEXÍVEL</span>
                <h2>Concebido para Qualquer Regime Institucional</h2>
            </div>

            <div class="onboarding-grid">
                <!-- Escola Pública -->
                <div class="onboarding-card">
                    <span class="onboarding-badge badge-public"><i class="fas fa-landmark me-1"></i> Modo Escola Pública</span>
                    <h3>Gestão de Vagas & Pautas Oficiais</h3>
                    <p>Ideal para estabelecimentos do Ensino Primário e Secundário Geral mantidos pelo Estado Moçambicano.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Gestão gratuita de matrículas e turmas</li>
                        <li><i class="fas fa-check-circle"></i> Emissão de Pautas Frequência & Exames MINEDH</li>
                        <li><i class="fas fa-check-circle"></i> Certidões de Habilitações de 0 a 20 valores</li>
                        <li><i class="fas fa-check-circle"></i> Ocultação automática de módulos de cobrança</li>
                    </ul>
                </div>

                <!-- Escola Privada -->
                <div class="onboarding-card">
                    <span class="onboarding-badge badge-private"><i class="fas fa-coins me-1"></i> Modo Escola Privada</span>
                    <h3>Gestão Académica & Tesouraria Integrada</h3>
                    <p>Solução completa com controlo de propinas, recibos oficiais e integração financeira.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Controlo de mensalidades e propinas por aluno</li>
                        <li><i class="fas fa-check-circle"></i> Emissão automatizada de recibos e relatórios</li>
                        <li><i class="fas fa-check-circle"></i> Integração de pagamentos MPesa / eMola</li>
                        <li><i class="fas fa-check-circle"></i> Notificações automáticas por SMS/WhatsApp</li>
                    </ul>
                </div>
            </div>

            <!-- MODULO PRE-INSCRICAO MADURO (EVITAR CONGESTIONAMENTO) -->
            <div class="pre-enrollment-banner">
                <div class="pre-enrollment-content">
                    <div class="pre-enrollment-badge">
                        <i class="fas fa-shield-halved me-1"></i> Eliminação de Filas e Congestionamento
                    </div>
                    <h3>Pré-Inscrição Inteligente de Candidatos</h3>
                    <p>
                        Evite enchentes e filas nas secretarias durante a fase de início do ano lectivo. Pais e encarregados de educação realizam a submissão de dados online e recebem a pré-validação do processo em tempo real.
                    </p>
                    <a href="{{ route('public.pre-enrollment') }}" class="btn-hero-primary" style="display: inline-flex;">
                        <i class="fas fa-user-plus"></i> Realizar Pré-Inscrição Agora
                    </a>
                </div>

                <div class="space-y-3 bg-slate-800/80 p-6 rounded-2xl border border-slate-700">
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Submissão remota de documentos de identificação
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Alocação prévia de turmas por disponibilidade de vagas
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Notificação imediata por SMS para o encarregado
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Atendimento organizado por agendamento
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">
                        <div class="footer-brand-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h4>Escola dos Visionários</h4>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm mb-3">
                        Rede Escolar de Excelência · Filiais em Quelimane e Alto Molócuè. Sistema de Gestão Escolar (ZAMEDU - SIGE) em plena conformidade com o MINEDH e a Lei 18/2018.
                    </p>
                    <div class="footer-dev-badge">
                        Desenvolvido por <a href="http://146.235.224.99/" target="_blank">Filipe dos Santos</a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-3">Nossas Filiais</h4>
                    <ul class="text-xs space-y-2 text-slate-400">
                        <li><i class="fas fa-map-marker-alt me-2 text-emerald-500"></i><strong>Quelimane:</strong> Av. Samora Machel, Bairro Central</li>
                        <li><i class="fas fa-map-marker-alt me-2 text-emerald-500"></i><strong>Alto Molócuè:</strong> Av. Eduardo Mondlane</li>
                        <li><i class="fas fa-globe me-2 text-emerald-500"></i>Zambézia, Moçambique</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-3">Contactos & Atendimento</h4>
                    <ul class="text-xs space-y-2 text-slate-400">
                        <li><i class="fas fa-envelope me-2 text-emerald-500"></i><a href="mailto:quelimane@visionarios.co.mz" class="hover:text-white">quelimane@visionarios.co.mz</a></li>
                        <li><i class="fas fa-envelope me-2 text-emerald-500"></i><a href="mailto:molocue@visionarios.co.mz" class="hover:text-white">molocue@visionarios.co.mz</a></li>
                        <li><i class="fas fa-phone me-2 text-emerald-500"></i>+258 84 300 1000 / +258 84 300 2000</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; {{ date('Y') }} <strong>Escola dos Visionários</strong> · Plataforma ZAMEDU - SIGE · Todos os direitos reservados. · Desenvolvido por <a href="http://146.235.224.99/" target="_blank" class="text-emerald-400 font-bold hover:underline">Filipe dos Santos</a>
            </div>
        </div>
    </footer>

    {{-- AI Widget Público --}}
    @include('partials.ai-widget-public')

</body>
</html>
