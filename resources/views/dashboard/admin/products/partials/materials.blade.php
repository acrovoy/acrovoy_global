{{-- =========================================================
    MATERIALS
========================================================== --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">
            Materials
        </h2>
        <p class="mt-1 text-xs text-stone-500">
            Selected materials, colors and their translations
        </p>
    </div>

    @if ($product1->materials->isNotEmpty())
        @php
            $materialGroups = $product1->materials->groupBy(
                fn ($material) => $material->material_group_id ?? 'ungrouped'
            );
        @endphp

        <div class="space-y-4 p-4 sm:p-5">
            @foreach ($materialGroups as $groupMaterials)
                @php
                    $materialGroup = $groupMaterials->first()?->materialGroup;
                @endphp

                <div class="min-w-0">
                    @if ($materialGroup)
                        <div class="mb-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-stone-500">
                                {{ $materialGroup->translatedName() ?? $materialGroup->name ?? $materialGroup->slug }}
                            </h3>

                            @if ($materialGroup->translatedDescription())
                                <p class="mt-1 text-xs leading-5 text-stone-500">
                                    {{ $materialGroup->translatedDescription() }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($groupMaterials as $material)
                            @php
                                $materialTranslations = $material->translations
                                    ->filter(fn ($translation) => filled($translation->name))
                                    ->values();

                                $materialMedia = $material->photo;
                                $materialCopies = $materialMedia
                                    ? ($materialImageCopies[$materialMedia->id] ?? [])
                                    : [];

                                $materialImage = $materialMedia?->cdn_url
                                    ?? ($materialCopies[0]['url'] ?? null);

                                $canPreviewMaterial = $materialMedia
                                    && $materialImage
                                    && count($materialCopies) > 0;
                            @endphp

                            <div
                                x-data="{ translationsOpen: false }"
                                class="min-w-0 rounded-lg border border-stone-200 bg-white p-2.5 transition hover:border-stone-300"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    {{-- MATERIAL IMAGE: OPEN SHARED VIEWER --}}
                                    <div class="relative h-12 w-12 shrink-0">
                                        @if ($canPreviewMaterial)
                                            <button
                                                type="button"
                                                class="admin-open-image-copies group/material relative flex h-full w-full items-center justify-center overflow-hidden rounded-md border border-stone-200 bg-stone-50 transition hover:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                                                data-title="{{ $materialMedia->original_file_name ?? $material->name }}"
                                                data-copies="{{ json_encode($materialCopies, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                                                aria-label="View image copies for {{ $material->name }}"
                                                title="View material photo and available copies"
                                            >
                                                <img
                                                    src="{{ $materialImage }}"
                                                    alt="{{ $material->name }}"
                                                    loading="lazy"
                                                    class="h-full w-full object-cover"
                                                >

                                                <span class="absolute inset-0 flex items-center justify-center bg-stone-950/0 transition group-hover/material:bg-stone-950/40">
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-5 w-5 text-white opacity-0 drop-shadow transition group-hover/material:opacity-100"
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
                                        @elseif ($materialImage)
                                            <div class="flex h-full w-full items-center justify-center overflow-hidden rounded-md border border-stone-200 bg-stone-50">
                                                <img
                                                    src="{{ $materialImage }}"
                                                    alt="{{ $material->name }}"
                                                    loading="lazy"
                                                    class="h-full w-full object-cover"
                                                >
                                            </div>
                                        @else
                                            <div class="flex h-full w-full items-center justify-center overflow-hidden rounded-md border border-stone-200 bg-stone-50">
                                                <img
                                                    src="{{ asset('images/no-image.png') }}"
                                                    alt="{{ $material->name }}"
                                                    loading="lazy"
                                                    class="h-full w-full object-cover"
                                                >
                                            </div>
                                        @endif
                                    </div>

                                    {{-- MATERIAL NAME AND TRANSLATIONS --}}
                                    <div class="min-w-0 flex-1">
                                        <p class="break-words text-sm font-medium leading-5 text-stone-800 [overflow-wrap:anywhere]">
                                            {{ $material->name ?? ('Material #' . $material->id) }}
                                        </p>

                                        @if ($materialTranslations->isNotEmpty())
                                            <button
                                                type="button"
                                                @click="translationsOpen = !translationsOpen"
                                                :aria-expanded="translationsOpen.toString()"
                                                class="mt-1 inline-flex items-center gap-1 text-xs text-stone-500 transition hover:text-sky-700"
                                            >
                                                <svg
                                                    class="h-3.5 w-3.5 shrink-0 transition-transform duration-200"
                                                    :class="{ 'rotate-180': translationsOpen }"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m6 9 6 6 6-6"
                                                    />
                                                </svg>

                                                <span>Translations</span>

                                                <span class="rounded bg-stone-100 px-1.5 py-0.5 text-[10px]">
                                                    {{ $materialTranslations->count() }}
                                                </span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- HIDDEN MATERIAL TRANSLATIONS --}}
                                @if ($materialTranslations->isNotEmpty())
                                    <div
                                        x-show="translationsOpen"
                                        x-transition
                                        style="display: none;"
                                        class="mt-2 space-y-1.5 border-t border-stone-100 pt-2"
                                    >
                                        @foreach ($materialTranslations as $translation)
                                            <div class="grid min-w-0 grid-cols-[32px_minmax(0,1fr)] gap-2">
                                                <span class="pt-0.5 text-[10px] font-semibold uppercase text-stone-400">
                                                    {{ $translation->locale }}
                                                </span>

                                                <span class="break-words text-xs leading-4 text-stone-600 [overflow-wrap:anywhere]">
                                                    {{ $translation->name }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="p-5 text-sm text-stone-500">
            No materials selected.
        </p>
    @endif
</section>