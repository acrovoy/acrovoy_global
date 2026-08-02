<?php

namespace App\Services\Context;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActiveEntityContext
{
    protected ?array $context = null;
    protected ?Model $resolvedEntity = null;

    public function resolve(): void
    {
        if (!Auth::check()) {
            $this->context = [
                'mode' => 'guest',
                'user_id' => null,
                'platform_role' => null,
                'organization_role' => null,
                'entity_type' => null,
                'entity_id' => null,
            ];

            return;
        }

        $this->context = session('active_context', [
            'mode' => 'guest',
            'user_id' => Auth::id(),
            'platform_role' => null,
            'organization_role' => null,
            'entity_type' => null,
            'entity_id' => null,
        ]);
    }

    protected function ctx(): array
    {
        if ($this->context === null) {
            $this->resolve();
        }

        return $this->context;
    }

    public function user()
    {
        return Auth::user();
    }

    public function userId(): ?int
    {
        return $this->ctx()['user_id'];
    }

    public function mode(): string
    {
        return $this->ctx()['mode'];
    }

    public function isGuest(): bool
    {
        return $this->mode() === 'guest';
    }

    public function isPersonal(): bool
    {
        return $this->mode() === 'personal';
    }

    public function isOrganization(): bool
    {
        return $this->mode() === 'organization';
    }

    public function platformRole(): ?string
    {
        return $this->ctx()['platform_role'];
    }

    public function organizationRole(): ?string
    {
        return $this->ctx()['organization_role'];
    }

    public function isSupplier(): bool
    {
        return $this->platformRole() === 'supplier';
    }

    public function isBuyer(): bool
    {
        return $this->platformRole() === 'buyer';
    }

    public function isLogistics(): bool
    {
        return $this->platformRole() === 'logistics';
    }

    public function entityType(): ?string
    {
        return $this->ctx()['entity_type'];
    }

    public function entityId(): ?int
    {
        return $this->ctx()['entity_id'];
    }

    public function hasEntity(): bool
    {
        return $this->entityType() !== null
            && $this->entityId() !== null;
    }

    public function entity(): ?Model
    {
        if (!$this->hasEntity()) {
            return null;
        }

        if ($this->resolvedEntity !== null) {
            return $this->resolvedEntity;
        }

        $class = $this->entityType();

        if (!class_exists($class)) {
            return null;
        }

        return $this->resolvedEntity = $class::find($this->entityId());
    }

    public function participant(): ?array
    {
        if (!$this->hasEntity()) {
            return null;
        }

        return [
            'type' => $this->entityType(),
            'id' => $this->entityId(),
        ];
    }

    public function identity(): array
    {
        return [
            'user_id' => $this->userId(),
            'mode' => $this->mode(),
            'platform_role' => $this->platformRole(),
            'organization_role' => $this->organizationRole(),
            'entity_type' => $this->entityType(),
            'entity_id' => $this->entityId(),
        ];
    }

    public function setContext(array $context): static
    {
        session([
            'active_context' => $context,
        ]);

        $this->context = $context;
        $this->resolvedEntity = null;

        return $this;
    }

    public function refresh(): static
    {
        $this->context = null;
        $this->resolvedEntity = null;

        $this->resolve();

        return $this;
    }

    public function clear(): static
    {
        session()->forget('active_context');

        $this->context = null;
        $this->resolvedEntity = null;

        return $this;
    }
}