@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6 max-w-3xl">

<a href="{{ route('admin.shipping-center.index') }}"
   class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
    ← Back to shippings
</a>

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">Edit Shipping Route</h2>
        <p class="text-sm text-gray-500">
            Update the shipping route, price, delivery time, notes and active status
        </p>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded border border-green-200 bg-green-50 text-green-800 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded border border-red-200 bg-red-50 text-red-800 text-sm">
        {{ session('error') }}
    </div>
@endif

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.shipping-center.update', $shippingCenter) }}"
          class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- Origin country --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Origin country
            </label>
            <select name="origin_country_id" id="origin_country_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Select country</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}"
                        {{ old('origin_country_id', $shippingCenter->origin_country_id) == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Origin location --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Origin location (optional)
    </label>
    <select name="origin_location_id" id="origin_location_id"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Select location</option>
        {{-- если нужны старые данные или при редактировании --}}
        @if(!empty($originLocations))
            @foreach($originLocations as $loc)
                <option value="{{ $loc->id }}"
                    {{ old('origin_location_id', $shippingCenter->origin_location_id ?? '') == $loc->id ? 'selected' : '' }}>
                    {{ str_repeat('— ', $loc->level) . $loc->name }}
                </option>
            @endforeach
        @endif
    </select>
</div>

       {{-- Destination country --}}
       <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Destination country
            </label>
            <select name="destination_country_id" id="destination_country_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Select country</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}"
                        {{ old('destination_country_id', $shippingCenter->destination_country_id) == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Destination location --}}
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Destination location (optional)
    </label>
    <select name="destination_location_id" id="destination_location_id"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Select location</option>
        @if(!empty($destinationLocations))
            @foreach($destinationLocations as $loc)
                <option value="{{ $loc->id }}"
                    {{ old('destination_location_id', $shippingCenter->destination_location_id ?? '') == $loc->id ? 'selected' : '' }}>
                    {{ str_repeat('— ', $loc->level) . $loc->name }}
                </option>
            @endforeach
        @endif
    </select>
</div>

        {{-- Price --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Shipping price
            </label>
            <input type="number"
                   name="price"
                   value="{{ old('price', $shippingCenter->price) }}"
                   step="0.01"
                   min="0"
                   required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- Delivery days --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Delivery time (days)
            </label>
            <input type="number"
                   name="delivery_days"
                   value="{{ old('delivery_days', $shippingCenter->delivery_days) }}"
                   min="0"
                   required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- Notes --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Notes
            </label>
            <textarea name="notes" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                      placeholder="Optional notes">{{ old('notes', $shippingCenter->notes) }}</textarea>
        </div>

        {{-- Active --}}
        <div class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   {{ old('is_active', $shippingCenter->is_active) ? 'checked' : '' }}
                   class="rounded border-gray-300">
            <span class="text-sm text-gray-700">Active route</span>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-4">
            <button type="submit"
                    class="px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-800 transition">
                Update
            </button>

            <a href="{{ route('admin.shipping-center.index') }}"
               class="text-sm text-gray-600 hover:underline">
                Cancel
            </a>
        </div>

    </form>
</div>

{{-- JS for dynamic locations --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    function loadLocations(countrySelect, locationSelect, selectedId = null) {
        async function fetchLocations(countryId) {
            locationSelect.innerHTML = '<option value="">Loading...</option>';
            if (!countryId) {
                locationSelect.innerHTML = '<option value="">Select location</option>';
                return;
            }
            try {
                const res = await fetch("{{ route('admin.settings.locations.locations') }}?country_id=" + countryId);
                const data = await res.json();

                locationSelect.innerHTML = '<option value="">Select location</option>';

                function addOption(loc, prefix = '') {
                    const option = document.createElement('option');
                    option.value = loc.id;
                    option.textContent = prefix + loc.name;
                    if (selectedId && selectedId == loc.id) option.selected = true;
                    locationSelect.appendChild(option);

                    if (loc.children && loc.children.length) {
                        loc.children.forEach(child => addOption(child, prefix + '— '));
                    }
                }

                data.forEach(region => addOption(region));

            } catch (err) {
                locationSelect.innerHTML = '<option value="">Select location</option>';
            }
        }

        countrySelect.addEventListener('change', function () {
            fetchLocations(this.value);
        });

        // если страна уже выбрана, подгружаем сразу
        if (countrySelect.value) {
            fetchLocations(countrySelect.value);
        }
    }

    loadLocations(
        document.getElementById('origin_country_id'),
        document.getElementById('origin_location_id'),
        "{{ old('origin_location_id', $shippingCenter->origin_location_id ?? '') }}"
    );

    loadLocations(
        document.getElementById('destination_country_id'),
        document.getElementById('destination_location_id'),
        "{{ old('destination_location_id', $shippingCenter->destination_location_id ?? '') }}"
    );
});
</script>
@endsection
