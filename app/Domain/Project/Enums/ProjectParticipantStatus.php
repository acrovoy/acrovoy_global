<?php

namespace App\Domain\Project\Enums;

enum ProjectParticipantStatus: string
{
    case INVITED = 'invited';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
    case VIEWED = 'viewed';
    case REMOVED = 'removed';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::INVITED =>
                'bg-amber-50 text-amber-700 border border-amber-100',

            self::ACCEPTED =>
                'bg-emerald-50 text-emerald-700 border border-emerald-100',

            self::DECLINED =>
                'bg-red-50 text-red-700 border border-red-100',

            self::VIEWED =>
                'bg-blue-50 text-blue-700 border border-blue-100',

            self::REMOVED =>
                'bg-gray-100 text-gray-500 border border-gray-200',
        };
    }

    public function baseBadge(): string
    {
        return 'text-xs px-2.5 py-1 rounded font-medium';
    }

    public function badge(): string
    {
        return $this->baseBadge() . ' ' . $this->badgeClasses();
    }

    public function isInvited(): bool
    {
        return $this === self::INVITED;
    }

    public function isAccepted(): bool
    {
        return $this === self::ACCEPTED;
    }

    public function isDeclined(): bool
    {
        return $this === self::DECLINED;
    }

    public function isViewed(): bool
    {
        return $this === self::VIEWED;
    }

    public function isRemoved(): bool
    {
        return $this === self::REMOVED;
    }
}