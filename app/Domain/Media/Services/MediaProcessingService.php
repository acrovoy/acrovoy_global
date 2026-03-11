<?php

namespace App\Domain\Media\Services;

use Imagick;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Jobs\ProcessMediaJob;

class MediaProcessingService
{
    /**
     * Dispatch async pipeline (normalize → generate → optimize)
     */
    public function dispatchPipeline(Media $media, int $delaySeconds = 0): void
    {
        ProcessMediaJob::dispatch($media->uuid)
            ->delay(now()->addSeconds($delaySeconds))
            ->onQueue('media');
    }

    /**
     * Синхронный вызов pipeline (редко используется, например для теста)
     */
    public function process(Media $media): void
    {
        // Вместо прямого OptimizeMediaJob запускаем весь pipeline
        $this->dispatchPipeline($media);
    }

    public function normalize(Media $media): void
{
    if (!str_starts_with($media->mime_type, 'image')) {
        return;
    }

    \Log::info("🖼 Normalizing image: {$media->file_path}");

    

    $path = storage_path("app/public/".$media->originalPath()); // <- тут хелпер

    if (!file_exists($path)) {
        \Log::warning("⚠️ File not found: {$path}");
        return;
    }

    try {
        $imagick = new Imagick($path);

        // Исправляем EXIF orientation
        switch ($imagick->getImageOrientation()) {
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $imagick->rotateimage("#000", 180);
                break;
            case Imagick::ORIENTATION_RIGHTTOP:
                $imagick->rotateimage("#000", 90);
                break;
            case Imagick::ORIENTATION_LEFTBOTTOM:
                $imagick->rotateimage("#000", -90);
                break;
        }

        $imagick->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);

        // Ограничиваем размер
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();
        $maxWidth = 1920;
        $maxHeight = 1080;

        if ($width > $maxWidth || $height > $maxHeight) {
            $imagick->thumbnailImage($maxWidth, $maxHeight, true);
        }

        // Удаляем EXIF
        $imagick->stripImage();

        // Перезаписываем файл
        $imagick->writeImage($path);
        $imagick->clear();
        $imagick->destroy();

        \Log::info("✅ Normalization done: {$media->file_path}");

    } catch (\Throwable $e) {
        \Log::error("❌ Normalize failed: {$e->getMessage()}");
        $media->update(['processing_status' => Media::STATUS_FAILED]);
    }
}

public function generateDocumentPreview(Media $media): void
{
    \Log::info("📄 Generating document preview for {$media->file_path}");

    $path = storage_path("app/public/" . $media->file_path);

    if (!file_exists($path)) {
        \Log::error("❌ PDF file not found: {$path}");
        $media->update(['processing_status' => Media::STATUS_FAILED]);
        return;
    }

    // Получаем конфиг размеров для этой коллекции
    $variantsConfig = config("media.collections.{$media->collection}.variants", [
        'thumb' => 300,
        'small' => 600,
        'medium' => 1200,
        'large' => 2000,
    ]);

    try {
        if (str_contains($media->mime_type, 'pdf')) {
            $imagick = new \Imagick();
            $imagick->setResolution(200, 200);
            $imagick->readImage($path . '[0]'); // первая страница
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(85);

            foreach ($variantsConfig as $variant => $width) {
                $variantPath = storage_path("app/public/" . $media->variantPath($variant));
                $variantDir = pathinfo($variantPath, PATHINFO_DIRNAME);
                if (!is_dir($variantDir)) {
                    mkdir($variantDir, 0755, true);
                }

                // создаем превью для этого варианта
                $thumb = clone $imagick; // клонируем для каждого размера
                $thumb->thumbnailImage($width, 0);
                $thumb->writeImage($variantPath);
                $thumb->clear();
                $thumb->destroy();

                \Log::info("✅ Document preview variant '{$variant}' generated: {$variantPath}");
            }

            $imagick->clear();
            $imagick->destroy();
        }

        $media->update(['processing_status' => Media::STATUS_READY]);

    } catch (\Throwable $e) {
        \Log::error("❌ Failed to generate document preview: {$e->getMessage()}");
        $media->update(['processing_status' => Media::STATUS_FAILED]);
    }
}


