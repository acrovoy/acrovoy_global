<?php

namespace App\Domain\Negotiation\Policies;

use App\Domain\RFQ\Models\RfqOfferParticipant;
use App\Models\User;

class RfqOfferParticipantPolicy
{
    /**
     * VIEW PARTICIPANT
     */
    public function view(User $user, RfqOfferParticipant $participant): bool
    {
        return $participant->user_id === $user->id;
    }
}