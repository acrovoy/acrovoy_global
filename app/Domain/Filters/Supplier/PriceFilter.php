<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class PriceFilter implements FilterInterface
{
    /**
     * Применяет фильтр по цене
     *
     * @param Builder $query
     * @param mixed   $value - массив с ключами 'min' и/или 'max'
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        $min = $value['min'] ?? null;
        $max = $value['max'] ?? null;

        return $query->when($min !== null, function ($q) use ($min) {
            $q->whereHas('priceTiers', function ($p) use ($min) {
                $p->where('price', '>=', $min);
            });
        })->when($max !== null, function ($q) use ($max) {
            $q->whereHas('priceTiers', function ($p) use ($max) {
                $p->where('price', '<=', $max);
            });
        });
    }
}