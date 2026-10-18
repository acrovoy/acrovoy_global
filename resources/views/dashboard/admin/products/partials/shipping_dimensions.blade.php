{{-- Shipping dimensions --}}
        <section class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-stone-900">
                    Shipping dimensions and package
                </h2>
            </div>

            @if ($dimensions)
                <div class="grid grid-cols-2 gap-3 p-5 sm:grid-cols-4">
                    @foreach ([
                        'Length' => $dimensions->length,
                        'Width' => $dimensions->width,
                        'Height' => $dimensions->height,
                        'Weight' => $dimensions->weight,
                    ] as $label => $value)
                        <div class="rounded-lg bg-stone-50 p-3">
                            <p class="text-xs text-stone-500">{{ $label }}</p>
                            <p class="mt-1 break-words text-lg font-semibold text-stone-900">
                                {{ $value !== null && $value !== '' ? $value : '—' }}
                            </p>
                        </div>
                    @endforeach

                    <div class="col-span-2 border-t border-stone-100 pt-3 sm:col-span-4">
                        <p class="text-xs text-stone-500">Package type</p>
                        <p class="mt-1 text-sm font-medium text-stone-800">
                            {{ $dimensions->package_type ?? '—' }}
                        </p>
                    </div>
                </div>
            @else
                <p class="p-5 text-sm text-stone-500">
                    No shipping dimensions have been saved.
                </p>
            @endif
        </section>