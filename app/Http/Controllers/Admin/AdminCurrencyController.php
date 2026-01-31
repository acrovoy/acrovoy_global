<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Currency;
use App\Models\ExchangeRate;

class AdminCurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::with('rate')
            ->orderBy('code')
            ->get();

        return view('dashboard.admin.currencies.index', compact('currencies'));
    }


    public function create()
    {
        return view('dashboard.admin.currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|size:3|unique:currencies,code',
            'name'        => 'required|string|max:50',
            'symbol'      => 'nullable|string|max:5',
            'is_active'   => 'boolean',
            'is_default'  => 'boolean',
            'is_priority' => 'boolean',
            'priority'    => 'nullable|string|in:low,medium,high',
            'notes'       => 'nullable|string|max:255',
        ]);

        // 🔒 If this currency is set as default — unset others
        if ($request->boolean('is_default')) {
            Currency::where('is_default', 1)->update(['is_default' => 0]);
        }

        Currency::create([
            'code'        => strtoupper($request->code),
            'name'        => $request->name,
            'symbol'      => $request->symbol,
            'is_active'   => $request->boolean('is_active'),
            'is_default'  => $request->boolean('is_default'),
            'is_priority' => $request->boolean('is_priority'),
            'priority'    => $request->priority ?? 'medium',
            'notes'       => $request->notes,
        ]);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Currency added');
    }

    public function edit(Currency $currency)
    {
        return view('dashboard.admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name'        => 'required|string|max:50',
            'symbol'      => 'nullable|string|max:5',
            'is_active'   => 'boolean',
            'is_default'  => 'boolean',
            'is_priority' => 'boolean',
            'priority'    => 'nullable|string|in:low,medium,high',
            'notes'       => 'nullable|string|max:255',
        ]);

        // 🔒 Only one default currency allowed
        if ($request->boolean('is_default')) {
            Currency::where('id', '!=', $currency->id)
                ->where('is_default', 1)
                ->update(['is_default' => 0]);
        }

        $currency->update([
            'name'        => $request->name,
            'symbol'      => $request->symbol,
            'is_active'   => $request->boolean('is_active'),
            'is_default'  => $request->boolean('is_default'),
            'is_priority' => $request->boolean('is_priority'),
            'priority'    => $request->priority ?? $currency->priority,
            'notes'       => $request->notes,
        ]);

       

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Currency added');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return back()->with('success', 'Currency deleted');
    }
}
