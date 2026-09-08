<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\ParentModel;
use App\Models\Student;
use App\Services\SchoolContext;

class ParentMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;
    protected User $adminA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        $province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $zip = Zip::create(['district_id' => $district->id, 'name' => 'Namacurra Sede', 'code' => 'ZIP-01', 'is_active' => true]);

        $this->schoolA = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola A',
            'code' => 'ESG-A',
            'type' => 'public',
            'is_active' => true,
        ]);

        $this->schoolB = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola B Sem Pais',
            'code' => 'ESG-B',
            'type' => 'public',
            'is_active' => true,
        ]);

        $this->adminA = User::factory()->create(['school_id' => $this->schoolA->id]);
        $this->adminA->assignRole('admin');

        // Create Parent in School A
        $userParent = User::factory()->create(['school_id' => $this->schoolA->id]);
        $userParent->assignRole('parent');

        ParentModel::create([
            'user_id' => $userParent->id,
            'first_name' => 'Pai',
            'last_name' => 'Escola A',
            'phone' => '+258841112233',
            'email' => 'pai.escolaA@test.com',
        ]);
    }

    public function test_parents_index_shows_parents_belonging_to_current_school()
    {
        $response = $this->actingAs($this->adminA)->get(route('parents.index'));

        $response->assertStatus(200);
        $response->assertSee('Pai Escola A');
    }

    public function test_parents_index_returns_empty_in_school_without_parents_when_in_support_mode()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Enter support mode for School B (which has no parents)
        $response = $this->actingAs($superAdmin)
            ->withSession([
                'support_mode' => true,
                'support_mode_school_id' => $this->schoolB->id,
                'support_mode_school_name' => $this->schoolB->name,
            ])
            ->get(route('parents.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Pai Escola A');
        $response->assertSee('0'); // Total Parents should be 0
    }
}
