<?php

namespace App\Services;

use App\Models\User;

class PlanService
{
    /**
     * Получить план пользователя
     */
    public static function getPlan(User $user)
    {
        return $user->premiumPlan;
    }

    /**
     * Можно ли добавлять новый продукт
     */
    public static function canAddProduct(User $user): bool
    {
        $plan = self::getPlan($user);
        if (!$plan) return false;

        $features = json_decode($plan->features, true);
        $limit = $features['products_limit'] ?? 0;

        if (is_null($limit)) return true; // Unlimited
        return $user->products()->count() < $limit;
    }

    /**
     * Получить видимость или приоритетное размещение товара
     */
    public static function getPlacement(User $user): ?string
    {
        $plan = self::getPlan($user);
        if (!$plan) return null;

        $features = json_decode($plan->features, true);

        if (!empty($features['priority_placement']) && $features['priority_placement']) {
            return 'top'; // Priority placement
        }

        return $features['visibility'] ?? 'basic';
    }

    /**
     * Есть ли аналитика
     */
    public static function hasAnalytics(User $user): bool
    {
        $plan = self::getPlan($user);
        if (!$plan) return false;

        $features = json_decode($plan->features, true);
        return !empty($features['analytics']);
    }

    /**
     * Проверка доступности функции по ключу
     */
    public static function featureEnabled(User $user, string $key): bool
    {
        $plan = self::getPlan($user);
        if (!$plan) return false;

        $features = json_decode($plan->features, true);
        return !empty($features[$key]);
    }
}
