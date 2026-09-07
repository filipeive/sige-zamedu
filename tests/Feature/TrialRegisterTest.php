<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\License;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TrialRegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
    }

    public function test_can_view_trial_registration_page()
    {
        $response = $this->get(route('public.trial-register'));
        $response->assertStatus(200);
        $response->assertSee('Registe a Sua Escola em 3 Passos');
    }

    public function test_can_register_school_for_15_day_trial()
    {
        $response = $this->post(route('public.trial-register.store'), [
            'school_name' => 'Escola Teste Antigravity',
            'institution_type' => 'escola_privada',
            'director_name' => 'Diretor Teste',
            'email' => 'direcao@escolateste.co.mz',
            'phone' => '+258841234567',
            'province' => 'Maputo Cidade',
            'district' => 'KaMpfumo',
            'estimated_students' => 300,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'direcao@escolateste.co.mz')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('admin'));

        $this->assertEquals('Escola Teste Antigravity', Setting::get('school_name'));

        $license = License::first();
        $this->assertNotNull($license);
        $this->assertEquals('active', $license->status);
    }
}
