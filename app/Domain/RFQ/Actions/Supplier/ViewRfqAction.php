<?php

namespace App\Domain\RFQ\Actions\Supplier;

use App\Domain\RFQ\Events\RfqViewed;
use App\Domain\RFQ\Models\Rfq;

class ViewRfqAction
{
    public function execute(Rfq $rfq, int $userId): Rfq
    {
        event(new RfqViewed($rfq, $userId));

        return $rfq;
    }
}