<?php

namespace App\Http\Controllers\Project\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Domain\Project\Models\Project;
use App\Models\Product;
use App\Models\Category;
use App\Models\AttributeGroup;
use App\Models\Attribute;

use App\Http\Requests\Project\CreateProjectRequest;
use App\Domain\RFQ\Services\RfqRequirementsLoader;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Actions\GetRfqCategoriesAction;

use App\Services\Company\ActiveContextService;
use App\Domain\Project\Actions\Buyer\ListBuyerProjectAction;
use App\Domain\Project\Actions\Buyer\CreateProjectAction;
use App\Domain\Project\DTO\CreateProjectData;

class BuyerProjectController extends Controller
{
    public function __construct(
        private ActiveContextService $context,
        private ListBuyerProjectAction $listBuyerProjectAction,
        private CreateProjectAction $createProjectAction,

    ) {}
    /**
     * Список проектов покупателя
     */
    public function index()
    {


        $result = $this->listBuyerProjectAction->execute($this->context);



        return view('project.buyer.index', [
            'projects' => $result['active'],
            'closedProjects' => $result['closed'],
        ]);
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        return view('project.buyer.create');
    }

    public function store(
        CreateProjectRequest $request,
        ActiveContextService $context
    ) {


        $buyerType = $context->type();
        $buyerId = $context->id();


        /**
         * DTO
         */

        $dto = CreateProjectData::fromArray(
            $request->validated()
        );

        /**
         * CREATE PROJECT
         */

        $rfq = $this->createProjectAction->execute(
            $dto,
            $buyerId,
            $buyerType,
            auth()->id()
        );

        return redirect()
            ->route('buyer.projects.index')
            ->with('success', 'Project created successfully');
    }





    public function show(Project $project)
    {

        $project->load([
            'rfqs.product',
            'rfqs.attributeValues',
        ]);

        return view('project.buyer.show', compact('project'));
    }

    public function requirements(Request $request, Project $project,
    Rfq $rfq)
    {

    if ($rfq->project_id !== $project->id) {
    abort(404);
}
        app(RfqRequirementsLoader::class)->load($rfq);




        /*
            |--------------------------------------------------------------------------
            | LOAD RFQ CATEGORIES ONLY
            |--------------------------------------------------------------------------
            */

        $categories = app(GetRfqCategoriesAction::class)->execute();

        /*
            |--------------------------------------------------------------------------
            | SELECT CATEGORY
            |--------------------------------------------------------------------------
            */

        $selectedCategory = null;

        /*
            |----------------------------------------------------------------------
            | RESOLVE CATEGORY (request OR saved RFQ)
            |----------------------------------------------------------------------
            */

        $categoryId = $request->get('category_id') ?? $rfq->category_id;

        if ($categoryId) {

            $selectedCategory = Category::query()
                ->with([
                    'translations',
                    'attributes' => function ($query) {
                        $query->with([
                            'translations',
                            'options.translations'
                        ]);
                    }
                ])
                ->forType('rfq')
                ->find($categoryId);

            $savedValues = $rfq->attributeValues
                ->keyBy('attribute_id');

            if ($selectedCategory) {

                $hiddenIds = $rfq->hiddenAttributes->pluck('id')->toArray();


                $attributes = $selectedCategory->attributes
                    ->whereNotIn('id', $hiddenIds)
                    ->sortBy('pivot.sort_order')
                    ->map(function ($attribute) use ($savedValues) {

                        $value = $savedValues->get($attribute->id);

                        $attribute->saved_value = match ($attribute->type) {

                            'select' => $value?->attribute_option_id,

                            'number',
                            'decimal' => $value?->value_number,

                            'boolean' => $value?->value_boolean,

                            'date' => $value?->value_date,

                            default => $value?->value_text,
                        };

                        $attribute->saved_options = $value?->options?->pluck('id')->toArray() ?? [];

                        return $attribute;
                    });
            }
        }

        $ownerType = $this->context->type();

        $ownerId = $this->context->id();



        $customRequirementIds = $rfq->customAttributeValues
            ->pluck('attribute_id')
            ->unique();

        $availableAttributes = Attribute::query()
            ->where('entity_type', 'rfq')
            ->where('context', 'requirement')
            ->where('is_custom', 1)
            ->where('is_active', true)
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->whereNotIn('id', $customRequirementIds) // 💥 важно
            ->get();

        $availableAttributesGrouped = $availableAttributes
            ->load('group')
            ->groupBy(fn($attr) => $attr->group?->name ?? 'General')
            ->sortBy(function ($attrs, $groupName) {
                return strtolower($groupName) === 'general' ? 0 : 1;
            });




        $attachedAttributes = $rfq->customAttributeValues()
            ->with([
                'attribute.group',
                'attribute.options.translations',
                'options.translations',
            ])
            ->get()
            ->map(function ($value) {

                $attribute = $value->attribute;

                /*
        |--------------------------------------------------------------
        | SAVED VALUE BY ATTRIBUTE TYPE
        |--------------------------------------------------------------
        */

                $attribute->saved_value = match ($attribute->type) {

                    'select' => $value->attribute_option_id,

                    'number',
                    'decimal' => $value->value_number,

                    'boolean' => $value->value_boolean,

                    'date' => $value->value_date,

                    default => $value->value_text,
                };

                /*
        |--------------------------------------------------------------
        | MULTISELECT OPTIONS
        |--------------------------------------------------------------
        */

                $attribute->saved_options =
                    $value->options?->pluck('id')->toArray() ?? [];

                return $attribute;
            })
            ->groupBy(fn($attr) => $attr->group?->name ?? 'General')
            ->sortBy(
                fn($_, $group) =>
                strtolower($group) === 'general' ? 0 : 1
            );






        $ownerType = $this->context->type();

        $ownerId = $this->context->id();

        $groups = AttributeGroup::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->get();
$selectedCategoryId = $rfq->category_id;
$selectedCategory = Category::find($selectedCategoryId);
        return view('project.buyer.requirements', compact('rfq', 'selectedCategory', 'categories', 'attributes', 
        'attachedAttributes', 'availableAttributesGrouped', 'project'));
    }

    public function updateField(Request $request, Project $project)
    {
        $field = $request->input('field');

        match ($field) {
            'title' => $project->update([
                'title' => $request->title
            ]),

            'description' => $project->update([
                'description' => $request->description
            ]),

            'deadline' => $project->update([
                'closed_at' => $request->closed_at
            ]),

            default => null,
        };

        return back();
    }
}
