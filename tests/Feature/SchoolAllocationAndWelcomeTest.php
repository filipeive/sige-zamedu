<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\SchoolAllocation;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\EnrollmentApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SchoolAllocationAndWelcomeTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected Province $province;
    protected District $district;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->province = Province::create(['name' => 'Zambézia', 'code' => 'ZAM', 'is_active' => true]);
        $this->district = District::create(['province_id' => $this->province->id, 'name' => 'Namacurra', 'code' => 'NAM', 'is_active' => true]);
        $zip = Zip::create(['district_id' => $this->district->id, 'name' => 'ZIP Namacurra', 'code' => 'ZIP-01', 'is_active' => true]);
        
        $this->school = School::create([
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
            'zip_id' => $zip->id,
            'name' => 'Escola Secundária de Namacurra',
            'code' => 'ES-NAM',
            'type' => 'public',
            'is_active' => true,
        ]);
    }

    public function test_welcome_page_loads_with_official_year_and_allocations_links()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ESCOLA DOS VISIONÁRIOS');
        $response->assertSee('Aqui se Prepara a Nova Geração');
        $response->assertSee('Inscrições Abertas');
        $response->assertSee('Reservar Vaga / Pré-Inscrição');
        // Assegurar que o botão público de cadastrar escola foi ocultado
        $response->assertDontSee('Registar Escola');
    }

    public function test_public_allocation_check_page_returns_success_and_lists_records()
    {
        $allocation = SchoolAllocation::create([
            'academic_year' => next_school_year(),
            'student_code' => 'TEST-SNE-9999',
            'student_name' => 'Aluno de Teste SNE',
            'gender' => 'female',
            'origin_school_name' => 'EPC Teste Origem',
            'destination_school_id' => $this->school->id,
            'grade_level' => '7',
            'shift' => 'manha',
            'status' => 'allocated',
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
        ]);

        $response = $this->get('/public/allocations?search=TEST-SNE-9999');

        $response->assertStatus(200);
        $response->assertSee('TEST-SNE-9999');
        $response->assertSee('Aluno de Teste SNE');
        $response->assertSee('EPC Teste Origem');
        $response->assertSee('Confirmar &amp; Pré-Inscrever', false);
    }

    public function test_public_vacancies_page_returns_success()
    {
        $response = $this->get('/public/vacancies');

        $response->assertStatus(200);
        $response->assertSee('Quadro Oficial de Vagas Escolares');
        $response->assertSee('REPÚBLICA DE MOÇAMBIQUE · MINEDH');
    }

    public function test_pre_enrollment_prefills_allocation_data_and_confirms_on_store()
    {
        $allocation = SchoolAllocation::create([
            'academic_year' => next_school_year(),
            'student_code' => 'ALLOC-CONFIRM-001',
            'student_name' => 'Candidata Confirmada',
            'gender' => 'female',
            'birth_date' => '2014-05-15',
            'origin_school_name' => 'EPC Samora Machel',
            'destination_school_id' => $this->school->id,
            'grade_level' => '7',
            'shift' => 'diurno',
            'status' => 'allocated',
            'province_id' => $this->province->id,
            'district_id' => $this->district->id,
        ]);

        // Carregar a página com allocation_id
        $viewResponse = $this->get('/public/pre-enrollment?allocation_id=' . $allocation->id);
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Afetação Oficial SNE Reconhecida');
        $viewResponse->assertSee('ALLOC-CONFIRM-001');

        // Submeter pré-inscrição
        $postResponse = $this->post('/public/pre-enrollment', [
            'allocation_id' => $allocation->id,
            'school_id' => $this->school->id,
            'student' => [
                'first_name' => 'Candidata',
                'last_name' => 'Confirmada',
                'birth_date' => '2014-05-15',
                'gender' => 'female',
                'grade_level' => '7',
                'shift' => 'diurno',
                'address' => 'Bairro Centro, Rua 4',
                'emergency_contact' => 'Pai Teste',
                'emergency_phone' => '+258840001111',
            ],
            'parent' => [
                'first_name' => 'Encarregado',
                'last_name' => 'Teste',
                'phone' => '+258840001111',
                'relationship' => 'pai',
            ],
        ]);

        $postResponse->assertSessionHasNoErrors();
        $this->assertEquals('confirmed', $allocation->fresh()->status);
    }

    public function test_super_admin_sidebar_clean_in_global_view_and_full_in_support_mode()
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create([
            'email' => 'superadmin.clean@visionarios.co.mz',
        ]);
        $superAdmin->assignRole($role);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Visão global do Super Admin: sem modo suporte
        $responseGlobal = $this->actingAs($superAdmin)->get('/dashboard');
        $responseGlobal->assertStatus(200);
        $responseGlobal->assertSee('Administração Global &amp; Territorial', false);
        $responseGlobal->assertSee('Inspeção MINEDH');
        $responseGlobal->assertSee('Afetações SNE 2027');
        $responseGlobal->assertSee('Quadro de Vagas 2027');
        // Não deve ver itens de contexto escolar local como "Todos os Alunos" na sidebar
        $responseGlobal->assertDontSee('Todos os Alunos');

        // Modo Suporte do Super Admin
        $responseSupport = $this->actingAs($superAdmin)
            ->withSession([
                'support_mode' => true,
                'support_mode_school_id' => $this->school->id,
                'support_mode_school_name' => $this->school->name,
            ])
            ->get('/dashboard');

        $responseSupport->assertStatus(200);
        $responseSupport->assertSee('Modo Suporte Ativo');
        $responseSupport->assertSee('Sair do Suporte');
        // No modo suporte, os módulos da escola devem estar habilitados
        $responseSupport->assertSee('Todos os Alunos');
    }

    public function test_territory_school_tables_contain_view_details_button()
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin = User::factory()->create([
            'email' => 'superadmin.territory@visionarios.co.mz',
        ]);
        $superAdmin->assignRole($role);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. ZIP show
        $zip = Zip::first();
        $zipResponse = $this->actingAs($superAdmin)->get('/admin/territory/zips/' . $zip->id);
        $zipResponse->assertStatus(200);
        $zipResponse->assertSee('Ver Detalhes');
        $zipResponse->assertSee(route('territorial.school', $this->school->id));

        // 2. District show
        $distResponse = $this->actingAs($superAdmin)->get('/admin/territory/districts/' . $this->district->id);
        $distResponse->assertStatus(200);
        $distResponse->assertSee('Ver Detalhes');
        $distResponse->assertSee(route('territorial.school', $this->school->id));

        // 3. Province show
        $provResponse = $this->actingAs($superAdmin)->get('/admin/territory/provinces/' . $this->province->id);
        $provResponse->assertStatus(200);
        $provResponse->assertSee('Ver Detalhes');
        $provResponse->assertSee(route('territorial.school', $this->school->id));
    }
}

