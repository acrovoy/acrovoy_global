@extends('dashboard.layout')

@section('dashboard-sidebar')

<aside class="w-full lg:w-1/4 max-w-[320px] flex-shrink-0">

<div class="bg-[#182544] text-white p-6 rounded-xl shadow-lg sticky top-6 font-mono">




    <div class="mb-4">
        <h3 class="font-semibold text-lg text-white/80 border-b border-dashed border-white/30 pb-3 tracking-widest uppercase">
            Order Summary
        </h3>

         {{-- Дата и время по TZ пользователя --}}
        <p id="order-datetime" class="text-xs text-gray-500 mt-2">
            Date: --.--.----<br>
            Time: --:--:--
        </p>
    </div>


    {{-- Items --}}
    <div class="space-y-4">

        @foreach($cartItems as $item)

        <div class="space-y-1">

            <div class="flex justify-between text-sm text-gray-300">

                <span class="text-white/50">
                    {{ str_pad($loop->iteration, 2,'0', STR_PAD_LEFT) }}.
                    {{ $item->product?->name }}
                </span>

                <span class="text-sm text-white/50">
                    × {{ $item->quantity }}
                </span>

            </div>


            <div class="flex justify-between text-sm text-white/60">

                <span>
                    {{ number_format($item->price, 2) }} $ per unit
                </span>

                <span id="total-{{ $item->id }}" class="text-sm text-white/80">
                    {{ number_format($item->price * $item->quantity, 2) }} $
                </span>

            </div>


            {{-- dashed separator --}}
            <div class="border-b border-dashed border-white/20 pt-2"></div>

        </div>

        @endforeach

    </div>


    {{-- Shipping --}}
    <div class="flex justify-between items-center mt-4 text-white/50">

        <span>
            Shipping
        </span>

        <span id="shipping-cost" class="text-white/80">
            0.00 $
        </span>

    </div>


    {{-- dashed separator --}}
    <div class="border-b border-dashed border-white/30 my-4"></div>


    {{-- Total --}}
    <div class="flex justify-between items-center text-lg font-bold tracking-wider">

        <span>
            TOTAL
        </span>

        <span id="grand-total">
            {{ number_format($total, 2) }} $
        </span>

    </div>


    {{-- footer receipt line --}}
    <div class="border-b border-dashed border-white/40 mt-4"></div>

    {{-- amount in words --}}
@php
$formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);

$dollars = floor($total);
$cents = round(($total - $dollars) * 100);
@endphp

<div id="total-in-words" class="mt-4 text-[10px] text-white/60 italic leading-relaxed uppercase">
    Total amount:<br>
    {{ ucfirst($formatter->format($dollars)) }} dollars
    @if($cents > 0)
        and {{ $formatter->format($cents) }} cents
    @endif
</div>

</div>

</aside>

@endsection


@section('dashboard-content')



        <div class="flex justify-between">
<h2 class="text-2xl font-bold">Checkout</h2>
<a href="{{ route('buyer.cart.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← Сart
        </a>

        </div>



<p class="text-sm text-gray-500 mb-6">
                    Check your order, choose delivery, and enter invoice or shipping details.
                </p>

<x-alerts />

<form method="POST" action="{{ route('buyer.orders.store') }}" id="checkoutForm">
    @csrf

    {{-- Товары --}}
    <div class="bg-gray-50 p-4 rounded-lg shadow-lg mb-6 border border-gray-200">
        <h3 class="font-semibold mb-4">Product(s) in order:</h3>

        <div class="space-y-3">

@foreach($cartItems as $item)

<div class="flex justify-between items-center pb-3 {{ !$loop->last ? 'border-b' : '' }}">

    {{-- LEFT: image + title --}}
    <div class="flex items-center gap-3">

        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.

        <img
            src="{{ $item->image_url }}"
            alt="{{ $item->product?->translated_name ?? 'Product unavailable' }}"
            class="w-16 h-16 object-contain rounded bg-gray-50 border ml-2"
        >

        <div>

            <a href="{{ route('product.show', $item->product?->slug) }}"
   target="_blank"
   class="font-semibold text-gray-900 hover:text-gray-700 transition-colors">
    {{ $item->product?->name }}
</a>

            <p class="text-sm text-gray-500 mt-1">
                {{ number_format($item->price, 2) }} $ per unit
            </p>

        </div>

    </div>


    {{-- RIGHT: quantity box --}}
    <div
        class="w-10 h-10 flex items-center justify-center
               border border-gray-300 rounded-lg
               text-sm font-semibold text-gray-700
               bg-white shadow-sm mr-6"
    >
        {{ $item->quantity }}
    </div>

</div>

@endforeach

