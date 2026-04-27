<?php

namespace App\Domain\Negotiation\Services;

use App\Domain\Negotiation\Models\NegotiationEvent;

class NegotiationAuditService
{
    public function log(
        string $type,
        $subject,
        int $rfqId,
        ?int $userId = null,
        array $payload = []
    ): void {
        NegotiationEvent::create([
            'type' => $type,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'rfq_id' => $rfqId,
            'user_id' => $userId,
            'payload' => $payload,
        ]);
    }
}