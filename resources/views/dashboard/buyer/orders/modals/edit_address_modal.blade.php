<div
    x-show="editAddressModalOpen"
    x-cloak
    class="fixed inset-0 z-50 overflow-hidden">
    {{-- Backdrop --}}
    <div
        x-show="editAddressModalOpen"
        x-transition.opacity
        class="absolute inset-0 bg-black/50"
        @click="editAddressModalOpen = false"></div>

    {{-- Drawer --}}
    <div
        x-show="editAddressModalOpen"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white shadow-xl overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 border-b px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold">
                Редактировать адрес
            </h2>

            <button
                type="button"
                @click="editAddressModalOpen = false"
                class="text-gray-500 hover:text-gray-700 text-xl">
                ✕
            </button>
        </div>

        <div class="p-6">

            <form method="POST" action="{{ route('buyer.orders.update-address', $order->id) }}">
                @csrf
                @method('PUT')

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


                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="editAddressModalOpen = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
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
async function fetchLocations(regionId, selectedCityId = null) {

    if (!cityInput) return;

    cityInput.innerHTML = '<option value="">Выберите город</option>';
    cityInput.disabled = true;

    try {

        const res = await fetch(`${locationsUrl}?region_id=${regionId}`);
        const data = await res.json();

        let cityFound = false;

        data.forEach(loc => {

            const option = document.createElement('option');

            option.value = loc.id;
            option.textContent = loc.name;
            option.dataset.name = loc.name;

            if (selectedCityId && selectedCityId == loc.id) {
                option.selected = true;
                cityFound = true;
            }

            cityInput.appendChild(option);
        });

        cityInput.disabled = false;

        if (selectedCityId && !cityFound) {
            cityManualInput.value = selectedCityId;
        }

    } catch (e) {
        console.error(e);
    }
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
async function fetchRegions(countryId, selectedRegionId = null) {
    if (!regionSelect) return;

    regionSelect.innerHTML = '<option value="">Выберите регион</option>';

    if (!countryId) return;

    try {

        const res = await fetch(`${regionsUrl}?country_id=${countryId}`);
        const data = await res.json();

        data.forEach(r => {

            const option = document.createElement('option');
            option.value = r.id;
            option.textContent = r.name;

            if (selectedRegionId && selectedRegionId == r.id) {
                option.selected = true;
            }

            regionSelect.appendChild(option);
        });

    } catch (e) {
        console.error(e);
    }
}


document.addEventListener('DOMContentLoaded', async () => {

    const selectedCountry = countrySelect?.value;
    const selectedRegion = '{{ $lastAddress->region ?? '' }}';
    const selectedCity = '{{ $lastAddress->city ?? '' }}';

    if (selectedCountry) {

        await fetchRegions(selectedCountry, selectedRegion);

        regionSelect.disabled = false;

        if (selectedRegion) {
            await fetchLocations(selectedRegion, selectedCity);
        }
    }
});



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
