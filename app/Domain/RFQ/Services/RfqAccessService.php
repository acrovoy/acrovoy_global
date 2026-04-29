<?php

namespace App\Domain\RFQ\Services;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqParticipant;
use App\Services\Company\ActiveContextService;
use App\Models\Supplier;

class RfqAccessService
{
    public function __construct(
        private ActiveContextService $context
    ) {}

    /**
     * VIEW ACCESS (WORKSPACE)
     */
    public function canViewRfq(Rfq $rfq, int $userId): bool
{
    $context = $this->context;

    // 1. owner
    if ($rfq->created_by === $userId) {
        return true;
    }

    // 2. buyer company owner
    if ($this->isBuyerCompanyOwner($rfq)) {
        return true;
    }

    // 3. public
    if ($rfq->visibility_type === 'public') {
        return true;
    }

    // 4. supplier participant
    if ($this->isSupplierParticipant($rfq)) {
        return true;
    }

    return false;
}

    /**
     * SUPPLIER FEED
     */
   public function getAvailableRfqsForSupplier()
{
    if (!$this->context->isCompany()) {
        return collect();
    }

    $supplierId = $this->context->supplierId();

    if (!$supplierId) {
        return collect();
    }

    return Rfq::query()
        ->where('visibility_type', 'public')
        ->orWhereHas('participants', function ($q) use ($supplierId) {

            $q->where('participant_type', Supplier::class)
              ->where('participant_id', $supplierId)
              ->whereIn('status', ['invited', 'accepted']);
        })
        ->latest()
        ->get();
}
    /**
     * CHECK PARTICIPATION
     */
    private function isSupplierParticipant(Rfq $rfq): bool
{
    $supplierId = $this->context->supplierId();

    if (!$supplierId) {
        return false;
    }

    return RfqParticipant::query()
        ->where('rfq_id', $rfq->id)
        ->where('participant_type', Supplier::class)
        ->where('participant_id', $supplierId)
        ->whereIn('status', ['invited', 'accepted'])
        ->exists();
}

    /**
     * BUYER OWNER CHECK
     */
    private function isBuyerCompanyOwner(Rfq $rfq): bool
{
    if (!$this->context->isCompany()) {
        return false;
    }

    return $rfq->buyer_id === $this->context->id()
        && $rfq->buyer_type === $this->context->type();
}
}