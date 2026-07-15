<?php

namespace App\Domain\Conversation\Enums;

enum ConversationType: string
{
    /**
     * Личная переписка между пользователями.
     */
    case PRIVATE = 'private';

    /**
     * Переписка, привязанная к бизнес-сущности
     * (Product, RFQ, Project, Order и т.д.).
     */
    case BUSINESS = 'business';

    /**
     * Групповой чат.
     * Зарезервировано на будущее.
     */
    case GROUP = 'group';

    /**
     * Системный диалог.
     */
    case SYSTEM = 'system';

    /**
     * Переписка со службой поддержки.
     */
    case SUPPORT = 'support';

    /**
     * Все доступные значения.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Для select и UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::PRIVATE => 'Private',
            self::BUSINESS => 'Business',
            self::GROUP => 'Group',
            self::SYSTEM => 'System',
            self::SUPPORT => 'Support',
        };
    }
}