<?php

namespace App\Domain\Project\Enums;

enum ProjectStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case COMPLETED = 'completed';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::COMPLETED => 'Completed',
            self::CLOSED => 'Closed',
            self::ARCHIVED => 'Archived',
        };
    }


    public function badgeClasses(): string
{
    return match ($this) {
        self::DRAFT => 'uppercase text-gray-700',
        self::PUBLISHED => 'uppercase text-blue-700',
        self::COMPLETED => 'uppercase text-green-700',
        self::CLOSED => 'uppercase text-red-700',
        self::ARCHIVED => 'uppercase text-gray-500',
    };
}

public function badgeIndexClasses(): string
{
    return match($this) {
        self::DRAFT => 'bg-gray-100 text-gray-600',
        self::PUBLISHED => 'bg-blue-100 text-blue-700',
        self::COMPLETED => 'bg-yellow-100 text-yellow-700',
        self::ARCHIVED => 'bg-gray-100 text-gray-700',
        self::CLOSED => 'bg-green-100 text-green-700',
    };
}

public function isDraft(): bool
{
    return $this === self::DRAFT;
}

public function isPublished(): bool
{
    return $this === self::PUBLISHED;
}

public function isClosed(): bool
{
    return $this === self::CLOSED;
}


}