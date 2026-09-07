<?php

namespace App\Traits;

use App\Models\School;
use App\Scopes\SchoolScope;
use App\Services\SchoolContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToSchool
{
    /**
     * Boot the BelongsToSchool trait for an Eloquent model.
     *
     * @return void
     */
    public static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(new SchoolScope());

        static::creating(function ($model) {
            if (is_null($model->school_id) && SchoolContext::hasSchoolContext()) {
                $model->school_id = SchoolContext::getSchoolId();
            }
        });
    }

    /**
     * Get the school that owns the model.
     *
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Scope a query to only include records for a specific school, bypassing the active context scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|null $schoolId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSchool($query, ?int $schoolId)
    {
        return $query->withoutGlobalScope(SchoolScope::class)
            ->where($this->getTable() . '.school_id', $schoolId);
    }

    /**
     * Scope a query to ignore the active school global scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutSchoolScope($query)
    {
        return $query->withoutGlobalScope(SchoolScope::class);
    }
}
