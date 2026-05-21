<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOfferVersion;
use Illuminate\Support\Facades\DB;

class SubmitOfferVersionAction
{
    public function execute(RfqOfferVersion $version): RfqOfferVersion
    {
        /*
        |--------------------------------------------------------------------------
        | ONLY DRAFT CAN BE SUBMITTED
        |--------------------------------------------------------------------------
        */

        if ($version->status !== 'draft') {
            abort(422, 'Only draft versions can be submitted.');
        }

        return DB::transaction(function () use ($version) {

            /*
            |--------------------------------------------------------------------------
            | LOCK OFFER VERSIONS
            |--------------------------------------------------------------------------
            */

            $offer = $version->offer;

            $nextVersionNumber = $offer->versions()
                ->lockForUpdate()
                ->whereNotNull('version_number')
                ->max('version_number') + 1;

            /*
            |--------------------------------------------------------------------------
            | SUBMIT VERSION
            |--------------------------------------------------------------------------
            */

            $version->update([
                'version_number' => $nextVersionNumber,
                'status' => 'submitted',
            ]);

            return $version->fresh();
        });
    }
}