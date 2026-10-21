<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class SchoolOwnerBranchSwitchTest extends TestCase
{
    use RefreshDatabase;

    protected $province;
    protected $district;

    protected function setUp(): void
    {
        parent::setUp();

        // Garantir que as roles necessárias existam
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->province = \App\Models\Province::create([
            'name' => 'Zambézia',
            'code' => 'ZAM_TEST',
            'capital' => 'Quelimane',
            'is_active' => true,
        ]);

        $this->district = \App\Models\District::create([
            'province_id' => $this->province->id,
            'name' => 'Quelimane',
            'code' => 'QUE_TEST',
            'is_active' => true,
        ]);
    }

    public function test_dev_accesses_saas_provider_dashboard()
    {
        $dev = User::factory()->create([
            'email' => 'dev_test@visionarios.co.mz',
            'school_id' => null,
        ]);
        $dev->assignRole('super_admin');

        $response = $this->actingAs($dev)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.super_admin');
        $response->assertSee('Painel do Desenvolvedor');
    }

    public function test_school_owner_accesses_visionarios_school_dashboard()
    {
        $schoolQue = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'name' => 'Escola dos Visionários — Quelimane',
            'code' => 'VIS-QUE-TEST',
            'type' => 'private',
            'is_active' => true,
        ]);

        $owner = User::factory()->create([
            'email' => 'dono_test@visionarios.co.mz',
            'school_id' => $schoolQue->id,
        ]);
        $owner->assignRole('admin');

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin');
        $response->assertDontSee('MINEDH · PLATAFORMA CENTRAL DE GOVERNAÇÃO MULTI-ESCOLA');
    }

    public function test_school_owner_can_switch_branches()
    {
        $schoolQue = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'name' => 'Escola dos Visionários — Quelimane',
            'code' => 'VIS-QUE-SWITCH',
            'type' => 'private',
            'is_active' => true,
        ]);

        $schoolMol = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'name' => 'Escola dos Visionários — Alto Molócuè',
            'code' => 'VIS-MOL-SWITCH',
            'type' => 'private',
            'is_active' => true,
        ]);

        $owner = User::factory()->create([
            'email' => 'owner_switch@visionarios.co.mz',
            'school_id' => $schoolQue->id,
        ]);
        $owner->assignRole('admin');

        // Alternar para Alto Molócuè
        $switchRes = $this->actingAs($owner)->get(route('branch.switch', $schoolMol->id));
        $switchRes->assertSessionHas('active_branch', $schoolMol->id);

        // Alternar para Consolidado da Rede
        $switchAllRes = $this->actingAs($owner)->get(route('branch.switch', 'all'));
        $switchAllRes->assertSessionHas('active_branch', 'all');

        // Tentativa não autorizada de mudar para uma escola pública externa
        $publicSchool = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'name' => 'Escola Pública Externa',
            'code' => 'PUB-EXT-TEST',
            'type' => 'public',
            'is_active' => true,
        ]);

        $unauthorizedRes = $this->actingAs($owner)->get(route('branch.switch', $publicSchool->id));
        $unauthorizedRes->assertStatus(403);
    }
}
