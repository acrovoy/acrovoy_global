<?php

namespace App\Domain\RFQ\Enums;

enum RfqStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case IN_NEGOTIATION = 'in_negotiation';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::IN_NEGOTIATION => 'In negotiation',
            self::CLOSED => 'Closed',
        };
    }

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
        self::DRAFT => 'bg-gray-100 text-gray-700',
        self::PUBLISHED => 'bg-blue-100 text-blue-700',
        self::IN_NEGOTIATION => 'bg-amber-100 text-amber-700',
        self::CLOSED => 'bg-zinc-100 text-zinc-700',
    };
}


}