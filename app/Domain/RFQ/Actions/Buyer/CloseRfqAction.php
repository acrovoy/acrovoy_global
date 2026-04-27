<?php


namespace App\Domain\RFQ\Actions\Buyer;

use App\Domain\RFQ\Enums\RfqStatus;
use App\Domain\RFQ\Models\Rfq;
use Carbon\Carbon;

class CloseRfqAction
{
    public function execute(Rfq $rfq): Rfq
    {
        abort_if($rfq->status === RfqStatus::CLOSED, 403);

        abort_unless(
            in_array($rfq->status, [
                RfqStatus::PUBLISHED,
                RfqStatus::IN_NEGOTIATION,
            ]),
            403
        );

        $rfq->update([
            'status' => RfqStatus::CLOSED,
            'closed_at' => Carbon::now(),
        ]);

        return $rfq;
    }
}