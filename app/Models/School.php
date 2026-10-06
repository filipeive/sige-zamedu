<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id',
        'district_id',
        'zip_id',
        'name',
        'code',
        'type',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'principal_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function setTypeAttribute($value): void
    {
        $this->attributes['type'] = match (strtolower($value ?? '')) {
            'pública', 'publica', 'public' => 'public',
            'privada', 'private' => 'private',
            'comunitária', 'comunitaria', 'community' => 'community',
            default => 'public',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match (strtolower($this->type ?? '')) {
            'public', 'pública', 'publica' => 'Pública',
            'private', 'privada' => 'Privada',
            'community', 'comunitária', 'comunitaria' => 'Comunitária',
            default => $this->type ?? 'Pública',
        };
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function zip(): BelongsTo
    {
        return $this->belongsTo(Zip::class);
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classRooms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function classes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function teachers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function licenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(License::class);
    }

    public function latestLicense(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(License::class)->latestOfMany();
    }
}
