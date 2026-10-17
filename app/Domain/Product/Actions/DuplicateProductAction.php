<?php

namespace App\Domain\Product\Actions;

use App\Domain\Media\Contracts\StorageInterface;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\CdnUrlService;
use App\Domain\Media\Services\MediaProcessingService;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DuplicateProductAction
{
    public function execute(Product $source, int $createdBy): Product
    {
        return DB::transaction(function () use ($source, $createdBy) {
            $original = Product::query()
                ->whereKey($source->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($original->original_product_id !== null) {
                throw ValidationException::withMessages([
                    'product' => 'Only an original product can be duplicated.',
                ]);
            }

            if ($original->status === 'archived') {
                throw ValidationException::withMessages([
                    'product' => 'Archived products cannot be duplicated.',
                ]);
            }

            $original->load([
                'translations',
                'priceTiers',
                'materials',
                'shippingTemplates',
                'paymentMethods',
                'paymentTerms',
                'shippingDimensions',
                'returnPolicy',
                'attributeValues.translations',
                'attributeValues.options',
                'warehouses',
            ]);

            $copy = $original->replicate([
                'slug',
                'status',
                'reject_reason',
                'created_at',
                'updated_at',
                'original_product_id',
                'sort_order',
                'created_by',
                'variant_group_id',
            ]);

            $copy->original_product_id = null;
            $copy->variant_group_id = null;
            $copy->slug = $this->generateUniqueSlug($original);
            $copy->status = 'draft';
            $copy->reject_reason = null;
            $copy->created_by = $createdBy;

            $copy->sort_order = ((int) Product::query()
                ->where('supplier_id', $original->supplier_id)
                ->whereNull('original_product_id')
                ->where('status', '!=', 'archived')
                ->max('sort_order')) + 1;

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
            $this->copyWarehouses($original, $copy);
            

            $this->normalizeSortOrder($copy);

            return $copy->fresh();
        });
    }

    private function generateUniqueSlug(Product $original): string
    {
        do {
            $slug = Str::slug($original->slug)
                . '-copy-'
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

    private function copyShippingTemplates(
        Product $original,
        Product $copy
    ): void {
        $copy->shippingTemplates()->sync(
            $original->shippingTemplates()
                ->pluck('shipping_templates.id')
                ->all()
        );
    }

    private function copyPaymentMethods(
        Product $original,
        Product $copy
    ): void {
        $copy->paymentMethods()->sync(
            $this->getPivotData(
                $original->paymentMethods,
                ['sort_order']
            )
        );
    }

    private function copyPaymentTerms(
        Product $original,
        Product $copy
    ): void {
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

    private function copyReturnPolicy(
        Product $original,
        Product $copy
    ): void {
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

    private function copyWarehouses(
        Product $original,
        Product $copy
    ): void {
        $copy->warehouses()->sync(
            $this->getPivotData(
                $original->warehouses,
                ['quantity']
            )
        );
    }

    
    private function normalizeSortOrder(Product $copy): void
{
    $ids = Product::query()
        ->where('supplier_id', $copy->supplier_id)
        ->whereNull('original_product_id')
        ->where('status', '!=', 'archived')
        ->where('id', '!=', $copy->id)
        ->orderBy('sort_order', 'asc')
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->lockForUpdate()
        ->pluck('id')
        ->map(fn ($id) => (int) $id)
        ->all();

    // The newly duplicated product must be first.
    array_unshift($ids, (int) $copy->id);

    foreach ($ids as $index => $id) {
        DB::table('products')
            ->where('id', $id)
            ->update([
                'sort_order' => $index + 1,
            ]);
    }
}
}
