<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Grade extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'student_id',
        'class_id',
        'subject_id',
        'assessment_id',
        'grade',
        'assessment_type',
        'term',
        'year',
        'date_recorded',
        'teacher_id',
        'comments',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'date_recorded' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['grade', 'assessment_type', 'term'])
            ->logOnlyDirty();
    }

    // ===========================================================
    // SISTEMA DE AVALIAÇÃO DE MOÇAMBIQUE (ENSINO PRIMÁRIO)
    // Diploma Ministerial nº 59/2015
    //
    // Tipos de avaliação por trimestre:
    // - ACS1, ACS2, ACS3: Avaliação Contínua e Sistemática (3 provas)
    // - ACP: Avaliação Contínua Parcial (prova trimestral)
    // - ACF: Avaliação Contínua Final (exame da 6ª classe)
    // ===========================================================

    const TYPE_ACS1 = 'ACS1';

    const TYPE_ACS2 = 'ACS2';

    const TYPE_ACS3 = 'ACS3';

    const TYPE_ACP = 'ACP';

    const TYPE_ACF = 'ACF';

    public static array $continuousTypes = [self::TYPE_ACS1, self::TYPE_ACS2, self::TYPE_ACS3, 'continuous', 'test', 'assignment', 'participation'];

    // Relacionamentos
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // Scopes
    public function scopeCurrentYear($query)
    {
        return $query->where('year', current_school_year());
    }

    public function scopeForTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    public function scopeContinuous($query)
    {
        return $query->whereIn('assessment_type', self::$continuousTypes);
    }

    public function scopeForAssessment($query, $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    // Accessors
    public function getGradeStatusAttribute()
    {
        if ($this->grade >= 17) {
            return 'Muito Bom';
        }
        if ($this->grade >= 14) {
            return 'Bom';
        }
        if ($this->grade >= 10) {
            return 'Suficiente';
        }
        if ($this->grade >= 7) {
            return 'Mediocre';
        }

        return 'Mau';
    }

    public function getFormattedGradeAttribute()
    {
        if ($this->assessment_type === 'behavioral') {
            return $this->comments ?: 'N/A';
        }

        return number_format((float) $this->grade, 1);
    }

    // ===========================================================
    // CÁLCULO DAS MÉDIAS (MÉtodo Moçambicano)
    // ===========================================================

    /**
     * MACS - Média das Avaliações Contínuas e Sistemáticas
     * MACS = (ACS1 + ACS2 + ACS3) / 3
     */
    public static function calculateMACS(int $studentId, ?int $subjectId, int $classId, int $term, $allGrades = null): ?float
    {
        if ($allGrades !== null) {
            $grades = $allGrades->filter(function ($g) use ($studentId, $subjectId, $classId, $term) {
                return (int) $g->student_id === $studentId
                    && (int) $g->class_id === $classId
                    && (int) $g->term === $term
                    && (! $subjectId || (int) $g->subject_id === $subjectId)
                    && in_array($g->assessment_type, self::$continuousTypes);
            });
        } else {
            $query = self::where('student_id', $studentId)
                ->where('class_id', $classId)
                ->where('term', $term)
                ->whereIn('assessment_type', self::$continuousTypes);

            if ($subjectId) {
                $query->where('subject_id', $subjectId);
            }

            $grades = $query->get();
        }

        if ($grades->isEmpty()) {
            return null;
        }

        $method = setting('macs_calculation_method', 'launched_only');
        if ($method === 'fixed_count') {
            $fixedCount = max(1, (int) setting('default_acs_count', 3));

            return round($grades->sum('grade') / $fixedCount, 1);
        }

        return round($grades->avg('grade'), 1);
    }

    protected static function getMACSWeight(): int
    {
        return (int) setting('macs_weight', 2);
    }

    protected static function getACPWeight(): int
    {
        return (int) setting('acp_weight_in_mt', 1);
    }

    protected static function getMinTermsForFinalGrade(): int
    {
        return (int) setting('min_terms_for_final_grade', 3);
    }

    /**
     * MT - Média Trimestral (Moçambique)
     * MT = (MACS_Weight * MACS + ACP_Weight * ACP) / (MACS_Weight + ACP_Weight)
     */
    public static function calculateMT(int $studentId, ?int $subjectId, int $classId, int $term, $allGrades = null): ?float
    {
        $macs = self::calculateMACS($studentId, $subjectId, $classId, $term, $allGrades);

        if ($allGrades !== null) {
            $acpGrades = $allGrades->filter(function ($g) use ($studentId, $subjectId, $classId, $term) {
                return (int) $g->student_id === $studentId
                    && (int) $g->class_id === $classId
                    && (int) $g->term === $term
                    && (! $subjectId || (int) $g->subject_id === $subjectId)
                    && in_array($g->assessment_type, [self::TYPE_ACP, 'exam']);
            });
            $acp = $acpGrades->isNotEmpty() ? $acpGrades->avg('grade') : null;
        } else {
            $query = self::where('student_id', $studentId)
                ->where('class_id', $classId)
                ->where('term', $term)
                ->whereIn('assessment_type', [self::TYPE_ACP, 'exam']);

            if ($subjectId) {
                $query->where('subject_id', $subjectId);
            }

            $acp = $query->avg('grade');
        }

        if ($macs === null && $acp === null) {
            return null;
        }

        if ($macs !== null && $acp === null) {
            return $macs;
        }

        if ($macs === null && $acp !== null) {
            return round((float) $acp, 1);
        }

        $macsWeight = self::getMACSWeight();
        $acpWeight = self::getACPWeight();
        $totalWeight = $macsWeight + $acpWeight;

        return round(($macsWeight * $macs + $acpWeight * $acp) / $totalWeight, 1);
    }

    /**
     * MFD - Média de Frequência por Disciplina
     * MFD = (MT1 + MT2 + MT3) / 3
     *
     * Para passar: MFD >= passing_grade
     */
    public static function calculateMFD(int $studentId, ?int $subjectId, int $classId, int $year): ?float
    {
        $minTerms = self::getMinTermsForFinalGrade();
        $mts = [];

        for ($term = 1; $term <= 3; $term++) {
            $mts[$term] = self::calculateMT($studentId, $subjectId, $classId, $term);
        }

        $validMts = collect($mts)->filter(fn ($v) => $v !== null);

        if ($validMts->count() < $minTerms) {
            return null;
        }

        return round($validMts->avg(), 1);
    }

    /**
     * MFD com ponderação de Exame Final (ACF) se ativado nas configurações
     */
    public static function calculateMFDWithExam(int $studentId, ?int $subjectId, int $classId, int $year): ?float
    {
        $mfd = self::calculateMFD($studentId, $subjectId, $classId, $year);
        if ($mfd === null) {
            return null;
        }

        $includeAcf = setting('include_acf_in_mfd', '0') == '1';
        if (! $includeAcf) {
            return $mfd;
        }

        $classRoom = ClassRoom::find($classId);
        $examLevels = array_map('trim', explode(',', setting('exam_class_levels', '6,7,10,12')));

        if ($classRoom && ! in_array((string)$classRoom->grade_level, $examLevels)) {
            return $mfd;
        }

        $acfQuery = self::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->where('year', $year)
            ->where('assessment_type', self::TYPE_ACF);

        if ($subjectId) {
            $acfQuery->where('subject_id', $subjectId);
        }

        $acf = $acfQuery->avg('grade');

        if ($acf === null) {
            return $mfd;
        }

        $acfWeight = (int) setting('acf_weight_in_mfd', 1);
        $termsWeight = (int) setting('terms_weight_in_mfd', 3);
        $totalWeight = $acfWeight + $termsWeight;

        return round(($termsWeight * $mfd + $acfWeight * $acf) / $totalWeight, 1);
    }

    /**
     * MF - Média Final (ponderada com exame se aplicável)
     */
    public static function calculateMF(int $studentId, ?int $subjectId, int $classId, int $year): ?float
    {
        return self::calculateMFDWithExam($studentId, $subjectId, $classId, $year);
    }

    /**
     * Verificar se o aluno está aprovado numa disciplina
     */
    public static function isApproved(int $studentId, int $subjectId, int $classId, int $year): bool
    {
        $mf = self::calculateMF($studentId, $subjectId, $classId, $year);
        $passingGrade = (float) setting('passing_grade', 10);

        return $mf !== null && $mf >= $passingGrade;
    }

    /**
     * Obter relatório completo das médias do aluno
     */
    public static function getFullReport(int $studentId, int $classId, int $year): array
    {
        $subjects = Subject::active()->get();
        $report = [];

        foreach ($subjects as $subject) {
            $macs1 = self::calculateMACS($studentId, $subject->id, $classId, 1);
            $macs2 = self::calculateMACS($studentId, $subject->id, $classId, 2);
            $macs3 = self::calculateMACS($studentId, $subject->id, $classId, 3);

            $mt1 = self::calculateMT($studentId, $subject->id, $classId, 1);
            $mt2 = self::calculateMT($studentId, $subject->id, $classId, 2);
            $mt3 = self::calculateMT($studentId, $subject->id, $classId, 3);

            $mfd = self::calculateMFD($studentId, $subject->id, $classId, $year);

            $report[$subject->id] = [
                'subject' => $subject->name,
                'macs' => [$macs1, $macs2, $macs3],
                'mt' => [$mt1, $mt2, $mt3],
                'mfd' => $mfd,
                'approved' => $mfd !== null && $mfd >= 10,
            ];
        }

        return $report;
    }
}
