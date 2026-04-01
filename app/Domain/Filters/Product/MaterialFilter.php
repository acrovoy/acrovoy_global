<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class MaterialFilter implements FilterInterface
{
    /**
     * Применяет фильтр по материалам
     *
     * @param Builder $query
     * @param mixed $value - массив slug выбранных материалов
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if (!empty($value) && is_array($value)) {
            $query->whereHas('materials', function (Builder $q) use ($value) {
                $q->whereIn('slug', $value);
            });
        }

        return $query;
    }
}