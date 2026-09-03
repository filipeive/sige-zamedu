<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFinancialAccess
{
    /**
     * Bloqueia acesso ao módulo financeiro para escolas públicas.
     * Escolas públicas não gerem propinas/mensalidades.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (is_public_school()) {
            $restrictedRoutes = [
                'payments.generate-monthly-fees',
                'payments.overdue',
            ];

            if ($request->routeIs($restrictedRoutes)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'Cobrança de mensalidades/propinas não é aplicável a escolas públicas.'
                    ], 403);
                }

                return redirect()->route('dashboard')->with('warning',
                    'Escolas públicas gerem apenas Taxas de Matrícula, Exames e Despesas (sem mensalidades recorrentes).'
                );
            }
        }

        return $next($request);
    }
}
