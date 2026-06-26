<?php

namespace App\Domain\RFQ\Policies;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqParticipant;
use App\Models\User;

use App\Services\Company\ActiveContextService;

class RfqPolicy
{
    /**
     * VIEW RFQ
     */
    public function view(User $user, Rfq $rfq): bool
{
    $context = app(ActiveContextService::class);

    if ($context->isGuest()) {
        return false;
    }

    /**
     * =========================================
     * BUYER ACCESS
     * =========================================
     */
    if ($context->role() === 'buyer') {

        // personal buyer
        if ($context->isPersonal()) {
            if ($rfq->buyer_type === User::class) {
                return $rfq->buyer_id === $user->id;
            }

            return $rfq->created_by === $user->id;
        }

        // company buyer
        if ($context->isCompany()) {

            return $rfq->company_id === $context->id();
        }
    }

    /**
     * =========================================
     * SUPPLIER ACCESS (morph participants)
     * =========================================
     */
    if ($context->role() === 'supplier') {

        $supplier = $context->supplier();

        if (!$supplier) {
            return false;
        }

        return RfqParticipant::query()
            ->where('rfq_id', $rfq->id)
            ->where('participant_type', \App\Models\Supplier::class)
            ->where('participant_id', $supplier->id)
            ->whereIn('status', ['invited', 'accepted'])
            ->exists();
    }

    return false;
}

    /**
     * UPDATE RFQ
     */
    public function update(User $user, Rfq $rfq): bool
{
    // только владелец RFQ может менять настройки
    return $rfq->created_by === $user->id;
}

    /**
     * PUBLISH RFQ
     */
    public function publish(User $user, Rfq $rfq): bool
    {
        return $rfq->created_by === $user->id
            && $rfq->status === 'draft';
    }

    /**
     * DELETE RFQ
     */
    public function delete(User $user, Rfq $rfq): bool
    {
        return $rfq->created_by === $user->id
            && $rfq->status !== 'published';
    }
}