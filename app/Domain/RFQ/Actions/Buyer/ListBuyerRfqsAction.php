<?php

namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Models\Rfq;
use App\Services\Company\ActiveContextService;

class ListBuyerRfqsAction
{
    public function execute(ActiveContextService $context)
    {
        /**
         * PERSONAL MODE
         */

        if ($context->isPersonal()) {

            return Rfq::query()
                ->where('buyer_type', auth()->user()::class)
                ->where('buyer_id', auth()->id())
                ->latest()
                ->paginate(20);
        }

        /**
         * COMPANY MODE
         */

        return Rfq::query()
            ->where('buyer_type', $context->type())
            ->where('buyer_id', $context->id())
            ->latest()
            ->paginate(20);
    }
}