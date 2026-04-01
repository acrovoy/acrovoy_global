<?php

namespace App\Domain\Filters\Supplier;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class SupplierTypeFilter implements FilterInterface
{
    /**
     * Применяет фильтр по типу поставщика
     *
     * @param Builder $query
     * @param mixed   $value - массив выбранных типов ['trusted', 'premium']
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        $types = (array) $value;

        if (empty($types)) {
            return $query;
        }

        return $query->where(function ($q) use ($types) {
            foreach ($types as $type) {
                switch ($type) {
                    case 'trusted':
                        $q->orWhere('is_trusted', 1);
                        break;
                    case 'verified':
                        $q->orWhere('is_verified', 1);
                        break;
                    case 'premium':
                        $q->orWhere('is_premium', 1);
                        break;
                    case 'gold':
                        $q->orWhere('reputation', '>', 121);
                        break;
                }
            }
        });
    }
}