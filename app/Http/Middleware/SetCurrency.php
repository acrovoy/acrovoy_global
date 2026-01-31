<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCurrency
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Если пользователь авторизован и у него есть валюта
        if (auth()->check() && auth()->user()->currency) {
            session(['currency' => auth()->user()->currency]);
        }

        // 2. Если в сессии нет валюты вообще
        if (!session()->has('currency')) {
            session(['currency' => 'USD']);
        }

        return $next($request);
    }
}
