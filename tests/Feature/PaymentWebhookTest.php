<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Student;
use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $province = Province::create(['name' => 'Maputo', 'code' => 'MPM', 'capital' => 'Maputo', 'is_active' => true]);
        $district = District::create(['province_id' => $province->id, 'name' => 'KaMpfumo', 'code' => 'MPM-KMP', 'is_active' => true]);
        $this->school = School::create(['province_id' => $province->id, 'district_id' => $district->id, 'name' => 'Escola Piloto', 'code' => 'ESC-PILOT', 'status' => 'active']);
        SchoolContext::setSchoolId($this->school->id);
    }

    public function test_mpesa_webhook_updates_payment_status()
    {
        // Create dependencies
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'student_number' => 'TEST-' . uniqid(),
            'gender' => 'male',
            'birthdate' => '2010-01-01',
            'registration_date' => now(),
            'monthly_fee' => 500,
            'status' => 'active'
        ]);

        // Create a dummy payment
        $payment = Payment::create([
            'student_id' => $student->id,
            'type' => 'mensalidade',
            'amount' => 500,
            'year' => 2024,
            'month' => 1,
            'due_date' => now()->addDays(10),
            'status' => 'pending',
            'reference_number' => 'TEST-MPESA-' . time()
        ]);

        $payload = [
            'output_ThirdPartyReference' => $payment->reference_number,
            'output_TransactionID' => 'MPESA-TX-' . time(),
            'output_ResponseCode' => 'INS-0',
            'output_ResponseDesc' => 'Success'
        ];

        $response = $this->postJson(route('webhooks.mpesa'), $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => 'processed']);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
            'payment_method' => 'mpesa',
            'transaction_id' => $payload['output_TransactionID']
        ]);

        // Cleanup
        $payment->delete();
    }

    public function test_emola_webhook_updates_payment_status()
    {
        // Create dependencies
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'student_number' => 'TEST-' . uniqid(),
            'gender' => 'female',
            'birthdate' => '2010-01-01',
            'registration_date' => now(),
            'monthly_fee' => 500,
            'status' => 'active'
        ]);

        // Create a dummy payment
        $payment = Payment::create([
            'student_id' => $student->id,
            'type' => 'mensalidade',
            'amount' => 500,
            'year' => 2024,
            'month' => 2,
            'due_date' => now()->addDays(10),
            'status' => 'pending',
            'reference_number' => 'TEST-EMOLA-' . time()
        ]);

        $payload = [
            'reference' => $payment->reference_number,
            'txn_id' => 'EMOLA-TX-' . time(),
            'status' => 'SUCCESS'
        ];

        $response = $this->postJson(route('webhooks.emola'), $payload);

        $response->assertStatus(200)
            ->assertJson(['status' => 'processed']);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
            'payment_method' => 'emola',
            'transaction_id' => $payload['txn_id']
        ]);

        // Cleanup
        $payment->delete();
    }
}
