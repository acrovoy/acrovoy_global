<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductFilter
{
    /**
     * Применяем фильтры к query.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function apply(Builder $query, Request $request): Builder
    {
        return $query
            // Фильтр по категории (slug)
            ->when($request->category, fn($q) =>
                $q->whereHas('category', fn($c) =>
                    $c->where('slug', $request->category)
                )
            )

            // Фильтр по материалам (multi select)
            ->when($request->material, fn($q) =>
                $q->whereHas('materials', fn($m) =>
                    $m->whereIn('slug', (array) $request->material)
                )
            )

            // Фильтр по минимальной цене
            ->when($request->min_price, fn($q) =>
                $q->whereHas('priceTiers', fn($p) =>
                    $p->where('price', '>=', $request->min_price)
                )
            )

            // Фильтр по максимальной цене
            ->when($request->max_price, fn($q) =>
                $q->whereHas('priceTiers', fn($p) =>
                    $p->where('price', '<=', $request->max_price)
                )
            )

            // MOQ
            ->when($request->min_moq, fn($q) =>
                $q->where('moq', '>=', $request->min_moq)
            )
            ->when($request->max_moq, fn($q) =>
                $q->where('moq', '<=', $request->max_moq)
            )

             // Sold count (агрегат)
            ->when($request->sold_from, fn($q) =>
                $q->having('sold_count', '>=', $request->sold_from)
            )


            // Lead time
            ->when($request->min_lead_time, fn($q) => $q->where('lead_time', '>=', $request->min_lead_time))
            ->when($request->max_lead_time, fn($q) => $q->where('lead_time', '<=', $request->max_lead_time))

            // Country filter
            ->when($request->country, fn($q) =>
                $q->whereIn('country_id', (array) $request->country)
            )


            // Можно добавить другие фильтры, например sold_count, country_id и т.д.
            ;
    }
}
