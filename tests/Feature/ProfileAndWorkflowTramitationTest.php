<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\Student;
use App\Models\PedagogicalMapSubmission;
use App\Models\CertificateRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ProfileAndWorkflowTramitationTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected Province $province;
    protected District $district;
    protected Zip $zip;
    protected User $director;
    protected User $secretary;
    protected User $zipCoordinator;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar estrutura territorial
        $this->province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $this->district = District::create(['province_id' => $this->province->id, 'name' => 'Quelimane', 'code' => 'QUE', 'is_active' => true]);
        $this->zip = Zip::create(['district_id' => $this->district->id, 'name' => 'ZIP Quelimane Central', 'code' => 'ZIP-QC', 'is_active' => true]);
        
        $this->school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $this->zip->id,
            'name' => 'Escola Secundária Geral de Quelimane',
            'code' => 'ESG-QUE',
            'type' => 'public',
            'is_active' => true,
        ]);

        // Criar permissões necessárias
        Permission::create(['name' => 'view_reports']);
        Permission::create(['name' => 'view_grades']);
        Permission::create(['name' => 'view_students']);
        Permission::create(['name' => 'approve_certificates']);

        // Criar papéis essenciais
        $roleSuperAdmin = Role::create(['name' => 'super_admin']);
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleSecretary = Role::create(['name' => 'secretary']);
        $roleTeacher = Role::create(['name' => 'teacher']);
        $roleZipCoordinator = Role::create(['name' => 'zip_coordinator']);
        $roleParent = Role::create(['name' => 'parent']);
        $roleStudent = Role::create(['name' => 'student']);

        // Atribuir permissões aos papéis
        $roleAdmin->givePermissionTo(['view_reports', 'view_grades', 'view_students', 'approve_certificates']);
        $roleSecretary->givePermissionTo(['view_students']);
        $roleZipCoordinator->givePermissionTo(['view_reports']);

        // Usuários
        $this->director = User::factory()->create([
            'name' => 'Director Geral',
            'email' => 'director@quelimane.edu.mz',
            'school_id' => $this->school->id,
            'phone' => '+258841234567',
        ]);
        $this->director->assignRole('admin');

        $this->secretary = User::factory()->create([
            'name' => 'Secretário Escolar',
            'email' => 'secretaria@quelimane.edu.mz',
            'school_id' => $this->school->id,
            'phone' => '+258842345678',
        ]);
        $this->secretary->assignRole('secretary');

        $this->zipCoordinator = User::factory()->create([
            'name' => 'Coordenador ZIP',
            'email' => 'coord.zip@quelimane.edu.mz',
            'zip_id' => $this->zip->id,
            'phone' => '+258843456789',
        ]);
        $this->zipCoordinator->assignRole('zip_coordinator');
    }

    public function test_profile_completion_calculation()
    {
        $newUser = User::factory()->create([
            'name' => 'Novo Utilizador',
            'email' => 'novo@exemplo.com',
            'phone' => null,
            'school_id' => null,
            'avatar' => null,
        ]);

        $completion = $newUser->getProfileCompletion();
        // Nome (20%) + Email (20%) = 40%
        $this->assertEquals(40, $completion['percentage']);
        $this->assertFalse($completion['is_complete']);

        // Adicionar Telefone (+20%) e Escola (+20%)
        $newUser->update([
            'phone' => '+258840000000',
            'school_id' => $this->school->id,
        ]);

        $completion2 = $newUser->getProfileCompletion();
        $this->assertEquals(80, $completion2['percentage']);

        // Atribuir Papel (+20%) -> 100%
        $newUser->assignRole('parent');
        $completion3 = $newUser->getProfileCompletion();
        $this->assertEquals(100, $completion3['percentage']);
        $this->assertTrue($completion3['is_complete']);
    }

    public function test_user_can_select_role_and_school_via_dashboard()
    {
        $user = User::factory()->create([
            'name' => 'Pai Registado',
            'email' => 'pai@exemplo.com',
            'school_id' => null,
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.select-role'), [
            'role' => 'parent',
            'school_id' => $this->school->id,
        ]);

        $response->assertRedirect(route('parent.dashboard'));
        $this->assertTrue($user->fresh()->hasRole('parent'));
        $this->assertEquals($this->school->id, $user->fresh()->school_id);
    }

    public function test_pedagogical_map_submission_to_zip_and_approval()
    {
        // Submeter Mapa da Escola à ZIP
        $response = $this->actingAs($this->director)->post(route('reports.pedagogical-map.submit-zip'), [
            'school_id' => $this->school->id,
            'year'      => 2026,
            'term'      => 1,
            'notes'     => 'Submissão oficial do 1º Trimestre para homologação ZIP.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $submission = PedagogicalMapSubmission::where('school_id', $this->school->id)
            ->where('year', 2026)
            ->where('term', 1)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals(PedagogicalMapSubmission::STATUS_SUBMITTED, $submission->status);
        $this->assertEquals($this->director->id, $submission->submitted_by);

        // Aprovar na ZIP
        $responseApprove = $this->actingAs($this->zipCoordinator)->post(route('reports.pedagogical-map.approve-zip', $submission), [
            'approval_notes' => 'Homologado sem inconformidades pela coordenação da ZIP.',
        ]);

        $responseApprove->assertRedirect();
        $responseApprove->assertSessionHas('success');

        $submission->refresh();
        $this->assertEquals(PedagogicalMapSubmission::STATUS_APPROVED, $submission->status);
        $this->assertEquals($this->zipCoordinator->id, $submission->approved_by);
        $this->assertNotNull($submission->approved_at);
    }

    public function test_certificate_request_and_director_approval_workflow()
    {
        $student = Student::create([
            'school_id'         => $this->school->id,
            'first_name'        => 'Amélia',
            'last_name'         => 'Mabunda',
            'birthdate'         => '2008-04-12',
            'gender'            => 'female',
            'student_number'    => 'ALU-2026-999',
            'registration_date' => now()->toDateString(),
            'monthly_fee'       => 0,
            'status'            => 'active',
        ]);

        // Secretaria solicita emissão de certidão ao Director
        $responseRequest = $this->actingAs($this->secretary)->post(route('certificates.request', $student), [
            'certificate_type' => 'certidao',
            'notes'            => 'Aluno solicitou certidão de habilitações para transferência.',
        ]);

        $responseRequest->assertRedirect();
        $responseRequest->assertSessionHas('success');

        $certRequest = CertificateRequest::where('student_id', $student->id)->first();
        $this->assertNotNull($certRequest);
        $this->assertEquals(CertificateRequest::STATUS_PENDING, $certRequest->status);
        $this->assertEquals($this->secretary->id, $certRequest->requested_by);

        // Director aprova o pedido
        $responseApprove = $this->actingAs($this->director)->post(route('certificates.approve', $certRequest), [
            'dispatch_notes' => 'Aprovado. Emitir documento oficial com selo branco.',
        ]);

        $responseApprove->assertRedirect();
        $responseApprove->assertSessionHas('success');

        $certRequest->refresh();
        $this->assertEquals(CertificateRequest::STATUS_APPROVED, $certRequest->status);
        $this->assertEquals($this->director->id, $certRequest->approved_by);
        $this->assertNotNull($certRequest->approved_at);
    }
}
