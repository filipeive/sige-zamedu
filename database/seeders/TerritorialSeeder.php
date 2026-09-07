<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\School;
use Illuminate\Database\Seeder;

class TerritorialSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Províncias de Moçambique
        $zambezia = Province::updateOrCreate(
            ['code' => 'ZAM'],
            ['name' => 'Zambézia', 'capital' => 'Quelimane', 'is_active' => true]
        );

        $maputo = Province::updateOrCreate(
            ['code' => 'MPM'],
            ['name' => 'Maputo Província', 'capital' => 'Matola', 'is_active' => true]
        );

        $sofala = Province::updateOrCreate(
            ['code' => 'SOF'],
            ['name' => 'Sofala', 'capital' => 'Beira', 'is_active' => true]
        );

        $nampula = Province::updateOrCreate(
            ['code' => 'NAP'],
            ['name' => 'Nampula', 'capital' => 'Nampula', 'is_active' => true]
        );

        // 2. Distritos da Zambézia
        $namacurra = District::updateOrCreate(
            ['code' => 'ZAM-NAM'],
            ['province_id' => $zambezia->id, 'name' => 'Namacurra', 'is_active' => true]
        );

        $nicoadala = District::updateOrCreate(
            ['code' => 'ZAM-NIC'],
            ['province_id' => $zambezia->id, 'name' => 'Nicoadala', 'is_active' => true]
        );

        $quelimane = District::updateOrCreate(
            ['code' => 'ZAM-QUE'],
            ['province_id' => $zambezia->id, 'name' => 'Quelimane', 'is_active' => true]
        );

        $mocuba = District::updateOrCreate(
            ['code' => 'ZAM-MOC'],
            ['province_id' => $zambezia->id, 'name' => 'Mocuba', 'is_active' => true]
        );

        // 3. ZIPs (Zonas de Influência Pedagógica) de Namacurra
        $zipSede = Zip::updateOrCreate(
            ['code' => 'ZIP-01-NAM'],
            [
                'district_id' => $namacurra->id,
                'name' => 'ZIP Nº 01 - Namacurra Sede',
                'headquarters' => 'Escola Secundária de Namacurra',
                'is_active' => true,
            ]
        );

        $zipMalei = Zip::updateOrCreate(
            ['code' => 'ZIP-02-NAM'],
            [
                'district_id' => $namacurra->id,
                'name' => 'ZIP Nº 02 - Malei',
                'headquarters' => 'EPC de Malei',
                'is_active' => true,
            ]
        );

        $zipMacuse = Zip::updateOrCreate(
            ['code' => 'ZIP-03-NAM'],
            [
                'district_id' => $namacurra->id,
                'name' => 'ZIP Nº 03 - Macuse',
                'headquarters' => 'EPC de Macuse',
                'is_active' => true,
            ]
        );

        // 4. Escolas Piloto
        School::updateOrCreate(
            ['code' => 'ESG-NAM'],
            [
                'province_id' => $zambezia->id,
                'district_id' => $namacurra->id,
                'zip_id' => $zipSede->id,
                'name' => 'Escola Secundária de Namacurra',
                'type' => 'public',
                'address' => 'Vila de Namacurra, Zambézia',
                'phone' => '+258 84 000 0001',
                'email' => 'esg.namacurra@minedh.gov.mz',
                'principal_name' => 'Dr. Armindo Sitoe',
                'is_active' => true,
            ]
        );

        School::updateOrCreate(
            ['code' => 'ESG-PAZ'],
            [
                'province_id' => $zambezia->id,
                'district_id' => $namacurra->id,
                'zip_id' => $zipSede->id,
                'name' => 'Escola Comunitária Escola da Paz',
                'type' => 'community',
                'address' => 'Bairro Central, Namacurra',
                'phone' => '+258 84 000 0002',
                'email' => 'escoladapaz@zamedu.co.mz',
                'principal_name' => 'Prof. Maria Nhabinde',
                'is_active' => true,
            ]
        );

        School::updateOrCreate(
            ['code' => 'EPC-NAM'],
            [
                'province_id' => $zambezia->id,
                'district_id' => $namacurra->id,
                'zip_id' => $zipSede->id,
                'name' => 'Escola Primária Completa de Namacurra',
                'type' => 'public',
                'address' => 'Namacurra Sede',
                'phone' => '+258 84 000 0003',
                'email' => 'epc.namacurra@minedh.gov.mz',
                'principal_name' => 'Prof. Carlos Tembe',
                'is_active' => true,
            ]
        );
    }
}
