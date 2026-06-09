<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductMaterialDTO;


use App\Domain\Product\Actions\SyncProductMaterialAction;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductMaterialsAction
{
    public function __construct(
      
        private SyncProductMaterialAction $materialAction,
        
    ) {}

    public function execute(
        Product $product,
        string $materialsSelected = '',
    ): Product {

        return DB::transaction(function () use (
            $product,
            $materialsSelected,
            
        ) {     
            /* ============================
             * Material Pipeline
             * ============================ */

            $materialIds = explode(',', $materialsSelected);

            $this->materialAction->execute(
                $product,
                array_filter($materialIds)
            );

          

            return $product;
        });
    }
}
