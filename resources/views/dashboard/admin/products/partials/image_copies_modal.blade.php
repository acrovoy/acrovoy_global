{{-- =========================================================
    IMAGE COPIES MODAL
========================================================== --}}
<div
    x-data="{
        open: false,
        title: '',
        copies: [],
        selectedCopy: null,

        selectCopy(copy) {
            this.selectedCopy = copy;
        }
    }"
    @open-image-copies.window="
        open = true;
        title = $event.detail.title || 'Image copies';
        copies = $event.detail.copies || [];
        selectedCopy = copies.find(copy => copy.is_original) || copies[0] || null;
    "
    @keydown.escape.window="if (open) open = false"
    x-show="open"
    x-cloak
    style="display: none;"
    class="fixed inset-0 z-[100]"
>
    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-stone-950/60 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- MODAL WRAPPER --}}
    <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">
        <section
            role="dialog"
            aria-modal="true"
            aria-labelledby="image-copies-modal-title"
            @click.stop
            class="flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl"
        >
            {{-- HEADER --}}
            <div class="flex items-start justify-between gap-4 border-b border-stone-200 px-5 py-4">
                <div class="min-w-0">
                    <h2
                        id="image-copies-modal-title"
                        class="text-base font-semibold text-stone-900"
                    >
                        Image Viewer
                    </h2>

                    <p
                        class="mt-1 break-words text-xs text-stone-500"
                        x-text="title"
                    ></p>

                    <p class="mt-1 text-xs text-stone-400">
                        Available file versions
                    </p>
                </div>

                <button
                    type="button"
                    @click="open = false"
                    aria-label="Close modal"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-stone-200 text-stone-500 transition hover:bg-stone-100 hover:text-stone-900"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>

            {{-- CONTENT --}}
            <div class="grid min-h-0 flex-1 grid-cols-1 overflow-y-auto md:grid-cols-[minmax(0,1.2fr)_minmax(280px,0.8fr)]">

                {{-- LEFT: SELECTED COPY PREVIEW AND DETAILS --}}
                <div class="min-w-0 border-b border-stone-100 bg-stone-50 p-4 sm:p-5 md:border-b-0 md:border-r">
                    <div class="flex min-h-64 items-center justify-center overflow-hidden rounded-lg border border-stone-200 bg-white p-3 sm:min-h-80">
                        <template x-if="selectedCopy">
                            <img
                                :src="selectedCopy.url"
                                :alt="selectedCopy.file_name"
                                class="max-h-[42vh] max-w-full object-contain"
                            >
                        </template>

                        <template x-if="!selectedCopy">
                            <p class="text-sm text-stone-400">
                                No image selected
                            </p>
                        </template>
                    </div>

                    <template x-if="selectedCopy">
                        <div class="mt-3 min-w-0">
                            {{-- SELECTED COPY INFORMATION --}}
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p
                                        class="break-words text-sm font-semibold text-stone-800"
                                        x-text="selectedCopy.label"
                                    ></p>

                                    <span
                                        x-show="selectedCopy.is_original"
                                        class="rounded-md border border-stone-200 bg-white px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-stone-500"
                                    >
                                        Original
                                    </span>
                                </div>

                                <p
                                    class="mt-1 break-all text-xs text-stone-500"
                                    x-text="selectedCopy.file_name"
                                ></p>

                                <p
                                    class="mt-1 text-xs text-stone-500"
                                    x-text="selectedCopy.size_label || 'Size unavailable'"
                                ></p>

                                <a
                                    :href="selectedCopy.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-3 inline-flex items-center gap-2 rounded-lg bg-stone-900 px-3 py-2 text-xs font-medium text-white transition hover:bg-stone-700"
                                >
                                    Open selected copy

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
                            </div>

                            {{-- =================================================
                                IMAGE DETAILS
                            ================================================== --}}
                            <div class="mt-5 border-t border-stone-200 pt-4">
                                <div class="mb-3">
                                    <h3 class="text-xs font-semibold uppercase tracking-wide text-stone-700">
                                        Image details
                                    </h3>

                                    <p class="mt-1 text-xs text-stone-500">
                                        Details update when you select another image.
                                    </p>
                                </div>

                                {{-- TECHNICAL SUMMARY --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="rounded-lg border border-stone-200 bg-white p-3">
                                        <p class="text-xs text-stone-500">
                                            Resolution
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-semibold text-stone-800"
                                            x-text="selectedCopy.width && selectedCopy.height
                                                ? Number(selectedCopy.width).toLocaleString() + ' × ' + Number(selectedCopy.height).toLocaleString() + ' px'
                                                : 'Not recorded'"
                                        ></p>
                                    </div>

                                    <div class="rounded-lg border border-stone-200 bg-white p-3">
                                        <p class="text-xs text-stone-500">
                                            File size
                                        </p>

                                        <p
                                            class="mt-1 break-words text-sm font-semibold text-stone-800"
                                            x-text="selectedCopy.size_label || 'Not recorded'"
                                        ></p>

                                        <p
                                            class="mt-1 text-[10px] text-stone-400"
                                            x-text="selectedCopy.size_bytes != null
                                                ? Number(selectedCopy.size_bytes).toLocaleString() + ' bytes'
                                                : ''"
                                        ></p>
                                    </div>

                                    <div class="col-span-2 rounded-lg border border-stone-200 bg-white p-3">
                                        <p class="text-xs text-stone-500">
                                            Format
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-semibold uppercase text-stone-800"
                                            x-text="selectedCopy.extension || 'Unknown'"
                                        ></p>

                                        <p
                                            class="mt-1 break-all text-xs text-stone-500"
                                            x-text="selectedCopy.mime_type || 'MIME type not recorded'"
                                        ></p>
                                    </div>
                                </div>

                                {{-- FILE INFORMATION --}}
                                <div class="mt-4">
                                    <h4 class="mb-2 text-xs font-semibold uppercase tracking-wide text-stone-500">
                                        File information
                                    </h4>

                                    <dl class="divide-y divide-stone-100 rounded-lg border border-stone-200 bg-white">
                                        <template
                                            x-for="field in [
                                                { label: 'Media ID', value: selectedCopy.media_id },
                                                { label: 'UUID', value: selectedCopy.uuid },
                                                { label: 'Original name', value: selectedCopy.original_file_name },
                                                { label: 'Stored filename', value: selectedCopy.file_name },
                                                { label: 'Collection', value: selectedCopy.collection },
                                                { label: 'Media role', value: selectedCopy.media_role },
                                                { label: 'File path', value: selectedCopy.path },
                                                { label: 'Original media path', value: selectedCopy.file_path },
                                                { label: 'Checksum', value: selectedCopy.checksum_hash || (selectedCopy.is_original ? 'Not recorded' : 'Not stored for this copy') },
                                                { label: 'Processing status', value: selectedCopy.processing_status },
                                                { label: 'Sort order', value: selectedCopy.sort_order },
                                                { label: 'Total views', value: selectedCopy.views_count },
                                                { label: 'Unique views', value: selectedCopy.unique_views_count },
                                                { label: 'Model type', value: selectedCopy.model_type },
                                                { label: 'Model ID', value: selectedCopy.model_id },
                                                { label: 'Created at', value: selectedCopy.created_at },
                                                { label: 'Updated at', value: selectedCopy.updated_at },
                                                { label: 'Deleted at', value: selectedCopy.deleted_at }
                                            ]"
                                            :key="field.label"
                                        >
                                            <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                                <dt
                                                    class="text-xs text-stone-500"
                                                    x-text="field.label"
                                                ></dt>

                                                <dd
                                                    class="min-w-0 break-words text-xs text-stone-700 [overflow-wrap:anywhere]"
                                                    x-text="field.value != null && field.value !== '' ? field.value : '—'"
                                                ></dd>
                                            </div>
                                        </template>

                                        <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                            <dt class="text-xs text-stone-500">
                                                Private
                                            </dt>
                                            <dd
                                                class="text-xs text-stone-700"
                                                x-text="selectedCopy.is_private ? 'Yes' : 'No'"
                                            ></dd>
                                        </div>

                                        <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                            <dt class="text-xs text-stone-500">
                                                Primary image
                                            </dt>
                                            <dd
                                                class="text-xs text-stone-700"
                                                x-text="selectedCopy.is_primary ? 'Yes' : 'No'"
                                            ></dd>
                                        </div>

                                        <div class="grid grid-cols-[100px_minmax(0,1fr)] gap-2 px-3 py-2">
                                            <dt class="text-xs text-stone-500">
                                                Main image
                                            </dt>
                                            <dd
                                                class="text-xs text-stone-700"
                                                x-text="selectedCopy.is_main ? 'Yes' : 'No'"
                                            ></dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- RIGHT: AVAILABLE COPIES --}}
                <div class="min-w-0 p-4 sm:p-5">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Available copies
                        </h3>

                        <span
                            class="rounded-md bg-stone-100 px-2 py-1 text-xs text-stone-500"
                            x-text="copies.length"
                        ></span>
                    </div>

                    <div class="space-y-2">
                        <template x-for="copy in copies" :key="copy.path">
                            <div
                                class="flex min-w-0 items-center gap-3 rounded-lg border p-3 transition"
                                :class="selectedCopy && selectedCopy.path === copy.path
                                    ? 'border-sky-300 bg-sky-50'
                                    : 'border-stone-200 bg-white hover:border-stone-300'"
                            >
                                <button
                                    type="button"
                                    @click="selectCopy(copy)"
                                    class="flex min-w-0 flex-1 items-center gap-3 text-left"
                                >
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-md border border-stone-200 bg-white">
                                        <img
                                            :src="copy.url"
                                            :alt="copy.file_name"
                                            loading="lazy"
                                            class="h-full w-full object-contain"
                                        >
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="break-words text-sm font-medium text-stone-800"
                                            x-text="copy.label"
                                        ></p>

                                        <p
                                            class="mt-1 break-all text-xs text-stone-500"
                                            x-text="copy.file_name"
                                        ></p>

                                        <p
                                            class="mt-1 text-xs text-stone-400"
                                            x-text="copy.size_label"
                                        ></p>
                                    </div>
                                </button>

                                <a
                                    :href="copy.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :aria-label="'Open ' + copy.file_name + ' in a new tab'"
                                    title="Open in a new tab"
                                    class="shrink-0 rounded-md p-2 text-stone-400 transition hover:bg-white hover:text-sky-700"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 4h6v6m-1-5-9 9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"/>
                                    </svg>
                                </a>
                            </div>
                        </template>

                        <template x-if="copies.length === 0">
                            <p class="rounded-lg border border-dashed border-stone-200 p-4 text-sm text-stone-500">
                                No image copies found.
                            </p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex justify-end border-t border-stone-200 px-5 py-3">
                <button
                    type="button"
                    @click="open = false"
                    class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                >
                    Close
                </button>
            </div>
        </section>
    </div>
</div>