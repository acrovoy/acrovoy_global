<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\PriceTier;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Str;
use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\CdnUrlService;
use App\Domain\Media\Services\MediaProcessingService;

class ApplyProductEditingCopyAction
{
    public function execute(Product $copy): Product
    {
        return DB::transaction(function () use ($copy) {
            $copy = Product::query()
                ->whereKey($copy->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($copy->original_product_id === null) {
                throw ValidationException::withMessages([
                    'product' => 'Товар не является временной копией.',
                ]);
            }

            if ($copy->status !== 'pending') {
                throw ValidationException::withMessages([
                    'product' => 'Одобрить можно только копию со статусом pending.',
                ]);
            }

            $original = Product::query()
                ->whereKey($copy->original_product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($original->original_product_id !== null) {
                throw ValidationException::withMessages([
                    'product' => 'Исходный товар не может быть временной копией.',
                ]);
            }

            if ($original->status !== 'approved') {
                throw ValidationException::withMessages([
                    'product' => 'Исходный товар больше не имеет статус approved.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Основные поля товара
            |--------------------------------------------------------------------------
            | Не переносим ID, slug, статус, связь с оригиналом
            | и группу вариантов.
            */

            $original->fill([
                'sku' => $copy->sku,
                'supplier_id' => $copy->supplier_id,
                'created_by' => $copy->created_by,
                'category_id' => $copy->category_id,
                'moq' => $copy->moq,
                'lead_time' => $copy->lead_time,
                'customization' => $copy->customization,
                'country_id' => $copy->country_id,
                'origin_region_id' => $copy->origin_region_id,
                'origin_city_id' => $copy->origin_city_id,
                'origin_address' => $copy->origin_address,
                'origin_contact_name' => $copy->origin_contact_name,
                'origin_contact_phone' => $copy->origin_contact_phone,
                'reject_reason' => null,
            ]);

            $original->status = 'approved';
            $original->save();

            /*
            |--------------------------------------------------------------------------
            | Переводы товара
            |--------------------------------------------------------------------------
            */

            $original->translations()->delete();

            foreach ($copy->translations()->get() as $translation) {
                $newTranslation = $translation->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $newTranslation->product_id = $original->id;
                $newTranslation->save();
            }

            /*
            |--------------------------------------------------------------------------
            | Ценовые диапазоны
            |--------------------------------------------------------------------------
            */

            $original->priceTiers()->delete();

            foreach ($copy->priceTiers()->get() as $tier) {
                $newTier = $tier->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $newTier->product_id = $original->id;
                $newTier->save();
            }

            /*
            |--------------------------------------------------------------------------
            | Материалы
            |--------------------------------------------------------------------------
            */

            $original->materials()->sync(
                $copy->materials()->pluck('materials.id')->all()
            );

            /*
            |--------------------------------------------------------------------------
            | Шаблоны доставки
            |--------------------------------------------------------------------------
            */

            $original->shippingTemplates()->sync(
                $copy->shippingTemplates()
                    ->pluck('shipping_templates.id')
                    ->all()
            );

            /*
            |--------------------------------------------------------------------------
            | Способы оплаты
            |--------------------------------------------------------------------------
            */

            $paymentMethods = [];

            foreach ($copy->paymentMethods as $method) {
                $paymentMethods[$method->id] = [
                    'sort_order' => $method->pivot->sort_order,
                ];
            }

            $original->paymentMethods()->sync($paymentMethods);

            /*
            |--------------------------------------------------------------------------
            | Условия оплаты
            |--------------------------------------------------------------------------
            */

            $paymentTerms = [];

            foreach ($copy->paymentTerms as $term) {
                $paymentTerms[$term->id] = [
                    'sort_order' => $term->pivot->sort_order,
                ];
            }

            $original->paymentTerms()->sync($paymentTerms);

            /*
            |--------------------------------------------------------------------------
            | Габариты доставки
            |--------------------------------------------------------------------------
            */

            $copyDimensions = $copy->shippingDimensions;
            $originalDimensions = $original->shippingDimensions;

            if ($originalDimensions) {
                $originalDimensions->delete();
            }

            if ($copyDimensions) {
                $newDimensions = $copyDimensions->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $original->shippingDimensions()->save($newDimensions);
            }

            /*
            |--------------------------------------------------------------------------
            | Политика возврата
            |--------------------------------------------------------------------------
            */

            $copyPolicy = $copy->returnPolicy;
            $originalPolicy = $original->returnPolicy;

            if ($originalPolicy) {
                $originalPolicy->delete();
            }

            if ($copyPolicy) {
                $newPolicy = $copyPolicy->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $original->returnPolicy()->save($newPolicy);
            }

            /*
            |--------------------------------------------------------------------------
            | Характеристики и их переводы / опции
            |--------------------------------------------------------------------------
            */

            $originalValues = $original->attributeValues()->get();

            foreach ($originalValues as $value) {
                $value->translations()->delete();
                $value->options()->delete();
                $value->delete();
            }

            foreach (
                $copy->attributeValues()
                    ->with(['translations', 'options'])
                    ->get() as $value
            ) {
                $newValue = $value->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $newValue->product_id = $original->id;
                $newValue->save();

                foreach ($value->translations as $translation) {
                    $newTranslation = $translation->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $newTranslation->product_attribute_value_id =
                        $newValue->id;

                    $newTranslation->save();
                }

                foreach ($value->options as $option) {
                    $newOption = $option->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $newOption->product_attribute_value_id =
                        $newValue->id;

                    $newOption->save();
                }
            }




            $this->replaceGalleryMedia($original, $copy);





            return $original->fresh();
        });
    }

    private function replaceGalleryMedia(Product $original, Product $copy): void
{
    $storage = app(StorageInterface::class);
    $cdnService = app(CdnUrlService::class);
    $processingService = app(MediaProcessingService::class);
    $mediaService = app(\App\Domain\Media\Services\MediaService::class);

    // Сохраняем ссылки только на старые фотографии до создания новых.
    $oldMediaRecords = $original->images()->get();

    $copyImages = $copy->images()
        ->orderBy('sort_order')
        ->get();

    $newMediaRecords = [];

    foreach ($copyImages as $media) {
        $uuid = (string) Str::uuid();

        $extension = $media->extension
            ?: pathinfo($media->file_name, PATHINFO_EXTENSION);

        $fileName = $uuid . ($extension ? ".{$extension}" : '');
        $basePath = "media/{$media->collection}/{$uuid}";
        $originalPath = "{$basePath}/original/{$fileName}";

        if (!$storage->copy(
            $media->originalPath(),
            $originalPath,
            (bool) $media->is_private
        )) {
            throw new \RuntimeException(
                "Не удалось скопировать фотографию {$media->id}."
            );
        }

        try {
            $newMedia = Media::create([
                'uuid' => $uuid,
                'model_type' => Product::class,
                'model_id' => $original->id,
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

            $newMediaRecords[] = $newMedia;
        } catch (\Throwable $e) {
            $storage->deleteDirectory($basePath);

            throw $e;
        }
    }

    // Удаляем только фотографии, которые принадлежали исходному товару
    // до создания новых записей.
    foreach ($oldMediaRecords as $oldMedia) {
        $mediaService->delete($oldMedia);
    }

    // Запускаем обработку новых фотографий.
    foreach ($newMediaRecords as $newMedia) {
        $processingService->dispatchPipeline($newMedia, 2);
    }
}



}