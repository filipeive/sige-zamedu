<?php

namespace Tests\Feature;

use App\Models\License;
use App\Models\User;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseCheckTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create(['name' => 'Maputo', 'code' => 'MPM', 'capital' => 'Maputo', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'KaMpfumo', 'code' => 'MPM-KMP', 'is_active' => true]);
        $this->school = School::create(['province_id' => $province->id, 'district_id' => $district->id, 'name' => 'Escola Piloto', 'code' => 'ESC-PILOT', 'status' => 'active']);
        SchoolContext::setSchoolId($this->school->id);
    }

    public function test_active_license_allows_access()
    {
        $user = User::factory()->create();

        License::create([
            'client_name' => 'Escola Teste',
            'license_key' => 'KEY-ACTIVE-123',
            'expires_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_grace_period_license_allows_access_with_warning()
    {
        $user = User::factory()->create();

        License::create([
            'client_name' => 'Escola Teste',
            'license_key' => 'KEY-GRACE-123',
            'expires_at' => now()->subDays(2),
            'grace_period_days' => 7,
            'status' => 'active', // Será recalculado para grace_period
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('licenseGraceWarning');
    }

    public function test_suspended_license_redirects_to_suspended_page()
    {
        $user = User::factory()->create();

        License::create([
            'client_name' => 'Escola Teste',
            'license_key' => 'KEY-SUSPENDED-123',
            'expires_at' => now()->subDays(20),
            'grace_period_days' => 7,
            'status' => 'active', // Será recalculado para suspended
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('license.suspended'));
    }
}
