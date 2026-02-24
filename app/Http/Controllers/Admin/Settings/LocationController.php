<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Location;
use App\Models\Country;
use App\Models\LocationTranslation;
use App\Models\Language;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $selectedCountry = $request->get('country_id');
        $selectedRegion  = $request->get('region_id');

        $locale = app()->getLocale();

        $allCountries = Country::all();

        $allRegions = Location::with(['translations'])
            ->whereNull('parent_id')
            ->when($selectedCountry, fn ($q) =>
                $q->where('country_id', $selectedCountry)
            )
            ->get();

        $unverifiedLocations = Location::whereNull('updated_by')
            ->orWhere('updated_by', '!=', auth()->id())
            ->with(['translations'])
            ->get();

        $query = Location::with([
                'children.translations',
                'country',
                'translations'
            ])
            ->whereNull('parent_id');

        if ($selectedCountry) {
            $query->where('country_id', $selectedCountry);
        }

        if ($selectedRegion) {
            $query->where('id', $selectedRegion);
        }

        $regions = $query
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        return view('dashboard.admin.settings.locations.index', compact(
            'regions',
            'allCountries',
            'allRegions',
            'selectedCountry',
            'selectedRegion',
            'unverifiedLocations',
            'locale'
        ));
    }

    public function create()
    {
        $countries = Country::all();
        $regions = Location::whereNull('parent_id')->get();

        return view('dashboard.admin.settings.locations.create', compact(
            'regions',
            'countries'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'parent_id' => 'nullable|exists:locations,id',
            'translations' => 'required|array'
        ]);

        $data = $request->only([
            'country_id',
            'parent_id'
        ]);

        $data['updated_by'] = auth()->id();

        $location = Location::create($data);

        foreach ($request->translations as $locale => $name) {

            if (!$name) continue;

            LocationTranslation::create([
                'location_id' => $location->id,
                'locale' => $locale,
                'name' => $name
            ]);
        }

        return redirect()
            ->route('admin.settings.locations.index')
            ->with('success', 'Населённый пункт добавлен.');
    }

    public function edit(Location $location)
    {
        $countries = Country::all();

        $regions = Location::whereNull('parent_id')->get();

        $location->load('translations');

        return view('dashboard.admin.settings.locations.edit', compact(
            'location',
            'regions',
            'countries'
        ));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'parent_id' => 'nullable|exists:locations,id',
            'translations' => 'required|array'
        ]);

        $data = $request->only([
            'country_id',
            'parent_id'
        ]);

        $data['updated_by'] = auth()->id();

        $location->update($data);

        LocationTranslation::where('location_id', $location->id)->delete();

        foreach ($request->translations as $locale => $name) {

            if (!$name) continue;

            LocationTranslation::create([
                'location_id' => $location->id,
                'locale' => $locale,
                'name' => $name
            ]);
        }

        return redirect()
            ->route('admin.settings.locations.index')
            ->with('success', 'Населённый пункт обновлён.');
    }

    public function destroy(Location $location)
    {
        $location->translations()->delete();
        $location->delete();

        return back()->with('success', 'Населённый пункт удалён.');
    }

    public function regionsByCountry(Request $request)
    {
        $countryId = $request->get('country_id');

        $regions = Location::with('translations')
            ->whereNull('parent_id')
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->get(['id', 'name']);

        return response()->json($regions);
    }

    public function regionsWithChildren(Request $request)
    {
        $countryId = $request->get('country_id');

        $regions = Location::with(['children.translations'])
            ->whereNull('parent_id')
            ->when($countryId, fn($q) => $q->where('country_id', $countryId))
            ->get(['id', 'name']);

        return response()->json($regions);
    }

    public function locationsByRegion(Request $request)
    {
        $regionId = $request->get('region_id');

        $locations = Location::with('translations')
            ->where('parent_id', $regionId)
            ->get(['id', 'name']);

        return response()->json($locations);
    }
}