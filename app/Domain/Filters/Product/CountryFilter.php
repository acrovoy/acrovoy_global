<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class CountryFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        // $value — массив id стран, например [1, 2, 3]
        if (!empty($value) && is_array($value)) {
            $query->whereIn('country_id', $value);
        }

        return $query;
    }
}