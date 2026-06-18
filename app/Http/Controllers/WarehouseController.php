<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Country;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Company\ActiveContextService;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $context = app(ActiveContextService::class);

        $warehouses = Warehouse::with(['country', 'location'])
            ->where('provider_type', $context->type())
            ->where('provider_id', $context->id())
            ->latest()
            ->get();

        $countries = Country::withCurrentTranslation()
            ->orderBy('name')->get();

        return view('dashboard.supplier.warehouses.index', compact('warehouses', 'countries'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FORM
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        

        return view('dashboard.supplier.warehouses.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',

            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($data) {

            $context = app(ActiveContextService::class);

            // если ставим default — снимаем у других
            if (!empty($data['is_default'])) {
                Warehouse::where('provider_type', $context->type())
                    ->where('provider_id', $context->id())
                    ->update(['is_default' => false]);
            }

            Warehouse::create([
                'provider_type' => $context->type(),
                'provider_id' => $context->id(),

                'name' => $data['name'],
                'contact_person' => $data['contact_person'] ?? null,
                'phone' => $data['phone'] ?? null,
                
                'address' => $data['address'] ?? null,

                'is_default' => $data['is_default'] ?? false,
                'status' => 'pending',

                'created_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('supplier.warehouses.index')
            ->with('success', 'Warehouse created successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Warehouse $warehouse)
    {
       
        return view('dashboard.supplier.warehouses.edit', compact(
            'warehouse'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',

           
            'address' => 'nullable|string',

            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($warehouse, $data) {

            $context = app(ActiveContextService::class);

            // если делаем default — сбрасываем остальные
            if (!empty($data['is_default'])) {
                Warehouse::where('provider_type', $context->type())
                    ->where('provider_id', $context->id())
                    ->update(['is_default' => false]);
            }

            $warehouse->update([
                'name' => $data['name'],
                'contact_person' => $data['contact_person'] ?? null,
                'phone' => $data['phone'] ?? null,

                'address' => $data['address'] ?? null,

                'is_default' => $data['is_default'] ?? false,
            ]);
        });

        return redirect()
            ->route('supplier.warehouses.index')
            ->with('success', 'Warehouse updated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()
            ->route('supplier.warehouses.index')
            ->with('success', 'Warehouse deleted successfully');
    }


    public function attachLocation(Request $request)
{
    $request->validate([
        'warehouse_id' => 'required|exists:warehouses,id',
        'location_id'  => 'nullable|exists:locations,id',
        'country'      => 'nullable|exists:countries,id',
        'region'       => 'nullable|exists:locations,id',
        'city_manual'  => 'nullable|string|max:255',
    ]);

    


    $warehouse = Warehouse::findOrFail($request->warehouse_id);

    $locationId = $request->location_id;

    if ($locationId) {
        $location = Location::find($locationId);
        $countryId = $location?->country_id;
    }
    

    /**
     * =====================================================
     * 1. IF EXISTING LOCATION SELECTED
     * =====================================================
     */
    if (!$locationId && $request->filled('city_manual')) {

        $finalCity = $request->city_manual;

                $newLocation = Location::create([
                'name'       => $finalCity,
                'parent_id'  => $request->region ?: null,
                'country_id' => $request->country ?: null,
                'updated_by' => auth()->id(),
            ]);

            $locationId = $newLocation->id;
            $countryId = $newLocation->country_id;
        }
   

    /**
     * =====================================================
     * 2. ATTACH TO WAREHOUSE
     * =====================================================
     */
    $warehouse->update([
        'location_id' => $locationId,
        'country_id' => $countryId,
        'status' => 'active',
    ]);



    return back()->with('success', 'Location attached successfully.');
}
}