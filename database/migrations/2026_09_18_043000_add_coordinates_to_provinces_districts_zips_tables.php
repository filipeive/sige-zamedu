<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['provinces', 'districts', 'zips'];

        foreach ($tables as $tableName) {
            if (!Schema::hasColumn($tableName, 'latitude')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->decimal('latitude', 10, 7)->nullable()->after('is_active');
                    $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['provinces', 'districts', 'zips'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['latitude', 'longitude']);
            });
        }
    }
};
