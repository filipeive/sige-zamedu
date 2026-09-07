<?php

namespace Tests\Feature;

use App\Models\ParentModel;
use App\Models\Teacher;
use App\Models\User;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAccessAndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create(['name' => 'Maputo', 'code' => 'MPM', 'capital' => 'Maputo', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'KaMpfumo', 'code' => 'MPM-KMP', 'is_active' => true]);
        $this->school = School::create(['province_id' => $province->id, 'district_id' => $district->id, 'name' => 'Escola Piloto', 'code' => 'ESC-PILOT', 'status' => 'active']);
        SchoolContext::setSchoolId($this->school->id);

        $permissions = [
            'view_grades', 'create_grades', 'edit_grades',
            'view_students', 'create_students', 'edit_students', 'delete_students', 'manage_students',
            'view_payments', 'process_payments', 'manage_payments',
            'view_reports', 'manage_settings', 'view_subjects'
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $secretaryRole = Role::firstOrCreate(['name' => 'secretary']);
        $secretaryRole->syncPermissions(['view_students', 'create_students', 'edit_students', 'manage_students', 'view_payments', 'process_payments', 'manage_payments']);

        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $teacherRole->syncPermissions(['view_grades', 'create_grades', 'edit_grades']);

        $parentRole = Role::firstOrCreate(['name' => 'parent']);
    }

    public function test_admin_can_access_pautas_and_dashboard()
    {
        $admin = User::factory()->create(['email' => 'admin@visionarios.co.mz']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/grades');
        $response->assertStatus(200);

        $responseDashboard = $this->actingAs($admin)->get('/dashboard');
        $responseDashboard->assertStatus(200);
    }

    public function test_secretary_can_access_students_and_payments()
    {
        $secretary = User::factory()->create(['email' => 'secretaria@visionarios.co.mz']);
        $secretary->assignRole('secretary');

        $response = $this->actingAs($secretary)->get('/students');
        $response->assertStatus(200);

        $responsePayments = $this->actingAs($secretary)->get('/payments');
        $responsePayments->assertStatus(200);
    }

    public function test_teacher_can_access_dashboard()
    {
        $teacherUser = User::factory()->create(['email' => 'professor@visionarios.co.mz']);
        $teacherUser->assignRole('teacher');
        Teacher::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Prof',
            'last_name' => 'Test',
            'email' => $teacherUser->email,
            'employee_id' => 'PROF-999',
            'hire_date' => now(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($teacherUser)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_parent_can_access_dashboard()
    {
        $parentUser = User::factory()->create(['email' => 'pai@visionarios.co.mz']);
        $parentUser->assignRole('parent');
        ParentModel::create([
            'user_id' => $parentUser->id,
            'first_name' => 'Pai',
            'last_name' => 'Test',
            'email' => $parentUser->email,
            'phone' => '841234567',
            'is_active' => true,
        ]);

        $response = $this->actingAs($parentUser)->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect(route('parent.dashboard'));
    }
}
