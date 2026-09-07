<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class EnrollmentApplication extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'type',
        'status',
        'student_id',
        'parent_data',
        'student_data',
        'academic_year',
        'total_amount',
        'payment_status',
        'payment_reference',
        'payment_date',
        'payment_proof_path',
        'admin_notes',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'parent_data' => 'array',
        'student_data' => 'array',
        'payment_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function documents()
    {
        return $this->hasMany(EnrollmentDocument::class, 'application_id');
    }
}
