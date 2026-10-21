<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Http\Request;

class BranchSwitchController extends Controller
{
    /**
     * Alternar a filial activa para a Escola dos Visionários ou visão consolidada.
     */
    public function switch(Request $request, $schoolId)
    {
        $user = auth()->user();

        if ($schoolId === 'all') {
            session([
                'active_branch' => 'all',
                'active_branch_name' => 'Rede Visionários (Consolidado)',
                'active_school_id' => null,
            ]);
            SchoolContext::clear();

            return redirect()->back()->with('success', 'Visão consolidada da Rede Visionários ativada.');
        }

        $school = School::where('id', $schoolId)->firstOrFail();

        // Segurança multi-tenant: apenas permitir alternar para filiais da rede Visionários (a menos que seja super_admin)
        if (!$user->hasRole('super_admin')) {
            $allowedSchoolIds = School::where('code', 'like', 'VIS-%')
                ->orWhere('name', 'like', '%Visionários%')
                ->pluck('id')
                ->toArray();

            if (!in_array((int) $school->id, $allowedSchoolIds)) {
                abort(403, 'Acesso não autorizado: Esta instituição não pertence à rede da Escola dos Visionários.');
            }
        }

        session([
            'active_branch' => (int) $school->id,
            'active_branch_name' => $school->name,
            'active_school_id' => (int) $school->id,
        ]);

        SchoolContext::setSchoolId((int) $school->id);

        return redirect()->back()->with('success', "Filial ativa alterada para: {$school->name}");
    }
}

