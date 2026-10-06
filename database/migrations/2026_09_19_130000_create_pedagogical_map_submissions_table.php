<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pedagogical_map_submissions')) {
            Schema::create('pedagogical_map_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->integer('year');
                $table->tinyInteger('term'); // 1, 2, 3
                $table->enum('status', ['draft', 'submitted_zip', 'approved_zip', 'rejected_zip'])->default('draft');
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('submitted_at')->nullable();
                $table->text('submission_notes')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();

                $table->unique(['school_id', 'year', 'term'], 'school_year_term_unique');
                $table->index(['year', 'term', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pedagogical_map_submissions');
    }
};

