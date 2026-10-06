<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\Setting;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiTenantSchoolNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_name_resolves_dynamically_from_active_school_id(): void
    {
        $province = Province::create(['name' => 'Sofala', 'code' => 'SOF', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'Beira', 'code' => 'BEI', 'is_active' => true]);
        $zip = Zip::create(['district_id' => $district->id, 'name' => 'ZIP Beira', 'code' => 'ZIP-01', 'is_active' => true]);

        $school1 = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola Secundária Samora Machel',
            'code' => 'ESG-SM',
            'type' => 'public',
            'is_active' => true,
        ]);

        $school2 = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Colégio Privado Estrela do Norte',
            'code' => 'COL-EN',
            'type' => 'private',
            'is_active' => true,
        ]);

        // Cenário 1: Utilizador autenticado na Escola 1
        $user1 = User::factory()->create([
            'school_id' => $school1->id,
            'name' => 'Secretário Escola 1',
        ]);
        $this->actingAs($user1);

        $this->assertEquals('Escola Secundária Samora Machel', \current_school_name());
        $this->assertEquals('Escola Secundária Samora Machel', setting('school_name'));
        $this->assertEquals('Escola Secundária Samora Machel', setting('school_short_name'));
        $this->assertTrue(\is_public_school());
        $this->assertFalse(\is_private_school());

        // Cenário 2: Utilizador autenticado na Escola 2
        $user2 = User::factory()->create([
            'school_id' => $school2->id,
            'name' => 'Director Escola 2',
        ]);
        $this->actingAs($user2);

        $this->assertEquals('Colégio Privado Estrela do Norte', \current_school_name());
        $this->assertEquals('Colégio Privado Estrela do Norte', setting('school_name'));
        $this->assertEquals('Colégio Privado Estrela do Norte', setting('school_short_name'));
        $this->assertFalse(\is_public_school());
        $this->assertTrue(\is_private_school());

        // Cenário 3: Modo Suporte do Super Administrador
        $superAdmin = User::factory()->create([
            'school_id' => null,
            'name' => 'Super Admin',
        ]);
        $this->actingAs($superAdmin);
        session(['support_mode_school_id' => $school1->id]);

        $this->assertEquals('Escola Secundária Samora Machel', \current_school_name());
        $this->assertEquals('Escola Secundária Samora Machel', setting('school_name'));

        // Cenário 4: SchoolContext explícito
        session()->forget('support_mode_school_id');
        SchoolContext::setSchoolId($school2->id);

        $this->assertEquals('Colégio Privado Estrela do Norte', \current_school_name());
        $this->assertEquals('Colégio Privado Estrela do Norte', setting('school_name'));
        SchoolContext::clear();
    }
}
