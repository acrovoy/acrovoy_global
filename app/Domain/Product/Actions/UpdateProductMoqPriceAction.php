<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductMoqDTO;

use App\Domain\Product\Actions\SyncProductPriceTierAction;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductMoqPriceAction
{
    public function __construct(

        private SyncProductPriceTierAction  $priceAction,
    
    ) {}

    public function execute(
        Product $product,
        ProductMoqDTO $data,
        array $priceTiers = [],
        
    ): Product {

        return DB::transaction(function () use (
            $product,
            $data,
            $priceTiers,
        ) {

          

            $product->update([
                'moq' => $data->moq,
                
            ]);

          

            /* ============================
             * Price Pipeline
             * ============================ */

            if (!empty($priceTiers)) {
                $this->priceAction->execute($product, $priceTiers);
            }

          
           

            return $product;
        });
    }
}
