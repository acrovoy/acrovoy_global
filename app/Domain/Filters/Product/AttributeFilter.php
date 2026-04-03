<?php

namespace App\Domain\Filters\Product;

use App\Domain\Filters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

class AttributeFilter implements FilterInterface
{
    public function apply(Builder $query, mixed $filters): Builder
    {
        foreach ($filters as $attributeCode => $selected) {
            if (empty($selected)) continue;

            $query->whereHas('attributeValues', function(Builder $q) use ($attributeCode, $selected) {
                $q->whereHas('attribute', function(Builder $qa) use ($attributeCode) {
                    $qa->where('code', $attributeCode)->where('is_filterable', 1);
                });

                // приводим к массиву
                $selectedValues = is_array($selected) ? $selected : [$selected];

                // фильтруем по опциям
                $q->whereHas('options', function(Builder $qo) use ($selectedValues) {
                    $qo->whereIn('attribute_option_id', $selectedValues);
                })
                // фильтруем текст/число
                ->orWhereHas('translations', function(Builder $qt) use ($selectedValues) {
                    $qt->whereIn('value', $selectedValues);
                });
            });
        }

        return $query;
    }
}