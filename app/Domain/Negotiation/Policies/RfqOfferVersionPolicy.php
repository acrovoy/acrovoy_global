<?php

namespace App\Domain\Negotiation\Policies;

use App\Domain\Negotiation\Models\RfqOfferVersion;
use App\Models\User;
use App\Domain\Negotiation\Services\NegotiationAccessService;

class RfqOfferVersionPolicy
{
    public function view(User $user, RfqOfferVersion $version): bool
    {
        return app(NegotiationAccessService::class)
            ->canViewOffer(
                $version->offer,
                $user->id
            );
    }


    public function accept(User $user, RfqOfferVersion $version): bool
    {
        return app(NegotiationAccessService::class)
            ->canManageOffer(
                $version->offer,
                $user->id
            );
    }
}