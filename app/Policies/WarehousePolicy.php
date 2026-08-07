<?php

namespace App\Policies;

use App\Models\User;

use App\Models\Warehouse;

class WarehousePolicy extends BasePolicy
{

    /**
     * View warehouse list
     */
    public function viewAny(): bool
    {
        return $this->isSupplier()
            && $this->supplier() !== null;
    }


    /**
     * View single warehouse
     */
    public function view(User $user, Warehouse $warehouse): bool
    {
        return $this->ownsWarehouse($warehouse);
    }


    /**
     * Create warehouse
     */
    public function create(): bool
    {
        if (!$this->isSupplier()) {
            return false;
        }


        if (!$this->supplier()) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | Personal supplier
        |--------------------------------------------------------------------------
        */

        if ($this->isPersonal()) {
            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | Company supplier
        |--------------------------------------------------------------------------
        */

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
     * Update warehouse
     */
    public function update(User $user, Warehouse $warehouse): bool
    {
        if (!$this->ownsWarehouse($warehouse)) {
            return false;
        }


        /*
        |--------------------------------------------------------------------------
        | Personal supplier
        |--------------------------------------------------------------------------
        */

        if ($this->isPersonal()) {
            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | Company supplier
        |--------------------------------------------------------------------------
        */

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
     * Delete warehouse
     */
    public function delete(User $user, Warehouse $warehouse): bool
    {
        if (!$this->ownsWarehouse($warehouse)) {
            return false;
        }


        return $this->isPersonal()
            ||
            $this->isCompanyManager();
    }



    /**
     * Restore
     */
    public function restore(User $user, Warehouse $warehouse): bool
    {
        return false;
    }



    /**
     * Force delete
     */
    public function forceDelete(User $user, Warehouse $warehouse): bool
    {
        return false;
    }



    /**
     * Check ownership
     */
    protected function ownsWarehouse(Warehouse $warehouse): bool
    {
        $supplier = $this->supplier();


        if (!$supplier) {
            return false;
        }


        return $warehouse->provider_type === get_class($supplier)
            &&
            $warehouse->provider_id === $supplier->id;
    }

}