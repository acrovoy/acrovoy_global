<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\ShippingCenter;
use App\Models\Country;

class AdminShippingCenterController extends Controller
{
    public function index()
    {
        $centers = ShippingCenter::all();
        return view('dashboard.admin.shipping-center.index', compact('centers'));
    }

    public function create()
    {

    $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('dashboard.admin.shipping-center.create', compact('countries'));
    }

    public function store(Request $request)
    {

    


        $data = $request->validate([
    'origin_country_id' => 'required|exists:countries,id',
    'destination_country_id' => 'required|exists:countries,id',
    'price' => 'required|numeric|min:0',
    'delivery_days' => 'required|integer|min:0',
    'is_active' => 'boolean',
    'notes' => 'nullable|string|max:1000',
    'origin_location_id' => 'nullable|exists:locations,id',
'destination_location_id' => 'nullable|exists:locations,id',
]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        ShippingCenter::create($data);

        return redirect()->route('admin.shipping-center.index')->with('success', 'The delivery service has been updated successfully.');
    }

    public function edit(ShippingCenter $shippingCenter)
    {

    $countries = Country::where('is_active', true)->orderBy('name')->get();
        return view('dashboard.admin.shipping-center.edit', compact('shippingCenter', 'countries'));
    }

    public function update(Request $request, ShippingCenter $shippingCenter)
    {
        $data = $request->validate([
    'origin_country_id' => 'required|exists:countries,id',
    'destination_country_id' => 'required|exists:countries,id',
    'price' => 'required|numeric|min:0',
    'delivery_days' => 'required|integer|min:0',
    'is_active' => 'boolean',
    'notes' => 'nullable|string|max:1000',
    'origin_location_id' => 'nullable|exists:locations,id',
'destination_location_id' => 'nullable|exists:locations,id',
]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $shippingCenter->update($data);

        return redirect()->route('admin.shipping-center.index')->with('success', 'The delivery service has been updated successfully.');
    }

    public function destroy(ShippingCenter $shippingCenter)
    {
        $shippingCenter->delete();

        return redirect()->route('admin.shipping-center.index')->with('success', 'Shipping Center deleted.');
    }
}
