<?php

namespace App\Services\Date;

use Carbon\Carbon;

class UserDateFormatter
{
    public function format(
        ?Carbon $date,
        string $timezone,
        string $format = 'd.m.Y H:i'
    ): ?string {

        if (!$date) {
            return null;
        }

        return $date
            ->copy()
            ->timezone($timezone)
            ->format($format);
    }

    /**
     * Формат даты для мессенджера.
     *
     * Сегодня      → 14:35
     * Вчера        → Yesterday 18:42
     * Остальные    → 21.07.2026 09:15
     */
    public function formatConversation(
    ?Carbon $date,
    string $timezone
): ?string {

    if (!$date) {
        return null;
    }

    $date = $date
        ->copy()
        ->timezone($timezone);

    $today = now($timezone);

    if ($date->isToday()) {
        return 'Today ' . $date->format('H:i');
    }

    if ($date->isYesterday()) {
        return 'Yesterday ' . $date->format('H:i');
    }

    if ($date->greaterThanOrEqualTo($today->copy()->subDays(7))) {
        return $date->format('l H:i');
    }

    return $date->format('d M H:i');
}
}