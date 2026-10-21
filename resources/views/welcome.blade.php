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
                <img src="{{ asset('images/logo-visionarios.png') }}" alt="Escola dos Visionários" style="height: 52px; width: auto; object-fit: contain;" class="brand-logo-img">
                <div class="brand-text">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 d-block" style="font-size: 10px; color: #007A3D; font-weight: 800;">ANO LECTIVO 2027 · ESCOLA PRIVADA</span>
                    <h1>ESCOLA DOS VISIONÁRIOS</h1>
                    <span>Aqui se Prepara a Nova Geração</span>
                </div>
            </div>

            <div class="top-actions">
                <a href="#requisitos" class="btn-pre-enrollment">
                    <i class="fas fa-clipboard-check"></i> Inscrições Abertas (Requisitos)
                </a>
                <a href="{{ route('public.pre-enrollment') }}" class="btn-pre-enrollment" style="background: #D1FAE5; border-color: #059669; color: #065F46;">
                    <i class="fas fa-file-signature"></i> Reservar Vaga / Pré-Inscrição
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
                        <i class="fas fa-sun text-yellow-400 me-1"></i>
                        AQUI SE PREPARA A NOVA GERAÇÃO · ANO LECTIVO 2027
                    </div>
                    <h1 class="hero-title">
                        Inscrições Abertas 2027 <span>Pré-Infantil, Pré-Escolar e Primário</span>
                    </h1>
                    <p class="hero-desc">
                        De 1ª à 6ª Classe com ensino de excelência e formação integral. Aulas de inglês em todas as classes, educação financeira infantil, princípios ético-cristãos e apoio psicopedagógico permanente nas nossas filiais de Quelimane e Alto Molócuè.
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ route('public.pre-enrollment') }}" class="btn-hero-primary">
                            <i class="fas fa-file-signature"></i> Reservar Vaga / Pré-Inscrição
                        </a>
                        <a href="#requisitos" class="btn-hero-secondary">
                            <i class="fas fa-clipboard-list"></i> Ver Requisitos & Taxas
                        </a>
                        <a href="{{ route('login') }}" class="btn-hero-secondary">
                            <i class="fas fa-right-to-bracket"></i> Aceder ao Portal
                        </a>
                    </div>

                    <!-- Níveis SNE -->
                    <div class="mt-6 pt-4 border-t border-white/20 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-yellow-400 uppercase tracking-wider">Homologação Oficial (SNE Lei 18/2018):</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">Pré-Infantil</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">Pré-Escolar</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">1ª à 6ª Classe</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-white/15 text-white border border-white/25">Quelimane & Alto Molócuè</span>
                    </div>
                </div>

                <div class="hero-poster-box">
                    <div style="background: rgba(255,255,255,0.08); padding: 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.25); box-shadow: 0 20px 45px rgba(0,0,0,0.35); text-align: center;">
                        <a href="{{ asset('images/cartaz-visionarios.jpg') }}" target="_blank" title="Clique para abrir o cartaz oficial em alta resolução">
                            <img src="{{ asset('images/cartaz-visionarios.jpg') }}" alt="Cartaz Oficial 2027 - Escola dos Visionários" style="width: 100%; max-height: 480px; object-fit: contain; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.25);">
                        </a>
                        <div class="d-flex align-items-center justify-content-between mt-2 px-2" style="font-size: 12px; color: rgba(255,255,255,0.9);">
                            <span><i class="fas fa-certificate text-warning me-1"></i> Cartaz Oficial 2027</span>
                            <a href="{{ asset('images/cartaz-visionarios.jpg') }}" target="_blank" style="color: #FCD116; font-weight: 700; text-decoration: underline;">
                                <i class="fas fa-expand me-1"></i> Ampliar Cartaz
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECÇÃO REQUISITOS, TAXAS E ACTIVIDADES DO CARTAZ OFICIAL -->
    <section class="py-16" id="requisitos">
        <div class="container">
            <div class="section-title">
                <span>INSCRIÇÕES ABERTAS 2027</span>
                <h2>Requisitos, Taxas & Actividades Extracurriculares</h2>
                <p class="text-slate-600 text-sm mt-2">Informações oficiais para o Ano Lectivo 2027 — Nível Pré-Infantil, Pré-Escolar e Primário (1ª à 6ª Classe)</p>
            </div>

            <!-- Banner Alerta Renovação Alunos Internos -->
            <div style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-left: 6px solid #D97706; padding: 20px 24px; border-radius: 14px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.12);">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 30px;">⚠️</span>
                    <div>
                        <strong style="color: #92400E; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; display: block;">Renovação de Matrícula para Alunos Internos</strong>
                        <span style="color: #78350F; font-size: 14px; font-weight: 700;">Período Oficial: <strong>De 1 de Outubro a 15 de Dezembro de 2026</strong></span>
                    </div>
                </div>
            </div>

            <div class="row g-4" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 60px;">
                <!-- Requisitos -->
                <div class="onboarding-card">
                    <span class="onboarding-badge badge-public"><i class="fas fa-folder-open me-1"></i> Documentos Obrigatórios</span>
                    <h3>Requisitos de Inscrição</h3>
                    <p>Documentos necessários para a matrícula de novos alunos:</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Documento de Identificação (Cédula pessoal, boletim de nascimento ou BI)</li>
                        <li><i class="fas fa-check-circle"></i> Atestado Médico oficial actualizado</li>
                        <li><i class="fas fa-check-circle"></i> Declaração da classe anterior (alunos de outras escolas)</li>
                        <li><i class="fas fa-check-circle"></i> 3 Fotografias recentes tipo passe</li>
                        <li><i class="fas fa-check-circle"></i> Ficha de inscrição e processo do aluno (secretaria)</li>
                    </ul>
                </div>

                <!-- Taxas e Valores -->
                <div class="onboarding-card" style="border-color: #059669;">
                    <span class="onboarding-badge badge-private"><i class="fas fa-receipt me-1"></i> Tabela de Valores 2027</span>
                    <h3>Taxas de Matrícula</h3>
                    <p>Valores oficiais da Escola dos Visionários para o ano lectivo:</p>
                    <div class="space-y-2 mb-4">
                        <div class="d-flex justify-content-between p-2 rounded bg-slate-50 border border-slate-200 text-sm">
                            <span class="text-slate-700 font-semibold">Taxa de Renovação / Matrícula:</span>
                            <strong class="text-slate-900">1.875,00 MT</strong>
                        </div>
                        <div class="d-flex justify-content-between p-2 rounded bg-slate-50 border border-slate-200 text-sm">
                            <span class="text-slate-700 font-semibold">Taxa de Avaliação:</span>
                            <strong class="text-slate-900">1.000,00 MT</strong>
                        </div>
                        <div class="d-flex justify-content-between p-2 rounded bg-slate-50 border border-slate-200 text-sm">
                            <span class="text-slate-700 font-semibold">Manual de Inglês:</span>
                            <strong class="text-slate-900">850,00 MT</strong>
                        </div>
                        <div class="d-flex justify-content-between p-3 rounded bg-emerald-50 border border-emerald-300 text-base font-bold text-emerald-900">
                            <span>Total Geral:</span>
                            <span>3.725,00 MT</span>
                        </div>
                    </div>
                    <a href="{{ route('public.pre-enrollment') }}" class="btn-hero-primary w-100 text-center justify-content-center" style="display: flex;">
                        <i class="fas fa-file-signature me-1"></i> Reservar Vaga Agora
                    </a>
                </div>

                <!-- Actividades e Diferenciais -->
                <div class="onboarding-card">
                    <span class="onboarding-badge badge-public" style="background: #FEF3C7; color: #92400E;"><i class="fas fa-star me-1"></i> Diferenciais de Ensino</span>
                    <h3>Actividades Extracurriculares</h3>
                    <p>Formação integral para preparar a nova geração:</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle text-emerald-600"></i> Aulas de Inglês em todas as classes</li>
                        <li><i class="fas fa-check-circle text-emerald-600"></i> Educação Moral e Cívica (baseada em princípios cristãos)</li>
                        <li><i class="fas fa-check-circle text-emerald-600"></i> Educação Financeira para a infância (6ª Classe)</li>
                        <li><i class="fas fa-check-circle text-emerald-600"></i> Educação Vocacional e Visitas de Excursão</li>
                        <li><i class="fas fa-check-circle text-emerald-600"></i> Canto, Dança e Actividades Culturais</li>
                        <li><i class="fas fa-check-circle text-emerald-600"></i> Psicólogo para apoiar dificuldades de aprendizagem</li>
                    </ul>
                </div>
            </div>

            <!-- MODULO RESERVAR VAGA / PRE-INSCRICAO MADURO -->
            <div class="pre-enrollment-banner">
                <div class="pre-enrollment-content">
                    <div class="pre-enrollment-badge">
                        <i class="fas fa-shield-halved me-1"></i> Atendimento Rápido e Sem Filas
                    </div>
                    <h3>Reserve a Vaga do Seu Educando para 2027</h3>
                    <p>
                        Garanta a vaga para o seu filho na Escola dos Visionários antes do encerramento das turmas. Preencha os dados online de forma rápida e segura para receber o contacto de confirmação da secretaria.
                    </p>
                    <a href="{{ route('public.pre-enrollment') }}" class="btn-hero-primary" style="display: inline-flex;">
                        <i class="fas fa-file-signature"></i> Fazer Pré-Inscrição / Reservar Vaga
                    </a>
                </div>

                <div class="space-y-3 bg-slate-800/80 p-6 rounded-2xl border border-slate-700">
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Preenchimento online simples e validação em tempo real
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Reserva antecipada nas classes de Pré-Infantil à 6ª Classe
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Contacto telefónico e WhatsApp direto da secretaria
                    </div>
                    <div class="flex items-center gap-3 text-sm font-bold text-emerald-400">
                        <i class="fas fa-check text-lg"></i> Atendimento na Sede (Mapiazua) e Filial Alto Molócuè
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                        <li><i class="fas fa-map-marker-alt me-2 text-emerald-500"></i><strong>Quelimane (Sede):</strong> Av. Eduardo Mondlane, Bairro Mapiazua</li>
                        <li><i class="fas fa-map-marker-alt me-2 text-emerald-500"></i><strong>Alto Molócuè:</strong> Av. Eduardo Mondlane</li>
                        <li><i class="fas fa-globe me-2 text-emerald-500"></i>Zambézia, Moçambique</li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-3">Secretaria & Inscrições</h4>
                    <ul class="text-xs space-y-2 text-slate-400">
                        <li><i class="fas fa-phone me-2 text-emerald-500"></i><strong>Quelimane:</strong> +258 85 558 0079 / +258 87 656 6029</li>
                        <li><i class="fas fa-envelope me-2 text-emerald-500"></i><a href="mailto:inscricoes@visionarios.co.mz" class="hover:text-white">inscricoes@visionarios.co.mz</a></li>
                        <li><i class="fas fa-envelope me-2 text-emerald-500"></i><a href="mailto:contacto@visionarios.co.mz" class="hover:text-white">contacto@visionarios.co.mz</a></li>
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
