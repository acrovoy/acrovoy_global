{{-- Product record --}}
<section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm xl:col-span-2">
    <div class="border-b border-stone-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-stone-900">
            Product record
        </h2>
        <p class="mt-1 text-xs text-stone-500">
            Stored product data and supplier information
        </p>
    </div>

    <dl class="grid grid-cols-1 divide-y divide-stone-100 px-5 sm:grid-cols-2 sm:gap-x-8 sm:divide-y-0">
        @php
            $recordFields = [
                'SKU' => $product1->sku,
                'Slug' => $product1->slug,
                'Category ID' => $product1->category_id,
                'Supplier ID' => $product1->supplier_id,
                'Created by' => $creator?->name
                    ? $creator->name . ' (#' . $creator->id . ')'
                    : ($product1->created_by ?? 'Not recorded'),
                'Variant group ID' => $product1->variant_group_id,
                'Minimum order quantity' => $product1->moq,
                'Lead time (days)' => $product1->lead_time,
                'Customization' => $product1->customization
                    ? 'Available'
                    : 'Not available',
                'Created at' => $product1->created_at?->format('d M Y, H:i'),
                'Last updated' => $product1->updated_at?->format('d M Y, H:i'),
            ];
        @endphp

        @foreach ($recordFields as $label => $value)
            <div class="flex min-w-0 items-start justify-between gap-4 border-b border-stone-100 py-3">
                <dt class="shrink-0 text-xs text-stone-500">
                    {{ $label }}
                </dt>

                <dd class="min-w-0 break-words text-right text-sm font-medium text-stone-800">
                    @if ($label === 'Created by' && $creator?->id)
                        <a
                            href="{{ url('/dashboard/admin/users/' . $creator->id) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-end gap-1 text-sky-600 transition hover:text-sky-700 hover:underline"
                            title="Open user profile in a new tab"
                        >
                            <span>
                                {{ $creator->name ?: 'User' }} (#{{ $creator->id }})
                            </span>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-3.5 w-3.5 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M14 4h6v6m-1-5-9 9"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"
                                />
                            </svg>
                        </a>
                    @else
                        {{ $value !== null && $value !== '' ? $value : '—' }}
                    @endif
                </dd>
            </div>
        @endforeach
    </dl>

    <div class="border-t border-stone-100 px-5 py-4">
        <p class="text-xs text-stone-500">Product URL slug</p>
        <p class="mt-1 break-all font-mono text-xs text-stone-700">
            {{ $product1->slug }}
        </p>
    </div>

    <div class="border-t border-stone-100 px-5 py-4">
        <a
            href=""
            class="inline-flex items-center gap-2 rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
        >
            Edit product
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m14 5 5 5M4 20l4.5-1 10-10a2.12 2.12 0 0 0-3-3l-10 10L4 20Z"
                />
            </svg>
        </a>
    </div>
</section>