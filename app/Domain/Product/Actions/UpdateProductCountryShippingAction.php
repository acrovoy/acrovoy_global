<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductCountryDTO;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductCountryShippingAction
{
    public function execute(
        Product $product,
        ProductCountryDTO $data,
        array $shippingTemplates = [],

    ): Product {

        return DB::transaction(function () use (
            $product,
            $shippingTemplates,
            $data,
        ) {



            /* ============================
             * Shipping Templates
             * ============================ */

            if (!empty($shippingTemplates)) {
                $product->shippingTemplates()->sync($shippingTemplates);
            }


            $product->update([

                'country_id' => $data->countryId,
            ]);

            return $product;
        });
    }
}
