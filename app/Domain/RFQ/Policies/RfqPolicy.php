<?php

namespace App\Domain\RFQ\Policies;

use App\Domain\RFQ\Models\Rfq;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\BasePolicy;

class RfqPolicy extends BasePolicy
{
    /**
     * View RFQ.
     */
    public function view(User $user, Rfq $rfq): bool
    {
        /*
        |--------------------------------------------------------------------------
        | BUYER
        |--------------------------------------------------------------------------
        */

        if ($this->isBuyer()) {
            return $this->ownsBuyer($rfq->buyer);
        }

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        if ($this->isSupplier()) {
            return $this->canSupplierView($rfq);
        }

        return false;
    }

    /**
     * Create RFQ.
     */
    public function create(User $user): bool
    {
        return $this->isBuyer()
            && $this->buyer() !== null;
    }

    /**
     * Update RFQ.
     */
    public function update(User $user, Rfq $rfq): bool
    {
        return $this->isBuyer()
            && $this->ownsBuyer($rfq->buyer);
    }

    /**
     * Delete RFQ.
     */
    public function delete(User $user, Rfq $rfq): bool
    {
        return $this->isBuyer()
            && $this->ownsBuyer($rfq->buyer);
    }

    /**
     * Publish RFQ.
     */
    public function publish(User $user, Rfq $rfq): bool
    {
        return $this->isBuyer()
            && $this->ownsBuyer($rfq->buyer);
    }

    /**
     * Manage participants.
     */
    public function manageParticipants(User $user, Rfq $rfq): bool
    {
        return $this->isBuyer()
            && $this->ownsBuyer($rfq->buyer);
    }

    /**
     * Add participant.
     */
    public function addParticipant(User $user, Rfq $rfq): bool
    {
        return $this->isBuyer()
            && $this->ownsBuyer($rfq->buyer);
    }

    /**
     * Remove participant.
     */
    public function removeParticipant(User $user, Rfq $rfq): bool
    {
        return $this->isBuyer()
            && $this->ownsBuyer($rfq->buyer);
    }

    /**
     * View participants.
     */
    public function viewParticipants(User $user, Rfq $rfq): bool
    {
        if ($this->isBuyer()) {
            return $this->ownsBuyer($rfq->buyer);
        }

        if ($this->isSupplier()) {
            return $this->isInvitedSupplier($rfq);
        }

        return false;
    }

    /**
     * View offers.
     */
    public function viewOffers(User $user, Rfq $rfq): bool
    {
        if ($this->isBuyer()) {
            return $this->ownsBuyer($rfq->buyer);
        }

        if ($this->isSupplier()) {
            return $this->isInvitedSupplier($rfq);
        }

        return false;
    }

    /**
     * Submit offer.
     */
    public function submitOffer(User $user, Rfq $rfq): bool
    {
        return $this->isSupplier()
            && $this->isInvitedSupplier($rfq);
    }

    /**
     * Close RFQ.
     */
    public function close(User $user, Rfq $rfq): bool
    {
        return $this->isBuyer()
            && $this->ownsBuyer($rfq->buyer);
    }

    /**
     * Public Open RFQ.
     *
     * This is intentionally separate from view().
     */
    public function viewPublic(Rfq $rfq): bool
    {
        return $rfq->visibility === 'open';
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Current Buyer owns RFQ.
     */
    protected function ownsBuyer(?\App\Models\Buyer $buyer): bool
    {
        return $buyer !== null
            && $this->buyer()?->id === $buyer->id;
    }

    /**
     * Current Supplier is an RFQ participant.
     */
    protected function isInvitedSupplier(Rfq $rfq): bool
    {
        $supplier = $this->supplier();

        if (!$supplier) {
            return false;
        }

        return $rfq->participants()
            ->where('participant_type', Supplier::class)
            ->where('participant_id', $supplier->id)
            ->whereIn('status', [
                'invited',
                'accepted',
            ])
            ->exists();
    }

    /**
     * Supplier can view RFQ according to visibility.
     */
    protected function canSupplierView(Rfq $rfq): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Explicit invitation always grants access
        |--------------------------------------------------------------------------
        */

        if ($this->isInvitedSupplier($rfq)) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | PRIVATE
        |--------------------------------------------------------------------------
        */

        if ($rfq->visibility === 'private') {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | PLATFORM
        |--------------------------------------------------------------------------
        */

        if ($rfq->visibility === 'platform') {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        if ($rfq->visibility === 'category') {
            return $this->matchesVisibilityCategory($rfq);
        }

        /*
        |--------------------------------------------------------------------------
        | OPEN
        |--------------------------------------------------------------------------
        */

        if ($rfq->visibility === 'open') {
            return true;
        }

        return false;
    }

    /**
     * Check supplier against RFQ visibility categories.
     *
     * TODO: use actual Supplier -> Category relation.
     */
    protected function matchesVisibilityCategory(Rfq $rfq): bool
    {
        $supplier = $this->supplier();

        if (!$supplier) {
            return false;
        }

        return false;
    }

    public function comparison(User $user, Rfq $rfq): bool
{
    return $this->isBuyer()
        && $this->ownsBuyer($rfq->buyer);
}

}