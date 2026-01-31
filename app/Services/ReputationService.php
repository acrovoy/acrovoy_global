<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierReputationLog;

class ReputationService
{
    /**
     * Пересчёт репутации поставщика с логированием
     * 
     * @param Supplier $supplier
     * @return int Новый репутационный балл
     */
    public function recalculate(Supplier $supplier): int
    {
        $score = 0;

        /**
         * 1️⃣ Базовые бонусы за профиль
         */
        if ($supplier->logo) {
            $scoreChange = 1;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Наличие логотипа');
        }

        if ($supplier->catalog_image) {
            $scoreChange = 1;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Наличие каталога');
        }

        $certCount = $supplier->certificates()->count();
        if ($certCount > 0) {
            $score += $certCount;
            $this->log($supplier, $certCount, 'Сертификаты');
        }

        if ($supplier->isProfileComplete()) {
            $scoreChange = 1;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Полный профиль');
        }

        /**
         * 2️⃣ Активность на платформе
         */
        $recentResponses = $supplier->rfqResponses()->where('response_time', '<=', 24)->count();
        if ($recentResponses > 0) {
            $score += $recentResponses;
            $this->log($supplier, $recentResponses, 'Быстрые ответы на RFQ (<24ч)');
        }

        $productCount = $supplier->products()->count();
        if ($productCount >= 10) {
            $scoreChange = 2;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Добавлено 10+ товаров');
        } elseif ($productCount >= 5) {
            $scoreChange = 1;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Добавлено 5+ товаров');
        }

        /**
         * 3️⃣ Заказы и сделки
         */
        $completedOrders = $supplier->orders()->where('status', 'completed')->get();
        foreach ($completedOrders as $order) {
            $scoreChange = 2;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Завершённый заказ', $order->id);
        }

        $lateOrders = $supplier->orders()
            ->where('status', 'completed')
            ->whereColumn('delivered_at', '>', 'due_date')
            ->get();
        foreach ($lateOrders as $order) {
            $scoreChange = -3;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Опоздание с доставкой', $order->id);
        }

        $canceledOrders = $supplier->orders()->where('status', 'canceled')->get();
        foreach ($canceledOrders as $order) {
            $scoreChange = -5;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Отмена заказа', $order->id);
        }

        /**
         * 4️⃣ Комиссия
         */
        $ordersWithPaidCommission = $supplier->orders()->where('commission_paid', true)->get();
        foreach ($ordersWithPaidCommission as $order) {
            $scoreChange = 5;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Оплата комиссии', $order->id);
        }

        /**
         * 5️⃣ Отзывы покупателей
         */
        $positiveReviews = $supplier->reviews()->where('rating', '>=', 4)->get();
        foreach ($positiveReviews as $review) {
            $scoreChange = 3;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Положительный отзыв', null);
        }

        $negativeReviews = $supplier->reviews()->where('rating', '<', 4)->get();
        foreach ($negativeReviews as $review) {
            $scoreChange = -2;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Негативный отзыв', null);
        }

        /**
         * 6️⃣ Решение споров
         */
        $resolvedDisputes = $supplier->disputes()->where('status', 'resolved')->get();
        foreach ($resolvedDisputes as $dispute) {
            $scoreChange = 3;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Решённый спор', null);
        }

        $unresolvedDisputes = $supplier->disputes()->where('status', 'open')->get();
        foreach ($unresolvedDisputes as $dispute) {
            $scoreChange = -5;
            $score += $scoreChange;
            $this->log($supplier, $scoreChange, 'Открытый спор', null);
        }

        /**
         * 7️⃣ Ограничение минимального значения
         */
        if ($score < 0) {
            $score = 0;
        }

        // Сохраняем итоговую репутацию
        $supplier->reputation = $score;
        $supplier->save();

        return $score;
    }

    /**
     * Логирование изменений репутации
     */
    protected function log(Supplier $supplier, int $scoreChange, string $reason, int $orderId = null)
    {
        SupplierReputationLog::create([
            'supplier_id' => $supplier->id,
            'score_change' => $scoreChange,
            'reason' => $reason,
            'order_id' => $orderId,
        ]);
    }
}
