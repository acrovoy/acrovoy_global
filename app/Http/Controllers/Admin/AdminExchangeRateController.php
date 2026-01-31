<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\ExchangeRate;

class AdminExchangeRateController extends Controller
{
    public function index()
    {
        $currencies = Currency::with('rate')
            ->orderBy('code')
            ->get();

        return view('dashboard.admin.exchange-rates.index', compact('currencies'));
    }

    public function update(Request $request, Currency $currency)
    {
        // USD всегда 1
        if ($currency->code === 'USD') {
            return back()->with('error', 'USD rate cannot be changed');
        }

        $request->validate([
            'rate' => 'required|numeric|min:0',
        ]);

        ExchangeRate::updateOrCreate(
            ['currency_code' => $currency->code],
            ['rate' => $request->rate]
        );

        return back()->with('success', 'Exchange rate updated');
    }
}
