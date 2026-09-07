<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Event;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PautaAndEventsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $primaryClass;
    protected $secondaryClass;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar permissões e admin
        Permission::firstOrCreate(['name' => 'view_grades']);
        Permission::firstOrCreate(['name' => 'manage_events']);
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->givePermissionTo(['view_grades', 'manage_events']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Criar turmas de Ensino Primário e Ensino Secundário
        $this->primaryClass = ClassRoom::create([
            'name' => '5ª Classe A',
            'grade_level' => 5,
            'school_year' => 2026,
            'is_active' => true,
        ]);

        $this->secondaryClass = ClassRoom::create([
            'name' => '10ª Classe B',
            'grade_level' => 10,
            'school_year' => 2026,
            'is_active' => true,
        ]);
    }

    public function test_class_room_identifies_primary_and_secondary_levels()
    {
        $this->assertTrue($this->primaryClass->isPrimary());
        $this->assertFalse($this->primaryClass->isSecondary());
        $this->assertEquals('Ensino Primário', $this->primaryClass->education_level_name);

        $this->assertTrue($this->secondaryClass->isSecondary());
        $this->assertFalse($this->secondaryClass->isPrimary());
        $this->assertEquals('Ensino Secundário', $this->secondaryClass->education_level_name);
    }

    public function test_can_view_pauta_trimestral()
    {
        $response = $this->actingAs($this->admin)->get(route('pautas.trimestral', $this->primaryClass->id));
        $response->assertStatus(200);
        $response->assertSee('Pauta Trimestral');
        $response->assertSee('5ª Classe A');
    }

    public function test_can_view_pauta_anual()
    {
        $response = $this->actingAs($this->admin)->get(route('pautas.anual', $this->secondaryClass->id));
        $response->assertStatus(200);
        $response->assertSee('Pauta Anual Consolidada');
        $response->assertSee('10ª Classe B');
    }

    public function test_can_view_pauta_final()
    {
        $response = $this->actingAs($this->admin)->get(route('pautas.final', $this->secondaryClass->id));
        $response->assertStatus(200);
        $response->assertSee('Pauta Final & Exames');
    }

    public function test_can_export_pauta_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('pautas.trimestral.excel', ['class' => $this->primaryClass->id, 'term' => 1, 'year' => 2026]));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_can_view_events_list()
    {
        Event::create([
            'title' => 'Reunião de Pais e Encarregados',
            'description' => 'Apresentação dos resultados do 1º Trimestre',
            'event_date' => now()->addDays(5),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'target_audience' => 'parents',
            'type' => 'meeting',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('events.index'));
        $response->assertStatus(200);
        $response->assertSee('Reunião de Pais e Encarregados');
    }
}
