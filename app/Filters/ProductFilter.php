<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductFilter
{
    public function apply(Builder $query, Request $request): Builder
    {
        /*
        |--------------------------------------------------------------------------
        | Category Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category')) {

            $query->whereHas('category', function ($c) use ($request) {
                $c->where('slug', $request->category);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Material Filter (Multi select)
        |--------------------------------------------------------------------------
        */

        if ($request->filled('material')) {

            $materials = (array) $request->material;

            $query->whereHas('materials', function ($m) use ($materials) {
                $m->whereIn('slug', $materials);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Price Tier Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('min_price')) {

            $query->whereHas('priceTiers', function ($p) use ($request) {
                $p->where('price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {

            $query->whereHas('priceTiers', function ($p) use ($request) {
                $p->where('price', '<=', $request->max_price);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | MOQ Filter
        |--------------------------------------------------------------------------
        */

       if ($request->filled('min_moq')) {
    $query->where('moq', '<=', (int) $request->min_moq);
}

        /*
        |--------------------------------------------------------------------------
        | Lead Time Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('min_lead_time')) {
            $query->where('lead_time', '>=', $request->min_lead_time);
        }

        if ($request->filled('max_lead_time')) {
            $query->where('lead_time', '<=', $request->max_lead_time);
        }

        /*
        |--------------------------------------------------------------------------
        | Country Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('country')) {

            $countries = (array) $request->country;

            $query->whereIn('country_id', $countries);
        }

        return $query;
    }
}