<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ setting('school_name', config('app.name', 'ZamEdu')) }} - @yield('title', 'Gestão Escolar')</title>
    
    <!-- PWA & Mobile Optimization -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4F46E5">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ZamEdu">

    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        {!! \App\Helpers\ThemeHelper::getCssVariables() !!}

        a {
            text-decoration: none !important;
        }
        a:hover {
            text-decoration: none !important;
        }

        /* ===== DARK MODE COMPLETE HARMONIZATION ===== */
        [data-bs-theme="dark"] body,
        [data-bs-theme="dark"] .main-content,
        [data-bs-theme="dark"] .admin-dashboard,
        [data-bs-theme="dark"] .bg-\[\#F4F6FA\] {
            background-color: var(--content-bg) !important;
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .school-card {
            background-color: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }

        [data-bs-theme="dark"] .bg-slate-50,
        [data-bs-theme="dark"] .bg-slate-50\/50,
        [data-bs-theme="dark"] .bg-slate-50\/70,
        [data-bs-theme="dark"] .bg-slate-50\/80,
        [data-bs-theme="dark"] .bg-slate-100 {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .text-slate-900,
        [data-bs-theme="dark"] .text-slate-800,
        [data-bs-theme="dark"] .text-slate-700 {
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .text-slate-600,
        [data-bs-theme="dark"] .text-slate-500,
        [data-bs-theme="dark"] .text-slate-400 {
            color: var(--text-muted) !important;
        }

        [data-bs-theme="dark"] .border-slate-100,
        [data-bs-theme="dark"] .border-slate-200,
        [data-bs-theme="dark"] .border-slate-200\/80,
        [data-bs-theme="dark"] .border-slate-300,
        [data-bs-theme="dark"] .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: var(--border-color) !important;
        }

        [data-bs-theme="dark"] .hover\:bg-slate-200\/60:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        [data-bs-theme="dark"] .bg-slate-800 {
            background-color: var(--primary) !important;
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .bg-slate-800:hover {
            background-color: var(--primary-dark) !important;
        }

        /* Success and Error Alerts in Settings (Dark Mode Harmonization) */
        [data-bs-theme="dark"] .bg-emerald-50 {
            background-color: rgba(16, 185, 129, 0.15) !important;
        }
        [data-bs-theme="dark"] .text-emerald-900 {
            color: #A7F3D0 !important;
        }
        [data-bs-theme="dark"] .bg-rose-50 {
            background-color: rgba(244, 63, 94, 0.15) !important;
        }
        [data-bs-theme="dark"] .text-rose-900 {
            color: #FECDD3 !important;
        }

        /* Badges settings Page in Dark Mode */
        [data-bs-theme="dark"] .bg-emerald-50 { background-color: rgba(16, 185, 129, 0.15) !important; }
        [data-bs-theme="dark"] .text-emerald-700 { color: #34D399 !important; }
        
        [data-bs-theme="dark"] .bg-blue-50 { background-color: rgba(59, 130, 246, 0.15) !important; }
        [data-bs-theme="dark"] .text-blue-700 { color: #60A5FA !important; }
        
        [data-bs-theme="dark"] .bg-purple-50 { background-color: rgba(139, 92, 246, 0.15) !important; }
        [data-bs-theme="dark"] .text-purple-700 { color: #A78BFA !important; }
        
        [data-bs-theme="dark"] .bg-amber-50 { background-color: rgba(245, 158, 11, 0.15) !important; }
        [data-bs-theme="dark"] .text-amber-700 { color: #FBBF24 !important; }
        
        [data-bs-theme="dark"] .bg-teal-50 { background-color: rgba(20, 184, 166, 0.15) !important; }
        [data-bs-theme="dark"] .text-teal-700 { color: #2DD4BF !important; }
        
        [data-bs-theme="dark"] .bg-indigo-50 { background-color: rgba(99, 102, 241, 0.15) !important; }
        [data-bs-theme="dark"] .text-indigo-700 { color: #818CF8 !important; }

        [data-bs-theme="dark"] input,
        [data-bs-theme="dark"] select,
        [data-bs-theme="dark"] textarea {
            background-color: var(--input-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--input-border) !important;
        }

        [data-bs-theme="dark"] .school-sidebar,
        [data-bs-theme="dark"] .school-header,
        [data-bs-theme="dark"] .main-header {
            background-color: var(--sidebar-bg) !important;
            border-color: var(--border-color) !important;
        }

        [data-bs-theme="dark"] .table {
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .table thead {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        [data-bs-theme="dark"] .table td,
        [data-bs-theme="dark"] .table th {
            border-color: var(--border-color) !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: var(--content-bg);
            color: var(--text-primary);
            line-height: 1.5;
            font-size: 13px;
            /* Reduzido de 14px */
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ESCOLAR - MOPHY HARMONIZED UI ===== */
        .school-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            z-index: 1040;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
        }

        .school-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* Header da Escola - MOPHY Harmonized */
        .school-header {
            height: var(--header-height);
            padding: 15px 18px;
            display: flex;
            align-items: center;
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
        }

        .school-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        .school-logo i {
            color: white;
            font-size: 20px;
        }

        .school-brand {
            flex: 1;
            min-width: 0;
            transition: all 0.3s ease;
        }

        .school-sidebar.collapsed .school-brand {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .school-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .school-subtitle {
            font-size: 11px;
            color: var(--sidebar-text-muted);
            font-weight: 600;
            margin-top: 1px;
        }

        .sidebar-toggle {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border-color);
            background: var(--sidebar-hover);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 8px;
        }

        .sidebar-toggle:hover {
            background: var(--border-color);
            color: var(--text-primary);
            transform: scale(1.05);
        }

        /* Navegação Escolar MOPHY */
        .school-nav {
            flex: 1;
            overflow-y: auto;
            padding: 15px 0;
        }

        .nav-section {
            margin-bottom: 20px;
        }

        .nav-section-title {
            padding: 0 22px 10px;
            font-size: 10px;
            font-weight: 700;
            color: var(--sidebar-text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .school-sidebar.collapsed .nav-section-title {
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
            padding: 10px 16px;
            margin: 0 12px;
            border-radius: 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.25s ease;
            position: relative;
            min-height: 42px;
            font-weight: 600;
        }

        .school-sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 10px;
            margin: 0 6px;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: var(--text-primary);
            padding-left: 16px;
        }

        .school-sidebar.collapsed .nav-link:hover {
            padding: 10px;
        }

        .nav-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            font-weight: 800;
            border-right: none;
        }

        .nav-icon {
            width: 20px;
            /* Reduzido */
            height: 20px;
            /* Reduzido */
            margin-right: 12px;
            /* Reduzido */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
            /* Reduzido */
        }

        .school-sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .nav-text {
            flex: 1;
            font-size: 13px;
            /* Reduzido */
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .school-sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 9px;
            /* Reduzido */
            padding: 3px 6px;
            /* Reduzido */
            border-radius: 12px;
            /* Reduzido */
            font-weight: 600;
            min-width: 18px;
            /* Reduzido */
            text-align: center;
            transition: all 0.3s ease;
        }

        .school-sidebar.collapsed .nav-badge {
            opacity: 0;
            transform: scale(0);
        }

        .badge-primary {
            background: var(--accent);
            color: white;
        }

        .badge-success {
            background: var(--success);
            color: white;
        }

        .badge-warning {
            background: var(--warning);
            color: white;
        }

        .badge-danger {
            background: var(--danger);
            color: white;
        }

        /* Área do Usuário - MOPHY Light UI */
        .user-area {
            border-top: 1px solid #5ee8e0ff;
            padding: 15px 18px;
            background: #ffffffff;
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .school-sidebar.collapsed .user-profile {
            justify-content: center;
            margin-bottom: 8px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10B981, #047857);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 15px;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.25);
        }

        .school-sidebar.collapsed .user-avatar {
            margin-right: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
            transition: all 0.3s ease;
        }

        .school-sidebar.collapsed .user-info {
            opacity: 0;
            width: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1px;
        }

        .user-role {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }

        .logout-btn {
            width: 100%;
            padding: 9px 12px;
            border: none;
            background: #fef2f2;
            color: #ef4444;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .school-sidebar.collapsed .logout-btn {
            padding: 9px;
        }

        .logout-btn:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .logout-text {
            margin-left: 6px;
            transition: all 0.3s ease;
        }

        .school-sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
        }

        /* ===== CONTEÚDO PRINCIPAL ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Header Principal - MOPHY Light UI */
        .main-header {
            height: var(--header-height);
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .mobile-menu-btn {
            width: 40px;
            height: 40px;
            border: 1px solid #f1f5f9;
            background: #ffffff;
            border-radius: 12px;
            display: none;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: #475569;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: #f8fafc;
            color: #047857;
            transform: translateY(-1px);
        }

        .page-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .page-title i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 18px;
            transition: var(--transition-base);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            /* Reduzido */
        }

        .header-search {
            position: relative;
        }

        .search-input {
            width: 280px;
            /* Reduzido */
            padding: 10px 40px 10px 16px;
            /* Reduzido */
            border: 1px solid var(--border-color);
            border-radius: 20px;
            /* Reduzido */
            font-size: 13px;
            /* Reduzido */
            background: var(--content-bg);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(26, 54, 93, 0.1);
            background: var(--card-bg);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-icon {
            position: absolute;
            right: 12px;
            /* Reduzido */
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            /* Reduzido */
        }

        .header-btn {
            width: 40px;
            /* Reduzido */
            height: 40px;
            /* Reduzido */
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .header-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(26, 54, 93, 0.3);
        }

        .notification-badge {
            position: absolute;
            top: -3px;
            /* Reduzido */
            right: -3px;
            /* Reduzido */
            background: var(--danger);
            color: white;
            font-size: 9px;
            /* Reduzido */
            padding: 2px 5px;
            /* Reduzido */
            border-radius: 12px;
            /* Reduzido */
            min-width: 16px;
            /* Reduzido */
            height: 16px;
            /* Reduzido */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Área de Conteúdo - Reduzida */
        .content-area {
            flex: 1;
            padding: 25px;
            /* Reduzido */
            background: var(--content-bg);
        }

        /* Cards de Estatísticas Escolares - Reduzidos */
        .school-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            /* Reduzido */
            gap: 20px;
            /* Reduzido */
            margin-bottom: 25px;
            /* Reduzido */
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 25px;
            /* Reduzido */
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            /* Reduzido */
            background: var(--primary);
        }

        .stat-card.students::before {
            background: var(--primary);
        }

        .stat-card.teachers::before {
            background: var(--accent);
        }

        .stat-card.payments::before {
            background: var(--success);
        }

        .stat-card.events::before {
            background: var(--warning);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            /* Reduzido */
            height: 60px;
            /* Reduzido */
            border-radius: 15px;
            /* Reduzido */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            /* Reduzido */
            font-size: 24px;
            /* Reduzido */
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.students {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .stat-icon.teachers {
            background: linear-gradient(135deg, var(--accent), #c53030);
        }

        .stat-icon.payments {
            background: linear-gradient(135deg, var(--success), #2f855a);
        }

        .stat-icon.events {
            background: linear-gradient(135deg, var(--warning), #c05621);
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .stat-icon.success {
            background: linear-gradient(135deg, var(--success), #2f855a);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning), #c05621);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, var(--info), #2b6cb0);
        }

        .stat-icon.pending {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 28px;
            /* Reduzido */
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 3px;
            line-height: 1;
            font-family: 'Poppins', sans-serif;
        }

        .stat-label {
            font-size: 14px;
            /* Reduzido */
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 8px;
            /* Reduzido */
        }

        .stat-change {
            font-size: 12px;
            /* Reduzido */
            font-weight: 600;
            padding: 3px 8px;
            /* Reduzido */
            border-radius: 15px;
            /* Reduzido */
            display: inline-flex;
            align-items: center;
        }

        .stat-change.positive {
            background: rgba(56, 161, 105, 0.1);
            color: var(--success);
        }

        .stat-change.negative {
            background: rgba(229, 62, 62, 0.1);
            color: var(--danger);
        }

        .stat-change i {
            margin-right: 3px;
            /* Reduzido */
            font-size: 11px;
            /* Reduzido */
        }

        /* Breadcrumb Escolar - Reduzido */
        .school-breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
            /* Reduzido */
        }

        .breadcrumb {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 12px 18px;
            /* Reduzido */
            box-shadow: var(--shadow);
        }

        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-dark);
        }

        .breadcrumb-item.active {
            color: var(--text-secondary);
        }

        /* Cards Escolares - MOPHY UI Style */
        .school-card {
            background: #ffffff;
            border: none;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .school-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .school-card-header {
            background: #ffffff;
            color: #0f172a;
            padding: 20px 24px;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .school-card-header i {
            margin-right: 10px;
            font-size: 16px;
            color: #10B981;
        }

        .school-card-body {
            padding: 24px;
        }

        /* Botões Escolares - Reduzidos */
        .btn-school {
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 10px 20px;
            /* Reduzido */
            font-size: 13px;
            /* Reduzido */
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-school i {
            margin-right: 6px;
            /* Reduzido */
        }

        .btn-school:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary-school {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-primary-school:hover {
            background: linear-gradient(135deg, var(--primary-dark), #0a1a2d);
            color: white;
        }

        .btn-secondary-school {
            background: linear-gradient(135deg, var(--secondary), var(--secondary-dark));
            color: white;
        }

        .btn-secondary-school:hover {
            background: linear-gradient(135deg, var(--secondary-dark), #9c7a1a);
            color: white;
        }

        .btn-success-school {
            background: linear-gradient(135deg, var(--success), #2f855a);
            color: white;
        }

        .btn-warning-school {
            background: linear-gradient(135deg, var(--warning), #c05621);
            color: white;
        }

        /* Tabelas Escolares - Reduzidas */
        .school-table-container {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            /* Reduzido */
        }

        .school-table-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 18px 22px;
            /* Reduzido */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .school-table-title {
            font-size: 16px;
            /* Reduzido */
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .school-table-title i {
            margin-right: 8px;
            /* Reduzido */
        }

        .table-school {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-school th {
            background: var(--content-bg);
            color: var(--text-primary);
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            padding: 15px 18px;
            /* Reduzido */
            font-size: 13px;
            /* Reduzido */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-school td {
            padding: 15px 18px;
            /* Reduzido */
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
            /* Reduzido */
        }

        .table-school tbody tr:hover {
            background: var(--content-bg);
        }

        /* Alertas Escolares - Reduzidos */
        .alert-school {
            border: none;
            border-radius: var(--border-radius);
            padding: 15px 22px;
            /* Reduzido */
            margin-bottom: 20px;
            /* Reduzido */
            border-left: 4px solid;
            /* Reduzido */
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .alert-school i {
            margin-right: 10px;
            /* Reduzido */
            font-size: 16px;
            /* Reduzido */
        }

        .alert-success-school {
            background: rgba(56, 161, 105, 0.1);
            color: var(--success);
            border-left-color: var(--success);
        }

        .alert-warning-school {
            background: rgba(221, 107, 32, 0.1);
            color: var(--warning);
            border-left-color: var(--warning);
        }

        .alert-danger-school {
            background: rgba(229, 62, 62, 0.1);
            color: var(--danger);
            border-left-color: var(--danger);
        }

        .alert-info-school {
            background: rgba(49, 130, 206, 0.1);
            color: var(--info);
            border-left-color: var(--info);
        }

        /* Footer Escolar */
        .school-footer {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(25, 67, 124, 0.08), rgba(75, 168, 60, 0.08)), var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 22px 0;
            margin-top: auto;
            box-shadow: 0 -6px 18px rgba(15, 23, 42, 0.06);
        }

        .school-footer::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 10% -20%, rgba(249, 168, 37, 0.18), transparent 35%);
            pointer-events: none;
        }

        .footer-shell {
            position: relative;
            z-index: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center;
            justify-content: space-between;
        }

        .footer-brand,
        .footer-meta {
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 12px;
            padding: 10px 14px;
            backdrop-filter: blur(8px);
        }

        .footer-brand .brand-main {
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
        }

        .footer-brand .brand-sub {
            color: var(--text-muted);
            font-size: 11px;
        }

        .footer-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .footer-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .footer-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-primary);
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            padding: 5px 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .footer-link:hover {
            color: var(--primary);
            background: rgba(25, 67, 124, 0.08);
        }

        /* Responsividade */
        @media (max-width: 1199.98px) {
            .main-content {
                margin-left: 0 !important;
            }

            .school-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .school-sidebar.mobile-visible {
                transform: translateX(0);
            }

            .sidebar-toggle-btn {
                display: none !important;
            }

            .mobile-menu-btn {
                display: flex !important;
            }

            .search-input {
                width: 220px;
                /* Reduzido */
            }
        }

        @media (max-width: 991.98px) {
            .content-area {
                padding: 18px;
                /* Reduzido */
            }

            .main-header {
                padding: 0 18px;
                /* Reduzido */
            }

            .search-input {
                width: 180px;
                /* Reduzido */
            }

            .page-title {
                font-size: 18px;
                /* Reduzido */
            }

            .sidebar-toggle-btn {
                display: none !important;
            }

            .footer-shell {
                flex-direction: column;
                align-items: stretch;
            }

            .footer-brand,
            .footer-meta {
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .header-search {
                display: none;
            }

            .sidebar-toggle-btn {
                display: none !important;
            }

            .page-title {
                font-size: 16px;
                /* Reduzido */
            }

            .stat-card {
                padding: 18px;
                /* Reduzido */
            }

            .stat-icon {
                width: 50px;
                /* Reduzido */
                height: 50px;
                /* Reduzido */
                font-size: 20px;
                /* Reduzido */
            }

            .stat-value {
                font-size: 24px;
                /* Reduzido */
            }
        }

        /* Overlay para mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        [data-bs-theme="dark"] .school-footer {
            background: linear-gradient(135deg, rgba(25, 67, 124, 0.2), rgba(75, 168, 60, 0.14)), var(--card-bg);
        }

        [data-bs-theme="dark"] .footer-brand,
        [data-bs-theme="dark"] .footer-meta {
            background: rgba(15, 23, 42, 0.46);
            border-color: rgba(148, 163, 184, 0.26);
        }

        [data-bs-theme="dark"] .footer-link {
            color: var(--text-secondary);
        }

        [data-bs-theme="dark"] .footer-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
        }

        /* Sistema de Toasts - Reduzido */
        .toast-container {
            position: fixed;
            top: 15px;
            /* Reduzido */
            right: 15px;
            /* Reduzido */
            z-index: 9999;
        }

        .toast-school {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            background: var(--card-bg);
            margin-bottom: 8px;
            /* Reduzido */
            min-width: 300px;
            /* Reduzido */
            overflow: hidden;
        }

        .toast-school .toast-header {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        /* Loading Animation - Reduzido */
        .loading-school {
            position: relative;
            color: transparent !important;
        }

        .loading-school::after {
            content: "";
            position: absolute;
            width: 16px;
            /* Reduzido */
            height: 16px;
            /* Reduzido */
            top: 50%;
            left: 50%;
            margin-left: -8px;
            /* Reduzido */
            margin-top: -8px;
            /* Reduzido */
            border: 2px solid var(--primary);
            /* Reduzido */
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin-school 1s ease-in-out infinite;
        }

        @keyframes spin-school {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar Escolar -->
    <nav class="school-sidebar" id="sidebar">
        <div class="school-header p-4 border-b border-white/10 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline group overflow-hidden">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-md border border-white/20 shrink-0 transition group-hover:scale-105">
                    @if(setting('school_logo'))
                        <img src="{{ setting('school_logo') }}" alt="Logo" class="w-8 h-8 object-contain rounded-xl">
                    @else
                        <i class="fas fa-graduation-cap text-lg"></i>
                    @endif
                </div>
                <div class="school-brand min-w-0">
                    <div class="school-name text-sm font-extrabold text-success truncate tracking-tight font-heading group-hover:text-emerald-300 transition">
                        {{ setting('school_short_name', config('app.name', 'ZamEdu')) }}
                    </div>
                    <div class="school-subtitle text-[10px] text-slate-300 font-medium truncate flex items-center gap-1">
                        @if(is_public_school())
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-400"></span> Escola Pública
                        @else
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Escola Privada
                        @endif
                    </div>
                </div>
            </a>
        </div>

        <div class="school-nav">
            @auth
                <!-- 1. DASHBOARD -->
                <div class="nav-section">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        @hasanyrole('super_admin|province_admin|province_inspector|district_admin|district_inspector|zip_coordinator|zip_admin')
                        <li class="nav-item">
                            <a href="{{ route('territorial.dashboard') }}"
                                class="nav-link {{ request()->routeIs('territorial.*') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-map-location-dot"></i>
                                </span>
                                <span class="nav-text">Inspeção MINEDH</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('onboarding.school') }}"
                                class="nav-link {{ request()->routeIs('onboarding.*') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-plus-circle"></i>
                                </span>
                                <span class="nav-text">Registar Nova Escola</span>
                            </a>
                        </li>
                        @endhasanyrole
                    </ul>
                </div>
            @else
                <!-- Links Públicos -->
                <div class="nav-section">
                    <div class="nav-section-title">Acesso Público</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('welcome') }}" class="nav-link">
                                <span class="nav-icon"><i class="fas fa-home"></i></span>
                                <span class="nav-text">Início</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('public.announcements') }}" class="nav-link {{ request()->routeIs('public.announcements') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-bullhorn"></i></span>
                                <span class="nav-text">Comunicados</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('public.material-lists') }}" class="nav-link {{ request()->routeIs('public.material-lists') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-list-ul"></i></span>
                                <span class="nav-text">Materiais</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('public.pre-enrollment') }}" class="nav-link {{ request()->routeIs('public.pre-enrollment') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-file-signature"></i></span>
                                <span class="nav-text">Pré-Inscrição</span>
                            </a>
                        </li>
                    </ul>
                </div>
            @endauth

            @auth
            <!-- PORTAL DO PROFESSOR (Visão dedicada para docente) -->
            @if (auth()->user()->role === 'teacher')
                <div class="nav-section">
                    <div class="nav-section-title">Meu Portal Docente</div>
                    <ul class="nav-list">
                        @if(is_menu_visible('teacher_dashboard'))
                        <li class="nav-item">
                            <a href="{{ route('teacher.dashboard') }}"
                                class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-home"></i></span>
                                <span class="nav-text">Início</span>
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('teacher_classes'))
                        <li class="nav-item">
                            <a href="{{ route('teacher.classes.index') }}"
                                class="nav-link {{ request()->routeIs('teacher.classes.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-chalkboard"></i></span>
                                <span class="nav-text">Minhas Turmas</span>
                                @php
                                    $classCount = auth()->user()->teacher?->classes()->active()->currentYear()->count() ?? 0;
                                @endphp
                                @if ($classCount > 0)
                                    <span class="nav-badge badge-success">{{ $classCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @php
                            $teacher = auth()->user()->teacher ?? null;
                            $classes = $teacher?->classes()->active()->currentYear()->get();
                            $firstClass = $classes?->first();
                        @endphp
                        @if(is_menu_visible('teacher_attendance'))
                        <li class="nav-item">
                            @if ($firstClass)
                                <a href="{{ route('teacher.attendance.class', $firstClass->id) }}"
                                    class="nav-link {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
                                    <span class="nav-text">Presenças</span>
                                </a>
                            @endif
                        </li>
                        @endif
                        @if(is_menu_visible('teacher_pending_grades'))
                        <li class="nav-item">
                            <a href="{{ route('teacher.grades.pending') }}"
                                class="nav-link {{ request()->routeIs('teacher.grades.pending') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-clock"></i></span>
                                <span class="nav-text">Avaliações Pendentes</span>
                                @php
                                    $pendingCount = auth()->user()->teacher?->getPendingAssessmentsCount() ?? 0;
                                @endphp
                                @if ($pendingCount > 0)
                                    <span class="nav-badge badge-warning">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('teacher_gradebook'))
                        <li class="nav-item">
                            @if ($firstClass)
                                <a href="{{ route('teacher.gradebook', $firstClass->id) }}"
                                    class="nav-link {{ request()->routeIs('teacher.gradebook') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-medal"></i></span>
                                    <span class="nav-text">Caderno de Notas</span>
                                </a>
                            @endif
                        </li>
                        @endif
                        @if(is_menu_visible('teacher_pautas'))
                        <li class="nav-item">
                            <a href="{{ route('pautas.index') }}"
                                class="nav-link {{ request()->routeIs('pautas.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-file-invoice"></i></span>
                                <span class="nav-text">Pautas das Turmas</span>
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('teacher_communications'))
                        <li class="nav-item">
                            <a href="{{ route('teacher.communications.index') }}"
                                class="nav-link {{ request()->routeIs('teacher.communications.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-bullhorn"></i></span>
                                <span class="nav-text">Comunicados</span>
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('teacher_leaves'))
                        <li class="nav-item">
                            <a href="{{ route('teacher.leave-requests.index') }}"
                                class="nav-link {{ request()->routeIs('teacher.leave-requests.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-calendar-times"></i></span>
                                <span class="nav-text">Minhas Licenças</span>
                                @php
                                    $teacherPendingLeaves = auth()->user()->teacher?->leaveRequests()->where('status', 'pending')->count() ?? 0;
                                @endphp
                                @if ($teacherPendingLeaves > 0)
                                    <span class="nav-badge badge-warning">{{ $teacherPendingLeaves }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            @endif

            <!-- PORTAL DO PAIS / RESPONSÁVEL -->
            @if (auth()->user()->role === 'parent')
                <div class="nav-section">
                    <div class="nav-section-title">Portal do Encarregado</div>
                    <ul class="nav-list">
                        @if(is_menu_visible('parent_dashboard'))
                        <li class="nav-item">
                            <a href="{{ route('parent.dashboard') }}"
                                class="nav-link {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-home"></i></span>
                                <span class="nav-text">Painel Principal</span>
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('parent_children'))
                        <li class="nav-item">
                            <a href="{{ route('parent.children') }}"
                                class="nav-link {{ request()->routeIs('parent.children') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-child"></i></span>
                                <span class="nav-text">Meus Educandos</span>
                            </a>
                        </li>
                        @endif
                        @if(is_private_school() && is_menu_visible('parent_payments'))
                        <li class="nav-item">
                            <a href="{{ route('parent.payments') }}"
                                class="nav-link {{ request()->routeIs('parent.payments*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <span class="nav-text">Propinas & Recibos</span>
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('parent_communications'))
                        <li class="nav-item">
                            <a href="{{ route('parent.communications') }}"
                                class="nav-link {{ request()->routeIs('parent.communications*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-bullhorn"></i></span>
                                <span class="nav-text">Comunicados & Notificações</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            @endif

            <!-- 2. GESTÃO DE ESTUDANTES & MATRÍCULAS -->
            @canany(['manage_students', 'view_students'])
                @if (auth()->user()->role !== 'teacher')
                    <div class="nav-section">
                        <div class="nav-section-title">Estudantes & Matrículas</div>
                        <ul class="nav-list">
                            @if(is_menu_visible('menu_students'))
                            <li class="nav-item">
                                <a href="{{ route('students.index') }}"
                                    class="nav-link {{ request()->routeIs('students.*') && !request()->routeIs('admin.students-archive.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-user-graduate"></i></span>
                                    <span class="nav-text">Todos os Alunos</span>
                                </a>
                            </li>
                            @endif
                            @can('manage_enrollments')
                                @if(is_menu_visible('menu_enrollments'))
                                <li class="nav-item">
                                    <a href="{{ route('enrollments.index') }}"
                                        class="nav-link {{ request()->routeIs('enrollments.*') && !request()->routeIs('admin.enrollments.renewals') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                                        <span class="nav-text">Matrículas & Inscrições</span>
                                        @php
                                            $pendingEnrollments = \App\Models\Enrollment::where('status', 'pending')->count();
                                        @endphp
                                        @if ($pendingEnrollments > 0)
                                            <span class="nav-badge badge-danger">{{ $pendingEnrollments }}</span>
                                        @endif
                                    </a>
                                </li>
                                @endif
                                @if(is_menu_visible('menu_renewals'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.enrollments.renewals') }}"
                                        class="nav-link {{ request()->routeIs('admin.enrollments.renewals') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-sync-alt"></i></span>
                                        <span class="nav-text">Renovação de Matrículas</span>
                                    </a>
                                </li>
                                @endif
                                @if(is_menu_visible('menu_promotions'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.promotion.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.promotion.*') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                                        <span class="nav-text">Passagem de Classe</span>
                                    </a>
                                </li>
                                @endif
                                @if(is_menu_visible('menu_students_archive'))
                                <li class="nav-item">
                                    <a href="{{ route('admin.students-archive.index') }}"
                                        class="nav-link {{ request()->routeIs('admin.students-archive.*') ? 'active' : '' }}">
                                        <span class="nav-icon"><i class="fas fa-archive"></i></span>
                                        <span class="nav-text">Alunos Arquivados</span>
                                    </a>
                                </li>
                                @endif
                            @endcan
                            @if(is_menu_visible('menu_parents'))
                            <li class="nav-item">
                                <a href="{{ route('parents.index') }}"
                                    class="nav-link {{ request()->routeIs('parents.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                                    <span class="nav-text">Encarregados de Educação</span>
                                </a>
                            </li>
                            @endif
                            @if(is_menu_visible('menu_gatekeeper'))
                            <li class="nav-item">
                                <a href="{{ route('gatekeeper.index') }}"
                                    class="nav-link {{ request()->routeIs('gatekeeper.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-id-card-clip"></i></span>
                                    <span class="nav-text">Portaria Digital (Cartões)</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                @endif
            @endcanany

            <!-- 3. ORGANIZAÇÃO ACADÉMICA & DOCENTES -->
            @canany(['manage_classes', 'view_classes', 'manage_subjects', 'manage_teachers', 'view_teachers'])
                <div class="nav-section">
                    <div class="nav-section-title">Organização Académica & Docentes</div>
                    <ul class="nav-list">
                        @canany(['manage_classes', 'view_classes'])
                            @if(is_menu_visible('menu_classes'))
                            <li class="nav-item">
                                <a href="{{ route('classes.index') }}"
                                    class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-chalkboard"></i></span>
                                    <span class="nav-text">Turmas por Nível</span>
                                </a>
                            </li>
                            @endif
                        @endcanany
                        @can('manage_subjects')
                            @if(is_menu_visible('menu_subjects'))
                            <li class="nav-item">
                                <a href="{{ route('subjects.index') }}"
                                    class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-book-open"></i></span>
                                    <span class="nav-text">Matriz Curricular</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                        @canany(['manage_teachers', 'view_teachers'])
                            @if(is_menu_visible('menu_teachers'))
                            <li class="nav-item">
                                <a href="{{ route('teachers.index') }}"
                                    class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                                    <span class="nav-text">Corpo Docente</span>
                                </a>
                            </li>
                            @endif
                        @endcanany
                        @can('manage_leave_requests')
                            @if(is_menu_visible('menu_staff_leaves'))
                            <li class="nav-item">
                                <a href="{{ route('staff-leave-requests.index') }}"
                                    class="nav-link {{ request()->routeIs('staff-leave-requests.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-calendar-times"></i></span>
                                    <span class="nav-text">Licenças & Ausências</span>
                                    @php
                                        $pendingRequests = \App\Models\StaffLeaveRequest::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingRequests > 0)
                                        <span class="nav-badge badge-warning">{{ $pendingRequests }}</span>
                                    @endif
                                </a>
                            </li>
                            @endif
                        @endcan
                    </ul>
                </div>
            @endcanany

            <!-- 4. AVALIAÇÕES, PAUTAS & DOCUMENTOS -->
            @canany(['manage_grades', 'view_grades', 'manage_attendances'])
                <div class="nav-section">
                    <div class="nav-section-title">Avaliações & Documentos</div>
                    <ul class="nav-list">
                        @can('manage_grades')
                            @if(is_menu_visible('menu_grades'))
                            <li class="nav-item">
                                <a href="{{ route('grades.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-medal"></i></span>
                                    <span class="nav-text">Lançamento de Notas & Pautas</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                        @unless(auth()->user()->role === 'teacher')
                            @if(is_menu_visible('menu_certificates'))
                            <li class="nav-item">
                                <a href="{{ route('certificates.index') }}"
                                    class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-certificate"></i></span>
                                    <span class="nav-text">Certidões & Certificados (MINEDH)</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reports.pedagogical-map') }}"
                                    class="nav-link {{ request()->routeIs('reports.pedagogical-map*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-table text-emerald-400"></i></span>
                                    <span class="nav-text">Mapa Pedagógico (HM/M)</span>
                                </a>
                            </li>
                            @endif
                        @endunless
                        @can('manage_attendances')
                            @if(is_menu_visible('menu_attendances'))
                            <li class="nav-item">
                                <a href="{{ route('attendances.index') }}"
                                    class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
                                    <span class="nav-text">Frequência & Assiduidade</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                    </ul>
                </div>
            @endcanany

            <!-- 5. GESTÃO FINANCEIRA -->
            @php
                $isAdmin = auth()->check() && (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('super_admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->role === 'admin' ||
                    auth()->user()->role === 'superadmin' ||
                    auth()->user()->role === 'super_admin'
                );
            @endphp
            @if ($isAdmin || auth()->user()->canAny(['manage_payments', 'view_payments', 'manage_expenses', 'view_financial_reports', 'generate_payment_references']))
                <div class="nav-section">
                    <div class="nav-section-title">{{ is_public_school() ? 'Gestão Financeira & Taxas' : 'Gestão Financeira' }}</div>
                    <ul class="nav-list">
                        @if (($isAdmin || auth()->user()->can('manage_expenses')) && is_menu_visible('menu_expenses'))
                            <li class="nav-item">
                                <a href="{{ route('expenses.index') }}"
                                    class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-receipt"></i></span>
                                    <span class="nav-text">{{ is_public_school() ? 'Despesas Operacionais' : 'Despesas' }}</span>
                                </a>
                            </li>
                        @endif

                        @if (($isAdmin || auth()->user()->canAny(['manage_payments', 'view_payments'])) && is_menu_visible('menu_payments'))
                            <li class="nav-item">
                                <a href="{{ route('payments.index') }}"
                                    class="nav-link {{ request()->routeIs('payments.index') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                                    <span class="nav-text">{{ is_public_school() ? 'Taxas de Matrícula & Exames' : 'Mensalidades & Propinas' }}</span>
                                    @if(is_private_school())
                                        @php
                                            $overduePayments = \App\Models\Payment::where('status', 'overdue')->count();
                                        @endphp
                                        @if ($overduePayments > 0)
                                            <span class="nav-badge badge-danger">{{ $overduePayments }}</span>
                                        @endif
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if (is_private_school() && ($isAdmin || auth()->user()->can('generate_payment_references')))
                            @if(is_menu_visible('menu_references'))
                            <li class="nav-item">
                                <a href="{{ route('payments.references') }}"
                                    class="nav-link {{ request()->routeIs('payments.references', 'payments.pending') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-clock"></i></span>
                                    <span class="nav-text">Referências & Pagamentos</span>
                                </a>
                            </li>
                            @endif
                            @if(is_menu_visible('menu_overdue'))
                            <li class="nav-item">
                                <a href="{{ route('payments.overdue') }}"
                                    class="nav-link {{ request()->routeIs('payments.overdue') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                    <span class="nav-text">Mensalidades em Atraso</span>
                                </a>
                            </li>
                            @endif
                        @endif

                        @if (is_private_school() && ($isAdmin || auth()->user()->can('view_financial_reports')) && is_menu_visible('menu_reports_financial'))
                            <li class="nav-item">
                                <a href="{{ route('reports.financial') }}"
                                    class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                                    <span class="nav-text">Relatório Financeiro</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            <!-- 6. COMUNICAÇÃO & FERRAMENTAS -->
            @canany(['manage_events', 'send_notifications', 'view_reports', 'export_reports'])
                <div class="nav-section">
                    <div class="nav-section-title">Comunicação & IA</div>
                    <ul class="nav-list">
                        @can('manage_events')
                            @if(is_menu_visible('menu_events'))
                            <li class="nav-item">
                                <a href="{{ route('events.index') }}"
                                    class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                                    <span class="nav-text">Calendário de Eventos</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                        @if(is_menu_visible('menu_communications'))
                        <li class="nav-item">
                            <a href="{{ route('admin.communications.index') }}"
                                class="nav-link {{ request()->routeIs('communications.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-bullhorn"></i></span>
                                <span class="nav-text">Comunicados Escolares</span>
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('menu_sms_center'))
                        <li class="nav-item">
                            <a href="{{ route('sms.index') }}"
                                class="nav-link {{ request()->routeIs('sms.index') && ! request()->routeIs('sms.inbox.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-comment-sms"></i></span>
                                <span class="nav-text">Central de SMS</span>
                            </a>
                        </li>
                        @endif
                        @php
                            $unreadSms = \App\Models\SmsInbox::where('is_read', false)->count();
                        @endphp
                        @if(is_menu_visible('menu_sms_inbox'))
                        <li class="nav-item">
                            <a href="{{ route('sms.inbox.index') }}"
                                class="nav-link {{ request()->routeIs('sms.inbox.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-inbox text-emerald-500"></i></span>
                                <span class="nav-text">SMS Recebidas</span>
                                @if($unreadSms > 0)
                                    <span class="ml-auto inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">
                                        {{ $unreadSms > 99 ? '99+' : $unreadSms }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @if(is_menu_visible('menu_ai_assistant'))
                        <li class="nav-item">
                            <a href="{{ route('ai.index') }}"
                                class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                                <span class="nav-icon"><i class="fas fa-robot text-emerald-400"></i></span>
                                <span class="nav-text">Assistente IA</span>
                            </a>
                        </li>
                        @endif
                        @canany(['view_reports', 'export_reports'])
                            @if(is_menu_visible('menu_reports'))
                            <li class="nav-item">
                                <a href="{{ route('reports.index') }}"
                                    class="nav-link {{ request()->routeIs('reports.*') && !request()->routeIs('reports.financial') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                                    <span class="nav-text">Relatórios & Estatísticas</span>
                                </a>
                            </li>
                            @endif
                        @endcanany
                    </ul>
                </div>
            @endcanany

            <!-- 7. CONFIGURAÇÕES & ADMINISTRAÇÃO DO SISTEMA -->
            @canany(['manage_users', 'manage_settings', 'backup_system', 'view_audit_logs', 'view_logs'])
                <div class="nav-section">
                    <div class="nav-section-title">Configurações & Sistema</div>
                    <ul class="nav-list">
                        @can('manage_users')
                            @if(is_menu_visible('menu_users'))
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-users-cog"></i></span>
                                    <span class="nav-text">Gestão de Usuários</span>
                                </a>
                            </li>
                            @endif
                            @if(is_menu_visible('menu_license'))
                            <li class="nav-item">
                                <a href="{{ route('admin.license') }}"
                                    class="nav-link {{ request()->routeIs('admin.license') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-key"></i></span>
                                    <span class="nav-text">Licença do Sistema</span>
                                </a>
                            </li>
                            @endif
                            @if(is_menu_visible('menu_academic_years'))
                            <li class="nav-item">
                                <a href="{{ route('admin.academic-years.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                                    <span class="nav-text">Gestão do Ano Lectivo</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                        @can('manage_settings')
                            @if(is_menu_visible('menu_settings'))
                            <li class="nav-item">
                                <a href="{{ route('admin.settings.index') }}"
                                    class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-cog"></i></span>
                                    <span class="nav-text">Configurações Gerais</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                        @can('backup_system')
                            @if(is_menu_visible('menu_backup'))
                            <li class="nav-item">
                                <a href="{{ route('admin.backup') }}"
                                    class="nav-link {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-database"></i></span>
                                    <span class="nav-text">Backup do Sistema</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                        @can('view_audit_logs')
                            @if(is_menu_visible('menu_audit'))
                            <li class="nav-item">
                                <a href="{{ route('admin.audit.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.audit.index') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-shield-alt"></i></span>
                                    <span class="nav-text">Registo de Auditoria</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                        @can('view_logs')
                            @if(is_menu_visible('menu_logs'))
                            <li class="nav-item">
                                <a href="{{ route('admin.logs') }}"
                                    class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                                    <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                                    <span class="nav-text">Logs do Sistema</span>
                                </a>
                            </li>
                            @endif
                        @endcan
                    </ul>
                </div>
            @endcanany

            <!-- Minha Conta -->
            <div class="nav-section">
                <div class="nav-section-title">Minha Conta</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}"
                            class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
                            <span class="nav-text">Meu Perfil</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('notifications.index') }}"
                            class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <span class="nav-icon"><i class="fas fa-bell"></i></span>
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
            @endauth
        </div>

        @auth
        <div class="user-area">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ explode(' ', auth()->user()->name)[0] }}</div>
                    <div class="user-role">
                        @switch(auth()->user()->role)
                            @case('admin') Administrador @break
                            @case('secretary') Secretaria @break
                            @case('pedagogy') Seção Pedagógica @break
                            @case('teacher') Professor(a) @break
                            @case('parent') Encarregado @break
                            @default Usuário
                        @endswitch
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
        @else
        <div class="user-area">
            <a href="{{ route('login') }}" class="logout-btn" style="background: var(--accent); color: var(--primary-dark);">
                <i class="fas fa-sign-in-alt"></i>
                <span class="logout-text">Entrar</span>
            </a>
        </div>
        @endauth
    </nav>

    <!-- Conteúdo Principal -->
    <div class="main-content" id="main-content">
        <!-- Header Principal -->
        <header class="main-header">
            <div class="header-left">
                <button class="sidebar-toggle sidebar-toggle-btn" onclick="toggleSidebar()" id="sidebar-toggle-btn"
                    style="backdrop-filter: blur(10px); background: var(--primary); border: 1px solid var(--border-color); margin: 0px 15px 0 -15px; color: white; transition: background 0.3s ease, transform 0.2s ease;">
                    <i class="fas fa-chevron-left" id="toggle-icon"></i>
                </button>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">
                    <i class="@yield('page-title-icon', $titleIcon ?? 'fas fa-tachometer-alt')"></i>
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="header-right">
                <div class="header-search" onclick="openCommandPalette()" style="cursor: pointer;">
                    <input type="text" class="search-input" readonly style="cursor: pointer;"
                        placeholder="Pesquisar alunos, turmas... (Ctrl + K)">
                    <i class="fas fa-search search-icon"></i>
                </div>

                @auth
                <button class="header-btn" id="notification-btn" data-bs-toggle="dropdown" title="Notificações">
                    <i class="fas fa-bell"></i>
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="notification-badge">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 360px; max-width: 92vw; border-radius: 16px; overflow: hidden;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center p-3" style="background: var(--surface-bg); border-bottom: 1px solid var(--border-color);">
                        <strong style="color: var(--text-primary);">Notificações</strong>
                        <a href="#" class="text-decoration-none small font-semibold" style="color: var(--primary);"
                            onclick="markAllAsRead(event)">
                            Marcar todas como lidas
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>

                    @forelse(auth()->user()->notifications->take(5) as $notification)
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-3 px-3 {{ $notification->read_at ? '' : 'bg-light' }}"
                                href="{{ $notification->data['action_url'] ?? '#' }}"
                                onclick="markAsRead('{{ $notification->id }}', event)"
                                style="border-bottom: 1px solid var(--border-color-soft, #f1f5f9); white-space: normal;">
                                <div class="flex-shrink-0 me-3 mt-1">
                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} text-{{ $notification->data['type'] ?? 'info' }} fs-5"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0" style="min-width: 0; word-break: break-word; overflow-wrap: anywhere;">
                                    <div class="fw-bold mb-1 text-wrap" style="color: var(--text-primary); font-size: 12px; line-height: 1.35; word-break: break-word; overflow-wrap: anywhere;">
                                        {{ $notification->data['title'] ?? 'Notificação' }}
                                    </div>
                                    <div class="text-muted small mb-1 text-wrap" style="font-size: 11px; line-height: 1.4; word-break: break-word; overflow-wrap: anywhere;">
                                        {{ $notification->data['message'] ?? 'Nova notificação' }}
                                    </div>
                                    <small class="text-muted opacity-75 d-block" style="font-size: 10px;">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if (!$notification->read_at)
                                    <div class="flex-shrink-0 ms-2">
                                        <span class="badge rounded-pill px-2 py-1 text-[9px] font-bold" style="background: var(--primary); color: #fff;">NOVO</span>
                                    </div>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="dropdown-item-text text-center text-muted py-4">
                            <i class="fas fa-bell-slash fs-3 mb-2 d-block opacity-50"></i>
                            Nenhuma notificação registrada
                        </li>
                    @endforelse

                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>
                    <li class="text-center p-2.5" style="background: var(--surface-bg);">
                        <a href="{{ route('notifications.index') }}" class="small text-decoration-none font-bold" style="color: var(--primary);">
                            Ver todas as notificações <i class="fas fa-arrow-right text-xs ms-1"></i>
                        </a>
                    </li>
                </ul>
                @endauth

                <button class="header-btn" onclick="toggleTheme()" title="Alternar Tema">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>

                @auth
                <div class="dropdown">
                    <button class="header-btn" data-bs-toggle="dropdown" title="Menu do Usuário">
                        <div class="user-avatar"
                            style="width: 32px; height: 32px; font-size: 13px; margin: 0; background: var(--primary);">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="dropdown-header">
                            <strong>{{ explode(' ', auth()->user()->name)[0] }}</strong>
                            <small class="d-block text-muted">
                                @switch(auth()->user()->role)
                                    @case('admin')
                                        Administrador do Sistema
                                    @break

                                    @case('secretary')
                                        Secretaria Escolar
                                    @break

                                    @case('pedagogy')
                                        Seção Pedagógica
                                    @break

                                    @case('teacher')
                                        Professor(a)
                                    @break

                                    @case('parent')
                                        Encarregado de Educação
                                    @break

                                    @default
                                        Usuário do Sistema
                                @endswitch
                            </small>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-3"></i>Meu Perfil
                            </a>
                        </li>
                        @if (auth()->user()->role === 'parent')
                            <li>
                                <a class="dropdown-item" href="{{ route('parent.dashboard') }}">
                                    <i class="fas fa-child me-3"></i>Portal dos Pais
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->role === 'teacher')
                            <li>
                                <a class="dropdown-item" href="{{ route('teacher.dashboard') }}">
                                    <i class="fas fa-chalkboard-teacher me-3"></i>Portal do Professor
                                </a>
                            </li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="#" onclick="toggleTheme()">
                                <i class="fas fa-moon me-3" id="theme-icon-dropdown"></i>
                                <span id="theme-text">Modo Escuro</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-3"></i>Sair do Sistema
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <a href="{{ route('login') }}" class="header-btn" title="Entrar">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
                @endauth
            </div>
        </header>

        <!-- Área de Conteúdo -->
        <div class="content-area">
            @if(isset($licenseGraceWarning))
                <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4 d-flex align-items-center justify-content-between" role="alert">
                    <div>
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $licenseGraceWarning }}
                    </div>
                    <a href="{{ route('contacto') }}" class="btn btn-sm btn-danger font-semibold">Solicitar Renovação</a>
                </div>
            @elseif(isset($licenseTrialWarning))
                <div class="alert alert-warning shadow-sm border-0 rounded-3 mb-4 d-flex align-items-center justify-content-between" role="alert">
                    <div>
                        <i class="fas fa-clock me-2"></i>
                        {{ $licenseTrialWarning }}
                    </div>
                    <a href="{{ route('contacto') }}" class="btn btn-sm btn-dark font-semibold">Ver Planos & Licenças</a>
                </div>
            @endif
            <!-- Breadcrumb -->
            <nav class="school-breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @auth
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Início
                            </a>
                        @else
                            <a href="{{ route('welcome') }}">
                                <i class="fas fa-home"></i> Início
                            </a>
                        @endauth
                    </li>
                    @yield('breadcrumbs')
                    @if (!View::hasSection('breadcrumbs'))
                        <li class="breadcrumb-item active">Dashboard</li>
                    @endif
                </ol>
            </nav>

            <!-- Erros de Validação (mantidos inline - listas não são adequadas para toast) -->
            @if (isset($errors) && $errors->any())
                <div class="alert-school alert-danger-school alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Erros encontrados:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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


            <!-- Modal de Confirmação Reutilizável -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border: none; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow-lg);">
                        <div class="modal-header" style="background: var(--card-bg); border-bottom: 1px solid var(--border-color); padding: 18px 24px;">
                            <h5 class="modal-title d-flex align-items-center gap-2" style="font-size: 15px; font-weight: 700; color: var(--text-primary);">
                                <i class="fas fa-shield-alt" style="color: var(--warning);" id="confirmModalIcon"></i>
                                <span id="confirmModalTitle">Confirmar Ação</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body" style="padding: 24px; background: var(--card-bg);">
                            <p id="confirmModalMessage" style="color: var(--text-secondary); font-size: 14px; margin: 0;"></p>
                        </div>
                        <div class="modal-footer" style="background: var(--surface-bg); border-top: 1px solid var(--border-color); padding: 14px 24px; gap: 8px;">
                            <button type="button" class="btn btn-sm" data-bs-dismiss="modal"
                                style="background: var(--surface-bg); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 10px; padding: 8px 20px; font-weight: 600; font-size: 13px;">
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-sm" id="confirmModalBtn"
                                style="background: var(--danger); color: white; border: none; border-radius: 10px; padding: 8px 20px; font-weight: 600; font-size: 13px;">
                                <i class="fas fa-check me-1"></i> Confirmar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo da Página -->
            @yield('content')
        </div>

        <!-- Footer Escolar -->
        <footer class="school-footer">
            <div class="content-area">
                <div class="footer-shell py-2">
                    <div class="footer-brand">
                        <div class="brand-main"><i class="fas fa-school me-2"></i>{{ setting('school_name', 'Escola Secundária Visionários') }}</div>
                        <div class="brand-sub">
                            © {{ current_school_year() }} Sistema de Gestão Escolar • {{ setting('school_district', setting('district', 'Namacurra')) }}, {{ setting('school_province', setting('province', 'Zambézia')) }} • Desenvolvido por <a href="http://146.235.224.99/" target="_blank" class="no-underline text-emerald-600 dark:text-emerald-400 hover:underline font-bold">Filipe dos Santos</a>
                        </div>
                    </div>
                    <div class="footer-meta">
                        <span class="footer-pill"><i class="fas fa-code-branch"></i>v1.0.0</span>
                        <a href="mailto:{{ setting('email', 'contacto@visionarios.co.mz') }}" class="footer-link">
                            <i class="fas fa-headset"></i>Suporte Técnico
                        </a>
                        <a href="#" class="footer-link" onclick="showHelpModal(); return false;">
                            <i class="fas fa-book-open"></i>Manual do Sistema
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // ===== VARIÁVEIS GLOBAIS =====
        let sidebarCollapsed = localStorage.getItem('school-sidebar-collapsed') === 'true';
        let mobileMenuOpen = false;

        // ===== GERENCIADOR DE SIDEBAR =====
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleIcon = document.getElementById('toggle-icon');

            if (window.innerWidth >= 1200) {
                sidebarCollapsed = !sidebarCollapsed;
                localStorage.setItem('school-sidebar-collapsed', sidebarCollapsed);

                if (sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('collapsed');
                    toggleIcon.className = 'fas fa-chevron-right';
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                    toggleIcon.className = 'fas fa-chevron-left';
                }
            }
        }

        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            mobileMenuOpen = !mobileMenuOpen;

            if (mobileMenuOpen) {
                sidebar.classList.add('mobile-visible');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('mobile-visible');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        // ===== GERENCIADOR DE TEMA =====
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('school-theme', newTheme);

            const icons = document.querySelectorAll('#theme-icon, #theme-icon-dropdown');
            const text = document.getElementById('theme-text');

            icons.forEach(icon => {
                icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            });

            if (text) {
                text.textContent = newTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }
        }

        // ===== SISTEMA DE NOTIFICAÇÕES =====
        async function markAsRead(notificationId, event) {
            if (event) event.preventDefault();

            try {
                const response = await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        const count = parseInt(badge.textContent) - 1;
                        if (count <= 0) {
                            badge.remove();
                        } else {
                            badge.textContent = count;
                        }
                    }

                    const item = event.target.closest('.dropdown-item');
                    if (item) {
                        item.classList.remove('bg-light');
                        const newBadge = item.querySelector('.badge.bg-primary');
                        if (newBadge) newBadge.remove();
                    }
                }
            } catch (error) {
                console.error('Erro ao marcar notificação:', error);
            }
        }

        async function markAllAsRead(event) {
            if (event) event.preventDefault();

            try {
                const response = await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) badge.remove();

                    document.querySelectorAll('.dropdown-item.bg-light').forEach(item => {
                        item.classList.remove('bg-light');
                    });
                    document.querySelectorAll('.badge.bg-primary').forEach(badge => badge.remove());

                    showToast('Todas as notificações foram marcadas como lidas', 'success');
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro ao marcar notificações', 'error');
            }
        }

        // ===== SISTEMA DE TOAST =====
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const iconMap = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };

            const colorMap = {
                success: 'text-bg-success',
                error: 'text-bg-danger',
                warning: 'text-bg-warning',
                info: 'text-bg-primary'
            };

            const toastId = 'toast-' + Date.now();
            const toastHtml = `
                <div class="toast ${colorMap[type]} toast-school" role="alert" id="${toastId}">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-${iconMap[type]} me-2"></i>
                        <span class="flex-grow-1">${message}</span>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', toastHtml);

            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        }

        // ===== MODAL DE AJUDA =====
        function showHelpModal() {
            const modalHtml = `
                <div class="modal fade" id="helpModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background: var(--primary); color: white;">
                                <h5 class="modal-title">
                                    <i class="fas fa-question-circle me-2"></i>
                                    Manual do Sistema {{ setting('school_short_name', 'ZamEdu') }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-user-graduate text-primary me-2"></i>Para Encarregados</h6>
                                        <ul class="list-unstyled">
                                            <li>• Acompanhar notas dos filhos</li>
                                            <li>• Visualizar presenças</li>
                                            <li>• Efetuar pagamentos</li>
                                            <li>• Receber comunicados</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-chalkboard-teacher text-primary me-2"></i>Para Professores</h6>
                                        <ul class="list-unstyled">
                                            <li>• Marcar presenças</li>
                                            <li>• Lançar avaliações</li>
                                            <li>• Comunicar com pais</li>
                                            <li>• Solicitar licenças</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <p class="text-muted">
                                        Para mais informações, entre em contato com o suporte técnico:
                                        <br><strong>suporte@visionarios.co.mz</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const existingModal = document.getElementById('helpModal');
            if (existingModal) existingModal.remove();

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('helpModal'));
            modal.show();
        }

        // ===== PESQUISA GLOBAL =====
        document.querySelector('.search-input')?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query.length > 2) {
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            }
        });

        // ===== INICIALIZAÇÃO =====
        document.addEventListener('DOMContentLoaded', function() {
            // Aplicar tema salvo
            const savedTheme = localStorage.getItem('school-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);

            const icons = document.querySelectorAll('#theme-icon, #theme-icon-dropdown');
            const text = document.getElementById('theme-text');

            icons.forEach(icon => {
                icon.className = savedTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            });

            if (text) {
                text.textContent = savedTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }

            // Aplicar estado do sidebar
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleIcon = document.getElementById('toggle-icon');

            if (window.innerWidth >= 1200 && sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                toggleIcon.className = 'fas fa-chevron-right';
            }

            // Event listeners
            document.getElementById('sidebar-overlay')?.addEventListener('click', () => {
                toggleMobileMenu();
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1200) {
                    if (mobileMenuOpen) toggleMobileMenu();
                    if (mainContent) mainContent.classList.remove('expanded');
                } else {
                    if (mainContent) mainContent.classList.add('expanded');
                }
            });

            // Auto-hide validation alerts after 12 seconds
            setTimeout(() => {
                document.querySelectorAll('.alert-school').forEach(alert => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert?.close();
                });
            }, 12000);

            // ===== DELEGATED CONFIRM SYSTEM =====
            // Intercepts forms with data-confirm attribute and shows styled modal
            document.addEventListener('submit', function(e) {
                const form = e.target;
                const msg = form.getAttribute('data-confirm');
                if (!msg) return;
                if (form._confirmed) { form._confirmed = false; return; }
                e.preventDefault();
                confirmAction(msg, function() {
                    form._confirmed = true;
                    form.submit();
                });
            });
            // Intercepts buttons/links with data-confirm attribute
            document.addEventListener('click', function(e) {
                const el = e.target.closest('[data-confirm]');
                if (!el || el.tagName === 'FORM') return;
                if (el.closest('form[data-confirm]')) return; // form-level confirm handles it
                if (el._confirmed) { el._confirmed = false; return; }
                e.preventDefault();
                confirmAction(el.getAttribute('data-confirm'), function() {
                    el._confirmed = true;
                    el.click();
                });
            });

            console.log('✅ ZamEdu Sistema Escolar iniciado com sucesso!');
        });

        // ===== MODAL DE CONFIRMAÇÃO =====
        let _confirmCallback = null;
        function confirmAction(message, callback, options = {}) {
            const modal = document.getElementById('confirmModal');
            if (!modal) { if (confirm(message)) callback(); return; }
            document.getElementById('confirmModalMessage').textContent = message;
            document.getElementById('confirmModalTitle').textContent = options.title || 'Confirmar Ação';
            const btn = document.getElementById('confirmModalBtn');
            btn.innerHTML = '<i class="fas fa-check me-1"></i> ' + (options.confirmText || 'Confirmar');
            if (options.danger === false) {
                btn.style.background = 'var(--primary)';
            } else {
                btn.style.background = 'var(--danger)';
            }
            _confirmCallback = callback;
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
        document.getElementById('confirmModalBtn')?.addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal?.hide();
            if (_confirmCallback) { _confirmCallback(); _confirmCallback = null; }
        });

        // ===== COMMAND PALETTE (CTRL+K) =====
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                openCommandPalette();
            }
        });

        let cpModalInstance = null;
        function openCommandPalette() {
            const el = document.getElementById('commandPaletteModal');
            if (!el) return;
            if (!cpModalInstance) {
                cpModalInstance = new bootstrap.Modal(el);
            }
            cpModalInstance.show();
            setTimeout(() => {
                const input = document.getElementById('commandPaletteInput');
                if (input) {
                    input.focus();
                    input.value = '';
                    fetchCommandResults('');
                }
            }, 200);
        }

        let cpDebounce = null;
        document.addEventListener('input', function(e) {
            if (e.target && e.target.id === 'commandPaletteInput') {
                clearTimeout(cpDebounce);
                const query = e.target.value;
                cpDebounce = setTimeout(() => {
                    fetchCommandResults(query);
                }, 150);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.target && e.target.id === 'commandPaletteInput' && e.key === 'Enter') {
                const query = e.target.value.trim();
                if (query.length > 0) {
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            }
        });

        function fetchCommandResults(query) {
            const container = document.getElementById('commandPaletteResults');
            if (!container) return;
            container.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> A pesquisar...</div>';
            fetch(`/search/quick?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        container.innerHTML = `
                            <div class="text-center py-4 text-muted">
                                <p class="mb-2">Nenhum resultado direto encontrado.</p>
                                ${query ? `<a href="/search?q=${encodeURIComponent(query)}" class="btn btn-sm btn-primary-school">Pesquisa Geral completa</a>` : ''}
                            </div>
                        `;
                        return;
                    }
                    let html = '<div class="list-group list-group-flush rounded-3">';
                    data.forEach(item => {
                        html += `
                            <a href="${item.url}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2.5 border-0 rounded-3 mb-1">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                        <i class="${item.icon}"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-slate-800 mb-0 text-sm">${item.title}</h6>
                                        <small class="text-xs text-muted">${item.subtitle}</small>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-xs text-slate-300"></i>
                            </a>
                        `;
                    });
                    if (query && query.length >= 2) {
                        html += `
                            <a href="/search?q=${encodeURIComponent(query)}" class="list-group-item list-group-item-action text-center text-primary fw-bold p-2.5 border-0 rounded-3 mt-2 bg-primary-subtle">
                                <i class="fas fa-search me-1"></i> Ver todos os resultados para "${query}" &rarr;
                            </a>
                        `;
                    }
                    html += '</div>';
                    container.innerHTML = html;
                })
                .catch(() => {
                    container.innerHTML = '<div class="text-center py-4 text-rose-500 text-xs">Erro ao carregar dados da pesquisa.</div>';
                });
        }

        // ===== API GLOBAL =====
        window.ZamEdu = {
            showToast,
            confirmAction,
            toggleSidebar,
            toggleTheme,
            markAsRead,
            markAllAsRead,
            showHelpModal,
            openCommandPalette,
            version: '1.0.0'
        };
    </script>

    <!-- COMMAND PALETTE (CTRL+K) MODAL -->
    <div class="modal fade" id="commandPaletteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="p-3 border-bottom bg-white d-flex align-items-center gap-2">
                    <i class="fas fa-search text-emerald-600 text-lg ms-2"></i>
                    <input type="text" id="commandPaletteInput" class="form-control border-0 shadow-none text-base font-semibold"
                        placeholder="Digite para pesquisar alunos, professores, turmas ou atalhos (Ctrl + K)..." autofocus>
                    <kbd class="badge bg-slate-100 text-slate-600 border px-2 py-1 text-xs me-2">ESC</kbd>
                </div>
                <div class="modal-body p-2" style="max-height: 380px; overflow-y: auto;" id="commandPaletteResults">
                    <!-- Dynamic items -->
                </div>
                <div class="modal-footer bg-slate-50 border-top py-2 px-3 justify-content-between text-xs text-muted">
                    <span><kbd class="me-1">↑↓</kbd> Navegar</span>
                    <span><kbd class="me-1">↵</kbd> Selecionar</span>
                    <span><kbd class="me-1">ESC</kbd> Fechar</span>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')

    {{-- SweetAlert2 + interceptador de flash messages --}}
    @include('partials.sweetalert')

    {{-- Alpine.js para o widget AI flutuante --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    {{-- Widget AI flutuante --}}
    @include('partials.ai-widget')
</body>

</html>
