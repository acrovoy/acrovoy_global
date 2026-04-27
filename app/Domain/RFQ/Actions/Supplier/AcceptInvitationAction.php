<?php


namespace App\Domain\RFQ\Actions\Supplier;

use App\Domain\RFQ\Models\RfqParticipant;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use Carbon\Carbon;

class AcceptInvitationAction
{
    public function execute(RfqParticipant $participant): RfqParticipant
    {
        $participant->update([
            'status' => RfqParticipantStatus::ACCEPTED,
            'responded_at' => Carbon::now(),
        ]);

        event(new \App\Domain\RFQ\Events\SupplierAcceptedInvitation($participant));

        return $participant;
    }
}