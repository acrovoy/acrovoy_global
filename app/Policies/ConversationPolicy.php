<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class ConversationPolicy extends BasePolicy
{
    /**
     * Buyer can start a conversation with Supplier.
     */
    public function contactSupplier(
        User $user,
        Supplier $supplier
    ): bool {
        if (!$this->isBuyer()) {
            return false;
        }

        return $supplier->exists;
    }
}