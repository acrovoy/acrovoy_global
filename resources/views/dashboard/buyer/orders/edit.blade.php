@extends('dashboard.layout')

@section('dashboard-content')


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


<h2 class="text-2xl font-bold mb-4">Редактирование заказа #{{ $order->id }}</h2>

<form method="POST" action="{{ route('buyer.orders.update', $order->id) }}">
    @csrf
    @method('PUT')



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
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="px-3 py-2">
                        <input type="text" name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}" class="border rounded p-1 w-full">
                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" class="border rounded p-1 w-16 text-center">
                    </td>
                    <td class="px-3 py-2 text-right">
                        <input type="text" name="items[{{ $index }}][price]" value="{{ number_format($item->price, 2) }}" class="border rounded p-1 w-20 text-right">
                    </td>
                    <td class="px-3 py-2 text-right font-semibold">
                        {{ number_format($item->quantity * $item->price, 2) }}₴
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2 font-bold">Итого: {{ number_format($order->items->sum(fn($i) => $i->quantity * $i->price), 2) }}₴</div>
    </div>

    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold">
        Сохранить изменения
    </button>
</form>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('saved-addresses');

    select.addEventListener('change', function () {
        const option = this.selectedOptions[0];
        if (!option) return;

        document.querySelector('input[name="first_name"]').value = option.dataset.first_name || '';
        document.querySelector('input[name="last_name"]').value = option.dataset.last_name || '';
        document.querySelector('input[name="country"]').value = option.dataset.country || '';
        document.querySelector('input[name="city"]').value = option.dataset.city || '';
        document.querySelector('input[name="region"]').value = option.dataset.region || '';
        document.querySelector('input[name="street"]').value = option.dataset.street || '';
        document.querySelector('input[name="postal_code"]').value = option.dataset.postal_code || '';
        document.querySelector('input[name="phone"]').value = option.dataset.phone || '';
    });

    // Автозаполнение при загрузке страницы, если выбран последний адрес
    if (select.selectedOptions.length > 0) {
        select.dispatchEvent(new Event('change'));
    }
});
</script>
