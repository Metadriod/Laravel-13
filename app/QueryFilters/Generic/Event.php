<?php

namespace App\QueryFilters\Generic;

use App\QueryFilters\Filter;
use Illuminate\Database\Eloquent\Builder;

class Event extends Filter
{
    private const FILTER_NAME = 'event';

    /** {@inheritDoc} */
    protected function getFilterName(): string
    {
        return static::FILTER_NAME;
    }

    /** {@inheritDoc} */
    protected function applyFilter(Builder $builder): Builder
    {
        $filterName = $this->getFilterName();

        return $builder->where('event', request($filterName));
    }
}
