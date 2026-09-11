<?php
namespace App\Models;

use App\Helpers\EducationLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\BelongsToSchool;

class ClassRoom extends Model
{
    use HasFactory, LogsActivity, BelongsToSchool;

    protected $table = 'classes';

    const SHIFT_LABELS = [
        'morning' => 'Manhã',
        'afternoon' => 'Tarde',
        'night' => 'Noite',
    ];

    protected $fillable = [
        'school_id',
        'name',
        'grade_level',
        'shift',
        'teaching_mode',
        'teacher_id',
        'assistant_teacher_id',
        'max_students',
        'current_students',
        'classroom',
        'school_year',
        'is_active',
    ];

    /**
     * Modos de ensino suportados.
     * - single_teacher   → 1 professor regente (+ opcional assistente) — pré-escolar/primário
     * - subject_based    → professor atribuído por disciplina — secundário
     */
    public const TEACHING_MODES = [
        'single_teacher' => 'Professor Regente (+ Assistente opcional)',
        'subject_based'  => 'Por Disciplina',
    ];

    /**
     * Resolver modo de ensino com base no nível (caso não esteja gravado).
     */
    public function resolveTeachingMode(): string
    {
        if (! empty($this->teaching_mode)) {
            return $this->teaching_mode;
        }
        // Auto-detect: pré-escolar e primário são single_teacher; secundário é subject_based
        return $this->isSecondary() ? 'subject_based' : 'single_teacher';
    }

    public function getTeachingModeAttribute($value): string
    {
        return $value ?: $this->resolveTeachingMode();
    }

    public function getTeachingModeLabelAttribute(): string
    {
        return self::TEACHING_MODES[$this->teaching_mode] ?? ucfirst(str_replace('_', ' ', $this->teaching_mode));
    }

    public function isSingleTeacher(): bool
    {
        return $this->teaching_mode === 'single_teacher';
    }

    public function isSubjectBased(): bool
    {
        return $this->teaching_mode === 'subject_based';
    }

    public function assistantTeacher()
    {
        return $this->belongsTo(Teacher::class, 'assistant_teacher_id');
    }

    public function getShiftLabelAttribute(): string
    {
        return match($this->shift) {
            'afternoon' => 'Tarde',
            'night' => 'Noite',
            default => 'Manhã',
        };
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'grade_level', 'teacher_id', 'is_active', 'school_year'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Turma {$eventName}");
    }

    // Relacionamentos
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'class_id', 'student_id')
            ->wherePivot('status', 'active')
            ->withPivot('enrollment_date', 'monthly_fee')
            ->withTimestamps();
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('school_year', current_school_year());
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade_level', $grade);
    }

    /**
     * Filtrar por nível de ensino conforme taxonomia SNE/MINEDH (Lei 18/2018).
     * Chaves: preschool | primary | secondary | ep1 | ep2 | esg1 | esg2 | basic | sne
     */
    public function scopeByEducationLevel($query, ?string $level)
    {
        if (empty($level) || $level === 'all') {
            return $query;
        }
        $grades = EducationLevel::gradesForLevel($level);
        return $query->whereIn('grade_level', $grades);
    }

    public function scopeWithTeacher($query)
    {
        return $query->with([
            'teacher' => function ($q) {
                $q->select('id', 'first_name', 'last_name', 'email');
            }
        ]);
    }

    // ─── Accessors SNE/MINEDH (Lei 18/2018) ───────────────────────────────

    /** Rótulo direto do grade_level (ex: "3ª Classe") */
    public function getGradeLevelNameAttribute(): string
    {
        return EducationLevel::label((int) $this->grade_level);
    }

    /** Chave textual do nível: preschool | primary | secondary */
    public function getEducationLevelAttribute(): string
    {
        return EducationLevel::levelKey((int) $this->grade_level);
    }

    /** Nome por extenso do nível de ensino (ex: "Ensino Primário") */
    public function getEducationLevelNameAttribute(): string
    {
        return EducationLevel::levelName((int) $this->grade_level);
    }

    /** Nome do ciclo MINEDH (ex: "1.º Ciclo do Ensino Primário") */
    public function getCycleNameAttribute(): string
    {
        return EducationLevel::cycleName((int) $this->grade_level);
    }

    /** Abreviatura do ciclo para badges (ex: "EP1", "ESG2") */
    public function getCycleAbbrAttribute(): string
    {
        return EducationLevel::cycleAbbr((int) $this->grade_level);
    }

    /** Cor Tailwind do nível (pink / blue / purple) */
    public function getEducationLevelColorAttribute(): string
    {
        return EducationLevel::tailwindColor((int) $this->grade_level);
    }

    // ─── Predicados ────────────────────────────────────────────────────────

    /**
     * Pré-Escolar — fora do SNE Geral.
     * Visível apenas em escolas privadas/comunitárias (cobram propinas).
     */
    public function isPreschool(): bool
    {
        return EducationLevel::isPreschool((int) $this->grade_level);
    }

    public function isPrimary(): bool
    {
        return EducationLevel::isPrimary((int) $this->grade_level);
    }

    public function isSecondary(): bool
    {
        return EducationLevel::isSecondary((int) $this->grade_level);
    }

    /** Educação Básica = escolaridade obrigatória (1ª–9ª Classe, Lei 18/2018) */
    public function isBasicEducation(): bool
    {
        return EducationLevel::isBasicEducation((int) $this->grade_level);
    }

    /** Esta turma realiza Exame Nacional MINEDH (9ª, 10ª ou 12ª Classe) */
    public function isNationalExamGrade(): bool
    {
        return EducationLevel::isNationalExamGrade((int) $this->grade_level);
    }

    /** Pertence ao SNE Geral (exclui Pré-Escolar) */
    public function isSneGeral(): bool
    {
        return EducationLevel::isSneGeral((int) $this->grade_level);
    }

    public function getCapacityPercentageAttribute()
    {
        if ($this->max_students == 0)
            return 0;
        return round(($this->current_students / $this->max_students) * 100, 1);
    }

    public function getStudentsCountAttribute()
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    public function getAvailableSlotsAttribute()
    {
        return max(0, $this->max_students - $this->current_students);
    }

    public function getIsFullAttribute()
    {
        return $this->current_students >= $this->max_students;
    }

    // Métodos
    public function updateStudentsCount()
    {
        $this->update([
            'current_students' => $this->enrollments()->where('status', 'active')->count()
        ]);
    }

    public function canAcceptMoreStudents()
    {
        return $this->current_students < $this->max_students;
    }

    protected static function boot()
    {
        parent::boot();

        // NOTE: Do NOT auto-update student count here as it causes infinite loop
        // The count should be updated explicitly when enrollments change
        // See Enrollment model observers for proper handling
    }
    public function weeklySchedule()
    {
        return $this->schedules()
            ->where('status', 'active')
            ->with(['subject', 'teacher'])
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->get()
            ->groupBy('weekday');
    }

}