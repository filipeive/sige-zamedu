<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\District;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Province;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Zip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TerritorialFemaleMetricsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected Province $province;
    protected District $district;
    protected Zip $zip;
    protected School $school;
    protected ClassRoom $class7;
    protected Teacher $teacher;
    protected static int $studentSeq = 1;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@visionarios.co.mz',
        ]);
        $this->superAdmin->assignRole($role);

        $this->province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $this->district = District::create(['province_id' => $this->province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $this->zip = Zip::create(['district_id' => $this->district->id, 'name' => 'ZIP Namacurra', 'code' => 'ZIP-01', 'is_active' => true]);
        $this->school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $this->zip->id,
            'name' => 'Escola Secundária de Namacurra',
            'code' => 'ES-NAM',
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create([
            'school_id' => $this->school->id,
            'first_name' => 'Professor',
            'last_name' => 'Silva',
            'email' => 'prof.silva@esnam.edu.mz',
            'gender' => 'male',
            'hire_date' => '2020-01-01',
            'is_active' => true,
        ]);

        $this->subject = \App\Models\Subject::create([
            'school_id' => $this->school->id,
            'name' => 'Matemática',
            'code' => 'MAT',
            'grade_level' => 7,
            'is_active' => true,
        ]);

        $this->class7 = ClassRoom::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->teacher->id,
            'name' => '7ª A',
            'grade_level' => 7,
            'section' => 'A',
            'school_year' => 2026,
            'shift' => 'morning',
            'is_active' => true,
        ]);
    }

    protected function makeStudent(string $gender, string $status = 'active'): Student
    {
        self::$studentSeq++;
        return Student::create([
            'school_id' => $this->school->id,
            'first_name' => 'Estudante',
            'last_name' => 'Teste ' . self::$studentSeq,
            'student_number' => 'STU-' . self::$studentSeq,
            'gender' => $gender,
            'status' => $status,
            'birthdate' => '2012-05-10',
            'registration_date' => '2026-01-15',
            'monthly_fee' => 0.0,
        ]);
    }

    protected function makeGrade(int $studentId, float $value): Grade
    {
        return Grade::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->teacher->id,
            'student_id' => $studentId,
            'class_id' => $this->class7->id,
            'subject_id' => $this->subject->id,
            'type' => 'acs1',
            'term' => 1,
            'year' => 2026,
            'grade' => $value,
            'date_recorded' => now()->toDateString(),
        ]);
    }

    public function test_student_model_gender_helpers_and_scopes(): void
    {
        $this->assertTrue(Student::isFemaleGender('female'));
        $this->assertTrue(Student::isFemaleGender('Female'));
        $this->assertTrue(Student::isFemaleGender('F'));
        $this->assertTrue(Student::isFemaleGender('f'));
        $this->assertTrue(Student::isFemaleGender('feminino'));
        $this->assertTrue(Student::isFemaleGender('mulher'));
        $this->assertFalse(Student::isFemaleGender('male'));
        $this->assertFalse(Student::isFemaleGender('m'));

        $femaleStudent = $this->makeStudent('female');
        $maleStudent = $this->makeStudent('male');

        $this->assertTrue($femaleStudent->isFemale());
        $this->assertFalse($maleStudent->isFemale());

        $this->assertEquals(1, Student::withoutGlobalScopes()->female()->count());
        $this->assertEquals(1, Student::withoutGlobalScopes()->male()->count());
    }

    public function test_territorial_index_dashboard_calculates_female_metrics_correctly(): void
    {
        // 2 alunas e 1 aluno
        $female1 = $this->makeStudent('female');
        $female2 = $this->makeStudent('female');
        $male1 = $this->makeStudent('male');

        // Notas
        $this->makeGrade($female1->id, 14.0);
        $this->makeGrade($female2->id, 8.0);
        $this->makeGrade($male1->id, 12.0);

        $response = $this->actingAs($this->superAdmin)->get(route('territorial.dashboard'));
        $response->assertStatus(200);

        // Alunos: total HM = 3, M = 2
        $response->assertViewHas('studentsHM', 3);
        $response->assertViewHas('studentsM', 2);

        // Avaliados: HM = 3, M = 2
        $response->assertViewHas('evaluatedHM', 3);
        $response->assertViewHas('evaluatedM', 2);

        // Positivas: HM = 2 (female1 e male1), M = 1 (female1)
        $response->assertViewHas('positiveHM', 2);
        $response->assertViewHas('positiveM', 1);

        // Taxa de aprovação: M = 1 / 2 = 50.0%
        $response->assertViewHas('approvalRateM', 50.0);
    }

    public function test_territorial_map_calculates_female_columns_correctly(): void
    {
        // 2 alunas e 2 alunos na 7ª classe
        $female1 = $this->makeStudent('female');
        $female2 = $this->makeStudent('female');
        $male1 = $this->makeStudent('male');
        $male2 = $this->makeStudent('male');

        foreach ([$female1, $female2, $male1, $male2] as $student) {
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $this->class7->id,
                'school_year' => 2026,
                'enrollment_date' => now()->toDateString(),
                'monthly_fee' => 0.0,
                'status' => 'active',
                'school_id' => $this->school->id,
            ]);
        }

        // Notas: alunas (female1 = 15.0 [passou], female2 = 7.0 [reprovou]), alunos (male1 = 12.0, male2 = 11.0)
        $this->makeGrade($female1->id, 15.0);
        $this->makeGrade($female2->id, 7.0);
        $this->makeGrade($male1->id, 12.0);
        $this->makeGrade($male2->id, 11.0);

        $response = $this->actingAs($this->superAdmin)->get(route('territorial.map', [
            'year' => 2026,
            'term' => 1,
            'level' => 'secondary',
        ]));

        $response->assertStatus(200);

        $mapData = $response->viewData('mapData');
        $this->assertNotNull($mapData);

        // 7ª classe
        $row7 = $mapData['by_level'][7] ?? null;
        $this->assertNotNull($row7);

        $this->assertEquals(4, $row7['entries_hm']);
        $this->assertEquals(2, $row7['entries_m']);
        $this->assertEquals(4, $row7['evaluated_hm']);
        $this->assertEquals(2, $row7['evaluated_m']);
        $this->assertEquals(3, $row7['passed_hm']);
        $this->assertEquals(1, $row7['passed_m']); // female1 passou, female2 reprovou
        $this->assertEquals(50.0, $row7['approval_rate_m']);
        $this->assertEquals(75.0, $row7['approval_rate_hm']);

        // Totais gerais
        $this->assertEquals(2, $mapData['totals']['entries_m']);
        $this->assertEquals(2, $mapData['totals']['evaluated_m']);
        $this->assertEquals(1, $mapData['totals']['passed_m']);
        $this->assertEquals(50.0, $mapData['totals']['approval_rate_m']);
    }
}
