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


        /**
     * Contact supplier about product.
     *
     * Buyer can contact the supplier if the product
     * belongs to an active supplier.
     */
    public function contact(User $user, Product $product): bool
    {
        return $product->supplier !== null;
    }


    /**
     * Add product to a project.
     *
     * Only users working in buyer context can add
     * products to their projects.
     */
    public function addToProject(User $user, Product $product): bool
    {
        return $product->supplier !== null
            && $this->isBuyer();
    }


    /**
     * Request product customization.
     *
     * Customization is available only when the supplier
     * offers customization for this product.
     */
    public function customize(User $user, Product $product): bool
    {
        return $product->supplier !== null
            && (bool) $product->customization
            && $this->isBuyer();
    }


    /**
 * Add product to wishlist.
 */
public function addToWishlist(User $user, Product $product): bool
{
    return $this->isBuyer()
        && $this->buyer() !== null;
}


}