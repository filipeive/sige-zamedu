<?php

namespace App\Helpers;

/**
 * EducationLevel — Fonte Única de Verdade para a Taxonomia do SNE Moçambique
 *
 * Implementa a estrutura oficial do Subsistema de Educação Geral
 * conforme a Lei n.º 18/2018 (Sistema Nacional de Educação — SNE).
 *
 * Estrutura oficial:
 *  • Ensino Primário
 *      - 1.º Ciclo EP: 1ª – 3ª Classe
 *      - 2.º Ciclo EP: 4ª – 6ª Classe
 *  • Ensino Secundário Geral
 *      - 1.º Ciclo ESG (ESG1): 7ª – 9ª Classe
 *      - 2.º Ciclo ESG (ESG2): 10ª – 12ª Classe
 *
 *  • Educação Básica (escolaridade obrigatória): 1ª – 9ª Classe
 *  • Exames Nacionais: 9ª, 10ª e 12ª Classe
 *
 *  • Pré-Escolar (grade_level = 0): Subsistema separado do SNE Geral.
 *    Visível apenas em escolas com tipo 'private' ou 'community'.
 *    Não aparece nos filtros e estatísticas SNE Geral.
 */
class EducationLevel
{
    // ─── Constantes de grade_level ──────────────────────────────────────────
    public const PRE_SCHOOL = 0;   // Pré-Escolar/Pré-Infantil (fora do SNE Geral)

    // Ensino Primário
    public const GRADE_1  = 1;
    public const GRADE_2  = 2;
    public const GRADE_3  = 3;
    public const GRADE_4  = 4;
    public const GRADE_5  = 5;
    public const GRADE_6  = 6;

    // Ensino Secundário Geral
    public const GRADE_7  = 7;
    public const GRADE_8  = 8;
    public const GRADE_9  = 9;
    public const GRADE_10 = 10;
    public const GRADE_11 = 11;
    public const GRADE_12 = 12;

    // ─── Agrupamentos oficiais ───────────────────────────────────────────────
    public const PRIMARY_GRADES   = [1, 2, 3, 4, 5, 6];
    public const SECONDARY_GRADES = [7, 8, 9, 10, 11, 12];

    /** 1.º Ciclo Ensino Primário */
    public const EP1_GRADES = [1, 2, 3];
    /** 2.º Ciclo Ensino Primário */
    public const EP2_GRADES = [4, 5, 6];
    /** 1.º Ciclo ESG (ESG1) */
    public const ESG1_GRADES = [7, 8, 9];
    /** 2.º Ciclo ESG (ESG2) */
    public const ESG2_GRADES = [10, 11, 12];

    /** Educação Básica = Escolaridade Obrigatória (1ª–9ª) */
    public const BASIC_EDUCATION_GRADES = [1, 2, 3, 4, 5, 6, 7, 8, 9];

    /** Classes sujeitas a Exame Nacional MINEDH */
    public const NATIONAL_EXAM_GRADES = [9, 10, 12];

    /** Todos os níveis SNE Geral (exclui Pré-Escolar) */
    public const SNE_GRADES = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    // ─── Mapa de rótulos ────────────────────────────────────────────────────
    public const GRADES = [
        0  => 'Pré-Escolar / Pré-Infantil',
        1  => '1ª Classe',
        2  => '2ª Classe',
        3  => '3ª Classe',
        4  => '4ª Classe',
        5  => '5ª Classe',
        6  => '6ª Classe',
        7  => '7ª Classe',
        8  => '8ª Classe',
        9  => '9ª Classe',
        10 => '10ª Classe',
        11 => '11ª Classe',
        12 => '12ª Classe',
    ];

    /**
     * Mapa de rótulos agrupados por nível e ciclo MINEDH.
     * Útil para selects com optgroup nos formulários.
     */
    public const GRADE_GROUPS = [
        'Pré-Escolar (Privado/Comunitário)' => [
            0 => 'Pré-Escolar / Pré-Infantil',
        ],
        'Ensino Primário — 1.º Ciclo (1ª–3ª)' => [
            1 => '1ª Classe',
            2 => '2ª Classe',
            3 => '3ª Classe',
        ],
        'Ensino Primário — 2.º Ciclo (4ª–6ª)' => [
            4 => '4ª Classe',
            5 => '5ª Classe',
            6 => '6ª Classe ⭐ Exame Nacional',
        ],
        'Ensino Secundário — ESG1, 1.º Ciclo (7ª–9ª)' => [
            7  => '7ª Classe',
            8  => '8ª Classe',
            9  => '9ª Classe ⭐ Exame Nacional',
        ],
        'Ensino Secundário — ESG2, 2.º Ciclo (10ª–12ª)' => [
            10 => '10ª Classe ⭐ Exame Nacional',
            11 => '11ª Classe',
            12 => '12ª Classe ⭐ Exame Nacional',
        ],
    ];

