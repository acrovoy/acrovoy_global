<?php


namespace App\Domain\RFQ\Actions\Supplier;

use App\Domain\RFQ\Models\RfqParticipant;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use Carbon\Carbon;

class DeclineInvitationAction
{
    public function execute(RfqParticipant $participant): RfqParticipant
    {
        $participant->update([
            'status' => RfqParticipantStatus::DECLINED,
            'responded_at' => Carbon::now(),
        ]);

        event(new \App\Domain\RFQ\Events\SupplierDeclinedInvitation($participant));

        return $participant;
    }
}