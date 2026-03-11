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
        'sort_order',
        'is_main',
        'processing_status',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_private' => 'boolean',
        'is_primary' => 'boolean'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_UPLOADING = 'uploading';
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_OPTIMIZED = 'optimized';
    const STATUS_READY = 'ready';
    const STATUS_FAILED = 'failed';
    const STATUS_DELETING = 'deleting';
    const STATUS_PROCESSED = 'processed'; // для видео

    // =============================
    // Storage helpers (с учетом твоей текущей basePath)
    // =============================

    public function basePath(): string
    {
        // оставляем твою текущую логику
        return dirname(dirname($this->file_path));
    }


    public function previewPath(): string
{
    return "media/{$this->collection}/{$this->uuid}/thumb/{$this->uuid}.webp";
}

    public function originalPath(): string
    {
        return $this->basePath() . '/original/' . $this->file_name;
    }

    // =============================
    // Variant helpers
    // =============================

    /**
     * Путь к variant (webp)
     */
    public function variantPath(string $variant): string
    {
        $filename = pathinfo($this->file_name, PATHINFO_FILENAME);
        return $this->basePath() . "/{$variant}/{$filename}.webp";
    }

    /**
     * URL к variant (CDN или локальный)
     */
    public function variantUrl(string $variant): string
    {
        $path = $this->variantPath($variant);

        if ($this->cdn_url) {
            return rtrim($this->cdn_url, '/') . '/' . $variant . '/' . pathinfo($this->file_name, PATHINFO_FILENAME) . '.webp';
        }

        return asset('storage/' . $path);
    }

    /**
     * Все variant URL для текущей коллекции
     */
    public function variants(): array
    {
        $configVariants = config("media.collections.{$this->collection}.variants", []);
        $result = [];

        foreach ($configVariants as $variant => $_) {
            $result[$variant] = $this->variantUrl($variant);
        }

        return $result;
    }

    /**
     * Массив для responsive (srcset)
     */
    public function responsive(): array
    {
        $variants = $this->variants();
        $responsive = [];

        foreach ($variants as $variant => $url) {
            $width = config("media.collections.{$this->collection}.variants.{$variant}", 0);
            if ($width) {
                $responsive[] = "{$url} {$width}w";
            }
        }

        return $responsive;
    }

    // =============================
    // Оригинальный URL
    // =============================

    public function getUrlAttribute(): string
    {
        if ($this->cdn_url) {
            return rtrim($this->cdn_url, '/') . '/original/' . $this->file_name;
        }

        return asset('storage/' . $this->originalPath());
    }

    // =============================
    // Главная картинка
    // =============================

    public function isMain(): bool
    {
        return $this->is_main === 1 && $this->sort_order === 0;
    }
}