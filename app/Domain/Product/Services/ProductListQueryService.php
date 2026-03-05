<?php

namespace App\Domain\Product\Services;

use App\Models\Product;

class ProductListQueryService
{
    public function getSupplierProducts($supplierId, array $filters)
    {
        $query = Product::query()
            ->with([
                'user',
                'category',
                'images',
                'priceTiers',
                'images',
            ])
            ->where('supplier_id', $supplierId);

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

        return $query->get();
    }
}