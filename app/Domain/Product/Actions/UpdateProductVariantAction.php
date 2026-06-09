<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductVariantDTO;

use App\Domain\Product\Actions\SyncProductPriceTierAction;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductVariantAction
{
    

    public function execute(
        Product $product,
        ProductVariantDTO $data,
        
        
    ): Product {

        return DB::transaction(function () use (
            $product,
            $data,
            
        ) {

          

            $product->update([
                'lead_time' => $data->leadTime,
                'customization' => $data->customization,
                
            ]);

          

           

          
           

            return $product;
        });
    }
}
