<?php

namespace App\Domain\Media\DTO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class UploadMediaDTO
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly Model $model,
        public readonly string $collection = 'default',
        public readonly string $mediaRole = 'default',
        public readonly bool $private = false,
        public readonly ?string $originalFileName = null,
        public readonly array $metadata = []
    ) {}

    public static function fromRequest($request, Model $model): self
    {
        $file = $request->file('file');

        return new self(
            file: $file,
            model: $model,
            collection: $request->get('collection', 'default'),
            mediaRole: $request->get('media_role', 'default'),
            private: $request->boolean('private', false),
            originalFileName: $file?->getClientOriginalName(),
            metadata: $request->get('metadata', [])
        );
    }
}