<div class="">
    <h3 class="text-xl font-semibold mb-4">Specifications

    <x-help-tooltip width="w-80">
    <div class="space-y-2 leading-relaxed">
        <div class="font-semibold text-white">Пользовательские характеристики</div>
        <div class="text-gray-200 text-sm">
            В этом разделе вы можете добавить свои уникальные параметры для товара, 
            если стандартные характеристики категории не подходят или их недостаточно.
        </div>
        <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
            <li>Каждое поле можно заполнить на нескольких языках.</li>
            <li>Вы можете добавлять любое количество параметров.</li>
            <li>Если подходящей категории нет, создайте пользовательскую и заполните поля вручную.</li>
        </ul>
        <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
            Пример: <span class="text-gray-200">
                Для «Уличный стол» добавьте: <br>
                • <strong>Parameter:</strong> Материал столешницы — <strong>Value:</strong> Эпоксидная смола<br>
                
            </span>
        </div>
    </div>
</x-help-tooltip>


    </h3>

    <div id="specs-step-4" class="space-y-4">

        @php
            // количество спецификаций берём из первого языка
            $specCount = collect($specsTranslations)->first()
                ? count(collect($specsTranslations)->first())
                : 0;
        @endphp

        @for($i = 0; $i < $specCount; $i++)
            <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-{{ $i }}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-semibold">Specification</h4>

                    @if($i > 0)
                        <button type="button"
                                onclick="removeSpec({{ $i }})"
                                class="text-red-600 hover:text-red-800 font-semibold">
                            ✕
                        </button>
                    @endif
                </div>

                {{-- Parameter --}}
<div class="mb-4">
    <label class="block mb-1 font-medium">Parameter</label>

    @foreach($languages as $index => $language)
        @php
            $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');
        @endphp

        @if($index === 0)
            <div class="flex items-center gap-2 mb-1 w-full" x-data="charCounter(60)">
                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                <div class="relative w-full">
                    <input type="text"
                           name="specs[{{ $i }}][{{ $language->code }}][key]"
                           class="input mb-2 w-full pr-12"
                           maxlength="60"
                           placeholder="Parameter ({{ $language->code }})"
                           value="{{ old('specs.'.$i.'.'.$language->code.'.key', $specsTranslations[$language->code][$i]['key'] ?? '') }}"
                           @input="update($event.target)"
                           x-init="update($el)">
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                        <span x-text="count"></span>/<span x-text="max"></span>
                    </div>
                </div>
            </div>
        @else
            <div x-show="open" x-collapse class="flex items-center gap-2 mb-2 w-full" x-data="charCounter(60)">
                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                <div class="relative w-full">
                    <input type="text"
                           name="specs[{{ $i }}][{{ $language->code }}][key]"
                           class="input mb-2 w-full pr-12"
                           maxlength="60"
                           placeholder="Parameter ({{ $language->code }})"
                           value="{{ old('specs.'.$i.'.'.$language->code.'.key', $specsTranslations[$language->code][$i]['key'] ?? '') }}"
                           @input="update($event.target)"
                           x-init="update($el)">
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                        <span x-text="count"></span>/<span x-text="max"></span>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