</div>

       
    </div>

  {{-- Delivery Option --}}
<div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">

    <div class="flex items-start justify-between gap-5">

        {{-- Left --}}
        <div class="min-w-0 flex-1">

            <h3 class="font-semibold text-gray-900">
                Delivery Option
            </h3>

            {{-- Selected delivery --}}
            <div id="selected-delivery-summary" class="mt-2">

                {{-- Empty state --}}
                <p
                    id="selected-delivery-empty"
                    class="text-sm text-gray-500"
                >
                    Select a delivery option
                </p>

                {{-- Selected state --}}
                <div id="selected-delivery-details" class="hidden">

                    <div class="flex items-start gap-3">

                        {{-- Courier logo --}}
                        <div
                            id="selected-delivery-logo-wrapper"
                            class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5"
                        >
                            <img
                                id="selected-delivery-logo"
                                src=""
                                alt=""
                                class="h-full w-full object-contain"
                            >
                        </div>

                        {{-- Delivery information --}}
                        <div class="min-w-0 flex-1">

                            {{-- Template + Delivery type --}}
                            <div class="flex items-center gap-2 flex-wrap">

                                <span
                                    id="selected-delivery-template-name"
                                    class="text-sm font-medium text-gray-900"
                                ></span>

                                <span
                                    id="selected-delivery-separator"
                                    class="hidden text-gray-300 select-none"
                                    aria-hidden="true"
                                >
                                    /
                                </span>

                                <span
                                    id="selected-delivery-label"
                                    class="text-sm text-gray-600"
                                ></span>

                            </div>

                            {{-- Courier name --}}
                            <!-- <p
                                id="selected-delivery-courier-name"
                                class="hidden mt-1 text-xs text-gray-500"
                            ></p> -->

                            {{-- Shipping price --}}
                            <div class="mt-1 flex items-center">

                                <span class="text-sm text-gray-500">
                                
                                </span>

                                <span
                                    id="selected-delivery-price"
                                    class="text-sm font-semibold text-gray-900"
                                ></span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Action --}}
        <button
            type="button"
            id="delivery-option-button"
            onclick="openDeliveryModal()"
            class="shrink-0 inline-flex items-center justify-center px-4 py-2 text-sm font-medium bg-blue-900 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-900/20 transition"
            aria-controls="delivery-option-modal"
            aria-haspopup="dialog"
        >
            Select
        </button>

    </div>

</div>

@include('dashboard.buyer.partials.delivery-option-modal')





{{-- Payment Method --}}
<div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">
    <div class="flex items-start justify-between gap-5">
        <div class="min-w-0 flex-1">
            <h3 class="font-semibold text-gray-900">
                Payment Method
            </h3>

            {{-- Selected payment method --}}
            <div id="selected-payment-summary" class="mt-2">
                <p id="selected-payment-empty" class="text-sm text-gray-500">
                    Select a payment method
                </p>

                <div id="selected-payment-details" class="hidden">
                    <div class="flex items-start gap-3">
                        <div
                            id="selected-payment-logo-wrapper"
                            class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5"
                        >
                            <img
                                id="selected-payment-logo"
                                src=""
                                alt=""
                                class="h-full w-full object-contain"
                            >
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    id="selected-payment-name"
                                    class="text-sm font-medium text-gray-900"
                                ></span>
                            </div>

                            <p
                                id="selected-payment-description"
                                class="hidden mt-1 text-xs text-gray-500"
                            ></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button
            type="button"
            id="payment-method-button"
            onclick="openPaymentMethodModal()"
            class="shrink-0 inline-flex items-center justify-center px-4 py-2 text-sm font-medium bg-blue-900 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-900/20 transition"
            aria-controls="payment-method-modal"
            aria-haspopup="dialog"
        >
            Select
        </button>
    </div>

    {{-- Submitted payment method ID --}}
    <input
        type="hidden"
        name="payment_method_id"
        id="selected-payment-method-id"
        value="{{ old('payment_method_id') }}"
    >

    @error('payment_method_id')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@include('dashboard.buyer.partials.payment-method-modal')





   {{-- =========================================================
    SAVED DELIVERY ADDRESSES
========================================================= --}}

