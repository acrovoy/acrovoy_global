<?php

namespace App\Http\Controllers\Rfq;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\UserAddress;
use App\Models\Country;
use App\Models\ShippingTemplate;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Models\Supplier;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Domain\RFQ\Models\RfqAttributeValue;
use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Resolvers\OfferVersionResolver;
use App\Domain\RFQ\Actions\GetRfqCategoriesAction;
use App\Domain\RFQ\Services\RfqRequirementsLoader;

use App\Facades\ActiveContext;



class RfqController extends Controller
{
    public function __construct(
        private ActiveContextService $context,
        private OfferVersionResolver $resolver
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
            'offers' => function ($q) {
        $q->whereHas('latestVersion', function ($version) {
            $version->where('status', '!=', 'draft');
        });
    },
            'participants.participant',
            'visibilityCategories',
            'deliveryAddress.regionLocation',
            'deliveryAddress.country',
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
        }

        $canCreateRevision = false;
        $versions = collect();
        $currentDraft = null;
        $offerVersion = null;
        $offer = null;
        $offers = null;
        $counterVersion = null;
        $isReadonly = true;
        $isCounter = false;
        $supplierOfferVersionToCounter = null;
        $shippingTemplates = collect();
        $requirementsCompleted = null;
        $participantsCompleted = null;
        $deliveryCompleted = null;
        $canPublish = null;

