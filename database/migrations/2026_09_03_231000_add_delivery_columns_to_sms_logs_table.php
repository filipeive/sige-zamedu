<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('sms_logs', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('sent_at');
            }
            if (! Schema::hasColumn('sms_logs', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('delivered_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn(['delivered_at', 'failed_at']);
        });
    }
};