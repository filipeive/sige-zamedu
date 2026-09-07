<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Services\HierarchyScopeResolver;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TerritorialRolesTest extends TestCase
{
    use RefreshDatabase;

    protected HierarchyScopeResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new HierarchyScopeResolver();

        // Seed roles & permissions
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);
    }

    public function test_spatie_territorial_roles_are_seeded_with_permissions()
    {
        $this->assertTrue(Role::where('name', 'province_inspector')->exists());
        $this->assertTrue(Role::where('name', 'district_inspector')->exists());
        $this->assertTrue(Role::where('name', 'zip_coordinator')->exists());

        $provinceInspector = Role::findByName('province_inspector');
        $this->assertTrue($provinceInspector->hasPermissionTo('view_territorial_dashboard'));
        $this->assertTrue($provinceInspector->hasPermissionTo('view_province_reports'));

        $zipCoordinator = Role::findByName('zip_coordinator');
        $this->assertTrue($zipCoordinator->hasPermissionTo('view_zip_reports'));
    }

    public function test_hierarchy_scope_resolver_returns_null_for_super_admin()
    {
        $superAdmin = User::factory()->create(['school_id' => null]);
        $superAdmin->assignRole('super_admin');

        $visibleSchoolIds = $this->resolver->resolveVisibleSchoolIds($superAdmin);
        $this->assertNull($visibleSchoolIds);

        $scope = $this->resolver->resolveTerritorialScope($superAdmin);
        $this->assertEquals('super', $scope['level']);
    }

    public function test_hierarchy_scope_resolver_filters_by_province()
    {
        $province1 = Province::create(['name' => 'Província Test 1', 'code' => 'P1', 'is_active' => true]);
        $province2 = Province::create(['name' => 'Província Test 2', 'code' => 'P2', 'is_active' => true]);

        $district1 = District::create(['province_id' => $province1->id, 'name' => 'Distrito 1', 'code' => 'D1', 'is_active' => true]);
        $district2 = District::create(['province_id' => $province2->id, 'name' => 'Distrito 2', 'code' => 'D2', 'is_active' => true]);

        $zip1 = Zip::create(['district_id' => $district1->id, 'name' => 'ZIP 1', 'code' => 'Z1', 'is_active' => true]);
        $zip2 = Zip::create(['district_id' => $district2->id, 'name' => 'ZIP 2', 'code' => 'Z2', 'is_active' => true]);

        $school1 = School::create(['province_id' => $province1->id, 'district_id' => $district1->id, 'zip_id' => $zip1->id, 'name' => 'Escola P1', 'code' => 'SCH1', 'is_active' => true]);
        $school2 = School::create(['province_id' => $province2->id, 'district_id' => $district2->id, 'zip_id' => $zip2->id, 'name' => 'Escola P2', 'code' => 'SCH2', 'is_active' => true]);

        $inspector = User::factory()->create([
            'province_id' => $province1->id,
            'school_id' => null,
        ]);
        $inspector->assignRole('province_inspector');

        $visibleIds = $this->resolver->resolveVisibleSchoolIds($inspector);
        $this->assertCount(1, $visibleIds);
        $this->assertContains($school1->id, $visibleIds);
        $this->assertNotContains($school2->id, $visibleIds);

        $scope = $this->resolver->resolveTerritorialScope($inspector);
        $this->assertEquals('province', $scope['level']);
        $this->assertEquals($province1->id, $scope['id']);
    }

    public function test_hierarchy_scope_resolver_filters_by_district_and_zip()
    {
        $province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        
        $zip1 = Zip::create(['district_id' => $district->id, 'name' => 'ZIP Sede', 'code' => 'Z1', 'is_active' => true]);
        $zip2 = Zip::create(['district_id' => $district->id, 'name' => 'ZIP Malei', 'code' => 'Z2', 'is_active' => true]);

        $school1 = School::create(['province_id' => $province->id, 'district_id' => $district->id, 'zip_id' => $zip1->id, 'name' => 'Escola ZIP 1', 'code' => 'SCH-Z1', 'is_active' => true]);
        $school2 = School::create(['province_id' => $province->id, 'district_id' => $district->id, 'zip_id' => $zip2->id, 'name' => 'Escola ZIP 2', 'code' => 'SCH-Z2', 'is_active' => true]);

        // District User
        $districtUser = User::factory()->create(['district_id' => $district->id, 'school_id' => null]);
        $districtUser->assignRole('district_inspector');

        $districtSchools = $this->resolver->resolveVisibleSchoolIds($districtUser);
        $this->assertCount(2, $districtSchools);
        $this->assertContains($school1->id, $districtSchools);
        $this->assertContains($school2->id, $districtSchools);

        // ZIP User
        $zipUser = User::factory()->create(['zip_id' => $zip1->id, 'school_id' => null]);
        $zipUser->assignRole('zip_coordinator');

        $zipSchools = $this->resolver->resolveVisibleSchoolIds($zipUser);
        $this->assertCount(1, $zipSchools);
        $this->assertContains($school1->id, $zipSchools);
        $this->assertNotContains($school2->id, $zipSchools);
    }

    public function test_hierarchy_scope_resolver_returns_single_school_for_school_user()
    {
        $province = Province::create(['name' => 'Test Prov', 'code' => 'TP', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'Test Dist', 'code' => 'TD', 'is_active' => true]);
        $zip = Zip::create(['district_id' => $district->id, 'name' => 'Test Zip', 'code' => 'TZ', 'is_active' => true]);

        $school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola Teste',
            'code' => 'SCH-TEST-SINGLE',
            'is_active' => true,
        ]);
        $schoolUser = User::factory()->create(['school_id' => $school->id]);

        $visibleIds = $this->resolver->resolveVisibleSchoolIds($schoolUser);
        $this->assertEquals([$school->id], $visibleIds);
    }
}
