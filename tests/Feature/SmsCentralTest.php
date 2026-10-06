<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SmsInbox;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsCentralTest extends TestCase
{
    use RefreshDatabase;

    private function createSchoolAndUser(): array
    {
        $province = \App\Models\Province::firstOrCreate(['code' => 'TEST'], ['name' => 'Província Teste']);
        $district = \App\Models\District::firstOrCreate(['code' => 'DIST-T'], ['province_id' => $province->id, 'name' => 'Distrito Teste']);
        $school = School::create([
            'province_id' => $province->id,
            'district_id' => $district->id,
            'name' => 'Escola Central Teste',
            'code' => 'ESC-CENTRAL-'.uniqid(),
            'status' => 'active',
        ]);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view_students', 'guard_name' => 'web']);
        $user = User::factory()->create(['school_id' => $school->id]);
        $user->givePermissionTo('view_students');

        return [$school, $user];
    }

    public function test_sms_inbox_redirects_to_central_sms_hub(): void
    {
        [$school, $user] = $this->createSchoolAndUser();

        $response = $this->actingAs($user)->get(route('sms.inbox.index'));

        $response->assertRedirect(route('sms.index', ['tab' => 'inbox']));
    }

    public function test_missing_sms_inbox_message_redirects_gracefully_without_404(): void
    {
        [$school, $user] = $this->createSchoolAndUser();

        // Requesting a non-existent message ID 999999 should not throw 404
        $response = $this->actingAs($user)->get('/sms/inbox/999999');

        $response->assertRedirect(route('sms.index', ['tab' => 'inbox']));
        $response->assertSessionHas('warning', 'Mensagem não encontrada ou já eliminada.');
    }

    public function test_central_sms_hub_loads_inbox_tab_successfully(): void
    {
        [$school, $user] = $this->createSchoolAndUser();

        SmsInbox::create([
            'from_phone' => '+258841234567',
            'to_phone' => '+258849876543',
            'content' => 'Olá, confirmo a recepção da informação.',
            'is_read' => false,
            'received_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('sms.index', ['tab' => 'inbox']));

        $response->assertStatus(200);
        $response->assertSee('Caixa de Entrada');
        $response->assertSee('+258841234567');
        $response->assertSee('Olá, confirmo a recepção da informação.');
    }
}
