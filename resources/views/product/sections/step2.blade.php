{{-- ================= MATERIALS ================= --}}
<h3 class="text-xl font-semibold mb-4">Materials Used</h3>

{{-- Контейнер для выбранных материалов (чипсы) --}}
<div id="selected-materials" class="flex flex-wrap gap-2 mb-2"></div>

{{-- Поиск --}}
<input type="text" id="materialSearch" placeholder="Search materials..." class="w-full mb-2 border rounded-lg border-gray-300 px-3 py-2 text-sm text-gray-600">

{{-- Список всех материалов для выбора --}}
<div id="materials-options" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
    @foreach($materialsPrepared as $material)
        <button type="button"
            class="material-option px-1 py-1 border rounded bg-white shadow-sm"
            data-id="{{ $material['id'] }}"
            data-name="{{ $material['translations'][app()->getLocale()]['name'] ?? '' }}">
            {{ $material['translations'][app()->getLocale()]['name'] ?? '' }}
        </button>
    @endforeach
</div>


<input type="hidden"
       name="materials_selected"
       id="materialsSelectedInput"
       value="{{ implode(',', $product->materials->pluck('id')->toArray()) }}">



<div class="mt-6 ">
    <h3 class="text-xl font-semibold mb-4">Specifications</h3>

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
                        @if($index === 0)
                            <label class="block text-sm text-gray-600 mb-1">
                                {{ strtoupper($language->code) }}
                            </label>
                            <input type="text"
                                   name="specs[{{ $i }}][{{ $language->code }}][key]"
                                   class="input mb-2 w-full"
                                   placeholder="Parameter ({{ $language->code }})"
                                   value="{{ $specsTranslations[$language->code][$i]['key'] ?? '' }}">
                        @else
                            <div x-show="open" x-collapse>
                                <label class="block text-sm text-gray-600 mb-1">
                                    {{ strtoupper($language->code) }}
                                </label>
                                <input type="text"
                                       name="specs[{{ $i }}][{{ $language->code }}][key]"
                                       class="input mb-2 w-full"
                                       placeholder="Parameter ({{ $language->code }})"
                                       value="{{ $specsTranslations[$language->code][$i]['key'] ?? '' }}">
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Value --}}
                <div>
                    <label class="block mb-1 font-medium">Value</label>

                    @foreach($languages as $index => $language)
                        @if($index === 0)
                            <label class="block text-sm text-gray-600 mb-1">
                                {{ strtoupper($language->code) }}
                            </label>
                            <input type="text"
                                   name="specs[{{ $i }}][{{ $language->code }}][value]"
                                   class="input mb-2 w-full"
                                   placeholder="Value ({{ $language->code }})"
                                   value="{{ $specsTranslations[$language->code][$i]['value'] ?? '' }}">
                        @else
                            <div x-show="open" x-collapse>
                                <label class="block text-sm text-gray-600 mb-1">
                                    {{ strtoupper($language->code) }}
                                </label>
                                <input type="text"
                                       name="specs[{{ $i }}][{{ $language->code }}][value]"
                                       class="input mb-2 w-full"
                                       placeholder="Value ({{ $language->code }})"
                                       value="{{ $specsTranslations[$language->code][$i]['value'] ?? '' }}">
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Кнопка языков --}}
                @if(count($languages) > 1)
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
                        @if($index === 0)
                            <input type="text"
                                   name="specs[0][{{ $language->code }}][key]"
                                   class="input mb-2 w-full"
                                   placeholder="Parameter ({{ $language->code }})">
                        @else
                            <div x-show="open" x-collapse>
                                <input type="text"
                                       name="specs[0][{{ $language->code }}][key]"
                                       class="input mb-2 w-full"
                                       placeholder="Parameter ({{ $language->code }})">
                            </div>
                        @endif
                    @endforeach
                </div>

                <div>
                    <label class="block mb-1 font-medium">Value</label>

                    @foreach($languages as $index => $language)
                        @if($index === 0)
                            <input type="text"
                                   name="specs[0][{{ $language->code }}][value]"
                                   class="input mb-2 w-full"
                                   placeholder="Value ({{ $language->code }})">
                        @else
                            <div x-show="open" x-collapse>
                                <input type="text"
                                       name="specs[0][{{ $language->code }}][value]"
                                       class="input mb-2 w-full"
                                       placeholder="Value ({{ $language->code }})">
                            </div>
                        @endif
                    @endforeach
                </div>

                @if(count($languages) > 1)
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

</script>

@vite(['resources/js/product-edit-specifications.js'])