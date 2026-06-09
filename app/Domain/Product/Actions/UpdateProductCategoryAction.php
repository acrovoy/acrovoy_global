<?php

namespace App\Domain\Product\Actions;

use App\Domain\Product\DTO\ProductCategoryDTO;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductCategoryAction
{

    public function __construct(

        private SyncProductAttributeAction $attribute,

    ) {}
    public function execute(
        Product $product,
        ProductCategoryDTO $data,



    ): Product {

        return DB::transaction(function () use (
            $product,
            $data,



        ) {

            $oldCategoryId = $product->category_id;


            $product->update([
                'category_id' => $data->categoryId,
            ]);


            /* ============================
             * Attribute Pipeline
             * ============================ */
            if (!empty($data->attributes)) {

                logger()->info('==== Attribute Pipeline Start ====', [
                    'product_id' => $product->id,
                    'current_category_id' => $oldCategoryId,
                    'new_category_id' => $data->categoryId,
                    'current_attributes' => $product->attributes()->with('options')->get()->map(fn($a) => [
                        'id' => $a->id,
                        'value' => $a->value,
                        'options' => $a->options->pluck('name'),
                    ]),
                    'incoming_attributes' => $data->attributes,
                ]);

                // Очистить старые значения атрибутов продукта, если категория поменялась
                if ($oldCategoryId !== $data->categoryId) {

                    // Берём все ProductAttributeValue для продукта
                    $oldValues = $product->attributeValues()
                        ->whereHas('attribute', function ($q) {
                            $q->where('is_custom', 0);
                        })
                        ->with('options', 'translations', 'attribute')
                        ->get();

                    foreach ($oldValues as $pav) {
                        // Удаляем связанные опции (для select/multiselect)
                        $pav->options()->delete();

                        // Удаляем переводы
                        $pav->translations()->delete();

                        // Удаляем саму привязку продукта к атрибуту
                        $pav->delete();
                    }

                    logger()->info('==== Old product attribute values deleted ====', [
                        'deleted_count' => count($oldValues),
                        'product_id' => $product->id,
                    ]);
                }

                // Сохраняем новые атрибуты
                $this->attribute->execute($product, $data->attributes);

                // 🔹 Обновляем отношение, чтобы в логах были реальные данные из базы
                $product->load('attributes.options');

                // Лог после сохранения новых атрибутов
                logger()->info('==== New attributes saved ====', [
                    'product_id' => $product->id,
                    'saved_attributes' => $product->attributes->map(fn($a) => [
                        'id' => $a->id,
                        'value' => $a->value,
                        'options' => $a->options->pluck('name'),
                    ]),
                ]);
            }


            return $product;
        });
    }
}
