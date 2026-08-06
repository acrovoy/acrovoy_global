<?php

namespace App\Domain\Collection\Services;

use App\Domain\Collection\Actions\AddProductToCollectionAction;
use App\Domain\Collection\Actions\CreateCollectionAction;
use App\Domain\Collection\Actions\DeleteCollectionAction;
use App\Domain\Collection\Actions\RemoveProductFromCollectionAction;
use App\Domain\Collection\Actions\UpdateCollectionAction;
use App\Domain\Collection\Models\ProductCollection;
use App\Models\Product;
use App\Services\Company\ActiveContextService;

use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;

use Illuminate\Support\Facades\Log;


class ProductCollectionService
{
    public function __construct(
        protected ActiveContextService $context,
        protected CreateCollectionAction $create,
        protected UpdateCollectionAction $update,
        protected DeleteCollectionAction $delete,
        protected AddProductToCollectionAction $addProduct,
        protected RemoveProductFromCollectionAction $removeProduct,
        protected MediaService $mediaService,
    ) {
    }

    /**
     * Create collection
     */
    public function create(array $data): ProductCollection
{
    Log::info('Collection create started', [
        'has_cover' => isset($data['cover']),
        'keys' => array_keys($data),
    ]);

    $identity = $this->context->identity();

    $data = array_merge($data, [
        'owner_type' => $identity['entity_type'],
        'owner_id' => $identity['entity_id'],
        'type' => $data['type'] ?? $this->defaultType(),
    ]);

    $collection = ($this->create)($data);

    Log::info('Collection created', [
        'id' => $collection->id,
    ]);

    if (!empty($data['cover'])) {

        Log::info('Cover upload started');

        $metadata = $this->mediaService->extractMetadata($data['cover']);

        Log::info('Metadata extracted', $metadata);

        $dto = new UploadMediaDTO(
            file: $data['cover'],
            model: $collection,
            collection: 'collection_cover',
            mediaRole: 'cover',
            private: false,
            originalFileName: $data['cover']->getClientOriginalName(),
            metadata: $metadata,
            sortOrder: 0,
            isMain: true
        );

        Log::info('DTO created');

        $media = $this->mediaService->upload($dto);

        Log::info('Media uploaded', [
            'media_id' => $media->id,
        ]);
    } else {

        Log::warning('Cover not found in data');

    }

    return $collection;
}

    /**
     * Update collection
     */
    public function update(
    ProductCollection $collection,
    array $data
): ProductCollection {

    $collection = ($this->update)(
        $collection,
        $data
    );

    if (!empty($data['cover'])) {

        if ($collection->cover) {
            $this->mediaService->delete($collection->cover);
        }

        $metadata = $this->mediaService->extractMetadata(
            $data['cover']
        );

        $dto = new UploadMediaDTO(

            file: $data['cover'],

            model: $collection,

            collection: 'collection_cover',

            mediaRole: 'cover',

            private: false,

            originalFileName: $data['cover']->getClientOriginalName(),

            metadata: $metadata,

            sortOrder: 0,

            isMain: true

        );

        $this->mediaService->upload($dto);
    }

    return $collection;
}

    /**
     * Delete collection
     */
    public function delete(
    ProductCollection $collection
): bool {

    if ($collection->cover) {

        $this->mediaService->delete(
            $collection->cover
        );

    }

    foreach ($collection->media as $media) {

        if ($collection->cover && $media->id === $collection->cover->id) {
            continue;
        }

        $this->mediaService->delete($media);

    }

    return ($this->delete)($collection);
}

    /**
     * Add product
     */
    public function addProduct(
        ProductCollection $collection,
        Product $product,
        ?int $sortOrder = null
    ) {
        return ($this->addProduct)(
            $collection,
            $product,
            $sortOrder
        );
    }

    /**
     * Remove product
     */
    public function removeProduct(
        ProductCollection $collection,
        Product $product
    ): bool {

        return ($this->removeProduct)(
            $collection,
            $product
        );
    }

    /**
     * Owner from ActiveContext
     */
    protected function owner(): array
{
    $identity = $this->context->identity();

    return [
        'owner_type' => $identity['entity_type'],
        'owner_id'   => $identity['entity_id'],
    ];
}

    /**
     * Default collection type
     */
    protected function defaultType(): string
{
    $identity = $this->context->identity();

    return match ($identity['platform_role']) {
        'supplier' => 'supplier',
        'buyer'    => 'buyer',
        default    => 'platform',
    };
}

public function myCollections()
{
    $identity = $this->context->identity();

    return ProductCollection::query()
        ->where('owner_type', $identity['entity_type'])
        ->where('owner_id', $identity['entity_id']);
}

}