<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Normaliza o campo grade_level nas tabelas `classes` e `subjects`
 * para alinhar com a taxonomia oficial do SNE Moçambique (Lei 18/2018).
 *
 * Mapeamento (remoção do offset de +1):
 *   grade_level = 0  → Pré-Infantil   → mantém-se 0 (Pré-Escolar unificado)
 *   grade_level = 1  → Pré-Escolar    → 0  (unificado com Pré-Infantil)
 *   grade_level = 2  → 1ª Classe      → 1
 *   grade_level = 3  → 2ª Classe      → 2
 *   ...
 *   grade_level = 13 → 12ª Classe     → 12
 *
 * Down: inverte a operação (grade_level >= 1 → grade_level + 1).
 *
 * Nota: Usa loop por valor (alto→baixo) para compatibilidade com SQLite e MySQL,
 * evitando o `ORDER BY` em UPDATE que só é suportado por MySQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Unificar Pré-Escolar (1 → 0)
        DB::table('classes')->where('grade_level', 1)->update(['grade_level' => 0]);
        DB::table('subjects')->where('grade_level', 1)->update(['grade_level' => 0]);

        // 2. Remover offset: iterar do valor mais alto para o mais baixo
        //    para evitar colisões (ex: 13→12 antes de 12→11, etc.)
        foreach (range(13, 2) as $from) {
            DB::table('classes')->where('grade_level', $from)->update(['grade_level' => $from - 1]);
            DB::table('subjects')->where('grade_level', $from)->update(['grade_level' => $from - 1]);
        }
    }

    public function down(): void
    {
        // Inverte: iterar do valor mais baixo para o mais alto
        foreach (range(1, 12) as $from) {
            DB::table('classes')->where('grade_level', $from)->update(['grade_level' => $from + 1]);
            DB::table('subjects')->where('grade_level', $from)->update(['grade_level' => $from + 1]);
        }

        // Restaurar Pré-Escolar (0 → 1) — best-effort
        DB::table('classes')->where('grade_level', 0)->update(['grade_level' => 1]);
        DB::table('subjects')->where('grade_level', 0)->update(['grade_level' => 1]);
    }
};

