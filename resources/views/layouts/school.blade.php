<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ setting('school_name', config('app.name', 'ZamEdu')) }} - @yield('title', 'Gestão Escolar Inteligente')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Cores Dinâmicas da Escola */
            --primary-navy: {{ setting('primary_color', '#0A2463') }};      /* Azul Marinho */
            --primary-ocean: {{ setting('secondary_color', '#0077B6') }};     /* Azul Oceano */
            --accent-sun: #FFB800;        /* Amarelo Sol */
            --accent-green: #00A878;      /* Verde */
            --accent-teal: #00B4D8;       /* Azul Claro */
            
            /* Cores de suporte */
            --success: #00A878;
            --warning: #FFB800;
            --danger: #E63946;
            --info: #00B4D8;
            --purple: #7209B7;
            
            /* Tons neutros */
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --gray-900: #0F172A;
            
            /* Gradientes */
            --gradient-primary: linear-gradient(135deg, var(--primary-navy) 0%, var(--primary-ocean) 100%);
            --gradient-accent: linear-gradient(135deg, var(--accent-sun) 0%, #FFD166 100%);
            --gradient-success: linear-gradient(135deg, var(--accent-green) 0%, #00C897 100%);
            --gradient-sidebar: linear-gradient(180deg, var(--primary-navy) 0%, #1E3A8A 100%);
            
            /* Layout */
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
            --header-height: 70px;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --border-radius-xl: 20px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 40px rgba(0, 0, 0, 0.12);
            --shadow-xl: 0 16px 60px rgba(0, 0, 0, 0.15);
        }

        [data-bs-theme="dark"] {
            --gray-50: #0F172A;
            --gray-100: #1E293B;
            --gray-200: #334155;
            --gray-300: #475569;
            --gray-400: #64748B;
            --gray-500: #94A3B8;
            --gray-600: #CBD5E1;
            --gray-700: #E2E8F0;
            --gray-800: #F1F5F9;
            --gray-900: #F8FAFC;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
            font-size: 14px;
            font-weight: 400;
            overflow-x: hidden;
        }

        .font-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ===== SIDEBAR MODERNA ===== */
        .vision-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--gradient-sidebar);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-xl);
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .vision-sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        /* Logo e Brand */
        .sidebar-header {
            height: var(--header-height);
            padding: 0 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .vision-sidebar.collapsed .sidebar-brand {
            justify-content: center;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: var(--gradient-accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(255, 184, 0, 0.3);
        }

        .brand-logo i {
            color: var(--primary-navy);
            font-size: 20px;
            font-weight: 700;
        }

        .brand-text {
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #fff 0%, #E2E8F0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 11px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .vision-sidebar.collapsed .brand-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        /* Navegação */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 24px 0;
        }

        .nav-section {
            margin-bottom: 32px;
        }

        .nav-section-title {
            padding: 0 24px 12px;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .vision-sidebar.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .vision-sidebar.collapsed .nav-link {
            padding: 14px;
            justify-content: center;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--accent-teal);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: var(--accent-sun);
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--accent-sun);
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .vision-sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .nav-text {
            flex: 1;
            font-size: 14px;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .vision-sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .vision-sidebar.collapsed .nav-badge {
            opacity: 0;
            transform: scale(0);
        }

        .badge-primary { background: var(--accent-teal); color: white; }
        .badge-success { background: var(--accent-green); color: white; }
        .badge-warning { background: var(--accent-sun); color: var(--primary-navy); }
        .badge-danger { background: var(--danger); color: white; }

        /* User Area */
        .sidebar-user {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 24px;
            background: rgba(255, 255, 255, 0.05);
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .vision-sidebar.collapsed .user-profile {
            justify-content: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--gradient-success);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 168, 120, 0.3);
        }

        .vision-sidebar.collapsed .user-avatar {
            margin-right: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
            transition: all 0.3s ease;
        }

        .vision-sidebar.collapsed .user-info {
            opacity: 0;
            width: 0;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: white;
            margin-bottom: 2px;
        }

        .user-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .logout-btn {
            width: 100%;
            padding: 12px 16px;
            border: none;
            background: rgba(230, 57, 70, 0.9);
            color: white;
            border-radius: var(--border-radius);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vision-sidebar.collapsed .logout-btn {
            padding: 12px;
        }

        .logout-btn:hover {
            background: var(--danger);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3);
        }

        .logout-text {
            margin-left: 8px;
            transition: all 0.3s ease;
        }

        .vision-sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
        }

        /* ===== HEADER MODERNO ===== */
        .vision-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--gray-200);
            z-index: 999;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        [data-bs-theme="dark"] .vision-header {
            background: rgba(15, 23, 42, 0.95);
            border-bottom-color: var(--gray-700);
        }

        .vision-sidebar.collapsed + .main-content .vision-header {
            left: var(--sidebar-collapsed);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .sidebar-toggle {
            width: 40px;
            height: 40px;
            border: none;
            background: var(--gray-100);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        [data-bs-theme="dark"] .sidebar-toggle {
            background: var(--gray-700);
            color: var(--gray-300);
        }

        .sidebar-toggle:hover {
            background: var(--primary-navy);
            color: white;
            transform: scale(1.05);
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [data-bs-theme="dark"] .page-title {
            color: var(--gray-100);
        }

        .page-title i {
            margin-right: 12px;
            color: var(--primary-ocean);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-search {
            position: relative;
        }

        .search-input {
            width: 320px;
            padding: 12px 45px 12px 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 14px;
            background: var(--gray-50);
            color: var(--gray-800);
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        [data-bs-theme="dark"] .search-input {
            background: var(--gray-800);
            border-color: var(--gray-600);
            color: var(--gray-200);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-ocean);
            box-shadow: 0 0 0 3px rgba(0, 119, 182, 0.1);
            background: white;
        }

        [data-bs-theme="dark"] .search-input:focus {
            background: var(--gray-700);
        }

        .search-input::placeholder {
            color: var(--gray-500);
        }

        .search-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 16px;
        }

        .header-action {
            width: 40px;
            height: 40px;
            border: 1px solid var(--gray-300);
            background: var(--gray-50);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        [data-bs-theme="dark"] .header-action {
            background: var(--gray-800);
            border-color: var(--gray-600);
            color: var(--gray-300);
        }

        .header-action:hover {
            background: var(--primary-ocean);
            color: white;
            border-color: var(--primary-ocean);
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .action-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
        }

        [data-bs-theme="dark"] .action-badge {
            border-color: var(--gray-800);
        }

        .user-dropdown .header-action {
            border: none;
            background: transparent;
        }

        .user-avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gradient-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        /* ===== CONTEÚDO PRINCIPAL ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding-top: var(--header-height);
            transition: all 0.3s ease;
            background: var(--gray-50);
        }

        .vision-sidebar.collapsed + .main-content {
            margin-left: var(--sidebar-collapsed);
        }

        .content-area {
            padding: 32px;
        }

        /* ===== COMPONENTES MODERNOS ===== */
        
        /* Cards */
        .vision-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        [data-bs-theme="dark"] .vision-card {
            background: var(--gray-800);
            border-color: var(--gray-700);
        }

        .vision-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .vision-card-header {
            padding: 24px;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        [data-bs-theme="dark"] .vision-card-header {
            background: var(--gray-700);
            border-bottom-color: var(--gray-600);
        }

        .vision-card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [data-bs-theme="dark"] .vision-card-title {
            color: var(--gray-100);
        }

        .vision-card-title i {
            margin-right: 12px;
            color: var(--primary-ocean);
        }

        .vision-card-body {
            padding: 24px;
        }

        /* Stat Cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius-lg);
            padding: 24px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        [data-bs-theme="dark"] .stat-card {
            background: var(--gray-800);
            border-color: var(--gray-700);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 24px;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stat-icon.primary { background: var(--gradient-primary); }
        .stat-icon.success { background: var(--gradient-success); }
        .stat-icon.warning { background: var(--gradient-accent); }
        .stat-icon.info { background: linear-gradient(135deg, var(--accent-teal) 0%, #0096C7 100%); }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 4px;
            line-height: 1;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [data-bs-theme="dark"] .stat-value {
            color: var(--gray-100);
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 8px;
        }

        [data-bs-theme="dark"] .stat-label {
            color: var(--gray-400);
        }

        .stat-change {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
        }

        .stat-change.positive {
            background: rgba(0, 168, 120, 0.1);
            color: var(--accent-green);
        }

        .stat-change.negative {
            background: rgba(230, 57, 70, 0.1);
            color: var(--danger);
        }

        .stat-change i {
            margin-right: 4px;
            font-size: 10px;
        }

        /* Botões */
        .btn-vision {
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 12px 24px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .btn-vision i {
            margin-right: 8px;
        }

        .btn-vision:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn-vision-primary {
            background: var(--gradient-primary);
            color: white;
        }

        .btn-vision-primary:hover {
            background: linear-gradient(135deg, #091C47 0%, #006494 100%);
            color: white;
        }

        .btn-vision-success {
            background: var(--gradient-success);
            color: white;
        }

        .btn-vision-warning {
            background: var(--gradient-accent);
            color: var(--primary-navy);
        }

        /* Tabelas */
        .table-vision {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 0;
        }

        [data-bs-theme="dark"] .table-vision {
            background: var(--gray-800);
        }

        .table-vision thead th {
            background: var(--gray-50);
            color: var(--gray-700);
            font-weight: 600;
            border-bottom: 2px solid var(--gray-200);
            padding: 16px 20px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [data-bs-theme="dark"] .table-vision thead th {
            background: var(--gray-700);
            color: var(--gray-300);
            border-bottom-color: var(--gray-600);
        }

        .table-vision tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-200);
            font-size: 14px;
        }

        [data-bs-theme="dark"] .table-vision tbody td {
            border-bottom-color: var(--gray-700);
        }

        .table-vision tbody tr:hover {
            background: var(--gray-50);
        }

        [data-bs-theme="dark"] .table-vision tbody tr:hover {
            background: var(--gray-700);
        }

        /* Badges */
        .badge-vision {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Alertas */
        .alert-vision {
            border: none;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 24px;
            border-left: 4px solid;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            background: var(--gray-50);
        }

        [data-bs-theme="dark"] .alert-vision {
            background: var(--gray-800);
        }

        .alert-vision i {
            margin-right: 12px;
            font-size: 18px;
            margin-top: 2px;
        }

        .alert-vision-success {
            border-left-color: var(--accent-green);
            color: var(--accent-green);
        }

        .alert-vision-warning {
            border-left-color: var(--accent-sun);
            color: var(--accent-sun);
        }

        .alert-vision-danger {
            border-left-color: var(--danger);
            color: var(--danger);
        }

        .alert-vision-info {
            border-left-color: var(--accent-teal);
            color: var(--accent-teal);
        }

        /* Breadcrumb */
        .breadcrumb-vision {
            background: transparent;
            padding: 0;
            margin-bottom: 24px;
        }

        .breadcrumb-vision .breadcrumb {
            background: white;
            border-radius: var(--border-radius);
            padding: 16px 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 0;
        }

        [data-bs-theme="dark"] .breadcrumb-vision .breadcrumb {
            background: var(--gray-800);
        }

        .breadcrumb-vision .breadcrumb-item a {
            color: var(--primary-ocean);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-vision .breadcrumb-item a:hover {
            color: var(--primary-navy);
        }

        .breadcrumb-vision .breadcrumb-item.active {
            color: var(--gray-600);
        }

        [data-bs-theme="dark"] .breadcrumb-vision .breadcrumb-item.active {
            color: var(--gray-400);
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 1199.98px) {
            .vision-sidebar {
                transform: translateX(-100%);
            }

            .vision-sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .vision-header {
                left: 0 !important;
            }

            .sidebar-toggle {
                display: flex !important;
            }
        }

        @media (max-width: 991.98px) {
            .content-area {
                padding: 24px;
            }

            .vision-header {
                padding: 0 24px;
            }

            .search-input {
                width: 240px;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .header-search {
                display: none;
            }

            .content-area {
                padding: 20px;
            }

            .vision-header {
                padding: 0 20px;
            }

            .page-title {
                font-size: 20px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .stat-value {
                font-size: 28px;
            }
        }

        /* Overlay Mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999"></div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="vision-sidebar" id="sidebar">
        <div class="sidebar-header p-4 border-b border-white/10 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="sidebar-brand flex items-center gap-3 no-underline group overflow-hidden">
                <div class="brand-logo w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-md border border-white/20 shrink-0 transition group-hover:scale-105">
                    @if(setting('school_logo'))
                        <img src="{{ setting('school_logo') }}" alt="Logo" class="w-8 h-8 object-contain rounded-xl">
                    @else
                        <i class="fas fa-graduation-cap text-lg"></i>
                    @endif
                </div>
                <div class="brand-text min-w-0">
                    <div class="brand-name text-sm font-extrabold text-white truncate tracking-tight group-hover:text-emerald-300 transition">
                        {{ setting('school_short_name', config('app.name', 'ZamEdu')) }}
                    </div>
                    <div class="brand-subtitle text-[10px] text-slate-300 font-medium truncate flex items-center gap-1">
                        @if(is_public_school())
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-400"></span> Escola Pública
                        @else
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Escola Privada
                        @endif
                    </div>
                </div>
            </a>
        </div>

        <div class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-section">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-chart-pie"></i>
                            </span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Gestão de Alunos -->
            @canany(['manage_students', 'view_students'])
            <div class="nav-section">
                <div class="nav-section-title">Gestão de Alunos</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-user-graduate"></i>
                            </span>
                            <span class="nav-text">Alunos</span>
                            @php
                                $activeStudents = \App\Models\Student::active()->count();
                            @endphp
                            <span class="nav-badge badge-primary">{{ $activeStudents }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('enrollments.index') }}" class="nav-link {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </span>
                            <span class="nav-text">Matrículas</span>
                            @php
                                $pendingEnrollments = \App\Models\Enrollment::where('status', 'pending')->count();
                            @endphp
                            @if($pendingEnrollments > 0)
                                <span class="nav-badge badge-warning">{{ $pendingEnrollments }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
            @endcanany

            <!-- Gestão Acadêmica -->
            @canany(['manage_classes', 'view_classes', 'manage_subjects'])
            <div class="nav-section">
                <div class="nav-section-title">Gestão Acadêmica</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-chalkboard"></i>
                            </span>
                            <span class="nav-text">Turmas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-book"></i>
                            </span>
                            <span class="nav-text">Disciplinas</span>
                        </a>
                    </li>
                    @can('manage_attendances')
                    <li class="nav-item">
                        <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                            <span class="nav-text">Presenças</span>
                        </a>
                    </li>
                    @endcan
                    @can('manage_grades')
                    <li class="nav-item">
                        <a href="{{ route('grades.index') }}" class="nav-link {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-medal"></i>
                            </span>
                            <span class="nav-text">Avaliações</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @endcanany

            <!-- Gestão Financeira -->
            @canany(['manage_payments', 'view_payments', 'manage_expenses', 'view_financial_reports'])
            <div class="nav-section">
                <div class="nav-section-title">{{ is_public_school() ? 'Gestão Financeira & Taxas' : 'Gestão Financeira' }}</div>
                <ul class="nav-list">
                    @can('manage_expenses')
                    <li class="nav-item">
                        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-receipt"></i>
                            </span>
                            <span class="nav-text">{{ is_public_school() ? 'Despesas Operacionais' : 'Despesas' }}</span>
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </span>
                            <span class="nav-text">{{ is_public_school() ? 'Taxas de Matrícula & Exames' : 'Mensalidades' }}</span>
                            @if(is_private_school())
                                @php
                                    $overduePayments = \App\Models\Payment::where('status', 'overdue')->count();
                                @endphp
                                @if($overduePayments > 0)
                                    <span class="nav-badge badge-danger">{{ $overduePayments }}</span>
                                @endif
                            @endif
                        </a>
                    </li>
                    @if(is_private_school())
                        @can('generate_payment_references')
                        <li class="nav-item">
                            <a href="{{ route('payments.references') }}" class="nav-link {{ request()->routeIs('payments.references', 'payments.pending') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <span class="nav-text">Referências & Pagamentos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('payments.overdue') }}" class="nav-link {{ request()->routeIs('payments.overdue') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                                <span class="nav-text">Mensalidades em Atraso</span>
                            </a>
                        </li>
                        @endcan
                        @can('view_financial_reports')
                        <li class="nav-item">
                            <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <span class="nav-text">Relatório Financeiro</span>
                            </a>
                        </li>
                        @endcan
                    @endif
                </ul>
            </div>
            @endcanany

            <!-- Eventos & Calendário -->
            @canany(['manage_events', 'view_events'])
            <div class="nav-section">
                <div class="nav-section-title">Eventos & Calendário</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <span class="nav-text">Eventos</span>
                        </a>
                    </li>
                </ul>
            </div>
            @endcanany

            <!-- Relatórios -->
            @canany(['view_reports', 'export_reports'])
            <div class="nav-section">
                <div class="nav-section-title">Relatórios</div>
                <ul class="nav-list">
                    @can('view_financial_reports')
                    <li class="nav-item">
                        <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <span class="nav-text">Financeiro</span>
                        </a>
                    </li>
                    @endcan
                    @can('view_academic_reports')
                    <li class="nav-item">
                        <a href="{{ route('reports.academic') }}" class="nav-link {{ request()->routeIs('reports.academic') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            <span class="nav-text">Académico</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @endcanany

            <!-- Gestão de Pessoal -->
            @canany(['manage_teachers', 'view_teachers'])
            <div class="nav-section">
                <div class="nav-section-title">Gestão de Pessoal</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </span>
                            <span class="nav-text">Professores</span>
                        </a>
                    </li>
                </ul>
            </div>
            @endcanany

            <!-- Administração -->
            @canany(['manage_users', 'manage_enrollments', 'manage_settings', 'backup_system', 'view_audit_logs', 'view_logs', 'manage_leave_requests'])
            <div class="nav-section">
                <div class="nav-section-title">Administração</div>
                <ul class="nav-list">
                    @can('manage_leave_requests')
                    <li class="nav-item">
                        <a href="{{ route('leave-requests.index') }}" class="nav-link {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                            <span class="nav-text">Licenças</span>
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a href="{{ route('admin.enrollments.renewals') }}" class="nav-link {{ request()->routeIs('admin.enrollments.renewals') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-sync-alt"></i>
                            </span>
                            <span class="nav-text">Renovar Matrículas</span>
                            @php
                                $pendingRenewals = \App\Models\Student::where('status', 'pending_renewal')->count();
                            @endphp
                            @if($pendingRenewals > 0)
                                <span class="nav-badge badge-warning">{{ $pendingRenewals }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.students-archive.index') }}" class="nav-link {{ request()->routeIs('admin.students-archive.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-archive"></i>
                            </span>
                            <span class="nav-text">Estudantes Arquivados</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.promotion.index') }}" class="nav-link {{ request()->routeIs('admin.promotion.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-layer-group"></i>
                            </span>
                            <span class="nav-text">Passagem de Classe</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.communications.index') }}" class="nav-link {{ request()->routeIs('communications.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-bullhorn"></i>
                            </span>
                            <span class="nav-text">Comunicações</span>
                        </a>
                    </li>
                    @can('manage_users')
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-users-cog"></i>
                            </span>
                            <span class="nav-text">Usuários</span>
                            <span class="nav-badge badge-danger">Admin</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.license') }}" class="nav-link {{ request()->routeIs('admin.license') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-key"></i>
                            </span>
                            <span class="nav-text">Licença do Sistema</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.academic-years.index') }}" class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <span class="nav-text">Ano Lectivo & Transição</span>
                        </a>
                    </li>
                    @endcan
                    @can('manage_settings')
                    <li class="nav-item">
                        <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                    @endcan
                    @can('backup_system')
                    <li class="nav-item">
                        <a href="{{ route('admin.backup') }}" class="nav-link {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-database"></i>
                            </span>
                            <span class="nav-text">Backup do Sistema</span>
                        </a>
                    </li>
                    @endcan
                    @can('view_audit_logs')
                    <li class="nav-item">
                        <a href="{{ route('admin.audit.index') }}" class="nav-link {{ request()->routeIs('admin.audit.index') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <span class="nav-text">Registo de Auditoria</span>
                        </a>
                    </li>
                    @endcan
                    @can('view_logs')
                    <li class="nav-item">
                        <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-file-alt"></i>
                            </span>
                            <span class="nav-text">Logs do Sistema</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </div>
            @endcanany

            <!-- Minha Conta -->
            <div class="nav-section">
                <div class="nav-section-title">Minha Conta</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-user-cog"></i>
                            </span>
                            <span class="nav-text">Meu Perfil</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-bell"></i>
                            </span>
                            <span class="nav-text">Notificações</span>
                            @php
                                $unreadNotifs = auth()->user()->unreadNotifications->count();
                            @endphp
                            @if ($unreadNotifs > 0)
                                <span class="nav-badge badge-danger">{{ $unreadNotifs }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- User Area -->
        <div class="sidebar-user">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ explode(' ', auth()->user()->name)[0] }}</div>
                    <div class="user-role">
                        @php
                            $roleName = auth()->user()->getRoleNames()->first() ?? 'user';
                        @endphp
                        {{ ucfirst($roleName) }}
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="logout-text">Sair</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <header class="vision-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <i class="{{ $titleIcon ?? 'fas fa-chart-pie' }}"></i>
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="header-right">
                <div class="header-search">
                    <input type="text" class="search-input" placeholder="Pesquisar...">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <button class="header-action" id="themeToggle" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>

                <button class="header-action" data-bs-toggle="dropdown" id="notificationsDropdown">
                    <i class="fas fa-bell"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="action-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </button>

                <div class="dropdown user-dropdown">
                    <button class="header-action" data-bs-toggle="dropdown">
                        <div class="user-avatar-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                        <li class="dropdown-header">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-cog me-2"></i>Meu Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-vision" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-1"></i>Início
                        </a>
                    </li>
                    @yield('breadcrumbs')
                </ol>
            </nav>

            <!-- Erros de Validação (mantidos inline) -->
            @if($errors->any())
                <div class="alert-vision alert-vision-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="flex-grow-1">
                        <strong>Erro!</strong> Verifique os campos do formulário.
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Toasts automáticos para mensagens de sessão -->
            @if (session('success') || session('error') || session('warning') || session('info'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        @if (session('success'))
                            showToast(@json(session('success')), 'success');
                        @endif
                        @if (session('error'))
                            showToast(@json(session('error')), 'error');
                        @endif
                        @if (session('warning'))
                            showToast(@json(session('warning')), 'warning');
                        @endif
                        @if (session('info'))
                            showToast(@json(session('info')), 'info');
                        @endif
                    });
                </script>
            @endif

            <!-- Conteúdo da Página -->
            <div class="fade-in">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ===== GERENCIAMENTO DO SIDEBAR =====
        let sidebarCollapsed = localStorage.getItem('vision-sidebar-collapsed') === 'true';
        let mobileMenuOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth >= 1200) {
                // Desktop: toggle collapsed state
                sidebarCollapsed = !sidebarCollapsed;
                localStorage.setItem('vision-sidebar-collapsed', sidebarCollapsed);
                
                if (sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                }
            } else {
                // Mobile: toggle menu
                mobileMenuOpen = !mobileMenuOpen;
                const overlay = document.getElementById('sidebarOverlay');
                
                if (mobileMenuOpen) {
                    sidebar.classList.add('mobile-open');
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }
        }

        // ===== GERENCIAMENTO DO TEMA =====
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('vision-theme', newTheme);
            
            const themeIcon = document.querySelector('#themeToggle i');
            themeIcon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }

        // ===== INICIALIZAÇÃO =====
        document.addEventListener('DOMContentLoaded', function() {
            // Aplicar tema salvo
            const savedTheme = localStorage.getItem('vision-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            
            const themeIcon = document.querySelector('#themeToggle i');
            themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            
            // Aplicar estado do sidebar
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth >= 1200 && sidebarCollapsed) {
                sidebar.classList.add('collapsed');
            }
            
            // Event listeners
            document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);
            
            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert-vision').forEach(alert => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            console.log('🎓 ZamEdu School System inicializado');
        });

        // ===== FUNÇÕES GLOBAIS =====
        function showToast(message, type = 'success') {
            const colorMap = {
                success: 'text-bg-success',
                error: 'text-bg-danger',
                warning: 'text-bg-warning',
                info: 'text-bg-primary'
            };
            const iconMap = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            const toastHtml = `
                <div class="toast align-items-center ${colorMap[type] || 'text-bg-success'} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${iconMap[type] || 'check-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            const container = document.querySelector('.toast-container');
            container.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = container.lastElementChild;
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        // ===== MODAL DE CONFIRMAÇÃO =====
        let _confirmCallback = null;
        function confirmAction(message, callback, options = {}) {
            if (confirm(message)) callback();
        }

        // API Global (ZamEdu)
        window.ZamEdu = {
            showToast,
            confirmAction,
            toggleSidebar,
            toggleTheme
        };
        window.Visionarios = window.ZamEdu;
    </script>

    @stack('scripts')

    {{-- Alpine.js para o widget AI flutuante --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    {{-- Widget AI flutuante --}}
    @include('partials.ai-widget')
</body>

</html>
