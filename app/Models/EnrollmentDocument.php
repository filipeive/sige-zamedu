<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class EnrollmentDocument extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'application_id',
        'document_type',
        'file_path',
        'is_verified',
    ];

    public function application()
    {
        return $this->belongsTo(EnrollmentApplication::class, 'application_id');
    }
}
