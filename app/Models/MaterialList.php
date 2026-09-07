<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class MaterialList extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'grade_level',
        'items',
        'academic_year',
        'notes',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
