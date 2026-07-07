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

        

        <div class="space-y-1">

            <div class="flex justify-between text-sm text-gray-300">

                <span class="text-white/50">
                    
                    {{ $cartItem->rfq?->title }}
                </span>

                <span class="text-sm text-white/50">
                    × {{ $cartItem->quantity }}
                </span>

            </div>


            <div class="flex justify-between text-sm text-white/60">

                <span>
                    {{ number_format($cartItem->price, 2) }} $ per unit
                </span>

                <span id="total-{{ $cartItem->id }}" class="text-sm text-white/80">
                    {{ number_format($cartItem->price * $cartItem->quantity, 2) }} $
                </span>

            </div>


            {{-- dashed separator --}}
            <div class="border-b border-dashed border-white/20 pt-2"></div>

        </div>

        

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
<a href="{{ route('rfqs.workspace', $cartItem->rfq?->id) }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            ← RFQ Overview
        </a>

        </div>



<p class="text-sm text-gray-500 mb-6">
                    Check your order, choose delivery, and enter invoice or shipping details.
                </p>

<x-alerts />

<form method="POST" action="{{ route('buyer.orders.rfq-store') }}" id="checkoutForm">
    @csrf

    {{-- Товары --}}
    <div class="bg-gray-50 p-4 rounded-lg shadow-lg mb-6 border border-gray-200">
        <h3 class="font-semibold mb-4">Product(s) in order:</h3>

        <div class="space-y-3">



<div class="flex justify-between items-center pb-3">

    {{-- LEFT: image + title --}}
    <div class="flex items-center gap-3">

       

        <img
    src="{{ asset('images/furniture_icon.jpg') }}"
    alt="RFQ"
    class="w-16 h-16 object-contain rounded bg-gray-50 border ml-2"
>

        <div>

            <a href="{{ route('rfqs.workspace', $cartItem->rfq?->id) }}"
   target="_blank"
   class="font-semibold text-gray-900 hover:text-gray-700 transition-colors">
    {{ $cartItem->rfq?->title }}
</a>

            <p class="text-sm text-gray-500 mt-1">
                {{ number_format($cartItem->price, 2) }} $ per unit
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
        {{ $cartItem->quantity }}
    </div>

</div>



</div>

       
    </div>

    {{-- Выбор доставки --}}
<div class="bg-white p-4 rounded-lg border border-gray-200 mb-6">
    <h3 class="font-semibold mb-2">Select the Delivery Option</h3>

    <div
        x-data="{ selectedShipping: {{ $shippingOptions->first()->id ?? 0 }} }"
        class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5"
    >

    
       @foreach($shippingOptions as $template)
            @php
                $totalShippingPrice = $template->computed_price * $cartItem->quantity;
            @endphp
            
            
       
            <label
                class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition flex flex-col gap-2"
                :class="{
                    'bg-gray-50 border-gray-300 shadow-md': selectedShipping == {{ $template->id }}
                }"
                @click="
                    selectedShipping = {{ $template->id }};
                    $refs.radio{{ $template->id }}.checked = true;
                    recalcTotal();
                "
            >
            <input
                type="radio"
                name="delivery_template_id"
                value="{{ $template->id }}"
                x-ref="radio{{ $template->id }}"
                class="hidden"
                data-price="{{ $totalShippingPrice }}"
                {{ $loop->first ? 'checked' : '' }}
            >

            <h4 class="font-semibold text-gray-900">{{ $template->title }}</h4>

            @if($template->description)
            <p class="text-gray-700 text-sm mt-1">{{ $template->description }}</p>
            @endif

            <div class="mt-2 text-gray-700 text-sm grid grid-cols-2 gap-2">
                

                @if(empty($template->price) || $template->price == 0 || empty($template->delivery_time))
                <div class="col-span-2 inline-flex items-center gap-2
                    bg-blue-50 border border-blue-100
                    px-3 py-1.5 rounded-lg text-blue-500 font-medium text-xs">
                    Delivery cost and delivery time will be calculated after order placement
                </div>
                @else
                {{-- PRICE --}}
                <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-lg">
                    <span class="text-sm text-blue-900 font-medium">Price:</span>
                    <span class="text-base font-semibold text-blue-900">
                        ${{ number_format($totalShippingPrice, 2) }}
                    </span>
                </div>

                {{-- DELIVERY TIME --}}
                <div>
                    <div class="font-medium">Delivery Time:</div>
                    <div>{{ $template->delivery_time }} days</div>
                </div>
                @endif
            </div>
         </label>
        @endforeach
    </div>
</div>


    {{-- Delivery Address --}}
<div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-5">
        Delivery Address
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">

        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                Contact Person
            </div>
            <div class="text-sm text-gray-900">
                {{ $deliveryAddress->first_name }}
                {{ $deliveryAddress->last_name }}
            </div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                Phone
            </div>
            <div class="text-sm text-gray-900">
                {{ $deliveryAddress->phone ?: '—' }}
            </div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                Country
            </div>
            <div class="text-sm text-gray-900">
                {{ optional($deliveryAddress->countryRelation)->name ?? $deliveryAddress->country }}
            </div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                Region
            </div>
            <div class="text-sm text-gray-900">
                {{ optional($deliveryAddress->regionRelation)->name ?? $deliveryAddress->region }}
            </div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                City
            </div>
            <div class="text-sm text-gray-900">
                {{ $deliveryAddress->city }}
            </div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                Postal Code
            </div>
            <div class="text-sm text-gray-900">
                {{ $deliveryAddress->postal_code ?: '—' }}
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                Street Address
            </div>
            <div class="text-sm text-gray-900">
                {{ $deliveryAddress->street }}
            </div>
        </div>

    </div>
</div>



    <input type="hidden" name="rfq" value="{{ $cartItem->rfq?->id }}">
    <input type="hidden" name="quantity" value="{{ $cartItem->quantity }}">
    <input type="hidden" name="delivery_price" id="delivery-price-input" value="0">
    <input type="hidden" name="total" id="total-input" value="{{ $total }}">

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
let cartItem = @json($cartItem);


// ============================================
// 5. Работа с корзиной: количество и пересчёт
// ============================================
function updateQuantity(delta) {

    cartItem.quantity += delta;

    if (cartItem.quantity < 1) {
        cartItem.quantity = 1;
    }

    document.getElementById('qty').textContent = cartItem.quantity;
    document.getElementById('total').textContent =
        (cartItem.price * cartItem.quantity).toFixed(2) + ' $';

    recalcTotal();
}

function recalcTotal() {

    let total = cartItem.price * cartItem.quantity;

    const selected = document.querySelector(
        'input[name="delivery_template_id"]:checked'
    );

    let shippingPrice = 0;

    if (selected) {
        shippingPrice = parseFloat(selected.dataset.price || 0);
    }

    document.getElementById('shipping-cost').textContent =
        shippingPrice.toFixed(2) + ' $';

    total += shippingPrice;

    document.getElementById('grand-total').textContent =
        total.toFixed(2) + ' $';

    document.getElementById('total-input').value = total;

    const deliveryPriceInput = document.getElementById('delivery-price-input');

    if (deliveryPriceInput) {
        deliveryPriceInput.value = shippingPrice;
    }

    // ================================
    // обновление суммы словами
    // ================================

    const dollars = Math.floor(total);
    const cents = Math.round((total - dollars) * 100);

    let words = numberToWords(dollars) + ' dollars';

    if (cents > 0) {
        words += ' and ' + numberToWords(cents) + ' cents';
    }

    const wordsElement = document.getElementById('total-in-words');

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

window.addEventListener('DOMContentLoaded', recalcTotal);


</script>





@endsection
