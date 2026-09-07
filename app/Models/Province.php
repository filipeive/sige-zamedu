<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'capital',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function zips(): HasManyThrough
    {
        return $this->hasManyThrough(Zip::class, District::class);
    }

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}
