<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class Assessment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'type',
        'term', // NOVO
        'max_grade',
        'due_date',
        'is_published'
    ];

    const TYPE_TEST = 'test';
    const TYPE_ASSIGNMENT = 'assignment';
    const TYPE_PROJECT = 'project';
    const TYPE_EXAM = 'exam';
    const TYPE_PARTICIPATION = 'participation';
    const TYPE_CONTINUOUS = 'continuous';
    const TYPE_ACS1 = 'ACS1';
    const TYPE_ACS2 = 'ACS2';
    const TYPE_ACS3 = 'ACS3';
    const TYPE_ACP = 'ACP';
    const TYPE_ACF = 'ACF';
    const TYPE_BEHAVIORAL = 'behavioral';

    protected $casts = [
        'due_date' => 'date',
        'max_grade' => 'decimal:2',
        'is_published' => 'boolean'
    ];

    // Relacionamentos
    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // Scopes
    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays($days))
            ->where('is_published', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('is_published', true);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // Métodos
    public function getGradedStudentsCount()
    {
        return $this->grades()->count();
    }

    public function getTotalStudentsCount()
    {
        return $this->class->students()->count();
    }

    public function getCompletionPercentage()
    {
        $total = $this->getTotalStudentsCount();
        if ($total == 0)
            return 0;

        return round(($this->getGradedStudentsCount() / $total) * 100, 1);
    }

    public function getStatusAttribute()
    {
        if ($this->due_date < now()) {
            return 'overdue';
        } elseif ($this->due_date <= now()->addDays(2)) {
            return 'urgent';
        } else {
            return 'upcoming';
        }
    }

    public function getStatusColorAttribute()
    {
        return [
            'overdue' => 'danger',
            'urgent' => 'warning',
            'upcoming' => 'info'
        ][$this->status];
    }
}