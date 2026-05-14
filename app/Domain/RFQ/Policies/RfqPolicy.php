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


    /**
     * =========================================
     * BUYER (company ownership, NOT user ownership)
     * =========================================
     */

    if ($rfq->buyer_type && $rfq->buyer_id) {

        // если buyer = User напрямую
        if ($rfq->buyer_type === User::class) {
            if ($rfq->buyer_id === $user->id) {
                return true;
            }
        }

        // если buyer = Company/Supplier (через members)
        $buyerClass = $rfq->buyer_type;

        if (method_exists($buyerClass, 'users')) {

            $isBuyerMember = $buyerClass::query()
                ->where('id', $rfq->buyer_id)
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('id', $user->id); // ❗ без users.id
                })
                ->exists();

            if ($isBuyerMember) {
                return true;
            }
        }
    }

   /**
     * SUPPLIER ACCESS — БЕЗ USER ID ВООБЩЕ
     */

    $supplier = app(ActiveContextService::class)->supplier();

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