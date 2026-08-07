<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\Supplier;
use App\Services\Company\ActiveContextService;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    protected ActiveContextService $context;

    public function __construct(ActiveContextService $context)
    {
        $this->context = $context;
    }

    /**
     * Active identity.
     */
    protected function identity(): array
    {
        return $this->context->identity();
    }

    /**
     * Current authenticated user.
     */
    protected function user()
    {
        return $this->context->user();
    }

    /**
     * Current represented entity.
     *
     * User | Supplier | Buyer
     */
    protected function entity(): ?Model
    {
        return $this->context->entity();
    }

    /**
     * Supplier profile of current entity.
     */
    protected function supplier(): ?Supplier
    {
        return $this->context->supplierProfile();
    }

    /**
     * Buyer profile of current entity.
     */
    protected function buyer(): ?Buyer
    {
        return $this->context->buyerProfile();
    }

    /**
     * Identity helpers.
     */
    protected function entityType(): ?string
    {
        return $this->identity()['entity_type'];
    }

    protected function entityId(): ?int
    {
        return $this->identity()['entity_id'];
    }

    protected function platformRole(): ?string
    {
        return $this->identity()['platform_role'];
    }

    protected function companyRole(): ?string
    {
        return $this->identity()['company_role'];
    }

    protected function mode(): string
    {
        return $this->identity()['mode'];
    }

    /**
     * Context checks.
     */
    protected function isGuest(): bool
    {
        return $this->context->isGuest();
    }

    protected function isPersonal(): bool
    {
        return $this->context->isPersonal();
    }

    protected function isCompany(): bool
    {
        return $this->context->isCompany();
    }

    protected function isSupplier(): bool
    {
        return $this->platformRole() === 'supplier';
    }

    protected function isBuyer(): bool
    {
        return $this->platformRole() === 'buyer';
    }

    /**
     * Company role checks.
     */
    protected function isOwner(): bool
    {
        return $this->companyRole() === 'owner';
    }

    protected function isAdministrator(): bool
    {
        return $this->companyRole() === 'administrator';
    }

    protected function isCompanyManager(): bool
    {
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
     * Entity ownership.
     */
    protected function ownsEntity(string $type, int $id): bool
    {
        return $this->entityType() === $type
            && $this->entityId() === $id;
    }

    /**
     * Supplier ownership.
     */
    protected function ownsSupplier(?Supplier $supplier): bool
    {
        return $supplier !== null
            && $this->supplier()?->id === $supplier->id;
    }

    /**
     * Buyer ownership.
     */
    protected function ownsBuyer(?Buyer $buyer): bool
    {
        return $buyer !== null
            && $this->buyer()?->id === $buyer->id;
    }
}