<div class="bg-white p-5 rounded-xl border border-gray-200 mb-6">

    <div class="flex items-start justify-between gap-4 mb-4">

        <div>
            <h3 class="text-base font-semibold text-gray-900">
                Saved Address
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Select a saved address or enter a new delivery address below.
            </p>
        </div>

    </div>

    <div>
        <label
            for="saved-addresses"
            class="block text-sm font-medium text-gray-700 mb-1.5"
        >
            Saved addresses
        </label>

        <select
            id="saved-addresses"
            name="saved_address_id"
            class="w-full
                   border border-gray-300
                   rounded-lg
                   px-3 py-2.5
                   text-sm text-gray-900
                   bg-white
                   focus:outline-none
                   focus:ring-2
                   focus:ring-blue-900/20
                   focus:border-blue-900
                   transition"
        >
            <option value="">
                Select a saved address
            </option>

            @foreach($savedAddresses as $address)

                <option
                    value="{{ $address->id }}"
                    data-first_name="{{ $address->first_name }}"
                    data-last_name="{{ $address->last_name }}"
                    data-country="{{ $address->country }}"
                    data-city="{{ $address->city }}"
                    data-region="{{ $address->region }}"
                    data-street="{{ $address->street }}"
                    data-postal_code="{{ $address->postal_code }}"
                    data-phone="{{ $address->phone }}"
                    {{ $lastAddress && $lastAddress->id === $address->id ? 'selected' : '' }}
                >
                    {{ $address->first_name }}
                    {{ $address->last_name ? ' ' . $address->last_name : '' }}
                    — {{ $address->street }}, {{ $address->city }}
                </option>

            @endforeach
        </select>
    </div>

</div>


{{-- =========================================================
    ADDRESS STATE
========================================================= --}}

<input
    type="hidden"
    name="address_modified"
    id="address_modified"
    value="0"
>


<label
    class="flex items-start gap-3
           mb-6
           text-sm text-gray-600
           cursor-pointer"
>
    <input
        type="checkbox"
        name="save_as_new"
        value="1"
        class="mt-0.5
               w-4 h-4
               rounded
               border-gray-300
               text-blue-900
               focus:ring-blue-900/20"
    >

    <span>
        Save this address and contact as a new saved address
    </span>
</label>


{{-- =========================================================
    CONTACT INFORMATION
========================================================= --}}

<div class="bg-white p-5 rounded-xl border border-gray-200 mb-6">

    <div class="mb-5">

        <h3 class="text-base font-semibold text-gray-900">
            Contact Information
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Enter the contact details for this delivery.
        </p>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- First Name --}}
        <div>

            <label
                for="first_name"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                First Name
            </label>

            <input
                type="text"
                name="first_name"
                id="first_name"
                value="{{ $lastAddress->first_name ?? auth()->user()->first_name ?? '' }}"
                autocomplete="given-name"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       placeholder-gray-400
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

        </div>


        {{-- Last Name --}}
        <div>

            <label
                for="last_name"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                Last Name
            </label>

            <input
                type="text"
                name="last_name"
                id="last_name"
                value="{{ $lastAddress->last_name ?? old('last_name') ?? '' }}"
                autocomplete="family-name"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       placeholder-gray-400
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

        </div>


        {{-- Phone --}}
        <div class="sm:col-span-2">

            <label
                for="phone"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                Phone Number
            </label>

            <input
                type="tel"
                name="phone"
                id="phone"
                value="{{ $lastAddress->phone ?? old('phone') ?? '' }}"
                autocomplete="tel"
                inputmode="tel"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       placeholder-gray-400
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

        </div>

    </div>

</div>


{{-- =========================================================
    DELIVERY ADDRESS
========================================================= --}}

<div class="bg-white p-5 rounded-xl border border-gray-200 mb-6">

    <div class="mb-5">

        <h3 class="text-base font-semibold text-gray-900">
            Delivery Address
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Enter the address where your order should be delivered.
        </p>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Country --}}
        <div>

            <label
                for="country"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                Country
            </label>

            <select
                name="country"
                id="country"
                autocomplete="country"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       bg-white
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

                <option value="">
                    Select country
                </option>

                @foreach($countries as $country)

                    <option
                        value="{{ $country->id }}"
                        {{ $lastAddress && $lastAddress->country == $country->id ? 'selected' : '' }}
                    >
                        {{ $country->name }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- Region --}}
        <div>

            <label
                for="region"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                Region / State
            </label>

            <select
                name="region"
                id="region"
                autocomplete="address-level1"
                disabled
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       bg-gray-50
                       disabled:cursor-not-allowed
                       disabled:text-gray-400
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

                <option value="">
                    Select region
                </option>

            </select>

        </div>


        {{-- City --}}
        <div>

            <label
                for="city"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                City
            </label>

            <select
                name="city"
                id="city"
                autocomplete="address-level2"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       bg-white
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

                <option value="">
                    Select city
                </option>

            </select>

            <p class="mt-1.5 text-xs leading-relaxed text-gray-500">
                Can't find your city or location? Enter it manually below.
            </p>

            <input
                type="text"
                name="city_manual"
                id="city_manual"
                placeholder="Enter city manually"
                autocomplete="address-level2"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       mt-2
                       text-sm text-gray-900
                       placeholder-gray-400
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

        </div>


        {{-- Street --}}
        <div class="sm:col-span-2">

            <label
                for="street"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                Street, Building, Apartment
            </label>

            <input
                type="text"
                name="street"
                id="street"
                value="{{ $lastAddress->street ?? old('street') ?? '' }}"
                autocomplete="street-address"
                placeholder="Street, building, apartment"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       placeholder-gray-400
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

        </div>


        {{-- Postal Code --}}
        <div>

            <label
                for="postal_code"
                class="block text-sm font-medium text-gray-700 mb-1.5"
            >
                Postal Code
            </label>

            <input
                type="text"
                name="postal_code"
                id="postal_code"
                value="{{ $lastAddress->postal_code ?? old('postal_code') ?? '' }}"
                autocomplete="postal-code"
                inputmode="numeric"
                placeholder="Postal code"
                class="w-full
                       border border-gray-300
                       rounded-lg
                       px-3 py-2.5
                       text-sm text-gray-900
                       placeholder-gray-400
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-900/20
                       focus:border-blue-900
                       transition"
            >

        </div>

    </div>

