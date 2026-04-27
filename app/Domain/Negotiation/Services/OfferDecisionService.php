<?php

namespace App\Domain\Negotiation\Services;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\Negotiation\Models\RfqOfferVersion;
use Illuminate\Support\Facades\DB;

class OfferDecisionService
{
    /**
     * ACCEPT specific version
     */
    public function accept(RfqOfferVersion $version, int $userId): void
    {
        DB::transaction(function () use ($version, $userId) {

            // 1. помечаем версию как принятую
            $version->update([
                'status' => 'accepted',
                'accepted_by' => $userId,
                'accepted_at' => now(),
            ]);

            // 2. отклоняем все остальные версии этого offer
            $this->rejectOtherVersions($version->offer_id, $version->id);

            // 3. фиксируем offer как "accepted"
            $version->offer->update([
                'status' => 'accepted',
                'accepted_version_id' => $version->id,
            ]);
        });
    }

    /**
     * REJECT specific offer (or version fallback logic)
     */
    public function reject(RfqOffer $offer, int $userId): void
    {
        DB::transaction(function () use ($offer, $userId) {

            // если уже есть accepted version → нельзя reject
            if ($offer->status === 'accepted') {
                throw new \DomainException('Offer already accepted');
            }

            $offer->update([
                'status' => 'rejected',
                'rejected_by' => $userId,
                'rejected_at' => now(),
            ]);

            // отклоняем все версии
            $offer->versions()->update([
                'status' => 'rejected',
            ]);
        });
    }

    /**
     * reject all versions except selected one
     */
    private function rejectOtherVersions(int $offerId, int $acceptedVersionId): void
    {
        RfqOfferVersion::query()
            ->where('offer_id', $offerId)
            ->where('id', '!=', $acceptedVersionId)
            ->update([
                'status' => 'rejected',
            ]);
    }
}