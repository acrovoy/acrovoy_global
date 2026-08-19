<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AttributeGroup;
use App\Models\AttributeGroupTranslation;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeGroupController extends Controller
{
    /**
     * Display attribute groups.
     */
    public function index()
    {
        $groups = AttributeGroup::query()
            ->whereNull('owner_type')
            ->whereNull('owner_id')
            ->with('translations')
            ->withCount('attributes')
            ->orderBy('name')
            ->get();

        return view(
            'dashboard.admin.settings.attribute-groups.index',
            compact('groups')
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view(
            'dashboard.admin.settings.attribute-groups.create',
            compact('languages')
        );
    }


    /**
     * Store attribute group.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:attribute_groups,name',
            ],

            'translations' => [
                'nullable',
                'array',
            ],

            'translations.*' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);


        DB::transaction(function () use ($request) {

            $group = AttributeGroup::create([
                'name' => $request->input('name'),

                'is_active' => true,

                // System group
                'owner_type' => null,
                'owner_id' => null,

                'created_by' => auth()->id(),
            ]);


            foreach (
                $request->input('translations', [])
                as $locale => $name
            ) {

                if (blank($name)) {
                    continue;
                }

                AttributeGroupTranslation::create([
                    'attribute_group_id' => $group->id,
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }
        });


        return redirect()
            ->route('admin.settings.attribute-groups.index')
            ->with(
                'success',
                'Attribute group created successfully.'
            );
    }


    /**
     * Show edit form.
     */
    public function edit(AttributeGroup $group)
    {
        // Only system groups can be edited here.
        abort_if(
            $group->owner_type !== null ||
            $group->owner_id !== null,
            404
        );


        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();


        $group->load('translations');


        return view(
            'dashboard.admin.settings.attribute-groups.edit',
            compact(
                'group',
                'languages'
            )
        );
    }


    /**
     * Update attribute group.
     */
    public function update(Request $request, AttributeGroup $group)
{
    abort_if(
        $group->owner_type !== null ||
        $group->owner_id !== null,
        404
    );

    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'translations' => ['nullable', 'array'],
        'translations.*' => ['nullable', 'string', 'max:255'],
    ]);

    DB::transaction(function () use ($request, $group) {

        $group->update([
            'name' => $request->input('name'),
            'is_active' => $request->boolean('is_active'),
        ]);

        AttributeGroupTranslation::where(
            'attribute_group_id',
            $group->id
        )->delete();

        foreach (
            $request->input('translations', []) as $locale => $name
        ) {

            if (blank($name)) {
                continue;
            }

            $group->translations()->create([
                'locale' => $locale,
                'name' => $name,
            ]);
        }
    });

    return redirect()
        ->route('admin.settings.attribute-groups.index')
        ->with('success', 'Attribute group updated successfully.');
}


    /**
     * Delete attribute group.
     */
    public function destroy(AttributeGroup $attributeGroup)
    {
        // Only system groups can be deleted here.
        abort_if(
            $attributeGroup->owner_type !== null ||
            $attributeGroup->owner_id !== null,
            404
        );


        /*
         * Do not delete a group that is already
         * assigned to attributes.
         */
        if ($attributeGroup->attributes()->exists()) {

            return back()
                ->with(
                    'error',
                    'Cannot delete this group because it is assigned to attributes.'
                );
        }


        DB::transaction(function () use ($attributeGroup) {

            /*
             * Delete translations first.
             */
            AttributeGroupTranslation::where(
                'attribute_group_id',
                $attributeGroup->id
            )->delete();


            $attributeGroup->delete();
        });


        return redirect()
            ->route('admin.settings.attribute-groups.index')
            ->with(
                'success',
                'Attribute group deleted successfully.'
            );
    }
}