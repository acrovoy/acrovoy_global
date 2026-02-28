<?php


namespace App\Domain\Media\Processing\Contracts;

use App\Domain\Media\Models\Media;

interface MediaProcessingStrategy
{
    public function process(Media $media): void;
}