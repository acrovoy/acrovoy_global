<div>

    @php
    $originalProductId = $product->original_product_id ?? $product->id;

    $parent = $variants->firstWhere('product_id', $originalProductId);

    $children = $variants
        ->where('product_id', '!=', $originalProductId)
        ->values();
@endphp

<input type="hidden" name="variants_present" value="1">


    {{-- 🔹 Parent Product Preview Editor --}}
    <div id="parent-product-block"
        class="mb-4 p-4 border rounded-xl bg-gray-50 flex flex-col items-start gap-4 {{ $parent ? '' : 'hidden' }} shadow-sm hover:shadow-md transition-shadow duration-200">

        <div class="flex-1 w-full">
            <label class="text-sm font-medium text-left">Main Product Title</label>

            @if($parent)
                <input type="hidden" name="variants[0][id]" value="{{ $parent->id }}">
            @endif

            <input type="hidden" name="variants[0][is_primary]" value="1">

            <input type="text"
                name="variants[0][title]"
                value="{{ old('variants.0.title', $parent->title ?? $product->name ?? '') }}"
                class="w-full border rounded-lg border-gray-300 p-2 mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Example: White Fabric">

            <input type="hidden"
    name="variants[0][linked_product_id]"
    value="{{ $originalProductId }}">

        </div>

        <div class="gap-2 mt-4 w-full">

            <label class="text-sm font-medium text-left">Variant Image</label>

            <label class="w-24 h-24 flex items-center justify-center border rounded cursor-pointer bg-gray-100 hover:bg-gray-200 transition relative overflow-hidden text-center">

                <span class="text-gray-500 text-sm leading-tight parent-upload-text absolute"
                    style="{{ $parent && $parent->image_url ? 'display:none;' : '' }}">
                    Click<br>to upload
                </span>

                <img
                    class="parent-image-preview w-full h-full object-cover rounded {{ $parent && $parent->image_url ? '' : 'hidden' }}"
                    src="{{ $parent->image_url ?? '' }}">

                <input type="file"
                    name="variants[0][image]"
                    class="parent-image-input opacity-0 absolute inset-0 cursor-pointer"
                    accept="image/*">

            </label>

        </div>
    </div>

    {{-- 🔹 Variant Container --}}
    <div id="variant-container" class="flex flex-col gap-3">

        @foreach($children as $v)

        <div class="variant-row border rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow duration-200 space-y-3 flex flex-col gap-2">

            <div>

                <div class="flex justify-between items-center">
                    <h4 class="font-semibold"></h4>

                    <button type="button"
                        class="remove-variant text-red-600 hover:text-red-800 font-semibold">
                        ✕
                    </button>

                </div>

                <label class="text-sm font-medium">Variant Title</label>

                <input type="hidden"
                    name="variants[{{ $loop->index+1 }}][id]"
                    value="{{ $v->id }}">

                <input type="text"
                    name="variants[{{ $loop->index+1 }}][title]"
                    value="{{ $v->title }}"
                    class="variant-title w-full border rounded-lg border-gray-300 p-2 mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    placeholder="Example: Yellow Fabric">

            </div>

            <div>

                <label class="text-sm font-medium">Variant Linked Product</label>

                <div class="variant-product-selector relative z-20">

                    <input type="hidden"
                        name="variants[{{ $loop->index+1 }}][linked_product_id]"
                        class="variant-product-select"
                        value="{{ $v->linked_product_id }}">

                    <div class="product-selector-trigger w-full border rounded-lg border-gray-300 p-2 mt-1 cursor-pointer bg-white flex items-center gap-2">

                        @if($v->linked_product_id && $linked = $products->firstWhere('id',$v->linked_product_id))

                        <img src="{{ $linked->mainImage?->url('thumb') ?? asset('images/no-image.png') }}"
                            class="w-6 h-6 rounded object-cover border">

                        <span class="text-sm">
                            {{ $linked->name ?? $linked->slug }}
                            <span class="text-gray-400">(ID: {{ $linked->id }})</span>
                        </span>

                        @else
                        <span class="text-gray-400">Select Linked Product</span>
                        @endif

                    </div>

                    <div class="product-selector-dropdown hidden absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
                        <div class="p-2 border-b">
                            <input type="text" class="product-search w-full border rounded p-1 text-sm" placeholder="Search product...">
                        </div>
                        <div class="product-options">
                            @foreach($products as $p)
                            <div class="product-option flex items-center gap-2 p-2 hover:bg-gray-100 cursor-pointer"
                                data-id="{{ $p->id }}"
                                data-name="{{ $p->name ?? $p->slug }}"
                                data-image="{{ $p->mainImage?->url('thumb') ?? asset('images/no-image.png') }}">
                                <img src="{{ $p->mainImage?->url('thumb') ?? asset('images/no-image.png') }}" class="w-8 h-8 object-cover rounded border">
                                <span class="text-sm">{{ $p->name ?? $p->slug }} <span class="text-gray-400">(Product id: {{ $p->id }})</span></span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div>
    <label class="text-sm font-medium">Variant Image</label>
    <label class="w-24 h-24 flex items-center justify-center border rounded cursor-pointer bg-gray-100 hover:bg-gray-200 transition relative overflow-hidden text-center">
        <span
            class="text-gray-500 text-sm leading-tight variant-upload-text absolute"
            style="{{ $v->media_id ? 'display:none;' : '' }}">
            Click<br>to upload
        </span>

        <img
            class="variant-image-preview w-full h-full object-cover rounded {{ $v->media_id ? '' : 'hidden' }}"
            src="{{ $v->image_url ?? '' }}">

        <input
            type="file"
            name="variants[{{ $loop->index+1 }}][image]"
            class="variant-image opacity-0 absolute inset-0 cursor-pointer"
            accept="image/*">
    </label>
