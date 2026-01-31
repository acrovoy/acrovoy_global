<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

use App\Models\Language;

class LocaleController extends Controller
{
    public function switch(string $locale)
    {
        if (!Language::where('code', $locale)->where('is_active', true)->exists()) {
            // Получаем код языка по умолчанию
            $locale = Language::where('is_default', true)->value('code');

            // Если даже default не найден — можно задать 'en' или вернуть 404
            if (!$locale) {
                abort(404);
            }
        }

        // Применяем язык
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Сохраняем язык в cookie на 1 год для гостей
        Cookie::queue('locale', $locale, 60*24*365); // 60*24*365 минут = 1 год

        // Если пользователь залогинен — сохраняем в БД
        if (Auth::check()) {
            $user = Auth::user();
            $user->language = $locale;
            $user->save();
        }

        return redirect()->back();
    }
}
