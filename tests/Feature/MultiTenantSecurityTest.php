<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Event;
use App\Models\Grade;
use App\Services\SchoolContext;
use App\Services\HierarchyScopeResolver;

class MultiTenantSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;
    protected User $userSchoolA;
    protected User $userSchoolB;

    protected Province $provinceZambezia;
    protected Province $provinceMaputo;

    protected District $districtNamacurra;
    protected District $districtMocuba;

    protected Zip $zip1;
    protected Zip $zip2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        // Create Territorial Hierarchy
        $this->provinceZambezia = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $this->provinceMaputo = Province::create(['name' => 'Maputo Província', 'code' => 'MPM', 'is_active' => true]);

        $this->districtNamacurra = District::create(['province_id' => $this->provinceZambezia->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $this->districtMocuba = District::create(['province_id' => $this->provinceZambezia->id, 'name' => 'Mocuba', 'code' => 'MOC', 'is_active' => true]);

        $this->zip1 = Zip::create(['district_id' => $this->districtNamacurra->id, 'name' => 'ZIP Namacurra Sede', 'code' => 'ZIP-01', 'is_active' => true]);
        $this->zip2 = Zip::create(['district_id' => $this->districtMocuba->id, 'name' => 'ZIP Mocuba Sede', 'code' => 'ZIP-02', 'is_active' => true]);

        // Create Schools
        $this->schoolA = School::create([
            'province_id' => $this->provinceZambezia->id,
            'district_id' => $this->districtNamacurra->id,
            'zip_id' => $this->zip1->id,
            'name' => 'Escola A - Namacurra',
            'code' => 'SCH-A',
            'is_active' => true,
        ]);

        $this->schoolB = School::create([
            'province_id' => $this->provinceZambezia->id,
            'district_id' => $this->districtMocuba->id,
            'zip_id' => $this->zip2->id,
            'name' => 'Escola B - Mocuba',
            'code' => 'SCH-B',
            'is_active' => true,
        ]);

        // Create Users for School A & School B
        $this->userSchoolA = User::factory()->create([
            'school_id' => $this->schoolA->id,
            'status' => 'active',
        ]);
        $this->userSchoolA->assignRole('admin');

        $this->userSchoolB = User::factory()->create([
            'school_id' => $this->schoolB->id,
            'status' => 'active',
        ]);
        $this->userSchoolB->assignRole('admin');
    }

    protected function createTestStudent(int $schoolId, array $overrides = []): Student
    {
        SchoolContext::setSchoolId($schoolId);
        return Student::create(array_merge([
            'school_id' => $schoolId,
            'student_number' => 'STU-' . uniqid(),
            'first_name' => 'Aluno',
            'last_name' => 'Teste',
            'gender' => 'M',
            'birthdate' => '2010-01-01',
            'registration_date' => now(),
            'monthly_fee' => 0.0,
            'enrollment_fee' => 0.0,
            'status' => 'active',
        ], $overrides));
    }

    /**
     * Test 1: User from School A cannot access or view Student from School B.
     */
    public function test_user_school_a_cannot_view_student_of_school_b()
    {
        // Create student in School B
        $studentB = $this->createTestStudent($this->schoolB->id, [
            'first_name' => 'João',
            'last_name' => 'Mocuba',
        ]);

        // Query as User School A
        SchoolContext::setSchoolId($this->schoolA->id);
        $response = $this->actingAs($this->userSchoolA)->get(route('students.show', $studentB->id));

        // SchoolScope returns 404 because student B does not exist in School A scope
        $this->assertTrue(in_array($response->status(), [404, 403]));
    }

    /**
     * Test 2: User from School A cannot view payments or financial records of School B.
     */
    public function test_user_school_a_cannot_view_payment_of_school_b()
    {
        $studentB = $this->createTestStudent($this->schoolB->id);
        $paymentB = Payment::create([
            'school_id' => $this->schoolB->id,
            'student_id' => $studentB->id,
            'reference_number' => 'PAY-REF-B-001',
            'amount' => 1500,
            'payment_date' => now(),
            'due_date' => now(),
            'status' => 'completed',
        ]);

        SchoolContext::setSchoolId($this->schoolA->id);
        $response = $this->actingAs($this->userSchoolA)->get(route('payments.show', $paymentB->id));

        $this->assertTrue(in_array($response->status(), [404, 403]));
    }

    /**
     * Test 3: Eloquent Global Scope prevents cross-school data leak in list queries.
     */
    public function test_eloquent_global_scope_prevents_cross_school_data_retrieval()
    {
        $this->createTestStudent($this->schoolA->id, ['first_name' => 'Aluno A1']);
        $this->createTestStudent($this->schoolA->id, ['first_name' => 'Aluno A2']);
        $this->createTestStudent($this->schoolB->id, ['first_name' => 'Aluno B1']);

        // Set context to School A and verify query
        SchoolContext::setSchoolId($this->schoolA->id);
        $studentsInScopeA = Student::all();

        $this->assertCount(2, $studentsInScopeA);
        foreach ($studentsInScopeA as $student) {
            $this->assertEquals($this->schoolA->id, $student->school_id);
            $this->assertNotEquals($this->schoolB->id, $student->school_id);
        }
    }

    /**
     * Test 4: ZIP Coordinator in ZIP 1 cannot access school drilldown in ZIP 2.
     */
    public function test_zip_coordinator_cannot_access_school_outside_their_zip()
    {
        $zipCoordinator1 = User::factory()->create([
            'zip_id' => $this->zip1->id,
            'school_id' => null,
        ]);
        $zipCoordinator1->assignRole('zip_coordinator');

        // Access School A (inside ZIP 1) -> 200 OK
        $responseOk = $this->actingAs($zipCoordinator1)->get(route('territorial.school', $this->schoolA->id));
        $responseOk->assertStatus(200);

        // Access School B (inside ZIP 2) -> 403 Forbidden
        $responseForbidden = $this->actingAs($zipCoordinator1)->get(route('territorial.school', $this->schoolB->id));
        $responseForbidden->assertStatus(403);
    }

    /**
     * Test 5: District Inspector in District 1 cannot access school in District 2.
     */
    public function test_district_inspector_cannot_access_school_in_another_district()
    {
        $districtInspector1 = User::factory()->create([
            'district_id' => $this->districtNamacurra->id,
            'school_id' => null,
        ]);
        $districtInspector1->assignRole('district_inspector');

        // Access School A in Namacurra -> 200 OK
        $responseOk = $this->actingAs($districtInspector1)->get(route('territorial.school', $this->schoolA->id));
        $responseOk->assertStatus(200);

        // Access School B in Mocuba -> 403 Forbidden
        $responseForbidden = $this->actingAs($districtInspector1)->get(route('territorial.school', $this->schoolB->id));
        $responseForbidden->assertStatus(403);
    }

    /**
     * Test 6: HierarchyScopeResolver returns exact isolated IDs per scope level.
     */
    public function test_hierarchy_scope_resolver_enforces_strict_isolation()
    {
        $resolver = new HierarchyScopeResolver();

        // User School A
        $visibleA = $resolver->resolveVisibleSchoolIds($this->userSchoolA);
        $this->assertEquals([$this->schoolA->id], $visibleA);

        // User School B
        $visibleB = $resolver->resolveVisibleSchoolIds($this->userSchoolB);
        $this->assertEquals([$this->schoolB->id], $visibleB);

        // User ZIP 1
        $userZip1 = User::factory()->create(['zip_id' => $this->zip1->id, 'school_id' => null]);
        $userZip1->assignRole('zip_coordinator');
        $visibleZip1 = $resolver->resolveVisibleSchoolIds($userZip1);
        $this->assertEquals([$this->schoolA->id], $visibleZip1);
        $this->assertNotContains($this->schoolB->id, $visibleZip1);
    }
}
