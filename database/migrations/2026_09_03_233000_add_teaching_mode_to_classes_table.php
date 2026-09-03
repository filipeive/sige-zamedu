<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Modo de ensino: como é gerida a relação turma↔professor
            //   single_teacher      → Pré-Escolar/Primário: 1 professor regente (+ opcional assistente)
            //   subject_based       → Secundário: professor atribuído por disciplina (class_subjects)
            $table->string('teaching_mode', 32)->nullable()->after('shift');

            // Professor assistente (só relevante para single_teacher do primário)
            $table->unsignedBigInteger('assistant_teacher_id')->nullable()->after('teacher_id');

            $table->foreign('assistant_teacher_id')
                ->references('id')
                ->on('teachers')
                ->nullOnDelete();

            $table->index('teaching_mode');
            $table->index('assistant_teacher_id');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['assistant_teacher_id']);
            $table->dropIndex(['assistant_teacher_id']);
            $table->dropIndex(['teaching_mode']);
            $table->dropColumn(['teaching_mode', 'assistant_teacher_id']);
        });
    }
};