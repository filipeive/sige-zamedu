<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sms_inbox', function (Blueprint $table) {
            $table->id();
            $table->string('provider_message_id')->nullable()->unique();
            $table->string('direction', 16)->default('incoming'); // incoming

            // Remetente (contact = quem enviou para nós)
            $table->string('from_phone', 32);
            $table->string('from_name')->nullable();

            // Destinatário (owner = o nosso número httpSMS)
            $table->string('to_phone', 32)->nullable();

            $table->text('content');
            $table->string('sim', 16)->nullable(); // SIM1 / SIM2

            // Matching com aluno/encarregado
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('parents', 'user_id')->nullOnDelete();
            $table->foreignId('matched_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Estado de leitura
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by')->nullable()->constrained('users')->nullOnDelete();

            // Auditoria
            $table->boolean('notified')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->index(['from_phone']);
            $table->index(['is_read', 'created_at']);
            $table->index(['student_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_inbox');
    }
};