<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Domain\Product\DTO\ProductDTO;
use App\Domain\Product\Events\ProductCreatedEvent;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function execute(ProductDTO $data): Product
    {
        return DB::transaction(function () use ($data) {

            $product = Product::create([
                'name' => $data->name,
                'supplier_id' => $data->supplierId,
                'sku' => $data->sku,
                'slug' => $data->slug,
                'category_id' => $data->categoryId,
                'moq' => $data->moq,
                'lead_time' => $data->leadTime,
                'customization' => $data->customization,
                'country_id' => $data->countryId,
            ]);

            ProductCreatedEvent::dispatch($product);

            return $product;
        });
    }
}