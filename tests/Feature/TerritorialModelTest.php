<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\School;
use Database\Seeders\TerritorialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TerritorialModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_territorial_seeder_populates_hierarchy_correctly()
    {
        $this->seed(TerritorialSeeder::class);

        $this->assertDatabaseHas('provinces', ['code' => 'ZAM', 'name' => 'Zambézia']);
        $this->assertDatabaseHas('districts', ['code' => 'ZAM-NAM', 'name' => 'Namacurra']);
        $this->assertDatabaseHas('zips', ['code' => 'ZIP-01-NAM', 'name' => 'ZIP Nº 01 - Namacurra Sede']);
        $this->assertDatabaseHas('schools', ['code' => 'ESG-NAM', 'name' => 'Escola Secundária de Namacurra']);
    }

    public function test_province_district_zip_school_relationships()
    {
        $province = Province::create([
            'name' => 'Gaza',
            'code' => 'GZA',
            'capital' => 'Xai-Xai',
            'is_active' => true,
        ]);

        $district = District::create([
            'province_id' => $province->id,
            'name' => 'Chókwè',
            'code' => 'GZA-CHO',
            'is_active' => true,
        ]);

        $zip = Zip::create([
            'district_id' => $district->id,
            'name' => 'ZIP Nº 01 - Chókwè',
            'code' => 'ZIP-01-CHO',
            'is_active' => true,
        ]);

        $school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola Secundária de Chókwè',
            'code' => 'ESG-CHO',
            'type' => 'public',
            'is_active' => true,
        ]);

        $this->assertCount(1, $province->districts);
        $this->assertEquals('Chókwè', $province->districts->first()->name);

        $this->assertCount(1, $district->zips);
        $this->assertEquals('ZIP Nº 01 - Chókwè', $district->zips->first()->name);

        $this->assertCount(1, $zip->schools);
        $this->assertEquals('Escola Secundária de Chókwè', $zip->schools->first()->name);

        $this->assertEquals('Gaza', $school->province->name);
        $this->assertEquals('Chókwè', $school->district->name);
        $this->assertEquals('ZIP Nº 01 - Chókwè', $school->zip->name);
    }
}
