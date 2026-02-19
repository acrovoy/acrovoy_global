@extends('dashboard.layout')

@section('dashboard-content')

<a href="{{ route('buyer.orders.show', $order->id) }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
            ← Back to order #{{ $order->id }}
        </a>

                <div class="flex items-center justify-between">
        <div>
<h2 class="text-2xl font-semibold">
    Edit order #{{ $order->id }}
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
                <tr class="order-item"
                    @if($order->type === 'rfq')
                        data-price="{{ $item['price'] ?? 0 }}"
                    @else
                        data-price-tiers='@json($item['priceTiers'])'
                    @endif
                >
                    <td class="px-3 py-2">{{ $item['product_name'] }}</td>

                    <td class="px-3 py-2 text-center">
                        <input type="number"
                            class="quantity border rounded p-1 w-16 text-center"
                            name="items[{{ $index }}][quantity]"
                            value="{{ $item['quantity'] }}"
                            min="1">
                    </td>

                    <td class="px-3 py-2 text-right">
                        <input type="text"
                            class="price border rounded p-1 w-20 text-right bg-gray-100 cursor-not-allowed"
                            name="items[{{ $index }}][price]"
                            value="{{ $item['price'] ?? 0 }}"
                            readonly>
                    </td>

                    <td class="px-3 py-2 text-right font-semibold total"></td>

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
        Сохранить как новый адрес и контакт
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



    <div class="bg-white p-4 rounded-lg shadow mb-6">
    <h3 class="font-semibold mb-4">Адрес доставки</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Страна --}}
        <div>
            <label class="text-sm text-gray-600">Страна</label>
            <select name="country" id="country" class="w-full border rounded p-2">
                <option value="">Выберите страну</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}"
                        {{ $lastAddress && $lastAddress->country == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Регион / область --}}
<div>
    <label class="text-sm text-gray-600">Регион / Область</label>
    <select name="region" id="region" class="w-full border rounded p-2" disabled>
        <option value="">Выберите регион</option>
    </select>
</div>

        {{-- Город --}}
<div>
    <label class="text-sm text-gray-600">Город</label>
    <select name="city" id="city" class="w-full border rounded p-2">
        <option value="">Выберите город</option>
    </select>
    <small class="text-gray-500 block mt-1">
        Если не нашли свой город или локацию, заполните поле ниже
    </small>
    <input type="text" name="city_manual" id="city_manual"
           placeholder="Введите свой город"
           class="w-full border rounded p-2 mt-1">
</div>

        {{-- Улица --}}
        <div class="sm:col-span-2">
            <label class="text-sm text-gray-600">Улица, дом, квартира</label>
            <input type="text" name="street" id="street"
                   value="{{ $lastAddress->street ?? '' }}"
                   class="w-full border rounded p-2">
        </div>

        {{-- Почтовый индекс --}}
        <div>
            <label class="text-sm text-gray-600">Почтовый индекс</label>
            <input type="text" name="postal_code" id="postal_code"
                   value="{{ $lastAddress->postal_code ?? '' }}"
                   class="w-full border rounded p-2">
        </div>
    </div>
</div>





    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold">
        Сохранить изменения
    </button>
</form>


<script>
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
                if (selectedCityId && selectedCityId == loc.id) {
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
function formatCurrency(value) {
    return Number(value).toFixed(2);
}

function recalcRow(row) {
    const quantityInput = row.querySelector('.quantity');
    const priceInput = row.querySelector('.price');
    const totalCell = row.querySelector('.total');

    const quantity = Number(quantityInput.value);

    // Если есть data-price-tiers — используем каскад
    const tiers = row.dataset.priceTiers ? JSON.parse(row.dataset.priceTiers) : null;

    let price = Number(priceInput.value);

    if (tiers) {
        const tier = tiers
            .filter(t => quantity >= t.min_qty && (t.max_qty === null || quantity <= t.max_qty))
            .sort((a,b) => b.min_qty - a.min_qty)[0];
        price = tier ? Number(tier.price) : 0;
        priceInput.value = formatCurrency(price);
    }

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
@endsection
