<?php

namespace App\Policies;

use App\Models\User;
use App\Facades\ActiveContext;

class TeamPolicy
{
    /*
    |--------------------------------------------------------------------------
    | COMPANY CONTEXT
    |--------------------------------------------------------------------------
    */

    /**
     * Проверка: пользователь находится в company mode
     */
    private function inCompany(): bool
    {
        return ActiveContext::mode() === 'company';
    }

    /**
     * Текущая роль пользователя в активной компании
     */
    private function role(): ?string
    {
        return ActiveContext::role();
    }

    /*
    |--------------------------------------------------------------------------
    | ROLES (HIERARCHY LOGIC)
    |--------------------------------------------------------------------------
    |
    | 👑 OWNER
    | - полный контроль над системой
    | - управление компанией, финансами, пользователями
    |
    | 🛡 ADMINISTRATOR
    | - операционное управление
    | - товары, заказы, RFQ, сотрудники (частично)
    | - без права удаления компании и смены owner
    |
    | 🛒 SALES
    | - работа с клиентами и RFQ
    | - создание офферов
    | - доступ к чатам
    | - без финансов и настроек компании
    |
    | 🏭 OPERATOR
    | - производство и выполнение заказов
    | - изменение статусов производства
    | - без RFQ создания и финансов
    |
    | 🚚 LOGISTICS
    | - доставка и логистика
    | - шаблоны доставки, трекинг
    | - без финансов
    |
    | 💰 ACCOUNTANT
    | - финансы, счета, платежи
    | - отчётность
    | - без товаров, RFQ и заказов
    */

    private function isOwner(): bool
    {
        return $this->role() === 'owner';
    }

    private function isAdmin(): bool
    {
        return in_array($this->role(), ['owner', 'administrator']);
    }

    private function isSales(): bool
    {
        return in_array($this->role(), ['owner', 'administrator', 'sales']);
    }

    private function isOperator(): bool
    {
        return in_array($this->role(), ['owner', 'administrator', 'operator']);
    }

    private function isLogistics(): bool
    {
        return in_array($this->role(), ['owner', 'administrator', 'logistics']);
    }

    private function isAccountant(): bool
    {
        return in_array($this->role(), ['owner', 'administrator', 'accountant']);
    }

    private function isMember(): bool
    {
        return in_array($this->role(), [
            'owner',
            'administrator',
            'sales',
            'operator',
            'logistics',
            'accountant'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TEAM MANAGEMENT (USERS & ROLES)
    |--------------------------------------------------------------------------
    */

    /**
     * Просмотр списка участников команды
     * OWNER + ADMIN
     */
    public function viewAny(User $user): bool
    {
        return $this->inCompany() && $this->isAdmin();
    }

    /**
     * Просмотр участника команды
     * OWNER + ADMIN
     */
    public function view(User $user): bool
    {
        return $this->inCompany() && $this->isAdmin();
    }

    /**
     * Приглашение новых пользователей
     * OWNER + ADMIN
     */
    public function invite(User $user): bool
    {
        return $this->inCompany() && $this->isAdmin();
    }

    /**
     * Изменение ролей сотрудников
     * OWNER + ADMIN
     */
    public function updateRole(User $user): bool
    {
        return $this->inCompany() && $this->isAdmin();
    }

    /**
     * Удаление сотрудников из компании
     * OWNER + ADMIN
     */
    public function remove(User $user): bool
    {
        return $this->inCompany() && $this->isAdmin();
    }

    /**
     * Запрет удаления владельца
     */
    public function removeOwner(User $user): bool
    {
        return false;
    }

    /**
     * Выход из компании (самостоятельно)
     * все роли кроме OWNER
     */
    public function leave(User $user): bool
    {
        return $this->inCompany() && !$this->isOwner();
    }

    /**
     * Управление ролями (создание / изменение системы ролей)
     * только OWNER
     */
    public function manageRoles(User $user): bool
    {
        return $this->inCompany() && $this->isOwner();
    }

    /*
    |--------------------------------------------------------------------------
    | MODULE ACCESS HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * SALES MODULE
     * RFQ, offers, chat
     */
    public function salesAccess(User $user): bool
    {
        return $this->inCompany() && $this->isSales();
    }

    /**
     * PRODUCTION MODULE
     * operator workflow
     */
    public function productionAccess(User $user): bool
    {
        return $this->inCompany() && $this->isOperator();
    }

    /**
     * LOGISTICS MODULE
     */
    public function logisticsAccess(User $user): bool
    {
        return $this->inCompany() && $this->isLogistics();
    }

    /**
     * FINANCE MODULE
     */
    public function accountingAccess(User $user): bool
    {
        return $this->inCompany() && $this->isAccountant();
    }

    /**
     * FULL ACCESS (OWNER ONLY)
     */
    public function fullAccess(User $user): bool
    {
        return $this->inCompany() && $this->isOwner();
    }
}