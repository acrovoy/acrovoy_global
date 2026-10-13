<?php

namespace App\Domain\Email\Jobs;

use App\Domain\Email\Mailables\SystemEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [5, 10, 30];

    public function __construct(
        public string $recipient,
        public string $title,
        public string $messageText,
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        Mail::to($this->recipient)->send(
            new SystemEmail(
                title: $this->title,
                messageText: $this->messageText,
                actionUrl: $this->actionUrl,
                actionLabel: $this->actionLabel,
            )
        );
    }
}
