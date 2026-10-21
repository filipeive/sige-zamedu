<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OwnerNetworkTest extends TestCase
{
    use RefreshDatabase;

    protected $province;
    protected $district;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->province = Province::create([
            'name' => 'Zambézia',
            'code' => 'ZAM_TEST',
            'capital' => 'Quelimane',
            'is_active' => true,
        ]);

        $this->district = District::create([
            'province_id' => $this->province->id,
            'name' => 'Quelimane',
            'code' => 'QUE_TEST',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_branches_list(): void
    {
        $school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'name' => 'Escola dos Visionários — Quelimane',
            'code' => 'VIS-QUE',
            'type' => 'private',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'school_id' => $school->id,
        ]);
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('owner.branches.index'));

        $response->assertStatus(200);
        $response->assertSee('Escola dos Visionários — Quelimane');
    }

    public function test_admin_can_create_new_branch(): void
    {
        $school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'name' => 'Escola dos Visionários — Quelimane',
            'code' => 'VIS-QUE',
            'type' => 'private',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'school_id' => $school->id,
        ]);
        $user->assignRole('admin');

        $response = $this->actingAs($user)->post(route('owner.branches.store'), [
            'name' => 'Escola dos Visionários — Alto Molócuè',
            'code' => 'VIS-MOL',
            'phone' => '+258841234567',
            'district_id' => $this->district->id,
            'province_id' => $this->province->id,
        ]);

        $response->assertRedirect(route('owner.branches.index'));
        $this->assertDatabaseHas('schools', [
            'name' => 'Escola dos Visionários — Alto Molócuè',
            'code' => 'VIS-MOL',
            'type' => 'private',
        ]);
    }
}
