<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SNEPedagogicalDocTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected ClassRoom $class;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'view_grades']);
        Permission::firstOrCreate(['name' => 'view_students']);

        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        /** @var User $admin */
        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);
        $this->admin->givePermissionTo(['view_grades', 'view_students']);

        $teacherUser = User::factory()->create();
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Prof',
            'last_name' => 'Teste',
            'email' => 'prof@test.com',
            'hire_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $this->class = ClassRoom::create([
            'name' => '12ª Classe A',
            'grade_level' => 12,
            'shift' => 'morning',
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'first_name' => 'Ana',
            'last_name' => 'Mabote',
            'student_number' => 'STU-2026-999',
            'gender' => 'female',
            'status' => 'active',
            'birthdate' => '2008-05-10',
            'registration_date' => '2026-01-01',
            'monthly_fee' => 3000.00,
        ]);

        $this->class->students()->attach($this->student->id, [
            'school_year' => 2026,
            'status' => 'active',
            'enrollment_date' => '2026-01-01',
            'monthly_fee' => 3000.00,
        ]);

        $subject = Subject::create([
            'name' => 'Matemática',
            'code' => 'MAT',
            'grade_level' => 12,
            'is_active' => true,
        ]);

        Grade::create([
            'student_id' => $this->student->id,
            'teacher_id' => $teacher->id,
            'class_id' => $this->class->id,
            'subject_id' => $subject->id,
            'grade' => 14,
            'term' => 1,
            'year' => 2026,
            'date_recorded' => now(),
            'type' => 'ACS1',
        ]);
    }

    public function test_pedagogical_map_view_renders_successfully()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.pedagogical-map'));
        $response->assertStatus(200);
        $response->assertSee('Mapa de Aproveitamento Pedagógico');
        $response->assertSee('Positivas');
    }

    public function test_pedagogical_map_export_csv_works()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.pedagogical-map.export', ['class_id' => $this->class->id]));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_pauta_exame_renders_and_updates_jury()
    {
        $response = $this->actingAs($this->admin)->get(route('pautas.exame', $this->class->id));
        $response->assertStatus(200);
        $response->assertSee('Pauta de Exames com Júris');

        $updateJuryResponse = $this->actingAs($this->admin)->post(route('pautas.exame.jury', $this->class->id), [
            'jury_president' => 'Dr. Armindo Sitoe',
            'jury_examiner1' => 'Prof. Maria Nhabinde',
            'jury_examiner2' => 'Prof. Carlos Tembe',
            'jury_code' => 'Júri Nº 12A',
        ]);

        $updateJuryResponse->assertRedirect();
        $this->assertEquals('Dr. Armindo Sitoe', setting("jury_class_{$this->class->id}_president"));
    }

    public function test_official_certificate_generates_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('certificates.official', $this->student->id));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
