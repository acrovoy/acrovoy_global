<?php

namespace App\Domain\RFQ\Policies;

use App\Domain\RFQ\Models\RfqParticipant;
use App\Models\User;
use App\Services\Company\ActiveContextService;
use App\Domain\RFQ\Enums\RfqParticipantStatus;
use App\Models\Supplier;

class RfqParticipantPolicy
{
    public function view(
        User $user,
        RfqParticipant $participant,
        ActiveContextService $context
    ): bool {
        if (!$context->isCompany()) {
            return false;
        }

        $company = $context->company();

        if (!$company) {
            return false;
        }

        return $participant->participant_type === Supplier::class
            && $participant->participant_id === $company->id;
    }

    public function accept(
        User $user,
        RfqParticipant $participant,
        ActiveContextService $context
    ): bool {
        if (!$context->isCompany()) {
            return false;
        }

        $company = $context->company();

        if (!$company) {
            return false;
        }

        return $participant->participant_type === Supplier::class
            && $participant->participant_id === $company->id
            && $participant->status === RfqParticipantStatus::INVITED;
    }
}