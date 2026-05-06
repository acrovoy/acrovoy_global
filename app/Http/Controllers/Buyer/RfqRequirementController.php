<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Domain\RFQ\Models\Rfq;
use App\Models\Category;
use App\Models\Attribute;


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

    public function storeCustomAttribute(Request $request, Rfq $rfq)
{
    $data = $request->validate([
        'id' => ['nullable', 'exists:attributes,id'],
        'key' => ['required', 'string'],
        'type' => ['required', 'string'],
        'value' => ['nullable'],
        'options' => ['nullable', 'array'],
    ]);

    /*
    |--------------------------------------------------------------------------
    | SYSTEM CODE (EN SAFE)
    |--------------------------------------------------------------------------
    */

    $code = Str::slug(
        $data['key'],
        '_'
    );

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE
    |--------------------------------------------------------------------------
    */

    $attribute = Attribute::updateOrCreate(
        [
            'id' => $data['id'] ?? null,
            'context' => 'rfq',
        ],
        [
            'code' => $code,   // 👈 ВАЖНО
            'type' => $data['type'],
            'is_custom' => 1,
            'is_system' => 0,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | TRANSLATION (NAME)
    |--------------------------------------------------------------------------
    */

    $attribute->translations()->updateOrCreate(
        [
            'locale' => app()->getLocale(),
        ],
        [
            'name' => $data['key'],
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | VALUE
    |--------------------------------------------------------------------------
    */

    $value = $data['value'];

    if (is_array($value)) {
        $value = json_encode(array_values($value));
    }

    RfqAttributeValue::updateOrCreate(
        [
            'rfq_id' => $rfq->id,
            'attribute_id' => $attribute->id,
        ],
        [
            'value_text' => in_array($data['type'], ['text', 'select', 'multiselect'])
                ? $value
                : null,

            'value_number' => $data['type'] === 'number'
                ? $value
                : null,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    if (in_array($data['type'], ['select', 'multiselect'])) {

        $attribute->options()->delete();

        foreach ($data['options'] ?? [] as $opt) {

            if (!$opt) continue;

            $option = $attribute->options()->create();

            $option->translations()->create([
                'locale' => app()->getLocale(),
                'value' => $opt,
            ]);
        }
    }

    return back()->with('success', 'Attribute saved');
}
}