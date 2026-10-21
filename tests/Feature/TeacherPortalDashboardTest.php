<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeacherPortalDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_dashboard_renders_with_assigned_classes_and_stats(): void
    {
        $province = Province::create([
            'name' => 'Zambézia',
            'code' => 'ZAM',
            'capital' => 'Quelimane',
            'is_active' => true,
        ]);
        $district = District::create([
            'province_id' => $province->id,
            'name' => 'Namacurra',
            'code' => 'NAM',
            'is_active' => true,
        ]);
        $school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Secundária de Namacurra',
            'code' => 'ESG-NAM',
            'status' => 'active',
        ]);
        SchoolContext::setSchoolId($school->id);

        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'email' => 'professor@visionarios.co.mz',
            'email_verified_at' => now(),
            'school_id' => $school->id,
        ]);
        $user->assignRole($teacherRole);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'first_name' => 'Professor',
            'last_name' => 'Exemplo',
            'email' => 'professor@visionarios.co.mz',
            'hire_date' => now()->subYears(3),
            'qualification' => 'Licenciatura em Ensino',
            'specialization' => 'Matemática e Ciências',
            'status' => 'active',
        ]);

        $class1 = ClassRoom::create([
            'school_id' => $school->id,
            'name' => '6ª Classe A',
            'grade_level' => 7,
            'school_year' => current_school_year(),
            'is_active' => true,
            'teacher_id' => $teacher->id,
            'shift' => 'morning',
            'classroom' => 'Sala 15',
        ]);

        $class2 = ClassRoom::create([
            'school_id' => $school->id,
            'name' => '9ª Classe A (Exame)',
            'grade_level' => 9,
            'school_year' => current_school_year(),
            'is_active' => true,
            'teacher_id' => $teacher->id,
            'shift' => 'morning',
            'classroom' => 'Sala 10',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'first_name' => 'Delfina',
            'last_name' => 'Mabunda',
            'student_number' => 'STU-2026-999',
            'status' => 'active',
            'gender' => 'female',
            'birthdate' => '2011-05-10',
            'registration_date' => '2026-01-01',
            'monthly_fee' => 1500,
        ]);

        $class1->students()->attach($student->id, [
            'school_id' => $school->id,
            'school_year' => current_school_year(),
            'status' => 'active',
            'enrollment_date' => '2026-01-01',
            'monthly_fee' => 1500,
        ]);

        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => 'Matemática',
            'code' => 'MAT-7',
            'grade_level' => 7,
            'is_active' => true,
        ]);

        $class1->subjects()->attach($subject->id, [
            'teacher_id' => $teacher->id,
            'school_id' => $school->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('teacher.dashboard'));

        $response->assertOk();
        $response->assertSee('Bem-vindo(a), Professor Exemplo!');
        $response->assertSee('6ª Classe A');
        $response->assertSee('9ª Classe A (Exame)');
        $response->assertSee('Presenças');
        $response->assertSee('Notas');
        $response->assertSee('Lançar Notas');
    }
}
