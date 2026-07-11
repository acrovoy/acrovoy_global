<?php

namespace App\Domain\RFQ\Enums;

enum RfqStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case IN_NEGOTIATION = 'in_negotiation';
    case CLOSED = 'closed';

    

    public function canPublish(): bool
    {
        return $this === self::DRAFT;
    }

    public function canClose(): bool
    {
        return match($this) {
            self::PUBLISHED,
            self::IN_NEGOTIATION => true,
            default => false,
        };
    }

    public function canEdit(): bool
    {
        return $this === self::DRAFT;
    }

    public function canReceiveOffers(): bool
    {
        return match($this) {
            self::PUBLISHED,
            self::IN_NEGOTIATION => true,
            default => false,
        };
    }

    public function badgeClasses(): string
{
    return match($this) {
        self::DRAFT => 'uppercase text-gray-700',
        self::PUBLISHED => 'uppercase text-blue-500',
        self::IN_NEGOTIATION => 'uppercase text-amber-700',
        self::CLOSED => 'uppercase text-zinc-700',
    };
}

public function badgeIndexClasses(): string
{
    return match($this) {
        self::DRAFT => 'bg-gray-100 text-gray-600',
        self::PUBLISHED => 'bg-blue-100 text-blue-700',
        self::IN_NEGOTIATION => 'bg-yellow-100 text-yellow-800',
        self::CLOSED => 'bg-green-100 text-green-700',
    };
}

public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::IN_NEGOTIATION => 'In negotiation',
            self::CLOSED => 'Closed',
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

public function canEditFull(): bool
{
    return $this === self::DRAFT;
}

public function canAddParticipants(): bool
{
    return in_array($this, [
        self::DRAFT,
        self::PUBLISHED,
    ]);
}

public function isLocked(): bool
{
    return $this !== self::DRAFT;
}

}