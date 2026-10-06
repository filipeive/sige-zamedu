<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StudentsIndexUxTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_index_renders_with_standard_breadcrumbs_and_grid_support(): void
    {
        $province = Province::firstOrCreate(['code' => 'TEST-P'], ['name' => 'Província Teste']);
        $district = District::firstOrCreate(['code' => 'TEST-D'], ['province_id' => $province->id, 'name' => 'Distrito Teste']);
        $school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Teste',
            'code' => 'ESC-TEST-'.uniqid(),
            'status' => 'active',
        ]);

        Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);
        $user = User::factory()->create(['school_id' => $school->id]);
        $user->givePermissionTo('view_students');

        $student = Student::create([
            'school_id' => $school->id,
            'first_name' => 'Bernardo',
            'last_name' => 'Mabunda',
            'student_number' => 'STU-2027-001',
            'gender' => 'male',
            'birthdate' => '2012-05-15',
            'registration_date' => '2026-01-10',
            'monthly_fee' => 1500.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('students.index'));

        $response->assertStatus(200);
        // Assert breadcrumbs do not contain redundant Início
        $response->assertSee('Directório de Alunos');
        $response->assertSee('Bernardo Mabunda');
        $response->assertSee('STU-2027-001');
        // Assert View Mode switcher exists
        $response->assertSee('viewMode === \'table\'', false);
        $response->assertSee('viewMode === \'grid\'', false);
        $response->assertSee('Ativos');
        $response->assertSee('Renovação Pendente');
    }
}
