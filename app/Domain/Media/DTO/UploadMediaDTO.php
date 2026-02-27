<?php

namespace App\Domain\Media\DTO;

use App\Models\User;
use Illuminate\Http\UploadedFile;

class UploadMediaDTO
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly User $user,
        public readonly string $collection = 'default',
        public readonly bool $private = false
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            file: $request->file('file'),
            user: $request->user(),
            collection: $request->get('collection', 'default'),
            private: $request->boolean('private', false)
        );
    }
}