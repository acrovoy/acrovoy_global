<h3 class="text-2xl font-bold mb-6">Basic Information</h3>

{{-- Product Name --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">
    <h4 class="font-semibold mb-3 flex items-center gap-2">
        Product Name
        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">Название товара</div>
                <div class="text-gray-200 text-sm">
                    Укажите понятное и читаемое название товара, которое будет
                    отображаться покупателям в каталоге и поиске.
                </div>
                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>используйте общепринятые термины (стул, стол, барная табуретка)</li>
                    <li>не добавляйте внутренние коды поставщика</li>
                    <li>избегайте лишних символов и сокращений</li>
                    <li>максимальное количество символов: <span class="font-semibold text-white">110</span></li>
                </ul>
                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Пример: <span class="text-gray-200">Барный стул из дуба, 75 см</span> или <span class="text-gray-200">Стул для столовой, металлический каркас, черный</span>
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
            <x-char-counter :max="110">
                <div x-data="charCounter(110)" class="relative w-full">
                    <input type="text"
                        name="name[{{ $language->code }}]"
                        class="input w-full"
                        maxlength="110"
                        placeholder="Dining chair, metal frame, black (required)"
                        x-model="value"
                        @input="update($event.target)"
                        x-init="update($el)"
                        required
                        style="padding-right: 4rem;" {{-- место для счётчика --}}
                        value="{{ old('name.' . $language->code, $translations[$language->code]['name'] ?? '') }}">

                    <!-- Счётчик поверх input -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                        :class="color">
                        <span x-text="count"></span>/<span x-text="max"></span>
                    </div>
                </div>
            </x-char-counter>
        </div>
        @else
        {{-- Остальные языки скрыты по умолчанию --}}
        <div x-show="open" class="flex items-center gap-2 mb-2 transition-all duration-300 ease-in-out flex-1">
            <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
            <x-char-counter :max="110">
                <div x-data="charCounter(110)" class="relative w-full">
                    <input type="text"
                        name="name[{{ $language->code }}]"
                        class="input w-full"
                        maxlength="110"
                        placeholder="Product Name (optional)"
                        x-model="value"
                        @input="update($event.target)"
                        x-init="update($el)"
                        style="padding-right: 4rem;" {{-- место для счётчика --}}
                        value="{{ old('name.' . $language->code, $translations[$language->code]['name'] ?? '') }}">



                    <!-- Счётчик символов внутри input -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                        :class="color">
                        <span x-text="count"></span>/<span x-text="max"></span>
                    </div>
                </div>
            </x-char-counter>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Кнопка для остальных языков --}}
    @if(count($languages) > 1)
    <div class="flex justify-between">
        <div class="mt-1 text-xs text-red-500 italic">
            * English version required
        </div>
        <button type="button"
            @click="open = !open"
            class="mt-1 text-xs text-blue-600 hover:underline flex items-center gap-1">
            Other Languages
            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>
    @endif
</div>

