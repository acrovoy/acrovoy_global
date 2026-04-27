<?php

namespace App\Domain\RFQ\Policies;

use App\Domain\RFQ\Models\Rfq;
use App\Domain\RFQ\Models\RfqParticipant;
use App\Models\User;

class RfqPolicy
{
    /**
     * VIEW RFQ
     */
    public function view(User $user, Rfq $rfq): bool
    {
        // OWNER (buyer)
        if ($rfq->created_by === $user->id) {
            return true;
        }

        // SUPPLIER ACCESS (via participant relation)
        return RfqParticipant::query()
            ->where('rfq_id', $rfq->id)
            ->whereIn('status', ['invited', 'accepted'])
            ->whereHas('supplier.users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->exists();
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