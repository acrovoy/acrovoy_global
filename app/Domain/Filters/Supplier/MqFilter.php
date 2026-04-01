<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class MOQFilter implements FilterInterface
{
    /**
     * Применяет фильтр по минимальному объему заказа (MOQ)
     *
     * @param Builder $query
     * @param mixed   $value - минимальный MOQ
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        $minMoq = (int) $value;

        if ($minMoq <= 0) {
            return $query;
        }

        return $query->where('moq', '<=', $minMoq);
    }
}