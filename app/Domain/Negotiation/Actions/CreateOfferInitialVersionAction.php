<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferVersion;

class CreateOfferInitialVersionAction
{
    public function execute(
        RfqOffer $offer,
        $supplier,
        $context
    ): RfqOfferVersion {

        /**
         * =========================
         * SAFETY CHECK
         * =========================
         */
        $existing = $offer->versions()
            ->where('is_counter', 0)
            ->first();

        if ($existing) {
            return $existing;
        }

        /**
         * =========================
         * CREATE FIRST VERSION
         * =========================
         */
        $version = $offer->versions()->create([
            'version_number' => 1,
            'status' => 'draft',
            'is_counter' => 0,

            'owner_type' => get_class($supplier),
            'owner_id' => $supplier->id,

            'created_by' => $context->user()->id,

            'comment' => null,
            'total_price' => 0,
        ]);

        /**
         * =========================
         * INIT EMPTY ITEMS STRUCTURE (optional but safe)
         * =========================
         */
        // intentionally empty - items are created via autosave

        return $version;
    }
}