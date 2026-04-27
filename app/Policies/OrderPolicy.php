<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Services\Company\ActiveContextService;

class OrderPolicy
{
    protected ActiveContextService $context;

    public function __construct(ActiveContextService $context)
    {
        $this->context = $context;
    }


    /**
     * Просмотр списка заказов
     */
    public function viewAny(User $user): bool
    {
        return true;
    }


    /**
     * Просмотр заказа
     */
    public function view(User $user, Order $order): bool
    {
        // platform admin
        if ($this->context->role() === 'admin') {
            return true;
        }

        // buyer (personal mode)
        if ($order->user_id === $user->id) {
            return true;
        }

        // supplier company
        if ($this->context->isCompany()) {

            $company = $this->context->company();

            if (
                $order->supplier_id === $company->id ||
                $order->logistic_company_id === $company->id
            ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Создание заказа
     */
    public function create(User $user): bool
    {
        return $this->context->role() === 'buyer';
    }


    /**
     * Обновление заказа
     */
    public function update(User $user, Order $order): bool
    {
        // supplier может обновлять свой заказ
        if ($this->context->isCompany()) {

            $company = $this->context->company();

            return $order->supplier_id === $company->id;
        }

        return false;
    }


    /**
     * Удаление заказа
     */
    public function delete(User $user, Order $order): bool
    {
        return false;
    }


    public function restore(User $user, Order $order): bool
    {
        return false;
    }


    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }


    /**
     * Покупатель может оставить отзыв
     */
    public function review(User $user, Order $order): bool
    {
        return
            $order->user_id === $user->id &&
            $order->status === 'completed';
    }


    /**
     * Покупатель может открыть спор
     */
    public function dispute(User $user, Order $order): bool
    {
        return
            $order->user_id === $user->id &&
            $order->status === 'completed';
    }
}