{{-- Short Description --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">

    <h4 class="font-semibold mb-3 flex items-center gap-2">
        Short Description

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">
                <div class="font-semibold text-white">Short Description</div>
                <div class="text-gray-200 text-sm">
                    Add a short product highlight that appears below the product name.
                    Describe the main advantage, material, or usage.
                </div>
                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>write one key benefit of the product</li>
                    <li>you may specify material or usage</li>
                    <li>avoid repeating the product name</li>
                    <li>maximum length:
                        <span class="font-semibold text-white">60 characters</span>
                    </li>
                </ul>
                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Example:
                    <span class="text-gray-200">Stackable restaurant chair</span>,
                    <span class="text-gray-200">Weather-resistant outdoor armchair</span>,
                    <span class="text-gray-200">Commercial grade metal frame</span>
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

        {{-- First language visible --}}
        <div class="flex items-center gap-2 mb-2 flex-1">

            <img src="{{ $flagPath }}"
                alt="{{ $language->code }}"
                class="w-5 h-5 rounded">


            <x-char-counter :max="60">

                <div x-data="charCounter(60)" class="relative w-full">

                    <input type="text"
                        name="undername[{{ $language->code }}]"
                        class="input w-full"
                        maxlength="60"
                        placeholder="Commercial grade metal frame"
                        x-model="value"
                        @input="update($event.target)"
                        x-init="update($el)"
                        style="padding-right: 4rem;"
                        value="{{ old('undername.' . $language->code) }}">


                    {{-- counter inside input --}}
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                        :class="color">

                        <span x-text="count"></span>/<span x-text="max"></span>

                    </div>

                </div>

            </x-char-counter>

        </div>


        @else

        {{-- Other languages hidden --}}
        <div x-show="open"
            class="flex items-center gap-2 mb-2 transition-all duration-300 ease-in-out flex-1">

            <img src="{{ $flagPath }}"
                alt="{{ $language->code }}"
                class="w-5 h-5 rounded">


            <x-char-counter :max="60">

                <div x-data="charCounter(60)" class="relative w-full">

                    <input type="text"
                        name="undername[{{ $language->code }}]"
                        class="input w-full"
                        maxlength="60"
                        placeholder="Undername"
                        x-model="value"
                        @input="update($event.target)"
                        x-init="update($el)"
                        style="padding-right: 4rem;"
                        value="{{ old('undername.' . $language->code) }}">


                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                        :class="color">

                        <span x-text="count"></span>/<span x-text="max"></span>

                    </div>

                </div>

            </x-char-counter>

        </div>

        @endif

        @endforeach

    </div>


    {{-- Footer --}}
    @if(count($languages) > 1)

    <div class="flex justify-between mt-1">

        <div class="text-xs text-gray-500 italic">
            * Optional, maximum 60 characters
        </div>


        <button type="button"
            @click="open = !open"
            class="mt-2 text-xs text-blue-600 hover:underline flex items-center gap-1">

            Other Languages

            <svg :class="{'rotate-180': open}"
                class="w-4 h-4 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7" />

            </svg>

        </button>

    </div>

    @endif

</div>



{{-- Full Product Description --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">

    <h4 class="font-semibold mb-3 flex items-center gap-2">

        Full Product Description

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">

                <div class="font-semibold text-white">
                    Full Product Description
                </div>

                <div class="text-gray-200 text-sm">
                    Provide a detailed description of the product.
                    Explain materials, construction, usage, and key benefits.
                </div>

                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>describe materials and build quality</li>
                    <li>mention dimensions or compatibility if relevant</li>
                    <li>explain where the product can be used</li>
                    <li>highlight durability or commercial suitability</li>
                    <li>avoid supplier codes or duplicated title text</li>
                    <li>
                        maximum length:
                        <span class="font-semibold text-white">
                            2000 characters
                        </span>
                    </li>
                </ul>

                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Example:
                    <span class="text-gray-200">
                        This commercial-grade dining chair features a reinforced steel frame,
                        powder-coated finish, and ergonomic seat designed for intensive use
                        in restaurants, cafés, and hospitality interiors.
                    </span>
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

        {{-- First language required --}}
        <div class="flex items-start gap-2 mb-2 flex-1">

            <img src="{{ $flagPath }}"
                class="w-5 h-5 rounded mt-1">


            <x-char-counter :max="2000">

                <div x-data="charCounter(2000)" class="relative w-full">

                    <textarea
                        name="description[{{ $language->code }}]"
                        class="input w-full"
                        rows="6"
                        maxlength="2000"
                        required
                        placeholder="Full description (required)"
                        x-model="value"
                        @input="update($event.target)"
                        x-init="update($el)"
                        style="padding-right: 4rem;">{{ old('description.' . $language->code) }}</textarea>


                    <div
                        class="absolute bottom-2 right-3 text-xs pointer-events-none"
                        :class="color">

                        <span x-text="count"></span>/<span x-text="max"></span>

                    </div>

                </div>

            </x-char-counter>

        </div>


        @else

        {{-- Other languages optional --}}
        <div x-show="open"
            class="flex items-start gap-2 mb-2 transition-all duration-300 ease-in-out flex-1">

            <img src="{{ $flagPath }}"
                class="w-5 h-5 rounded mt-1">


            <x-char-counter :max="2000">

                <div x-data="charCounter(2000)" class="relative w-full">

                    <textarea
                        name="description[{{ $language->code }}]"
                        class="input w-full"
                        rows="6"
                        maxlength="2000"
                        placeholder="Full description (optional)"
                        x-model="value"
                        @input="update($event.target)"
                        x-init="update($el)"
                        style="padding-right: 4rem;">{{ old('description.' . $language->code) }}</textarea>


                    <div
                        class="absolute bottom-2 right-3 text-xs pointer-events-none"
                        :class="color">

                        <span x-text="count"></span>/<span x-text="max"></span>

                    </div>

                </div>

            </x-char-counter>

        </div>

        @endif

        @endforeach

    </div>


    @if(count($languages) > 1)

    <div class="flex justify-between mt-1">

        <div class="text-xs text-red-500 italic">
            * English version required
        </div>


        <button type="button"
            @click="open = !open"
            class="mt-2 text-xs text-blue-600 hover:underline flex items-center gap-1">

            Other Languages

            <svg :class="{'rotate-180': open}"
                class="w-4 h-4 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">

                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7" />

            </svg>

        </button>

    </div>

    @endif

</div>



{{-- SKU --}}
<div class="border rounded p-4 mb-4 bg-white shadow-sm">

    <h4 class="font-semibold mb-3 flex items-center gap-2">

        SKU

        <x-help-tooltip width="w-80">
            <div class="space-y-2 leading-relaxed">

                <div class="font-semibold text-white">
                    SKU (Stock Keeping Unit)
                </div>

                <div class="text-gray-200 text-sm">
                    Unique product identifier used for inventory tracking,
                    integrations, and internal product management.
                </div>

                <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
                    <li>use only letters, numbers, and hyphens</li>
                    <li>do not use spaces or special characters</li>
                    <li>each SKU must be unique</li>
                    <li>maximum length: <span class="text-white font-semibold">64 characters</span></li>
                </ul>

                <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
                    Example:
                    <span class="text-gray-200">
                        CHR-OAK-75-BLK
                    </span>
                </div>

            </div>
        </x-help-tooltip>

    </h4>


    <x-char-counter :max="64">

        <div x-data="charCounter(64)" class="relative w-full">

            <input
                type="text"
                name="sku"
                class="input w-full"
                maxlength="64"
                required
                placeholder="Enter SKU"
                x-model="value"
                @input="update($event.target)"
                x-init="update($el)"
                style="padding-right: 4rem;"
                value="{{ old('sku') }}">




            <div
                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs"
                :class="color">

                <span x-text="count"></span>/<span x-text="max"></span>

            </div>

        </div>

    </x-char-counter>

    <div class="flex justify-between">
        <div class="text-xs text-red-500 italic mt-1">
            * Required and must be unique
        </div>
        <div class="text-xs text-gray-500 mt-1">
            Used for inventory tracking and integrations
        </div>
    </div>
</div>