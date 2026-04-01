<?php

namespace App\Domain\Filters;

use App\Domain\Filters\Contracts\FilterInterface;

class FilterFactory
{
    /**
     * Массив конфигурации фильтров
     * 'request_param' => FilterClass::class
     *
     * @var array<string, class-string<FilterInterface>>
     */
    protected array $config = [];

    /**
     * Конструктор
     *
     * @param array<string, class-string<FilterInterface>> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Создаёт FilterPipeline на основе переданных параметров
     *
     * @param array $params - параметры запроса или любые данные ['category' => 'chairs']
     * @return FilterPipeline
     */
    public function make(array $params = []): FilterPipeline
    {
        $filters = [];

        foreach ($this->config as $param => $filterClass) {
            // Если фильтр задан в параметрах запроса
            if (!empty($params[$param])) {
                $filters[$param] = new $filterClass();
            }
        }

        return new FilterPipeline($filters);
    }
}