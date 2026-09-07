<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'students',
            'teachers',
            'classes',
            'subjects',
            'class_schedules',
            'class_subjects',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'school_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'class_subjects',
            'class_schedules',
            'subjects',
            'classes',
            'teachers',
            'students',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'school_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['school_id']);
                    $table->dropColumn('school_id');
                });
            }
        }
    }
};
