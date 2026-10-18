{{-- =========================================================
    SHIPPING OPTIONS
========================================================== --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
    @php
        $shippingTemplates = $product1->shippingTemplates
            ->reject(fn ($template) =>
                $template->provider_type === \App\Models\LogisticCompany::class
                && (int) $template->provider_id === 1
            )
            ->values();
    @endphp

    <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
        <div>
            <h2 class="text-sm font-semibold text-stone-900">Shipping options</h2>
            <p class="mt-1 text-xs text-stone-500">
                Shipping templates selected for this product
            </p>
        </div>
        <span class="text-xs text-stone-400">
            {{ $shippingTemplates->count() }} selected
        </span>
    </div>

    @if ($shippingTemplates->isNotEmpty())
    <div class="divide-y divide-stone-100">
        @foreach ($shippingTemplates as $template)
            @php
                $templateTranslation = $template->translations->firstWhere('locale', app()->getLocale())
                    ?? $template->translations->firstWhere('locale', 'en')
                    ?? $template->translations->first();
                $destinations = $template->locations->unique('id')->values();
                $templateTitle = $template->title ?: ($templateTranslation?->title ?? ('Shipping template #' . $template->id));
                $templateDescription = $template->description ?: $templateTranslation?->description;
            @endphp

            <div class="p-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="break-words text-sm font-semibold text-stone-900">{{ $templateTitle }}</h3>
                            @if ($template->is_default)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-100">Default</span>
                            @endif
                        </div>
                        @if ($templateDescription)
                            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-stone-600">{{ $templateDescription }}</p>
                        @endif
                        <p class="mt-2 text-xs text-stone-500">Warehouse: <span class="font-medium text-stone-700">{{ $template->warehouse?->name ?? 'Not specified' }}</span></p>
                    </div>
                    <div class="shrink-0 rounded-lg bg-stone-50 p-3 lg:min-w-40">
                        <p class="text-xs text-stone-500">Delivery time</p>
                        <p class="mt-1 text-sm font-semibold text-stone-900">{{ filled($template->delivery_time) ? $template->delivery_time : '—' }}</p>
                    </div>
                </div>

                <div class="mt-4 border-t border-stone-100 pt-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">Delivery options and prices</p>
                    @if ($template->deliveryOptions->isNotEmpty())
                        <div class="mt-3 space-y-3">
                            @foreach ($template->deliveryOptions as $option)
                                @php
                                    $deliveryTypeName = $option->deliveryType?->name
                                        ?: $option->deliveryType?->code
                                        ?: 'Delivery';
                                @endphp
                                <div class="flex flex-col gap-2 rounded-lg border border-stone-100 bg-white p-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex min-w-0 flex-wrap items-center gap-1.5">
                                        <span class="inline-flex max-w-full items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-800">{{ $deliveryTypeName }}</span>
                                        @if ($option->incoterm)
                                            <span class="inline-flex items-center rounded-md bg-stone-100 px-2 py-1 text-[10px] font-medium text-stone-600">{{ $option->incoterm }}</span>
                                        @endif
                                    </div>
                                    <div class="shrink-0 sm:min-w-32 sm:text-right">
                                        @if ($option->price !== null)
                                            <p class="text-sm font-semibold tabular-nums text-stone-900">${{ number_format((float) $option->price, 2) }}</p>
                                            <p class="text-[10px] text-stone-400">{{ $option->price_unit_label }}</p>
                                        @else
                                            <p class="text-xs text-stone-400">Price on request</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-stone-500">No delivery options configured.</p>
                    @endif
                </div>

                    {{-- DELIVERY DESTINATIONS --}}
                    <div class="mt-4 border-t border-stone-100 pt-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-stone-500">
                            Delivery destinations
                        </p>

                        @if ($destinations->isNotEmpty())
                            <div
                                x-data="{ expanded: false, canExpand: false }"
                                x-init="$nextTick(() => {
                                    canExpand = $refs.locations.scrollHeight > 160;
                                })"
                                class="mt-2 min-w-0"
                            >
                                <div
                                    x-ref="locations"
                                    class="flex flex-wrap gap-1.5 overflow-hidden transition-[max-height] duration-200"
                                    :style="expanded ? 'max-height: none' : 'max-height: 160px'"
                                >
                                    @foreach ($destinations as $location)
                                        @php
                                            $isCity = $location->children->isEmpty();

                                            $bgColor = $isCity ? 'bg-blue-100' : 'bg-green-100';
                                            $textColor = $isCity ? 'text-blue-800' : 'text-green-800';
                                            $borderColor = $isCity ? 'border-blue-200' : 'border-green-200';
                                        @endphp

                                        <span class="inline-flex max-w-full items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $bgColor }} {{ $textColor }} {{ $borderColor }}">
                                            {{ $location->name }}
                                        </span>
                                    @endforeach
                                </div>

                                <button
                                    type="button"
                                    x-cloak
                                    x-show="canExpand"
                                    @click="expanded = !expanded"
                                    :aria-expanded="expanded.toString()"
                                    :title="expanded ? 'Show less' : 'Show all destinations'"
                                    :aria-label="expanded ? 'Collapse destinations' : 'Expand destinations'"
                                    class="mt-1 flex h-7 w-7 items-center justify-center rounded-md text-stone-500 transition hover:bg-stone-100 hover:text-stone-800"
                                >
                                    <svg
                                        class="h-4 w-4 transition-transform duration-200"
                                        :class="expanded ? 'rotate-180' : ''"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="m5 7.5 5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <p class="mt-2 text-sm text-stone-500">
                                No destinations listed on this template.
                            </p>
                        @endif
                    </div>

                    {{-- ALL TEMPLATE TRANSLATIONS --}}
                    @if ($template->translations->isNotEmpty())
                        <details class="mt-4 border-t border-stone-100 pt-3">
                            <summary class="cursor-pointer text-xs font-medium text-stone-500 transition hover:text-stone-800">
                                View all shipping template translations
                                <span class="ml-1 text-stone-400">
                                    ({{ $template->translations->count() }})
                                </span>
                            </summary>

                            <div class="mt-3 space-y-2">
                                @foreach ($template->translations
    ->sortBy(fn ($translation) =>
        $languagesSortOrder[$translation->locale] ?? PHP_INT_MAX
    )
    ->values() as $translation)
                                    <div class="rounded-lg border border-stone-100 bg-stone-50 p-3">
                                        <div class="mb-2">
                                            <span class="inline-flex rounded-md border border-stone-200 bg-white px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-stone-600">
                                                {{ $translation->locale }}
                                            </span>
                                        </div>

                                        <p class="text-xs font-medium text-stone-500">Title</p>
                                        <p class="mt-1 break-words text-sm font-medium text-stone-800">
                                            {{ $translation->title ?? '—' }}
                                        </p>

                                        @if ($translation->description)
                                            <p class="mt-3 text-xs font-medium text-stone-500">
                                                Description
                                            </p>
                                            <p class="mt-1 whitespace-pre-line break-words text-xs leading-5 text-stone-600">
                                                {{ $translation->description }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="p-5 text-sm text-stone-500">
            No shipping templates are assigned to this product.
        </p>
    @endif
</section>