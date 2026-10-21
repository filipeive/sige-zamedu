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
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GradeBatchCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_create_shows_editable_rows_and_only_class_subjects(): void
    {
        $province = Province::create([
            'name' => 'Maputo',
            'code' => 'MPM',
            'capital' => 'Maputo',
            'is_active' => true,
        ]);
        $district = District::create([
            'province_id' => $province->id,
            'name' => 'KaMpfumo',
            'code' => 'MPM-KMP',
            'is_active' => true,
        ]);
        $school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Piloto',
            'code' => 'ESC-PILOT',
            'status' => 'active',
        ]);
        SchoolContext::setSchoolId($school->id);

        Permission::firstOrCreate(['name' => 'view_grades', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create_grades', 'guard_name' => 'web']);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo(['view_grades', 'create_grades']);
        $this->actingAs($user);

        $teacherUser = User::factory()->create();
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Professor',
            'last_name' => 'Teste',
            'email' => 'professor@test.local',
            'hire_date' => '2026-01-01',
            'status' => 'active',
        ]);
        $class = ClassRoom::create([
            'name' => 'Turma 10A',
            'grade_level' => 10,
            'school_year' => 2026,
            'is_active' => true,
            'teacher_id' => $teacher->id,
        ]);
        $subject = Subject::create([
            'name' => 'Matemática',
            'code' => 'MAT',
            'grade_level' => 10,
            'is_active' => true,
        ]);
        $unassignedSubject = Subject::create([
            'name' => 'Biologia',
            'code' => 'BIO',
            'grade_level' => 10,
            'is_active' => true,
        ]);
        $class->subjects()->attach($subject->id, [
            'teacher_id' => $teacher->id,
            'school_id' => $school->id,
        ]);

        $student = Student::create([
            'first_name' => 'João',
            'last_name' => 'Silva',
            'student_number' => 'STU-2026-001',
            'status' => 'active',
            'gender' => 'male',
            'birthdate' => '2010-01-01',
            'registration_date' => '2026-01-01',
            'monthly_fee' => 2500,
        ]);
        $class->students()->attach($student->id, [
            'school_id' => $school->id,
            'school_year' => 2026,
            'status' => 'active',
            'enrollment_date' => '2026-01-01',
            'monthly_fee' => 2500,
        ]);

        $response = $this->get(route('grades.batch-create', [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'term' => 1,
            'assessment_type' => 'ACS2',
            'year' => 2026,
        ]));

        $response->assertOk()
            ->assertSee('João Silva')
            ->assertSee('grade-input')
            ->assertSee('Matemática')
            ->assertDontSee('Biologia');

        $this->assertNotSame($subject->id, $unassignedSubject->id);

        $this->from(route('grades.batch-create', ['class_id' => $class->id]))
            ->post(route('grades.batch-store'), [
                'class_id' => $class->id,
                'subject_id' => $unassignedSubject->id,
                'term' => 1,
                'year' => 2026,
                'assessment_type' => 'ACS2',
                'grades' => [$student->id => ['student_id' => $student->id, 'grade' => 12]],
            ])
            ->assertSessionHasErrors('subject_id');

        $student->enrollments()->where('class_id', $class->id)->delete();
        $this->get(route('grades.batch-create', [
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'term' => 1,
            'assessment_type' => 'ACS2',
            'year' => 2026,
        ]))->assertOk()->assertSee('Nenhum aluno matriculado nesta turma');
    }
}