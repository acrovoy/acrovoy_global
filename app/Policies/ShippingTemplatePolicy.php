<?php

namespace App\Policies;

use App\Models\ShippingTemplate;
use App\Models\User;
use App\Services\Company\ActiveContextService;

class ShippingTemplatePolicy
{
    public function update(User $user, ShippingTemplate $template): bool
    {
        $context = app(ActiveContextService::class);

        if (!$context->isCompany()) {
            return false;
        }

        return $template->manufacturer_id === $context->id();
    }

    public function delete(User $user, ShippingTemplate $template): bool
    {
        $context = app(ActiveContextService::class);

        if (!$context->isCompany()) {
            return false;
        }

        return $template->manufacturer_id === $context->id();
    }

    public function view(User $user, ShippingTemplate $template): bool
    {
        $context = app(ActiveContextService::class);

        if (!$context->isCompany()) {
            return false;
        }

        return $template->manufacturer_id === $context->id();
    }

    public function create(User $user): bool
    {
        $context = app(ActiveContextService::class);

        return $context->isCompany();
    }

    public function viewAny(User $user): bool
    {
        $context = app(ActiveContextService::class);

        return $context->isCompany();
    }
}