<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TerritorialRolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
        ]);
    }
}
