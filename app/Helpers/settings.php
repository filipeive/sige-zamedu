<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

require_once __DIR__ . '/SchoolHelper.php';

if (!function_exists('setting')) {
    /**
     * Obter ou definir valor de configuração.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app(Setting::class);
        }

        try {
            return Setting::get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (!function_exists('is_menu_visible')) {
    /**
     * Verificar se um menu específico está visível para a função/perfil do utilizador atual.
     *
     * @param string $menuKey
     * @param mixed $user
     * @return bool
     */
    function is_menu_visible($menuKey, $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return true;
        }

        $role = null;
        if (is_object($user)) {
            $role = $user->role ?? (method_exists($user, 'roles') ? ($user->roles->first()?->name ?? null) : null);
            
            // Super Admin sempre tem visibilidade total
            if (method_exists($user, 'hasRole')) {
                if ($user->hasRole('super_admin') || $user->hasRole('superadmin')) {
                    return true;
                }
            }
            if (($user->role ?? null) === 'superadmin' || ($user->role ?? null) === 'super_admin') {
                return true;
            }

            // Proteção contra Lockout: Configurações Gerais e Gestão de Usuários sempre visíveis para admin
            $isAdmin = (($user->role ?? null) === 'admin') || (method_exists($user, 'hasRole') && $user->hasRole('admin'));
            if ($isAdmin && in_array($menuKey, ['menu_settings', 'menu_users'])) {
                return true;
            }

            // Menu Licenças e Ausências visível para professores e admins
            if ($menuKey === 'menu_leave_requests') {
                if ($isAdmin || $role === 'teacher' || (method_exists($user, 'hasRole') && $user->hasRole('teacher'))) {
                    return true;
                }
            }
        }

        // Se uma configuração explícita existir na BD, respeitá-la
        $settingValue = setting($menuKey, null);
        if ($settingValue !== null) {
            return (bool) $settingValue;
        }

        return true;
    }
}
