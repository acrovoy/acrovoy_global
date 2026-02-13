{{-- SUCCESS --}}
@if(session('success'))
<div class="mb-6 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
    {{ session('success') }}
</div>
@endif

{{-- ERROR --}}
@if(session('error'))
<div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
<div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
    <ul class="list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST"
    action="{{ route('manufacturer.products.store') }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    @php
    $languages = \App\Models\Language::where('is_active', true)->get();
    @endphp

    {{-- Шаги формы --}}
    <div class="form-step" data-step="1">
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


    </div>

    <div class="form-step hidden" data-step="2">

        {{-- Images --}}
        <div>
            <h3 class="text-xl font-semibold mb-4">Product Images</h3>

            <label
                for="productImages"
                class="bg-white border-2 border-dashed border-gray-300 rounded-xl p-8
               flex flex-col items-center justify-center
               cursor-pointer hover:border-blue-600 hover:bg-blue-50
               transition text-center">
                <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16V4m0 0L3 8m4-4l4 4m6 4v8m0 0l4-4m-4 4l-4-4" />
                </svg>

                <span class="text-lg font-medium text-gray-700">
                    Upload product images
                </span>

                <input
                    type="file"
                    name="images[]"
                    multiple
                    id="productImages"
                    class="hidden"
                    accept="image/jpeg,image/png,image/webp" />

                <input
                    id="productMainImage"
                    type="hidden"
                    name="main_image"
                     />
            </label>

            {{-- Пояснение --}}
            <p class="text-sm text-gray-500 mt-2">
                JPG, PNG. Max 5 MB per image.
            </p>

            {{-- Превью --}}
            <div id="imagesPreview" class="flex  flex-wrap gap-4 mt-4"></div>
        </div>




        <h3 class="text-xl font-semibold mb-4">Price Tiers</h3>
        {{-- Price tiers --}}
        <div id="price-tiers" class="space-y-3">
            <div class="grid grid-cols-3 gap-4">
                <input type="number" name="price_tiers[0][min_qty]" placeholder="Min Qty" class="input">
                <input type="number" name="price_tiers[0][max_qty]" placeholder="Max Qty" class="input">
                <input type="number" name="price_tiers[0][price]" placeholder="Unit Price $" class="input">
            </div>
        </div>
        <button type="button" onclick="addPriceTier()" class="text-blue-700 mt-3">+ Add price tier</button>
    </div>

    <div class="form-step hidden" data-step="3">
        <h3 class="text-xl font-semibold mb-4">Colors & Materials</h3>
        <div id="materials-wrapper" class="flex flex-col"></div>
        <button type="button" id="addMaterialBtn" class="text-blue-700 mt-3">+ Add Material</button>

        <h3 class="mt-6 text-xl font-semibold mb-4">Materials Used</h3>
        <div id="selected-materials" class="flex flex-wrap gap-2 mb-2"></div>
        <input type="text" id="materialSearch" placeholder="Search materials..." class="w-full mb-2">
        <div id="materials-options" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
            @foreach($materials as $material)
            <button type="button" class="shadow-sm material-option px-1 py-1 border rounded bg-white" data-id="{{ $material->id }}" data-name="{{ $material->translations->first()?->name ?? $material->name }}">
                {{ $material->translations->first()?->name ?? $material->name }}
            </button>
            @endforeach
        </div>
        <input type="hidden" name="materials_selected" id="materialsSelectedInput">
    </div>

    {{-- SPECIFICATIONS --}}
