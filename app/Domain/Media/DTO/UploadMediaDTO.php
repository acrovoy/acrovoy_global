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
        public readonly bool $private = false
    ) {}

    public static function fromRequest($request, Model $model): self
    {
        return new self(
            file: $request->file('file'),
            model: $model,
            collection: $request->get('collection', 'default'),
            private: $request->boolean('private', false)
        );
    }
}