<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\RFQ\Models\Rfq;
use App\Models\Supplier;
use App\Services\Company\ActiveContextService;

class CreateRfqOfferAction
{
    public function execute(
        Rfq $rfq,
        Supplier $supplier,
        ActiveContextService $context
    ): RfqOffer {
        /**
         * =========================
         * FIND OR CREATE OFFER
         * =========================
         */
        $offer = RfqOffer::firstOrCreate([
            'rfq_id' => $rfq->id,
            'participant_type' => Supplier::class,
            'participant_id' => $supplier->id,
        ]);

        /**
         * =========================
         * ENSURE BASIC STATE EXISTS
         * =========================
         */
        if ($offer->versions()->count() === 0) {

            $offer->versions()->create([
                'version_number' => null,
                'status' => 'draft',

                'owner_type' => Supplier::class,
                'owner_id' => $supplier->id,

                'created_by' => $context->user()->id,
            ]);
        }

        return $offer;
    }
}