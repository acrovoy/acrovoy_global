<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Enums\RfqStatus;
use App\Models\Buyer;

class ListBuyerRfqsAction
{
    public function execute(Buyer $buyer): array
    {
        $query = Rfq::query()
            ->with(['category'])
            ->where('buyer_type', $buyer::class)
            ->where('buyer_id', $buyer->getKey())
            ->whereNull('project_id');

        return [
            'active' => (clone $query)
                ->where('status', '!=', RfqStatus::CLOSED)
                ->latest()
                ->paginate(10, ['*'], 'active_page'),

            'closed' => (clone $query)
                ->where('status', RfqStatus::CLOSED)
                ->latest()
                ->paginate(10, ['*'], 'closed_page'),
        ];
    }
}