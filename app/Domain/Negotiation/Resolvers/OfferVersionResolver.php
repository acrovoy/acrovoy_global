<?php

namespace App\Domain\Negotiation\Resolvers;

use App\Domain\Negotiation\Models\RfqOffer;

class OfferVersionResolver
{
    public function resolve(
        RfqOffer $offer,
        ?int $requestedVersionId = null
    ) {
        /**
         * =========================
         * 1. EXPLICIT VERSION (URL)
         * =========================
         */
        if ($requestedVersionId) {

            $version = $offer->versions()
                ->with(['items.options.translations'])
                ->where('id', $requestedVersionId)
                ->first();

            if ($version) {
                return $version;
            }
        }

        /**
         * =========================
         * 2. ACTIVE DRAFT
         * =========================
         */


        $draft = $offer->versions()
            ->where('status', 'draft')
            ->where('is_counter', 0)
            ->orderByDesc('created_at')
            ->first();


        if ($draft) {
            return $draft;
        }

        /**
         * =========================
         * 3. LAST VERSION (fallback)
         * =========================
         */


        return $offer->versions()
            ->where(function ($q) {

                // supplier versions
                $q->where('is_counter', 0)

                    // counter versions ONLY if submitted
                    ->orWhere(function ($q) {
                        $q->where('is_counter', 1)
                            ->where('status', 'submitted');
                    });
            })
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * =========================
     * GET CURRENT DRAFT ONLY
     * =========================
     */
    public function currentDraft(RfqOffer $offer)
    {
        return $offer->versions()
            ->where('status', 'draft')
            ->where('is_counter', 0)
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * =========================
     * GET LAST SUBMITTED
     * =========================
     */
    public function lastSubmitted(RfqOffer $offer)
    {
        return $offer->versions()
            ->where('status', 'submitted')
            ->where('is_counter', 0)
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * =========================
     * CHECK IF LATEST IS SUBMITTED
     * =========================
     */
    public function isLatestSubmitted(RfqOffer $offer, $version): bool
    {
        $latestSubmitted = $this->lastSubmitted($offer);

        return $version
            && $latestSubmitted
            && $version->id === $latestSubmitted->id;
    }


    public function hasDraft(RfqOffer $offer): bool
    {
        return $offer->versions()
            ->where('status', 'draft')
            ->where('is_counter', 0)
            ->exists();
    }


    public function canCreateRevision(
        RfqOffer $offer,
        $version
    ): bool {

        $latestSubmitted = $this->lastSubmitted($offer);

        $isLatestSubmitted =
            $version
            && $latestSubmitted
            && $version->id === $latestSubmitted->id;

        return $isLatestSubmitted
            && !$this->hasDraft($offer);
    }


    public function lastSupplierVersion(RfqOffer $offer)
    {
        return $offer->versions()
            ->where('is_counter', 0)
            ->orderByDesc('created_at')
            ->first();
    }


    public function latestCounterVersion(
        RfqOffer $offer,
        int $userId,
        string $status
    ) {
        return $offer->versions()
            ->where('is_counter', 1)
            ->where('status', $status)
            ->where('created_by', $userId)
            ->latest()
            ->first();
    }

    public function lastSupplierSubmittedVersion(RfqOffer $offer)
    {
        return $offer->versions()
            ->where('is_counter', 0)
            ->where('status', 'submitted')
            ->orderByDesc('created_at')
            ->first();
    }
}
