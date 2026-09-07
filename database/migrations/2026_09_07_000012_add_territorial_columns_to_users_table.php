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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'province_id')) {
                $table->foreignId('province_id')->nullable()->after('school_id')->constrained('provinces')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('province_id')->constrained('districts')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'zip_id')) {
                $table->foreignId('zip_id')->nullable()->after('district_id')->constrained('zips')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['zip_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['province_id']);
            $table->dropColumn(['zip_id', 'district_id', 'province_id']);
        });
    }
};
