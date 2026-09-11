<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'classes']);

        // Filtro por escola (super admin / territorial)
        if ($request->filled('school_id')) {
            $query->whereHas('classes', function ($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }

        // Filtros
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('bi_number', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->paginate(20);
        $schools = School::orderBy('name')->get();
        
        return view('teachers.index', compact('teachers', 'schools'));
    }

    public function create()
    {
        $classes = ClassRoom::active()->currentYear()->get();
        return view('teachers.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'required|string|max:20',
            'bi_number' => 'required|string|unique:teachers,bi_number',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'hire_date' => 'required|date',
            'qualification' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'address' => 'required|string',
            'create_user_account' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Criar o professor
            $teacher = Teacher::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'bi_number' => $request->bi_number,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'hire_date' => $request->hire_date,
                'qualification' => $request->qualification,
                'specialization' => $request->specialization,
                'salary' => $request->salary,
                'address' => $request->address,
                'status' => 'active',
            ]);

            // Criar conta de usuário se solicitado
            if ($request->create_user_account) {
                $user = User::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make('password123'), // Senha padrão
                    'email_verified_at' => now(),
                ]);

                // Atribuir papel de professor
                $teacherRole = Role::where('name', 'teacher')->first();
                if ($teacherRole) {
                    $user->assignRole($teacherRole);
                }

                // Associar usuário ao professor
                $teacher->update(['user_id' => $user->id]);
            }

            DB::commit();

            return redirect()->route('teachers.show', $teacher->id)
                ->with('success', 'Professor cadastrado com sucesso!' . 
                      ($request->create_user_account ? ' Conta de usuário criada.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar professor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'classes', 'classSubjects.subject', 'leaveRequests']);
        $classes = $teacher->classes()->active()->currentYear()->get();
        $allClasses = ClassRoom::active()->orderBy('name')->get();
        
        // Estatísticas do professor
        $stats = [
            'total_classes' => $teacher->classes()->count(),
            'current_classes' => $teacher->classes()->active()->currentYear()->count(),
            'total_subjects' => $teacher->classSubjects()->count(),
            'pending_leave_requests' => $teacher->leaveRequests()->pending()->count(),
        ];

        return view('teachers.show', compact('teacher', 'classes', 'allClasses', 'stats'));
    }

    public function edit(Teacher $teacher)
    {
        $classes = ClassRoom::active()->currentYear()->get();
        return view('teachers.edit', compact('teacher', 'classes'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'required|string|max:20',
            'bi_number' => 'required|string|unique:teachers,bi_number,' . $teacher->id,
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'hire_date' => 'required|date',
            'qualification' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'address' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $teacher->update($request->all());

            // Atualizar nome do usuário se existir
            if ($teacher->user) {
                $teacher->user->update([
                    'name' => $request->first_name . ' ' . $request->last_name
                ]);
            }

            return redirect()->route('teachers.show', $teacher->id)
                ->with('success', 'Professor atualizado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar professor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            // Verificar se o professor tem turmas atribuídas
            if ($teacher->classes()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Não é possível excluir o professor. Existem turmas atribuídas a ele.');
            }

            $teacher->delete();

            return redirect()->route('teachers.index')
                ->with('success', 'Professor excluído com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir professor: ' . $e->getMessage());
        }
    }

    // Ativar/Desativar professor
    public function toggleStatus(Teacher $teacher)
    {
        try {
            $newStatus = $teacher->status == 'active' ? 'inactive' : 'active';
            $teacher->update(['status' => $newStatus]);

            $message = $newStatus == 'active' ? 'Professor ativado com sucesso!' : 'Professor desativado com sucesso!';

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao alterar status: ' . $e->getMessage());
        }
    }

    // Atribuir turma ao professor
    public function assignClass(Request $request, Teacher $teacher)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'role' => 'required|in:main,assistant',
        ]);

        try {
            $class = ClassRoom::findOrFail($request->class_id);

            // Se for professor principal, atualizar teacher_id na turma
            if ($request->role == 'main') {
                $class->update(['teacher_id' => $teacher->id]);
            }

            // Se a model/tabela TeacherClassAssignment existir, grava o registo
            if (class_exists(\App\Models\TeacherClassAssignment::class)) {
                \App\Models\TeacherClassAssignment::updateOrCreate([
                    'teacher_id' => $teacher->id,
                    'class_id' => $request->class_id,
                ], [
                    'role' => $request->role,
                    'assigned_date' => now(),
                    'is_active' => true,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Turma "' . $class->name . '" atribuída ao professor com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atribuir turma: ' . $e->getMessage());
        }
    }
}