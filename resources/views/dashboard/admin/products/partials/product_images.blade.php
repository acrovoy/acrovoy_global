{{-- =========================================================
    PRODUCT IMAGE GALLERY AND TECHNICAL DETAILS
========================================================== --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
        <div>
            <h2 class="text-sm font-semibold text-stone-900">Product images</h2>
            <p class="mt-1 text-xs text-stone-500">Gallery and image file information</p>
        </div>
        <span class="rounded-md bg-stone-50 px-2.5 py-1 text-xs text-stone-500">
            {{ $images->count() }} files
        </span>
    </div>

    <div class="p-4">
        @if ($images->isNotEmpty())
            @php
                $selectedGalleryImage = $mainImage ?? $images->first();
            @endphp

            <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)]">

                {{-- LEFT: IMAGE AND THUMBNAILS --}}
                <div class="min-w-0">
                    <div class="flex h-72 items-center justify-center overflow-hidden rounded-lg border border-stone-100 bg-stone-50">
                        <img
                            id="adminMainImage"
                            src="{{ $selectedGalleryImage->cdn_url ?? asset('images/no-image.png') }}"
                            alt="{{ $selectedGalleryImage->original_file_name ?? $product1->name ?? 'Product image' }}"
                            class="h-full w-full object-contain"
                        >
                    </div>

                    <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-5 lg:grid-cols-4">
                        @foreach ($images as $image)
                            <button
                                type="button"
                                class="admin-image-thumbnail flex h-16 items-center justify-center overflow-hidden rounded-lg border bg-white p-1 transition hover:border-stone-500 {{ $image->id === $selectedGalleryImage->id ? 'border-stone-700' : 'border-stone-200' }}"
                                data-src="{{ $image->cdn_url }}"
                                data-alt="{{ $image->original_file_name ?? $image->file_name }}"
                                data-meta-target="adminImageMeta-{{ $image->id }}"
                                aria-label="View image {{ $loop->iteration }}"
                                aria-pressed="{{ $image->id === $selectedGalleryImage->id ? 'true' : 'false' }}"
                            >
                                <img
                                    src="{{ $image->cdn_url ?? asset('images/no-image.png') }}"
                                    alt=""
                                    loading="lazy"
                                    class="h-full w-full object-contain"
                                >
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- RIGHT: SELECTED IMAGE DETAILS --}}
                <div class="min-w-0 overflow-hidden rounded-lg border border-stone-200">
                    <div class="border-b border-stone-100 bg-stone-50 px-4 py-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-stone-600">
                            Image details
                        </h3>
                        <p class="mt-1 text-xs text-stone-500">
                            Details update when you select another image.
                        </p>
                    </div>

                    <div class="max-h-[350px] space-y-4 overflow-y-auto p-4">
                        @foreach ($images as $image)
                            @php
                                $bytes = is_numeric($image->size_bytes)
                                    ? (int) $image->size_bytes
                                    : null;

                                if ($bytes === null) {
                                    $formattedSize = 'Not recorded';
                                } elseif ($bytes >= 1048576) {
                                    $formattedSize = number_format($bytes / 1048576, 2) . ' MB';
                                } elseif ($bytes >= 1024) {
                                    $formattedSize = number_format($bytes / 1024, 2) . ' KB';
                                } else {
                                    $formattedSize = number_format($bytes) . ' bytes';
                                }

                                $metadata = $image->metadata;

                                if (is_string($metadata)) {
                                    $decodedMetadata = json_decode($metadata, true);

                                    $metadata = json_last_error() === JSON_ERROR_NONE
                                        ? $decodedMetadata
                                        : $metadata;
                                }

                                $metadataIsEmpty = $metadata === null
                                    || $metadata === []
                                    || $metadata === '[]'
                                    || $metadata === '';

                                $metadataJson = is_array($metadata) || is_object($metadata)
                                    ? json_encode(
                                        $metadata,
                                        JSON_PRETTY_PRINT
                                            | JSON_UNESCAPED_UNICODE
                                            | JSON_UNESCAPED_SLASHES
                                    )
                                    : $metadata;

                                $availableCopies = $imageCopies[$image->id] ?? [];
                            @endphp

                            <div
                                id="adminImageMeta-{{ $image->id }}"
                                class="admin-image-metadata {{ $image->id === $selectedGalleryImage->id ? '' : 'hidden' }}"
                            >
                                {{-- SUMMARY --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-lg border border-stone-100 bg-stone-50/70 p-3">
                                        <p class="text-xs text-stone-500">Resolution</p>
                                        <p
                                            data-image-dimensions
                                            class="mt-1 text-sm font-semibold text-stone-800"
                                        >
                                            @if ($image->width && $image->height)
                                                {{ number_format($image->width) }} × {{ number_format($image->height) }} px
                                            @else
                                                Reading dimensions...
                                            @endif
                                        </p>
                                    </div>

                                    <div class="rounded-lg border border-stone-100 bg-stone-50/70 p-3">
                                        <p class="text-xs text-stone-500">File size</p>
                                        <p class="mt-1 break-words text-sm font-semibold text-stone-800">
                                            {{ $formattedSize }}
                                        </p>
                                        @if ($bytes !== null)
                                            <p class="mt-1 text-[10px] text-stone-400">
                                                {{ number_format($bytes) }} bytes
                                            </p>
                                        @endif
                                    </div>

                                    <div class="col-span-2 rounded-lg border border-stone-100 bg-stone-50/70 p-3">
                                        <p class="text-xs text-stone-500">Format</p>
                                        <p class="mt-1 text-sm font-semibold uppercase text-stone-800">
                                            {{ $image->extension ?: 'Unknown' }}
                                        </p>
                                        <p class="mt-1 break-all text-xs text-stone-500">
                                            {{ $image->mime_type ?: 'MIME type not recorded' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- FILE INFORMATION --}}
                                <div>
                                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-stone-500">
                                        File information
                                    </h4>

                                    <dl class="divide-y divide-stone-100 rounded-lg border border-stone-100">
                                        @php
                                            $details = [
                                                'Media ID' => $image->id,
                                                'UUID' => $image->uuid,
                                                'Original name' => $image->original_file_name,
                                                'Stored filename' => $image->file_name,
                                                'Collection' => $image->collection,
                                                'Media role' => $image->media_role,
                                                'File path' => $image->file_path,
                                                'Checksum' => $image->checksum_hash,
                                                'Processing status' => $image->processing_status,
                                                'Sort order' => $image->sort_order,
                                                'Total views' => $image->views_count,
                                                'Unique views' => $image->unique_views_count,
                                                'Model type' => $image->model_type,
                                                'Model ID' => $image->model_id,
                                                'Created at' => $image->created_at?->format('d M Y, H:i:s'),
                                                'Updated at' => $image->updated_at?->format('d M Y, H:i:s'),
                                                'Deleted at' => $image->deleted_at?->format('d M Y, H:i:s'),
                                            ];
                                        @endphp

                                        @foreach ($details as $label => $value)
                                            @if ($value !== null && $value !== '')
                                                <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                                    <dt class="text-xs text-stone-500">
                                                        {{ $label }}
                                                    </dt>
                                                    <dd class="min-w-0 break-words text-xs text-stone-700 [overflow-wrap:anywhere]">
                                                        {{ $value }}
                                                    </dd>
                                                </div>
                                            @endif
                                        @endforeach

                                        <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                            <dt class="text-xs text-stone-500">Private</dt>
                                            <dd class="text-xs text-stone-700">
                                                {{ $image->is_private ? 'Yes' : 'No' }}
                                            </dd>
                                        </div>

                                        <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                            <dt class="text-xs text-stone-500">Primary image</dt>
                                            <dd class="text-xs text-stone-700">
                                                {{ $image->is_primary ? 'Yes' : 'No' }}
                                            </dd>
                                        </div>

                                        <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                            <dt class="text-xs text-stone-500">Main image</dt>
                                            <dd class="text-xs text-stone-700">
                                                {{ $image->is_main ? 'Yes' : 'No' }}
                                            </dd>
                                        </div>

                                        {{-- IMAGE URL AND COPIES BUTTON --}}
                                        @if ($image->cdn_url || count($availableCopies) > 1)
                                            <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                                <dt class="text-xs text-stone-500">
                                                    Image URL
                                                </dt>

                                                <dd class="min-w-0">
                                                    @if ($image->cdn_url)
                                                        <a
                                                            href="{{ $image->cdn_url }}"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="text-xs text-sky-700 hover:underline"
                                                        >
                                                            Open original
                                                        </a>
                                                    @endif

                                                    @if (count($availableCopies) > 1)
                                                        <div class="mt-2">
                                                            <button
    type="button"
    class="admin-open-image-copies inline-flex items-center gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2 text-xs font-medium text-stone-700 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800"
    data-title="{{ $image->original_file_name ?? $image->file_name }}"
    data-copies="{{ json_encode($availableCopies, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-4 w-4 shrink-0"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
    >
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <circle cx="8.5" cy="8.5" r="1.5"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"/>
    </svg>

    <span>View image copies</span>

    <span class="rounded bg-stone-100 px-1.5 py-0.5 text-[10px]">
        {{ count($availableCopies) }}
    </span>
</button>
                                                        </div>
                                                    @endif
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>

                                {{-- ADDITIONAL METADATA --}}
                                @if (!$metadataIsEmpty)
                                    <div>
                                        <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-stone-500">
                                            Additional metadata
                                        </h4>
                                        <pre class="max-h-48 overflow-auto rounded-lg border border-stone-200 bg-stone-50 p-3 text-xs leading-5 text-stone-700"><code>{{ $metadataJson }}</code></pre>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="flex h-64 flex-col items-center justify-center rounded-lg border border-dashed border-stone-200 bg-stone-50 text-center">
                <p class="text-sm font-medium text-stone-600">No images uploaded</p>
                <p class="mt-1 text-xs text-stone-400">
                    The supplier did not add a product gallery.
                </p>
            </div>
        @endif
    </div>
</section>

{{-- Keep the modal outside the overflow-hidden gallery section --}}
@include('dashboard.admin.products.partials.image_copies_modal')

<script>
document.addEventListener('click', function (event) {
    const target = event.target instanceof Element
        ? event.target
        : event.target?.parentElement;

    const button = target?.closest('.admin-open-image-copies');

    if (!button) return;

    let copies = [];

    try {
        copies = JSON.parse(button.dataset.copies || '[]');
    } catch (error) {
        console.error('Could not parse image copies:', error);
        return;
    }

    if (!copies.length) {
        console.warn('No image copies available for:', button.dataset.title);
        return;
    }

    window.dispatchEvent(new CustomEvent('open-image-copies', {
        detail: {
            title: button.dataset.title || 'Image copies',
            copies: copies
        }
    }));
});

document.addEventListener('DOMContentLoaded', function () {
    const mainImage = document.getElementById('adminMainImage');
    const thumbnails = document.querySelectorAll('.admin-image-thumbnail');
    const metadataPanels = document.querySelectorAll('.admin-image-metadata');

    if (!mainImage) return;

    function updateDimensions() {
        const activePanel = Array.from(metadataPanels)
            .find(panel => !panel.classList.contains('hidden'));

        const dimensionsLabel = activePanel?.querySelector('[data-image-dimensions]');

        if (!dimensionsLabel) return;

        if (mainImage.naturalWidth && mainImage.naturalHeight) {
            dimensionsLabel.textContent =
                `${mainImage.naturalWidth.toLocaleString()} × ${mainImage.naturalHeight.toLocaleString()} px`;
        } else {
            dimensionsLabel.textContent = 'Could not read dimensions';
        }
    }

    function selectImage(button) {
        const targetPanel = document.getElementById(button.dataset.metaTarget);

        metadataPanels.forEach(panel => {
            panel.classList.toggle('hidden', panel !== targetPanel);
        });

        thumbnails.forEach(thumbnail => {
            const selected = thumbnail === button;

            thumbnail.classList.toggle('border-stone-700', selected);
            thumbnail.classList.toggle('border-stone-200', !selected);
            thumbnail.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });

        mainImage.alt = button.dataset.alt || 'Product image';

        const targetSrc = button.dataset.src;

        if (targetSrc && mainImage.src !== new URL(targetSrc, window.location.href).href) {
            const dimensionsLabel = targetPanel?.querySelector('[data-image-dimensions]');

            if (dimensionsLabel) {
                dimensionsLabel.textContent = 'Reading dimensions...';
            }

            mainImage.src = targetSrc;
        } else {
            updateDimensions();
        }
    }

    thumbnails.forEach(button => {
        button.addEventListener('click', () => selectImage(button));
    });

    mainImage.addEventListener('load', updateDimensions);

    if (mainImage.complete) {
        updateDimensions();
    }
});
</script>