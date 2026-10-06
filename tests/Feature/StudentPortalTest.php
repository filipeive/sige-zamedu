<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'student']);

        $province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'capital' => 'Quelimane', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'Namacurra', 'code' => 'ZAM-NAM', 'is_active' => true]);
        $this->school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Secundária de Namacurra',
            'code' => 'ESG-NAM',
            'status' => 'active'
        ]);
        SchoolContext::setSchoolId($this->school->id);
    }

    public function test_guest_cannot_access_student_portal(): void
    {
        $response = $this->get(route('student.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_can_access_dashboard_and_simulate_exam(): void
    {
        $user = User::factory()->create([
            'email' => 'aluno.secundario@visionarios.co.mz',
            'school_id' => $this->school->id
        ]);
        $user->assignRole('student');

        $student = Student::create([
            'school_id' => $this->school->id,
            'first_name' => 'Elídio',
            'last_name' => 'Macamo',
            'student_number' => 'ZAM20260099',
            'gender' => 'male',
            'birthdate' => '2008-03-15',
            'registration_date' => now(),
            'monthly_fee' => 0.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('student.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Portal do Estudante');
        $response->assertSee('Elídio Macamo');

        // Testar Simulação de Exame (Fórmula Oficial MINEDH)
        // MAF = 12, NE = 8, ACF = 11 -> NE_Eficaz = 11, NFD = round((12 + 11)/2) = 12
        $simResponse = $this->actingAs($user)->postJson(route('student.simulate-exam'), [
            'maf' => 12.0,
            'ne'  => 8.0,
            'acf' => 11.0,
        ]);

        $simResponse->assertStatus(200);
        $simResponse->assertJson([
            'success' => true,
            'ne_eficaz' => 11.0,
            'nfd' => 12,
            'approved' => true,
        ]);
    }
}

