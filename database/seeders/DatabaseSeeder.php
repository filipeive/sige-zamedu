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

        // Dados Demo (Apenas em desenvolvimento/demonstração)
        if (app()->environment('local', 'testing', 'demo')) {
            $this->call([
                SubjectSeeder::class,
                ClassRoomSeeder::class,
                DemoDataSeeder::class,
            ]);
        }
    }
}