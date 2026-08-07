<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy extends BasePolicy
{
    /**
     * View product list.
     */
    public function viewAny(User $user): bool
{
    return $this->isSupplier()
        && $this->supplier() !== null;
}

    /**
     * View product.
     */
    public function view(User $user, Product $product): bool
    {
        return $this->ownsSupplier($product->supplier);
    }

    /**
     * Create product.
     */
    public function create(User $user): bool
    {
        if (!$this->supplier()) {
            return false;
        }

        if ($this->isPersonal()) {
            return true;
        }

        return in_array(
            $this->companyRole(),
            [
                'owner',
                'administrator',
                'sales',
            ],
            true
        );
    }

    /**
     * Update product.
     */
    public function update(User $user, Product $product): bool
    {
        if (!$this->ownsSupplier($product->supplier)) {
            return false;
        }

        if ($this->isPersonal()) {
            return true;
        }

        return in_array(
            $this->companyRole(),
            [
                'owner',
                'administrator',
                'sales',
            ],
            true
        );
    }

    /**
     * Delete product.
     */
    public function delete(User $user, Product $product): bool
    {
        if (!$this->ownsSupplier($product->supplier)) {
            return false;
        }

        if ($this->isPersonal()) {
            return true;
        }

        return in_array(
            $this->companyRole(),
            [
                'owner',
                'administrator',
            ],
            true
        );
    }

    /**
     * Restore.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Force delete.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}