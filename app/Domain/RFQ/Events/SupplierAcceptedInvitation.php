<?php

namespace App\Domain\RFQ\Events;

use App\Domain\RFQ\Models\RfqParticipant;

class SupplierAcceptedInvitation
{
    public function __construct(
        public RfqParticipant $participant
    ) {}
}