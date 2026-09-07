<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\Setting;
use App\Models\License;

class SchoolOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected Province $province;
    protected District $district;
    protected Zip $zip;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        $this->province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $this->district = District::create(['province_id' => $this->province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $this->zip = Zip::create(['district_id' => $this->district->id, 'name' => 'ZIP Malei', 'code' => 'ZIP-MAL', 'is_active' => true]);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');
    }

    public function test_can_view_onboarding_wizard_page()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('onboarding.school'));

        $response->assertStatus(200);
        $response->assertSee('Wizard de Onboarding Multi-Escola');
        $response->assertSee('Zambézia');
        $response->assertSee('Namacurra');
    }

    public function test_can_store_new_school_via_onboarding_wizard()
    {
        $formData = [
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $this->zip->id,
            'school_name' => 'Escola Comunitária de Malei',
            'school_code' => 'ESG-MAL-99',
            'type' => 'community',
            'email' => 'escola.malei@zamedu.co.mz',
            'phone' => '+258 84 999 8888',
            'address' => 'Vila de Malei',
            'admin_name' => 'Prof. João Malei',
            'admin_email' => 'diretor.malei@zamedu.co.mz',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'academic_year' => '2026',
            'passing_grade' => '9.5',
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('onboarding.school.store'), $formData);
        $response->assertSessionHasNoErrors();

        $school = School::where('code', 'ESG-MAL-99')->first();
        $this->assertNotNull($school);
        $this->assertEquals('Escola Comunitária de Malei', $school->name);
        $this->assertEquals($this->province->id, $school->province_id);
        $this->assertEquals($this->district->id, $school->district_id);
        $this->assertEquals($this->zip->id, $school->zip_id);

        $response->assertRedirect(route('onboarding.school.success', $school->id));

        // Check Admin User
        $adminUser = User::where('email', 'diretor.malei@zamedu.co.mz')->first();
        $this->assertNotNull($adminUser);
        $this->assertEquals($school->id, $adminUser->school_id);
        $this->assertTrue($adminUser->hasRole('admin'));

        // Check Settings
        $settingName = Setting::where('school_id', $school->id)->where('key', 'school_name')->first();
        $this->assertNotNull($settingName);
        $this->assertEquals('Escola Comunitária de Malei', $settingName->value);

        // Check License
        $license = License::where('school_id', $school->id)->first();
        $this->assertNotNull($license);
        $this->assertEquals('active', $license->status);
    }

    public function test_can_view_onboarding_success_dashboard()
    {
        $school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $this->zip->id,
            'name' => 'Escola Sucesso',
            'code' => 'ESC-SUC-01',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'school_id' => $school->id,
            'email' => 'admin.sucesso@zamedu.co.mz',
        ]);
        $admin->assignRole('admin');

        $response = $this->actingAs($this->superAdmin)->get(route('onboarding.school.success', $school->id));

        $response->assertStatus(200);
        $response->assertSee('Escola Integrada com Sucesso!');
        $response->assertSee('ESC-SUC-01');
        $response->assertSee('admin.sucesso@zamedu.co.mz');
    }
}
