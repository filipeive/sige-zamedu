<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Subject;

class TerritorialAggregationTest extends TestCase
{
    use RefreshDatabase;

    protected Province $province;
    protected District $district;
    protected Zip $zip;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        $this->province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $this->district = District::create(['province_id' => $this->province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $this->zip = Zip::create(['district_id' => $this->district->id, 'name' => 'ZIP Sede', 'code' => 'ZIP1', 'is_active' => true]);

        $this->school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $this->zip->id,
            'name' => 'Escola Secundária de Namacurra',
            'code' => 'ESG-NAM-01',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_access_territorial_pedagogical_map()
    {
        $superAdmin = User::factory()->create(['school_id' => $this->school->id]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('territorial.map'));

        $response->assertStatus(200);
        $response->assertSee('Mapa Geral de Aproveitamento Pedagógico');
        $response->assertSee('MINISTÉRIO DA EDUCAÇÃO E DESENVOLVIMENTO HUMANO');
        $response->assertSee('1º CICLO DO ENSINO SECUNDÁRIO');
        $response->assertSee('2º CICLO DO ENSINO SECUNDÁRIO');
    }

    public function test_province_inspector_can_access_territorial_map()
    {
        $inspector = User::factory()->create([
            'province_id' => $this->province->id,
            'school_id' => null,
        ]);
        $inspector->assignRole('province_inspector');

        $response = $this->actingAs($inspector)->get(route('territorial.map'));

        $response->assertStatus(200);
        $response->assertSee('MAPA DE APROVEITAMENTO PEDAGÓGICO');
    }

    public function test_export_pdf_returns_pdf_document()
    {
        $superAdmin = User::factory()->create(['school_id' => $this->school->id]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('territorial.map.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_csv_returns_csv_file()
    {
        $superAdmin = User::factory()->create(['school_id' => $this->school->id]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('territorial.map.csv'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_unauthorized_user_cannot_access_map()
    {
        $user = User::factory()->create(['school_id' => $this->school->id]);

        $response = $this->actingAs($user)->get(route('territorial.map'));

        $response->assertStatus(403);
    }
}
