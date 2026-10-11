@extends('dashboard.layout')

@section('dashboard-content')

<div class="mx-auto max-w-6xl">

    {{-- PAGE HEADER --}}
    <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-gray-600">
                    Workspace
                </span>
                <span class="text-xs text-gray-400">Delivery management</span>
            </div>

            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
                Add Delivery Address
            </h1>

            <p class="mt-2 max-w-2xl text-sm text-gray-500">
                Save a delivery address for your orders and shipments.
            </p>
        </div>

        <a
            href="{{ route('buyer.addresses.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 18l-6-6 6-6"/>
            </svg>
            Back to Addresses
        </a>
    </div>

    {{-- ALERTS --}}
    <div class="mb-6 space-y-3">
        <x-alerts />

        @if($errors->any())
            <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-semibold">Please check the following:</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">

        {{-- ADDRESS FORM --}}
        <div class="lg:col-span-2">
            <form
    action="{{ isset($address) && $address->exists
        ? route('buyer.addresses.update', $address->id)
        : route('buyer.addresses.store') }}"
    method="POST"
    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
    id="delivery-address-form"
>
    @csrf

    @if(isset($address) && $address->exists)
        @method('PUT')
    @endif

                <div class="border-b border-gray-200 px-5 py-5 sm:px-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">
                            <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1116 0z"/>
                                <circle cx="12" cy="10" r="2.5"/>
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Address Information</h2>
                            <p class="mt-0.5 text-xs text-gray-500">
                                Enter the recipient and delivery details.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-7 px-5 py-6 sm:px-6">

                    {{-- RECIPIENT --}}
                    <section>
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Recipient</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Contact details for the delivery.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div>
                                <label for="first_name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    First Name <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    value="{{ old('first_name', $address->first_name ?? '') }}"
                                    required
                                    maxlength="100"
                                    autocomplete="given-name"
                                    placeholder="Enter first name"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 transition focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('first_name') border-red-300 @enderror"
                                >

                                @error('first_name')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Last Name
                                </label>

                                <input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    value="{{ old('last_name', $address->last_name ?? '') }}"
                                    maxlength="100"
                                    autocomplete="family-name"
                                    placeholder="Enter last name"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 transition focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('last_name') border-red-300 @enderror"
                                >

                                @error('last_name')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="phone" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone', $address->phone ?? '') }}"
                                    required
                                    maxlength="50"
                                    autocomplete="tel"
                                    placeholder="+380 XX XXX XX XX"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 transition focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('phone') border-red-300 @enderror"
                                >

                                @error('phone')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </section>

                    <div class="border-t border-gray-100"></div>

                    {{-- DELIVERY LOCATION --}}
                    <section>
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-900">Delivery Location</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Provide the destination address and location.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            {{-- COUNTRY --}}
                            <div class="sm:col-span-2">
                                <label for="country" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Country <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="country"
                                    name="country"
                                    required
                                    autocomplete="country"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 transition focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('country') border-red-300 @enderror"
                                >
                                    <option value="">Select country</option>

                                    @foreach($countries as $countryOption)
                                        <option
                                            value="{{ $countryOption->id }}"
                                            @selected((string) old('country', $selectedCountry ?? '') === (string) $countryOption->id)
                                        >
                                            {{ $countryOption->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('country')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- REGION --}}
                            <div>
                                <label for="region" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    State / Region <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="region"
                                    name="region"
                                    required
                                    autocomplete="address-level1"
                                    data-selected="{{ old('region', $selectedRegion ?? '') }}"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 transition focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('region') border-red-300 @enderror"
                                >
                                    <option value="">Select region</option>

                                    @foreach($regions as $regionOption)
                                        <option
                                            value="{{ $regionOption->id }}"
                                            @selected((string) old('region', $selectedRegion ?? '') === (string) $regionOption->id)
                                        >
                                            {{ $regionOption->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('region')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- CITY MODE --}}
                            <div>
                                <label for="city_mode" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    City Entry <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="city_mode"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10"
                                >
                                    <option value="existing" @selected(old('city_mode', 'existing') === 'existing')>
                                        Select existing city
                                    </option>
                                    <option value="manual" @selected(old('city_mode') === 'manual')>
                                        Enter city manually
                                    </option>
                                </select>
                            </div>

                            {{-- EXISTING CITY --}}
                            <div id="city-select-wrapper" class="sm:col-span-2">
                                <label for="city" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    City <span class="text-red-500">*</span>
                                </label>

                                <select
                                    id="city"
                                    name="city"
                                    data-selected="{{ old('city', $selectedCity ?? '') }}"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('city') border-red-300 @enderror"
                                >
                                    <option value="">Select city</option>

                                    @foreach($cities as $cityOption)
                                        <option
                                            value="{{ $cityOption->id }}"
                                            @selected((string) old('city', $selectedCity ?? '') === (string) $cityOption->id)
                                        >
                                            {{ $cityOption->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('city')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- MANUAL CITY --}}
                            <div id="city-manual-wrapper" class="hidden sm:col-span-2">
                                <label for="city_manual" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Enter City <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="city_manual"
                                    name="city_manual"
                                    value="{{ old('city_manual') }}"
                                    maxlength="150"
                                    placeholder="Enter city name"
                                    disabled
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('city_manual') border-red-300 @enderror"
                                >

                                @error('city_manual')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- STREET --}}
                            <div class="sm:col-span-2">
                                <label for="street" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Street Address <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="street"
                                    name="street"
                                    value="{{ old('street', $address->street ?? '') }}"
                                    required
                                    maxlength="255"
                                    autocomplete="street-address"
                                    placeholder="Street, building, apartment, etc."
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('street') border-red-300 @enderror"
                                >

                                @error('street')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- POSTAL CODE --}}
                            <div>
                                <label for="postal_code" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Postal Code
                                </label>

                                <input
                                    type="text"
                                    id="postal_code"
                                    name="postal_code"
                                    value="{{ old('postal_code', $address->postal_code ?? '') }}"
                                    maxlength="30"
                                    autocomplete="postal-code"
                                    placeholder="Enter postal code"
                                    class="w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900/10 @error('postal_code') border-red-300 @enderror"
                                >

                                @error('postal_code')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </section>

                    <div class="border-t border-gray-100"></div>

                    {{-- DEFAULT ADDRESS --}}
