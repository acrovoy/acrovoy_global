<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOfferVersion;

class DeleteDraftOfferVersionAction
{
    public function execute(RfqOfferVersion $version): void
    {
        /*
        |--------------------------------------------------------------------------
        | ONLY DRAFT CAN BE DELETED
        |--------------------------------------------------------------------------
        */

        if ($version->status !== 'draft') {
            abort(422, 'Only draft versions can be deleted.');
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE CHILD RELATIONS FIRST
        |--------------------------------------------------------------------------
        */

        $version->items()->each(function ($item) {
            $item->options()->detach();
        });

        $version->items()->delete();

        /*
        |--------------------------------------------------------------------------
        | DELETE VERSION
        |--------------------------------------------------------------------------
        */

        $version->delete();
    }
}