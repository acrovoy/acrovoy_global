<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attribute;
use App\Models\AttributeTranslation;
use App\Models\Language;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('translations')
            ->orderBy('sort_order')
            ->paginate(20);

        return view(
            'dashboard.admin.settings.attributes.index',
            compact('attributes')
        );
    }


    public function create()
    {
        return view(
            'dashboard.admin.settings.attributes.create'
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:attributes,code',
            'type' => 'required',
            'translations' => 'required|array',
        ]);


        $attribute = Attribute::create([
            'code' => $request->code,
            'type' => $request->type,
            'unit' => $request->unit,
            'is_required' => $request->boolean('is_required'),
            'is_filterable' => $request->boolean('is_filterable'),
            'sort_order' => $request->sort_order ?? 0,
        ]);


        foreach ($request->translations as $locale => $name) {

            if (!$name) continue;

            AttributeTranslation::create([
                'attribute_id' => $attribute->id,
                'locale' => $locale,
                'name' => $name
            ]);
        }


        return redirect()
            ->route('admin.settings.attributes.index')
            ->with('success', 'Attribute created');
    }


    public function edit(Attribute $attribute)
    {
        $attribute->load('translations');

        return view(
            'dashboard.admin.settings.attributes.edit',
            compact('attribute')
        );
    }


    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'code' => 'required|unique:attributes,code,' . $attribute->id,
            'type' => 'required',
            'translations' => 'required|array',
        ]);


        $attribute->update([
            'code' => $request->code,
            'type' => $request->type,
            'unit' => $request->unit,
            'is_required' => $request->boolean('is_required'),
            'is_filterable' => $request->boolean('is_filterable'),
            'sort_order' => $request->sort_order ?? 0,
        ]);


        AttributeTranslation::where(
            'attribute_id',
            $attribute->id
        )->delete();


        foreach ($request->translations as $locale => $name) {

            if (!$name) continue;

            AttributeTranslation::create([
                'attribute_id' => $attribute->id,
                'locale' => $locale,
                'name' => $name
            ]);
        }


        return redirect()
            ->route('admin.settings.attributes.index')
            ->with('success', 'Attribute updated');
    }


    public function destroy(Attribute $attribute)
    {
        $attribute->translations()->delete();
        $attribute->delete();

        return back()->with(
            'success',
            'Attribute deleted'
        );
    }
}
