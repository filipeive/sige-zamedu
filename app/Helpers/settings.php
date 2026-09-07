<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

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
            $value = Cache::remember("setting_{$key}", 3600, function () use ($key) {
                $setting = Setting::where('key', $key)->first();
                return $setting ? $setting->value : null;
            });

            if ($value !== null && $value !== '') {
                return $value;
            }
        } catch (\Throwable $e) {
            // Em caso de falha de BD ou tabelas não criadas
        }

        return $default;
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
        } elseif (is_string($user)) {
            $role = $user;
        }

        $role = $role ?? 'guest';
        
        $savedVisibility = setting('menu_visibility_' . $role);

        if ($savedVisibility === null || $savedVisibility === '') {
            return true;
        }

        $allowedMenus = is_array($savedVisibility) ? $savedVisibility : json_decode($savedVisibility, true);
        if (!is_array($allowedMenus)) {
            return true;
        }

        return in_array($menuKey, $allowedMenus);
    }
}

