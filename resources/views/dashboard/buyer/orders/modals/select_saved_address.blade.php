<div id="address-drawer-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50"
     onclick="closeAddressDrawer()"></div>

<div id="address-drawer"
     class="fixed right-0 top-0 h-screen w-[520px]
            bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300
            z-50 flex flex-col overflow-hidden">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    Доставка и контакт
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Выберите сохранённый адрес или заполните новый адрес доставки.
                </p>
            </div>

            <button type="button"
                    onclick="closeAddressDrawer()"
                    class="text-gray-400 hover:text-gray-700 transition">
                ✕
            </button>
        </div>
    </div>

    <form method="POST"
      action="{{ route('buyer.rfqs.attach.address', $rfq) }}"
      id="address-form"
      class="flex flex-col flex-1 min-h-0">

        @csrf

        <div class="flex-1 min-h-0 overflow-y-auto px-6 py-5 space-y-5">

            {{-- Селект с сохранёнными адресами --}}
            <div class="border border-gray-200 rounded-xl p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-800 mb-5">
                    Выберите сохранённый адрес
                </h3>

                <select id="saved-addresses"
                        name="saved_address_id"
                        class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
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

            {{-- Контакты --}}
            <div class="border border-gray-200 rounded-xl p-5">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-800 mb-5">
                    Контактные данные
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Имя
                        </label>

                        <input type="text"
                               name="first_name"
                               id="first_name"
                               value="{{ $currentSavedAddress->first_name ?? auth()->user()->first_name ?? '' }}"
                               class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Фамилия
                        </label>

                        <input type="text"
                               name="last_name"
                               id="last_name"
                               value="{{ $currentSavedAddress->last_name ?? old('last_name') ?? '' }}"
                               class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Телефон
                        </label>

                        <input type="text"
                               name="phone"
                               id="phone"
                               value="{{ $currentSavedAddress->phone ?? old('phone') ?? '' }}"
                               class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                </div>
            </div>

            {{-- Адрес --}}
            <div class="border border-gray-200 rounded-xl p-5">

                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-800 mb-5">
                    Адрес доставки
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Страна
                        </label>

                        <select name="country"
                                id="country"
                                class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            <option value="">Выберите страну</option>

                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ $currentSavedAddress && $currentSavedAddress->country == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Регион / Область
                        </label>

                        <select name="region"
                                id="region"
                                disabled
                                class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            <option value="">Выберите регион</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Город
                        </label>

                        <select name="city"
                                id="city"
                                class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            <option value="">Выберите город</option>
                        </select>

                        <small class="text-gray-500 block mt-2">
                            Если не нашли свой город или локацию, заполните поле ниже
                        </small>

                        <input type="text"
                               name="city_manual"
                               id="city_manual"
                               placeholder="Введите свой город"
                               class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Улица, дом, квартира
                        </label>

                        <input type="text"
                               name="street"
                               id="street"
                               value="{{ $currentSavedAddress->street ?? '' }}"
                               class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                    <div>
                        <label class="text-xs text-gray-500 uppercase tracking-wide">
                            Почтовый индекс
                        </label>

                        <input type="text"
                               name="postal_code"
                               id="postal_code"
                               value="{{ $currentSavedAddress->postal_code ?? '' }}"
                               class="w-full mt-2 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                    </div>

                </div>
            </div>

            <input type="hidden" name="saved_address_id" id="saved_address_id">

        </div>

        {{-- FOOTER --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between gap-2">

            <button type="button"
                    onclick="closeAddressDrawer()"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                Cancel
            </button>

            <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white hover:bg-gray-800 transition shadow-sm">
                Сохранить адрес
            </button>

        </div>

    </form>

</div>


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