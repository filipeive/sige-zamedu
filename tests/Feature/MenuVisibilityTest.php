<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Province;
use App\Models\School;
use App\Services\SchoolContext;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuVisibilityTest extends TestCase
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

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
    }

    public function test_admin_can_access_menu_visibility_tab()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/settings?tab=menus');

        $response->assertStatus(200);
        $response->assertSee('Gestão de Visibilidade do Menu Lateral');
        $response->assertSee('Visibilidade de Menus');
    }

    public function test_admin_can_update_menu_visibility_settings()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'active_tab' => 'menus',
            'settings' => [
                'menu_visibility_teacher' => ['teacher_dashboard', 'teacher_classes'],
                'menu_visibility_parent' => ['parent_dashboard', 'parent_children'],
            ],
        ];

        $response = $this->actingAs($admin)->patch('/admin/settings', $payload);

        $response->assertRedirect('/admin/settings?tab=menus');
        $response->assertSessionHas('success');

        $teacherSetting = Setting::where('key', 'menu_visibility_teacher')->first();
        $this->assertNotNull($teacherSetting);
        $this->assertStringContainsString('teacher_dashboard', $teacherSetting->value);
        $this->assertStringContainsString('teacher_classes', $teacherSetting->value);
        $this->assertStringNotContainsString('teacher_leaves', $teacherSetting->value);

        // Test helper function is_menu_visible
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $this->assertTrue(is_menu_visible('teacher_dashboard', $teacher));
        $this->assertTrue(is_menu_visible('teacher_classes', $teacher));
        $this->assertFalse(is_menu_visible('teacher_leaves', $teacher));
    }

    public function test_pedagogy_dashboard_renders_without_errors()
    {
        Role::firstOrCreate(['name' => 'pedagogy', 'guard_name' => 'web']);
        $pedagogy = User::factory()->create();
        $pedagogy->assignRole('pedagogy');

        $response = $this->actingAs($pedagogy)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Pedagógico');
        $response->assertSee('Desempenho por Turma');
    }
}
