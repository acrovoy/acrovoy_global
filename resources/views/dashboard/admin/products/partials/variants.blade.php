{{-- =========================================================
    VARIANTS
========================================================== --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">
            Variations and color options
        </h2>
        <p class="mt-1 text-xs text-stone-500">
            Variant group ID: {{ $product1->variant_group_id ?? 'Not assigned' }}
        </p>
    </div>

    @if ($variantItems->isNotEmpty())
        <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($variantItems->sortBy('sort_order') as $variantItem)
                @php
    $variantProduct = $variantItem->product;

    // Use the variant item's image or the related product's main image.
    $variantMedia = $variantItem->media
        ?? $variantProduct?->mainImage;

    $variantTitle = $variantItem->title
        ?: ($variantProduct?->name ?? 'Variant');

    $variantCopies = $variantMedia
        ? ($variantImageCopies[$variantMedia->id] ?? [])
        : [];

    // If the controller has no copy list for this media record,
    // discover the existing files without changing the controller.
    if (empty($variantCopies) && $variantMedia && filled($variantMedia->file_path)) {
        $publicDisk = \Illuminate\Support\Facades\Storage::disk('public');

        $variantFilePath = ltrim(
            str_replace('\\', '/', $variantMedia->file_path),
            '/'
        );

        $parentFolder = basename(dirname($variantFilePath));

        $renditionFolders = [
            'original', 'large', 'medium', 'small', 'thumb', 'thumbnail',
        ];

        $baseDirectory = in_array($parentFolder, $renditionFolders, true)
            ? dirname(dirname($variantFilePath))
            : dirname($variantFilePath);

        $fileStem = pathinfo($variantFilePath, PATHINFO_FILENAME);

        $imageExtensions = [
            'jpg', 'jpeg', 'png', 'webp', 'gif',
            'avif', 'jfif', 'bmp', 'tif', 'tiff',
        ];

        try {
            $copyPaths = collect($publicDisk->allFiles($baseDirectory))
                ->map(fn ($path) => str_replace('\\', '/', $path))
                ->filter(function ($path) use ($imageExtensions, $fileStem) {
                    return pathinfo($path, PATHINFO_FILENAME) === $fileStem
                        && in_array(
                            strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                            $imageExtensions,
                            true
                        );
                })
                ->unique()
                ->values();

            // Ensure the original is included when it exists.
            if (
                $publicDisk->exists($variantFilePath)
                && !$copyPaths->contains($variantFilePath)
            ) {
                $copyPaths->push($variantFilePath);
            }

            $variantCopies = $copyPaths->map(function ($copyPath) use (
                $publicDisk,
                $variantFilePath,
                $variantMedia
            ) {
                $isOriginal = $copyPath === $variantFilePath;
                $absolutePath = $publicDisk->path($copyPath);

                try {
                    $bytes = (int) $publicDisk->size($copyPath);
                } catch (\Throwable $e) {
                    $bytes = null;
                }

                if ($bytes === null) {
                    $sizeLabel = 'Size unavailable';
                } elseif ($bytes >= 1048576) {
                    $sizeLabel = number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    $sizeLabel = number_format($bytes / 1024, 2) . ' KB';
                } else {
                    $sizeLabel = number_format($bytes) . ' bytes';
                }

                $imageInfo = is_file($absolutePath)
                    ? @getimagesize($absolutePath)
                    : false;

                $extension = strtolower(pathinfo($copyPath, PATHINFO_EXTENSION));

                $mimeType = is_array($imageInfo)
                    ? ($imageInfo['mime'] ?? null)
                    : null;

                if (!$mimeType) {
                    $mimeType = match ($extension) {
                        'jpg', 'jpeg', 'jfif' => 'image/jpeg',
                        'png' => 'image/png',
                        'webp' => 'image/webp',
                        'gif' => 'image/gif',
                        'avif' => 'image/avif',
                        'bmp' => 'image/bmp',
                        'tif', 'tiff' => 'image/tiff',
                        default => null,
                    };
                }

                return [
                    'label' => $isOriginal
                        ? 'Original'
                        : \Illuminate\Support\Str::headline(basename(dirname($copyPath))),
                    'file_name' => basename($copyPath),
                    'path' => $copyPath,
                    'url' => '/storage/' . implode(
                        '/',
                        array_map('rawurlencode', explode('/', $copyPath))
                    ),
                    'size_bytes' => $bytes,
                    'size_label' => $sizeLabel,
                    'width' => is_array($imageInfo) ? ($imageInfo[0] ?? null) : null,
                    'height' => is_array($imageInfo) ? ($imageInfo[1] ?? null) : null,
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'media_id' => $variantMedia->id,
                    'uuid' => $variantMedia->uuid,
                    'original_file_name' => $variantMedia->original_file_name,
                    'collection' => $variantMedia->collection,
                    'media_role' => $variantMedia->media_role,
                    'file_path' => $variantMedia->file_path,
                    'checksum_hash' => $isOriginal ? $variantMedia->checksum_hash : null,
                    'processing_status' => $variantMedia->processing_status,
                    'sort_order' => $variantMedia->sort_order,
                    'views_count' => $variantMedia->views_count,
                    'unique_views_count' => $variantMedia->unique_views_count,
                    'model_type' => $variantMedia->model_type,
                    'model_id' => $variantMedia->model_id,
                    'created_at' => $variantMedia->created_at?->format('d M Y, H:i:s'),
                    'updated_at' => $variantMedia->updated_at?->format('d M Y, H:i:s'),
                    'deleted_at' => $variantMedia->deleted_at?->format('d M Y, H:i:s'),
                    'is_private' => (bool) $variantMedia->is_private,
                    'is_primary' => (bool) $variantMedia->is_primary,
                    'is_main' => (bool) $variantMedia->is_main,
                    'is_original' => $isOriginal,
                ];
            })->sortByDesc('is_original')->values()->all();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // Use the discovered original URL first, then the media URL.
    $variantImage = $variantMedia?->cdn_url
        ?? collect($variantCopies)->firstWhere('is_original')['url'] ?? null
        ?? ($variantCopies[0]['url'] ?? null);

    $variantUrl = $variantProduct?->slug
        ? url('/product/' . $variantProduct->slug)
        : null;

    $canPreviewVariantImage = $variantMedia
        && $variantImage
        && count($variantCopies) > 0;
@endphp

                <div class="flex min-w-0 items-start gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-stone-300">

                    {{-- VARIANT IMAGE: OPENS IMAGE VIEWER --}}
                    <div class="relative h-20 w-20 shrink-0">
                        @if ($canPreviewVariantImage)
                            <button
                                type="button"
                                class="admin-open-image-copies group/image relative flex h-full w-full items-center justify-center overflow-hidden rounded-md border border-stone-200 bg-stone-50 transition hover:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                                data-title="{{ $variantMedia->original_file_name ?? $variantTitle }}"
                                data-copies="{{ json_encode($variantCopies, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                                aria-label="View image copies for {{ $variantTitle }}"
                                title="View image and available copies"
                            >
                                <img
                                    src="{{ $variantImage }}"
                                    alt="{{ $variantTitle }}"
                                    loading="lazy"
                                    class="h-full w-full object-contain"
                                >

                                <span class="absolute inset-0 flex items-center justify-center bg-stone-950/0 transition group-hover/image:bg-stone-950/35">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 text-white opacity-0 drop-shadow transition group-hover/image:opacity-100"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <circle cx="11" cy="11" r="6.5"/>
                                        <path stroke-linecap="round" d="m16 16 4 4M11 8v6M8 11h6"/>
                                    </svg>
                                </span>
                            </button>
                        @elseif ($variantImage)
                            <div class="flex h-full w-full items-center justify-center overflow-hidden rounded-md border border-stone-200 bg-stone-50">
                                <img
                                    src="{{ $variantImage }}"
                                    alt="{{ $variantTitle }}"
                                    loading="lazy"
                                    class="h-full w-full object-contain"
                                >
                            </div>
                        @else
                            <div class="flex h-full w-full items-center justify-center rounded-md border border-stone-200 bg-stone-50">
                                <span class="px-2 text-center text-xs text-stone-400">
                                    No image
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- VARIANT DETAILS --}}
                    <div class="min-w-0 flex-1">
                        @if ($variantUrl)
                            <a
                                href="{{ $variantUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group/product break-words text-sm font-medium text-stone-900 hover:text-sky-700 hover:underline"
                                title="Open product in a new tab"
                            >
                                {{ $variantTitle }}
                            </a>
                        @else
                            <p class="break-words text-sm font-medium text-stone-900">
                                {{ $variantTitle }}
                            </p>
                        @endif

                        <p class="mt-1 text-xs text-stone-500">
                            Variant item #{{ $variantItem->id }}
                        </p>

                        @if ($variantProduct)
                            <p class="mt-1 text-xs text-stone-500">
                                Product #{{ $variantProduct->id }}
                            </p>

                            <p class="mt-1 break-words text-xs text-stone-500">
                                SKU: {{ $variantProduct->sku ?: '—' }}
                            </p>
                        @endif

                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1">
                            @if ($variantUrl)
                                <a
                                    href="{{ $variantUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-sky-700 transition hover:text-sky-900 hover:underline"
                                >
                                    Open product

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3.5 w-3.5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 4h6v6m-1-5-9 9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"/>
                                    </svg>
                                </a>
                            @endif

                            @if ($canPreviewVariantImage)
                                <button
                                    type="button"
                                    class="admin-open-image-copies text-xs font-medium text-stone-500 transition hover:text-sky-700 hover:underline"
                                    data-title="{{ $variantMedia->original_file_name ?? $variantTitle }}"
                                    data-copies="{{ json_encode($variantCopies, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                                >
                                    View photo
                                    <span class="text-stone-400">({{ count($variantCopies) }})</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="p-5 text-sm text-stone-500">
            No variant items are associated with this product.
        </p>
    @endif
</section>