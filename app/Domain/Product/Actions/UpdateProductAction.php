<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductDTO;

use App\Domain\Product\Actions\SyncProductPriceTierAction ;
use App\Domain\Product\Actions\SyncProductMaterialAction;
use App\Domain\Product\Actions\SyncProductSpecificationAction;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductAction
{
    public function __construct(
        
        private SyncProductPriceTierAction  $priceAction,
        private SyncProductMaterialAction $materialAction,
        private SyncProductSpecificationAction $specAction
    ) {}

    public function execute(
        Product $product,
        ProductDTO $data,
        array $translations = [],
        array $shippingTemplates = [],
        array $mediaFiles = [],
        array $existingIds = [],
        array $sortOrder = [],
        array $existingSortOrder = [],
        array $isMain = [],
        array $priceTiers = [],
        string $materialsSelected = '',
        array $specifications = []
    ): Product {

        return DB::transaction(function () use (
            $product,
            $data,
            $translations,
            $shippingTemplates,
            $mediaFiles,
            $existingIds,
            $sortOrder,
            $existingSortOrder,
            $isMain,
            $priceTiers,
            $materialsSelected,
            $specifications
        ) {

           /* ============================
 * Product Identity
 * ============================ */

// 🔹 Лог перед обновлением продукта
logger()->info('==== Before Product Update ====', [
    'product_id' => $product->id,
    'product_exists_in_object' => $product->exists,
    'product_in_db' => \App\Models\Product::where('id', $product->id)->exists(),
]);

$product->update([
    'slug' => $data->slug,
    'name' => $data->name,
    'sku' => $data->sku,
    'supplier_id' => $data->supplierId,
    'category_id' => $data->categoryId,
    'moq' => $data->moq,
    'lead_time' => $data->leadTime,
    'customization' => $data->customization,
    'country_id' => $data->countryId,
]);

// 🔹 Лог после обновления продукта
logger()->info('==== After Product Update ====', [
    'product_id' => $product->id,
    'product_exists_in_object' => $product->exists,
    'product_in_db' => \App\Models\Product::where('id', $product->id)->exists(),
]);

            /* ============================
             * Translations
             * ============================ */

            /* ============================
 * Translations
 * ============================ */

if (!empty($translations)) {

    foreach ($translations as $locale => $payload) {

                if (empty($payload['name'])) {
                    continue;
                }

                // 🔹 Логи для диагностики
                logger()->info('==== Translation step ====', [
                    'product_id' => $product->id,
                    'product_exists_in_object' => $product->exists,
                    'product_in_db' => Product::where('id', $product->id)->exists(),
                ]);

                $product->translations()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'locale' => $locale
                    ],
                    [
                        'name' => $payload['name'] ?? null,
                        'undername' => $payload['undername'] ?? null,
                        'description' => $payload['description'] ?? null
                    ]
                );
            }
        }

            

            /*
            |--------------------------------------------------------------------------
            | 1. Delete removed media
            |--------------------------------------------------------------------------
            */

            $currentMediaIds = $product->images()->pluck('id')->toArray();

            $idsToDelete = array_diff($currentMediaIds, $existingIds);

            if (!empty($idsToDelete)) {

                $mediaService = app(\App\Domain\Media\Services\MediaService::class);

                $mediaToDelete = $product->images()
                    ->whereIn('id', $idsToDelete)
                    ->get();

                foreach ($mediaToDelete as $media) {
                    $mediaService->delete($media);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Update existing media (order + main)
            |--------------------------------------------------------------------------
            */
            foreach ($existingIds as $index => $id) {

                $product->images()
                    ->where('id', $id)
                    ->update([
                        'sort_order' => $existingSortOrder[$index] ?? $index,
                        'is_main'    => $existingSortOrder[$index] == 0 ? 1 : 0,
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Upload new media
            |--------------------------------------------------------------------------
            */

            $startOrder = count($existingIds);

            if (!empty($mediaFiles)) {

                $mediaService = app(\App\Domain\Media\Services\MediaService::class);

                foreach ($mediaFiles as $index => $file) {

                    $dto = new \App\Domain\Media\DTO\UploadMediaDTO(
                        file: $file,
                        model: $product,
                        collection: 'product_gallery',
                        mediaRole: 'product_image',
                        sortOrder: $sortOrder[$index],
                        isMain: $sortOrder[$index] == 0 ? 1 : 0
                    );

                    $mediaService->upload($dto);
                }
            }

           
            /* ============================
             * Price Pipeline
             * ============================ */

            if (!empty($priceTiers)) {
                $this->priceAction->execute($product, $priceTiers);
            }

            /* ============================
             * Material Pipeline
             * ============================ */

            $materialIds = explode(',', $materialsSelected);

            $this->materialAction->execute(
                $product,
                array_filter($materialIds)
            );

            /* ============================
             * Specification Pipeline
             * ============================ */

            if (!empty($specifications)) {
                $this->specAction->execute(
                    $product,
                    $specifications
                );
            }

            /* ============================
             * Shipping Templates
             * ============================ */

            if (!empty($shippingTemplates)) {
                $product->shippingTemplates()->sync($shippingTemplates);
            }

            return $product;
        });
    }
}