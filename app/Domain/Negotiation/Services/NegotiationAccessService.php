<?php

namespace App\Domain\Negotiation\Services;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Services\Company\ActiveContextService;

class NegotiationAccessService
{
    public function __construct(
        private ActiveContextService $context
    ) {}

    /**
     * CAN VIEW OFFER
     */
    public function canViewOffer(RfqOffer $offer, int $userId): bool
    {
        /**
         * BUYER (RFQ owner)
         */
        if ($offer->rfq->buyer_id === $userId) {
            return true;
        }

        /**
         * SUPPLIER CONTEXT
         */
        if ($this->context->isCompany()) {

            $supplier = $this->context->company();

            if (!$supplier) {
                return false;
            }

            return $supplier->id === $offer->supplier_id;
        }

        return false;
    }

    /**
     * CAN EDIT OFFER
     */
    public function canEditOffer(RfqOffer $offer, int $userId): bool
    {
        if (!$this->context->isCompany()) {
            return false;
        }

        $supplier = $this->context->company();

        return $supplier && $supplier->id === $offer->supplier_id;
    }

    /**
     * CAN MANAGE OFFER (future extensions)
     */
    public function canManageOffer(RfqOffer $offer, int $userId): bool
    {
        return $this->canEditOffer($offer, $userId);
    }
}