<?php

namespace App\Domain\Filters\Supplier;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class CategoryFilter implements FilterInterface
{
    /**
     * Применяет фильтр по категории товаров поставщика
     *
     * @param Builder $query
     * @param mixed   $value - slug категории
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        return $query->whereHas('products.category', function($q) use ($value) {
            $q->where('slug', $value);
        });
    }
}