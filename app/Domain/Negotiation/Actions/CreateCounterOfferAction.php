<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Resolvers\OfferVersionResolver;

class CreateCounterOfferAction
{
    public function __construct(
        private OfferVersionResolver $resolver
    ) {}

    public function execute(RfqOffer $offer, int $userId, $context, bool $create = false): array
    {




        /*
        |---------------       
        -----------------------------------------------------------
        | STRICT RULE: ONLY SUBMITTED SUPPLIER VERSION
        |--------------------------------------------------------------------------
        */

        $supplierVersion = $this->resolver
            ->lastSupplierSubmittedVersion($offer);

        $supplierOfferVersion = $this->resolver
            ->lastSupplierSubmittedVersion($offer);








        if (!$supplierVersion) {
            abort(404, 'No submitted supplier version found.');
        }

        /*
        |--------------------------------------------------------------------------
        | COUNTER VERSIONS (by actor)
        |--------------------------------------------------------------------------
        */

        $draftCounter = $this->resolver->latestCounterVersion(
            $offer,
            $userId,
            'draft',
            $context
        );

        $submittedCounter = $this->resolver->latestCounterVersion(
            $offer,
            $userId,
            'submitted',
            $context
        );

        /*
        |--------------------------------------------------------------------------
        | CASE 1: EDIT EXISTING DRAFT
        |--------------------------------------------------------------------------
        */

        if ($draftCounter) {

        
            return [
                'mode' => 'draft',
                'offerVersion' => $supplierVersion,
                'supplierOfferVersion' => $supplierOfferVersion,
                'counterVersion' => $draftCounter,
                'itemsByAttribute' => $supplierVersion->items->keyBy('attribute_id'),
                'counterItemsByAttribute' => $draftCounter->items->load('options')->keyBy('attribute_id'),
                'versions' => $this->getVersionsForBuyerDraftMode($offer),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | CASE 2: READ ONLY SUBMITTED COUNTER
        |--------------------------------------------------------------------------
        */

        if ($submittedCounter && $create == false) {


        return [
        'mode' => 'readonly',
        'offerVersion' => $supplierVersion,
        'counterVersion' => $submittedCounter,
        'itemsByAttribute' => $supplierVersion->items->keyBy('attribute_id'),
        'counterItemsByAttribute' => $submittedCounter->items->load('options')->keyBy('attribute_id'),
        'versions' => $this->getVersions($offer),
        'isReadonly' => true,
        ];
        }

        /*
        |--------------------------------------------------------------------------
        | CASE 3: CREATE NEW COUNTER VERSION
        |--------------------------------------------------------------------------
        */

        $newVersion = $this->createCounterVersion(
            $offer,
            $supplierVersion,
            $context
        );

        return [
            'mode' => 'created',
            'offerVersion' => $supplierVersion,
            'counterVersion' => $newVersion,
            'itemsByAttribute' => $supplierVersion->items->load('options')->keyBy('attribute_id'),
            'counterItemsByAttribute' => $newVersion->items->load('options')->keyBy('attribute_id'),
            'versions' => $this->getVersions($offer),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ONLY NON-DRAFT VERSIONS FOR VIEW
    |--------------------------------------------------------------------------
    */

    private function getVersions(RfqOffer $offer)
    {
        return $offer->versions()
            ->where('status', '!=', 'draft')
            ->orderByDesc('created_at')
            ->get();
    }

    private function getAllVersions(RfqOffer $offer)
    {
        return $offer->versions()
          ->orderByDesc('created_at')
            ->get();
    }


    private function getVersionsForBuyerDraftMode(RfqOffer $offer)
{
    return $offer->versions()
        ->where(function ($q) {

            $q->where(function ($q) {
                // supplier NON-draft versions
                $q->where('is_counter', 0)
                  ->where('status', '!=', 'draft');
            })

            ->orWhere(function ($q) {
                
                $q->where('is_counter', 1);
            });

        })
        ->orderByDesc('created_at')
        ->with(['items.options'])
        ->get();
}

    /*
    |--------------------------------------------------------------------------
    | CREATE COUNTER VERSION
    |--------------------------------------------------------------------------
    */

    private function createCounterVersion(
        RfqOffer $offer,
        $supplierVersion,
        $context
    ) {

   
        $newVersion = $offer->versions()->create([
            'version_number' => null,
            'status' => 'draft',
            'is_counter' => 1,

            /*
            |--------------------------------------------------------------------------
            | IDENTITY (AUDIT)
            |--------------------------------------------------------------------------
            */
            'created_by' => $context->user()->id,

            /*
            |--------------------------------------------------------------------------
            | ACTOR CONTEXT (USER OR COMPANY)
            |--------------------------------------------------------------------------
            */
            'owner_type' => $context->isPersonal() ? 'App\Models\User' : 'App\Models\Buyer',
            'owner_id' => $context->isPersonal()
                ? $context->user()->id
                : $context->company()->id,

            'total_price' => $supplierVersion->total_price,
        ]);

        foreach ($supplierVersion->items as $item) {
            $newItem = $newVersion->items()->create([
                'attribute_id' => $item->attribute_id,
                'unit_price' => null,
                'quantity' => $item->quantity,
                'currency' => $item->currency,
                'lead_time_days' => $item->lead_time_days,
                'moq' => $item->moq,
                'notes' => '',
            ]);

            if ($item->options->count()) {
                $newItem->options()->sync($item->options->pluck('id'));
            }
        }

        return $newVersion;
    }
}