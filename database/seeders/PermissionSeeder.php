<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Resetar cache de roles e permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Helper para criar permissão com segurança
        $createPermission = function ($name) {
            return Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        };

        // ========== CRIAR PERMISSÕES ==========

        // Dashboard e Acesso Geral
        $createPermission('access_dashboard');
        $createPermission('view_statistics');

        // Gestão de Alunos
        $createPermission('view_students');
        $createPermission('create_students');
        $createPermission('edit_students');
        $createPermission('delete_students');
        $createPermission('manage_students');
        $createPermission('view_own_students');

        // Gestão de Professores
        $createPermission('view_teachers');
        $createPermission('create_teachers');
        $createPermission('edit_teachers');
        $createPermission('delete_teachers');
        $createPermission('manage_teachers');

        // Gestão de Turmas
        $createPermission('view_classes');
        $createPermission('create_classes');
        $createPermission('edit_classes');
        $createPermission('delete_classes');
        $createPermission('manage_classes');
        $createPermission('view_own_classes');

        // Gestão de Disciplinas
        $createPermission('view_subjects');
        $createPermission('create_subjects');
        $createPermission('edit_subjects');
        $createPermission('delete_subjects');
        $createPermission('manage_subjects');

        // Gestão de Matrículas
        $createPermission('view_enrollments');
        $createPermission('create_enrollments');
        $createPermission('edit_enrollments');
        $createPermission('delete_enrollments');
        $createPermission('manage_enrollments');

        // Gestão de Presenças
        $createPermission('view_attendances');
        $createPermission('mark_attendances');
        $createPermission('manage_attendances');
        $createPermission('view_own_class_attendances');

        // Gestão de Notas/Avaliações
        $createPermission('view_grades');
        $createPermission('create_grades');
        $createPermission('edit_grades');
        $createPermission('delete_grades');
        $createPermission('manage_grades');
        $createPermission('view_own_grades');
        $createPermission('grade_own_subjects');

        // Gestão Financeira
        $createPermission('view_payments');
        $createPermission('create_payments');
        $createPermission('edit_payments');
        $createPermission('delete_payments');
        $createPermission('manage_payments');
        $createPermission('view_own_payments');
        $createPermission('generate_payment_references');
        $createPermission('process_payments');
        $createPermission('manage_expenses');
        $createPermission('view_financial_reports');

        // Gestão de Eventos
        $createPermission('view_events');
        $createPermission('create_events');
        $createPermission('edit_events');
        $createPermission('delete_events');
        $createPermission('manage_events');

        // Comunicação
        $createPermission('send_notifications');
        $createPermission('send_bulk_notifications');
        $createPermission('manage_communications');

        // Relatórios
        $createPermission('view_reports');
        $createPermission('view_basic_reports');
        $createPermission('view_financial_reports');
        $createPermission('view_academic_reports');
        $createPermission('export_reports');

        // Gestão de Usuários e Sistema
        $createPermission('manage_users');
        $createPermission('create_users');
        $createPermission('edit_users');
        $createPermission('delete_users');
        $createPermission('view_users');

        // Configurações do Sistema
        $createPermission('manage_settings');
        $createPermission('backup_system');
        $createPermission('view_logs');
        $createPermission('view_audit_logs');
        $createPermission('manage_school_year');

        // Licenças de Staff
        $createPermission('manage_leave_requests');
        $createPermission('approve_leave_requests');
        $createPermission('create_leave_requests');
        $createPermission('view_leave_requests');

        // Portaria / Segurança
        $createPermission('view_gatekeeper_logs');

        // Observações e Registros
        $createPermission('manage_observations');
        $createPermission('create_observations');
        $createPermission('view_observations');
        $createPermission('manage_student_records');

        // Território e Inspeção (Província / Distrito / ZIP)
        $createPermission('view_territorial_dashboard');
        $createPermission('view_province_reports');
        $createPermission('view_district_reports');
        $createPermission('view_zip_reports');

        // ========== CRIAR ROLES E ATRIBUIR PERMISSÕES ==========

        $createRole = function ($name) {
            return Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        };

        // 1. SUPER ADMIN - Acesso total ao sistema
        $superAdminRole = $createRole('super_admin');
        $superAdminRole->givePermissionTo(Permission::all());

        // 2. ADMIN
        $adminRole = $createRole('admin');
        $adminRole->syncPermissions([
            'access_dashboard', 'view_statistics',
            'view_students', 'create_students', 'edit_students', 'delete_students', 'manage_students',
            'view_teachers', 'create_teachers', 'edit_teachers', 'delete_teachers', 'manage_teachers',
            'view_classes', 'create_classes', 'edit_classes', 'delete_classes', 'manage_classes',
            'view_subjects', 'create_subjects', 'edit_subjects', 'delete_subjects', 'manage_subjects',
            'view_enrollments', 'create_enrollments', 'edit_enrollments', 'delete_enrollments', 'manage_enrollments',
            'view_attendances', 'mark_attendances', 'manage_attendances',
            'view_grades', 'create_grades', 'edit_grades', 'delete_grades', 'manage_grades',
            'view_payments', 'create_payments', 'edit_payments', 'delete_payments', 'manage_payments',
            'generate_payment_references', 'process_payments',
            'view_events', 'create_events', 'edit_events', 'delete_events', 'manage_events',
            'send_notifications', 'send_bulk_notifications', 'manage_communications',
            'view_reports', 'view_basic_reports', 'view_financial_reports', 'view_academic_reports', 'export_reports',
            'manage_users', 'create_users', 'edit_users', 'delete_users', 'view_users',
            'manage_settings', 'view_logs', 'view_audit_logs', 'manage_school_year',
            'manage_leave_requests', 'approve_leave_requests', 'view_leave_requests',
            'manage_observations', 'create_observations', 'view_observations', 'manage_student_records',
            'manage_expenses',
        ]);

        // 3. SECRETARIA
        $secretaryRole = $createRole('secretary');
        $secretaryRole->syncPermissions([
            'access_dashboard', 'view_statistics',
            'view_students', 'create_students', 'edit_students', 'manage_students',
            'view_teachers', 'view_classes', 'view_subjects',
            'view_enrollments', 'create_enrollments', 'edit_enrollments', 'manage_enrollments',
            'view_payments', 'create_payments', 'edit_payments', 'manage_payments',
            'generate_payment_references', 'process_payments',
            'view_attendances', 'view_grades',
            'view_events', 'create_events', 'edit_events',
            'send_notifications', 'manage_communications',
            'view_basic_reports', 'view_financial_reports', 'export_reports',
            'view_leave_requests',
            'manage_expenses',
        ]);

        // 4. SEÇÃO PEDAGÓGICA
        $pedagogyRole = $createRole('pedagogy');
        $pedagogyRole->syncPermissions([
            'access_dashboard', 'view_statistics',
            'view_classes', 'create_classes', 'edit_classes', 'manage_classes',
            'view_subjects', 'create_subjects', 'edit_subjects', 'manage_subjects',
            'view_teachers', 'create_teachers', 'edit_teachers', 'manage_teachers',
            'view_students', 'create_students', 'edit_students', 'manage_students',
            'view_enrollments', 'create_enrollments', 'edit_enrollments', 'manage_enrollments',
            'view_attendances', 'manage_attendances',
            'view_grades', 'manage_grades', 'create_grades', 'edit_grades',
            'manage_observations', 'create_observations', 'view_observations', 'manage_student_records',
            'manage_leave_requests', 'approve_leave_requests', 'view_leave_requests',
            'view_reports', 'view_academic_reports', 'export_reports',
            'send_notifications', 'manage_communications',
            'view_events', 'create_events', 'edit_events',
        ]);

        // 5. PROFESSOR
        $teacherRole = $createRole('teacher');
        $teacherRole->syncPermissions([
            'access_dashboard',
            'view_own_classes', 'view_subjects',
            'mark_attendances', 'view_own_class_attendances',
            'grade_own_subjects', 'create_grades', 'edit_grades', 'view_grades',
            'view_students',
            'create_observations', 'view_observations',
            'create_leave_requests', 'view_leave_requests',
            'send_notifications', 'view_events', 'view_basic_reports',
        ]);

        // 6. PAI/ENCARREGADO
        $parentRole = $createRole('parent');
        $parentRole->syncPermissions([
            'access_dashboard',
            'view_own_students', 'view_own_grades', 'view_own_payments',
            'view_events', 'view_basic_reports',
        ]);

        // 6b. ALUNO
        $studentRole = $createRole('student');
        $studentRole->syncPermissions([
            'access_dashboard',
            'view_own_grades',
            'view_events',
            'view_basic_reports',
        ]);

        // 7. SEGURANÇA / PORTARIA
        $securityRole = $createRole('security');
        $securityRole->syncPermissions([
            'access_dashboard',
            'view_gatekeeper_logs',
            'view_events',
        ]);

        // 8. INSPECTOR / ADMIN PROVINCIAL
        $provinceAdminRole = $createRole('province_admin');
        $provinceInspectorRole = $createRole('province_inspector');
        $provincePermissions = [
            'access_dashboard', 'view_statistics',
            'view_territorial_dashboard', 'view_province_reports', 'view_district_reports', 'view_zip_reports',
            'view_academic_reports', 'view_reports', 'export_reports',
        ];
        $provinceAdminRole->syncPermissions($provincePermissions);
        $provinceInspectorRole->syncPermissions($provincePermissions);

        // 9. INSPECTOR / ADMIN DISTRITAL
        $districtAdminRole = $createRole('district_admin');
        $districtInspectorRole = $createRole('district_inspector');
        $districtPermissions = [
            'access_dashboard', 'view_statistics',
            'view_territorial_dashboard', 'view_district_reports', 'view_zip_reports',
            'view_academic_reports', 'view_reports', 'export_reports',
        ];
        $districtAdminRole->syncPermissions($districtPermissions);
        $districtInspectorRole->syncPermissions($districtPermissions);

        // 10. COORDENADOR DE ZIP
        $zipCoordinatorRole = $createRole('zip_coordinator');
        $zipAdminRole = $createRole('zip_admin');
        $zipPermissions = [
            'access_dashboard', 'view_statistics',
            'view_territorial_dashboard', 'view_zip_reports',
            'view_academic_reports', 'view_reports', 'export_reports',
        ];
        $zipCoordinatorRole->syncPermissions($zipPermissions);
        $zipAdminRole->syncPermissions($zipPermissions);

        // ========== CRIAR USUÁRIOS DE EXEMPLO SE NÃO EXISTIREM ==========

        $visSchoolId = \App\Models\School::where('code', 'VIS-QUE')->value('id')
            ?? \App\Models\School::value('id');
        $molSchoolId = \App\Models\School::where('code', 'VIS-MOL')->value('id')
            ?? $visSchoolId;

        $createUser = function ($data, $role) use ($visSchoolId) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'school_id' => array_key_exists('school_id', $data) ? $data['school_id'] : $visSchoolId,
                    'province_id' => $data['province_id'] ?? null,
                    'district_id' => $data['district_id'] ?? null,
                    'zip_id' => $data['zip_id'] ?? null,
                ]
            );
            $user->syncRoles([$role]);
            return $user;
        };

        // 1. Provedor da Plataforma SaaS & Gestor de Licenças (Filipe dos Santos)
        $createUser([
            'name' => 'Filipe dos Santos (Desenvolvedor & Provedor SaaS)',
            'email' => 'dev@visionarios.co.mz',
            'password' => 'dev123',
            'school_id' => null
        ], 'super_admin');

        $createUser([
            'name' => 'Filipe dos Santos (Super Admin)',
            'email' => 'superadmin@visionarios.co.mz',
            'password' => 'superadmin123',
            'school_id' => null
        ], 'super_admin');

        // 2. Dono da Escola / Director Geral (Escola dos Visionários)
        $createUser([
            'name' => 'Director Geral & Proprietário',
            'email' => 'dono@visionarios.co.mz',
            'password' => 'dono123',
            'school_id' => $visSchoolId
        ], 'admin');

        $createUser([
            'name' => 'Director Geral & Proprietário',
            'email' => 'admin@visionarios.co.mz',
            'password' => 'admin123',
            'school_id' => $visSchoolId
        ], 'admin');

        // 3. Filial Alto Molócuè
        $createUser([
            'name' => 'Director de Filial — Alto Molócuè',
            'email' => 'molocue@visionarios.co.mz',
            'password' => 'molocue123',
            'school_id' => $molSchoolId
        ], 'admin');

        // 4. Operações da Escola
        $createUser(['name' => 'Secretaria & Tesouraria', 'email' => 'secretaria@visionarios.co.mz', 'password' => 'secretaria123', 'school_id' => $visSchoolId], 'secretary');
        $createUser(['name' => 'Director Pedagógico', 'email' => 'pedagogia@visionarios.co.mz', 'password' => 'pedagogia123', 'school_id' => $visSchoolId], 'pedagogy');
        $demoTeacherUser = $createUser(['name' => 'Professor Exemplo', 'email' => 'professor@visionarios.co.mz', 'password' => 'professor123', 'school_id' => $visSchoolId], 'teacher');
        $createUser(['name' => 'Encarregado de Educação', 'email' => 'encarregado@visionarios.co.mz', 'password' => 'pai123', 'school_id' => $visSchoolId], 'parent');
        $createUser(['name' => 'Encarregado de Educação (Pai)', 'email' => 'pai@visionarios.co.mz', 'password' => 'pai123', 'school_id' => $visSchoolId], 'parent');
        $createUser(['name' => 'Estudante Visionário', 'email' => 'aluno@visionarios.co.mz', 'password' => 'aluno123', 'school_id' => $visSchoolId], 'student');
        
        // Escolas só existem depois do seed demo; em BD vazia (testes/1º deploy) não cria o Teacher.
        $demoSchoolId = $demoTeacherUser->school_id ?: ($visSchoolId ?: 1);
        $demoTeacher = \App\Models\School::whereKey($demoSchoolId)->exists()
            ? \App\Models\Teacher::firstOrCreate(
                ['user_id' => $demoTeacherUser->id],
                [
                    'first_name' => 'Professor',
                    'last_name' => 'Exemplo',
                    'email' => 'professor@visionarios.co.mz',
                    'phone' => '+258 84 999 1234',
                    'hire_date' => now()->subYears(3),
                    'qualification' => 'Licenciatura em Ensino',
                    'specialization' => 'Matemática e Ciências',
                    'status' => 'active',
                    'school_id' => $demoSchoolId,
                ]
            )
            : null;

        if ($demoTeacher) {
            $demoClassIds = [25, 27, 32, 41, 43];
            foreach ($demoClassIds as $cId) {
                $c = \App\Models\ClassRoom::find($cId);
                if ($c) {
                    $c->update(['teacher_id' => $demoTeacher->id, 'school_year' => 2026, 'is_active' => true]);
                    $subQuery = \App\Models\Subject::where('school_id', $demoTeacher->school_id)
                        ->where(function($q) use ($c) {
                            $q->where('grade_level', $c->grade_level)->orWhere('grade_level', $c->grade_level - 1);
                        })->take(5)->get();
                    if ($subQuery->isEmpty()) {
                        $subQuery = \App\Models\Subject::where('school_id', $demoTeacher->school_id)->take(4)->get();
                    }
                    foreach ($subQuery as $sub) {
                        \App\Models\ClassSubject::updateOrCreate(
                            ['class_id' => $c->id, 'subject_id' => $sub->id],
                            ['teacher_id' => $demoTeacher->id, 'school_id' => $demoTeacher->school_id]
                        );
                    }
                }
            }
        }

        $createUser(['name' => 'Encarregado de Educação', 'email' => 'pai@visionarios.co.mz', 'password' => 'pai123', 'school_id' => $visSchoolId], 'parent');
        $createUser(['name' => 'Aluno Exemplo', 'email' => 'aluno@visionarios.co.mz', 'password' => 'aluno123', 'school_id' => $visSchoolId], 'student');
        $createUser(['name' => 'Segurança / Portaria', 'email' => 'seguranca@visionarios.co.mz', 'password' => 'seguranca123', 'school_id' => $visSchoolId], 'security');

        // Usuários Territoriais
        $createUser(['name' => 'Inspector Provincial Zambézia', 'email' => 'inspector.zambezia@minedh.gov.mz', 'password' => 'provincia123'], 'province_inspector');
        $createUser(['name' => 'Director Distrital Namacurra', 'email' => 'director.namacurra@minedh.gov.mz', 'password' => 'distrito123'], 'district_inspector');
        $createUser(['name' => 'Coordenador ZIP Namacurra Sede', 'email' => 'zip.namacurra@minedh.gov.mz', 'password' => 'zip123'], 'zip_coordinator');

        $this->command->info('Sistema de permissões e roles sincronizado com sucesso!');
    }
}