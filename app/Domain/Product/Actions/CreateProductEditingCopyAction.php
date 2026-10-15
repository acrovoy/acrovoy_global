<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\CdnUrlService;
use App\Domain\Media\Services\MediaProcessingService;

class CreateProductEditingCopyAction
{
    public function execute(Product $original): Product
{
    return DB::transaction(function () use ($original) {
        $original = Product::query()
            ->whereKey($original->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($original->original_product_id !== null) {
            throw ValidationException::withMessages([
                'product' => 'Нельзя создать копию на основе временной копии товара.',
            ]);
        }

        if ($original->status !== 'approved') {
            throw ValidationException::withMessages([
                'product' => 'Редактировать через копию можно только одобренный товар.',
            ]);
        }

        $existingCopy = Product::query()
            ->where('original_product_id', $original->id)
            ->first();

        if ($existingCopy) {
            return $existingCopy;
        }

        $copy = $original->replicate([
            'slug',
            'status',
            'reject_reason',
            'created_at',
            'updated_at',
        ]);

        $copy->original_product_id = $original->id;
        $copy->slug = $this->generateUniqueSlug($original);
        $copy->status = 'draft';
        $copy->reject_reason = null;
        $copy->save();

        $this->copyTranslations($original, $copy);
        $this->copyPriceTiers($original, $copy);
        $this->copyMaterials($original, $copy);
        $this->copyShippingTemplates($original, $copy);
        $this->copyPaymentMethods($original, $copy);
        $this->copyPaymentTerms($original, $copy);
        $this->copyShippingDimensions($original, $copy);
        $this->copyReturnPolicy($original, $copy);
        $this->copyAttributeValues($original, $copy);
        $this->copyGalleryMedia($original, $copy);

        return $copy->fresh();
    });
}



    private function generateUniqueSlug(Product $original): string
    {
        do {
            $slug = Str::slug($original->slug)
                . '-edit-'
                . Str::lower(Str::random(8));
        } while (
            Product::query()->where('slug', $slug)->exists()
        );

        return $slug;
    }

    private function copyTranslations(Product $original, Product $copy): void
    {
        foreach ($original->translations as $translation) {
            $newTranslation = $translation->replicate([
                'created_at',
                'updated_at',
            ]);

            $newTranslation->product_id = $copy->id;
            $newTranslation->save();
        }
    }

    private function copyPriceTiers(Product $original, Product $copy): void
    {
        foreach ($original->priceTiers as $tier) {
            $newTier = $tier->replicate([
                'created_at',
                'updated_at',
            ]);

            $newTier->product_id = $copy->id;
            $newTier->save();
        }
    }

    private function copyMaterials(Product $original, Product $copy): void
    {
        $copy->materials()->sync(
            $original->materials()->pluck('materials.id')->all()
        );
    }

    private function copyShippingTemplates(Product $original, Product $copy): void
    {
        $copy->shippingTemplates()->sync(
            $original->shippingTemplates()->pluck('shipping_templates.id')->all()
        );
    }

    private function copyPaymentMethods(Product $original, Product $copy): void
    {
        $copy->paymentMethods()->sync(
            $this->getPivotData(
                $original->paymentMethods,
                ['sort_order']
            )
        );
    }

    private function copyPaymentTerms(Product $original, Product $copy): void
    {
        $copy->paymentTerms()->sync(
            $this->getPivotData(
                $original->paymentTerms,
                ['sort_order']
            )
        );
    }

    private function getPivotData($models, array $pivotColumns): array
    {
        $data = [];

        foreach ($models as $model) {
            $pivotData = [];

            foreach ($pivotColumns as $column) {
                $pivotData[$column] = $model->pivot->{$column} ?? null;
            }

            $data[$model->getKey()] = $pivotData;
        }

        return $data;
    }

    private function copyShippingDimensions(
        Product $original,
        Product $copy
    ): void {
        $dimensions = $original->shippingDimensions;

        if (!$dimensions) {
            return;
        }

        $newDimensions = $dimensions->replicate([
            'created_at',
            'updated_at',
        ]);

        $copy->shippingDimensions()->save($newDimensions);
    }

    private function copyReturnPolicy(Product $original, Product $copy): void
    {
        $policy = $original->returnPolicy;

        if (!$policy) {
            return;
        }

        $newPolicy = $policy->replicate([
            'created_at',
            'updated_at',
        ]);

        $copy->returnPolicy()->save($newPolicy);
    }

    private function copyAttributeValues(
        Product $original,
        Product $copy
    ): void {
        foreach ($original->attributeValues as $value) {
            $newValue = $value->replicate([
                'created_at',
                'updated_at',
            ]);

            $newValue->product_id = $copy->id;
            $newValue->save();

            if (method_exists($value, 'translations')) {
                foreach ($value->translations as $translation) {
                    $newTranslation = $translation->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $newTranslation->product_attribute_value_id = $newValue->id;
                    $newTranslation->save();
                }
            }

            if (method_exists($value, 'options')) {
                foreach ($value->options as $option) {
                    $newOption = $option->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $newOption->product_attribute_value_id = $newValue->id;
                    $newOption->save();
                }
            }
        }
    }

    private function copyGalleryMedia(Product $original, Product $copy): void
{
    $storage = app(StorageInterface::class);
    $cdnService = app(CdnUrlService::class);
    $processingService = app(MediaProcessingService::class);

    foreach ($original->images()->get() as $media) {
        $uuid = (string) Str::uuid();

        $extension = $media->extension
            ?: pathinfo($media->file_name, PATHINFO_EXTENSION);

        $fileName = $uuid . ($extension ? ".{$extension}" : '');

        $basePath = "media/{$media->collection}/{$uuid}";
        $originalPath = "{$basePath}/original/{$fileName}";

        $sourcePath = $media->originalPath();

        if (!$storage->copy(
            $sourcePath,
            $originalPath,
            (bool) $media->is_private
        )) {
            throw new \RuntimeException(
                "Не удалось скопировать файл медиа {$media->id}."
            );
        }

        try {
            $newMedia = Media::create([
                'uuid' => $uuid,
                'model_type' => get_class($copy),
                'model_id' => $copy->id,
                'collection' => $media->collection,
                'media_role' => $media->media_role,
                'original_file_name' => $media->original_file_name,
                'file_name' => $fileName,
                'file_path' => $originalPath,
                'cdn_url' => $cdnService->generate(
                    $originalPath,
                    (bool) $media->is_private
                ),
                'mime_type' => $media->mime_type,
                'extension' => $extension,
                'size_bytes' => $media->size_bytes,
                'width' => $media->width,
                'height' => $media->height,
                'checksum_hash' => $media->checksum_hash,
                'is_private' => $media->is_private,
                'is_primary' => $media->is_primary,
                'sort_order' => $media->sort_order,
                'is_main' => $media->is_main,
                'processing_status' => Media::STATUS_QUEUED,
                'metadata' => $media->metadata,
            ]);
        } catch (\Throwable $e) {
            $storage->deleteDirectory($basePath);

            throw $e;
        }

        $processingService->dispatchPipeline($newMedia, 2);
    }
}


}