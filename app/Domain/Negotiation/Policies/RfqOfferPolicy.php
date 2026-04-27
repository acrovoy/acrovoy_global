<?php

namespace App\Domain\Negotiation\Policies;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Services\Company\ActiveContextService;
use App\Models\User;

class RfqOfferPolicy
{
    /**
     * VIEW OFFER
     */
    public function view(User $user, RfqOffer $offer): bool
    {
        $context = app(ActiveContextService::class);

        /**
         * BUYER (owner RFQ)
         */
        if ($offer->rfq->buyer_id === $user->id) {
            return true;
        }

        /**
         * SUPPLIER (offer owner via context)
         */
        if ($context->isCompany()) {

            $supplier = $context->company();

            if ($supplier && $supplier->id === $offer->supplier_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * UPDATE OFFER
     */
    public function update(User $user, RfqOffer $offer): bool
    {
        $context = app(ActiveContextService::class);

        if (!$context->isCompany()) {
            return false;
        }

        $supplier = $context->company();

        return $supplier && $supplier->id === $offer->supplier_id;
    }
}