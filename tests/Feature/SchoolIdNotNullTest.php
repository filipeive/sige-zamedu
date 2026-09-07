<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\Student;
use App\Services\SchoolContext;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SchoolIdNotNullTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create([
            'name' => 'Zambézia',
            'code' => 'ZAM',
            'capital' => 'Quelimane',
            'is_active' => true,
        ]);

        $district = District::create([
            'province_id' => $province->id,
            'name' => 'Namacurra',
            'code' => 'ZAM-NAM',
            'is_active' => true,
        ]);

        $this->school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Secundária Geral de Namacurra',
            'code' => 'ESG-NAM',
            'status' => 'active',
        ]);

        SchoolContext::clear();
    }

    public function test_inserting_raw_record_without_school_id_throws_integrity_exception(): void
    {
        $this->expectException(QueryException::class);

        // Raw DB insert bypassing Eloquent trait to verify database-level NOT NULL constraint
        DB::table('students')->insert([
            'student_number' => 'STU-FAIL',
            'first_name' => 'Teste',
            'last_name' => 'SemEscola',
            'gender' => 'M',
            'birthdate' => '2010-01-01',
            'registration_date' => '2024-01-10',
            'monthly_fee' => 1500,
            'status' => 'active',
            'school_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_eloquent_creation_with_school_context_succeeds_with_non_null_school_id(): void
    {
        SchoolContext::setSchoolId($this->school->id);

        $student = Student::create([
            'student_number' => 'STU-PASS',
            'first_name' => 'Maria',
            'last_name' => 'Sucesso',
            'gender' => 'F',
            'birthdate' => '2011-03-15',
            'registration_date' => '2024-01-10',
            'monthly_fee' => 1500,
            'status' => 'active',
        ]);

        $this->assertNotNull($student->school_id);
        $this->assertEquals($this->school->id, $student->school_id);
    }
}
