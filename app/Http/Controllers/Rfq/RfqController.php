<?php

namespace App\Http\Controllers\Rfq;

use App\Http\Controllers\Controller;
use App\Domain\RFQ\Models\Rfq;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use App\Models\Supplier;
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
                'attributeValues.attribute.options.translations',
                'attributeValues.options',
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

        /*
        |--------------------------------------------------------------------------
        | LOAD OFFERS ONLY WHEN NEEDED
        |--------------------------------------------------------------------------
        */

        $offerVersion = null;

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

            /**
             * =========================
             * GET OR CREATE DRAFT VERSION
             * =========================
             */
            $offerVersion = $offer->versions()
                ->where('status', 'draft')
                ->orderByDesc('version_number')
                ->first();

            if (!$offerVersion) {

                $offerVersion = $offer->versions()->create([
                    'version_number' => ($offer->versions()->max('version_number') ?? 0) + 1,
                    'status' => 'draft',
                    'created_by' => $this->context->user()->id,
                ]);

                // важно: сразу загрузить отношения для нового
                $offerVersion->load(['items.options.translations']);
            } else {

                // важно: reload чтобы не было stale data
                $offerVersion->load(['items.options.translations']);
            }








            /**
             * =========================
             * BUILD MAPS (ВАЖНО ДЛЯ BLADE)
             * =========================
             */
            $itemsByRequirement = $offerVersion->items
                ->whereNotNull('requirement_id')
                ->keyBy('requirement_id');

            $itemsByAttribute = $offerVersion->items
                ->whereNotNull('attribute_id')
                ->keyBy('attribute_id');
        }


        if ($activeTab === 'offers') {

            $rfq->loadMissing([
                'offers.supplier',
                'offers.latestVersion',
            ]);
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

            // 🔥 ВАЖНО: ДОБАВИТЬ MAPS
            'itemsByRequirement' => $offerVersion?->items
                ?->whereNotNull('requirement_id')
                ?->keyBy('requirement_id') ?? collect(),

            'itemsByAttribute' => $offerVersion?->items
                ?->whereNotNull('attribute_id')
                ?->keyBy('attribute_id') ?? collect(),

            'buyerSnapshotMap' => $buyerSnapshotMap,


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
