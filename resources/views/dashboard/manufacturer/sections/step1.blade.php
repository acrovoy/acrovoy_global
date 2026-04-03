<h3 class="text-2xl font-bold mb-6">Basic Information</h3>

{{-- Product Name --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">
    <h4 class="font-semibold mb-3 flex items-center gap-2">
        Product Name
        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">Product Name</div>
                <div class="text-gray-200 text-sm">
                    Укажите понятное и читаемое название товара, которое будет
                    отображаться покупателям в каталоге и поиске.
                </div>
                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>используйте общепринятые слова</li>
                    <li>не добавляйте внутренние коды поставщика</li>
                    <li>избегайте лишних символов и сокращений</li>
                </ul>
                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Пример: <span class="text-gray-200">Wireless Bluetooth Headphones</span>
                </div>
            </div>
        </x-help-tooltip>
    </h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
        @php
            $flagPath = "/images/flags/svg/".strtolower($language->code).".svg";
        @endphp
        @if($index == 0)
        {{-- Первый язык всегда виден --}}
        <div class="flex items-center gap-2 mb-2 flex-1">
            <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
            <input type="text"
                name="name[{{ $language->code }}]"
                class="input w-full"
                placeholder="Product Name ({{ $language->code }})"
                value="{{ old('name.' . $language->code, $translations[$language->code]['name'] ?? '') }}">
        </div>
        @else
        {{-- Остальные языки скрыты по умолчанию --}}
        <div x-show="open" class="flex items-center gap-2 mb-2 transition-all duration-300 ease-in-out flex-1">
            <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
            <input type="text"
                name="name[{{ $language->code }}]"
                class="input w-full"
                placeholder="Product Name ({{ $language->code }})"
                value="{{ old('name.' . $language->code, $translations[$language->code]['name'] ?? '') }}">
        </div>
        @endif
        @endforeach
    </div>

    {{-- Кнопка для остальных языков --}}
    @if(count($languages) > 1)
    <button type="button"
        @click="open = !open"
        class="mt-2 text-sm text-blue-600 hover:underline flex items-center gap-1">
        Other Languages
        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    @endif
</div>

{{-- Short Description --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">
    <h4 class="font-semibold mb-3">Short Description</h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
        @php
            $flagPath = "/images/flags/svg/".strtolower($language->code).".svg";
        @endphp
        @if($index == 0)
        {{-- Первый язык всегда виден --}}
        <div class="flex items-center gap-2 mb-2 flex-1">
            <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
            <input type="text"
                name="undername[{{ $language->code }}]"
                class="input flex-1"
                placeholder="Undername ({{ $language->code }})"
                value="{{ old('undername.' . $language->code) }}">
        </div>
        @else
        {{-- Остальные языки скрыты по умолчанию --}}
        <div x-show="open" class="flex items-center gap-2 mb-2 transition-all duration-300 ease-in-out flex-1">
            <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
            <input type="text"
                name="undername[{{ $language->code }}]"
                class="input flex-1"
                placeholder="Undername ({{ $language->code }})"
                value="{{ old('undername.' . $language->code) }}">
        </div>
        @endif
        @endforeach
    </div>

    {{-- Кнопка для остальных языков --}}
    @if(count($languages) > 1)
    <button type="button"
        @click="open = !open"
        class="mt-2 text-sm text-blue-600 hover:underline flex items-center gap-1">
        Other Languages
        <svg :class="{'rotate-180': open}" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    @endif
</div>

{{-- Full Product Description --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">
    <h4 class="font-semibold mb-3">Full Product Description</h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
        @php
            $flagPath = "/images/flags/svg/".strtolower($language->code).".svg";
        @endphp
        @if($index == 0)
        {{-- Первый язык всегда виден --}}
        <div class="flex items-start gap-2 mb-2 flex-1">
            <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded mt-1">
            <textarea name="description[{{ $language->code }}]"
                class="input mb-2 flex-1"
                rows="4"
                placeholder="Full Description ({{ $language->code }})">{{ old('description.' . $language->code) }}</textarea>
        </div>
        @else
        {{-- Остальные языки скрыты по умолчанию --}}
        <div x-show="open" class="flex items-start gap-2 mb-2 transition-all duration-300 ease-in-out flex-1">
            <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-6 h-6 rounded mt-1">
            <textarea name="description[{{ $language->code }}]"
                class="input mb-2 flex-1"
                rows="4"
                placeholder="Full Description ({{ $language->code }})">{{ old('description.' . $language->code) }}</textarea>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Кнопка для остальных языков --}}
    @if(count($languages) > 1)
    <button type="button"
        @click="open = !open"
        class="mt-2 text-sm text-blue-600 hover:underline flex items-center gap-1">
        Other Languages
        <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    @endif
</div>

{{-- SKU --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm">
    <h4 class="font-semibold mb-3 flex items-center gap-2">
        SKU
        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">SKU</div>
                <div class="text-gray-200 text-sm">
                    Уникальный артикул товара, который будет использоваться для идентификации в системе и поиске.
                </div>
                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>Используйте только буквы, цифры и дефисы</li>
                    <li>Не используйте пробелы и специальные символы</li>
                    <li>Каждый SKU должен быть уникальным</li>
                </ul>
                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Пример: <span class="text-gray-200">BT-HEAD-001</span>
                </div>
            </div>
        </x-help-tooltip>
    </h4>

    <input type="text"
        name="sku"
        class="input w-full"
        placeholder="Enter SKU"
        value="{{ old('sku') }}">
</div>