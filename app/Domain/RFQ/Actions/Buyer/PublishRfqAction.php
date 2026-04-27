<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\Models\Rfq;
use Carbon\Carbon;

use App\Domain\Negotiation\Services\NegotiationAuditService;

class PublishRfqAction
{

public function __construct(
        private NegotiationAuditService $audit
    ) {}


    public function execute(Rfq $rfq,
        int $userId): Rfq
    {
        abort_unless($rfq->status === RfqStatus::DRAFT, 403);

        $rfq->update([
            'status' => RfqStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ]);


         $this->audit->log('rfq.published', $rfq, $rfq->id, $userId);




        return $rfq;
    }
}