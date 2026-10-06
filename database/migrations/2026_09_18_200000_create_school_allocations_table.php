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
        Schema::create('school_allocations', function (Blueprint $table) {
            $table->id();
            $table->integer('academic_year')->default(2027)->index();
            $table->string('student_code')->nullable()->index();
            $table->string('student_name')->index();
            $table->enum('gender', ['male', 'female'])->default('male')->index();
            $table->date('birth_date')->nullable();
            
            // Escola Primária de Origem (onde concluiu a 6ª classe no Ensino Primário)
            $table->foreignId('origin_school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->string('origin_school_name')->nullable();
            
            // Escola Secundária de Destino (onde foi afetado para a 7ª classe - ESG1)
            $table->foreignId('destination_school_id')->constrained('schools')->cascadeOnDelete();
            
            // Classe e Turno de afetação
            $table->string('grade_level')->default('7')->index(); // 7ª Classe (ESG1)
            $table->string('shift')->default('diurno'); // diurno, manha, tarde
            
            // Status do Processo de Transição / Afetação
            $table->enum('status', ['allocated', 'confirmed', 'enrolled'])->default('allocated')->index();
            
            // Localização Territorial SNE
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_allocations');
    }
};

