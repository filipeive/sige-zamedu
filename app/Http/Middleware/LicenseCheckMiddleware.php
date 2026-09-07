<?php

namespace App\Http\Middleware;

use App\Models\License;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LicenseCheckMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth()->user();
            $schoolId = $user?->school_id;
            $license = null;

            if ($schoolId) {
                $license = License::where('school_id', $schoolId)->first();
            }

            if (! $license) {
                $license = License::first();
            }
        } catch (\Throwable $e) {
            $license = null;
        }

        if ($license) {
            $status = $license->evaluateStatus();

            // Rotas isentas de bloqueio por suspensão de licença
            $exemptRoutes = [
                'logout',
                'license.suspended',
                'admin.license',
                'admin.license.update',
                'login',
            ];

            if ($status === 'suspended') {
                if (!$request->routeIs($exemptRoutes)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'message' => 'A licença da aplicação encontra-se suspensa. Por favor, entre em contacto com a FDS Software.'
                        ], 403);
                    }

                    return redirect()->route('license.suspended');
                }
            }

            if ($status === 'trial' || ($license->is_trial && $status === 'active')) {
                $daysRemaining = max(0, (int) now()->diffInDays($license->expires_at, false));
                view()->share('licenseTrialWarning', "Modo de Teste / Avaliação: Restam {$daysRemaining} dias de acesso completo.");
            }

            if ($status === 'grace_period') {
                $daysRemaining = max(0, (int) now()->diffInDays($license->expires_at->addDays($license->grace_period_days), false));
                view()->share('licenseGraceWarning', "Atenção: A licença expirou. Encontra-se no período de tolerância ({$daysRemaining} dias restantes). Contacte a FDS Software.");
            }
        }

        return $next($request);
    }
}
