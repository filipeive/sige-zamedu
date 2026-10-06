<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('certificate_requests')) {
            Schema::create('certificate_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->enum('certificate_type', ['certidao', 'conclusao', 'official_minedh'])->default('official_minedh');
                $table->enum('status', ['pending_approval', 'approved', 'rejected', 'issued'])->default('pending_approval');
                $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
                $table->timestamp('requested_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'status']);
                $table->index(['student_id', 'certificate_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};

