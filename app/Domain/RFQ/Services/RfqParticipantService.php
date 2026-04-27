<?php

namespace App\Domain\RFQ\Services;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqParticipant;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Supplier;

class RfqParticipantService
{
    /**
     * CORE DOMAIN ACTION (POLYMORPHIC INVITE)
     */
    public function invite(Rfq $rfq, Model $participant): RfqParticipant
    {

    


        return RfqParticipant::updateOrCreate(
            [
                'rfq_id' => $rfq->id,
                'participant_type' => $participant::class,
                'participant_id' => $participant->id,
            ],
            [
                'status' => RfqParticipantStatus::INVITED,
                'invited_at' => Carbon::now(),
            ]
        );
    }

    /**
     * INVITE BY SUPPLIER IDS LIST (CATEGORY FLOW)
     */
    public function inviteSuppliers(Rfq $rfq, iterable $supplierIds): void
    {
        $suppliers = Supplier::whereIn('id', $supplierIds)->get();

        foreach ($suppliers as $supplier) {
            $this->invite($rfq, $supplier);
        }
    }

    

    /**
     * EMAIL INVITE (EXTERNAL PARTICIPANT)
     */
    public function inviteByEmail(Rfq $rfq, string $email): void
    {
        \Mail::raw(
            "You were invited to RFQ #{$rfq->id}",
            fn ($mail) => $mail->to($email)->subject('RFQ Invitation')
        );
    }

    /**
     * REMOVE PARTICIPANT (SOFT DOMAIN REMOVE)
     */
    public function remove(RfqParticipant $participant): void
    {
        if ($participant->status === RfqParticipantStatus::REMOVED) {
            return;
        }

        $participant->update([
            'status' => RfqParticipantStatus::REMOVED,
        ]);
    }
}