<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

use App\Models\Country;

class CountryController extends Controller
{
    public function set(string $code)
    {
        $country = Country::where('code', $code)
            ->where('is_active', 1)
            ->firstOrFail();

        session(['purchase_country' => $country->code]);

        if (auth()->check()) {
            auth()->user()->update([
                'purchase_country' => $country->code
            ]);
        } else {
        // 🔥 Для гостей сохраняем в куки на 1 год
        Cookie::queue('purchase_country', $country->code, 60 * 24 * 365);
    }

        return redirect()->back();
    }
}
