<?php

namespace App\Policies;

use App\Models\ShippingTemplate;
use App\Models\User;

class ShippingTemplatePolicy extends BasePolicy
{
    /**
     * View list.
     */
    public function viewAny(): bool
    {
        return $this->isSupplier()
            && $this->supplier() !== null;
    }

    /**
     * View template.
     */
    public function view(User $user, ShippingTemplate $shippingTemplate): bool
    {
        return $this->ownsEntity(
            $shippingTemplate->provider_type,
            $shippingTemplate->provider_id
        );
    }

    /**
     * Create template.
     */
    public function create(User $user): bool
    {
        return $this->entity() !== null;
    }

    /**
     * Update template.
     */
    public function update(User $user, ShippingTemplate $shippingTemplate): bool
    {
        return $this->ownsEntity(
            $shippingTemplate->provider_type,
            $shippingTemplate->provider_id
        );
    }

    /**
     * Delete template.
     */
    public function delete(User $user, ShippingTemplate $shippingTemplate): bool
    {
        return $this->ownsEntity(
            $shippingTemplate->provider_type,
            $shippingTemplate->provider_id
        );
    }

    /**
     * Restore template.
     */
    public function restore(User $user, ShippingTemplate $shippingTemplate): bool
    {
        return $this->delete($user, $shippingTemplate);
    }

    /**
     * Force delete template.
     */
    public function forceDelete(User $user, ShippingTemplate $shippingTemplate): bool
    {
        return false;
    }
}