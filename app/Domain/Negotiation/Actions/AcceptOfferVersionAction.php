<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOfferVersion;
use App\Domain\Negotiation\Services\OfferVersionAcceptService;

use App\Domain\Negotiation\Services\NegotiationAuditService;

class AcceptOfferVersionAction
{
    public function __construct(
        private OfferVersionAcceptService $service,
        private NegotiationAuditService $audit
    ) {}

    public function execute(RfqOfferVersion $version, int $userId): void
    {
        $this->service->accept($version);

        $this->audit->log(
            'version.accepted',
            $version,
            $version->offer->rfq_id,
            $userId,
            [
                'price' => $version->total_price
            ]
        );
    }
}