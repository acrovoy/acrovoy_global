<?php

namespace App\Domain\Product\Actions;



use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductMediaAction
{
    

    public function execute(
        Product $product,
        array $mediaFiles = [],
        array $existingIds = [],
        array $sortOrder = [],
        array $existingSortOrder = [],
        array $isMain = [],
        
    ): Product {

        return DB::transaction(function () use (
            $product,
            $mediaFiles,
            $existingIds,
            $sortOrder,
            $existingSortOrder,
            $isMain,
            
        ) {

          
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


            return $product;
        });
    }
}
