<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ParentModel extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'parents';
    protected $primaryKey = 'user_id';
    public $incrementing = false; 

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'mother_name',
        'mother_phone', 
        'mother_email',
        'relationship',
        'profession',
        'workplace',
        'emergency_contact',
        'emergency_phone',
        'bi_number',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function scopeForCurrentSchool($query)
    {
        if (\App\Services\SchoolContext::hasSchoolContext()) {
            $schoolId = \App\Services\SchoolContext::getSchoolId();
            return $query->where(function ($q) use ($schoolId) {
                $q->whereHas('students', function ($sq) use ($schoolId) {
                    $sq->where('school_id', $schoolId);
                })->orWhereHas('user', function ($uq) use ($schoolId) {
                    $uq->where('school_id', $schoolId);
                });
            });
        }

        return $query;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'phone', 'email', 'address'])
            ->logOnlyDirty();
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}

