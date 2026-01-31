<?php

use App\Models\Currency;
use App\Models\ExchangeRate;

if (!function_exists('price')) {
    /**
     * Конвертирует цену в выбранную валюту и форматирует.
     *
     * @param float $amount Сумма в базовой валюте (USD)
     * @param string|null $currency_code Код валюты (например, 'EUR'). Если null — берём из сессии.
     * @param bool $with_symbol Добавлять символ валюты
     * @return string
     */
    function price(?float $amount, string $currency_code = null, bool $with_symbol = true): string
{
    $amount = $amount ?? 0.0; // если null → 0.0

    // Определяем валюту
    $currency_code = $currency_code ?: (
        auth()->check() ? auth()->user()->currency : session('currency', 'USD')
    );

    // Берём курс
    $rate = ExchangeRate::where('currency_code', $currency_code)->first()?->rate ?? 1;

    // Конвертируем
    $converted = $amount * $rate;

    // Символ валюты
    $symbol = '';
    if ($with_symbol) {
        $symbol = Currency::where('code', $currency_code)->first()?->symbol ?? $currency_code;
    }

    return number_format($converted, 2) . ' ' . $symbol;
}
}
