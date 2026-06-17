<div id="address-drawer-overlay" class="fixed inset-0 bg-black/40 hidden z-50"></div>

<div id="address-drawer"
     class="fixed right-0 top-0 h-full w-[520px] bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto p-6">

    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Доставка и контакт</h3>

        <button type="button" onclick="closeAddressDrawer()"
                class="text-gray-400 hover:text-gray-700">
            ✕
        </button>
    </div>

   
    <div>


    <form method="POST" action="{{ route('buyer.rfqs.attach.address', $rfq) }}" id="address-form">
    @csrf


{{-- Селект с сохранёнными адресами --}}
    <div class="bg-white p-4 rounded-lg mb-6 border border-gray-200">
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
                        {{ $currentAddressId == $address->id ? 'selected' : '' }}>
                    {{ $address->first_name }} {{ $address->last_name ?? '' }}, {{ $address->street }}, {{ $address->city }}
                </option>
            @endforeach
        </select>
    </div>


    <input type="hidden" name="address_modified" id="address_modified" value="0">

    


    {{-- Контакты и адрес --}}
    <div class="bg-white p-4 rounded-lg mb-6 border border-gray-200">
        <h3 class="font-semibold mb-4">Контактные данные</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Имя --}}
            <div>
                <label class="text-sm text-gray-600">Имя</label>
                <input type="text"
                       name="first_name"
                       id="first_name"
                       value="{{ $currentSavedAddress->first_name ?? auth()->user()->first_name ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Фамилия --}}
            <div>
                <label class="text-sm text-gray-600">Фамилия</label>
                <input type="text"
                       name="last_name"
                       id="last_name"
                       value="{{ $currentSavedAddress->last_name ?? old('last_name') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

            {{-- Телефон --}}
            <div class="sm:col-span-2">
                <label class="text-sm text-gray-600">Телефон</label>
                <input type="text"
                       name="phone"
                       id="phone"
                       value="{{ $currentSavedAddress->phone ?? old('phone') ?? '' }}"
                       class="w-full border rounded p-2">
            </div>

          
            
        </div>
    </div>



    <div class="bg-white p-4 rounded-lg mb-6 border border-gray-200">
    <h3 class="font-semibold mb-4">Адрес доставки</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Страна --}}
        <div>
            <label class="text-sm text-gray-600">Страна</label>
            <select name="country" id="country" class="w-full border rounded p-2">
                <option value="">Выберите страну</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}"
                        {{ $currentSavedAddress && $currentSavedAddress->country == $country->id ? 'selected' : '' }}>
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
                   value="{{ $currentSavedAddress->street ?? '' }}"
                   class="w-full border rounded p-2">
        </div>

        {{-- Почтовый индекс --}}
        <div>
            <label class="text-sm text-gray-600">Почтовый индекс</label>
            <input type="text" name="postal_code" id="postal_code"
                   value="{{ $currentSavedAddress->postal_code ?? '' }}"
                   class="w-full border rounded p-2">
        </div>
    </div>
</div>
<input type="hidden" name="saved_address_id" id="saved_address_id">


<button type="submit"
        class="w-full mt-4 px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
    Сохранить адрес
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

document.getElementById('saved-addresses')?.addEventListener('change', function () {
    

    document.getElementById('saved_address_id').value = this.value;

    
});

document.addEventListener('DOMContentLoaded', function() {
    // Если есть сохранённый адрес с выбранной страной и регионом — подгружаем их
    @if($currentSavedAddress)
    @if($currentSavedAddress->country)
        fetchRegions({{ $currentSavedAddress->country }}, {{ $currentSavedAddress->region }}, function () {
            if ({{ $currentSavedAddress->region }}) {
                fetchLocations({{ $currentSavedAddress->region }}, "{{ $currentSavedAddress->city }}");
            }
        });

        regionSelect.disabled = false;
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
    regionSelect.disabled = false;

    fetchRegions(
        selected.dataset.country,
        selected.dataset.region,
        function () {
            // ВАЖНО: города грузим только после регионов

            // Подгрузка городов
            if (selected.dataset.region) {
                fetchLocations(selected.dataset.region, selected.dataset.city);
            }
        }
    );

} else {
    regionSelect.disabled = true;
    regionSelect.innerHTML = '<option value="">Выберите регион</option>';

    cityInput.innerHTML = '<option value="">Выберите город</option>';
    cityInput.disabled = true;
}

    // 👉 Заполняем ручное поле если город есть
    // if (selected.dataset.city) {
    //     cityManualInput.value = selected.dataset.city;
    // }
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

    markAddressChanged();

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

    markAddressChanged();

    fetchLocations(regionId);
});


// ============================================
// Подгрузка локаций (города)
// ============================================
function fetchLocations(regionId, selectedCityName = null) {
    if (!cityInput) return;

    cityInput.innerHTML = '<option value="">Выберите город</option>';
    cityInput.disabled = true;

    fetch(`${locationsUrl}?region_id=${regionId}`)
        .then(res => res.json())
        .then(data => {

            let found = false;

            data.forEach(loc => {
                const option = document.createElement('option');

                option.value = loc.id;        // сохраняем ID (нормально)
                option.textContent = loc.name;

                // 🔥 СРАВНИВАЕМ ПО NAME
                if (
                    selectedCityName &&
                    String(selectedCityName).trim().toLowerCase() ===
                    String(loc.name).trim().toLowerCase()
                ) {
                    option.selected = true;
                    found = true;
                }

                cityInput.appendChild(option);
            });

            cityInput.disabled = false;

            // fallback
            if (!found && data.length > 0) {
                cityInput.value = data[0].id;
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

    markAddressChanged();
});


cityManualInput?.addEventListener('input', function() {
    if (this.value.trim() !== '') {
        cityInput.value = '';
    }

    markAddressChanged();
});

// ============================================
// 4. Подгрузка регионов
// ============================================
function fetchRegions(countryId, selectedRegionId = null, callback = null) {
    if (!regionSelect) return;

    regionSelect.innerHTML = '<option value="">Выберите регион</option>';

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

            // 👉 ВАЖНО: сигнал что регионы загружены
            if (callback) callback();
        })
        .catch(console.error);
}


// ============================================
// 6. Отметка изменения адреса
// ============================================

document.addEventListener('input', function(e) {

    const fields = [
        'first_name',
        'last_name',
        'street',
        'postal_code',
        'phone',
        'city_manual'
    ];

    if (fields.includes(e.target.id)) {
       markAddressChanged();
    }
});

function markAddressChanged() {
    const hidden = document.getElementById('address_modified');

    if (hidden) {
        hidden.value = '1';
        console.log('address_modified = 1');
    }
}

</script>