<?php

namespace App\Domain\Email\Services;

use App\Domain\Email\Jobs\SendEmailJob;
use InvalidArgumentException;

class EmailService
{
    public function sendSystemEmail(
        string $recipient,
        string $title,
        string $messageText,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
    ): void {
        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'The recipient email address is invalid.'
            );
        }

        SendEmailJob::dispatch(
            recipient: $recipient,
            title: $title,
            messageText: $messageText,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
        );
    }
}
