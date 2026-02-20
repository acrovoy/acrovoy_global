<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class SupplierFilter
{
    public function apply(Builder $query, Request $request): Builder
    {
        return $query
            // Фильтр по категории
            ->when($request->category, function($q) use ($request) {
                    $q->whereHas('products.category', function($cat) use ($request) {
                        $cat->where('slug', $request->category);
                    });
                })

            // Фильтр по странам
            ->when($request->country, fn($q) =>
                $q->whereIn('country_id', (array) $request->country)
            )

            // Фильтр по типу поставщика
            ->when($request->supplier_type, function($q) use ($request) {
                $types = (array) $request->supplier_type;

                $q->where(function($q2) use ($types) {
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
                                $q2->orWhere('reputation', '>', 121);
                                break;
                        }
                    }
                });
            });
    }
}