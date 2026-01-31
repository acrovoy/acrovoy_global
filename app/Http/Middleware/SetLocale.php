<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

use App\Models\Language;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // 🔥 Авторизованный → язык из БД
        if (Auth::check()) {
            $locale = Auth::user()->language;
        } else {
            // 👤 Гость → сначала проверяем cookie, затем сессию, затем fallback
            $locale = Cookie::get('locale', Session::get('locale', config('app.locale')));
        }

        // Проверяем, что язык существует и активен
        if (!Language::where('code', $locale)->where('is_active', true)->exists()) {
            // Берем язык по умолчанию из таблицы languages
            $locale = Language::where('is_default', true)->value('code') ?? config('app.locale');
        }

        // Применяем язык
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Сохраняем cookie на 1 год для гостей
        if (!Auth::check()) {
            Cookie::queue('locale', $locale, 60 * 24 * 365); // 60*24*365 = минуты в году
        }

        return $next($request);
    }


}