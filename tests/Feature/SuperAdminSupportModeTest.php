<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;

class SuperAdminSupportModeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
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
            'name' => 'Escola Secundária de Teste',
            'code' => 'ESG-TESTE',
            'type' => 'public',
            'is_active' => true,
        ]);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');
    }

    public function test_super_admin_lands_on_super_admin_governance_dashboard_when_not_in_support_mode()
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Painel do Super Administrador');
        $response->assertSee('Escola Secundária de Teste');
        $response->assertSee('Entrar no Modo Suporte');
    }

    public function test_super_admin_can_activate_support_mode_for_specific_school()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('super-admin.impersonate', $this->school->id));

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue(session('support_mode'));
        $this->assertEquals($this->school->id, session('support_mode_school_id'));

        $dashboardResponse = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('MODO SUPORTE ATIVO');
        $dashboardResponse->assertSee('Sair do Suporte');
    }

    public function test_super_admin_can_exit_support_mode_and_return_to_central_dashboard()
    {
        $this->actingAs($this->superAdmin)
            ->withSession([
                'support_mode' => true,
                'support_mode_school_id' => $this->school->id,
                'support_mode_school_name' => $this->school->name,
            ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('super-admin.stop-impersonating'));

        $response->assertRedirect(route('dashboard'));
        $this->assertFalse(session()->has('support_mode'));

        $dashboardResponse = $this->actingAs($this->superAdmin)->get(route('dashboard'));
        $dashboardResponse->assertSee('Painel do Super Administrador');
    }
}
