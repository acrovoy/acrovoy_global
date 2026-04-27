<?php

namespace App\Domain\RFQ\DTO;

class CreateRfqOfferData
{
    public function __construct(
        public readonly int $rfq_id,
        public readonly array $items
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            rfq_id: $data['rfq_id'],
            items: $data['items'] ?? [],
        );
    }
}