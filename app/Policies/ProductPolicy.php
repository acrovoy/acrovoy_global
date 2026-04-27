<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\Supplier;
use App\Services\Company\ActiveContextService;

class ProductPolicy
{
    /**
     * View list of products
     */
    public function viewAny(User $user): bool
    {
        $context = app(ActiveContextService::class);

        return $context->isCompany()
            && $context->type() === Supplier::class;
    }


    /**
     * View single product
     */
    public function view(User $user, Product $product): bool
    {
        $context = app(ActiveContextService::class);

        return $context->isCompany()
            && $context->type() === Supplier::class
            && $product->supplier_id === $context->id();
    }


    /**
     * Create product
     */
    public function create(User $user): bool
    {
        $context = app(ActiveContextService::class);

        return $context->isCompany()
            && $context->type() === Supplier::class
            && in_array($context->role(), [
                'owner',
                'administrator',
                'sales'
            ]);
    }


    /**
     * Update product
     */
    public function update(User $user, Product $product): bool
    {

    
        $context = app(ActiveContextService::class);

        return $context->isCompany()
            && $context->type() === Supplier::class
            && $product->supplier_id === $context->id()
            && in_array($context->role(), [
                'owner',
                'administrator',
                'sales'
            ]);
    }


    /**
     * Delete product
     */
    public function delete(User $user, Product $product): bool
    {
        $context = app(ActiveContextService::class);

        return $context->isCompany()
            && $context->type() === Supplier::class
            && $product->supplier_id === $context->id()
            && in_array($context->role(), [
                'owner',
                'administrator'
            ]);
    }


    /**
     * Restore product
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }


    /**
     * Force delete product permanently
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}