<?php

namespace App\Domain\Product\Actions;

use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\PriceTier;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Str;
use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\CdnUrlService;
use App\Domain\Media\Services\MediaProcessingService;

use App\Models\ProductVariantGroup;
use App\Models\ProductVariantItem;

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
            | Способы оплаты и Условия оплаты
            |--------------------------------------------------------------------------
            */


$this->applyPaymentAndReturnSettings($original, $copy);

$original->unsetRelations();
$copy->unsetRelations();

$original->load(['paymentMethods', 'paymentTerms', 'returnPolicy']);
$copy->load(['paymentMethods', 'paymentTerms', 'returnPolicy']);


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



            $this->applyVariantEditingCopy($original, $copy);

            $this->replaceGalleryMedia($original, $copy);





            return $original->fresh();
        });
    }

    private function applyVariantEditingCopy(
    Product $original,
    Product $copy
): void {
    /*
    |--------------------------------------------------------------------------
    | Проверяем, что поставщик действительно сохранял варианты
    |--------------------------------------------------------------------------
    */

    if (!$copy->variant_group_id) {
        return;
    }

    $stagingGroup = ProductVariantGroup::query()
        ->whereKey($copy->variant_group_id)
        ->first();

    if (!$stagingGroup) {
        return;
    }

    $metadata = $stagingGroup->metadata ?? [];

    if (
        (int) ($metadata['editing_copy_of'] ?? 0) !== (int) $original->id
        || ($metadata['variants_saved'] ?? false) !== true
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Загружаем сохранённые элементы временной группы
    |--------------------------------------------------------------------------
    */

    $stagedItems = ProductVariantItem::query()
        ->with('media')
        ->where('variant_group_id', $stagingGroup->id)
        ->orderBy('sort_order')
        ->get();


    // Если в staging-группе осталась только запись самого оригинала,
    // это означает, что все дочерние вариации были удалены.
    if (
        $stagedItems->count() === 1
        && (int) $stagedItems->first()->product_id === (int) $original->id
    ) {
        $stagedItems = collect();
    }

    /*
    |--------------------------------------------------------------------------
    | Определяем группу оригинального товара
    |--------------------------------------------------------------------------
    */

    $originalGroup = $original->variant_group_id
        ? ProductVariantGroup::query()
            ->whereKey($original->variant_group_id)
            ->lockForUpdate()
            ->first()
        : null;

    if (!$originalGroup && $stagedItems->isNotEmpty()) {
        $originalGroup = ProductVariantGroup::create([
            'name' => $stagingGroup->name,
            'sort_order' => $stagingGroup->sort_order,
            'metadata' => [],
        ]);

        $original->update([
            'variant_group_id' => $originalGroup->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Если сохранён пустой список, удаляем связи оригинальной группы
    |--------------------------------------------------------------------------
    */

    if ($stagedItems->isEmpty()) {
        if ($originalGroup) {
            $oldItems = ProductVariantItem::query()
                ->with('media')
                ->where('variant_group_id', $originalGroup->id)
                ->get();

            $mediaService = app(
                \App\Domain\Media\Services\MediaService::class
            );

            foreach ($oldItems as $oldItem) {
                $linkedProduct = $oldItem->product;

                if ($oldItem->media) {
                    $mediaService->delete($oldItem->media);
                }

                $oldItem->delete();

                if (
                    $linkedProduct
                    && (int) $linkedProduct->variant_group_id
                        === (int) $originalGroup->id
                ) {
                    $linkedProduct->update([
                        'variant_group_id' => null,
                    ]);
                }
            }

            $originalGroup->delete();

            $original->update([
                'variant_group_id' => null,
            ]);
        }

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Удаляем из исходной группы варианты, которых больше нет в копии
    |--------------------------------------------------------------------------
    */

    $incomingProductIds = $stagedItems
        ->pluck('product_id')
        ->filter()
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values();

    $oldItems = ProductVariantItem::query()
        ->with('media')
        ->where('variant_group_id', $originalGroup->id)
        ->get();

    $mediaService = app(
        \App\Domain\Media\Services\MediaService::class
    );

    foreach ($oldItems as $oldItem) {
        if (
            $incomingProductIds->contains(
                (int) $oldItem->product_id
            )
        ) {
            continue;
        }

        $linkedProduct = $oldItem->product;

        if ($oldItem->media) {
            $mediaService->delete($oldItem->media);
        }

        $oldItem->delete();

        // Убираем связь с группой, но сам товар не удаляем.
        if (
            $linkedProduct
            && (int) $linkedProduct->variant_group_id
                === (int) $originalGroup->id
        ) {
            $linkedProduct->update([
                'variant_group_id' => null,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Создаём или обновляем элементы исходной группы
    |--------------------------------------------------------------------------
    */

    foreach ($stagedItems as $stagedItem) {
        $targetItem = ProductVariantItem::query()
            ->where('variant_group_id', $originalGroup->id)
            ->where('product_id', $stagedItem->product_id)
            ->first();

        if (!$targetItem) {
            $targetItem = new ProductVariantItem();

            $targetItem->variant_group_id = $originalGroup->id;
            $targetItem->product_id = $stagedItem->product_id;
        }

        $targetItem->title = $stagedItem->title;
        $targetItem->attribute_value_json =
            $stagedItem->attribute_value_json;
        $targetItem->sort_order = $stagedItem->sort_order;
        $targetItem->metadata = $stagedItem->metadata;
        $targetItem->save();

        /*
        |--------------------------------------------------------------------------
        | Переносим изображение, только если оно есть в копии
        |--------------------------------------------------------------------------
        | Если поставщик не загружал новое изображение, сохраняем
        | существующее изображение исходного элемента.
        */

        if ($stagedItem->media) {
            $newMedia = $this->copyVariantMedia(
                $stagedItem->media,
                $targetItem
            );

            $oldMedia = $targetItem->media;

            $targetItem->update([
                'media_id' => $newMedia->id,
            ]);

            if ($oldMedia && $oldMedia->id !== $newMedia->id) {
                $mediaService->delete($oldMedia);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Обновляем принадлежность связанного товара группе
        |--------------------------------------------------------------------------
        */

        if ($stagedItem->product_id) {
            Product::query()
                ->whereKey($stagedItem->product_id)
                ->update([
                    'variant_group_id' => $originalGroup->id,
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Оригинал должен ссылаться на исходную, а не временную группу
    |--------------------------------------------------------------------------
    */

    $original->update([
        'variant_group_id' => $originalGroup->id,
    ]);
}

private function copyVariantMedia(
    Media $media,
    ProductVariantItem $targetItem
): Media {
    $storage = app(StorageInterface::class);
    $cdnService = app(CdnUrlService::class);
    $processingService = app(MediaProcessingService::class);

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
            "Не удалось скопировать изображение варианта {$media->id}."
        );
    }

    try {
        $newMedia = Media::create([
            'uuid' => $uuid,
            'model_type' => ProductVariantItem::class,
            'model_id' => $targetItem->id,
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

    return $newMedia;
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


private function applyPaymentAndReturnSettings(
    Product $original,
    Product $copy
): void {
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
}


}