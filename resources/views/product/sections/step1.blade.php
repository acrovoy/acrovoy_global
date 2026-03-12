{{-- ================= BASIC INFO ================= --}}
<h3 class="text-2xl font-bold mb-6">Basic Information</h3>

{{-- Product Name --}}
<div x-data="{ open: false }" class="border rounded p-4 mb-4 bg-white shadow-sm">
    <h4 class="font-semibold mb-3">Product Name</h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
            @if($index == 0)
                {{-- Первый язык всегда виден --}}
                <div class="flex-1">
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <input type="text"
                           name="name[{{ $language->code }}]"
                           class="input mb-2 w-full"
                           placeholder="Product Name ({{ $language->code }})"
                           value="{{ old(
                               'name.' . $language->code,
                               $translations[$language->code]['name'] ?? ''
                           ) }}">
                </div>
            @else
                {{-- Остальные языки скрыты по умолчанию --}}
                <div x-show="open"
                     x-collapse
                     class="flex-1 mb-2">
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <input type="text"
                           name="name[{{ $language->code }}]"
                           class="input w-full"
                           placeholder="Product Name ({{ $language->code }})"
                           value="{{ old(
                               'name.' . $language->code,
                               $translations[$language->code]['name'] ?? ''
                           ) }}">
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



{{-- Short Description --}}
<div x-data="{ open: false }" class="border rounded p-4 mb-4 bg-white shadow-sm">
    <h4 class="font-semibold mb-3">Short Description</h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
            @if($index == 0)
                {{-- Первый язык всегда виден --}}
                <div class="flex-1">
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <input type="text"
                           name="undername[{{ $language->code }}]"
                           class="input mb-2 w-full"
                           placeholder="Undername ({{ $language->code }})"
                           value="{{ old(
                               'undername.' . $language->code,
                               $translations[$language->code]['undername'] ?? ''
                           ) }}">
                </div>
            @else
                {{-- Остальные языки скрыты по умолчанию --}}
                <div x-show="open"
                     x-collapse
                     class="flex-1 mb-2">
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <input type="text"
                           name="undername[{{ $language->code }}]"
                           class="input w-full"
                           placeholder="Undername ({{ $language->code }})"
                           value="{{ old(
                               'undername.' . $language->code,
                               $translations[$language->code]['undername'] ?? ''
                           ) }}">
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


{{-- Full Description --}}
<div x-data="{ open: false }" class="border rounded p-4 mb-4 bg-white shadow-sm">
    <h4 class="font-semibold mb-3">Full Product Description</h4>

    <div class="flex-col md:flex-row gap-2">
        @foreach($languages as $index => $language)
            @if($index == 0)
                {{-- Первый язык всегда виден --}}
                <div class="flex-1">
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <textarea name="description[{{ $language->code }}]"
                              class="input mb-2 w-full"
                              rows="4"
                              placeholder="Full Description ({{ $language->code }})">{{ old(
                                  'description.' . $language->code,
                                  $translations[$language->code]['description'] ?? ''
                              ) }}</textarea>
                </div>
            @else
                {{-- Остальные языки скрыты по умолчанию --}}
                <div x-show="open"
                     x-collapse
                     class="flex-1 mb-2">
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <textarea name="description[{{ $language->code }}]"
                              class="input w-full"
                              rows="4"
                              placeholder="Full Description ({{ $language->code }})">{{ old(
                                  'description.' . $language->code,
                                  $translations[$language->code]['description'] ?? ''
                              ) }}</textarea>
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



        {{-- Category --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Category</label>
            <select name="category" class="input w-full">
                <option value="">Select a category</option>
                @php
                function renderCategoryOptions($categories, $prefix = '', $selected = null) {
                foreach ($categories as $category) {
                $sel = $category->id == $selected ? 'selected' : '';
                echo '<option value="'.$category->id.'" '.$sel.'>'
                    .$prefix.$category->name.
                    '</option>';

                if ($category->children && $category->children->count() > 0) {
                renderCategoryOptions($category->children, $prefix.'— ', $selected);
                }
                }
                }

                $rootCategories = $categories->where('parent_id', null);
                renderCategoryOptions($rootCategories, '', $product->category_id);
                @endphp
            </select>
        </div>