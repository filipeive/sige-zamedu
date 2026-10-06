<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;

class TerritoryCascadeIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected Province $provinceMaputo;
    protected Province $provinceZambezia;
    protected District $districtMatola;
    protected District $districtNamacurra;
    protected Zip $zipMatola;
    protected Zip $zipNamacurra;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        $this->provinceMaputo = Province::create(['name' => 'Maputo Província', 'code' => 'MPP', 'is_active' => true]);
        $this->provinceZambezia = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);

        $this->districtMatola = District::create(['province_id' => $this->provinceMaputo->id, 'name' => 'Matola', 'code' => 'MAT', 'is_active' => true]);
        $this->districtNamacurra = District::create(['province_id' => $this->provinceZambezia->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);

        $this->zipMatola = Zip::create(['district_id' => $this->districtMatola->id, 'name' => 'ZIP Matola Sede', 'code' => 'ZIP-MAT-01', 'is_active' => true]);
        $this->zipNamacurra = Zip::create(['district_id' => $this->districtNamacurra->id, 'name' => 'ZIP Namacurra Sede', 'code' => 'ZIP-NAM-01', 'is_active' => true]);

        $school = School::create([
            'province_id' => $this->provinceMaputo->id,
            'district_id' => $this->districtMatola->id,
            'zip_id' => $this->zipMatola->id,
            'name' => 'Escola Base de Teste',
            'code' => 'ESC-BASE-01',
            'is_active' => true,
        ]);

        $this->superAdmin = User::factory()->create(['school_id' => $school->id]);
        $this->superAdmin->assignRole('super_admin');
    }

    public function test_cannot_create_school_with_mismatched_province_and_district(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.territory.schools.store'), [
            'name' => 'Escola Invalida',
            'code' => 'ESC-INV-01',
            'type' => 'Pública',
            'province_id' => $this->provinceMaputo->id,
            'district_id' => $this->districtNamacurra->id, // Pertence a Zambézia!
            'zip_id' => null,
        ]);

        $response->assertSessionHasErrors(['district_id']);
        $this->assertDatabaseMissing('schools', ['code' => 'ESC-INV-01']);
    }

    public function test_cannot_create_school_with_mismatched_district_and_zip(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.territory.schools.store'), [
            'name' => 'Escola Invalida ZIP',
            'code' => 'ESC-INV-02',
            'type' => 'Pública',
            'province_id' => $this->provinceMaputo->id,
            'district_id' => $this->districtMatola->id,
            'zip_id' => $this->zipNamacurra->id, // Pertence a Namacurra/Zambézia!
        ]);

        $response->assertSessionHasErrors(['zip_id']);
        $this->assertDatabaseMissing('schools', ['code' => 'ESC-INV-02']);
    }

    public function test_can_create_school_with_consistent_territorial_hierarchy(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.territory.schools.store'), [
            'name' => 'Escola Primária da Matola',
            'code' => 'EPC-MAT-01',
            'type' => 'Pública',
            'province_id' => $this->provinceMaputo->id,
            'district_id' => $this->districtMatola->id,
            'zip_id' => $this->zipMatola->id,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('schools', ['code' => 'EPC-MAT-01']);
    }

    public function test_onboarding_rejects_mismatched_territory(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('onboarding.school.store'), [
            'province_id' => $this->provinceMaputo->id,
            'district_id' => $this->districtNamacurra->id, // Mismatch!
            'zip_id' => $this->zipMatola->id,
            'school_name' => 'Escola Onboarding Invalida',
            'type' => 'public',
            'email' => 'onboard.inv@escola.ac.mz',
            'phone' => '+258841112222',
            'admin_name' => 'Admin Onboarding',
            'admin_email' => 'admin.inv@escola.ac.mz',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'academic_year' => '2026',
            'passing_grade' => '9.5',
        ]);

        $response->assertSessionHasErrors(['district_id']);
        $this->assertDatabaseMissing('schools', ['email' => 'onboard.inv@escola.ac.mz']);
    }

    public function test_onboarding_accepts_consistent_territory(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('onboarding.school.store'), [
            'province_id' => $this->provinceMaputo->id,
            'district_id' => $this->districtMatola->id,
            'zip_id' => $this->zipMatola->id,
            'school_name' => 'Escola Nova da Matola',
            'school_code' => 'ESC-MAT-NOVA',
            'type' => 'public',
            'email' => 'onboard.valid@escola.ac.mz',
            'phone' => '+258841112233',
            'admin_name' => 'Admin Matola',
            'admin_email' => 'admin.valid@escola.ac.mz',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'academic_year' => '2026',
            'passing_grade' => '9.5',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('schools', ['code' => 'ESC-MAT-NOVA']);
    }
}
