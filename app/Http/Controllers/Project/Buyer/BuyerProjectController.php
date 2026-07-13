<?php

namespace App\Http\Controllers\Project\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Domain\Project\Models\Project;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\AttributeGroup;
use App\Models\Attribute;
use App\Models\UserAddress;
use App\Models\User;

use App\Http\Requests\Project\CreateProjectRequest;
use App\Domain\RFQ\Services\RfqRequirementsLoader;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Actions\GetRfqCategoriesAction;
use App\Domain\Project\Actions\Buyer\InviteProjectParticipantAction;
use App\Domain\Project\Actions\Buyer\RemoveProjectParticipantAction;
use App\Domain\Project\Actions\Buyer\UpdateProjectVisibilityAction;
use App\Domain\Project\Actions\Buyer\UpdateProjectVisibilityCategoriesAction;

use App\Domain\Project\Models\ProjectParticipant;
use App\Domain\Project\Enums\ProjectVisibilityType;

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

        private InviteProjectParticipantAction $inviteParticipant,
        private RemoveProjectParticipantAction $removeParticipant,
        private UpdateProjectVisibilityAction $updateVisibility,
        private UpdateProjectVisibilityCategoriesAction $updateVisibilityCategories,

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

    public function offers(Project $project, Rfq $rfq)
    {
  

    $ownerType = $this->context->type();

        $ownerId = $this->context->id();

        $groups = AttributeGroup::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->get();

        $counterOffers = collect();
        $counterItemsByAttribute = collect();
        $lastsubmittedVersion = collect();
        $existingDraftCounter = null;
        $supplierOfferVersion = null;


            $rfq->loadMissing([
                'offers.participant',
                'offers.latestVersion',
                'offers.versions.items.options',
            ]);

            $addressa = UserAddress::find($rfq->delivery_address_id);

            if (!$addressa) {
                $cityId = null;
            } else {

                $eexistingLocation = \App\Models\Location::where('name', $addressa->city)
                    ->where('parent_id', $addressa->region)
                    ->first();

                $cityId = $eexistingLocation?->id;
            }

            $supplierOffer = $rfq->offers->first();
            $supplierType = $supplierOffer->participant_type;
            $supplierId = $supplierOffer->participant_id;

            $shippingTemplates = collect();

            if ($cityId) {
                $shippingTemplates = \App\Models\ShippingTemplate::with([
                    'translations',
                    'warehouse',
                    'warehouse.location.parent',
                ])
                    ->where('is_active', 1)
                    ->where('provider_type', $supplierType)
                    ->where('provider_id', $supplierId)
                    ->whereHas('locations', function ($q) use ($cityId) {
                        $q->where('locations.id', $cityId);
                    })
                    ->get();
            }

            $offers = $rfq->offers
                ->filter(function ($offer) {
                    return $offer->latestVersion &&
                        in_array($offer->latestVersion->status, [
                            'submitted',
                            'accepted',
                            'rejected'
                        ]);
                });

            $offerId = request('offer');

            $offer = null;
            $offerVersion = null;
            $versions = collect();

            // =========================
            // SELECT OFFER
            // =========================
            if ($offerId) {
                $offer = $offers->firstWhere('id', (int) $offerId);
            }

            // =========================
            // AUTO SELECT FIRST
            // =========================
            if (!$offer && $offers->isNotEmpty()) {
                $offer = $offers->first();
            }

            // =========================
            // LOAD VERSIONS (ALWAYS SAFE)
            // =========================
            if ($offer) {

                $offerVersion = $offer->latestVersion;

                $supplierOfferVersion = $offer->versions()
                    ->where('is_counter', 0)
                    ->where('status', '!=', 'draft')
                    ->orderByDesc('created_at')
                    ->with(['items.options'])
                    ->first();

                $lastsubmittedVersion = $offer->versions()
                    ->where('status', '!=', 'draft')
                    ->orderByDesc('created_at')
                    ->first();

                $versions = $offer->versions()
                    ->where(function ($q) {

                        // НЕ supplier draft
                        $q->where(function ($q) {
                            $q->where('is_counter', 1)
                                ->orWhere('status', '!=', 'draft');
                        });
                    })
                    ->orderByDesc('created_at')
                    ->get();
            }

            // =========================================================
            // ACTIVE VERSION (VIEW ONLY SWITCH)
            // =========================================================
            $counterVersionId = request('counter_version');

            $counterVersion = $counterVersion ?? null;

            if ($offer && $counterVersionId) {
                $counterVersion = $offer->versions()
                    ->with([
                        'items.options'
                    ])
                    ->where('id', (int) $counterVersionId)
                    ->first();
            }

            $offerVersion = $counterVersion ?? $offerVersion;

            $offerVersion = $offerVersion && $offerVersion->status !== 'draft'
                ? $offerVersion
                : $offer->versions()
                ->where('status', '!=', 'draft')
                ->orderByDesc('created_at')
                ->first();

            if ($offerVersion->is_counter == 1) {
                $versionNumberOfCounter = $offerVersion->version_number;
                $supplierOfferVersionToCounter = $offer->versions()
                    ->where('status', '!=', 'draft')
                    ->where('is_counter', 0)
                    ->where('version_number', $versionNumberOfCounter - 1)
                    ->first();
            }





            // =========================================================
            // AUTO REDIRECT TO COUNTER DRAFT
            // (ONLY WHEN NOT IN VIEW MODE)
            // =========================================================

            $isCounterRoute = request()->routeIs('buyer.rfqs.counter-offer.create');

            $isViewingVersion = (bool) $counterVersionId;

            if (
                $offer &&
                !$isCounterRoute &&
                !$isViewingVersion
            ) {

                $existingDraftCounter = $offer->versions()
                    ->where('is_counter', 1)
                    ->where('status', 'draft')
                    ->where('owner_type', $ownerType)
                    ->where('owner_id', $ownerId)
                    ->orderByDesc('created_at')
                    ->first();

                if ($existingDraftCounter) {


                    $counterItemsByAttribute = $existingDraftCounter
                        ? $existingDraftCounter->items()
                        ->with('options')
                        ->get()
                        ->keyBy('attribute_id')
                        : collect();



                    return redirect()->route(
                        'buyer.rfqs.counter-offer.create',
                        [
                            'rfq' => $rfq->id,
                            'offer' => $offer->id,
                            'version' => $existingDraftCounter->id,
                            'shippingTemplates' => $shippingTemplates,
                            'counterVersion' => $counterVersion,
                            'supplierOfferVersionToCounter' => $supplierOfferVersionToCounter,
                            'supplierOfferVersion' => $supplierOfferVersion,
                            'counterItemsByAttribute' => $counterItemsByAttribute ?? collect(),

                        ]
                    );
                }
            }
        }



        public function participants(Project $project)
{
    $project->load([
        'participants.participant',
    ]);

    /*
    |--------------------------------------------------------------------------
    | ALL POSSIBLE PARTICIPANTS
    |--------------------------------------------------------------------------
    */

    $allparticipants = collect()

    ->merge(
        Supplier::query()
            ->orderBy('name')
            ->get()
            ->map(fn ($supplier) => [
                'id'    => $supplier->id,
                'type'  => Supplier::class,
                'label' => $supplier->name,
            ])
    )

    ->merge(
        User::query()
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => [
                'id'    => $user->id,
                'type'  => User::class,
                'label' => trim($user->name . ' ' . $user->last_name),
            ])
    )

    ->sortBy('label')
    ->values();

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */

    $allCategories = Category::query()
        ->forType('product')
        ->orderBy('sort_order')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | SELECTED CATEGORIES
    |--------------------------------------------------------------------------
    */

    $selectedCategoryIds = $project->visibilityCategories()
        ->pluck('categories.id')
        ->toArray();

    return view(
        'project.buyer.participants',
        [
            'project'             => $project,
            'participants'        => $project->participants,
            'allparticipants'     => $allparticipants,
            'allCategories'       => $allCategories,
            'selectedCategoryIds' => $selectedCategoryIds,
            'visibility'          => $project->visibility_type->value,
        ]
    );
}


public function updateVisibility(
    Request $request,
    Project $project
) {
    $validated = $request->validate([
        'visibility_type' => ['required'],
    ]);

    $this->updateVisibility->execute(
        $project,
        ProjectVisibilityType::from($validated['visibility_type'])
    );

    return back()->with('success', 'Visibility updated.');
}

public function storeParticipant(
    Request $request,
    Project $project
) {
    $request->validate([
        'participant_type' => 'required|string',
        'participant_id'   => 'required|integer',
    ]);

    $participant = $request->participant_type::findOrFail(
        $request->participant_id
    );

    $this->inviteParticipant->execute(
        $project,
        $participant
    );

    return back()->with('success', 'Participant invited.');
}

public function removeParticipant(
    Project $project,
    ProjectParticipant $participant
) {
    abort_unless(
        $participant->project_id === $project->id,
        404
    );

    $this->removeParticipant->execute($participant);

    return back()->with('success', 'Participant removed.');
}
   

public function updateVisibilityCategories(
    Request $request,
    Project $project
) {
    $this->updateVisibilityCategories->execute(
        $project,
        $request->input('category_ids', [])
    );

    return back()->with('success', 'Categories updated.');
}



}