<div class="form-step hidden" data-step="4">
    <h3 class="text-xl font-semibold mb-4">Specifications</h3>

    <div id="specs-step-4" class="space-y-4">

        {{-- SPEC #0 --}}
        <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-0">
            <h4 class="font-semibold mb-3">Specification</h4>

            {{-- Parameter --}}
            <div class="mb-4">
                <label class="block mb-1 font-medium">Parameter</label>

                @foreach($languages as $index => $language)
                    @if($index === 0)
                        <label class="block text-sm text-gray-600 mb-1">
                            {{ strtoupper($language->code) }}
                        </label>
                        <input type="text"
                               name="specs[0][{{ $language->code }}][key]"
                               placeholder="Parameter ({{ $language->code }})"
                               class="input mb-2 w-full">
                    @else
                        <div x-show="open" x-collapse>
                            <label class="block text-sm text-gray-600 mb-1">
                                {{ strtoupper($language->code) }}
                            </label>
                            <input type="text"
                                   name="specs[0][{{ $language->code }}][key]"
                                   placeholder="Parameter ({{ $language->code }})"
                                   class="input mb-2 w-full">
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
                               name="specs[0][{{ $language->code }}][value]"
                               placeholder="Value ({{ $language->code }})"
                               class="input mb-2 w-full">
                    @else
                        <div x-show="open" x-collapse>
                            <label class="block text-sm text-gray-600 mb-1">
                                {{ strtoupper($language->code) }}
                            </label>
                            <input type="text"
                                   name="specs[0][{{ $language->code }}][value]"
                                   placeholder="Value ({{ $language->code }})"
                                   class="input mb-2 w-full">
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Languages toggle --}}
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

    </div>

    <button type="button"
            onclick="addSpec('specs-step-4')"
            class="mt-3 text-blue-700 font-medium">
        + Add specification
    </button>
</div>




    <div class="form-step hidden" data-step="5">
        <h3 class="text-xl font-semibold mb-4">Commercial Terms</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <input type="text" name="moq" placeholder="MOQ (e.g. 10 pcs)" class="input">
            <input type="text" name="lead_time" placeholder="Lead time (e.g. 25–35 days)" class="input">
            <select name="customization" class="input">
                <option value="available">Customization Available</option>
                <option value="not_available">No Customization</option>
            </select>
        </div>


        {{-- Country of origin --}}
        <div class="mt-6">
            <h3 class="text-xl font-semibold mb-4">Country of Origin</h3>

            <select name="country_id" class="input w-full">
                <option value="">Select a country</option>
                @foreach($countries as $country)
                <option value="{{ $country->id }}">
                    {{ $country->name }}
                </option>
                @endforeach
            </select>
            <p class="text-sm text-gray-500 mt-1">Выберите страну, из которой поставляется товар.</p>
        </div>



        
        {{-- Shipping Templates --}}
<div class="mt-6">
    <h3 class="text-xl font-semibold mb-4">Shipping Templates</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Platform / Default Shipping --}}
        @if($defaultShippingTemplate)
            <label
                class="border-2 border-dashed border-gray-400 rounded-xl p-4 cursor-pointer transition
                       hover:border-gray-700 hover:bg-gray-100
                       flex gap-3 items-start bg-gray-50 shadow-sm">

                <input
                    type="checkbox"
                    class="mt-1"
                    checked
                    disabled
                >

                <input
                    type="hidden"
                    name="shipping_templates[]"
                    value="{{ $defaultShippingTemplate->id }}"
                                        
                >

                <div>
                    <div class="font-semibold text-gray-900 flex items-center gap-2">
                        {{ $defaultShippingTemplate->title }}
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">
                            Platform delivery
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                        {{ $defaultShippingTemplate->description }}
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        Price and delivery time will be calculated after order placement
                    </div>
                </div>
            </label>
        @endif

        {{-- Seller Shipping Templates --}}
        @foreach($shippingTemplates as $template)
            <label
                class="border rounded-xl p-4 cursor-pointer transition
                       hover:border-blue-600 hover:bg-blue-50
                       flex gap-3 items-start bg-white shadow-sm">

                <input
                    type="checkbox"
                    name="shipping_templates[]"
                    value="{{ $template->id }}"
                    class="mt-1">

                <div>
                    <div class="font-semibold text-gray-900">
                        {{ $template->title }}
                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                        {{ $template->description }}
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        Seller-defined delivery
                    </div>
                </div>
            </label>
        @endforeach

    </div>

    <p class="text-sm text-gray-500 mt-2">
        Выберите один или несколько шаблонов доставки.
        Если выбран только платформенный вариант — заказ будет ожидать расчёта доставки.
    </p>
</div>


    </div>


    {{-- Навигация между шагами --}}
    <div class="flex mt-6">
        <button type="button" id="prevBtn" class="bg-gray-300 px-6 py-2 rounded hidden">Назад</button>
        <button type="button" id="nextBtn" class="ml-auto bg-blue-800 text-white px-6 py-2 rounded">Далее</button>
        <button type="submit" id="submitBtn" class="ml-auto bg-green-600 text-white px-6 py-2 rounded hidden">Сохранить</button>
    </div>
