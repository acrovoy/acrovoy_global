<?php

namespace App\Services\Company;

use App\Models\CompanyUser;
use App\Models\Supplier;
use App\Models\Buyer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActiveContextService
{
    private ?array $context = null;

    private ?Model $resolvedCompany = null;

    /**
     * BUILD CONTEXT
     */
    public function resolve(): void
    {
        if (!auth()->check()) {

            $this->context = [
                'mode' => 'guest',
                'user' => null,
                'company_id' => null,
                'company_type' => null,
                'role' => null,
            ];

            return;
        }

        $user = auth()->user();

        $type = session('active_company_type');
        $id   = session('active_company_id');

        /**
         * PERSONAL MODE
         */
        $mode = session('active_mode', 'personal');

        if ($mode === 'personal') {

            $personal = session('active_personal_mode', 'buyer');

            $this->context = [
                'mode' => 'personal',
                'user' => $user,
                'company_id' => $user->id,
                'company_type' => \App\Models\User::class,
                'role' => $personal,
            ];

            return;
        }


        if ($mode === 'company' && $type && $id) {

            /**
             * COMPANY MODE
             */
            $membership = CompanyUser::query()
                ->where('user_id', $user->id)
                ->where('company_id', $id)
                ->where('company_type', $type)
                ->where('status', 'active')
                ->first();

            /**
             * fallback → если нет доступа
             */
            if (!$membership) {

                $personal = session('active_personal_mode', $user->setting('platform_mode', 'buyer'));

                $this->context = [
                    'mode' => 'personal',
                    'user' => $user,
                    'company_id' => null,
                    'company_type' => null,
                    'role' => $personal,
                ];

                return;
            }

            /**
             * COMPANY CONTEXT
             */
            $this->context = [
                'mode' => 'company',
                'user' => $user,
                'company_id' => $id,
                'company_type' => $type,
                'role' => $membership->role,
            ];
        } else {
            $this->fallbackPersonal($user);
        }
    }

    /**
     * SAFE CONTEXT ACCESS
     */
    private function ctx(): array
    {
        if ($this->context === null) {
            $this->resolve();
        }

        return $this->context;
    }

    /**
     * USER
     */
    public function user()
    {
        return $this->ctx()['user'];
    }

    /**
     * MODE CHECKS
     */
    public function isPersonal(): bool
    {
        return $this->ctx()['mode'] === 'personal';
    }

    public function isCompany(): bool
    {
        return $this->ctx()['mode'] === 'company';
    }

    public function isGuest(): bool
    {
        return $this->ctx()['mode'] === 'guest';
    }

    /**
     * COMPANY ID
     */
    public function id(): ?int
    {
        return $this->ctx()['company_id'];
    }

    /**
     * COMPANY TYPE
     */
    public function type(): ?string
    {
        return $this->ctx()['company_type'];
    }

    /**
     * ROLE
     */
    public function role(): ?string
    {
        return $this->ctx()['role'];
    }

    /**
     * MODE
     */
    public function mode(): string
    {
        return $this->ctx()['mode'];
    }

    /**
     * RESOLVE ACTIVE COMPANY (POLYMORPHIC)
     */
    public function company(): ?Model
    {
        if (!$this->isCompany()) {
            return null;
        }

        if ($this->resolvedCompany !== null) {
            return $this->resolvedCompany;
        }

        return $this->resolvedCompany = CompanyUser::query()
            ->with('company')
            ->where('user_id', auth()->id())
            ->where('company_id', $this->id())
            ->where('company_type', $this->type())
            ->first()
            ?->company;
    }

    /**
     * SUPPLIER CONTEXT (ONLY IF ACTIVE COMPANY IS SUPPLIER)
     */
    public function supplier(): ?Supplier
{
    return $this->supplierProfile();
}

    public function supplierParticipant(): ?Supplier
{
    if (!$this->isSupplier()) {
        return null;
    }

    $supplier = $this->supplierProfile();

    return $supplier instanceof Supplier
        ? $supplier
        : null;
}

    public function buyer(): ?Buyer
{
    return $this->buyerProfile();
}

    public function buyerId(): ?int
    {
        return $this->buyer()?->id;
    }

    /**
     * SUPPLIER ID SAFE ACCESS
     */
    public function supplierId(): ?int
{
    return $this->supplier()?->id;
}




    public function isSupplier(): bool
{
    return $this->supplierProfile() !== null;
}

public function isBuyer(): bool
{
    return $this->buyerProfile() !== null;
}

    private function fallbackPersonal($user): void
    {
        $personalMode = $user->setting('platform_mode', 'buyer');

        $this->context = [
            'mode' => 'personal',
            'user' => $user,
            'company_id' => $user->id,
            'company_type' => \App\Models\User::class,
            'role' => $personalMode,
        ];
    }

    public function identity(): array
{
    return [
        'user_id' => $this->user()?->id,

        'mode' => $this->mode(),

         /*
         |----------------------------------------------------------
         | Роль на платформе
         | buyer / supplier
         |----------------------------------------------------------
         */
        'platform_role' => $this->platformRole(),

        /*
         |----------------------------------------------------------
         | Роль внутри компании
         | owner / sales / administrator / ...
         |----------------------------------------------------------
         */
        'company_role' => $this->companyRole(),

        
        /*
         | Для обратной совместимости
         |----------------------------------------------------------
         */
        'role' => $this->role(),

        /*
         |----------------------------------------------------------
         | Представляемый контекст
         |----------------------------------------------------------
         */
        'entity_type' => $this->entityType(),

'entity_id' => $this->entityId(),
    ];
}

public function entity(): ?Model
{
    if ($this->resolvedCompany !== null) {
        return $this->resolvedCompany;
    }

    /**
     * COMPANY MODE
     */
    if ($this->isCompany()) {
        return $this->resolvedCompany = CompanyUser::query()
            ->with('company')
            ->where('user_id', auth()->id())
            ->where('company_id', $this->id())
            ->where('company_type', $this->type())
            ->first()
            ?->company;
    }

    /**
     * PERSONAL MODE
     *
     * User сам entity НЕ является.
     * Entity определяется активной ролью пользователя.
     */
    if ($this->isPersonal()) {

        return $this->resolvedCompany = match ($this->platformRole()) {

            'supplier' => Supplier::query()
                ->where('supplierable_type', User::class)
                ->where('supplierable_id', $this->user()->id)
                ->first(),

            'buyer' => Buyer::query()
                ->where('buyerable_type', User::class)
                ->where('buyerable_id', $this->user()->id)
                ->first(),

            default => null,
        };
    }

    return null;
}

public function entityType(): ?string
{
    $entity = $this->entity();

    return $entity ? get_class($entity) : null;
}

public function entityId(): ?int
{
    return $this->entity()?->getKey();
}


public function platformRole(): ?string
{
    if ($this->isCompany()) {

        return match ($this->type()) {
            Supplier::class => 'supplier',
            Buyer::class => 'buyer',
            default => null,
        };
    }


    $user = $this->user();

    return session(
        'active_personal_mode',
        $user?->setting('platform_mode', 'buyer')
    );
}

/**
 * owner / administrator / sales / logistics...
 */
public function companyRole(): ?string
{
    if (!$this->isCompany()) {
        return null;
    }

    return $this->ctx()['role'] ?? null;
}


public function participant(): ?array
{
    $entity = $this->entity();

    if (!$entity) {
        return null;
    }

    return [
        'type' => $entity::class,
        'id'   => $entity->getKey(),
    ];
}

public function supplierProfile(): ?Supplier
{
    $entity = $this->entity();

    return $entity instanceof Supplier
        ? $entity
        : null;
}

public function buyerProfile(): ?Buyer
{
    $entity = $this->entity();

    return $entity instanceof Buyer
        ? $entity
        : null;
}


}
