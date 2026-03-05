@extends('dashboard.layout')

@section('dashboard-content')


<div>
            <h2 class="text-2xl font-semibold text-gray-900">Edit Product</h2>
            <p class="text-sm text-gray-500 mb-4">
                Manage all your products, edit details, prices, and inventory.
            </p>
        </div>



<div class="bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
<div class="p-6">

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
    action="{{ route('products.update', $product->id) }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    @php
    $languages = \App\Models\Language::where('is_active', true)->get();
    @endphp

    {{-- ================= STEP 1: Basic Info ================= --}}
    <div class="form-step" data-step="1">
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
    </div>

    {{-- ================= STEP 2: Images & Price Tiers ================= --}}
    <div class="form-step hidden" data-step="2">

    
        {{-- Images --}}
        <div id="productImagesUploader" class="space-y-4">

            <h3 class="text-xl font-semibold">Product Images</h3>

            <input
                type="file"
                id="productImages"
                name="images[]"
                multiple
                accept="image/png,image/jpeg"
                class="hidden"
            />

            {{-- Drop Zone --}}
            <label
                for="productImages"
                id="productImagesDropZone"
                class="bg-white border-2 border-dashed border-gray-300 rounded-xl p-8
                    flex flex-col items-center justify-center
                    cursor-pointer hover:border-blue-600 hover:bg-blue-50
                    transition text-center">

                <svg class="w-10 h-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16V4m0 0L3 8m4-4l4 4m6 4v8m0 0l4-4m-4 4l-4-4"/>
                </svg>

                <span class="text-lg font-medium text-gray-700">
                    Upload product images
                </span>

                <p class="text-sm text-gray-500 mt-2">
                    JPG, PNG. Max 5 MB per image.
                </p>
            </label>

            {{-- Preview Container --}}
            <div id="imagesPreview" class="flex flex-wrap gap-4 mt-4"></div>

            <div id="imagesMetaInputs"></div>

        </div>

        @if($product->images->isNotEmpty())
        <script>
        window.existingImages = {!! json_encode(
            $product->images
                ->sortBy('sort_order')
                ->values()
                ->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'url' => $img->cdn_url,
                        'sort_order' => $img->sort_order ?? 0,
                    ];
                })
        ) !!};
        </script>
        @endif

        @vite([
            'resources/js/product-edit-uploader.js',
            'resources/js/product-edit.js',
            'resources/js/product-form-steps.js'
        ])

       {{--  PRICE TIERS  --}}
        <h3 class="text-xl font-semibold mt-6 mb-4">Price Tiers</h3>
        <div id="price-tiers" class="space-y-3">
            @foreach($product->priceTiers as $i => $tier)
            <div class="grid grid-cols-3 gap-4 items-center" id="price-tier-{{ $i }}">
                <input type="number" name="price_tiers[{{ $i }}][min_qty]" placeholder="Min Qty" class="input" value="{{ $tier->min_qty }}">
                <input type="number" name="price_tiers[{{ $i }}][max_qty]" placeholder="Max Qty" class="input" value="{{ $tier->max_qty }}">
                <div class="flex gap-2">
                    <input type="number" name="price_tiers[{{ $i }}][price]" placeholder="Unit Price $" class="input flex-1" value="{{ $tier->price }}">
                    <button type="button" onclick="removePriceTier({{ $i }})" class="text-red-600 font-bold hover:text-red-800">✕</button>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" onclick="addPriceTier()" class="text-blue-700 mt-3">+ Add price tier</button>
    </div>

    {{-- ================= STEP 3: Colors & Materials ================= --}}
    <div class="form-step hidden" data-step="3">
        <h3 class="text-xl font-semibold mb-4">Colors & Materials</h3>
        <div id="materials-wrapper" class="flex flex-col">

            @foreach($product->colors as $i => $material)
            <div class="flex items-center gap-4 mt-2 material-item" id="material-{{ $i }}">
                @php
                    $bgStyle = '';
                    if ($material->texture_path) {
                        $bgStyle = "background-image: url('" . asset('storage/' . $material->texture_path) . "'); background-size: cover; background-position: center; background-color: #fff;";
                    } elseif ($material->color) {
                        $bgStyle = "background-color: {$material->color};";
                    }
                @endphp

                <div class="w-12 h-12 border rounded cursor-pointer"
                    id="preview-{{ $i }}"
                    data-link="{{ $material->linked_product_id ? '/product/' . $material->linked_product_id : '' }}"
                    style="{{ $bgStyle }}"></div>

                <input type="hidden" name="materials[{{ $material->id }}][color]" id="colorInput-{{ $i }}" value="">
                <input type="file" name="materials[{{ $material->id }}][texture]" class="hidden" id="fileInput-{{ $i }}">
                <button type="button" id="colorBtn-{{  $i}}" class="px-4 py-2 bg-blue-800 text-white rounded">Выбрать цвет</button>
                <button type="button" onclick="document.getElementById('fileInput-{{ $i }}').click()" class="px-4 py-2 bg-blue-800 text-white rounded">Выбрать файл</button>
                <span class="text-gray-500 px-2 flex items-center">🔗</span>
                <input type="number" name="materials[{{ $material->id}}][linked_product_id]" placeholder="Product ID" class="w-32 px-2 py-1 border rounded text-sm" oninput="setMaterialLink({{ $i }}, this.value)" value="{{ $material->linked_product_id }}">
                <button type="button" id="removeMaterialBtn-{{ $i }}" class="text-red-600 font-bold">✕</button>
            </div>
            @endforeach
        </div>

        <button type="button" id="addMaterialBtn" class="mt-3 text-blue-700 font-medium">+ Add material</button>


    

        {{-- ================= MATERIALS ================= --}}
