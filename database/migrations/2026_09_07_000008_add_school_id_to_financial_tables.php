<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'payments',
            'expenses',
            'fee_types',
            'financial_categories',
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
            'financial_categories',
            'fee_types',
            'expenses',
            'payments',
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
