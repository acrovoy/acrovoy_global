@extends('dashboard.layout')

@section('dashboard-content')

<a href=""
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to order #{{ $order->id }}
        </a>

                <div class="flex items-center justify-between">
        <div>
<h2 class="text-2xl font-semibold">
    Редактирование заказа #{{ $order->id }}
</h2>
<p class="text-sm text-gray-500 mb-6">
                    Manage exchange rates relative to the base currency (USD)
                </p>

                </div>
        <span class="px-3 py-1 rounded text-sm
            @if($order['status'] === 'pending') bg-yellow-100 text-yellow-800
            @elseif($order['status'] === 'confirmed') bg-green-100 text-blue-800
            @elseif($order['status'] === 'paid') bg-blue-100 text-blue-800
            @elseif($order['status'] === 'shipped') bg-green-100 text-green-800
            @else bg-gray-100 text-gray-800
            @endif
        ">
            {{ ucfirst($order['status']) }}
        </span>
    </div>



{{-- Flash messages --}}
@if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('buyer.orders.update', $order->id) }}">
    @csrf
    @method('PUT')



    {{-- Список товаров --}}
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <h3 class="font-semibold mb-2">Товары в заказе</h3>

    <table class="w-full text-sm border border-gray-200 rounded">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">Продукт</th>
                <th class="px-3 py-2 text-center">Кол-во</th>
                <th class="px-3 py-2 text-right">Цена</th>
                <th class="px-3 py-2 text-right">Сумма</th>
            </tr>
        </thead>
        <tbody>
    @foreach($orderItems as $index => $item)
    <tr class="order-item" data-price-tiers='@json($item['priceTiers'])'>
        <td class="px-3 py-2">{{ $item['product_name'] }}</td>
        <td class="px-3 py-2 text-center">
            <input type="number" 
                   class="quantity border rounded p-1 w-16 text-center" 
                   name="items[{{ $index }}][quantity]" 
                   value="{{ $item['quantity'] }}" min="1">
        </td>
        <td class="px-3 py-2 text-right">
            <input type="text" 
                   class="price border rounded p-1 w-20 text-right bg-gray-100 cursor-not-allowed" 
                   name="items[{{ $index }}][price]" 
                   value="{{ $item['price'] }}" readonly>
        </td>
        <td class="px-3 py-2 text-right font-semibold total"></td>

        {{-- Скрытое поле id товара --}}
        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] }}">
        <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item['product_name'] }}">
    </tr>
    @endforeach
</tbody>
    </table>

    <div class="mt-2 font-bold text-right">
        Итого: <span id="grand-total"></span>$
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
                data-phone="{{ $address->phone }}">
            {{ $address->first_name }} {{ $address->last_name ?? '' }}, {{ $address->street }}, {{ $address->city }}
        </option>
    @endforeach
</select>
    </div>



    {{-- Контактные данные --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h3 class="font-semibold mb-2">Контактная информация</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <input type="text" name="first_name" value="{{ old('first_name', $order->first_name) }}" placeholder="Имя" class="border rounded p-2 w-full">
            <input type="text" name="last_name" value="{{ old('last_name', $order->last_name) }}" placeholder="Фамилия" class="border rounded p-2 w-full">
            <input type="text" name="country" value="{{ old('country', $order->country) }}" placeholder="Страна" class="border rounded p-2 w-full">
            <input type="text" name="city" value="{{ old('city', $order->city) }}" placeholder="Город" class="border rounded p-2 w-full">
            <input type="text" name="region" value="{{ old('region', $order->region) }}" placeholder="Регион" class="border rounded p-2 w-full">
            <input type="text" name="street" value="{{ old('street', $order->street) }}" placeholder="Улица" class="border rounded p-2 w-full">
            <input type="text" name="postal_code" value="{{ old('postal_code', $order->postal_code) }}" placeholder="Почтовый индекс" class="border rounded p-2 w-full">
            <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" placeholder="Телефон" class="border rounded p-2 w-full">
        </div>
    </div>





    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold">
        Сохранить изменения
    </button>
</form>
@endsection

<script>
function formatCurrency(value) {
    return Number(value).toFixed(2);
}

function recalcRow(row) {
    const quantityInput = row.querySelector('.quantity');
    const priceInput = row.querySelector('.price');
    const totalCell = row.querySelector('.total');
    const priceTiers = JSON.parse(row.dataset.priceTiers);

    const quantity = Number(quantityInput.value);

    // Берём последнюю подходящую ступень (min_qty DESC)
    const tier = priceTiers
        .filter(t => quantity >= t.min_qty && (t.max_qty === null || quantity <= t.max_qty))
        .sort((a,b) => b.min_qty - a.min_qty)[0];

    const price = tier ? Number(tier.price) : 0;
    priceInput.value = formatCurrency(price);

    const total = quantity * price;
    totalCell.textContent = formatCurrency(total) + '$';
    return total;
}

function recalcGrandTotal() {
    const rows = document.querySelectorAll('.order-item');
    let grandTotal = 0;
    rows.forEach(row => {
        grandTotal += recalcRow(row);
    });
    document.getElementById('grand-total').textContent = formatCurrency(grandTotal);
}

document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.order-item');
    rows.forEach(row => {
        const input = row.querySelector('.quantity');
        input.addEventListener('input', recalcGrandTotal);
    });
    recalcGrandTotal();
});
</script>






<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('saved-addresses');

    // 1️⃣ Сохраняем адрес из заказа
    const orderAddress = {
        first_name: "{{ $order->first_name }}",
        last_name: "{{ $order->last_name }}",
        country: "{{ $order->country }}",
        city: "{{ $order->city }}",
        region: "{{ $order->region }}",
        street: "{{ $order->street }}",
        postal_code: "{{ $order->postal_code }}",
        phone: "{{ $order->phone }}"
    };

    // Заполняем поля **адресом из заказа**
    function fillFields(addr) {
        document.querySelector('input[name="first_name"]').value = addr.first_name || '';
        document.querySelector('input[name="last_name"]').value = addr.last_name || '';
        document.querySelector('input[name="country"]').value = addr.country || '';
        document.querySelector('input[name="city"]').value = addr.city || '';
        document.querySelector('input[name="region"]').value = addr.region || '';
        document.querySelector('input[name="street"]').value = addr.street || '';
        document.querySelector('input[name="postal_code"]').value = addr.postal_code || '';
        document.querySelector('input[name="phone"]').value = addr.phone || '';
    }

    fillFields(orderAddress); // ✅ при загрузке формы

    // 2️⃣ Обработчик выбора сохранённого адреса
    select.addEventListener('change', function () {
        const option = this.selectedOptions[0];
        if (!option || !option.value) {
            // Если выбран "-- Выберите адрес --", вернуть адрес из заказа
            fillFields(orderAddress);
            return;
        }

        const savedAddress = {
            first_name: option.dataset.first_name,
            last_name: option.dataset.last_name,
            country: option.dataset.country,
            city: option.dataset.city,
            region: option.dataset.region,
            street: option.dataset.street,
            postal_code: option.dataset.postal_code,
            phone: option.dataset.phone
        };

        fillFields(savedAddress);
    });
});

</script>

