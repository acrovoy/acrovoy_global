<?php

namespace App\Domain\RFQ\Enums;

enum RfqVisibilityType: string
{
    case PRIVATE = 'private';
    case CATEGORY = 'category';
    case PLATFORM = 'platform';
    case OPEN = 'open';

    /**
     * UI label (для Blade)
     */
    public function label(): string
    {
        return match ($this) {
            self::PRIVATE => 'Private',
            self::CATEGORY => 'Category',
            self::PLATFORM => 'Platform',
            self::OPEN => 'Open',
        };
    }

    /**
     * Badge styling (если понадобится)
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::PRIVATE => 'bg-gray-100 text-gray-700',
            self::CATEGORY => 'bg-blue-100 text-blue-700',
            self::PLATFORM => 'bg-purple-100 text-purple-700',
            self::OPEN => 'bg-green-100 text-green-700',
        };
    }

    /**
     * Business logic helpers
     */
    public function isPublic(): bool
    {
        return in_array($this, [
            self::PLATFORM,
            self::OPEN,
        ]);
    }

    public function isRestricted(): bool
    {
        return in_array($this, [
            self::PRIVATE,
            self::CATEGORY,
        ]);
    }
}