<section>
    <label for="is_default" class="flex cursor-pointer items-start gap-3">
        <input
            type="checkbox"
            id="is_default"
            name="is_default"
            value="1"
            @checked((bool) old(
                'is_default',
                isset($address) && $address->exists
                    ? $address->is_default
                    : $addresses->isEmpty()
            ))
            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-2 focus:ring-gray-900/20"
        >

        <span>
            <span class="block text-sm font-medium text-gray-800">
                Set as default delivery address
            </span>
            <span class="mt-1 block text-xs leading-5 text-gray-500">
                Use this address as your preferred delivery destination during checkout.
            </span>
        </span>
    </label>

    @error('is_default')
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</section>

                </div>

                {{-- FORM FOOTER --}}
                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <a
                        href="{{ route('buyer.addresses.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 hover:text-gray-900"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 active:scale-[0.98]"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Address
                    </button>
                </div>
            </form>
        </div>

        {{-- SIDEBAR --}}
        <aside class="space-y-4">

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Delivery Details</h3>
                </div>

                <p class="mt-3 text-sm leading-6 text-gray-500">
                    Make sure your address and contact information are accurate to help avoid delivery delays.
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">Default Address</h3>
                </div>

                <p class="mt-3 text-sm leading-6 text-gray-500">
                    Your default address is preselected during checkout. You can change it at any time from your address list.
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                    Current addresses
                </p>
                <p class="mt-2 text-2xl font-semibold tracking-tight text-gray-900">
                    {{ $addresses->count() }}
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    Saved delivery {{ \Illuminate\Support\Str::plural('address', $addresses->count()) }}
                </p>
            </div>

        </aside>
    </div>
</div>

