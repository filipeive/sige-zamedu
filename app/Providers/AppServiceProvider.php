<?php

namespace App\Providers;

use App\Notifications\Channels\SmsChannel;
use App\Services\Ai\AiManager;
use App\Services\Sms\SmsManager;
use App\Services\Sms\SmsNotificationService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsManager::class);
        $this->app->singleton(SmsNotificationService::class);
        $this->app->singleton(AiManager::class);
    }

    public function boot()
    {
        // Directives para permissões
        Blade::if('can', function ($permission) {
            return auth()->check() && auth()->user()->can($permission);
        });

        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('anyrole', function ($roles) {
            if (!auth()->check()) return false;

            foreach ($roles as $role) {
                if (auth()->user()->hasRole($role)) {
                    return true;
                }
            }
            return false;
        });

        // Canal de notificação 'sms' — registado em lazy para evitar dependências circulares
        \Illuminate\Support\Facades\Notification::extend('sms', function ($app) {
            return new SmsChannel($app->make(SmsManager::class));
        });

        // Log executed SQL queries for performance monitoring
        DB::listen(function ($query) {
            Log::debug('SQL Query', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });
    }
}