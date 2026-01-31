<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Country;

class CountriesController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('sort_order')->get();

        return view('dashboard.admin.settings.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('dashboard.admin.settings.countries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:5|unique:countries,code',
            'name'        => 'required|string|max:255',
            'is_active'   => 'boolean',
            'is_priority' => 'boolean',
            'is_default'  => 'boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        Country::create([
            'code'        => strtolower($data['code']),
            'name'        => $data['name'],
            'is_active'   => $data['is_active'] ?? false,
            'is_priority' => $data['is_priority'] ?? false,
            'is_default'  => $data['is_default'] ?? false,
            'sort_order'  => $data['sort_order'] ?? null,
        ]);

        return redirect()
            ->route('admin.settings.countries.index')
            ->with('success', 'Country added successfully.');
    }

    public function edit(Country $country)
    {
        return view('dashboard.admin.settings.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:5|unique:countries,code,' . $country->id,
            'name'        => 'required|string|max:255',
            'is_active'   => 'boolean',
            'is_priority' => 'boolean',
            'is_default'  => 'boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $country->update([
            'code'        => strtolower($data['code']),
            'name'        => $data['name'],
            'is_active'   => $data['is_active'] ?? false,
            'is_priority' => $data['is_priority'] ?? false,
            'is_default'  => $data['is_default'] ?? false,
            'sort_order'  => $data['sort_order'] ?? null,
        ]);

        return redirect()
            ->route('admin.settings.countries.index')
            ->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()
            ->route('admin.settings.countries.index')
            ->with('success', 'Country deleted.');
    }
}
