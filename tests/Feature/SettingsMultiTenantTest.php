<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Setting;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;

class SettingsMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        $province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $zip = Zip::create(['district_id' => $district->id, 'name' => 'Namacurra Sede', 'code' => 'ZIP-01', 'is_active' => true]);

        $this->school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola Teste Configurações',
            'code' => 'ESG-CFG',
            'type' => 'public',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create(['school_id' => $this->school->id]);
        $this->admin->assignRole('admin');

        Setting::create(['key' => 'school_name', 'value' => 'Nome Global Antigo', 'group' => 'school', 'school_id' => $this->school->id]);
    }

    public function test_can_update_school_settings_without_duplicate_key_error()
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.settings.update'), [
                'settings' => [
                    'school_name' => 'Escola Secundária Atualizada dos Visionários',
                    'school_phone' => '+258849998877',
                ],
                'active_tab' => 'escola',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(
            'Escola Secundária Atualizada dos Visionários',
            Setting::get('school_name')
        );
    }
}
