<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\License;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class StudentManagementTest extends TestCase
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

        // Create an active license to allow request execution
        License::create([
            'school_id' => $this->school->id,
            'client_name' => 'Escola Teste',
            'license_key' => 'KEY-TEST-STUDENTS',
            'expires_at' => now()->addDays(30),
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_with_permission_can_view_student_list()
    {
        $user = User::factory()->create();
        Permission::findOrCreate('view_students');
        $user->givePermissionTo('view_students');

        Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'student_number' => 'ZAM-2026-TEST',
            'gender' => 'male',
            'birthdate' => '2010-05-15',
            'registration_date' => now(),
            'monthly_fee' => 2500.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/students');

        $response->assertStatus(200);
        $response->assertSee('Test Student');
        $response->assertSee('ZAM-2026-TEST');
    }

    public function test_unauthenticated_user_cannot_access_students()
    {
        $response = $this->get('/students');
        $response->assertRedirect('/login');
    }
}
