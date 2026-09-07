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
use App\Models\Grade;
use Spatie\Permission\Models\Role;

class TerritorialDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Province $province;
    protected District $district;
    protected Zip $zip;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        $this->province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $this->district = District::create(['province_id' => $this->province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $this->zip = Zip::create(['district_id' => $this->district->id, 'name' => 'ZIP Sede', 'code' => 'ZIP1', 'is_active' => true]);

        $this->school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $this->zip->id,
            'name' => 'Escola Secundária de Namacurra',
            'code' => 'ESG-NAM-01',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_access_territorial_dashboard()
    {
        $superAdmin = User::factory()->create(['school_id' => $this->school->id]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('territorial.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Painel Territorial de Inspeção MINEDH');
        $response->assertSee('Namacurra');
    }

    public function test_province_inspector_can_access_dashboard_and_see_scoped_schools()
    {
        $inspector = User::factory()->create([
            'province_id' => $this->province->id,
            'school_id' => null,
        ]);
        $inspector->assignRole('province_inspector');

        $response = $this->actingAs($inspector)->get(route('territorial.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Zambézia');
        $response->assertSee('Namacurra');
    }

    public function test_zip_coordinator_can_access_school_drilldown_in_scope()
    {
        $zipCoordinator = User::factory()->create([
            'zip_id' => $this->zip->id,
            'school_id' => null,
        ]);
        $zipCoordinator->assignRole('zip_coordinator');

        $response = $this->actingAs($zipCoordinator)->get(route('territorial.school', $this->school->id));

        $response->assertStatus(200);
        $response->assertSee('Escola Secundária de Namacurra');
        $response->assertSee('Rendimento Escolar Regulamentar MINEDH');
    }

    public function test_zip_coordinator_cannot_access_school_outside_scope()
    {
        $otherProvince = Province::create(['name' => 'Nampula', 'code' => 'NAP', 'is_active' => true]);
        $otherDistrict = District::create(['province_id' => $otherProvince->id, 'name' => 'Mocuba', 'code' => 'MOC', 'is_active' => true]);
        $otherZip = Zip::create(['district_id' => $otherDistrict->id, 'name' => 'ZIP Mocuba', 'code' => 'ZIP-MOC', 'is_active' => true]);
        
        $otherSchool = School::create([
            'province_id' => $otherProvince->id,
            'district_id' => $otherDistrict->id,
            'zip_id' => $otherZip->id,
            'name' => 'Escola Nampula',
            'code' => 'SCH-NAP',
            'is_active' => true,
        ]);

        $zipCoordinator = User::factory()->create([
            'zip_id' => $this->zip->id,
            'school_id' => null,
        ]);
        $zipCoordinator->assignRole('zip_coordinator');

        $response = $this->actingAs($zipCoordinator)->get(route('territorial.school', $otherSchool->id));

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_access_territorial_dashboard()
    {
        $regularUser = User::factory()->create(['school_id' => $this->school->id]);

        $response = $this->actingAs($regularUser)->get(route('territorial.dashboard'));

        $response->assertStatus(403);
    }
}