</div>





    <input
    type="hidden"
    name="delivery_template_id"
    id="selected-delivery-template"
    value=""
>

    <input type="hidden"
        name="delivery_price"
        id="delivery-price-input"
        value="0">

    <input type="hidden"
        name="total"
        id="total-input"
        value="{{ $total }}">

    <div class="text-right">
        <button type="submit"
                class="px-4 py-2 text-sm bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition">
            Подтвердить заказ
        </button>
    </div>
</form>

{{-- JS --}}
<script>
(function () {
    const tz = @json(auth()->user()->timezone ?? null)
              || localStorage.getItem('timezone')
              || Intl.DateTimeFormat().resolvedOptions().timeZone;

    // Функция для текущей даты и времени по TZ пользователя
    function updateOrderDateTime() {
        const now = new Date();
        const options = { 
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            timeZone: tz
        };
        const formatted = now.toLocaleString([], options);
        const [date, time] = formatted.split(', ');
        document.getElementById('order-datetime').innerHTML = `Date: ${date}<br>Time: ${time}`;
    }

    updateOrderDateTime();
    setInterval(updateOrderDateTime, 1000);
})();
</script>


<script>
let cartItems = @json($cartItems);
const regionsUrl = @json(route('buyer.locations.regions'));
const locationsUrl = @json(route('buyer.locations.locations'));

const countrySelect = document.getElementById('country');
const regionSelect = document.getElementById('region');
const cityInput = document.getElementById('city');
const cityManualInput = document.getElementById('city_manual');

// ============================================
// 0. Инициализация
// ============================================
if (regionSelect) regionSelect.disabled = !countrySelect?.value;

// 👉 блокируем select города если регион не выбран
if (cityInput) cityInput.disabled = !regionSelect?.value;

// ❗ Вариант 2 — поле ручного ввода всегда активно
if (cityManualInput) cityManualInput.disabled = false;


document.addEventListener('DOMContentLoaded', function() {
    // Если есть сохранённый адрес с выбранной страной и регионом — подгружаем их
    @if($lastAddress)
        @if($lastAddress->country)
            fetchRegions({{ $lastAddress->country }}, {{ $lastAddress->region }});
            regionSelect.disabled = false;
        @endif

        @if($lastAddress->region)
            fetchLocations({{ $lastAddress->region }}, '{{ $lastAddress->city }}');
        @endif
    @endif
});

// ============================================
// 1. Подгрузка и заполнение сохранённых адресов
// ============================================
document.getElementById('saved-addresses')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (!selected.value) return;

    document.getElementById('first_name').value = selected.dataset.first_name || '';
    document.getElementById('last_name').value = selected.dataset.last_name || '';
    document.getElementById('country').value = selected.dataset.country || '';
    document.getElementById('region').value = selected.dataset.region || '';
    document.getElementById('street').value = selected.dataset.street || '';
    document.getElementById('postal_code').value = selected.dataset.postal_code || '';
    document.getElementById('phone').value = selected.dataset.phone || '';

    // Подгрузка регионов
    if (selected.dataset.country) {
        fetchRegions(selected.dataset.country, selected.dataset.region);
        regionSelect.disabled = false;
    } else {
        regionSelect.disabled = true;
        regionSelect.innerHTML = '<option value="">Выберите регион</option>';
    }

    // Подгрузка городов
    if (selected.dataset.region) {
        fetchLocations(selected.dataset.region, selected.dataset.city);
    }

    // 👉 Заполняем ручное поле если город есть
    if (selected.dataset.city) {
        cityManualInput.value = selected.dataset.city;
    }
});


