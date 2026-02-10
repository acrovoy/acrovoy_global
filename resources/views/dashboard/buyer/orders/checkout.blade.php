@extends('dashboard.layout')

@section('dashboard-content')
<h2 class="text-2xl font-bold mb-4">Оформление заказа</h2>

<form method="POST" action="{{ route('buyer.orders.store') }}" id="checkoutForm">
    @csrf

    {{-- Товары --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold mb-2">Ваш заказ</h3>

        <div class="space-y-2">
            @foreach($cartItems as $item)
            <div class="flex justify-between items-center border-b pb-2">
                <div class="flex items-center gap-2">
                    <img src="{{ $item->product?->mainImage
                        ? asset('storage/' . $item->product->mainImage->image_path)
                        : asset('images/no-image.png') }}"
                         class="w-12 h-12 object-contain rounded">

                    <div>
                        <p>{{ $item->product?->name }}</p>

                        <div class="flex items-center gap-1 mt-1">
                            <button type="button"
                                    class="px-2 py-1 border rounded"
                                    onclick="updateQuantity({{ $item->id }}, -1)">−</button>

                            <span id="qty-{{ $item->id }}">{{ $item->quantity }}</span>

                            <button type="button"
                                    class="px-2 py-1 border rounded"
                                    onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                        </div>

                        <p class="text-sm text-gray-500 mt-1">
                            {{ number_format($item->price, 2) }}₴
                        </p>
                    </div>
                </div>

                <div class="font-semibold"
                     id="total-{{ $item->id }}">
                    {{ number_format($item->price * $item->quantity, 2) }}₴
                </div>
            </div>
            @endforeach
        </div>

        {{-- Доставка строка --}}
        <div class="flex justify-between items-center border-b pt-2 mt-2 pb-4 text-gray-700 font-medium">
            <span>Доставка</span>
            <span id="shipping-cost">0.00$</span>
        </div>

        {{-- Итого --}}
        <div class="text-right mt-4 font-bold">
            Итого: <span id="grand-total">{{ number_format($total, 2) }}$</span>
        </div>
    </div>

    {{-- Выбор доставки --}}
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <h3 class="font-semibold mb-2">Доставка</h3>

    <div
        x-data="{ selectedShipping: {{ $shippingOptions->first()->id ?? 0 }} }"
        class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5"
    >
        @foreach($shippingOptions as $template)
        <label
            class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition flex flex-col gap-2"
            :class="{
                'bg-blue-50 border-blue-400 shadow-md':
                selectedShipping == {{ $template->id }}
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
                data-price="{{ $template->price ?? 0 }}"
                {{ $loop->first ? 'checked' : '' }}
            >

            <h4 class="font-semibold text-gray-900">
                {{ $template->title }}
            </h4>

            @if($template->description)
            <p class="text-gray-700 text-sm mt-1">
                {{ $template->description }}
            </p>
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
                        ${{ number_format($template->price, 2) }}
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


    {{-- Селект с сохранёнными адресами --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold mb-4">Выберите сохранённый адрес</h3>

        <select id="saved-addresses" name="saved_address_id" class="w-full border rounded p-2">
            <option value="">-- Выберите адрес --</option>
            @foreach($savedAddresses as $address)
                <option value="{{ $address->id }}"
                        data-first_name="{{ $address->first_name }}"
                        data-last_name="{{ $address->last_name }}"
                        data-country="{{ $address->country }}"
                        data-city="{{ $address->city }}"
                        data-region="{{ $address->region }}"
                        data-street="{{ $address->street }}"
                        data-postal_code="{{ $address->postal_code }}"
                        data-phone="{{ $address->phone }}"
                        {{ $lastAddress && $lastAddress->id === $address->id ? 'selected' : '' }}>
                    {{ $address->first_name }} {{ $address->last_name ?? '' }}, {{ $address->street }}, {{ $address->city }}
                </option>
            @endforeach
        </select>
    </div>


    <input type="hidden" name="address_modified" id="address_modified" value="0">

    <label class="flex items-center gap-2 mt-3 text-sm text-gray-600">
        <input type="checkbox" name="save_as_new" value="1">
        Сохранить как новый адрес
    </label>


    {{-- Контакты и адрес --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold mb-4">Контактные данные и адрес</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Имя --}}
            <div>
                <label class="text-sm text-gray-600">Имя</label>
                <input type="text"
                       name="first_name"
                       id="first_name"
                       value="{{ $lastAddress->first_name ?? auth()->user()->first_name ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Фамилия --}}
            <div>
                <label class="text-sm text-gray-600">Фамилия</label>
                <input type="text"
                       name="last_name"
                       id="last_name"
                       value="{{ $lastAddress->last_name ?? old('last_name') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Страна --}}
            <div>
                <label class="text-sm text-gray-600">Страна</label>
                <input type="text"
                       name="country"
                       id="country"
                       placeholder="UA"
                       value="{{ $lastAddress->country ?? old('country', 'UA') }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Город --}}
            <div>
                <label class="text-sm text-gray-600">Город</label>
                <input type="text"
                       name="city"
                       id="city"
                       value="{{ $lastAddress->city ?? old('city') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Регион --}}
            <div>
                <label class="text-sm text-gray-600">Регион / Область</label>
                <input type="text"
                       name="region"
                       id="region"
                       value="{{ $lastAddress->region ?? old('region') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Почтовый индекс --}}
            <div>
                <label class="text-sm text-gray-600">Почтовый индекс</label>
                <input type="text"
                       name="postal_code"
                       id="postal_code"
                       value="{{ $lastAddress->postal_code ?? old('postal_code') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Улица --}}
            <div class="sm:col-span-2">
                <label class="text-sm text-gray-600">Улица, дом, квартира</label>
                <input type="text"
                       name="street"
                       id="street"
                       value="{{ $lastAddress->street ?? old('street') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Телефон --}}
            <div class="sm:col-span-2">
                <label class="text-sm text-gray-600">Телефон</label>
                <input type="text"
                       name="phone"
                       id="phone"
                       value="{{ $lastAddress->phone ?? old('phone') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>
        </div>
    </div>

    <input type="hidden" name="delivery_price" id="delivery-price-input" value="0">
    <input type="hidden" name="total" id="total-input" value="{{ $total }}">

    <div class="text-right">
        <button type="submit"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold">
            Подтвердить заказ
        </button>
    </div>
</form>

{{-- JS --}}
<script>
let cartItems = @json($cartItems);

// Заполнение формы при выборе адреса
document.getElementById('saved-addresses')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (!selected.value) return;

    document.getElementById('first_name').value = selected.dataset.first_name || '';
    document.getElementById('last_name').value = selected.dataset.last_name || '';
    document.getElementById('country').value = selected.dataset.country || '';
    document.getElementById('city').value = selected.dataset.city || '';
    document.getElementById('region').value = selected.dataset.region || '';
    document.getElementById('street').value = selected.dataset.street || '';
    document.getElementById('postal_code').value = selected.dataset.postal_code || '';
    document.getElementById('phone').value = selected.dataset.phone || '';
});

// Обновление количества
function updateQuantity(itemId, delta) {
    const item = cartItems.find(i => i.id === itemId);
    if (!item) return;

    item.quantity += delta;
    if (item.quantity < 1) item.quantity = 1;

    document.getElementById('qty-' + itemId).textContent = item.quantity;
    document.getElementById('total-' + itemId).textContent =
        (item.price * item.quantity).toFixed(2) + '₴';

    recalcTotal();
}

// Пересчёт суммы
function recalcTotal() {
    let total = cartItems.reduce(
        (sum, i) => sum + i.price * i.quantity,
        0
    );

    const selected = document.querySelector(
        'input[name="delivery_template_id"]:checked'
    );

    let shippingPrice = 0;
    if (selected) {
        shippingPrice = parseFloat(selected.dataset.price || 0);
    }

    document.getElementById('shipping-cost').textContent =
        shippingPrice.toFixed(2) + '₴';

    total += shippingPrice;

    document.getElementById('grand-total').textContent =
        total.toFixed(2) + '₴';

    document.getElementById('total-input').value = total;

    const deliveryPriceInput = document.getElementById('delivery-price-input');
    if (deliveryPriceInput) {
        deliveryPriceInput.value = shippingPrice;
    }
}

window.addEventListener('DOMContentLoaded', recalcTotal);


// Если пользователь меняет любое поле адреса — помечаем как изменённое
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

@endsection
