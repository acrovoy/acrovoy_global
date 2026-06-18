<?php

namespace App\Domain\Product\Services;

use App\Models\Product;

class ProductListQueryService
{
    public function getSupplierProducts(
        int $supplierId,
        string $supplierType,
        array $filters
    ) {
        $query = Product::query()
            ->with([
                'user',
                'category',
                'images',
                'priceTiers',
                'warehouses' => function ($q) {
        $q->withPivot('quantity');
    },
            ])
            ->where('supplier_id', $supplierId)
            ->where('supplier_type', $supplierType);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['user']}%");
            });
        }

        match ($filters['sort'] ?? null) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'status' => $query->orderBy('status'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return $query->paginate(10)->withQueryString();
    }
}