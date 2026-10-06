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

        $deliveryTypes = [
            'self_pickup' => 'Self Pickup',
            'curbside_delivery' => 'Curbside Delivery',
            'door_to_door' => 'Door-to-Door Delivery',
            'white_glove' => 'White Glove Delivery',
            'delivery_assembly' => 'Delivery & Assembly',
            'delivery_installation' => 'Delivery & Installation',
            'custom' => 'Custom Delivery',
        ];

        $selectedUnit = old('price_unit', $shippingTemplate->price_unit ?? 'per_item');
        $selectedDeliveryType = old('delivery_type', $shippingTemplate->delivery_type ?? 'door_to_door');
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
                            <input type="text" name="title[{{ $language->code }}]" class="input"
                                   placeholder="Title ({{ $language->code }})"
                                   value="{{ old('title.' . $language->code, $title) }}">
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Description</label>
                            <input type="text" name="description[{{ $language->code }}]" class="input"
                                   placeholder="Description ({{ $language->code }})"
                                   value="{{ old('description.' . $language->code, $description) }}">
                        </div>
                    </div>
                </div>
            @endforeach

            @if($languages->count() > 1)
                <button type="button" @click="open = !open"
                        class="mt-1 text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                    Other Languages
                    <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            @endif
        </div>

        {{-- DELIVERY TYPE --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Delivery Type</label>
            <select name="delivery_type" class="input">
                @foreach($deliveryTypes as $key => $label)
                    <option value="{{ $key }}" {{ $selectedDeliveryType === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1.5">Select the type of delivery service provided to the customer.</p>
        </div>

        {{-- PRICE --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Price</label>
                    <input type="number" min="0" step="0.01" name="price" class="input"
                           value="{{ old('price', $shippingTemplate->price ?? '') }}"
                           placeholder="0.00">
                    <p class="text-xs text-gray-500 mt-1.5">Enter price in <strong>USD</strong>. It will be automatically converted to the user's selected currency.</p>
                </div>

                <div>
                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Price Unit</label>
                    <select name="price_unit" class="input">
                        @foreach($units as $key => $label)
                            <option value="{{ $key }}" {{ $selectedUnit === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1.5">Choose how the price should be applied.</p>
                </div>
            </div>
        </div>

        {{-- DELIVERY TIME --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5 mb-5">
            <label class="block mb-1.5 text-sm font-medium text-gray-700">Delivery Time</label>
            <input type="text" name="delivery_time" class="input"
                   value="{{ old('delivery_time', $shippingTemplate->delivery_time ?? '') }}"
                   placeholder="e.g. 3-5 days">
        </div>

        {{-- DELIVERY DESTINATION --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-5">
                <h4 class="font-semibold text-gray-900">Delivery Destination</h4>
                <p class="text-xs text-gray-500 mt-1">Select the regions or cities where this shipping template is available.</p>
            </div>

            <x-location-tree :locations="$countries"
                             :selectedLocations="old('locations', $selectedLocations)" />
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('supplier.shipping-templates.index') }}"
           class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
            Cancel
        </a>

        <button type="submit" id="submitBtn"
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