// ============================================
// 2. Подгрузка регионов по выбранной стране
// ============================================
countrySelect?.addEventListener('change', function() {
    const countryId = this.value;

    if (!countryId) {
        regionSelect.disabled = true;
        regionSelect.innerHTML = '<option value="">Выберите регион</option>';

        // очищаем город
        cityInput.innerHTML = '<option value="">Выберите город</option>';
        cityInput.disabled = true;

        cityManualInput.value = '';

        return;
    }

    regionSelect.disabled = false;

    cityInput.innerHTML = '<option value="">Выберите город</option>';
    cityInput.disabled = true;
    cityManualInput.value = '';

    fetchRegions(countryId);
});


// ============================================
// 3. Подгрузка городов по выбранному региону
// ============================================
regionSelect?.addEventListener('change', function() {
    const regionId = this.value;

    if (!regionId) {
        cityInput.innerHTML = '<option value="">Выберите город</option>';
        cityInput.disabled = true;
        return;
    }

    fetchLocations(regionId);
});


// ============================================
// Подгрузка локаций (города)
// ============================================
function fetchLocations(regionId, selectedCityId = null) {
    if (!cityInput) return;

    cityInput.innerHTML = '<option value="">Выберите город</option>';
    cityInput.disabled = true;

    fetch(`${locationsUrl}?region_id=${regionId}`)
        .then(res => res.json())
        .then(data => {

            let cityFound = false;

            data.forEach(loc => {
                const option = document.createElement('option');
                
                // Передаем ID города в value
                option.value = loc.id;

                // Название города для отображения
                option.textContent = loc.name;

                // Сохраняем название в data-name
                option.dataset.name = loc.name;
                
                // Если выбранный город совпадает
                if (selectedCityId && (selectedCityId == loc.id || selectedCityId == loc.name)) {
                    option.selected = true;
                    cityFound = true;
                }

                cityInput.appendChild(option);
            });

            cityInput.disabled = false;

            // Если выбранный город не найден — оставляем его в ручном поле
            if (selectedCityId && !cityFound) {
                cityManualInput.value = selectedCityId; // Или можно передать название
            }
        })
        .catch(console.error);
}


// ============================================
// Если пользователь выбирает город из списка — очищаем ручной ввод
// ============================================
cityInput?.addEventListener('change', function() {
    if (this.value !== '') {
        // При выборе города из списка очищаем ручной ввод
        cityManualInput.value = '';

        // Можно дополнительно синхронизировать название:
        const selectedOption = this.selectedOptions[0];
        if (selectedOption) {
            cityManualInput.dataset.name = selectedOption.dataset.name;
        }
    }
});


cityManualInput?.addEventListener('input', function() {
    if (this.value.trim() !== '') {
        cityInput.value = '';
    }
});

