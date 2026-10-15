<?php

namespace App\Domain\Product\Actions;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaService;
use App\Models\Product;
use App\Models\ProductVariantGroup;
use App\Models\ProductVariantItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteProductAction
{
    public function __construct(
        private MediaService $mediaService
    ) {
    }

    public function execute(Product $product): void
    {
        $mediaIds = DB::transaction(function () use ($product) {
            $product = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($product->original_product_id !== null) {
                throw ValidationException::withMessages([
                    'product' => 'Временные копии нужно удалять через DeleteProductEditingCopyAction.',
                ]);
            }

            /*
             * Не удаляем товары, связанные с операционными данными.
             */
            if (
                $product->orderItems()->exists()
                || $product->reviews()->exists()
                || $product->rfqs()->exists()
            ) {
                throw ValidationException::withMessages([
                    'product' => 'Нельзя удалить товар: с ним связаны заказы, отзывы или RFQ.',
                ]);
            }

            /*
             * Не удаляем оригинал, пока существует его временная копия.
             */
            if ($product->editingCopy()->exists()) {
                throw ValidationException::withMessages([
                    'product' => 'Сначала удалите или обработайте временную копию товара.',
                ]);
            }

            $mediaIds = $product->images()
                ->pluck('id')
                ->all();

            /*
             * Удаляем элементы группы вариаций.
             * Сами связанные товары остаются в каталоге.
             */
            if ($product->variant_group_id) {
                $group = ProductVariantGroup::query()
                    ->whereKey($product->variant_group_id)
                    ->lockForUpdate()
                    ->first();

                if ($group) {
                    $items = ProductVariantItem::query()
                        ->where('variant_group_id', $group->id)
                        ->get();

                    $itemMediaIds = $items
                        ->pluck('media_id')
                        ->filter()
                        ->unique()
                        ->values();

                    $itemIds = $items->pluck('id')->all();

                    /*
                     * Очищаем принадлежность группы у связанных товаров,
                     * кроме удаляемого товара.
                     */
                    Product::query()
                        ->where('variant_group_id', $group->id)
                        ->whereKeyNot($product->id)
                        ->update([
                            'variant_group_id' => null,
                        ]);

                    ProductVariantItem::query()
                        ->where('variant_group_id', $group->id)
                        ->delete();

                    $group->delete();

                    /*
                     * Медиа вариации удаляем, только если больше
                     * ни один элемент на них не ссылается.
                     */
                    foreach ($itemMediaIds as $mediaId) {
                        $stillUsed = ProductVariantItem::query()
                            ->where('media_id', $mediaId)
                            ->exists();

                        if (!$stillUsed) {
                            $mediaIds[] = (int) $mediaId;
                        }
                    }
                }
            }

            /*
             * Удаляем принадлежащие товару записи и связи.
             */
            $product->translations()->delete();
            $product->priceTiers()->delete();
            $product->materials()->detach();
            $product->shippingTemplates()->detach();
            $product->paymentMethods()->detach();
            $product->paymentTerms()->detach();
            $product->wishlistedBy()->detach();
            $product->collections()->detach();

            
            foreach ($product->attributeValues()->get() as $value) {
                $value->translations()->delete();
                $value->options()->delete();
                $value->delete();
            }

            /*
            * Удаляем габариты упаковки товара.
            */
            $product->shippingDimensions()->delete();
            $product->returnPolicy()?->delete();

            /*
             * Не удаляем другие товары, которые могли быть связаны
             * с этим товаром как варианты.
             */
            $product->variant_group_id = null;
            $product->save();

            $product->delete();

            return array_values(array_unique($mediaIds));
        });

        /*
         * Удаление файлов ставим в очередь только после успешного commit.
         */
        foreach ($mediaIds as $mediaId) {
            $media = Media::query()->find($mediaId);

            if ($media) {
                $this->mediaService->delete($media);
            }
        }
    }
}
