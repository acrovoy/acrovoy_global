<?php

namespace App\Domain\RFQ\Enums;

enum RfqParticipantStatus: string
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
                'text-gray-500 bg-gray-100 px-2 py-1 rounded',
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
}