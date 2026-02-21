<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SupplierType;
use App\Models\Language;

class SupplierTypeController extends Controller
{
    public function index()
    {
        $supplierTypes = SupplierType::with('translations')->get();

        return view(
            'dashboard.admin.settings.supplier-types.index',
            compact('supplierTypes')
        );
    }

    public function create()
    {
        
$languages = \App\Models\Language::where('is_active', true)->get();
        return view(
            'dashboard.admin.settings.supplier-types.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:supplier_types,slug',
            'translations.*.name' => 'required|string|max:255',
        ]);

        $type = SupplierType::create([
            'slug' => $request->slug,
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
            ->route('admin.settings.supplier-types.index')
            ->with('success', 'Supplier type created successfully');
    }

    public function edit(SupplierType $supplierType)
    {
        $languages = \App\Models\Language::where('is_active', true)->get();

        $supplierType->load('translations');

        return view(
            'dashboard.admin.settings.supplier-types.edit',
            compact('supplierType', 'languages')
        );
    }

    public function update(Request $request, SupplierType $supplierType)
    {
        $request->validate([
            'slug' => 'required|unique:supplier_types,slug,' . $supplierType->id,
            'translations.*.name' => 'required|string|max:255',
        ]);

        $supplierType->update([
            'slug' => $request->slug,
        ]);

        if (!empty($request->translations)) {

            foreach ($request->translations as $locale => $data) {

                $translation = $supplierType->translations()
                    ->firstOrNew(['locale' => $locale]);

                $translation->name = $data['name'] ?? '';
                $translation->save();
            }
        }

        return redirect()
            ->route('admin.settings.supplier-types.index')
            ->with('success', 'Supplier type updated successfully');
    }

    public function destroy(SupplierType $supplierType)
    {
        $supplierType->delete();

        return redirect()
            ->route('admin.settings.supplier-types.index')
            ->with('success', 'Supplier type deleted');
    }
}