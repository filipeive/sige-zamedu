<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Province;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OwnerNetworkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Listar as filiais da rede privada geridas pelo Dono / Diretor.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'director', 'super_admin'])) {
            abort(403, 'Acesso restrito ao Diretor Geral / Proprietário da instituição.');
        }

        $query = School::with(['province', 'district'])
            ->withCount([
                'students as active_students_count' => function ($q) {
                    $q->where('status', 'active');
                },
                'classRooms as classes_count' => function ($q) {
                    $q->where('is_active', true);
                }
            ]);

        if (!$user->hasRole('super_admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('code', 'like', 'VIS-%')
                  ->orWhere('name', 'like', '%Visionários%')
                  ->orWhere('id', $user->school_id);
            });
        }

        $branches = $query->orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $districts = District::orderBy('name')->get();

        return view('owner.branches.index', compact('branches', 'provinces', 'districts'));
    }

    /**
     * Criar uma nova filial na rede.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'director', 'super_admin'])) {
            abort(403, 'Acesso restrito ao Diretor Geral.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:schools,code',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'principal_name' => 'nullable|string|max:150',
            'province_id' => 'nullable|exists:provinces,id',
            'district_id' => 'nullable|exists:districts,id',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'VIS-' . strtoupper(Str::random(5));
        }

        // Zambézia como padrão
        if (empty($validated['province_id'])) {
            $zambezia = Province::where('name', 'like', '%Zambézia%')->first();
            $validated['province_id'] = $zambezia?->id ?? Province::first()?->id ?? 1;
        }

        $validated['type'] = 'private';
        $validated['is_active'] = true;

        $school = School::create($validated);

        return redirect()->route('owner.branches.index')
            ->with('success', "Nova filial '{$school->name}' registada com sucesso na Rede Visionários!");
    }

    /**
     * Atualizar dados de uma filial existente.
     */
    public function update(Request $request, School $school)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'director', 'super_admin'])) {
            abort(403, 'Acesso restrito ao Diretor Geral.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'principal_name' => 'nullable|string|max:150',
            'is_active' => 'required|boolean',
        ]);

        $school->update($validated);

        return redirect()->route('owner.branches.index')
            ->with('success', "Filial '{$school->name}' atualizada com sucesso!");
    }

    /**
     * Eliminar ou desativar uma filial.
     */
    public function destroy(School $school)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'director', 'super_admin'])) {
            abort(403, 'Acesso restrito ao Diretor Geral.');
        }

        $hasStudents = $school->students()->exists() || $school->enrollments()->exists();
        $hasClasses = $school->classRooms()->exists();

        if ($hasStudents || $hasClasses) {
            $school->update(['is_active' => false]);
            return redirect()->route('owner.branches.index')
                ->with('warning', "A filial '{$school->name}' possui turmas ou estudantes registados e por motivos de segurança foi DESATIVADA em vez de eliminada.");
        }

        $schoolName = $school->name;
        $school->delete();

        return redirect()->route('owner.branches.index')
            ->with('success', "Filial '{$schoolName}' eliminada com sucesso da rede.");
    }

    /**
     * Alternar status activo / inactivo de uma filial.
     */
    public function toggleStatus(School $school)
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'director', 'super_admin'])) {
            abort(403, 'Acesso restrito ao Diretor Geral.');
        }

        $school->update(['is_active' => !$school->is_active]);

        $statusStr = $school->is_active ? 'ativada' : 'desativada';
        return redirect()->route('owner.branches.index')
            ->with('success', "Filial '{$school->name}' foi {$statusStr} com sucesso.");
    }
}
