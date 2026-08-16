<?php

namespace App\Domain\Negotiation\Policies;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferVersion;
use App\Domain\RFQ\Models\Rfq;
use App\Models\Buyer;
use App\Models\Supplier;
use App\Models\User;
use App\Policies\BasePolicy;
use App\Domain\RFQ\Enums\RfqStatus;

class RfqOfferPolicy extends BasePolicy
{
    /**
     * View offers for RFQ.
     *
     * Buyer:
     *     can view offers of own RFQ.
     *
     * Supplier:
     *     can view offers only for RFQs
     *     where this supplier participates.
     */
    public function view(User $user, Rfq $rfq): bool
    {
        if ($this->isBuyer()) {
            return $this->ownsBuyer($rfq->buyer);
        }

        if ($this->isSupplier()) {
            return $this->isParticipant($rfq);
        }

        return false;
    }


    /**
     * View offer comparison.
     *
     * Only RFQ owner (Buyer) can compare supplier offers.
     */
    


    /**
     * Accept supplier offer version.
     *
     * Only RFQ owner can accept an offer.
     */
    public function accept(
        User $user,
        Rfq $rfq,
        RfqOffer $offer,
        RfqOfferVersion $version
    ): bool {
        if (!$this->isBuyer()) {
            return false;
        }

        if (!$this->ownsBuyer($rfq->buyer)) {
            return false;
        }

        if ($offer->rfq_id !== $rfq->id) {
            return false;
        }

        return $version->rfq_offer_id === $offer->id;
    }


    /**
     * Reject supplier offer.
     *
     * Only RFQ owner can reject an offer.
     */
    public function reject(
        User $user,
        RfqOffer $offer
    ): bool {
        if (!$this->isBuyer()) {
            return false;
        }

        $rfq = $offer->rfq;

        if (!$rfq) {
            return false;
        }

        return $this->ownsBuyer($rfq->buyer);
    }


    /**
     * Create / edit supplier offer.
     *
     * Supplier must be an RFQ participant.
     */
    public function create(
        User $user,
        Rfq $rfq
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }


        return $this->isSupplier()
            && $this->isParticipant($rfq);
    }


    /**
     * Autosave supplier offer version.
     *
     * Supplier must participate in RFQ.
     */
    public function autosave(
        User $user,
        Rfq $rfq
    ): bool {


      


        return $this->isSupplier()
            && $this->isParticipant($rfq);
    }


    /**
     * Submit supplier offer version.
     *
     * Version must belong to an offer of this supplier.
     */
    public function submit(
        User $user,
        Rfq $rfq,
        RfqOfferVersion $version
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }


        if (!$this->isSupplier()) {
            return false;
        }

        if (!$this->isParticipant($rfq)) {
            return false;
        }

        $offer = $version->offer;

        if (!$offer) {
            return false;
        }

        if ($offer->rfq_id !== $rfq->id) {
            return false;
        }

        return $this->ownsSupplierOffer($offer);
    }


    /**
     * Create supplier revision.
     *
     * Supplier can create a revision only for its own offer.
     */
    public function createRevision(
        User $user,
        Rfq $rfq
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }


        if (!$this->isSupplier()) {
            return false;
        }

        return $this->isParticipant($rfq);
    }


    /**
     * Create buyer counter offer.
     *
     * Only Buyer who owns the RFQ can create
     * a counter offer.
     */
    public function createCounterOffer(
        User $user,
        Rfq $rfq,
        RfqOffer $offer
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }


        if (!$this->isBuyer()) {
            return false;
        }

        if (!$this->ownsBuyer($rfq->buyer)) {
            return false;
        }

        return $offer->rfq_id === $rfq->id;
    }


    /**
     * Autosave buyer counter offer.
     *
     * Only Buyer who owns the RFQ can modify
     * a counter-offer draft.
     */
    public function buyerCounterAutosave(
        User $user,
        Rfq $rfq,
        RfqOffer $offer,
        RfqOfferVersion $version
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }


        if (!$this->isBuyer()) {
            return false;
        }

        if (!$this->ownsBuyer($rfq->buyer)) {
            return false;
        }

        if ($offer->rfq_id !== $rfq->id) {
            return false;
        }

        if ($version->rfq_offer_id !== $offer->id) {
            return false;
        }

        return (bool) $version->is_counter
            && $version->status === 'draft';
    }


    /**
     * Submit buyer counter offer.
     */
    public function submitCounter(
        User $user,
        Rfq $rfq,
        RfqOfferVersion $version
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }


        if (!$this->isBuyer()) {
            return false;
        }

        if (!$this->ownsBuyer($rfq->buyer)) {
            return false;
        }

        $offer = $version->offer;

        if (!$offer) {
            return false;
        }

        if ($offer->rfq_id !== $rfq->id) {
            return false;
        }

        return (bool) $version->is_counter;
    }


    /**
     * Delete supplier draft.
     */
    public function deleteDraft(
        User $user,
        Rfq $rfq,
        RfqOfferVersion $version
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }

        if (!$this->isSupplier()) {
            return false;
        }

        if (!$this->isParticipant($rfq)) {
            return false;
        }

        $offer = $version->offer;

        if (!$offer) {
            return false;
        }

        if ($offer->rfq_id !== $rfq->id) {
            return false;
        }

        return $this->ownsSupplierOffer($offer)
            && $version->status === 'draft'
            && !$version->is_counter;
    }


    /**
     * Delete buyer counter-offer draft.
     */
    public function deleteCounterDraft(
        User $user,
        Rfq $rfq,
        RfqOffer $offer,
        RfqOfferVersion $version
    ): bool {

     if ($this->isRfqClosed($rfq)) {
        return false;
    }
    
        if (!$this->isBuyer()) {
            return false;
        }

        if (!$this->ownsBuyer($rfq->buyer)) {
            return false;
        }

        if ($offer->rfq_id !== $rfq->id) {
            return false;
        }

        if ($version->rfq_offer_id !== $offer->id) {
            return false;
        }

        return (bool) $version->is_counter
            && $version->status === 'draft';
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Current Supplier participates in RFQ.
     *
     * NEW ARCHITECTURE:
     *
     * participant_type = Supplier::class
     * participant_id   = current Supplier profile ID
     */
    protected function isParticipant(Rfq $rfq): bool
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
     * Check that offer belongs to current Supplier.
     */
    protected function ownsSupplierOffer(RfqOffer $offer): bool
    {
        $supplier = $this->supplier();

        if (!$supplier) {
            return false;
        }

        return $offer->participant_type === Supplier::class
            && (int) $offer->participant_id === (int) $supplier->id;
    }

    protected function isRfqClosed(Rfq $rfq): bool
{
    return $rfq->status === RfqStatus::CLOSED;
}

}