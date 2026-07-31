<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BusinessType;
use App\Models\Language;

class BusinessTypeController extends Controller
{
    public function index()
    {
        $businessTypes = BusinessType::with('translations')->get();

        return view(
            'dashboard.admin.settings.business-types.index',
            compact('businessTypes')
        );
    }

    public function create()
    {
        
$languages = Language::where('is_active', true)->get();

$targetTypes = [
        'supplier' => 'Supplier',
        'buyer' => 'Buyer',
        'logistic_company' => 'Logistic Company',
        'user_supplier' => 'Supplier Individual',
        'user_buyer' => 'Buyer Individual',
    ];

    return view(
        'dashboard.admin.settings.business-types.create',
        compact('languages', 'targetTypes')
    );
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:business_types,slug',
            'target_type' => 'required|in:supplier,buyer,logistic_company,user',
            'translations.*.name' => 'required|string|max:255',
        ]);

        $type = BusinessType::create([
            'slug' => $request->slug,
            'target_type' => $request->target_type,
        ]);

        if (!empty($request->translations)) {

            foreach ($request->translations as $locale => $data) {

                $type->translations()->create([
                    'locale' => $locale,
                    'name' => $data['name'] ?? '',
                ]);
            }
        }

        return redirect()
            ->route('admin.settings.business-types.index')
            ->with('success', 'Business type created successfully');
    }

    public function edit(BusinessType $businessType)
    {
        $languages = Language::where('is_active', true)->get();

         $targetTypes = [
        'supplier' => 'Supplier',
        'buyer' => 'Buyer',
        'logistic_company' => 'Logistic Company',
        'user_supplier' => 'Supplier Individual',
        'user_buyer' => 'Buyer Individual',
    ];

        $businessType->load('translations');

        return view(
            'dashboard.admin.settings.business-types.edit',
            compact('businessType', 'languages', 'targetTypes')
        );
    }

    public function update(Request $request, BusinessType $businessType)
    {
        $request->validate([
            'slug' => 'required|unique:business_types,slug,' . $businessType->id,
            'target_type' => 'required|in:supplier,buyer,logistic_company,user',
            'translations.*.name' => 'required|string|max:255',
        ]);

        $businessType->update([
            'slug' => $request->slug,
            'target_type' => $request->target_type,
        ]);

        if (!empty($request->translations)) {

            foreach ($request->translations as $locale => $data) {

                $translation = $businessType->translations()
                    ->firstOrNew(['locale' => $locale]);

                $translation->name = $data['name'] ?? '';
                $translation->save();
            }
        }

        return redirect()
            ->route('admin.settings.business-types.index')
            ->with('success', 'Business type updated successfully');
    }

    public function destroy(BusinessType $businessType)
    {
        $businessType->delete();

        return redirect()
            ->route('admin.settings.business-types.index')
            ->with('success', 'Business type deleted');
    }
}