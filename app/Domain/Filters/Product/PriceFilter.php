<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class PriceFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        if (!empty($value['min'])) {
            $query->where('price', '>=', $value['min']);
        }
        if (!empty($value['max'])) {
            $query->where('price', '<=', $value['max']);
        }

        return $query;
    }
}