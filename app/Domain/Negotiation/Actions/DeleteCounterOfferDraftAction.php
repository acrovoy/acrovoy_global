<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOfferVersion;

class DeleteCounterOfferDraftAction
{
    public function execute(RfqOfferVersion $version, $context): void
    {
        /*
        |------------------------------------------
        | SECURITY CHECKS
        |------------------------------------------
        */

        if ($version->is_counter !== 1) {
            abort(403, 'Only counter versions can be deleted.');
        }

        if ($version->status !== 'draft') {
            abort(403, 'Only draft versions can be deleted.');
        }

        

        /*
        |------------------------------------------
        | OPTIONAL: extra safety (owner check)
        |------------------------------------------
        */

        if ($context->isCompany()) {
            if ($version->owner_id !== $context->id()) {
                abort(403, 'Invalid company ownership.');
            }
        }

        /*
        |------------------------------------------
        | DELETE ITEMS FIRST (if no cascade)
        |------------------------------------------
        */

        $version->items()->delete();

        /*
        |------------------------------------------
        | DELETE VERSION
        |------------------------------------------
        */

        $version->delete();
    }
}