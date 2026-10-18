{{-- =========================================================
    PRODUCT TITLE AND QUICK DETAILS
========================================================== --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    <div class="p-5 sm:p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="min-w-0 flex-1" x-data="{ translationsOpen: false }">
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">
                    Product submission
                </p>

                <h1 class="mt-2 break-words text-xl font-semibold tracking-tight text-stone-900">
                    {{ $product1->name ?: 'Untitled product' }}
                </h1>

                @if ($product1->undername)
                    <p class="whitespace-pre-line text-sm leading-6 text-stone-600">
                        {{ $product1->undername }}
                    </p>
                @endif

                @if ($translations->isNotEmpty())
                    <button
                        type="button"
                        @click="translationsOpen = !translationsOpen"
                        :aria-expanded="translationsOpen.toString()"
                        aria-controls="product-translations"
                        class="mt-3 inline-flex items-center gap-2 rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-medium text-stone-600 transition hover:border-stone-300 hover:bg-stone-100 hover:text-stone-900"
                    >
                        <svg
                            class="h-4 w-4 transition-transform duration-200"
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

                        <span class="rounded-md bg-white px-1.5 py-0.5 text-xs text-stone-500">
                            {{ $translations->count() }}
                        </span>
                    </button>

                    <div
                        id="product-translations"
                        x-show="translationsOpen"
                        x-transition
                        style="display: none;"
                        class="mt-3 space-y-3"
                    >
                        @foreach ($translations as $translation)
                            @if (filled($translation->name) || filled($translation->undername))
                                <div class="rounded-lg border border-stone-200 bg-stone-50/70 p-4">
                                    <div class="mb-3 flex items-center justify-between">
                                        <span class="rounded-md border border-stone-200 bg-white px-2 py-1 text-xs font-semibold uppercase tracking-wide text-stone-600">
                                            {{ $translation->locale }}
                                        </span>
                                    </div>

                                    @if (filled($translation->name))
                                        <p class="text-xs font-medium text-stone-500">
                                            Title
                                        </p>
                                        <p class="mt-1 break-words text-sm font-semibold text-stone-900">
                                            {{ $translation->name }}
                                        </p>
                                    @endif

                                    @if (filled($translation->undername))
                                        <p class="{{ filled($translation->name) ? 'mt-3' : '' }} text-xs font-medium text-stone-500">
                                            Undername
                                        </p>
                                        <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-stone-700">
                                            {{ $translation->undername }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs text-stone-600">
                        SKU: {{ $product1->sku ?: 'Not provided' }}
                    </span>

                    <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs text-stone-600">
                        Category: {{ $product1->category?->name ?? 'Not selected' }}
                    </span>

                    <span class="rounded-md border border-stone-200 bg-stone-50 px-2.5 py-1 text-xs text-stone-600">
                        Supplier: {{ $product1->supplier?->name ?? 'Unknown' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:min-w-96">
                <div class="rounded-lg bg-stone-50 p-3">
                    <p class="text-xs text-stone-500">Product ID</p>
                    <p class="mt-1 text-lg font-semibold text-stone-900">
                        #{{ $product1->id }}
                    </p>
                </div>

                <div class="rounded-lg bg-stone-50 p-3">
                    <p class="text-xs text-stone-500">Price tiers</p>
                    <p class="mt-1 text-lg font-semibold text-stone-900">
                        {{ $product1->priceTiers->count() }}
                    </p>
                </div>

                <div class="rounded-lg bg-stone-50 p-3">
                    <p class="text-xs text-stone-500">Specifications</p>
                    <p class="mt-1 text-lg font-semibold text-stone-900">
                        {{ $product1->attributeValues->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($product1->status === 'rejected' && $product1->reject_reason)
        <div class="border-t border-red-100 bg-red-50 px-5 py-4 sm:px-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">
                Rejection reason
            </p>
            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-red-900">
                {{ $product1->reject_reason }}
            </p>
        </div>
    @endif
</section>