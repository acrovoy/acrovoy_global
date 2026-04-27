<?php

namespace App\Domain\RFQ\DTO;

class CreateRfqData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $type,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            type: $data['type'],
        );
    }
}