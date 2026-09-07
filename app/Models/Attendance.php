<?php

// app/Models/Attendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class Attendance extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'attendance_date',
        'status',
        'notes',
        'marked_by',
        'arrival_time',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'arrival_time' => 'datetime',
    ];

    // Relacionamentos
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Scopes
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('attendance_date', now()->month)
                    ->whereYear('attendance_date', now()->year);
    }
}