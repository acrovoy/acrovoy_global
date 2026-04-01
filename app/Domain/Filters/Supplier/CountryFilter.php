<?php

namespace App\Domain\Filters\Supplier;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class CountryFilter implements FilterInterface
{
    /**
     * Применяет фильтр по странам к запросу
     *
     * @param Builder $query
     * @param mixed   $value - id стран или массив id
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        $countries = (array) $value;

        if (empty($countries)) {
            return $query;
        }

        return $query->whereIn('country_id', $countries);
    }
}