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
        if (!$this->isCompany()) {
            return null;
        }

        if ($this->type() !== Supplier::class) {
            return null;
        }

        return $this->company();
    }

    public function supplierParticipant(): ?array
{
    if (!$this->isSupplier()) {
        return null;
    }

    return [
        'type' => $this->isCompany()
            ? $this->type()          // Supplier::class / Company class
            : User::class,

        'id' => $this->id(),
    ];
}

    public function buyer(): ?Buyer
    {
        if (!$this->isCompany()) {
            return null;
        }

        if ($this->type() !== Buyer::class) {
            return null;
        }

        return $this->company();
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
    if (!$this->isSupplier()) {
        return null;
    }

    return $this->id();
}


public function supplierType(): string
{
    return $this->isCompany()
        ? $this->type()
        : User::class;
}

    public function isSupplier(): bool
    {
        if ($this->isCompany()) {
            return $this->type() === \App\Models\Supplier::class;
        }

        return auth()->user()?->setting('platform_mode') === 'supplier';
    }

    public function isBuyer(): bool
    {
        if ($this->isCompany()) {
            return $this->type() === \App\Models\Buyer::class;
        }

        return auth()->user()?->setting('platform_mode') === 'buyer';
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

        'role' => $this->role(),

        'entity_type' => $this->isCompany()
            ? $this->type()
            : User::class,

        'entity_id' => $this->isCompany()
            ? $this->id()
            : $this->user()?->id,
    ];
}


}
