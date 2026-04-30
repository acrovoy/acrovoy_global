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
        self::DRAFT => 'uppercase text-gray-700',
        self::PUBLISHED => ' uppercase text-blue-500',
        self::IN_NEGOTIATION => 'uppercase text-amber-700',
        self::CLOSED => 'uppercase text-zinc-700',
    };
}


}