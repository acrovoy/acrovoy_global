<?php

namespace App\Domain\Product\Services;

use App\Models\Product;

class ProductListQueryService
{
    
    public function getSupplierProductStatistics(int $supplierId): array
    {
        $statistics = Product::query()
            ->where('supplier_id', $supplierId)
            ->whereNull('original_product_id')
            ->where('status', '!=', 'archived')
            ->selectRaw(
                'COUNT(*) as added_count,
                 SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as published_count',
                ['approved']
            )
            ->first();

        $addedCount = (int) $statistics->added_count;
        $publishedCount = (int) ($statistics->published_count ?? 0);

        return [
            'addedCount' => $addedCount,
            'publishedCount' => $publishedCount,
            'inCatalogCount' => $publishedCount,
            'notInCatalogCount' => max(0, $addedCount - $publishedCount),
            'productLimit' => 10,
        ];
    }


    public function getSupplierProducts(
        int $supplierId,
        array $filters
    ) {
        $query = Product::query()
            ->with([
                'user',
                'creator',
                'category',
                'images',
                'priceTiers',
                'editingCopy',
                'warehouses' => function ($q) {
                    $q->withPivot('quantity');
                },
            ])
            ->where('supplier_id', $supplierId)
            ->whereNull('original_product_id')
            ->where('status', '!=', 'archived');

        
        // Search by translated product name or SKU.
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($translationQuery) use ($search) {
                    $translationQuery->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    );
                })
                ->orWhere('products.sku', 'like', '%' . $search . '%');
            });
        }


        // Filter by status.
        $allowedStatuses = [
            'draft',
            'pending',
            'approved',
            'rejected',
        ];

        if (
            !empty($filters['status'])
            && in_array($filters['status'], $allowedStatuses, true)
        ) {
            $query->where('status', $filters['status']);
        }

        // Filter by category.
        if (
            !empty($filters['category'])
            && ctype_digit((string) $filters['category'])
        ) {
            $query->where('category_id', (int) $filters['category']);
        }

        
        // Filter by creator name using products.created_by.
        if (!empty($filters['user'])) {
            $creatorName = trim($filters['user']);

            $query->whereHas('creator', function ($q) use ($creatorName) {
                $q->where('name', 'like', '%' . $creatorName . '%');
            });
        }


        
        
        // Sorting.
        match ($filters['sort'] ?? 'newest') {
            'oldest' => $query->orderBy('products.created_at', 'asc'),

            'name_asc' => $query->orderBy(
                \App\Models\ProductTranslation::query()
                    ->select('name')
                    ->whereColumn(
                        'product_translations.product_id',
                        'products.id'
                    )
                    ->where('locale', 'en')
                    ->limit(1),
                'asc'
            ),

            'name_desc' => $query->orderBy(
                \App\Models\ProductTranslation::query()
                    ->select('name')
                    ->whereColumn(
                        'product_translations.product_id',
                        'products.id'
                    )
                    ->where('locale', 'en')
                    ->limit(1),
                'desc'
            ),

            'status' => $query
                ->orderBy('products.status')
                ->orderByDesc('products.created_at'),

            default => $query->orderBy('products.created_at', 'desc'),
        };


        return $query->paginate(10)->withQueryString();
    }
}
