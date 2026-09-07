<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ParentModel;
use App\Models\Student;
use App\Services\Sms\SmsNotificationService;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ParentPortalTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'parent']);

        $province = Province::create(['name' => 'Maputo', 'code' => 'MPM', 'capital' => 'Maputo', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'KaMpfumo', 'code' => 'MPM-KMP', 'is_active' => true]);
        $this->school = School::create(['province_id' => $province->id, 'district_id' => $district->id, 'name' => 'Escola Piloto', 'code' => 'ESC-PILOT', 'status' => 'active']);
        SchoolContext::setSchoolId($this->school->id);
    }

    public function test_parent_portal_dashboard_and_features(): void
    {
        $user = User::factory()->create(['email' => 'pai@visionarios.co.mz']);
        $user->assignRole('parent');

        $parent = ParentModel::create([
            'user_id' => $user->id,
            'first_name' => 'Pai',
            'last_name' => 'Teste',
            'email' => $user->email,
            'phone' => '841234567',
        ]);

        $student = Student::create([
            'first_name' => 'Ana',
            'last_name' => 'Silva',
            'student_number' => 'STU-1001',
            'parent_id' => $user->id,
            'birthdate' => '2015-05-10',
            'gender' => 'female',
            'registration_date' => now(),
            'monthly_fee' => 3500.00,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('parent.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Meus Educandos');

        $response = $this->actingAs($user)->get(route('parent.student-grades', $student));
        $response->assertStatus(200);
        $response->assertSee('Boletim de Notas');

        $response = $this->actingAs($user)->get(route('parent.student-attendance', $student));
        $response->assertStatus(200);
        $response->assertSee('Registo de Presenças');
    }

    public function test_sms_dispatches_in_app_notification(): void
    {
        $user = User::factory()->create(['email' => 'pai@visionarios.co.mz', 'phone' => '841234567']);
        $user->assignRole('parent');

        $parent = ParentModel::create([
            'user_id' => $user->id,
            'first_name' => 'Pai',
            'last_name' => 'Teste',
            'email' => $user->email,
            'phone' => '841234567',
        ]);

        $student = Student::create([
            'first_name' => 'Pedro',
            'last_name' => 'Silva',
            'student_number' => 'STU-1002',
            'parent_id' => $parent->user_id,
            'birthdate' => '2015-05-10',
            'gender' => 'male',
            'registration_date' => now(),
            'monthly_fee' => 3500.00,
            'status' => 'active',
        ]);

        $smsService = app(SmsNotificationService::class);

        $smsService->send(
            phone: '841234567',
            message: 'Notificação de teste via SMS',
            channel: 'guardian',
            context: 'unit-test',
            student: $student
        );

        $user->refresh();
        $this->assertEquals(1, $user->notifications()->count());
    }

    public function test_parent_can_send_message_and_stores_communication(): void
    {
        $user = User::factory()->create(['email' => 'pai.mensagem@visionarios.co.mz']);
        $user->assignRole('parent');

        ParentModel::create([
            'user_id' => $user->id,
            'first_name' => 'João',
            'last_name' => 'Manuel',
            'email' => $user->email,
            'phone' => '849998877',
        ]);

        $response = $this->actingAs($user)->post(route('parent.send-message'), [
            'subject' => 'Dúvida sobre mensalidade',
            'message' => 'Gostaria de obter informações sobre o pagamento do mês de Setembro.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('communications', [
            'created_by' => $user->id,
            'message' => 'Gostaria de obter informações sobre o pagamento do mês de Setembro.',
        ]);
    }
}
