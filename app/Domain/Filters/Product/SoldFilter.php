<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class SoldFilter implements FilterInterface
{
    /**
     * Применяет фильтр по количеству проданных товаров
     *
     * @param Builder $query
     * @param mixed $value - минимальное количество проданных товаров
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if (!empty($value)) {
            $query->where('sold', '>=', (int)$value);
        }

        return $query;
    }
}