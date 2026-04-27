<?php

namespace App\Domain\RFQ\Actions\Supplier;


use App\Domain\Negotiation\Services\OfferDecisionService;

use App\Domain\Negotiation\Models\RfqOffer;

class RejectOfferAction
{
    public function __construct(
        private OfferDecisionService $service
    ) {}

    public function execute(RfqOffer $offer, int $userId): void
    {
        $this->service->reject($offer, $userId);
    }
}