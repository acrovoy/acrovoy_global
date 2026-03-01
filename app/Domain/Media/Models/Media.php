<?php

namespace App\Domain\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'uuid',
        'model_type',
        'model_id',
        'collection',
        'media_role',
        'original_file_name',
        'file_name',
        'file_path',
        'cdn_url',
        'mime_type',
        'extension',
        'size_bytes',
        'width',
        'height',
        'checksum_hash',
        'is_private',
        'is_primary',
        'processing_status',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_private' => 'boolean',
        'is_primary' => 'boolean'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_OPTIMIZED = 'optimized';
    const STATUS_READY = 'ready';
    const STATUS_FAILED = 'failed';



public function basePath(): string
{
    return dirname(dirname($this->file_path));
}

public function previewPath(): string
{
    return $this->basePath() . '/preview/' . $this->file_name;
}

    
}