        if ($activeTab === 's-requirements') {

            $rfq->loadMissing([
                'attributeValues.attribute.options',
                'attributeValues.options',
                'hiddenAttributes',
            ]);

            $hiddenIds = $rfq->hiddenAttributes->pluck('id')->toArray();

            $rfq->setRelation(
                'attributeValues',
                $rfq->attributeValues->reject(function ($value) use ($hiddenIds) {
                    return in_array($value->attribute_id, $hiddenIds);
                })
            );


            $supplier = $this->context->supplier();

            if (!$supplier) {
                abort(403);
            }

            $offer = app(\App\Domain\Negotiation\Actions\CreateRfqOfferAction::class)
                ->execute(
                    rfq: $rfq,
                    supplier: $supplier,
                    context: $this->context
                );

            $resolver = app(OfferVersionResolver::class);

            $offerVersion = $resolver->resolve(
                $offer,
                request('version')
            );

            if ($offerVersion->is_counter == 1) {
                $versionNumberOfCounter = $offerVersion->version_number;
                $supplierOfferVersionToCounter = $offer->versions()
                    ->where('status', '!=', 'draft')
                    ->where('is_counter', 0)
                    ->where('version_number', $versionNumberOfCounter - 1)
                    ->first();
            }

            $currentDraft = $resolver->currentDraft($offer);

            $canCreateRevision = $resolver->canCreateRevision($offer, $offerVersion);

            $versions = $offer->versions()
                ->orderByDesc('created_at')
                ->with(['items.options.translations'])
                ->get();




            $isReadonly = $offerVersion
                ? $offerVersion->status !== 'draft'
                : true;


            $isCounter = $offerVersion?->is_counter ?? false;
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
        $lastsubmittedVersion = collect();
        $existingDraftCounter = null;
        $supplierOfferVersion = null;



        if ($activeTab === 'offers') {



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

            $addressa = UserAddress::find($rfq->delivery_address_id);

            if (!$addressa) {
                $cityId = null;
            } else {

                $eexistingLocation = \App\Models\Location::where('name', $addressa->city)
                    ->where('parent_id', $addressa->region)
                    ->first();

                $cityId = $eexistingLocation?->id;
            }

            $shippingTemplates = ShippingTemplate::with('translations', 'locations')
                ->where('provider_type', $ownerType)
                ->where('provider_id', $ownerId)
                ->where('is_active', true)
                ->whereHas('locations', function ($q) use ($cityId) {
                    $q->where('locations.id', $cityId);
                })
                ->get();


            // Флаги заполененности
            $requirementsCompleted = $rfq->attributeValues()->exists();

            $participantsCompleted = $rfq->participants()
                ->whereIn('status', ['invited', 'accepted'])
                ->exists();

            $deliveryCompleted = !empty($rfq->delivery_address_id);

            $canPublish =
                $requirementsCompleted &&
                $participantsCompleted &&
                $deliveryCompleted;
            // Флаги заполененности




        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */






        $countries = Country::withCurrentTranslation()
            ->orderBy('name')->get();


        $buyerType = $this->context->type();
        $buyerId = $this->context->id();


        $savedAddresses = UserAddress::query()
            ->where('user_id', $buyerId)
            ->where('user_type', $buyerType)
            ->orderByDesc('updated_at')->get();

        // Опционально: последний использованный адрес
        $lastAddress = $savedAddresses->first();

        $currentSavedAddress = UserAddress::query()
            ->where('id', $rfq->delivery_address_id)
            ->first();

        $currentAddressId = $rfq->delivery_address_id;

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
            'savedAddresses' => $savedAddresses,
            'lastAddress' => $lastAddress,
            'currentSavedAddress' => $currentSavedAddress,
            'currentAddressId' => $currentAddressId,
            'countries' => $countries,
            'shippingTemplates' => $shippingTemplates,
            'selectedCategoryIds' => $selectedCategoryIds,
            'offerVersion' => $offerVersion,
            'isReadonly' => $isReadonly,
            'isCounter' => $isCounter,
            'currentDraft' => $currentDraft,
            'versions' => $versions,
            'canCreateRevision' => $canCreateRevision,
            'offer' => $offer,
            'offers' => $offers,
            'counterOffers' => $counterOffers,
            'counterVersion' => $counterVersion,
            'lastsubmittedVersion' => $lastsubmittedVersion,
            'existingDraftCounter' => $existingDraftCounter,
            'supplierOfferVersion' => $supplierOfferVersion,
            'supplierOfferVersionToCounter' => $supplierOfferVersionToCounter,
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
            // Флаги заполененности
            'requirementsCompleted' => $requirementsCompleted,
            'participantsCompleted' => $participantsCompleted,
            'deliveryCompleted' => $deliveryCompleted,
            'canPublish' => $canPublish,
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



    public function attachAddress(Request $request, Rfq $rfq)
    {
        $request->validate([
            'saved_address_id' => 'nullable|exists:user_addresses,id',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'country' => 'nullable',
            'region' => 'nullable',
            'city' => 'nullable',
            'street' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'phone' => 'nullable|string',
            'save_as_new' => 'nullable|boolean',
        ]);



        $addressId = $request->saved_address_id;

        // 1. ЕСЛИ ВЫБРАН СУЩЕСТВУЮЩИЙ
        if ($addressId) {
            $address = UserAddress::where('id', $addressId)
                ->where('user_id', ActiveContext::id())
                ->where('user_type', ActiveContext::type())
                ->firstOrFail();
        }

        // 2. ЕСЛИ НОВЫЙ АДРЕС
        else {


            $finalCity = $request->city_manual ?: null;
            $cityId = null;

            // 1️⃣ Если пользователь ввёл новый город вручную
            if ($request->filled('city_manual')) {
                $existingLocation = \App\Models\Location::where('name', $finalCity)
                    ->where('parent_id', $request->region)
                    ->first();

                if ($existingLocation) {
                    $cityId = $existingLocation->id;
                } else {
                    $newLocation = \App\Models\Location::create([
                        'name'       => $finalCity,
                        'parent_id'  => $request->region ?: null,
                        'country_id' => $request->country,
                        'updated_by' => auth()->user()->id,
                    ]);
                    $cityId = $newLocation->id;
                }
            }
            // 2️⃣ Если город выбран из списка
            elseif ($request->filled('city')) {
                // Здесь важно, чтобы в форме приходил ID выбранного города, а не название
                $cityId = (int) $request->city;
            }

            $cityModel = \App\Models\Location::find($cityId);
            $finalCity = $cityModel?->name ?? '';


            $address = UserAddress::create([
                'user_id' => ActiveContext::id(),
                'user_type' => ActiveContext::type(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'country' => $request->country,
                'region' => $request->region,
                'city' => $finalCity,
                'street' => $request->street,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
            ]);

            // если пользователь хочет сохранить
            if ($request->save_as_new) {
                // уже сохранён автоматически
            }
        }

        $rfq->update([
            'delivery_address_id' => $address->id,
        ]);

        return back()->with('success', 'Address updated');
    }

    public function publish(Rfq $rfq)
    {
        // безопасность: публикуем только draft
        if (!$rfq->status->canPublish()) {
            return back()->with('error', 'RFQ cannot be published.');
        }

        // можно добавить финальную проверку readiness
        $completed =
            $rfq->attributeValues()->exists() &&
            $rfq->participants()->exists() &&
            !empty($rfq->delivery_address_id);

        if (!$completed) {
            return back()->with('error', 'RFQ is not complete.');
        }

        $rfq->update([
            'status' => RfqStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        return back()->with('success', 'RFQ published successfully.');
    }


    public function close(Rfq $rfq)
    {
        // можно закрывать только опубликованные / в переговорах
        if (!$rfq->status->canClose()) {
            return back()->with('error', 'RFQ cannot be closed in current status.');
        }

        $rfq->update([
            'status' => RfqStatus::CLOSED,
            'closed_at' => now(),
        ]);

        return back()->with('success', 'RFQ closed successfully.');
    }
}