@php
    $initialRegions = $regions->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'country_id' => $item->country_id,
    ])->values();

    $initialCities = $cities->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'parent_id' => $item->parent_id,
        'country_id' => $item->country_id,
    ])->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const countrySelect = document.getElementById('country');
    const regionSelect = document.getElementById('region');
    const citySelect = document.getElementById('city');
    const cityMode = document.getElementById('city_mode');
    const citySelectWrapper = document.getElementById('city-select-wrapper');
    const cityManualWrapper = document.getElementById('city-manual-wrapper');
    const cityManualInput = document.getElementById('city_manual');

    if (
        !countrySelect ||
        !regionSelect ||
        !citySelect ||
        !cityMode ||
        !citySelectWrapper ||
        !cityManualWrapper ||
        !cityManualInput
    ) {
        return;
    }

    const regionsUrlTemplate = @json(route(
        'buyer.addresses.regions',
        ['country' => '__COUNTRY__']
    ));

    const citiesUrlTemplate = @json(route(
        'buyer.addresses.cities',
        [
            'country' => '__COUNTRY__',
            'region' => '__REGION__'
        ]
    ));

    let regionsController = null;
    let citiesController = null;

    function resetSelect(select, placeholder) {
        select.innerHTML = '';

        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;

        select.appendChild(option);
    }

    function populateOptions(select, items, placeholder, selectedId = '') {
        resetSelect(select, placeholder);

        items.forEach(item => {
            const option = document.createElement('option');

            option.value = item.id;
            option.textContent = item.name;
            option.selected = String(item.id) === String(selectedId);

            select.appendChild(option);
        });

        if (selectedId && !items.some(item => String(item.id) === String(selectedId))) {
            select.value = '';
        }
    }

    async function loadRegions(countryId, selectedRegionId = '', selectedCityId = '') {
        if (regionsController) {
            regionsController.abort();
        }

        if (citiesController) {
            citiesController.abort();
            citiesController = null;
        }

        resetSelect(regionSelect, countryId ? 'Loading regions...' : 'Select region');
        resetSelect(citySelect, 'Select city');

        regionSelect.disabled = Boolean(countryId);
        citySelect.disabled = true;

        if (!countryId) {
            resetSelect(regionSelect, 'Select region');
            regionSelect.disabled = false;
            updateCityMode();
            return;
        }

        const controller = new AbortController();
        regionsController = controller;

        const url = regionsUrlTemplate.replace(
            '__COUNTRY__',
            encodeURIComponent(countryId)
        );

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                signal: controller.signal
            });

            if (!response.ok) {
                throw new Error(`Failed to load regions: HTTP ${response.status}`);
            }

            const regions = await response.json();

            if (regionsController !== controller) {
                return;
            }

            populateOptions(
                regionSelect,
                regions,
                'Select region',
                selectedRegionId
            );

            if (regionSelect.value) {
                await loadCities(
                    countryId,
                    regionSelect.value,
                    selectedCityId
                );
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Unable to load regions:', error);
                resetSelect(regionSelect, 'Unable to load regions');
                resetSelect(citySelect, 'Select city');
            }
        } finally {
            if (regionsController === controller) {
                regionsController = null;
                regionSelect.disabled = false;
                updateCityMode();
            }
        }
    }

    async function loadCities(countryId, regionId, selectedCityId = '') {
        if (citiesController) {
            citiesController.abort();
        }

        resetSelect(
            citySelect,
            countryId && regionId ? 'Loading cities...' : 'Select city'
        );

        citySelect.disabled = true;

        if (!countryId || !regionId) {
            resetSelect(citySelect, 'Select city');
            updateCityMode();
            return;
        }

        const controller = new AbortController();
        citiesController = controller;

        const url = citiesUrlTemplate
            .replace('__COUNTRY__', encodeURIComponent(countryId))
            .replace('__REGION__', encodeURIComponent(regionId));

        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                signal: controller.signal
            });

            if (!response.ok) {
                throw new Error(`Failed to load cities: HTTP ${response.status}`);
            }

            const cities = await response.json();

            if (citiesController !== controller) {
                return;
            }

            populateOptions(
                citySelect,
                cities,
                'Select city',
                selectedCityId
            );
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Unable to load cities:', error);
                resetSelect(citySelect, 'Unable to load cities');
            }
        } finally {
            if (citiesController === controller) {
                citiesController = null;
                updateCityMode();
            }
        }
    }

    function updateCityMode() {
        const manual = cityMode.value === 'manual';

        citySelectWrapper.classList.toggle('hidden', manual);
        cityManualWrapper.classList.toggle('hidden', !manual);

        citySelect.disabled = manual || !countrySelect.value || !regionSelect.value;
        citySelect.required = !manual;

        cityManualInput.disabled = !manual;
        cityManualInput.required = manual;
    }

    countrySelect.addEventListener('change', function () {
        cityManualInput.value = '';

        loadRegions(this.value);
    });

    regionSelect.addEventListener('change', function () {
        citySelect.value = '';
        cityManualInput.value = '';

        loadCities(countrySelect.value, this.value);
    });

    cityMode.addEventListener('change', updateCityMode);

    // Restore saved selections when editing an address.
    const initialRegionId = regionSelect.value;
    const initialCityId = citySelect.value;

    loadRegions(
        countrySelect.value,
        initialRegionId,
        initialCityId
    );

    updateCityMode();
});
</script>

@endsection