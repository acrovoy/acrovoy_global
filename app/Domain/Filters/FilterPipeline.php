<?php

namespace App\Domain\Filters;

use Illuminate\Database\Eloquent\Builder;

class FilterPipeline
{
    /**
     * Массив фильтров в формате:
     * 'param_name' => FilterClass
     * 
     * @var array<string, App\Domain\Filters\Contracts\FilterInterface>
     */
    protected array $filters = [];

    /**
     * Конструктор.
     *
     * @param array<string, App\Domain\Filters\Contracts\FilterInterface> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Применяет все фильтры к запросу.
     *
     * @param Builder $query   - Eloquent Builder
     * @param array   $params  - массив параметров фильтров ['category' => 'chairs', 'min_price' => 50]
     * @return Builder
     */
    public function apply(Builder $query, array $params): Builder
    {
        foreach ($this->filters as $param => $filter) {
            if (!empty($params[$param])) {
                $query = $filter->apply($query, $params[$param]);
            }
        }

        return $query;
    }

    /**
     * Добавляет фильтр в pipeline.
     *
     * @param string $paramName
     * @param App\Domain\Filters\Contracts\FilterInterface $filter
     */
    public function addFilter(string $paramName, $filter): void
    {
        $this->filters[$paramName] = $filter;
    }
}