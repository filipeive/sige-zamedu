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

        $altoMolocue = District::updateOrCreate(
            ['code' => 'ZAM-MOL'],
            ['province_id' => $zambezia->id, 'name' => 'Alto Molócuè', 'is_active' => true]
        );

        $mocuba = District::updateOrCreate(
            ['code' => 'ZAM-MOC'],
            ['province_id' => $zambezia->id, 'name' => 'Mocuba', 'is_active' => true]
        );

        // 3. ZIPs (Zonas de Influência Pedagógica)
        $zipSede = Zip::updateOrCreate(
            ['code' => 'ZIP-01-NAM'],
            [
                'district_id' => $namacurra->id,
                'name' => 'ZIP Nº 01 - Namacurra Sede',
                'headquarters' => 'Escola Secundária de Namacurra',
                'is_active' => true,
            ]
        );

        $zipQuelimane = Zip::updateOrCreate(
            ['code' => 'ZIP-01-QUE'],
            [
                'district_id' => $quelimane->id,
                'name' => 'ZIP Central de Quelimane',
                'headquarters' => 'Escola dos Visionários — Quelimane',
                'is_active' => true,
            ]
        );

        $zipMolocue = Zip::updateOrCreate(
            ['code' => 'ZIP-01-MOL'],
            [
                'district_id' => $altoMolocue->id,
                'name' => 'ZIP Central de Alto Molócuè',
                'headquarters' => 'Escola dos Visionários — Alto Molócuè',
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

        // 4. Escolas Principais da Rede Privada Visionários
        School::updateOrCreate(
            ['code' => 'VIS-QUE'],
            [
                'province_id' => $zambezia->id,
                'district_id' => $quelimane->id,
                'zip_id' => $zipQuelimane->id,
                'name' => 'Escola dos Visionários — Quelimane',
                'type' => 'private',
                'address' => 'Av. Eduardo Mondlane, Bairro Mapiazua, Quelimane',
                'phone' => '+258 85 558 0079',
                'email' => 'quelimane@visionarios.co.mz',
                'principal_name' => 'Director Geral Filipe dos Santos',
                'is_active' => true,
            ]
        );

        School::updateOrCreate(
            ['code' => 'VIS-MOL'],
            [
                'province_id' => $zambezia->id,
                'district_id' => $altoMolocue->id,
                'zip_id' => $zipMolocue->id,
                'name' => 'Escola dos Visionários — Alto Molócuè',
                'type' => 'private',
                'address' => 'Av. Eduardo Mondlane, Alto Molócuè',
                'phone' => '+258 84 300 2000',
                'email' => 'molocue@visionarios.co.mz',
                'principal_name' => 'Director de Filial Manuel Cossa',
                'is_active' => true,
            ]
        );

        // 5. Escolas de Referência Pública (compatibilidade de testes e SNE)
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

        $this->seedNationalCoverage();
    }

    private function seedNationalCoverage(): void
    {
        $provinces = [
            'MPC' => ['Maputo Cidade', 'Maputo'],
            'GAZ' => ['Gaza', 'Xai-Xai'],
            'INH' => ['Inhambane', 'Inhambane'],
            'MAN' => ['Manica', 'Chimoio'],
            'TET' => ['Tete', 'Tete'],
            'CDG' => ['Cabo Delgado', 'Pemba'],
            'NIA' => ['Niassa', 'Lichinga'],
        ];
        foreach ($provinces as $code => [$name, $capital]) {
            Province::updateOrCreate(['code' => $code], ['name' => $name, 'capital' => $capital, 'is_active' => true]);
        }

        $districts = [
            'MPC-KMP' => ['MPC', 'KaMpfumo'],
            'MPC-KMV' => ['MPC', 'KaMavota'],
            'MPM-MAT' => ['MPM', 'Matola'],
            'MPM-MAR' => ['MPM', 'Marracuene'],
            'MPM-BOA' => ['MPM', 'Boane'],
            'SOF-BEI' => ['SOF', 'Beira'],
            'SOF-DON' => ['SOF', 'Dondo'],
            'NAP-NAP' => ['NAP', 'Nampula'],
            'NAP-NAC' => ['NAP', 'Nacala-Porto'],
        ];
        foreach ($districts as $code => [$prov, $name]) {
            District::updateOrCreate(
                ['code' => $code],
                ['province_id' => Province::where('code', $prov)->value('id'), 'name' => $name, 'is_active' => true]
            );
        }

        $zips = [
            'ZIP-01-QUE' => ['ZAM-QUE', 'ZIP Central de Quelimane', 'ESG 25 de Setembro'],
            'ZIP-01-KMP' => ['MPC-KMP', 'ZIP KaMpfumo Central', 'Escola Secundária Josina Machel'],
            'ZIP-01-MAT' => ['MPM-MAT', 'ZIP Matola Sede', 'Escola Secundária da Matola'],
            'ZIP-01-MAR' => ['MPM-MAR', 'ZIP Marracuene Sede', 'Escola Secundária de Marracuene'],
            'ZIP-01-BEI' => ['SOF-BEI', 'ZIP Beira Urbana', 'Escola Secundária Samora Machel da Beira'],
            'ZIP-01-NAP' => ['NAP-NAP', 'ZIP Nampula Central', 'Escola Secundária de Nampula'],
        ];
        foreach ($zips as $code => [$dist, $name, $hq]) {
            Zip::updateOrCreate(
                ['code' => $code],
                ['district_id' => District::where('code', $dist)->value('id'), 'name' => $name, 'headquarters' => $hq, 'is_active' => true]
            );
        }

        $schools = [
            ['EPC-MAL', 'ZIP-02-NAM', 'Escola Primária Completa de Malei', 'public', 'Posto Administrativo de Malei, Namacurra', 'epc.malei@minedh.gov.mz', 'Prof. Lurdes Manjate'],
            ['EPC-MAC', 'ZIP-03-NAM', 'Escola Primária Completa de Macuse', 'public', 'Localidade de Macuse, Namacurra', 'epc.macuse@minedh.gov.mz', 'Prof. Samuel Cossa'],
            ['ESG-QUE', 'ZIP-01-QUE', 'Escola Secundária Geral 25 de Setembro', 'public', 'Av. Samora Machel, Quelimane', 'esg25setembro@minedh.gov.mz', 'Prof. Rogério Bila'],
            ['COL-ZAM', 'ZIP-01-QUE', 'Colégio Privado Estrela da Zambézia', 'private', 'Bairro Aeroporto, Quelimane', 'estrela.zambezia@colegio.co.mz', 'Dra. Teresa Guimarães'],
            ['ESG-JOS', 'ZIP-01-KMP', 'Escola Secundária Josina Machel', 'public', 'Av. Julius Nyerere, Polana Cimento, Maputo', 'josina.machel@minedh.gov.mz', 'Dr. Fernando Moiane'],
            ['ESG-MAN', 'ZIP-01-KMP', 'Escola Secundária Francisco Manyanga', 'public', 'Av. Eduardo Mondlane, Maputo', 'manyanga@minedh.gov.mz', 'Dra. Ângela Macamo'],
            ['COL-VIS', 'ZIP-01-KMP', 'Colégio Internacional Visionários', 'private', 'Sommerschield II, Maputo', 'colegio@visionarios.co.mz', 'Dr. Filipe dos Santos'],
            ['ESG-MAT', 'ZIP-01-MAT', 'Escola Secundária da Matola', 'public', 'Av. da União Africana, Matola', 'esg.matola@minedh.gov.mz', 'Prof. Arlindo Banze'],
            ['ESG-MAR', 'ZIP-01-MAR', 'Escola Secundária de Marracuene', 'public', 'Vila de Marracuene, Maputo Província', 'esg.marracuene@minedh.gov.mz', 'Prof. Zacarias Matusse'],
            ['ESG-BEI', 'ZIP-01-BEI', 'Escola Secundária Samora Machel da Beira', 'public', 'Chaimite, Cidade da Beira, Sofala', 'samora.beira@minedh.gov.mz', 'Dr. Manuel Couto'],
            ['ESG-NAP', 'ZIP-01-NAP', 'Escola Secundária de Nampula', 'public', 'Bairro Muatala, Cidade de Nampula', 'esg.nampula@minedh.gov.mz', 'Dra. Amina Issufo'],
        ];
        foreach ($schools as $i => [$code, $zipCode, $name, $type, $address, $email, $principal]) {
            $zip = Zip::where('code', $zipCode)->first();
            School::updateOrCreate(
                ['code' => $code],
                [
                    'province_id' => $zip->district->province_id,
                    'district_id' => $zip->district_id,
                    'zip_id' => $zip->id,
                    'name' => $name,
                    'type' => $type,
                    'address' => $address,
                    'phone' => sprintf('+258 84 000 %04d', $i + 4),
                    'email' => $email,
                    'principal_name' => $principal,
                    'is_active' => true,
                ]
            );
        }
    }
}
