<?php
namespace App\Models;

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
     * Filtrar por nível de ensino (preschool | primary | secondary).
     */
    public function scopeByEducationLevel($query, ?string $level)
    {
        if (empty($level) || $level === 'all') {
            return $query;
        }
        return match ($level) {
            'preschool' => $query->whereIn('grade_level', [0, 1]),
            'primary'   => $query->whereIn('grade_level', [2, 3, 4, 5, 6, 7]),
            'secondary' => $query->whereIn('grade_level', [8, 9, 10, 11, 12, 13]),
            default     => $query,
        };
    }

    public function scopeWithTeacher($query)
    {
        return $query->with([
            'teacher' => function ($q) {
                $q->select('id', 'first_name', 'last_name', 'email');
            }
        ]);
    }

    // Accessors
    public function getGradeLevelNameAttribute()
    {
        $grades = [
            0 => 'Pré-Infantil',
            1 => 'Pré-Escolar / Infantil',
            2 => '1ª Classe',
            3 => '2ª Classe',
            4 => '3ª Classe',
            5 => '4ª Classe',
            6 => '5ª Classe',
            7 => '6ª Classe',
            8 => '7ª Classe',
            9 => '8ª Classe',
            10 => '9ª Classe',
            11 => '10ª Classe',
            12 => '11ª Classe',
            13 => '12ª Classe',
        ];

        return $grades[$this->grade_level] ?? (is_numeric($this->grade_level) ? $this->grade_level . 'ª Classe' : $this->grade_level);
    }

    public function getEducationLevelAttribute(): string
    {
        $num = (int) $this->grade_level;
        if ($num === 0 || $num === 1 || str_contains(strtolower((string)$this->grade_level), 'pré') || str_contains(strtolower((string)$this->grade_level), 'infantil')) {
            return 'preschool';
        }
        if ($num >= 2 && $num <= 7) {
            return 'primary';
        }
        return 'secondary';
    }

    public function getEducationLevelNameAttribute(): string
    {
        return match ($this->education_level) {
            'preschool' => 'Pré-Escolar & Infantil',
            'primary' => 'Ensino Primário',
            'secondary' => 'Ensino Secundário',
            default => 'Ensino Geral'
        };
    }

    public function isPreschool(): bool
    {
        return $this->education_level === 'preschool';
    }

    public function isPrimary(): bool
    {
        return $this->education_level === 'primary';
    }

    public function isSecondary(): bool
    {
        return $this->education_level === 'secondary';
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