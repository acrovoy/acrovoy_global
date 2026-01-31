<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Set selected currency
     */
    public function setCurrency($currency)
    {
        $currency = strtoupper($currency);

        // 1. Сохраняем в сессии
        session(['currency' => $currency]);

        // 2. Если пользователь авторизован — сохраняем в базе
        if (auth()->check()) {
            $user = auth()->user();
            $user->currency = $currency;
            $user->save();
        }

        return back();
    }
}
