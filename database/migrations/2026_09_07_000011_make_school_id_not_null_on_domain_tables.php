<?php

use App\Models\School;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The domain tables where school_id is converted to NOT NULL.
     *
     * @var array<string>
     */
    protected array $domainTables = [
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
        'settings',
        'licenses',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Check if any domain table has orphaned records needing backfill
        $hasOrphans = false;
        foreach ($this->domainTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                if (DB::table($tableName)->whereNull('school_id')->exists()) {
                    $hasOrphans = true;
                    break;
                }
            }
        }

        // 2. Perform backfill only if orphans exist
        if ($hasOrphans) {
            $defaultSchoolId = DB::table('schools')->value('id');
            if (!$defaultSchoolId) {
                $provinceId = DB::table('provinces')->value('id');
                if (!$provinceId) {
                    $provinceId = DB::table('provinces')->insertGetId([
                        'name' => 'Sede Nacional',
                        'code' => 'NAT',
                        'capital' => 'Maputo',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $districtId = DB::table('districts')->where('province_id', $provinceId)->value('id');
                if (!$districtId) {
                    $districtId = DB::table('districts')->insertGetId([
                        'province_id' => $provinceId,
                        'name' => 'Distrito Central',
                        'code' => 'NAT-CEN',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $defaultSchoolId = DB::table('schools')->insertGetId([
                    'province_id' => $provinceId,
                    'district_id' => $districtId,
                    'name' => 'Escola Padrão',
                    'code' => 'ESC-PADRAO',
                    'type' => 'public',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($this->domainTables as $tableName) {
                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                    DB::table($tableName)
                        ->whereNull('school_id')
                        ->update(['school_id' => $defaultSchoolId]);
                }
            }
        }

        // 3. Change school_id column to NOT NULL across all domain tables
        foreach ($this->domainTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('school_id')->nullable(false)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->domainTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('school_id')->nullable()->change();
                });
            }
        }
    }
};