public function generateVariant(Media $media, ?string $specificVariant = null, ?int $specificWidth = null): void
{
    \Log::info("Collection for media: {$media->collection}");

    $variantsConfig = config("media.collections.{$media->collection}.variants", []);
    \Log::info("Variants config: " . json_encode($variantsConfig));

    if ($specificVariant && $specificWidth) {
        $variantsConfig = [$specificVariant => $specificWidth];
    }

    $originalPath = storage_path("app/public/" . ltrim($media->originalPath(), '/'));

    \Log::info("Original path resolved to: {$originalPath}");

    if (!file_exists($originalPath)) {
        \Log::warning("⚠️ Original file not found, cannot generate variants: {$originalPath}");
        return;
    }

    \Log::info("Starting variants loop. Count: " . count($variantsConfig));

    foreach ($variantsConfig as $variant => $width) {

        \Log::info("----- LOOP START -----");
        \Log::info("Variant key: {$variant}");
        \Log::info("Variant width: {$width}");

        $variantPath = storage_path("app/public/" . ltrim($media->variantPath($variant), '/'));

        \Log::info("Variant path resolved: {$variantPath}");

        $variantDir = pathinfo($variantPath, PATHINFO_DIRNAME);

        if (!is_dir($variantDir)) {
            \Log::info("Creating directory: {$variantDir}");
            mkdir($variantDir, 0755, true);
        }

        try {

            \Log::info("Opening Imagick for: {$originalPath}");

            $imagick = new \Imagick($originalPath);

            \Log::info("Resizing to width {$width}");

            $imagick->thumbnailImage($width, 0);

            \Log::info("Writing image to {$variantPath}");

            $imagick->writeImage($variantPath);

            $imagick->clear();
            $imagick->destroy();

            \Log::info("✅ Variant '{$variant}' generated at {$variantPath}");

        } catch (\Throwable $e) {

            \Log::error("❌ Failed to generate variant '{$variant}': {$e->getMessage()}");

            $media->update([
                'processing_status' => Media::STATUS_FAILED,
            ]);
        }

        \Log::info("----- LOOP END -----");
    }

    \Log::info("All variants loop finished.");
}


public function optimizeVariant(Media $media, string $variant): void
{
    \Log::info("⚡ Optimizing variant '{$variant}' for media {$media->file_path}");

    $variantPath = storage_path("app/public/" . $media->variantPath($variant));

    if (!file_exists($variantPath)) {
        \Log::warning("⚠️ Variant file not found: {$variantPath}");
        return;
    }

    try {
        $imagick = new \Imagick($variantPath);

        // Простейшая оптимизация: сохраняем с меньшей компрессией
        if (in_array($media->extension, ['jpg', 'jpeg'])) {
            $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality(75); // например 75%
        }

        $imagick->stripImage(); // удалить EXIF/метаданные
        $imagick->writeImage($variantPath);
        $imagick->clear();
        $imagick->destroy();

        \Log::info("✅ Variant '{$variant}' optimized successfully");

    } catch (\Throwable $e) {
        \Log::error("❌ Failed to optimize variant '{$variant}': {$e->getMessage()}");
        $media->update([
            'processing_status' => Media::STATUS_FAILED,
        ]);
    }
}


    /**
     * Проверка, что media — видео
     */
    protected function isVideo(Media $media): bool
    {
        return str_starts_with($media->mime_type, 'video');
    }

    /**
     * Транскодинг видео (отдельная логика)
     */
    public function transcodeVideo(Media $media): void
    {
        if (! $this->isVideo($media)) {
            return;
        }

        // Здесь можно диспатчить VideoTranscodingJob
        // VideoTranscodingJob::dispatch($media->uuid)->onQueue('media');

        $media->update([
            'processing_status' => 'processed'
        ]);
    }
}