</form>

<style>
    .input {
        width: 100%;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-family: 'Figtree', sans-serif;
    }
</style>

@vite(['resources/js/product-create.js', 'resources/js/product-form-steps.js'])

{{-- JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>

<script>
    const preview = document.getElementById('preview-0');
    const colorInput = document.getElementById('colorInput-0');
    const fileInput = document.getElementById('fileInput-0');

    // Создаём Pickr, привязанный к кнопке
    const pickr = Pickr.create({
        el: '#colorBtn-0',
        theme: 'classic',
        default: '#ffffff',
        inline: false,
        useAsButton: true, // ключевой момент — Pickr не заменяет кнопку
        components: {
            preview: true,
            opacity: false,
            hue: true,
            interaction: {
                input: true,
                save: true
            }
        }
    });

    // При выборе цвета
    pickr.on('save', (color) => {
        const hex = color.toHEXA().toString();
        colorInput.value = hex;
        preview.style.backgroundColor = hex;
        preview.style.backgroundImage = '';
        pickr.hide();
    });

    // Загрузка текстуры
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            colorInput.value = ''; // сброс цвета
            const reader = new FileReader();
            reader.onload = e => {
                preview.style.backgroundImage = `url('${e.target.result}')`;
                preview.style.backgroundSize = 'cover';
                preview.style.backgroundPosition = 'center';
                preview.style.backgroundColor = '#ffffff';
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    });
</script>

<script>
    /**
     * Определяем стартовый индекс спецификаций
     * Для edit-формы — берём количество уже существующих specs
     * Для create — начнётся с 1
     */
    specIndex = Date.now()

    /**
     * Добавление спецификации
     */
    function addSpec(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        let html = `
        <div x-data="{ open: false }" class="border rounded p-4 bg-white" id="spec-${specIndex}">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold">Specification</h4>
                <button type="button"
                        onclick="removeSpec(${specIndex})"
                        class="text-red-600 hover:text-red-800 font-semibold">
                    ✕
                </button>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Parameter</label>
        `;

        @foreach($languages as $index => $language)
            @if($index === 0)
                html += `
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <input type="text"
                           name="specs[${specIndex}][{{ $language->code }}][key]"
                           class="input mb-2 w-full"
                           placeholder="Parameter ({{ $language->code }})">
                `;
            @else
                html += `
                    <div x-show="open" x-collapse>
                        <label class="block text-sm text-gray-600 mb-1">
                            {{ strtoupper($language->code) }}
                        </label>
                        <input type="text"
                               name="specs[${specIndex}][{{ $language->code }}][key]"
                               class="input mb-2 w-full"
                               placeholder="Parameter ({{ $language->code }})">
                    </div>
                `;
            @endif
        @endforeach

        html += `
            </div>

            <div>
                <label class="block mb-1 font-medium">Value</label>
        `;

        @foreach($languages as $index => $language)
            @if($index === 0)
                html += `
                    <label class="block text-sm text-gray-600 mb-1">
                        {{ strtoupper($language->code) }}
                    </label>
                    <input type="text"
                           name="specs[${specIndex}][{{ $language->code }}][value]"
                           class="input mb-2 w-full"
                           placeholder="Value ({{ $language->code }})">
                `;
            @else
                html += `
                    <div x-show="open" x-collapse>
                        <label class="block text-sm text-gray-600 mb-1">
                            {{ strtoupper($language->code) }}
                        </label>
                        <input type="text"
                               name="specs[${specIndex}][{{ $language->code }}][value]"
                               class="input mb-2 w-full"
                               placeholder="Value ({{ $language->code }})">
                    </div>
                `;
            @endif
        @endforeach

        html += `
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
        `;

        container.insertAdjacentHTML('beforeend', html);

        // 🔴 КРИТИЧНО: инициализация Alpine для динамически добавленного блока
        if (window.Alpine) {
            Alpine.initTree(container.lastElementChild);
        }

        specIndex++;
    }

    /**
     * Удаление спецификации
     */
    function removeSpec(index) {
        const el = document.getElementById(`spec-${index}`);
        if (el) {
            el.remove();
        }
    }
</script>