<?php

namespace App\Domain\RFQ\Actions\Supplier;

use App\Domain\RFQ\Enums\RfqParticipantStatus;
use App\Domain\RFQ\Models\RfqParticipant;
use Carbon\Carbon;

class MarkRfqViewedAction
{
    public function execute(RfqParticipant $participant): RfqParticipant
    {
        if ($participant->status === RfqParticipantStatus::INVITED) {
            $participant->update([
                'status' => RfqParticipantStatus::VIEWED,
                'responded_at' => Carbon::now(),
            ]);
        }

        event(new \App\Domain\RFQ\Events\RfqViewed($participant));

        return $participant;
    }
}