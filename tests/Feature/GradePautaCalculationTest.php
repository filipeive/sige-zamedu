<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GradePautaCalculationTest extends TestCase
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
    }

    public function test_pauta_uses_exact_grade_model_calculations()
    {
        Permission::firstOrCreate(['name' => 'view_grades']);

        /** @var User $user */
        $user = User::factory()->create();
        $user->givePermissionTo('view_grades');
        $this->actingAs($user);

        $teacherUser = User::factory()->create();
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Prof',
            'last_name' => 'Teste',
            'email' => 'prof@test.com',
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

        $student = Student::create([
            'first_name' => 'João',
            'last_name' => 'Silva',
            'student_number' => 'STU-2026-001',
            'status' => 'active',
            'gender' => 'male',
            'birthdate' => '2010-01-01',
            'registration_date' => '2026-01-01',
            'monthly_fee' => 2500.00,
        ]);

        $class->students()->attach($student->id, [
            'school_id' => $this->school->id,
            'school_year' => 2026,
            'status' => 'active',
            'enrollment_date' => '2026-01-01',
            'monthly_fee' => 2500.00,
        ]);

        $subject = Subject::create([
            'name' => 'Matemática',
            'code' => 'MAT',
            'grade_level' => 10,
            'is_active' => true,
        ]);
        $class->subjects()->attach($subject->id, ['teacher_id' => $teacher->id, 'school_id' => $this->school->id]);

        // Step 1: Create 1 ACS grade (ACS1 = 14.0) without ACP.
        // MACS should be 14.0, MT should return 14.0 (fallback when ACP is null)
        $g1 = Grade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date_recorded' => now(),
            'assessment_type' => Grade::TYPE_ACS1,
            'grade' => 14.0,
            'term' => 1,
            'year' => 2026,
        ]);

        $macsStep1 = Grade::calculateMACS($student->id, $subject->id, $class->id, 1);
        $mtStep1 = Grade::calculateMT($student->id, $subject->id, $class->id, 1);
        $this->assertEquals(14.0, $macsStep1);
        $this->assertEquals(14.0, $mtStep1);

        // Step 2: Create second ACS grade (ACS2 = 16.0) without overwriting ACS1.
        // MACS should be (14 + 16)/2 = 15.0, MT should return 15.0
        $g2 = Grade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date_recorded' => now(),
            'assessment_type' => Grade::TYPE_ACS2,
            'grade' => 16.0,
            'term' => 1,
            'year' => 2026,
        ]);

        $macsStep2 = Grade::calculateMACS($student->id, $subject->id, $class->id, 1);
        $mtStep2 = Grade::calculateMT($student->id, $subject->id, $class->id, 1);
        $this->assertEquals(15.0, $macsStep2);
        $this->assertEquals(15.0, $mtStep2);

        // Step 3: Create ACP grade (ACP = 12.0).
        // Weighted MT = (2 * 15.0 + 1 * 12.0) / 3 = 14.0
        $g3 = Grade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date_recorded' => now(),
            'assessment_type' => Grade::TYPE_ACP,
            'grade' => 12.0,
            'term' => 1,
            'year' => 2026,
        ]);

        $macsStep3 = Grade::calculateMACS($student->id, $subject->id, $class->id, 1);
        $mtStep3 = Grade::calculateMT($student->id, $subject->id, $class->id, 1);
        $this->assertEquals(15.0, $macsStep3);
        $this->assertEquals(14.0, $mtStep3);

        // Test in-memory collection optimization returns identical result
        $allGrades = Grade::where('class_id', $class->id)->where('term', 1)->where('year', 2026)->get();
        $inMemoryMACS = Grade::calculateMACS($student->id, $subject->id, $class->id, 1, $allGrades);
        $inMemoryMT = Grade::calculateMT($student->id, $subject->id, $class->id, 1, $allGrades);

        $this->assertEquals(15.0, $inMemoryMACS);
        $this->assertEquals(14.0, $inMemoryMT);

        // Verify endpoint renders without errors and contains calculated grade
        $response = $this->get(route('grades.index', [
            'class_id' => $class->id,
            'term' => 1,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee(number_format(14.0, 1));
    }
}
