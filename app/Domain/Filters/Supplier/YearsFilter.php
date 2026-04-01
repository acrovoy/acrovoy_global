<?php

namespace App\Domain\Filters\Supplier;

use Illuminate\Database\Eloquent\Builder;
use App\Domain\Filters\Contracts\FilterInterface;
use Carbon\Carbon;

class YearsFilter implements FilterInterface
{
    /**
     * Фильтр по количеству лет на платформе
     *
     * пример:
     * ?years=5
     * означает: зарегистрирован ≥ 5 лет назад
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        $years = (int) $value;

        if ($years <= 0) {
            return $query;
        }

        $date = Carbon::now()->subYears($years);

        return $query->where('created_at', '<=', $date);
    }
}