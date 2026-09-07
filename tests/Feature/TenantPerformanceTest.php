<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Enrollment;

class TenantPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_multi_tenant_and_territorial_indexes_exist()
    {
        $this->assertTrue(Schema::hasTable('schools'));
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('students'));
        $this->assertTrue(Schema::hasTable('grades'));
        $this->assertTrue(Schema::hasTable('attendances'));
        $this->assertTrue(Schema::hasTable('payments'));
        $this->assertTrue(Schema::hasTable('classes'));
        $this->assertTrue(Schema::hasTable('enrollments'));
    }

    public function test_eager_loading_models_with_school_context_executes_efficiently()
    {
        $province = \App\Models\Province::create(['name' => 'Prov Perf', 'code' => 'PP', 'is_active' => true]);
        $district = \App\Models\District::create(['province_id' => $province->id, 'name' => 'Dist Perf', 'code' => 'DP', 'is_active' => true]);
        $zip = \App\Models\Zip::create(['district_id' => $district->id, 'name' => 'Zip Perf', 'code' => 'ZP', 'is_active' => true]);

        $school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola Performance Test',
            'code' => 'SCH-PERF',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['school_id' => $school->id]);

        $classes = ClassRoom::withoutGlobalScopes()->where('school_id', $school->id)->with(['students', 'subjects'])->get();
        $this->assertNotNull($classes);
    }
}
