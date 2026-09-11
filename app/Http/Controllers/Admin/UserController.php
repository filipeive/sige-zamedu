<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\ParentModel;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->authorize('manage_users');

        $query = User::with('roles');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();

        return view('admin.users.index', compact('users', 'roles', 'totalUsers', 'activeUsers'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create_users');

        $roles = Role::where('name', '!=', 'super_admin')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->authorize('create_users');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'phone' => $request->phone,
                'email_verified_at' => now(),
            ]);

            // Atribuir role
            $user->assignRole($request->role);

            // Criar perfil específico baseado no role
            $this->createUserProfile($user, $request->role, $request->all());

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view_users');

        $user->load(['roles', 'teacher', 'parent', 'school.province', 'school.district', 'school.zip', 'province', 'district', 'zip']);
        $loginHistory = $this->getLoginHistory($user);
        $auditLogs = \App\Models\AuditLog::where('user_id', $user->id)->latest()->take(10)->get();

        return view('admin.users.show', compact('user', 'loginHistory', 'auditLogs'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $this->authorize('edit_users');

        // Prevenir edição de super_admin por não super_admins
        if ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Não tem permissão para editar super administradores.');
        }

        $roles = Role::where('name', '!=', 'super_admin')->get();
        $user->load(['roles', 'teacher', 'parent']);

        // Passar dados antigos para a view
        $userData = [
            'user' => $user,
            'roles' => $roles,
            'current_role' => $user->roles->first()->name ?? '',
            'teacher_data' => $user->teacher,
            'parent_data' => $user->parent
        ];

        return view('admin.users.edit', $userData);
    }
    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit_users');

        // Prevenir edição de super_admin por não super_admins
        if ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Não tem permissão para editar super administradores.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status,
                'phone' => $request->phone,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Atualizar role
            $user->syncRoles([$request->role]);

            // Atualizar perfil específico
            $this->updateUserProfile($user, $request->role, $request->all());

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete_users');

        // Prevenir exclusão de super_admin
        if ($user->hasRole('super_admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Não é possível excluir super administradores.');
        }

        // Prevenir exclusão do próprio usuário
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Não pode excluir a sua própria conta.');
        }

        try {
            DB::beginTransaction();

            // Remover perfil específico
            $this->deleteUserProfile($user);

            // Excluir usuário
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuário excluído com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('edit_users');

        // Prevenir desativação de super_admin por não super_admins
        if ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')) {
            return response()->json(['error' => 'Não tem permissão para desativar super administradores.'], 403);
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);

        $newStatus = $user->status === 'active' ? 'ativada' : 'desativada';

        return response()->json([
            'success' => true,
            'message' => "Conta {$newStatus} com sucesso!",
            'status' => $user->status
        ]);
    }

    /**
     * Create user profile based on role.
     */
    private function createUserProfile(User $user, string $role, array $data)
    {
        switch ($role) {
            case 'teacher':
                Teacher::create([
                    'user_id' => $user->id,
                    'first_name' => explode(' ', $user->name)[0],
                    'last_name' => implode(' ', array_slice(explode(' ', $user->name), 1)),
                    'email' => $user->email,
                    'phone' => $data['phone'] ?? null,
                    'hire_date' => now(),
                    'qualification' => $data['qualification'] ?? 'Licenciatura',
                    'specialization' => $data['specialization'] ?? 'Geral',
                    'status' => 'active',
                ]);
                break;

            case 'parent':
                ParentModel::create([
                    'user_id' => $user->id,
                    'first_name' => explode(' ', $user->name)[0],
                    'last_name' => implode(' ', array_slice(explode(' ', $user->name), 1)),
                    'email' => $user->email,
                    'phone' => $data['phone'] ?? null,
                    'relationship' => $data['relationship'] ?? 'Other',
                    'status' => 'active',
                ]);
                break;
        }
    }

    /**
     * Update user profile based on role.
     */
    private function updateUserProfile(User $user, string $role, array $data)
    {
        switch ($role) {
            case 'teacher':
                $teacher = $user->teacher;
                if ($teacher) {
                    $teacher->update([
                        'first_name' => explode(' ', $user->name)[0],
                        'last_name' => implode(' ', array_slice(explode(' ', $user->name), 1)),
                        'email' => $user->email,
                        'phone' => $data['phone'] ?? null,
                        'qualification' => $data['qualification'] ?? $teacher->qualification,
                        'specialization' => $data['specialization'] ?? $teacher->specialization,
                    ]);
                }
                break;

            case 'parent':
                $parent = $user->parent;
                if ($parent) {
                    $parent->update([
                        'first_name' => explode(' ', $user->name)[0],
                        'last_name' => implode(' ', array_slice(explode(' ', $user->name), 1)),
                        'email' => $user->email,
                        'phone' => $data['phone'] ?? null,
                        'relationship' => $data['relationship'] ?? $parent->relationship,
                    ]);
                }
                break;
        }
    }

    /**
     * Delete user profile.
     */
    private function deleteUserProfile(User $user)
    {
        if ($user->teacher) {
            $user->teacher->delete();
        }

        if ($user->parent) {
            $user->parent->delete();
        }
    }

    /**
     * Get user login history (simulado).
     */
    private function getLoginHistory(User $user)
    {
        // Aqui você pode integrar com um sistema de logs real
        return [
            [
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'login_at' => now()->subHours(2),
                'success' => true
            ],
            [
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'login_at' => now()->subDays(1),
                'success' => true
            ],
        ];
    }
}