<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Location;
use App\Models\Country;

class LocationController extends Controller
{
    public function index(Request $request)
{
    $selectedCountry = $request->get('country_id');
    $selectedRegion  = $request->get('region_id');

    $allCountries = Country::all();

    // ❗ регионы для фильтра (без пагинации)
    $allRegions = Location::whereNull('parent_id')
        ->when($selectedCountry, fn ($q) =>
            $q->where('country_id', $selectedCountry)
        )
        ->get();

    // основной запрос
    $query = Location::with(['children', 'country'])
        ->whereNull('parent_id');

    if ($selectedCountry) {
        $query->where('country_id', $selectedCountry);
    }

    if ($selectedRegion) {
        $query->where('id', $selectedRegion);
    }

    // ✅ ПАГИНАЦИЯ
    $regions = $query
        ->orderBy('name')
        ->paginate(8)
        ->withQueryString();

    return view('dashboard.admin.settings.locations.index', compact(
        'regions',
        'allCountries',
        'allRegions',
        'selectedCountry',
        'selectedRegion'
    ));
}





    public function create()
    {
        $countries = Country::all();
        $regions = Location::whereNull('parent_id')->get();
        return view('dashboard.admin.settings.locations.create', compact('regions', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:locations,id',
            'country_id' => 'required|exists:countries,id',
        ]);

        Location::create($request->all());
        return redirect()->route('admin.settings.locations.index')->with('success', 'Населённый пункт добавлен.');
    }

    public function edit(Location $location)
    {

    
        $countries = Country::all();
        
        $regions = Location::whereNull('parent_id')->get();
        return view('dashboard.admin.settings.locations.edit', compact('location', 'regions', 'countries'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:locations,id',
            'country_id' => 'required|exists:countries,id',
        ]);

        $location->update($request->all());
        return redirect()->route('admin.settings.locations.index')->with('success', 'Населённый пункт обновлён.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return back()->with('success', 'Населённый пункт удалён.');
    }

    public function regionsByCountry(Request $request)
{
    $countryId = $request->get('country_id');

    $regions = Location::whereNull('parent_id')
                ->when($countryId, fn($q) => $q->where('country_id', $countryId))
                ->get(['id', 'name']);

    return response()->json($regions);
}

public function regionsWithChildren(Request $request)
{
    $countryId = $request->get('country_id');

    // Берём регионы верхнего уровня с детьми
    $regions = Location::with('children')
                ->whereNull('parent_id')
                ->when($countryId, fn($q) => $q->where('country_id', $countryId))
                ->get(['id', 'name']);

    return response()->json($regions);
}

}
