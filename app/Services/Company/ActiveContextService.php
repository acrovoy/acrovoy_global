<?php

namespace App\Services\Company;

use App\Models\CompanyUser;
use App\Models\Supplier;
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
        if (!$type || $type === 'personal' || !$id) {

            $this->context = [
                'mode' => 'personal',
                'user' => $user,
                'company_id' => null,
                'company_type' => null,
                'role' => 'buyer',
            ];

            return;
        }

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

            $this->context = [
                'mode' => 'personal',
                'user' => $user,
                'company_id' => null,
                'company_type' => null,
                'role' => 'buyer',
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

    /**
     * SUPPLIER ID SAFE ACCESS
     */
    public function supplierId(): ?int
    {
        return $this->supplier()?->id;
    }
}