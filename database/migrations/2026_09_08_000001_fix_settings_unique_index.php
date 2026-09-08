<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                try {
                    $table->dropUnique('settings_key_unique');
                } catch (\Throwable $e) {
                    // Ignore if already dropped
                }
            });

            Schema::table('settings', function (Blueprint $table) {
                try {
                    $table->index(['key', 'school_id'], 'idx_settings_key_school');
                } catch (\Throwable $e) {
                    // Ignore if index already exists
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                try {
                    $table->dropIndex('idx_settings_key_school');
                } catch (\Throwable $e) {}
            });
        }
    }
};
