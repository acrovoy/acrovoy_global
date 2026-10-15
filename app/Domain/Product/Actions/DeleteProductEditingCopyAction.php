<?php

namespace App\Domain\Product\Actions;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaService;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariantGroup;
use App\Models\ProductVariantItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteProductEditingCopyAction
{
    public function __construct(
        private MediaService $mediaService
    ) {
    }

    public function execute(Product $copy): void
    {
        $mediaToDelete = DB::transaction(function () use ($copy) {
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
                    'product' => 'Удалить можно только подтверждённую копию со статусом pending.',
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

            /*
             * Не удаляем копию, если у неё появились операционные данные.
             * Такие записи нельзя удалять автоматически.
             */
            if (
                $copy->orderItems()->exists()
                || $copy->reviews()->exists()
                || $copy->rfqs()->exists()
            ) {
                throw ValidationException::withMessages([
                    'product' => 'Копию нельзя удалить: с ней связаны заказы, отзывы или RFQ.',
                ]);
            }

            $mediaIds = [];

            /*
             * Галерея временной копии.
             */
            $mediaIds = array_merge(
                $mediaIds,
                $copy->images()->pluck('id')->all()
            );

            /*
             * Временная группа вариаций.
             * Удаляем её только при совпадении метаданных.
             */
            if ($copy->variant_group_id) {
                $stagingGroup = ProductVariantGroup::query()
                    ->whereKey($copy->variant_group_id)
                    ->lockForUpdate()
                    ->first();

                if ($stagingGroup) {
                    $metadata = $stagingGroup->metadata ?? [];

                    $isOwnStagingGroup =
                        (int) ($metadata['editing_copy_of'] ?? 0)
                            === (int) $original->id
                        && ($metadata['variants_saved'] ?? false) === true;

                    if ($isOwnStagingGroup) {
                        $items = ProductVariantItem::query()
                            ->where('variant_group_id', $stagingGroup->id)
                            ->get();

                        $itemIds = $items->pluck('id')->all();

                        $itemMediaIds = $items
                            ->pluck('media_id')
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        /*
                         * Удаляем элементы staging-группы,
                         * но не связанные товары.
                         */
                        ProductVariantItem::query()
                            ->where('variant_group_id', $stagingGroup->id)
                            ->delete();

                        /*
                         * Медиа элемента удаляем только если на него
                         * больше не ссылаются другие элементы.
                         */
                        foreach ($itemMediaIds as $mediaId) {
                            $stillUsed = ProductVariantItem::query()
                                ->where('media_id', $mediaId)
                                ->exists();

                            if (!$stillUsed) {
                                $mediaIds[] = $mediaId;
                            }
                        }

                        $stagingGroup->delete();
                    }
                }
            }

            /*
             * Удаляем данные, принадлежащие только копии.
             */
            $copy->translations()->delete();
            $copy->priceTiers()->delete();
            $copy->materials()->detach();
            $copy->shippingTemplates()->detach();
            $copy->paymentMethods()->detach();
            $copy->paymentTerms()->detach();
            $copy->wishlistedBy()->detach();
            $copy->collections()->detach();

            

            foreach ($copy->attributeValues()->get() as $value) {
                $value->translations()->delete();
                $value->options()->delete();
                $value->delete();
            }

            $copy->shippingDimensions()->delete();
            $copy->returnPolicy()?->delete();

            /*
             * Остаточные ссылки на staging-группу не должны
             * оставаться у самой копии.
             */
            $copy->variant_group_id = null;
            $copy->save();

            $copy->delete();

            return array_values(array_unique($mediaIds));
        });

        /*
         * Файлы удаляем только после commit.
         * Записи Media должны существовать до выполнения задания.
         */
        foreach ($mediaToDelete as $mediaId) {
            $media = Media::query()->find($mediaId);

            if ($media) {
                $this->mediaService->delete($media);
            }
        }
    }
}
