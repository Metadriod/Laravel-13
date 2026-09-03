<?php

namespace App\QueryFilters\AuditLog;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class AuditableType extends Filter
{
    private const FILTER_NAME = 'auditable_type';

    /** {@inheritDoc} */
    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }

    /** {@inheritDoc} */
    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();

        return $builder->where('auditable_type', request($filterName));
    }
}
