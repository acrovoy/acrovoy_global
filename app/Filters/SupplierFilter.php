<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class SupplierFilter
{
    public function apply(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->category, fn($q) =>
                $q->whereHas('products.category', fn($c) =>
                    $c->where('slug', $request->category)
                )
            )
            ->when($request->material, fn($q) =>
                $q->whereHas('products.materials', fn($m) =>
                    $m->whereIn('slug', (array) $request->material)
                )
            )
            ->when($request->min_price, fn($q) =>
                $q->whereHas('products.priceTiers', fn($p) =>
                    $p->where('price', '>=', $request->min_price)
                )
            )
            ->when($request->max_price, fn($q) =>
                $q->whereHas('products.priceTiers', fn($p) =>
                    $p->where('price', '<=', $request->max_price)
                )
            )
            ->when($request->min_moq, fn($q) =>
                $q->whereHas('products', fn($p) =>
                    $p->where('moq', '>=', $request->min_moq)
                )
            )
            ->when($request->max_moq, fn($q) =>
                $q->whereHas('products', fn($p) =>
                    $p->where('moq', '<=', $request->max_moq)
                )
            )
            ->when($request->sold_from, fn($q) =>
                $q->having('sold_count', '>=', $request->sold_from)
            );
    }
}
