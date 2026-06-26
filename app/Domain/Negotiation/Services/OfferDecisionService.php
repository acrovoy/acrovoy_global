<?php

namespace App\Domain\Negotiation\Services;

use App\Domain\Negotiation\Models\RfqOffer;
use App\Domain\RFQ\Enums\RfqStatus;
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

            // 1. accept version
            $version->update([
                'status' => 'accepted',
                'accepted_by' => $userId,
                'accepted_at' => now(),
            ]);

            // 2. reject other versions of same offer
            $this->rejectOtherVersions($version->rfq_offer_id, $version->id);

            // 3. accept offer
            $offer = $version->offer;

            $offer->update([
                'status' => 'accepted',
                'accepted_version_id' => $version->id,
            ]);

            // 4. reject all other offers in RFQ
            $offer->rfq->offers()
                ->where('id', '!=', $offer->id)
                ->update([
                    'status' => 'rejected',

                ]);

            // 5. reject all versions of other offers in RFQ
            $offer->rfq->offers()
                ->where('id', '!=', $offer->id)
                ->with('versions')
                ->get()
                ->each(function ($rejectedOffer) use ($userId) {

                    $rejectedOffer->versions()->update([
                        'status' => 'rejected',
                    ]);
                });
                
            // 6. CLOSE RFQ
                $offer->rfq->update([
                    'status' => RfqStatus::CLOSED,
                    'closed_at' => now(),
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
            ->where('rfq_offer_id', $offerId)
            ->where('id', '!=', $acceptedVersionId)
            ->update([
                'status' => 'rejected',
            ]);
    }
}