<h3 class="mt-6 text-xl font-semibold mb-4">Materials Used</h3>

{{-- Контейнер для выбранных материалов (чипсы) --}}
<div id="selected-materials" class="flex flex-wrap gap-2 mb-2"></div>

{{-- Поиск --}}
<input type="text" id="materialSearch" placeholder="Search materials..." class="w-full mb-2">

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

    </div>
     

   {{-- ================= STEP 4: Specifications ================= --}}
<div class="form-step hidden" data-step="4">
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

    {{-- ================= STEP 5: Commercial Terms ================= --}}
    <div class="form-step hidden" data-step="5">
        <h3 class="text-xl font-semibold mb-4">Commercial Terms</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <input type="text" name="moq" placeholder="MOQ (e.g. 10 pcs)" class="input" value="{{ $product->moq }}">
            <input type="text" name="lead_time" placeholder="Lead time (e.g. 25–35 days)" class="input" value="{{ $product->lead_time }}">
            <select name="customization" class="input">
    <option value="1" {{ old('customization', $product->customization) == 1 ? 'selected' : '' }}>
        Customization Available
    </option>
    <option value="0" {{ old('customization', $product->customization) == 0 ? 'selected' : '' }}>
        No Customization
    </option>
</select>
        </div>

        {{-- Country of origin --}}
        <div class="mt-6">
            <h3 class="text-xl font-semibold mb-4">Country of Origin</h3>
            <select name="country_id" class="input w-full">
                <option value="">Select a country</option>
                @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ $product->country_id == $country->id ? 'selected' : '' }}>
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

        {{-- Скрытый input, чтобы значение отправлялось --}}
        <input type="hidden" name="shipping_templates[]" value="{{ $defaultShippingTemplate->id }}">

        {{-- Видимый чекбокс только для UI --}}
        <input
            type="checkbox"
            value="{{ $defaultShippingTemplate->id }}"
            class="mt-1"
            checked
            disabled
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
                    class="mt-1"
                    {{ in_array($template->id, old('shipping_templates', $productShippingIds ?? [])) ? 'checked' : '' }}
                >

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
    <div class="flex justify-between mt-6">
        <button type="button" id="prevBtn" class="bg-gray-300 px-6 py-2 rounded hidden">Назад</button>
        <button type="button" id="nextBtn" class="bg-blue-800 text-white px-6 py-2 rounded">Далее</button>
        <button type="submit" id="submitBtn" class="bg-green-600 text-white px-6 py-2 rounded hidden">Сохранить</button>
    </div>
</form>
</div>
</div>

<style>
    .input {
        width: 100%;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-family: 'Figtree', sans-serif;
    }
</style>

@vite(['resources/js/product-edit.js', 'resources/js/product-form-steps.js'])


@endsection

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />




<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>



