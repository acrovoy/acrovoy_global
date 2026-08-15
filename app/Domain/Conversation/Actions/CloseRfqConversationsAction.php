<?php

namespace App\Domain\Conversation\Actions;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\RFQ\Models\Rfq;
use App\Domain\Conversation\Models\Conversation;

class CloseRfqConversationsAction
{
    public function execute(Rfq $rfq): void
    {
        $offerIds = $rfq->offers()
            ->pluck('id');

        if ($offerIds->isEmpty()) {
            return;
        }

        Conversation::query()
            ->where('subject_type', RfqOffer::class)
            ->whereIn('subject_id', $offerIds)
            ->where('status', '!=', 'closed')
            ->update([
                'status' => 'closed',
            ]);
    }
}