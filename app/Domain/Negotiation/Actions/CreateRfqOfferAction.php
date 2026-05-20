<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\RFQ\Models\Rfq;

class CreateRfqOfferAction
{
    public function execute(
        Rfq $rfq,
        $supplier,
        $context
    ): RfqOffer {
        /**
         * =========================
         * FIND OR CREATE OFFER
         * =========================
         */
        $offer = RfqOffer::firstOrCreate([
            'rfq_id' => $rfq->id,
            'participant_type' => get_class($supplier),
            'participant_id' => $supplier->id,
        ]);

        /**
         * =========================
         * ENSURE BASIC STATE EXISTS
         * =========================
         * (offer itself doesn't guarantee version existence)
         */

        if ($offer->versions()->count() === 0) {
            $offer->versions()->create([
                'version_number' => 1,
                'status' => 'draft',
                'owner_type' => get_class($supplier),
                'owner_id' => $supplier->id,
                'created_by' => $context->user()->id,
            ]);
        }

        return $offer;
    }
}