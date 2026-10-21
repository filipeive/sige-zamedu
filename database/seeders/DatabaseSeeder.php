<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Executar os seeders do sistema.
     * Separa seeders core (obrigatórios) de dados demo.
     */
    public function run()
    {
        // Seeders Estruturais / Core (Necessários para qualquer nova escola)
        $this->call([
            TerritorialSeeder::class,
            PermissionSeeder::class,
            SuperAdminSeeder::class,
            SettingSeeder::class,
            FinancialCategorySeeder::class,
        ]);

        // Dados Operacionais e Demo (Escola dos Visionários)
        $demoSchoolId = \App\Models\School::where('code', 'VIS-QUE')->value('id')
            ?? \App\Models\School::where('code', 'ESG-NAM')->value('id')
            ?? \App\Models\School::value('id');

        if ($demoSchoolId) {
            \App\Services\SchoolContext::setSchoolId($demoSchoolId);

            $this->call([
                SubjectSeeder::class,
                ClassRoomSeeder::class,
                DemoDataSeeder::class,
            ]);

            \App\Services\SchoolContext::clear();
        }
    }
}