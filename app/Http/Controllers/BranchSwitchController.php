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

        session([
            'active_branch' => (int) $school->id,
            'active_branch_name' => $school->name,
            'active_school_id' => (int) $school->id,
        ]);

        SchoolContext::setSchoolId((int) $school->id);

        return redirect()->back()->with('success', "Filial ativa alterada para: {$school->name}");
    }
}
