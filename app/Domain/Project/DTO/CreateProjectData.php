<?php

namespace App\Domain\Project\DTO;

class CreateProjectData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $closed_at,
    ) {}

    public static function fromArray(array $data): self
    {

 
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            closed_at: $data['closed_at'] ?? null,
        );
    }
}