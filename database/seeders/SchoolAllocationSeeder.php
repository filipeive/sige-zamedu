<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\SchoolAllocation;
use App\Models\District;
use App\Models\Province;

class SchoolAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter escolas existentes
        $secondarySchools = School::where('name', 'like', '%Secund%')
            ->orWhere('name', 'like', '%ESG%')
            ->orWhere('name', 'like', '%Geral%')
            ->get();

        if ($secondarySchools->isEmpty()) {
            $secondarySchools = School::all();
        }

        if ($secondarySchools->isEmpty()) {
            return;
        }

        $primarySchools = [
            'EPC de Namacurra Sede',
            'EPC 3 de Fevereiro - Namacurra',
            'EPC Samora Machel',
            'EPC de Macuse',
            'EPC Eduardo Mondlane - Quelimane',
            'EPC de Nicoadala Centro',
            'EPC Heróis Moçambicanos',
            'EPC 25 de Junho',
            'EPC Josina Machel',
            'EPC Mateus Sansão Muthemba',
        ];

        $studentNames = [
            ['name' => 'Amélia António Sitoe', 'gender' => 'female', 'birth' => '2014-04-12'],
            ['name' => 'Carlos Alberto Mucavel', 'gender' => 'male', 'birth' => '2014-06-25'],
            ['name' => 'Fátima Salomão Cossa', 'gender' => 'female', 'birth' => '2014-01-18'],
            ['name' => 'Delfina Manuel Macamo', 'gender' => 'female', 'birth' => '2013-11-30'],
            ['name' => 'Helder Jorge Langa', 'gender' => 'male', 'birth' => '2014-03-08'],
            ['name' => 'Joana Ernesto Manjate', 'gender' => 'female', 'birth' => '2014-08-14'],
            ['name' => 'Lucas Tomás Bila', 'gender' => 'male', 'birth' => '2014-02-22'],
            ['name' => 'Mariamo Sulemane Chissano', 'gender' => 'female', 'birth' => '2014-07-05'],
            ['name' => 'Nelson Bernardo Zandamela', 'gender' => 'male', 'birth' => '2013-12-19'],
            ['name' => 'Zacarias Pedro Munguambe', 'gender' => 'male', 'birth' => '2014-05-10'],
            ['name' => 'Aida Francisco Tivane', 'gender' => 'female', 'birth' => '2014-09-03'],
            ['name' => 'Benedito Osvaldo Nhantumbo', 'gender' => 'male', 'birth' => '2014-10-17'],
        ];

        $targetSchool = $secondarySchools->first();
        $districtId = $targetSchool->district_id;
        $provinceId = $targetSchool->province_id;

        foreach ($studentNames as $idx => $st) {
            $code = sprintf('SNE-2027-%04d', $idx + 1);
            $origin = $primarySchools[$idx % count($primarySchools)];
            $destSchool = $secondarySchools[$idx % $secondarySchools->count()];

            SchoolAllocation::updateOrCreate(
                ['student_code' => $code, 'academic_year' => 2027],
                [
                    'student_name' => $st['name'],
                    'gender' => $st['gender'],
                    'birth_date' => $st['birth'],
                    'origin_school_name' => $origin,
                    'destination_school_id' => $destSchool->id,
                    'grade_level' => '7',
                    'shift' => $idx % 2 === 0 ? 'manha' : 'tarde',
                    'status' => 'allocated',
                    'province_id' => $destSchool->province_id ?? $provinceId,
                    'district_id' => $destSchool->district_id ?? $districtId,
                    'notes' => "Afetação automática SDEJT/ZIP baseada na conclusão com aproveitamento da 6ª classe no Ensino Primário.",
                ]
            );
        }
    }
}

