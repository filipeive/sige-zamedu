<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\License;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\SchoolContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminControlController extends Controller
{
    /**
     * Activar o Modo Suporte Técnico para operar no contexto de uma escola específica.
     */
    public function impersonate(Request $request, $schoolId)
    {
        $user = auth()->user();

        if (!$user || ($user->role !== 'super_admin' && !$user->hasRole('super_admin'))) {
            abort(403, 'Acesso reservado ao Super Administrador da Plataforma.');
        }

        $school = School::with(['province', 'district', 'zip'])->findOrFail($schoolId);

        session([
            'support_mode' => true,
            'support_mode_school_id' => $school->id,
            'support_mode_school_name' => $school->name,
            'support_mode_activated_at' => now()->toDateTimeString(),
        ]);

        SchoolContext::setSchoolId((int) $school->id);

        return redirect()->route('dashboard')->with('success', "🎧 MODO SUPORTE ATIVO: A operar na escola '{$school->name}' (#{$school->id}).");
    }

    /**
     * Sair do Modo Suporte Técnico e regressar ao Painel Central do Super Admin.
     */
    public function stopImpersonating(Request $request)
    {
        $schoolName = session('support_mode_school_name', 'Escola');

        session()->forget([
            'support_mode',
            'support_mode_school_id',
            'support_mode_school_name',
            'support_mode_activated_at',
        ]);

        SchoolContext::clear();

        return redirect()->route('dashboard')->with('info', "ℹ️ Saiu do Modo Suporte da escola '{$schoolName}'. Regressou ao Painel Central do Super Admin.");
    }
}
