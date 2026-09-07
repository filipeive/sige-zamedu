<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Teacher;
use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        License::create([
            'client_name' => 'Escola Teste',
            'license_key' => 'KEY-TEST-TEACHERS',
            'expires_at' => now()->addDays(30),
            'status' => 'active',
        ]);
    }

    public function test_authenticated_user_with_permission_can_view_teachers()
    {
        $user = User::factory()->create();
        Permission::findOrCreate('view_teachers');
        $user->givePermissionTo('view_teachers');

        Teacher::create([
            'first_name' => 'Carlos',
            'last_name' => 'Mendes',
            'email' => 'carlos.mendes@zamedu.co.mz',
            'phone' => '+258 84 123 4567',
            'qualification' => 'Licenciatura em Matemática',
            'specialization' => 'Matemática',
            'bi_number' => '123456789A',
            'hire_date' => now()->subYears(3),
            'salary' => 35000.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/teachers');

        $response->assertStatus(200);
        $response->assertSee('Carlos Mendes');
        $response->assertSee('Matemática');
    }

    public function test_years_experience_accessor_returns_positive_integer()
    {
        $teacher = new Teacher([
            'hire_date' => now()->subYears(4),
        ]);

        $this->assertEquals(4, $teacher->years_experience);
    }
}