</div>

        </div>
        @endforeach

    </div>

    <button type="button"
    id="addVariantBtn"
    class="inline-flex items-center gap-2 mt-4 px-4 py-2
           text-sm font-medium text-blue-600
           bg-blue-50 border border-blue-100
           rounded-lg
           hover:bg-blue-100 hover:border-blue-200
           active:scale-[0.98]
           transition-all duration-150 shadow-sm">

    <span class="text-lg leading-none">+</span>
    <span>Add Variant</span>

</button>

</div>

{{-- 🔹 Template for a variant row --}}
<template id="variant-row-template">
    <div class="variant-row border rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow duration-200 space-y-3 flex flex-col gap-2">

        {{-- 🔹 Header и удаление --}}
        <div class="flex justify-between items-center">
            <h4 class="font-semibold">New Variant</h4>
            <button type="button" class="remove-variant text-red-600 hover:text-red-800 font-semibold">✕</button>
        </div>

        {{-- 🔹 Variant Title --}}
        <div>
            <label class="text-sm font-medium">Variant Title</label>
            
            <input type="hidden" class="variant-id" value="">
            <input type="text"
                name="variants[][title]"
                value=""
                class="variant-title w-full border rounded-lg border-gray-300 p-2 mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Example: Yellow Fabric">
        </div>

        {{-- 🔹 Linked Product --}}
        <div>
            <label class="text-sm font-medium">Variant Linked Product</label>
            <div class="variant-product-selector relative z-20">
                <input type="hidden" name="variants[][linked_product_id]" class="variant-product-select" value="">
                <div class="product-selector-trigger w-full border rounded-lg border-gray-300 p-2 mt-1 cursor-pointer bg-white flex items-center gap-2">
                    <span class="text-gray-400">Select Linked Product</span>
                </div>

                <div class="product-selector-dropdown hidden absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">
                    <div class="p-2 border-b">
                        <input type="text" class="product-search w-full border rounded p-1 text-sm" placeholder="Search product...">
                    </div>
                    <div class="product-options">
                        @foreach($products as $p)
                        <div class="product-option flex items-center gap-2 p-2 hover:bg-gray-100 cursor-pointer"
                            data-id="{{ $p->id }}"
                            data-name="{{ $p->name ?? $p->slug }}"
                            data-image="{{ $p->mainImage?->url('thumb') ?? asset('images/no-image.png') }}">
                            <img src="{{ $p->mainImage?->url('thumb') ?? asset('images/no-image.png') }}" class="w-8 h-8 object-cover rounded border">
                            <span class="text-sm">{{ $p->name ?? $p->slug }} <span class="text-gray-400">(Product id: {{ $p->id }})</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔹 Variant Image --}}
        <div>
            <label class="text-sm font-medium">Variant Image</label>
            <label class="w-24 h-24 flex items-center justify-center border rounded cursor-pointer bg-gray-100 hover:bg-gray-200 transition relative overflow-hidden text-center">
                <span class="text-gray-500 text-sm leading-tight variant-upload-text absolute">
                    Click<br>to upload
                </span>
                <img class="variant-image-preview w-full h-full object-cover rounded hidden" src="">
                <input type="file" name="variants[][image]" class="variant-image opacity-0 absolute inset-0 cursor-pointer" accept="image/*">
            </label>
        </div>

    </div>
</template>





