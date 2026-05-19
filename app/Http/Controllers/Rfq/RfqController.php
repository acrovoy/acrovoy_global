<?php

namespace App\Http\Controllers\Rfq;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use App\Models\Supplier;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Domain\RFQ\Models\RfqAttributeValue;
use App\Domain\Negotiation\Models\RfqOffer;


class RfqController extends Controller
{
    public function __construct(
        private ActiveContextService $context
    ) {}

    public function show(Request $request, Rfq $rfq)
    {
        $this->authorize('view', $rfq);


        $buyerSnapshotMap = $rfq->attributeValues
            ->keyBy('attribute_id');



        $allowedTabs = [
            'overview',
            'requirements',
            's-requirements',
            'participants',
            'offers',
            'audit',
        ];

        $activeTab = $request->get('tab', 'overview');

        if (!in_array($activeTab, $allowedTabs)) {
            $activeTab = 'overview';
        }

        /*
        |--------------------------------------------------------------------------
        | BASE RFQ RELATIONS (always loaded)
        |--------------------------------------------------------------------------
        */

        $rfq->loadMissing([
            'participants.participant',
            'visibilityCategories',
        ]);

        /*
        |--------------------------------------------------------------------------
        | TAB-AWARE LAZY LOADING
        |--------------------------------------------------------------------------
        */

        $categories = null;
        $selectedCategory = null;
        $attributes = collect();

        if ($activeTab === 'requirements') {

            $rfq->loadMissing([

                /*
        |--------------------------------------------------------------------------
        | SYSTEM ATTRIBUTES
        |--------------------------------------------------------------------------
        */
                'systemAttributeValues.attribute.translations',
                'systemAttributeValues.attribute.options.translations',
                'systemAttributeValues.options',

                /*
        |--------------------------------------------------------------------------
        | CUSTOM ATTRIBUTES
        |--------------------------------------------------------------------------
        */
                'customAttributeValues.attribute.translations',
                'customAttributeValues.attribute.options.translations',

                'customAttributeValues.options.translations',
            ]);





            /*
            |--------------------------------------------------------------------------
            | LOAD RFQ CATEGORIES ONLY
            |--------------------------------------------------------------------------
            */

            $categories = Category::query()
                ->with('translations')
                ->selectable()
                ->forType('rfq')
                ->ordered()
                ->get();

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
                    $attributes = $selectedCategory->attributes
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
        }

        $canCreateRevision = false;
        $versions = collect();
        $currentDraft = null;
        $offerVersion = null;
        $offer = null;
        $offers = null;
        $counterVersion = null;
        $isReadonly = true;

        if ($activeTab === 's-requirements') {

            $rfq->loadMissing([
                'attributeValues.attribute.options',
                'attributeValues.options',
            ]);

            $supplier = $this->context->supplier();

            if (!$supplier) {
                abort(403);
            }

            $offer = \App\Domain\Negotiation\Models\RfqOffer::query()
                ->firstOrCreate([
                    'rfq_id' => $rfq->id,
                    'participant_type' => get_class($supplier),
                    'participant_id' => $supplier->id,
                ]);

            /*
    |--------------------------------------------------------------------------
    | SELECT VERSION FROM REQUEST
    |--------------------------------------------------------------------------
    */
            $requestedVersionId = request('version');

            if ($requestedVersionId) {

                $offerVersion = $offer->versions()
                    ->with(['items.options.translations'])
                    ->where('id', $requestedVersionId)
                    ->first();
            }

            /*
    |--------------------------------------------------------------------------
    | DEFAULT BEHAVIOUR
    |--------------------------------------------------------------------------
    */
            if (!$offerVersion) {

                $offerVersion = $offer->versions()
                    ->where('status', 'draft')
                    ->orderByDesc('version_number')
                    ->first();

                /*
        |--------------------------------------------------------------------------
        | IF NO DRAFT EXISTS
        |--------------------------------------------------------------------------
        */
                if (!$offerVersion) {

                    if ($offer->versions()->count() === 0) {

                        $offerVersion = $offer->versions()->create([
                            'version_number' => 1,
                            'status' => 'draft',
                            'created_by' => $this->context->user()->id,
                        ]);
                    } else {

                        $offerVersion = $offer->versions()
                            ->orderByDesc('version_number')
                            ->first();
                    }
                }
            }

            /*
    |--------------------------------------------------------------------------
    | LOAD RELATIONS (SAFE)
    |--------------------------------------------------------------------------
    */
            if ($offerVersion) {
                $offerVersion->load([
                    'items.options.translations'
                ]);
            }

            /*
    |--------------------------------------------------------------------------
    | CURRENT DRAFT (FOR HISTORY PANEL)
    |--------------------------------------------------------------------------
    */
            $currentDraft = $offer->versions()
                ->where('status', 'draft')
                ->orderByDesc('version_number')
                ->first();

            /*
    |--------------------------------------------------------------------------
    | READONLY LOGIC (SAFE)
    |--------------------------------------------------------------------------
    */
            $isReadonly = $offerVersion
                ? $offerVersion->status !== 'draft'
                : true;

            $customRequirementIds = $rfq->customAttributeValues
                ->pluck('attribute_id')
                ->unique();


            $versions = $offer->versions()
                ->orderByDesc('version_number')
                ->with(['items.options.translations'])
                ->get();



            $latestSubmittedVersion = $offer->versions()
                ->where('status', 'submitted')
                ->orderByDesc('version_number')
                ->first();

            $isLatestSubmitted = $offerVersion
                && $latestSubmittedVersion
                && $offerVersion->id === $latestSubmittedVersion->id;

            $hasDraft = $offer->versions()
                ->where('status', 'draft')
                ->exists();

            $canCreateRevision = $isLatestSubmitted && !$hasDraft;

            $isReadonly = $offerVersion->status !== 'draft';
        }










        $ownerType = $this->context->isPersonal()
            ? \App\Models\User::class
            : \App\Models\Supplier::class;

        $ownerId = $this->context->isPersonal()
            ? auth()->user()->id
            : $this->context->company()->id;



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






        $ownerType = $this->context->isPersonal()
            ? \App\Models\User::class
            : \App\Models\Supplier::class;

        $ownerId = $this->context->isPersonal()
            ? auth()->user()->id
            : $this->context->company()->id;

        $groups = AttributeGroup::where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->get();

        $counterOffers = collect();
        $counterItemsByAttribute = collect();


        if ($activeTab === 'offers') {

            $rfq->loadMissing([
                'offers.participant',
                'offers.latestVersion',
                'offers.versions.items.options',
            ]);

            $offers = $rfq->offers;

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

                $versions = $offer->versions()
                    ->orderByDesc('version_number')
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



            if ($counterVersion) {
                $counterVersion->loadMissing('items.options');

                $counterItemsByAttribute = $counterVersion
                    ->items
                    ->keyBy('attribute_id');
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
                    ->where('created_by', auth()->id())
                    ->orderByDesc('version_number')
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
                            'counterVersion' => $counterVersion,
                            'counterItemsByAttribute' => $counterItemsByAttribute ?? collect(),

                        ]
                    );
                }
            }
        }







        /*
        |--------------------------------------------------------------------------
        | LOAD REQUIREMENTS ONLY WHEN NEEDED
        |--------------------------------------------------------------------------
        */

        if ($activeTab === 'overview') {

            $rfq->loadMissing([
                'attributeValues.attribute.options.translations',
                'attributeValues.options',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->limit(50)
            ->get();

        $allCategories = Category::query()
            ->where('is_selectable', 1)
            ->where('is_leaf', 1)
            ->orderBy('name')
            ->get();


        $participants = $rfq->participants()
            ->active()
            ->with('participant')
            ->latest('invited_at')
            ->get();

        $selectedCategoryIds = $rfq->visibilityCategories
            ->pluck('id')
            ->toArray();





        return view('rfq.workspace', [

            'rfq' => $rfq,
            'activeTab' => $activeTab,

            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'attributes' => $attributes,
            'suppliers' => $suppliers,
            'allCategories' => $allCategories,
            'participants' => $participants,
            'selectedCategoryIds' => $selectedCategoryIds,
            'offerVersion' => $offerVersion,
            'isReadonly' => $isReadonly,
            'currentDraft' => $currentDraft,
            'versions' => $versions,
            'canCreateRevision' => $canCreateRevision,
            'offer' => $offer,
            'offers' => $offers,
            'counterOffers' => $counterOffers,
            'counterVersion' => $counterVersion,

            'counterItemsByAttribute' => $counterItemsByAttribute,

            'itemsByAttribute' => $offerVersion?->items
                ?->whereNotNull('attribute_id')
                ?->keyBy('attribute_id') ?? collect(),
            'customRequirementIds' => $customRequirementIds ?? collect(),

            'buyerSnapshotMap' => $buyerSnapshotMap,
            'availableAttributes' => $availableAttributes,
            'availableAttributesGrouped' => $availableAttributesGrouped,
            'attachedAttributes' => $attachedAttributes,

            'groups' => $groups,


            'context_mode' => $this->context->mode(),
            'context_role' => $this->context->role(),

            'isBuyer' => $this->isBuyer($rfq),
            'isSupplier' => $this->isSupplierParticipant($rfq),
        ]);
    }

    private function isBuyer(Rfq $rfq): bool
    {
        if ($this->context->isGuest()) {
            return false;
        }

        if ($this->context->isPersonal()) {
            return $rfq->created_by === $this->context->user()->id;
        }

        return $this->context->isCompany()
            && $rfq->company_id === $this->context->id();
    }

    private function isSupplierParticipant(Rfq $rfq): bool
    {
        if (!$this->context->isCompany()) {
            return false;
        }

        return $rfq->participants
            ->contains('supplier_id', $this->context->id());
    }
}
