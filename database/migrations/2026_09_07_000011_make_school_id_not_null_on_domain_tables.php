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
        // 1. Ensure a default school exists for backfill safety
        $defaultSchoolId = DB::table('schools')->value('id') ?? 1;

        // 2. Perform backfill on any orphaned records prior to changing nullability
        foreach ($this->domainTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                DB::table($tableName)
                    ->whereNull('school_id')
                    ->update(['school_id' => $defaultSchoolId]);
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