<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('variant-container');
    const addBtn = document.getElementById('addVariantBtn');
    const template = document.getElementById('variant-row-template');
    const parentBlock = document.getElementById('parent-product-block');

    if (!container || !addBtn || !template || !parentBlock) {
        return;
    }

    let variantIndex = 0;

    /*
    |--------------------------------------------------------------------------
    | Parent image preview
    |--------------------------------------------------------------------------
    */

    const parentInput = document.querySelector('.parent-image-input');
    const parentPreview = document.querySelector('.parent-image-preview');
    const parentUploadText = document.querySelector('.parent-upload-text');

    if (parentInput) {
        parentInput.addEventListener('change', function () {
            const file = this.files?.[0];

            if (file) {
                parentPreview.src = URL.createObjectURL(file);
                parentPreview.classList.remove('hidden');
                parentUploadText?.classList.add('hidden');
            } else {
                parentPreview?.classList.add('hidden');
                parentUploadText?.classList.remove('hidden');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Initialize existing rows
    |--------------------------------------------------------------------------
    */

    const existingRows = container.querySelectorAll('.variant-row');

    existingRows.forEach((row, index) => {
        attachRowEvents(row, index);
    });

    variantIndex = existingRows.length;

    if (existingRows.length > 0) {
        parentBlock.classList.remove('hidden');
    }

    /*
    |--------------------------------------------------------------------------
    | Attach events to one row
    |--------------------------------------------------------------------------
    */

    function attachRowEvents(row, index) {
        const titleInput = row.querySelector('.variant-title');
        const productInput = row.querySelector('.variant-product-select');
        const imageInput = row.querySelector('.variant-image');
        const idInput = row.querySelector('.variant-id');

        if (titleInput) {
            titleInput.name = `variants[${index + 1}][title]`;
        }

        if (productInput) {
            productInput.name = `variants[${index + 1}][linked_product_id]`;
        }

        if (imageInput) {
            imageInput.name = `variants[${index + 1}][image]`;
        }

        if (idInput) {
            idInput.name = `variants[${index + 1}][id]`;
        }

        const label = imageInput?.closest('label');
        const preview = label?.querySelector('.variant-image-preview');
        const uploadText = label?.querySelector('.variant-upload-text');

        if (imageInput && !imageInput.dataset.previewBound) {
            imageInput.dataset.previewBound = '1';

            imageInput.addEventListener('change', function () {
                const file = this.files?.[0];

                if (file && preview) {
                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('hidden');
                    uploadText?.classList.add('hidden');
                } else {
                    preview?.classList.add('hidden');
                    uploadText?.classList.remove('hidden');
                }
            });
        }

        initProductSelector(row);
    }

    /*
    |--------------------------------------------------------------------------
    | Product selector
    |--------------------------------------------------------------------------
    */

    function initProductSelector(row) {
        const selector = row.querySelector('.variant-product-selector');

        if (!selector || selector.dataset.initialized === '1') {
            return;
        }

        selector.dataset.initialized = '1';

        const trigger = selector.querySelector('.product-selector-trigger');
        const dropdown = selector.querySelector('.product-selector-dropdown');
        const options = selector.querySelectorAll('.product-option');
        const hiddenInput = selector.querySelector('.variant-product-select');
        const searchInput = selector.querySelector('.product-search');

        function selectOption(option) {
            const id = option.dataset.id;
            const name = option.dataset.name;
            const image = option.dataset.image;

            hiddenInput.value = id;

            trigger.replaceChildren();

            const img = document.createElement('img');
            img.src = image;
            img.className = 'w-6 h-6 rounded object-cover border';

            const span = document.createElement('span');
            span.className = 'text-sm';
            span.textContent = `${name} (ID: ${id})`;

            trigger.append(img, span);
            dropdown.classList.add('hidden');
        }

        if (hiddenInput?.value) {
            const selectedOption = selector.querySelector(
                `[data-id="${hiddenInput.value}"]`
            );

            if (selectedOption) {
                selectOption(selectedOption);
            }
        }

        trigger.addEventListener('click', function () {
            document.querySelectorAll('.product-selector-dropdown')
                .forEach(item => {
                    if (item !== dropdown) {
                        item.classList.add('hidden');
                    }
                });

            dropdown.classList.toggle('hidden');
        });

        options.forEach(option => {
            option.addEventListener('click', function () {
                selectOption(option);
            });
        });

        searchInput?.addEventListener('input', function () {
            const value = this.value.toLowerCase();

            options.forEach(option => {
                option.style.display = option.innerText
                    .toLowerCase()
                    .includes(value)
                    ? 'flex'
                    : 'none';
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Reindex rows
    |--------------------------------------------------------------------------
    */

    function reindexRows() {
        const rows = container.querySelectorAll('.variant-row');

        rows.forEach((row, index) => {
            attachRowEvents(row, index);
        });

        variantIndex = rows.length;
    }

    /*
    |--------------------------------------------------------------------------
    | Remove child variant
    |--------------------------------------------------------------------------
    */

    container.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-variant');

        if (!button) {
            return;
        }

        const row = button.closest('.variant-row');

        if (!row) {
            return;
        }

        row.remove();
        reindexRows();

        // Родительский товар остаётся частью группы.
        parentBlock.classList.remove('hidden');
    });

    /*
    |--------------------------------------------------------------------------
    | Create new child variant
    |--------------------------------------------------------------------------
    */

    function createVariantRow() {
        parentBlock.classList.remove('hidden');

        const clone = template.content.cloneNode(true);
        container.appendChild(clone);

        const rows = container.querySelectorAll('.variant-row');
        const row = rows[rows.length - 1];

        attachRowEvents(row, variantIndex);
        variantIndex++;
    }

    addBtn.addEventListener('click', createVariantRow);

    /*
    |--------------------------------------------------------------------------
    | Close dropdowns when clicking outside
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.variant-product-selector')) {
            document.querySelectorAll('.product-selector-dropdown')
                .forEach(dropdown => dropdown.classList.add('hidden'));
        }
    });
});
</script>