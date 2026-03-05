<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Domain\Product\Events\ProductDeletedEvent;
use Illuminate\Support\Facades\Auth;
use App\Domain\Media\Services\MediaService;
use App\Domain\Media\Models\Media;



class DeleteProductAction
{
    public function execute(Product $product): void
{
    $supplierId = Auth::user()?->supplier?->id;

    abort_if(
        !$supplierId || $product->supplier_id !== $supplierId,
        403,
        'Unauthorized action.'
    );

    /*
     * Load product media
     */
    $mediaItems = Media::where([
        'model_type' => Product::class,
        'model_id' => $product->id
    ])->get();


    $mediaService = app(MediaService::class);
    /*
     * Async delete pipeline
     */
    foreach ($mediaItems as $media) {
        $mediaService->delete($media);
    }

    $productId = $product->id;

    $product->delete();

    ProductDeletedEvent::dispatch($productId);
}
}