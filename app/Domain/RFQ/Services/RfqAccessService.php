<?php

namespace App\Domain\RFQ\Services;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqParticipant;
use App\Services\Company\ActiveContextService;

class RfqAccessService
{
    /**
     * VIEW ACCESS (WORKSPACE)
     */
    public function canViewRfq(
        Rfq $rfq,
        array $context,
        int $userId
    ): bool
    {
        /**
         * 1. BUYER OWNER (personal or company)
         */
        if ($rfq->created_by === $userId) {
            return true;
        }

        if ($this->isBuyerCompanyOwner($rfq, $context)) {
            return true;
        }

        /**
         * 2. PUBLIC RFQ (future-ready)
         */
        if ($rfq->visibility_type === 'public') {
            return true;
        }

        /**
         * 3. SUPPLIER ACCESS VIA PARTICIPATION
         */
        if ($this->isSupplierParticipant($rfq, $context)) {
            return true;
        }

        return false;
    }

    /**
     * SUPPLIER LIST ACCESS (FEED)
     */
    public function getAvailableRfqsForSupplier(
        array $context,
        int $userId
    )
    {
        return Rfq::query()
            ->where(function ($q) use ($context, $userId) {

                $q->where('visibility_type', 'public')

                  ->orWhereHas('participants', function ($p) use ($context) {
                      $p->where('supplier_id', $context['company_id'] ?? null)
                        ->where('status', 'invited');
                  })

                  ->orWhereHas('participants', function ($p) use ($userId) {
                      $p->where('supplier_id', $userId);
                  });
            })
            ->latest()
            ->get();
    }

    /**
     * CHECK SUPPLIER PARTICIPATION
     */
    private function isSupplierParticipant(Rfq $rfq, array $context): bool
    {
        if (!isset($context['company_id'])) {
            return false;
        }

        return RfqParticipant::query()
            ->where('rfq_id', $rfq->id)
            ->where('supplier_id', $context['company_id'])
            ->whereIn('status', ['invited', 'accepted'])
            ->exists();
    }

    /**
     * BUYER COMPANY OWNERSHIP
     */
    private function isBuyerCompanyOwner(Rfq $rfq, array $context): bool
    {
        if (($context['mode'] ?? null) !== 'company') {
            return false;
        }

        return $rfq->buyer_id === ($context['company_id'] ?? null)
            && $rfq->buyer_type === ($context['company_type'] ?? null);
    }
}