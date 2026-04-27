<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Domain\RFQ\Models\Rfq;
use App\Models\Category;

use App\Domain\RFQ\Actions\SaveRfqRequirementsAction;


use App\Domain\RFQ\Models\RfqAttributeValue;

class RfqRequirementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX (optional)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE (legacy UI entry)
    |--------------------------------------------------------------------------
    */
    public function create(Rfq $rfq)
    {
        return view('rfq.requirements.create', compact('rfq'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT (WORKSPACE CATEGORY + ATTRIBUTES)
    |--------------------------------------------------------------------------
    */
    public function edit(Request $request, Rfq $rfq)
    {
        $categoryId = $request->get('category_id');

        $categories = Category::query()
            ->with('translations')
            ->selectable()
            ->forType('rfq')
            ->ordered()
            ->get();

        $selectedCategory = null;
        $attributes = collect();

        if ($categoryId) {

            $selectedCategory = Category::query()
                ->with([
                    'translations',
                    'attributes.translations',
                    'attributes.options.translations',
                ])
                ->forType('rfq')
                ->findOrFail($categoryId);

            $attributes = $selectedCategory->attributes
                ->sortBy('pivot.sort_order');
        }

        return view('rfq.workspace.requirements', [
            'rfq' => $rfq,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'attributes' => $attributes,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (NEW RFQ REQUIREMENTS ENGINE)
    |--------------------------------------------------------------------------
    */
    public function store(
        Request $request,
        SaveRfqRequirementsAction $action
    ) {

   

        $validated = $request->validate([
    'rfq_id' => ['required', 'exists:rfqs,id'],
    'category_id' => ['required', 'exists:categories,id'],

    'attributes' => ['nullable', 'array'],

    'custom_attributes' => ['nullable', 'array'],
'custom_attributes.*.id' => ['nullable'],
'custom_attributes.*._delete' => ['nullable'],
'custom_attributes.*.key' => ['nullable', 'string'],
'custom_attributes.*.value' => ['nullable'],
'custom_attributes.*.type' => ['nullable', 'string'],
]);

        $action->execute(
            $validated['rfq_id'],
            $validated['category_id'],
            $validated['attributes'] ?? [],
            $validated['custom_attributes'] ?? []
        );

        return back()->with('success', 'Requirements saved successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW (READ REQUIREMENTS FROM NEW TABLE)
    |--------------------------------------------------------------------------
    */
    public function show(Rfq $rfq)
    {
        $requirements = RfqAttributeValue::query()
            ->where('rfq_id', $rfq->id)
            ->with([
                'attribute.translations',
                'option.translations',
                'options.translations',
            ])
            ->get();

        return view('rfq.workspace.requirements.show', [
            'rfq' => $rfq,
            'requirements' => $requirements,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE (OPTIONAL - if inline editing exists)
    |--------------------------------------------------------------------------
    */
    public function update(
        Request $request,
        Rfq $rfq,
        RfqAttributeValue $value
    ) {
        abort_unless($value->rfq_id === $rfq->id, 403);

        $data = $request->validate([
            'value_text' => ['nullable', 'string'],
            'value_number' => ['nullable', 'numeric'],
            'value_boolean' => ['nullable', 'boolean'],
            'value_date' => ['nullable', 'date'],
            'attribute_option_id' => ['nullable', 'exists:attribute_options,id'],
        ]);

        $value->update($data);

        return back()->with('success', 'Requirement updated');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(
        Rfq $rfq,
        RfqAttributeValue $value
    ) {
        abort_unless($value->rfq_id === $rfq->id, 403);

        $value->delete();

        return back()->with('success', 'Requirement deleted');
    }
}