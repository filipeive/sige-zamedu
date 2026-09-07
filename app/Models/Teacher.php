<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\BelongsToSchool;

class Teacher extends Model
{
    use HasFactory, LogsActivity, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'hire_date',
        'qualification',
        'specialization',
        'bi_number',
        'birth_date',
        'gender',
        'address',
        'salary',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'phone', 'status'])
            ->logOnlyDirty();
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassRoom::class, 'teacher_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    /**
     * Todas as turmas onde o professor lecciona:
     *  - como regente (classes.teacher_id)
     *  - como professor de uma disciplina (class_subjects.teacher_id)
     */
    public function allTeachingClasses()
    {
        $regentIds = $this->classes()->pluck('classes.id');
        $subjectIds = $this->classSubjects()->pluck('class_id');
        return ClassRoom::whereIn('id', $regentIds->merge($subjectIds)->unique());
    }

    /**
     * IDs de todas as turmas onde o professor lecciona (regente + disciplinas).
     */
    public function allTeachingClassIds(): array
    {
        return $this->allTeachingClasses()->pluck('id')->toArray();
    }

    /**
     * Disciplinas que o professor lecciona nesta turma específica.
     *  - Se for regente no primário (single_teacher) → todas as disciplinas da turma
     *  - Se for secundário (subject_based) → só as disciplinas atribuídas via class_subjects
     */
    public function taughtSubjectsInClass(ClassRoom $class)
    {
        if ($class->teacher_id === $this->id) {
            return $class->subjects()->get();
        }
        $subjectIds = $this->classSubjects()
            ->where('class_id', $class->id)
            ->pluck('subject_id');
        return \App\Models\Subject::whereIn('id', $subjectIds)->get();
    }

    /**
     * Verifica se o professor tem acesso à turma (regente OU disciplina atribuída).
     */
    public function canAccessClass(int $classId): bool
    {
        if ($this->classes()->where('classes.id', $classId)->exists()) {
            return true;
        }
        return $this->classSubjects()->where('class_id', $classId)->exists();
    }

    /**
     * Verifica se o professor lecciona uma (turma, disciplina) específicas.
     * Regente tem acesso a todas as disciplinas da turma.
     */
    public function canAccessClassSubject(int $classId, int $subjectId): bool
    {
        if ($this->classes()->where('classes.id', $classId)->exists()) {
            return true;
        }
        return $this->classSubjects()
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(StaffLeaveRequest::class, 'staff_id');
    }

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getYearsExperienceAttribute()
    {
        return $this->hire_date ? (int) abs(now()->diffInYears($this->hire_date)) : 0;
    }

    public function todaysSchedules()
    {
        return $this->schedules()
            ->where('weekday', now()->dayOfWeek)
            ->where('status', 'active')
            ->with(['class', 'subject'])
            ->orderBy('start_time')
            ->get();
    }
    // Adicionar este método ao modelo Teacher
    public function getTotalStudents()
    {
        return $this->classes()
            ->active()
            ->currentYear()
            ->withCount('students')
            ->get()
            ->sum('students_count');
    }

    public function getPendingAssessmentsCount()
    {
        return $this->classes()->active()->currentYear()->count() * 2;
    }
}
