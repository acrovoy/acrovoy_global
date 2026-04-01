<?php

namespace App\Domain\Filters\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    /**
     * Применяет фильтр к Eloquent Builder.
     *
     * @param Builder $query  - текущий запрос
     * @param mixed   $value  - значение фильтра (string, array, int, etc.)
     * @return Builder
     */
    public function apply(Builder $query, mixed $value): Builder;
}