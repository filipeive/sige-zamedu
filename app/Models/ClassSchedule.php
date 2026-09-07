<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class ClassSchedule extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'weekday',
        'start_time',
        'end_time',
        'classroom',
        'academic_year',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'weekday' => 'integer'
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'schedule_id');
    }

    // Escopos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('academic_year', now()->year);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForWeekday($query, $weekday)
    {
        return $query->where('weekday', $weekday);
    }

    public function scopeToday($query)
    {
        return $query->where('weekday', now()->dayOfWeek);
    }

    // Acessores
    public function getWeekdayNameAttribute()
    {
        $weekdays = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            0 => 'Domingo'
        ];

        return $weekdays[$this->weekday] ?? 'Desconhecido';
    }

    public function getTimeRangeAttribute()
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getDurationAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    // Métodos
    public function isHappeningNow()
    {
        $now = now();
        $currentTime = $now->format('H:i');
        $currentWeekday = $now->dayOfWeek;

        return $this->weekday == $currentWeekday && 
               $currentTime >= $this->start_time->format('H:i') && 
               $currentTime <= $this->end_time->format('H:i');
    }

    public function getNextClass()
    {
        $now = now();
        $currentTime = $now->format('H:i');
        $currentWeekday = $now->dayOfWeek;

        return self::where('teacher_id', $this->teacher_id)
            ->where(function($query) use ($currentWeekday, $currentTime) {
                $query->where('weekday', '>', $currentWeekday)
                      ->orWhere(function($q) use ($currentWeekday, $currentTime) {
                          $q->where('weekday', $currentWeekday)
                            ->where('start_time', '>', $currentTime);
                      });
            })
            ->orderBy('weekday')
            ->orderBy('start_time')
            ->first();
    }
}