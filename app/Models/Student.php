<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\BelongsToSchool;

/**
 * @property \Carbon\Carbon|null $birthdate
 */
class Student extends Model
{
    use HasFactory, LogsActivity, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_number',
        'first_name',
        'last_name',
        'gender',
        'birthdate',
        'birth_place',
        'registration_date',
        'monthly_fee',
        'parent_id',
        'address',
        'school_exit',
        'exit_year',
        'previous_class',
        'previous_grade',
        'emergency_contact',
        'emergency_phone',
        'medical_certificate',
        'passport_photo',
        'observations',
        'has_special_needs',
        'special_needs_description',
        'status',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'registration_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'has_special_needs' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'status', 'monthly_fee'])
            ->logOnlyDirty();
    }

    // Relacionamentos
    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id', 'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function currentEnrollment()
    {
        $currentYear = function_exists('current_school_year') ? current_school_year() : now()->year;

        return $this->hasOne(Enrollment::class)
            ->whereIn('status', ['active', 'completed'])
            ->where('school_year', $currentYear);
    }

    // current class
    public function currentClass()
    {
        return optional($this->currentEnrollment)->class;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }

    public function studentRecords()
    {
        return $this->hasMany(StudentRecord::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'pending_renewal']);
    }

    public function scopePendingRenewal($query)
    {
        return $query->where('status', 'pending_renewal');
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->whereHas('currentEnrollment.class', function ($q) use ($grade) {
            $q->where('grade_level', $grade);
        });
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getAgeAttribute()
    {
        return $this->birthdate ? intval(\Carbon\Carbon::parse($this->birthdate)->diffInYears(now())) : null;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->passport_photo) {
            return asset('storage/'.$this->passport_photo);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->full_name).'&background=2E7D32&color=fff';
    }

    public function getCurrentClassAttribute()
    {
        return $this->currentEnrollment?->class;
    }
}
