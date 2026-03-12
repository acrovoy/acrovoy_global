<h3 class="text-2xl font-bold mb-6">Basic Information</h3>

        {{-- Product Name --}}
        <div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">
            <h4 class="font-semibold mb-3">Product Name</h4>

            <div class="flex-col md:flex-row gap-2">
                @foreach($languages as $index => $language)
                    @if($index == 0)
                        {{-- Первый язык всегда виден --}}
                        <div class="flex-1 mb-2">
                            <label class="block text-sm text-gray-600 mb-1">
                                {{ strtoupper($language->code) }}
                            </label>
                            <input type="text"
                                name="name[{{ $language->code }}]"
                                class="input w-full"
                                placeholder="Product Name ({{ $language->code }})"
                                value="{{ old('name.' . $language->code, $translations[$language->code]['name'] ?? '') }}">
                        </div>
                    @else
                        {{-- Остальные языки скрыты по умолчанию --}}
                        <div x-show="open" class="flex-1 mb-2 transition-all duration-300 ease-in-out">
                            <label class="block text-sm text-gray-600 mb-1">
                                {{ strtoupper($language->code) }}
                            </label>
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
                    @if($index == 0)
                        {{-- Первый язык всегда виден --}}
                        <input type="text"
                            name="undername[{{ $language->code }}]"
                            class="input flex-1 mb-2"
                            placeholder="Undername ({{ $language->code }})"
                            value="{{ old('undername.' . $language->code) }}">
                    @else
                        {{-- Остальные языки скрыты по умолчанию --}}
                        <input x-show="open"
                            type="text"
                            name="undername[{{ $language->code }}]"
                            class="input flex-1 mb-2 transition-all duration-300 ease-in-out"
                            placeholder="Undername ({{ $language->code }})"
                            value="{{ old('undername.' . $language->code) }}">
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


        {{-- Full Product Description --}}
        <div class="border rounded p-4 mb-4 bg-white shadow-sm" x-data="{ open: false }">
            <h4 class="font-semibold mb-3">Full Product Description</h4>

            <div class="flex-col md:flex-row gap-2">
                @foreach($languages as $index => $language)
                    @if($index == 0)
                        {{-- Первый язык всегда виден --}}
                        <textarea name="description[{{ $language->code }}]"
                                class="input mb-2 flex-1"
                                rows="4"
                                placeholder="Full Description ({{ $language->code }})">{{ old('description.' . $language->code) }}</textarea>
                    @else
                        {{-- Остальные языки скрыты по умолчанию --}}
                        <textarea x-show="open"
                                name="description[{{ $language->code }}]"
                                class="input mb-2 flex-1 transition-all duration-300 ease-in-out"
                                rows="4"
                                placeholder="Full Description ({{ $language->code }})">{{ old('description.' . $language->code) }}</textarea>
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


        {{-- Категория --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium">Category</label>
            <select name="category" class="input w-full">
                <option value="">Select a category</option>
                @php
                function renderCategoryOptions($categories, $prefix = '') {
                foreach ($categories as $category) {
                echo '<option value="'.$category->id.'">'.$prefix.$category->name.'</option>';
                if ($category->children && $category->children->count() > 0) {
                renderCategoryOptions($category->children, $prefix.'— ');
                }
                }
                }
                $rootCategories = $categories->where('parent_id', null);
                renderCategoryOptions($rootCategories);
                @endphp
            </select>
        </div>