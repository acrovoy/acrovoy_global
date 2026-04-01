<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class MOQFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        if (!empty($value)) {
            $query->where('moq', '>=', $value);
        }

        return $query;
    }
}