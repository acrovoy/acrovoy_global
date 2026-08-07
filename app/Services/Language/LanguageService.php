<?php

namespace App\Services\Language;

use App\Models\Language;

class LanguageService
{
    public function activeLocales(): array
    {
        return Language::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }


    public function activeLanguages()
    {
        return Language::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}