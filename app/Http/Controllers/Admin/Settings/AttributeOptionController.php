<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\AttributeOptionTranslation;

class AttributeOptionController extends Controller
{
    public function index(Attribute $attribute)
    {
        abort_if(
            !in_array($attribute->type, ['select', 'multiselect']),
            404
        );

        $options = $attribute->options()
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return view(
            'dashboard.admin.settings.attributes.options.index',
            compact('attribute', 'options')
        );
    }


    public function store(Request $request, Attribute $attribute)
    {
        $request->validate([
            'translations' => 'required|array'
        ]);


        $option = AttributeOption::create([
            'attribute_id' => $attribute->id,
            'sort_order' => $request->sort_order ?? 0
        ]);


        foreach ($request->translations as $locale => $value) {

            if (!$value) continue;

            AttributeOptionTranslation::create([
                'attribute_option_id' => $option->id,
                'locale' => $locale,
                'value' => $value
            ]);
        }


        return back()->with(
            'success',
            'Option created'
        );
    }


    public function update(
        Request $request,
        Attribute $attribute,
        AttributeOption $option
    )
    {
        $request->validate([
            'translations' => 'required|array'
        ]);


        $option->update([
            'sort_order' => $request->sort_order ?? 0
        ]);


        AttributeOptionTranslation::where(
            'attribute_option_id',
            $option->id
        )->delete();


        foreach ($request->translations as $locale => $value) {

            if (!$value) continue;

            AttributeOptionTranslation::create([
                'attribute_option_id' => $option->id,
                'locale' => $locale,
                'value' => $value
            ]);
        }


        return back()->with(
            'success',
            'Option updated'
        );
    }


    public function destroy(
        Attribute $attribute,
        AttributeOption $option
    )
    {
        $option->translations()->delete();

        $option->delete();

        return back()->with(
            'success',
            'Option deleted'
        );
    }
}