{{-- Origin --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Product origin and contact (OLD VERSION BLOCK)
                </h2>
            </div>

            <dl class="divide-y divide-stone-100 px-5">
                @foreach ([
                    'Country' => $originCountry?->name,
                    'Region' => $originRegion?->name,
                    'City' => $originCity?->name,
                    'Origin address' => $product1->origin_address,
                    'Contact person' => $product1->origin_contact_name,
                    'Contact phone' => $product1->origin_contact_phone,
                ] as $label => $value)
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-xs text-stone-500">{{ $label }}</dt>
                        <dd class="max-w-[65%] break-words text-right text-sm font-medium text-stone-800">
                            {{ $value !== null && $value !== '' ? $value : '—' }}
                        </dd>
                    </div>
                @endforeach
            </dl>

            <div class="border-t border-stone-100 px-5 py-3">
                <p class="text-xs text-stone-400">
                    Country ID: {{ $product1->country_id ?? '—' }}
                    <span class="mx-1">·</span>
                    Region ID: {{ $product1->origin_region_id ?? '—' }}
                    <span class="mx-1">·</span>
                    City ID: {{ $product1->origin_city_id ?? '—' }}
                </p>
            </div>
        </section>