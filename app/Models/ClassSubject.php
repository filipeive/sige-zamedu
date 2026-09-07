<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\BelongsToSchool;

class ClassSubject extends Model
{
    use HasFactory, LogsActivity, BelongsToSchool;

    protected $table = 'class_subjects';

    protected $fillable = [
        'school_id',
        'class_id',
        'subject_id',
        'teacher_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['class_id', 'subject_id', 'teacher_id'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Disciplina da turma {$eventName}");
    }

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

    // Accessors
    public function getSubjectNameAttribute()
    {
        return $this->subject ? $this->subject->name : 'N/A';
    }

    public function getTeacherNameAttribute()
    {
        return $this->teacher ? $this->teacher->full_name : 'Não atribuído';
    }

    public function getClassNameAttribute()
    {
        return $this->class ? $this->class->name : 'N/A';
    }
}