    /**
     * Mapa de filtros de nível por chave textual.
     * Usado em tabs, filtros URL e controllers.
     */
    public const LEVEL_KEYS = [
        'preschool' => 'Pré-Escolar',
        'primary'   => 'Ensino Primário (1ª–6ª)',
        'secondary' => 'Ensino Secundário (7ª–12ª)',
        'ep1'       => '1.º Ciclo EP (1ª–3ª)',
        'ep2'       => '2.º Ciclo EP (4ª–6ª)',
        'esg1'      => 'ESG1 — 1.º Ciclo (7ª–9ª)',
        'esg2'      => 'ESG2 — 2.º Ciclo (10ª–12ª)',
        'basic'     => 'Educação Básica (1ª–9ª)',
    ];

    // ─── Métodos de consulta ─────────────────────────────────────────────────

    /**
     * Retorna o rótulo de um grade_level.
     */
    public static function label(int $grade): string
    {
        return self::GRADES[$grade] ?? "{$grade}ª Classe";
    }

    /**
     * Retorna a chave textual do nível de ensino (para URLs e filtros).
     */
    public static function levelKey(int $grade): string
    {
        if ($grade === 0) return 'preschool';
        if ($grade >= 1 && $grade <= 6) return 'primary';
        return 'secondary';
    }

    /**
     * Retorna o nome do nível de ensino (para exibição).
     */
    public static function levelName(int $grade): string
    {
        return match (self::levelKey($grade)) {
            'preschool' => 'Pré-Escolar',
            'primary'   => 'Ensino Primário',
            'secondary' => 'Ensino Secundário',
            default     => 'Ensino Geral',
        };
    }

    /**
     * Retorna o nome do ciclo MINEDH (para cabeçalhos de pautas e relatórios).
     */
    public static function cycleName(int $grade): string
    {
        if ($grade === 0) return 'Pré-Escolar';
        if ($grade >= 1 && $grade <= 3) return '1.º Ciclo do Ensino Primário';
        if ($grade >= 4 && $grade <= 6) return '2.º Ciclo do Ensino Primário';
        if ($grade >= 7 && $grade <= 9) return 'ESG — 1.º Ciclo (ESG1)';
        if ($grade >= 10 && $grade <= 12) return 'ESG — 2.º Ciclo (ESG2)';
        return 'Ensino Geral';
    }

    /**
     * Retorna abreviatura do ciclo (para badges e pautas).
     */
    public static function cycleAbbr(int $grade): string
    {
        if ($grade === 0) return 'PRÉ';
        if ($grade >= 1 && $grade <= 3) return '1.ºCiclo EP';
        if ($grade >= 4 && $grade <= 6) return '2.ºCiclo EP';
        if ($grade >= 7 && $grade <= 9) return 'ESG1';
        if ($grade >= 10 && $grade <= 12) return 'ESG2';
        return 'EP';
    }

    // ─── Predicados ─────────────────────────────────────────────────────────

    public static function isPreschool(int $grade): bool
    {
        return $grade === 0;
    }

    public static function isPrimary(int $grade): bool
    {
        return $grade >= 1 && $grade <= 6;
    }

    public static function isSecondary(int $grade): bool
    {
        return $grade >= 7 && $grade <= 12;
    }

    public static function isBasicEducation(int $grade): bool
    {
        return in_array($grade, self::BASIC_EDUCATION_GRADES, true);
    }

    public static function isNationalExamGrade(int $grade): bool
    {
        return in_array($grade, self::NATIONAL_EXAM_GRADES, true);
    }

    public static function isSneGeral(int $grade): bool
    {
        return $grade >= 1 && $grade <= 12;
    }

    // ─── Helpers para queries ────────────────────────────────────────────────

    /**
     * Retorna os grade_levels para uma chave de nível/ciclo.
     * Usado em ClassRoom::scopeByEducationLevel().
     */
    public static function gradesForLevel(string $level): array
    {
        return match ($level) {
            'preschool' => [0],
            'primary'   => self::PRIMARY_GRADES,
            'secondary' => self::SECONDARY_GRADES,
            'ep1'       => self::EP1_GRADES,
            'ep2'       => self::EP2_GRADES,
            'esg1'      => self::ESG1_GRADES,
            'esg2'      => self::ESG2_GRADES,
            'basic'     => self::BASIC_EDUCATION_GRADES,
            'sne'       => self::SNE_GRADES,
            default     => self::SNE_GRADES,
        };
    }

    /**
     * Determina o modo de ensino recomendado pelo nível.
     * single_teacher → Pré-Escolar e Ensino Primário
     * subject_based  → Ensino Secundário
     */
    public static function defaultTeachingMode(int $grade): string
    {
        return self::isSecondary($grade) ? 'subject_based' : 'single_teacher';
    }

    /**
     * Retorna a cor CSS (Tailwind) associada ao nível de ensino.
     * Usado em badges e cards.
     */
    public static function tailwindColor(int $grade): string
    {
        if ($grade === 0) return 'pink';
        if ($grade >= 1 && $grade <= 6) return 'blue';
        return 'purple';
    }

    /**
     * Retorna a cor hex associada ao nível de ensino.
     * Usado em PDFs e gráficos.
     */
    public static function hexColor(int $grade): string
    {
        if ($grade === 0) return '#ec4899';
        if ($grade >= 1 && $grade <= 6) return '#3b82f6';
        return '#8b5cf6';
    }
}
