<?php

namespace App\Policies;

use App\Models\RfqOffer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RfqOfferPolicy
{


    public function acceptOffer(User $user, RfqOffer $offer): bool
{
    // Только покупатель, который создал RFQ
    if ($user->id !== $offer->rfq->buyer_id) return false;

    // RFQ должна быть активной
    if ($offer->rfq->status !== 'active') return false;

    // Оффер ещё не принят
    if ($offer->status !== 'pending') return false;

    return true;
}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RfqOffer $rfqOffer): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RfqOffer $rfqOffer): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RfqOffer $rfqOffer): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RfqOffer $rfqOffer): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RfqOffer $rfqOffer): bool
    {
        //
    }
}
