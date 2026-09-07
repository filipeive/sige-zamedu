<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchoolTenantColumnsTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_id_column_exists_on_business_tables()
    {
        $tables = [
            'students',
            'teachers',
            'classes',
            'subjects',
            'class_schedules',
            'class_subjects',
            'enrollments',
            'enrollment_applications',
            'enrollment_documents',
            'grades',
            'assessments',
            'attendances',
            'payments',
            'expenses',
            'fee_types',
            'financial_categories',
            'communications',
            'conversations',
            'messages',
            'sms_logs',
            'sms_inbox',
            'events',
            'gate_logs',
            'staff_leave_requests',
            'material_lists',
            'users',
            'settings',
            'licenses',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                Schema::hasColumn($table, 'school_id'),
                "Coluna school_id em falta na tabela {$table}"
            );
        }
    }

    public function test_schools_backfill_artisan_command_populates_orphan_records()
    {
        // Criar aluno e turma sem school_id
        $student = Student::create([
            'first_name' => 'Manuel',
            'last_name' => 'Sitoe',
            'student_number' => 'STU-BACKFILL-01',
            'gender' => 'male',
            'status' => 'active',
            'birthdate' => '2010-01-01',
            'registration_date' => '2026-01-01',
            'monthly_fee' => 2000.00,
        ]);

        $class = ClassRoom::create([
            'name' => '10ª Classe B',
            'grade_level' => 10,
            'shift' => 'morning',
            'is_active' => true,
        ]);

        $this->assertNull($student->fresh()->school_id);
        $this->assertNull($class->fresh()->school_id);

        // Executar comando de backfill
        $exitCode = Artisan::call('schools:backfill');
        $this->assertEquals(0, $exitCode);

        $pilotSchool = School::where('code', 'ESG-PAZ')->first();
        $this->assertNotNull($pilotSchool);

        $this->assertEquals($pilotSchool->id, $student->fresh()->school_id);
        $this->assertEquals($pilotSchool->id, $class->fresh()->school_id);
    }
}
