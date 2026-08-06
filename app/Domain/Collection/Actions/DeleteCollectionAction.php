<?php

namespace App\Domain\Collection\Actions;

use App\Domain\Collection\Models\ProductCollection;
use Illuminate\Support\Facades\DB;

class DeleteCollectionAction
{
    /**
     * Delete Collection
     */
    public function __invoke(
        ProductCollection $collection
    ): bool {

        return DB::transaction(function () use (
            $collection
        ) {

            /*
            |--------------------------------------------------------------------------
            | Delete Translations
            |--------------------------------------------------------------------------
            */

            $collection
                ->translations()
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Detach Relations
            |--------------------------------------------------------------------------
            */

            $collection
                ->products()
                ->detach();

            

            /*
            |--------------------------------------------------------------------------
            | Delete Collection
            |--------------------------------------------------------------------------
            */

            return (bool) $collection->delete();

        });

    }
}