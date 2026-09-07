<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zip extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
        'name',
        'code',
        'headquarters',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function province(): BelongsTo
    {
        return $this->district->province();
    }

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}
