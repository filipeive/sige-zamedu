<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'grades',
            'assessments',
            'attendances',
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
            'attendances',
            'assessments',
            'grades',
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
