<?php

namespace App\Policies;

use App\Models\ShippingTemplate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShippingTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShippingTemplate $shippingTemplate): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ShippingTemplate $template)
{
    return $user->id === $template->manufacturer_id;
}

public function delete(User $user, ShippingTemplate $template)
{
    return $user->id === $template->manufacturer_id;
}

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ShippingTemplate $shippingTemplate): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ShippingTemplate $shippingTemplate): bool
    {
        //
    }
}