// ============================================
// 4. Подгрузка регионов
// ============================================
function fetchRegions(countryId, selectedRegionId = null) {
    if (!regionSelect) return;

    regionSelect.innerHTML = '<option value="">Выберите регион</option>';

    if (!countryId) return;

    fetch(`${regionsUrl}?country_id=${countryId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(r => {
                const option = document.createElement('option');
                option.value = r.id;
                option.textContent = r.name;

                if (selectedRegionId && selectedRegionId == r.id) {
                    option.selected = true;
                }

                regionSelect.appendChild(option);
            });
        })
        .catch(console.error);
}


// ============================================
// 5. Работа с корзиной: количество и пересчёт
// ============================================
function updateQuantity(itemId, delta) {
    const item = cartItems.find(i => i.id === itemId);
    if (!item) return;

    item.quantity += delta;
    if (item.quantity < 1) item.quantity = 1;

    document.getElementById('qty-' + itemId).textContent = item.quantity;
    document.getElementById('total-' + itemId).textContent =
        (item.price * item.quantity).toFixed(2) + ' $';

    recalcTotal();
}

function recalcTotal() {

    let total = cartItems.reduce(
        (sum, i) => sum + Number(i.price) * Number(i.quantity),
        0
    );

    const selected = document.querySelector(
        'input[name="delivery_option_id"]:checked'
    );

    let shippingPrice = 0;

    if (selected) {
        shippingPrice = parseFloat(
            selected.dataset.price || 0
        );

        const templateId = selected.dataset.templateId || '';

        const templateInput = document.getElementById(
            'selected-delivery-template'
        );

        if (templateInput) {
            templateInput.value = templateId;
        }
    }

    // ================================
    // Shipping
    // ================================

    const shippingCostElement =
        document.getElementById('shipping-cost');

    if (shippingCostElement) {

        if (shippingPrice > 0) {
            shippingCostElement.textContent =
                shippingPrice.toFixed(2) + ' $';
        } else {
            shippingCostElement.textContent = 'FREE';
        }
    }

    // ================================
    // Grand Total
    // ================================

    total += shippingPrice;

    document.getElementById('grand-total').textContent =
        total.toFixed(2) + ' $';

    document.getElementById('total-input').value =
        total.toFixed(2);

    // ================================
    // Delivery price input
    // ================================

    const deliveryPriceInput =
        document.getElementById('delivery-price-input');

    if (deliveryPriceInput) {
        deliveryPriceInput.value =
            shippingPrice.toFixed(2);
    }

    // ================================
    // Сумма словами
    // ================================

    const dollars = Math.floor(total);

    const cents = Math.round(
        (total - dollars) * 100
    );

    let words =
        numberToWords(dollars) + ' dollars';

    if (cents > 0) {
        words +=
            ' and ' +
            numberToWords(cents) +
            ' cents';
    }

    const wordsElement =
        document.getElementById('total-in-words');

    if (wordsElement) {
        wordsElement.textContent =
            'Total amount: ' +
            words.charAt(0).toUpperCase() +
            words.slice(1);
    }
}

function numberToWords(num) {

    const ones = [
        '', 'one','two','three','four','five',
        'six','seven','eight','nine','ten',
        'eleven','twelve','thirteen','fourteen','fifteen',
        'sixteen','seventeen','eighteen','nineteen'
    ];

    const tens = [
        '', '', 'twenty','thirty','forty',
        'fifty','sixty','seventy','eighty','ninety'
    ];

    if (num < 20) return ones[num];

    if (num < 100)
        return tens[Math.floor(num / 10)] +
            (num % 10 ? ' ' + ones[num % 10] : '');

    if (num < 1000)
        return ones[Math.floor(num / 100)] +
            ' hundred ' +
            (num % 100 ? numberToWords(num % 100) : '');

    if (num < 1000000)
        return numberToWords(Math.floor(num / 1000)) +
            ' thousand ' +
            (num % 1000 ? numberToWords(num % 1000) : '');

    return num;
}



function openDeliveryModal() {
    const modal = document.getElementById(
        'delivery-option-modal'
    );

    if (!modal) return;

    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');

    document.body.classList.add('overflow-hidden');
}


function closeDeliveryModal() {
    const modal = document.getElementById(
        'delivery-option-modal'
    );

    if (!modal) return;

    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');

    document.body.classList.remove('overflow-hidden');
}


function confirmDeliveryOption() {

    const selected = document.querySelector(
        'input[name="delivery_option_id"]:checked'
    );

    if (!selected) {
        return;
    }

    const templateId =
        selected.dataset.templateId || '';

    const templateName =
        selected.dataset.templateName || '';

    const deliveryLabel =
        selected.dataset.label || '';

    const price =
        parseFloat(selected.dataset.price || 0);

    // ================================
    // Courier
    // ================================

    const courierLogo =
        selected.dataset.courierLogo || '';

    const courierName =
        selected.dataset.courierName || '';

    // ================================
    // Delivery template
    // ================================

    const templateInput =
        document.getElementById('selected-delivery-template');

    if (templateInput) {
        templateInput.value = templateId;
    }

    // ================================
    // Selected summary
    // ================================

    const emptyBlock =
        document.getElementById('selected-delivery-empty');

    const detailsBlock =
        document.getElementById('selected-delivery-details');

    const templateNameElement =
        document.getElementById('selected-delivery-template-name');

    const labelElement =
        document.getElementById('selected-delivery-label');

    const priceElement =
        document.getElementById('selected-delivery-price');

    const actionButton =
        document.getElementById('delivery-option-button');

    // ================================
    // Courier logo and name
    // ================================

    const logoWrapper =
        document.getElementById('selected-delivery-logo-wrapper');

    const logoImage =
        document.getElementById('selected-delivery-logo');

    const courierNameElement =
        document.getElementById('selected-delivery-courier-name');

    if (logoWrapper && logoImage) {

        // Reset previous logo
        logoImage.onload = null;
        logoImage.onerror = null;

        logoImage.removeAttribute('src');

        logoWrapper.classList.add('hidden');
        logoWrapper.classList.remove('flex');

        if (courierLogo.trim() !== '') {

            let logoUrl = courierLogo.trim();

            // Absolute URL or root-relative path
            if (!/^https?:\/\//i.test(logoUrl)) {

                // Remove a possible leading slash
                logoUrl = logoUrl.replace(/^\/+/, '');

                // Avoid adding storage/ twice
                if (!logoUrl.startsWith('storage/')) {
                    logoUrl = 'storage/' + logoUrl;
                }

                // Laravel-compatible URL
                logoUrl = '/' + logoUrl;
            }

            logoImage.alt = courierName || 'Shipping courier';

            logoImage.onload = function () {
                logoWrapper.classList.remove('hidden');
                logoWrapper.classList.add('flex');
            };

            logoImage.onerror = function () {
                logoWrapper.classList.add('hidden');
                logoWrapper.classList.remove('flex');

                console.error(
                    'Courier logo failed to load:',
                    logoUrl
                );
            };

            logoImage.src = logoUrl;
        }
    }

    // Courier name
    if (courierNameElement) {
        courierNameElement.textContent = courierName;
        courierNameElement.classList.toggle(
            'hidden',
            !courierName
        );
    }

    // ================================
    // Template name
    // ================================

    if (templateNameElement) {
        templateNameElement.textContent = templateName;
    }

    // ================================
    // Delivery type
    // ================================

    if (labelElement) {
        labelElement.textContent = deliveryLabel;
    }

    // ================================
    // Price
    // ================================

    if (priceElement) {

        if (price > 0) {

            priceElement.textContent =
                '$' + price.toFixed(2);

            priceElement.classList.remove(
                'text-emerald-600'
            );

            priceElement.classList.add(
                'text-gray-900'
            );

        } else {

            priceElement.textContent = 'FREE';

            priceElement.classList.remove(
                'text-gray-900'
            );

            priceElement.classList.add(
                'text-emerald-600'
            );
        }
    }

    // ================================
    // Toggle summary state
    // ================================

    if (emptyBlock) {
        emptyBlock.classList.add('hidden');
    }

    if (detailsBlock) {
        detailsBlock.classList.remove('hidden');
    }

    // ================================
    // Update action button
    // ================================

    if (actionButton) {
        actionButton.textContent = 'Change';
    }

    // ================================
    // Close modal + recalculate
    // ================================

    closeDeliveryModal();

    recalcTotal();
}



window.addEventListener('DOMContentLoaded', recalcTotal);


// ============================================
// 6. Отметка изменения адреса
// ============================================
[
  'first_name',
  'last_name',
  'country',
  'city',
  'region',
  'street',
  'postal_code',
  'phone'
].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;

    el.addEventListener('input', () => {
        document.getElementById('address_modified').value = '1';
    });
});
</script>

<script>
    // =========================================================
    // PAYMENT METHOD MODAL
    // =========================================================

    function openPaymentMethodModal() {
        const modal = document.getElementById('payment-method-modal');

        if (!modal) {
            return;
        }

        const selectedId = document.getElementById(
            'selected-payment-method-id'
        )?.value;

        const radios = modal.querySelectorAll(
            'input[name="payment_method_option_id"]'
        );

        radios.forEach(radio => {
            radio.checked = selectedId
                ? radio.value === selectedId
                : false;
        });

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closePaymentMethodModal() {
        const modal = document.getElementById('payment-method-modal');

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // =========================================================
    // SVG ICON HELPERS
    // =========================================================

    function decodePaymentMethodIcon(encodedIcon) {
        if (!encodedIcon) {
            return '';
        }

        try {
            const binary = atob(encodedIcon);

            const bytes = Uint8Array.from(
                binary,
                character => character.charCodeAt(0)
            );

            return new TextDecoder('utf-8').decode(bytes);
        } catch (error) {
            console.error('Unable to decode payment method icon.', error);
            return '';
        }
    }

    function isSafePaymentMethodSvg(svg) {
        if (!svg || typeof svg !== 'string') {
            return false;
        }

        const parser = new DOMParser();
        const documentSvg = parser.parseFromString(
            svg,
            'image/svg+xml'
        );

        if (
            documentSvg.querySelector('parsererror') ||
            documentSvg.documentElement?.localName !== 'svg'
        ) {
            return false;
        }

        // Reject potentially executable or externally loaded SVG content.
        if (
            documentSvg.querySelector(
                'script, foreignObject, iframe, object, embed, audio, video'
            )
        ) {
            return false;
        }

        const elements = documentSvg.querySelectorAll('*');

        for (const element of elements) {
            for (const attribute of Array.from(element.attributes)) {
                const name = attribute.name.toLowerCase();
                const value = attribute.value.trim();

                if (name.startsWith('on')) {
                    return false;
                }

                if (
                    ['href', 'xlink:href', 'src'].includes(name) &&
                    value &&
                    !value.startsWith('#') &&
                    !value.startsWith('data:')
                ) {
                    return false;
                }

                if (
                    name === 'style' &&
                    /url\s*\(|expression\s*\(/i.test(value)
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    function renderPaymentMethodIcon(encodedIcon) {
        const wrapper = document.getElementById(
            'selected-payment-logo-wrapper'
        );

        const imageElement = document.getElementById(
            'selected-payment-logo'
        );

        if (!wrapper || !imageElement) {
            return;
        }

        // Remove the previously rendered SVG, if any.
        const existingIcon = wrapper.querySelector(
            '[data-payment-method-svg]'
        );

        if (existingIcon) {
            existingIcon.remove();
        }

        imageElement.classList.remove('hidden');
        imageElement.removeAttribute('src');
        imageElement.removeAttribute('alt');

        const svg = decodePaymentMethodIcon(encodedIcon);

        if (!svg || !isSafePaymentMethodSvg(svg)) {
            wrapper.classList.add('hidden');
            return;
        }

        const parser = new DOMParser();
        const parsed = parser.parseFromString(
            svg,
            'image/svg+xml'
        );

        const svgElement = parsed.documentElement;

        // Normalize presentation for the checkout summary.
        svgElement.setAttribute('aria-hidden', 'true');
        svgElement.setAttribute('focusable', 'false');
        svgElement.removeAttribute('width');
        svgElement.removeAttribute('height');

        svgElement.style.width = '100%';
        svgElement.style.height = '100%';
        svgElement.style.maxWidth = '100%';
        svgElement.style.maxHeight = '40px';
        svgElement.style.display = 'block';

        // Import the SVG as a DOM node rather than using innerHTML.
        const importedSvg = document.importNode(svgElement, true);
        importedSvg.setAttribute('data-payment-method-svg', '');

        imageElement.classList.add('hidden');
        wrapper.appendChild(importedSvg);
        wrapper.classList.remove('hidden');
    }

    // =========================================================
    // UPDATE SELECTED PAYMENT METHOD SUMMARY
    // =========================================================

    function updatePaymentMethodSummary(radio) {
        if (!radio) {
            return;
        }

        const methodId = radio.dataset.methodId || radio.value;
        const methodName = radio.dataset.methodName || '';
        const methodDescription = radio.dataset.methodDescription || '';
        const methodIcon = radio.dataset.methodLogo || '';

        const hiddenInput = document.getElementById(
            'selected-payment-method-id'
        );

        const emptyState = document.getElementById(
            'selected-payment-empty'
        );

        const details = document.getElementById(
            'selected-payment-details'
        );

        const nameElement = document.getElementById(
            'selected-payment-name'
        );

        const descriptionElement = document.getElementById(
            'selected-payment-description'
        );

        const button = document.getElementById(
            'payment-method-button'
        );

        if (!methodId) {
            return;
        }

        if (hiddenInput) {
            hiddenInput.value = methodId;
        }

        if (emptyState) {
            emptyState.classList.add('hidden');
        }

        if (details) {
            details.classList.remove('hidden');
        }

        if (nameElement) {
            nameElement.textContent = methodName;
        }

        if (descriptionElement) {
            descriptionElement.textContent = methodDescription;
            descriptionElement.classList.toggle(
                'hidden',
                !methodDescription
            );
        }

        renderPaymentMethodIcon(methodIcon);

        if (button) {
            button.textContent = 'Change';
        }
    }

    // =========================================================
    // CONFIRM PAYMENT METHOD
    // =========================================================

    function confirmPaymentMethod() {
        const modal = document.getElementById('payment-method-modal');

        if (!modal) {
            return;
        }

        const selected = modal.querySelector(
            'input[name="payment_method_option_id"]:checked'
        );

        if (!selected) {
            alert('Please select a payment method.');
            return;
        }

        const methodId = selected.dataset.methodId || selected.value;

        if (!methodId) {
            alert('Unable to determine the selected payment method.');
            return;
        }

        updatePaymentMethodSummary(selected);
        closePaymentMethodModal();
    }

    // =========================================================
    // RESTORE SELECTED PAYMENT METHOD AFTER PAGE RELOAD
    // =========================================================

    document.addEventListener('DOMContentLoaded', function () {
        const hiddenInput = document.getElementById(
            'selected-payment-method-id'
        );

        const modal = document.getElementById('payment-method-modal');

        if (!hiddenInput || !modal || !hiddenInput.value) {
            return;
        }

        const selectedId = hiddenInput.value;

        const radio = Array.from(
            modal.querySelectorAll(
                'input[name="payment_method_option_id"]'
            )
        ).find(input => input.value === selectedId);

        if (!radio) {
            return;
        }

        radio.checked = true;
        updatePaymentMethodSummary(radio);
    });

    // =========================================================
    // CLOSE MODAL WITH ESCAPE
    // =========================================================

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePaymentMethodModal();
        }
    });
</script>



@endsection
