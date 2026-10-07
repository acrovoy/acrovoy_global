<x-alerts />

<form method="POST"
      action="{{ $shippingTemplate && $shippingTemplate->id ? route('supplier.shipping-templates.update', $shippingTemplate->id) : route('supplier.shipping-templates.store') }}"
      class="space-y-5 bg-gray-50 border border-gray-200 rounded-2xl shadow-sm p-6"
      id="shippingTemplateForm">
    @csrf

    @if($shippingTemplate && $shippingTemplate->id)
        @method('PUT')
    @endif

    <input type="hidden" name="manufacturer_id" value="{{ auth()->id() }}">

    @php
        $languages = \App\Models\Language::where('is_active', true)->orderBy('sort_order')->get();
        $selectedLocations = $shippingTemplate ? $shippingTemplate->locations->pluck('id')->toArray() : [];

        $units = [
            'per_item' => 'Per Item',
            'per_kg' => 'Per Kilogram',
            'per_cubic_meter' => 'Per Cubic Meter',
            'flat' => 'Flat Rate',
        ];

        $deliveryTypes = $deliveryTypes ?? collect();

        $savedDeliveryOptions = $shippingTemplate
            ? $shippingTemplate->deliveryOptions->keyBy('delivery_type_id')
            : collect();

            $selectedUnit = old('price_unit', $shippingTemplate->price_unit ?? 'per_item');
    @endphp

    <div class="form-step" data-step="1">

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Basic Information</h3>
            <p class="text-sm text-gray-500 mt-1">Create a shipping template with pricing, delivery service and destination.</p>
        </div>

        {{-- TRANSLATIONS --}}
        <div x-data="{ open: false }" class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
            <h4 class="font-semibold text-gray-900 mb-4">Template Translations</h4>

            @foreach($languages as $index => $language)
                @php
                    $translation = $shippingTemplate?->translations->firstWhere('locale', $language->code);
                    $title = $translation->title ?? '';
                    $description = $translation->description ?? '';
                    $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');
                @endphp

                <div @if($index > 0) x-show="open" x-collapse @endif class="mb-5 last:mb-0">
                    <label class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                        <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                        {{ strtoupper($language->code) }}
                    </label>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Template Title</label>
                            <input type="text"
                                   name="title[{{ $language->code }}]"
                                   class="input"
                                   placeholder="Title ({{ $language->code }})"
                                   value="{{ old('title.' . $language->code, $title) }}">
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Description</label>
                            <input type="text"
                                   name="description[{{ $language->code }}]"
                                   class="input"
                                   placeholder="Description ({{ $language->code }})"
                                   value="{{ old('description.' . $language->code, $description) }}">
                        </div>
                    </div>
                </div>
            @endforeach

            @if($languages->count() > 1)
                <button type="button"
                        @click="open = !open"
                        class="mt-1 text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                    Other Languages
                    <svg :class="{ 'rotate-180': open }"
                         class="w-4 h-4 transition-transform"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            @endif
        </div>

        {{-- DELIVERY OPTIONS --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
            <div class="mb-5">
                <h4 class="font-semibold text-gray-900">Delivery Options & Pricing</h4>
                <p class="text-xs text-gray-500 mt-1">
    Set a price for each delivery type. Leave the price empty to disable the option. A price of 0 means free delivery.
</p>
<p class="text-xs text-gray-500 mt-4">
    Prices are entered in USD and automatically converted to the user's selected currency.
    Free delivery can be set to 0.00. Leave the price empty to disable the option.
</p>
            </div>

            <div class="space-y-3">
                @forelse($deliveryTypes as $deliveryType)
                    @php
                        $option = $savedDeliveryOptions->get($deliveryType->id);

                        $price = old(
                            "delivery_options.{$deliveryType->id}.price",
                            $option?->price
                        );

                        $priceUnit = old(
                            "delivery_options.{$deliveryType->id}.price_unit",
                            $option?->price_unit ?? 'flat'
                        );
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_150px_190px] gap-3 items-end p-3 rounded-xl border border-gray-100 hover:border-gray-200 transition">

                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-800">
                                {{ $deliveryType->name }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                {{ $deliveryType->code }}
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-500">
                                Price (USD)
                            </label>
                            <input type="number"
                                   name="delivery_options[{{ $deliveryType->id }}][price]"
                                   min="0"
                                   step="0.01"
                                   class="input"
                                   value="{{ $price }}"
                                   placeholder="0.00">
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-medium text-gray-500">
                                Price Unit
                            </label>
                            <select name="delivery_options[{{ $deliveryType->id }}][price_unit]"
                                    class="input">
                                @foreach($units as $key => $label)
                                    <option value="{{ $key }}" {{ $priceUnit === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                @empty
                    <div class="p-4 rounded-xl bg-gray-50 text-sm text-gray-500">
                        No active delivery types found. Add or activate delivery types in Settings.
                    </div>
                @endforelse
            </div>

            <p class="text-xs text-gray-500 mt-4">
                Prices are entered in USD and automatically converted to the user's selected currency.
                Only delivery options with a price greater than zero will be used.
            </p>

            {{-- Compatibility with existing shipping_templates fields --}}
            <input type="hidden" name="price" value="0">
            <input type="hidden" name="price_unit" value="flat">
        </div>

  


        {{-- DELIVERY TIME --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
            <label class="block mb-1.5 text-sm font-medium text-gray-700">
                Delivery Time
            </label>

            <input type="text"
                   name="delivery_time"
                   class="input"
                   value="{{ old('delivery_time', $shippingTemplate->delivery_time ?? '') }}"
                   placeholder="e.g. 3-5 days">
        </div>

        {{-- DELIVERY DESTINATION --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-5">
                <h4 class="font-semibold text-gray-900">Delivery Destination</h4>
                <p class="text-xs text-gray-500 mt-1">
                    Select the regions or cities where this shipping template is available.
                </p>
            </div>

            <x-location-tree
                :locations="$countries"
                :selectedLocations="old('locations', $selectedLocations)"
            />
        </div>

    </div>

    {{-- ACTIONS --}}
    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('supplier.shipping-templates.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            Cancel
        </a>

        <button type="submit"
                id="submitBtn"
                class="px-5 py-2.5 text-sm font-medium bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
            Save Template
        </button>
    </div>

</form>

<style>
.input {
    width: 100%;
    border: 1px solid #d1d5db;
    background: #fff;
    padding: .7rem .9rem;
    border-radius: .65rem;
    font-family: 'Figtree', sans-serif;
    transition: border-color .2s, box-shadow .2s;
}
.input:focus {
    border-color: #9ca3af;
    box-shadow: 0 0 0 2px rgba(156,163,175,.15);
    outline: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('shippingTemplateForm');
    const button = document.getElementById('submitBtn');

    if (form && button) {
        form.addEventListener('submit', () => {
            button.disabled = true;
            button.classList.add('opacity-60', 'cursor-not-allowed');
            button.textContent = 'Saving...';
        });
    }
});
</script>