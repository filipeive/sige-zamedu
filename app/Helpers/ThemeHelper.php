<?php

namespace App\Helpers;

class ThemeHelper
{
    /**
     * Obter todas as variáveis de CSS institucionais parametrizadas para o Theme Engine.
     */
    public static function getCssVariables(): string
    {
        $primary = setting('primary_color', '#007A3D');
        $secondary = setting('secondary_color', '#034522');
        $accent = setting('accent_color', '#FCD116');
        $borderRadius = setting('border_radius', '16px');

        return "
        :root {
            /* Branding Tokens Institucionais */
            --primary: {$primary};
            --primary-dark: color-mix(in srgb, var(--primary) 82%, #000);
            --primary-light: color-mix(in srgb, var(--primary) 18%, #fff);
            --primary-soft: color-mix(in srgb, var(--primary) 10%, #fff);
            
            --secondary: {$secondary};
            --secondary-dark: color-mix(in srgb, var(--secondary) 82%, #000);
            --secondary-light: color-mix(in srgb, var(--secondary) 18%, #fff);
            --secondary-soft: color-mix(in srgb, var(--secondary) 10%, #fff);
            
            --accent: {$accent};
            --accent-light: color-mix(in srgb, var(--accent) 20%, #fff);

            /* Cores Semânticas de Estado (Harmonizadas) */
            --success: #10B981;
            --success-dark: #047857;
            --success-soft: #D1FAE5;
            
            --warning: #F59E0B;
            --warning-dark: #B45309;
            --warning-soft: #FEF3C7;
            
            --danger: #EF4444;
            --danger-dark: #B91C1C;
            --danger-soft: #FEE2E2;
            
            --info: #06B6D4;
            --info-dark: #0E7490;
            --info-soft: #CFFAFE;

            /* Superfícies & Layout (Modo Claro) */
            --content-bg: #F4F6FA;
            --card-bg: #FFFFFF;
            --surface-bg: #F8FAFC;
            --sidebar-bg: #FFFFFF;
            --sidebar-text: #334155;
            --sidebar-text-muted: #94A3B8;
            --sidebar-active-bg: color-mix(in srgb, var(--primary) 12%, #fff);
            --sidebar-active-text: var(--primary);
            --sidebar-hover: #F8FAFC;
            
            --border-color: #E2E8F0;
            --border-color-soft: #F1F5F9;
            
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --text-muted: #94A3B8;
            
            --input-bg: #FFFFFF;
            --input-border: #CBD5E1;
            --input-focus-border: var(--primary);
            --input-focus-ring: color-mix(in srgb, var(--primary) 25%, transparent);

            /* Dimensões & Sombras */
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 68px;
            --header-height: 65px;
            --border-radius: {$borderRadius};
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.06);
            --transition-base: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-bs-theme=\"dark\"] {
            /* Superfícies & Layout (Modo Escuro) */
            --content-bg: #0B132B;
            --card-bg: #1C2541;
            --surface-bg: #151E3D;
            --sidebar-bg: #1C2541;
            --sidebar-text: #E2E8F0;
            --sidebar-text-muted: #64748B;
            --sidebar-active-bg: color-mix(in srgb, var(--primary) 30%, transparent);
            --sidebar-active-text: #34D399;
            --sidebar-hover: #2B385B;
            
            --border-color: #2D3748;
            --border-color-soft: #1E293B;
            
            --text-primary: #F8FAFC;
            --text-secondary: #CBD5E1;
            --text-muted: #64748B;
            
            --input-bg: #151E3D;
            --input-border: #334155;
            --input-focus-border: var(--primary);
            --input-focus-ring: color-mix(in srgb, var(--primary) 40%, transparent);
            
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.35);
        }
        ";
    }
}
