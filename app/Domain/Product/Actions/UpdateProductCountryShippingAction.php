<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductCountryDTO;

use App\Domain\Product\Actions\SyncProductPriceTierAction;
use App\Domain\Product\Actions\SyncProductMaterialAction;
use App\Domain\Product\Actions\SyncProductSpecificationAction;
use App\Domain\Product\Actions\SyncProductAttributeAction;
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
