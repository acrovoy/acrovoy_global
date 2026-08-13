<?php

namespace App\Domain\RFQ\Services;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\Models\RfqParticipant;
use App\Services\Company\ActiveContextService;
use App\Domain\RFQ\Enums\RfqVisibilityType;

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
    // 1. buyer company owner
    if ($this->isBuyerCompanyOwner($rfq)) {
        return true;
    }

    // 2. public
    if ($rfq->visibility_type === 'platform') {
        return true;
    }

    // 3. supplier participant
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


    $supplier = $this->context->supplierProfile();

    
    if (!$supplier) {
        return Rfq::query()->whereRaw('1 = 0')->get();
    }

    $supplierId = $supplier->getKey();
    $supplierType = $supplier::class;

    return Rfq::query()
        ->where('status', RfqStatus::PUBLISHED)
        ->where(function ($q) use ($supplierId, $supplierType) {

            $q->whereIn('visibility_type', [
                RfqVisibilityType::OPEN,
                RfqVisibilityType::PLATFORM,
            ])

            ->orWhereHas('participants', function ($q) use (
                $supplierId,
                $supplierType
            ) {
                $q->where('participant_type', $supplierType)
                    ->where('participant_id', $supplierId)
                    ->whereIn('status', [
                        'invited',
                        'accepted',
                    ]);
            });
        })
        ->latest()
        ->get();
}

/**
 * CLOSED RFQs FOR SUPPLIER
 */
public function getClosedRfqsForSupplier()
{
    $supplier = $this->context->supplierProfile();

    if (!$supplier) {
        return Rfq::query()->whereRaw('1 = 0')->get();
    }

    $supplierId = $supplier->getKey();
    $supplierType = $supplier::class;

    return Rfq::query()
        ->where('status', RfqStatus::CLOSED)
        ->where(function ($q) use ($supplierId, $supplierType) {

            $q->whereIn('visibility_type', [
                RfqVisibilityType::OPEN,
                RfqVisibilityType::PLATFORM,
            ])

            ->orWhereHas('participants', function ($q) use (
                $supplierId,
                $supplierType
            ) {
                $q->where('participant_type', $supplierType)
                    ->where('participant_id', $supplierId)
                    ->whereIn('status', [
                        'invited',
                        'accepted',
                    ]);
            });
        })
        ->latest()
        ->get();
}


    /**
     * CHECK PARTICIPATION
     */
    private function isSupplierParticipant(Rfq $rfq): bool
{
    $supplier = $this->context->supplierProfile();

    if (!$supplier) {
        return false;
    }

    return RfqParticipant::query()
        ->where('rfq_id', $rfq->id)
        ->where('participant_type', $supplier::class)
        ->where('participant_id', $supplier->getKey())
        ->whereIn('status', [
            'invited',
            'accepted',
        ])
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