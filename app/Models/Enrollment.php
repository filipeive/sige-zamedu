<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class Enrollment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'school_year',
        'status',
        'enrollment_date',
        'cancellation_date',
        'monthly_fee',
        'payment_day',
        'observations'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'cancellation_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'school_year' => 'integer',
    ];

    // Definir os valores possíveis para status
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_CANCELLED = 'cancelled';

    // Relações
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Escopos
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('school_year', $year);
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('school_year', current_school_year());
    }

    // Métodos de verificação de status
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // Gera número de matrícula automático
    public static function generateEnrollmentNumber()
    {
        $year = current_school_year();
        $lastEnrollment = self::where('school_year', $year)->latest()->first();

        if ($lastEnrollment) {
            $lastNumber = intval(substr($lastEnrollment->enrollment_number, -4));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "MAT{$year}{$nextNumber}";
    }
}