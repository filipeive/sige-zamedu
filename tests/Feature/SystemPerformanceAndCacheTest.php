<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\School;
use App\Services\SystemCacheService;
use App\Jobs\ProcessAuditLogJob;
use App\Jobs\GeneratePedagogicalReportJob;
use App\Jobs\SendSmsNotificationJob;
use App\Jobs\RecalculateClassGradesJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;

class SystemPerformanceAndCacheTest extends TestCase
{
    use RefreshDatabase;

    protected Province $province;
    protected District $district;
    protected Zip $zip;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->province = Province::create(['name' => 'Zambézia', 'code' => 'PRV-ZAM', 'is_active' => true]);
        $this->district = District::create(['name' => 'Mocuba', 'code' => 'DST-MOC', 'province_id' => $this->province->id, 'is_active' => true]);
        $this->zip = Zip::create(['name' => 'ZIP Mocuba Sede', 'code' => 'ZIP-MOC-01', 'district_id' => $this->district->id, 'is_active' => true]);
        $this->school = School::create([
            'name' => 'Escola Secundária de Mocuba',
            'code' => 'SCH-MOC-001',
            'type' => 'Pública',
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $this->zip->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function system_cache_service_remembers_and_flushes_territorial_queries()
    {
        $service = app(SystemCacheService::class);

        $provinces = $service->getProvinces();
        $this->assertCount(1, $provinces);
        $this->assertTrue(Cache::has('system_provinces_all'));

        $stats = $service->getTerritoryStats();
        $this->assertEquals(1, $stats['total_schools']);
        $this->assertEquals(1, $stats['total_zips']);

        // Flush cache
        $service->flushTerritoryCache();
        $this->assertFalse(Cache::has('system_provinces_all'));
    }

    /** @test */
    public function api_v1_territory_endpoints_return_cached_json_responses()
    {
        $response = $this->getJson(route('api.v1.territory.provinces'));
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.name', 'Zambézia');

        $responseDistricts = $this->getJson(route('api.v1.territory.districts', ['province_id' => $this->province->id]));
        $responseDistricts->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.name', 'Mocuba');

        $responseSchools = $this->getJson(route('api.v1.territory.schools', ['district_id' => $this->district->id]));
        $responseSchools->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.name', 'Escola Secundária de Mocuba');
    }

    /** @test */
    public function system_performance_jobs_can_be_dispatched()
    {
        Queue::fake();

        ProcessAuditLogJob::dispatch(['user_id' => 1, 'action' => 'test', 'description' => 'Test log']);
        GeneratePedagogicalReportJob::dispatch(['school_id' => $this->school->id], 1);
        SendSmsNotificationJob::dispatch(['+258840000000'], 'Test message');
        RecalculateClassGradesJob::dispatch(1, 1, 2026);

        Queue::assertPushed(ProcessAuditLogJob::class);
        Queue::assertPushed(GeneratePedagogicalReportJob::class);
        Queue::assertPushed(SendSmsNotificationJob::class);
        Queue::assertPushed(RecalculateClassGradesJob::class);
    }
}