{{-- Value --}}
<div>
    <label class="block mb-1 font-medium">Value</label>

    @foreach($languages as $index => $language)
        @php
            $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');
        @endphp

        @if($index === 0)
            <div class="flex items-center gap-2 mb-1 w-full" x-data="charCounter(60)">
                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                <div class="relative w-full">
                    <input type="text"
                           name="specs[{{ $i }}][{{ $language->code }}][value]"
                           class="input mb-2 w-full pr-12"
                           maxlength="60"
                           placeholder="Value ({{ $language->code }})"
                           value="{{ old('specs.'.$i.'.'.$language->code.'.value', $specsTranslations[$language->code][$i]['value'] ?? '') }}"
                           @input="update($event.target)"
                           x-init="update($el)">
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                        <span x-text="count"></span>/<span x-text="max"></span>
                    </div>
                </div>
            </div>
        @else
            <div x-show="open" x-collapse class="flex items-center gap-2 mb-2 w-full" x-data="charCounter(60)">
                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                <div class="relative w-full">
                    <input type="text"
                           name="specs[{{ $i }}][{{ $language->code }}][value]"
                           class="input mb-2 w-full pr-12"
                           maxlength="60"
                           placeholder="Value ({{ $language->code }})"
                           value="{{ old('specs.'.$i.'.'.$language->code.'.value', $specsTranslations[$language->code][$i]['value'] ?? '') }}"
                           @input="update($event.target)"
                           x-init="update($el)">
                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                        <span x-text="count"></span>/<span x-text="max"></span>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

                {{-- Кнопка языков --}}
                @if(count($languages) > 1)
                    <div class="flex justify-between mt-1">
                        <div class="text-xs text-gray-500 italic">
                            * Optional, maximum xx characters
                        </div>

                        <button type="button"
                                @click="open = !open"
                                class="mt-3 text-sm text-blue-600 hover:underline flex items-center gap-1">
                            Other Languages
                            <svg :class="{ 'rotate-180': open }"
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
        @endfor

        {{-- Если спецификаций ещё нет --}}
        @if($specCount === 0)
            <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-0">
                <h4 class="font-semibold mb-3">Specification</h4>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Parameter</label>

                    @foreach($languages as $index => $language)
                        @php
                            $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');
                        @endphp

                        @if($index === 0)
                            <div class="flex items-center gap-2 mb-1 w-full" x-data="charCounter(60)">
                                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                                <div class="relative w-full">
                                    <input type="text"
                                           name="specs[0][{{ $language->code }}][key]"
                                           class="input mb-2 w-full pr-12"
                                           maxlength="60"
                                           placeholder="Parameter ({{ $language->code }})"
                                           @input="update($event.target)"
                                           x-init="update($el)">
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                                        <span x-text="count"></span>/<span x-text="max"></span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div x-show="open" x-collapse class="flex items-center gap-2 mb-2 w-full" x-data="charCounter(60)">
                                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                                <div class="relative w-full">
                                    <input type="text"
                                           name="specs[0][{{ $language->code }}][key]"
                                           class="input mb-2 w-full pr-12"
                                           maxlength="60"
                                           placeholder="Parameter ({{ $language->code }})"
                                           @input="update($event.target)"
                                           x-init="update($el)">
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                                        <span x-text="count"></span>/<span x-text="max"></span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div>
                    <label class="block mb-1 font-medium">Value</label>

                    @foreach($languages as $index => $language)
                        @php
                            $flagPath = asset('images/flags/svg/' . strtolower($language->code) . '.svg');
                        @endphp

                        @if($index === 0)
                            <div class="flex items-center gap-2 mb-1 w-full" x-data="charCounter(60)">
                                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                                <div class="relative w-full">
                                    <input type="text"
                                           name="specs[0][{{ $language->code }}][value]"
                                           class="input mb-2 w-full pr-12"
                                           maxlength="60"
                                           placeholder="Value ({{ $language->code }})"
                                           @input="update($event.target)"
                                           x-init="update($el)">
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                                        <span x-text="count"></span>/<span x-text="max"></span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div x-show="open" x-collapse class="flex items-center gap-2 mb-2 w-full" x-data="charCounter(60)">
                                <img src="{{ $flagPath }}" alt="{{ $language->code }}" class="w-5 h-5 rounded">
                                <div class="relative w-full">
                                    <input type="text"
                                           name="specs[0][{{ $language->code }}][value]"
                                           class="input mb-2 w-full pr-12"
                                           maxlength="60"
                                           placeholder="Value ({{ $language->code }})"
                                           @input="update($event.target)"
                                           x-init="update($el)">
                                    <div class="absolute right-2 top-1/2 -translate-y-1/2 text-xs" :class="color">
                                        <span x-text="count"></span>/<span x-text="max"></span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if(count($languages) > 1)

                <div class="flex justify-between mt-1">

        <div class="text-xs text-gray-500 italic">
            * Optional, maximum 60 characters
        </div>


                    <button type="button"
                            @click="open = !open"
                            class="mt-3 text-sm text-blue-600 hover:underline flex items-center gap-1">
                        Other Languages
                        <svg :class="{ 'rotate-180': open }"
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
        @endif
    </div>

    <button type="button"
            onclick="addSpec('specs-step-4')"
            class="mt-3 text-blue-700 font-medium">
        + Add specification
    </button>
</div>

<script>
    window.appLanguages = @json($languages->pluck('code'));
    window.existingSpecs = @json(
        collect($product->specifications ?? [])->values()
    );

    function charCounter(max = 60) {
        return {
            max,
            count: 0,
            color: 'text-gray-400',
            update(el) {
                this.count = el.value.length;
                if (this.count >= this.max) this.color = 'text-red-500';
                else if (this.count > this.max * 0.7) this.color = 'text-yellow-500';
                else this.color = 'text-gray-400';
            }
        }
    }
</script>

@vite(['resources/js/product-edit-specifications.js'])