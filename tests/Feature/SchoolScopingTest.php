<?php

namespace Tests\Feature;

use App\Http\Middleware\IdentifySchool;
use App\Models\ClassRoom;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SchoolScopingTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;

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

        $this->schoolA = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Piloto A',
            'code' => 'ESC-A',
            'status' => 'active',
        ]);

        $this->schoolB = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Piloto B',
            'code' => 'ESC-B',
            'status' => 'active',
        ]);

        SchoolContext::clear();
    }

    public function test_models_are_scoped_by_active_school_context(): void
    {
        // 1. Create student for School A
        SchoolContext::setSchoolId($this->schoolA->id);
        $studentA = Student::create([
            'student_number' => 'STU-001',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'gender' => 'M',
            'birthdate' => '2010-05-15',
            'registration_date' => '2024-01-10',
            'monthly_fee' => 1500,
            'status' => 'active',
        ]);

        // 2. Create student for School B
        SchoolContext::setSchoolId($this->schoolB->id);
        $studentB = Student::create([
            'student_number' => 'STU-002',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'gender' => 'F',
            'birthdate' => '2011-08-20',
            'registration_date' => '2024-01-10',
            'monthly_fee' => 1500,
            'status' => 'active',
        ]);

        // 3. Query under School A context
        SchoolContext::setSchoolId($this->schoolA->id);
        $studentsA = Student::all();
        $this->assertCount(1, $studentsA);
        $this->assertEquals('João', $studentsA->first()->first_name);

        // 4. Query under School B context
        SchoolContext::setSchoolId($this->schoolB->id);
        $studentsB = Student::all();
        $this->assertCount(1, $studentsB);
        $this->assertEquals('Maria', $studentsB->first()->first_name);
    }

    public function test_creating_model_automatically_assigns_active_school_id(): void
    {
        SchoolContext::setSchoolId($this->schoolA->id);

        $teacher = Teacher::create([
            'first_name' => 'Carlos',
            'last_name' => 'Mendonça',
            'email' => 'carlos@escola-a.com',
            'phone' => '841234567',
            'hire_date' => '2023-02-01',
            'status' => 'active',
        ]);

        $this->assertNotNull($teacher->school_id);
        $this->assertEquals($this->schoolA->id, $teacher->school_id);
    }

    public function test_bypassing_school_scope_allows_cross_school_queries(): void
    {
        SchoolContext::setSchoolId($this->schoolA->id);
        Student::create([
            'student_number' => 'STU-001',
            'first_name' => 'Aluno A',
            'last_name' => 'Teste',
            'gender' => 'M',
            'birthdate' => '2010-01-01',
            'registration_date' => '2024-01-10',
            'monthly_fee' => 1500,
            'status' => 'active',
        ]);

        SchoolContext::setSchoolId($this->schoolB->id);
        Student::create([
            'student_number' => 'STU-002',
            'first_name' => 'Aluno B',
            'last_name' => 'Teste',
            'gender' => 'F',
            'birthdate' => '2010-01-01',
            'registration_date' => '2024-01-10',
            'monthly_fee' => 1500,
            'status' => 'active',
        ]);

        // Cross-school query using withoutSchoolScope()
        SchoolContext::setSchoolId($this->schoolA->id);
        $allStudents = Student::withoutSchoolScope()->get();

        $this->assertCount(2, $allStudents);
    }

    public function test_run_in_context_helper_executes_callback_in_temporary_context(): void
    {
        SchoolContext::setSchoolId($this->schoolA->id);
        $this->assertEquals($this->schoolA->id, SchoolContext::getSchoolId());

        $capturedInsideId = SchoolContext::runInContext($this->schoolB->id, function () {
            return SchoolContext::getSchoolId();
        });

        $this->assertEquals($this->schoolB->id, $capturedInsideId);
        $this->assertEquals($this->schoolA->id, SchoolContext::getSchoolId());
    }

    public function test_identify_school_middleware_sets_school_context_from_authenticated_user(): void
    {
        $user = User::factory()->create([
            'school_id' => $this->schoolB->id,
        ]);

        $request = Request::create('/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new IdentifySchool();
        $middleware->handle($request, function ($req) use ($user) {
            $this->assertEquals($user->school_id, SchoolContext::getSchoolId());
            return response('OK');
        });
    }
}
