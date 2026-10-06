<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassRoom;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_runs_successfully_and_seeds_demo_showcase_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Validar 11 Províncias de Moçambique
        $this->assertEquals(11, Province::count());
        $this->assertDatabaseHas('provinces', ['code' => 'ZAM', 'name' => 'Zambézia']);
        $this->assertDatabaseHas('provinces', ['code' => 'MPC', 'name' => 'Maputo Cidade']);
        $this->assertDatabaseHas('provinces', ['code' => 'SOF', 'name' => 'Sofala']);
        $this->assertDatabaseHas('provinces', ['code' => 'NAP', 'name' => 'Nampula']);

        // 2. Validar Escolas em várias regiões
        $this->assertGreaterThanOrEqual(10, School::count());
        $this->assertDatabaseHas('schools', ['code' => 'ESG-NAM', 'type' => 'public']);
        $this->assertDatabaseHas('schools', ['code' => 'ESG-PAZ', 'type' => 'community']);
        $this->assertDatabaseHas('schools', ['code' => 'ESG-JOS', 'name' => 'Escola Secundária Josina Machel']);

        // 3. Validar Contas de Demonstração
        $demoEmails = [
            'superadmin@visionarios.co.mz',
            'inspector.zambezia@minedh.gov.mz',
            'director.namacurra@minedh.gov.mz',
            'zip.namacurra@minedh.gov.mz',
            'admin@visionarios.co.mz',
            'pedagogia@visionarios.co.mz',
            'professor@visionarios.co.mz',
            'secretaria@visionarios.co.mz',
            'pai@visionarios.co.mz',
            'seguranca@visionarios.co.mz',
        ];

        foreach ($demoEmails as $email) {
            $this->assertDatabaseHas('users', ['email' => $email]);
            $user = User::withoutGlobalScopes()->where('email', $email)->first();
            $this->assertNotNull($user, "User {$email} must exist in users table");
            $this->assertTrue($user->roles()->exists(), "User {$email} must have an assigned Spatie role");
        }

        // 4. Validar Turmas e Alunos
        $this->assertGreaterThan(0, ClassRoom::count());
        $this->assertGreaterThan(0, Student::count());
    }
}
