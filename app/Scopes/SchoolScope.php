<?php

namespace App\Scopes;

use App\Services\SchoolContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (SchoolContext::hasSchoolContext()) {
            $builder->where($model->getTable() . '.school_id', SchoolContext::getSchoolId());
        }
    }
}
