<?php

namespace App\Domain\Filters\Product;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class SupplierTypeFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        $types = (array) $value;

        if (empty($types)) {
            return $query;
        }

        return $query->whereHas('supplier', function (Builder $q) use ($types) {
            $q->where(function (Builder $q2) use ($types) {

                foreach ($types as $type) {
                    switch ($type) {
                        case 'trusted':
                            $q2->orWhere('is_trusted', 1);
                            break;

                        case 'verified':
                            $q2->orWhere('is_verified', 1);
                            break;

                        case 'premium':
                            $q2->orWhere('is_premium', 1);
                            break;

                        case 'gold':
                            // GOLD + PLATINUM через числовой диапазон репутации
                            $q2->orWhere(function (Builder $q3) {
                                $q3->whereBetween('reputation', [121, 200])   // Gold
                                   ->orWhere('reputation', '>', 200);       // Platinum
                            });
                            break;
                    }
                }
            });

            // Если нужны другие типы (trusted/premium/verified), проверяем связь supplierTypes
            $otherTypes = array_diff($types, ['gold']);
            if (!empty($otherTypes)) {
                $q->orWhereHas('supplierTypes', function (Builder $q4) use ($otherTypes) {
                    $q4->whereIn('slug', $otherTypes);
                });
            }
        });
    }
}