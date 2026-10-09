<div
    id="delivery-option-modal"
    class="fixed inset-0 z-50 hidden"
    aria-hidden="true"
>
    {{-- Overlay --}}
    <div
        class="absolute inset-0 bg-black/50"
        onclick="closeDeliveryModal()"
    ></div>

    {{-- Modal --}}
    <div class="relative z-10 flex min-h-full items-center justify-center p-4">
        <div
            class="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-2xl shadow-2xl"
        >
            {{-- Header --}}
            <div
                class="sticky top-0 z-10 flex items-center justify-between gap-4 px-6 py-4 bg-white border-b border-gray-200"
            >
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Select Delivery Option
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Choose the preferred delivery method for your order.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeDeliveryModal()"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
                    aria-label="Close"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            {{-- Delivery Options --}}
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    @foreach($shippingOptions as $template)

                        @php
                            $deliveryOptions = $template->deliveryOptions
                                ->filter(function ($option) {
                                    return $option->is_active
                                        && $option->deliveryType?->is_active
                                        && $option->price !== null;
                                })
                                ->sortBy('sort_order')
                                ->values();

                            $courier = $template->courier;

                            $courierTranslation = $courier?->translations
                                ->firstWhere('locale', app()->getLocale())
                                ?? $courier?->translations->firstWhere('locale', 'en');

                            $courierLogo = $courier?->logo;

                            $courierName = $courierTranslation?->name
                                ?? $courier?->code
                                ?? '';
                        @endphp

                        @if($deliveryOptions->isNotEmpty())

                            <div class="border border-gray-200 rounded-xl p-4 transition">

                                {{-- Template --}}
                                <div class="mb-4">
                                    <div class="flex items-center gap-3">

                                        @if($courierLogo)
                                            <img
                                                src="{{ \Illuminate\Support\Str::startsWith($courierLogo, ['http://', 'https://'])
                                                    ? $courierLogo
                                                    : asset('storage/' . ltrim($courierLogo, '/')) }}"
                                                alt="{{ $courierName ?: 'Shipping courier' }}"
                                                class="h-10 w-10 shrink-0 object-contain"
                                                loading="lazy"
                                                onerror="this.style.display='none';"
                                            >
                                        @endif

                                        <div class="min-w-0">
                                            <h4 class="font-semibold text-gray-900">
                                                {{ $template->title }}
                                            </h4>

                                            @if($courierName)
                                                <p class="mt-0.5 text-xs text-gray-500">
                                                    {{ $courierName }}
                                                </p>
                                            @endif
                                        </div>

                                    </div>

                                    @if($template->description)
                                        <p class="mt-2 text-sm text-gray-500">
                                            {{ $template->description }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Delivery types --}}
                                <div class="space-y-2">

                                    @foreach($deliveryOptions as $option)

                                        @php
                                            $totalShippingPrice = 0;

                                            foreach ($cartItems as $item) {
                                                $pricePerItem = $item->product->computeShippingPrice(
                                                    $template,
                                                    $option
                                                );

                                                $totalShippingPrice +=
                                                    $pricePerItem * $item->quantity;
                                            }

                                            $deliveryTypeName = $option->deliveryType?->name
                                                ?? $option->deliveryType?->code
                                                ?? '';
                                        @endphp

                                        <label
                                            class="delivery-option-label flex items-center justify-between gap-3 border border-gray-200 rounded-lg px-3 py-3 bg-white cursor-pointer hover:border-gray-300 hover:shadow-sm transition"
                                            data-option-id="{{ $option->id }}"
                                        >
                                            <div class="flex items-center gap-3 min-w-0">

                                                <input
                                                    type="radio"
                                                    name="delivery_option_id"
                                                    value="{{ $option->id }}"
                                                    class="delivery-option-radio h-4 w-4 shrink-0 text-blue-900"
                                                    data-template-id="{{ $template->id }}"
                                                    data-template-name="{{ $template->title }}"
                                                    data-price="{{ $totalShippingPrice }}"
                                                    data-label="{{ $deliveryTypeName }}"
                                                    data-courier-logo="{{ $courierLogo ?? '' }}"
                                                    data-courier-name="{{ $courierName }}"
                                                >

                                                <div class="min-w-0">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $deliveryTypeName }}
                                                    </div>

                                                    @if($option->price_unit)
                                                        <div class="text-[11px] text-gray-400">
                                                            {{ $option->price_unit_label }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="shrink-0 text-right">
                                                @if((float) $totalShippingPrice > 0)
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        ${{ number_format($totalShippingPrice, 2) }}
                                                    </div>
                                                @else
                                                    <div class="text-sm font-semibold text-emerald-600">
                                                        FREE
                                                    </div>
                                                @endif
                                            </div>
                                        </label>

                                    @endforeach

                                </div>

                                {{-- Delivery time --}}
                                @if($template->delivery_time)
                                    <div class="mt-3 text-xs text-gray-500">
                                        <span class="font-medium">Delivery Time:</span>
                                        {{ $template->delivery_time }} days
                                    </div>
                                @endif

                            </div>

                        @endif

                    @endforeach

                </div>
            </div>

            {{-- Footer --}}
            <div class="sticky bottom-0 flex justify-end gap-3 px-6 py-4 bg-white border-t border-gray-200">
                <button
                    type="button"
                    onclick="closeDeliveryModal()"
                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    onclick="confirmDeliveryOption()"
                    class="px-4 py-2 text-sm bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition"
                >
                    Confirm Delivery
                </button>
            </div>
        </div>
    </div>
</div>