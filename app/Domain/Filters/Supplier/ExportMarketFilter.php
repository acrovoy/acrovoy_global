<?php

namespace App\Domain\Filters\Supplier;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;

class ExportMarketFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $value): Builder
    {
        $markets = (array) $value;

        if (empty($markets)) {
            return $query;
        }

        return $query->whereHas('exportMarkets', function ($q) use ($markets) {
            $q->whereIn('export_markets.id', $markets);
        });
    }
}