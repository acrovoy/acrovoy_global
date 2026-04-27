<?php

namespace App\Domain\Negotiation\Services;

use App\Domain\Negotiation\Models\RfqOfferVersion;

class OfferVersionAcceptService
{
    public function accept(
        RfqOfferVersion $version
    ): void {

        $version->update([

            'accepted_at' => now()

        ]);
    }
}