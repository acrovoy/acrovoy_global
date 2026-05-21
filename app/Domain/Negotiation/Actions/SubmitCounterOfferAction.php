<?php

namespace App\Domain\Negotiation\Actions;

use App\Domain\Negotiation\Models\RfqOfferVersion;
use App\Domain\Negotiation\Models\RfqOffer;
use Illuminate\Support\Facades\DB;

class SubmitCounterOfferAction
{
    public function execute(RfqOfferVersion $version): RfqOfferVersion
    {
        /*
        |--------------------------------------------------------------------------
        | ONLY DRAFT COUNTER CAN BE SUBMITTED
        |--------------------------------------------------------------------------
        */

        if ($version->status !== 'draft') {
            abort(422, 'Only draft counter offers can be submitted.');
        }

        if (!$version->is_counter) {
            abort(422, 'This is not a counter version.');
        }

        return DB::transaction(function () use ($version) {

            $offer = $version->offer;

            /*
            |--------------------------------------------------------------------------
            | LOCK VERSION SEQUENCE
            |--------------------------------------------------------------------------
            */

            $nextVersionNumber = $offer->versions()
                ->lockForUpdate()
                ->whereNotNull('version_number')
                ->max('version_number') + 1;

            /*
            |--------------------------------------------------------------------------
            | SUBMIT COUNTER
            |--------------------------------------------------------------------------
            */

            $version->update([
                'status' => 'submitted',
                'version_number' => $nextVersionNumber,
            ]);

            return $version->fresh();
        });
    }
}