<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();

            // Categorização da mensagem
            $table->string('channel', 32)->default('guardian'); // guardian | event | payment | bulk | system
            $table->string('context', 64)->nullable(); // ex: mark-exit, falta, propina

            // Destinatário
            $table->string('recipient_name')->nullable();
            $table->string('phone', 32);

            // Conteúdo
            $table->text('message');

            // Resultado
            $table->string('status', 32)->default('pending'); // pending | sent | failed | disabled
            $table->string('provider', 32)->default('httpsms');
            $table->string('provider_id')->nullable();
            $table->text('provider_response')->nullable();
            $table->text('error')->nullable();

            // Custos (opcional — httpSMS expõe no response)
            $table->decimal('cost', 8, 4)->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['channel', 'created_at']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};