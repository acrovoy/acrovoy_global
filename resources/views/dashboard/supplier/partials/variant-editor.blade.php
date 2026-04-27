<div>

    {{-- 🔹 Parent Product Preview Editor --}}
    <div id="parent-product-block" class="mb-4 p-4 border rounded-xl bg-gray-50 flex flex-col items-start gap-4 hidden shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="flex-1 w-full">
            <label class="text-sm font-medium text-left">Main Product Title</label>
            <input type="text"
                name="parent_title"
                value="{{ old('parent_title') }}"
                class="w-full border rounded-lg border-gray-300 p-2 mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Example: White Fabric">
        </div>

        <div class="gap-2 mt-4 w-full">
            <label class="text-sm font-medium text-left">Variant Image</label>

            <!-- Upload block -->
            <label class="w-24 h-24 flex items-center justify-center border rounded cursor-pointer bg-gray-100 hover:bg-gray-200 transition relative overflow-hidden text-center">

                <span class="text-gray-500 text-sm leading-tight parent-upload-text absolute">
                    Click<br>to upload
                </span>

                <img class="parent-image-preview w-full h-full object-cover rounded hidden">

                <input type="file"
                    name="parent_image"
                    class="parent-image-input opacity-0 absolute inset-0 cursor-pointer"
                    accept="image/*">
            </label>

            <!-- Старое превью оставлено (но скрыто) -->
            <img class="parent-image-preview w-24 h-24 object-cover rounded border bg-gray-100 mt-2 transition-transform duration-150 hover:scale-105 hidden">

        </div>
    </div>

    {{-- 🔹 Variant Container --}}
    <div id="variant-container" class="flex flex-col gap-3"></div>

    <button type="button"
        id="addVariantBtn"
        class="text-blue-700 mt-3 font-medium px-4 py-2 rounded-lg hover:bg-blue-50 transition">
        + Add Variant
    </button>

</div>

{{-- 🔹 Template for a variant row --}}
<template id="variant-row-template">
    <div class="variant-row border rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow duration-200 space-y-3 flex flex-col gap-2">

        <div>
            <div class="flex justify-between items-center">
                <h4 class="font-semibold"></h4>
                <button type="button" class="remove-variant text-red-600 hover:text-red-800 font-semibold">
                    ✕
                </button>
            </div>
            <label class="text-sm font-medium">Variant Title</label>
            <input type="text"
                class="variant-title w-full border rounded-lg border-gray-300 p-2 mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Example: Yellow Fabric">
        </div>

        <div>
            <label class="text-sm font-medium">Variant Linked Product</label>
            <div class="variant-product-selector relative z-20">

                <input type="hidden" class="variant-product-select">

                <!-- Selected -->
                <div class="product-selector-trigger w-full border rounded-lg border-gray-300 p-2 mt-1 cursor-pointer bg-white flex items-center gap-2">
                    <span class="text-gray-400">Select Linked Product</span>
                </div>

                <!-- Dropdown -->
                <div class="product-selector-dropdown hidden absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto">

                    <div class="p-2 border-b">
                        <input type="text"
                            class="product-search w-full border rounded p-1 text-sm"
                            placeholder="Search product...">
                    </div>

                    <div class="product-options">

                        @foreach($products as $p)

                        <div class="product-option flex items-center gap-2 p-2 hover:bg-gray-100 cursor-pointer"
                            data-id="{{ $p->id }}"
                            data-name="{{ $p->name ?? $p->slug }}"
                            data-image="{{ $p->mainImage?->url('thumb') ?? asset('images/no-image.png') }}">

                            <img src="{{ $p->mainImage?->url('thumb') ?? asset('images/no-image.png') }}"
                                class="w-8 h-8 object-cover rounded border">

                            <span class="text-sm">
                                {{ $p->name ?? $p->slug }}
                                <span class="text-gray-400">(Product id: {{ $p->id }})</span>
                            </span>

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>
        </div>

        <div>
            <label class="text-sm font-medium">Variant Image</label>

            <!-- Upload block -->
            <label class="w-24 h-24 flex items-center justify-center border rounded cursor-pointer bg-gray-100 hover:bg-gray-200 transition relative overflow-hidden text-center">

                <span class="text-gray-500 text-sm leading-tight variant-upload-text absolute">
                    Click<br>to upload
                </span>

                <img class="variant-image-preview w-full h-full object-cover rounded hidden">

                <input type="file"
                    class="variant-image opacity-0 absolute inset-0 cursor-pointer"
                    accept="image/*">
            </label>

            <!-- Старое превью оставлено -->
            <img class="variant-image-preview w-24 h-24 object-cover rounded border bg-gray-100 mt-2 transition-transform duration-150 hover:scale-105 hidden">

        </div>

    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const container = document.getElementById('variant-container');
    const addBtn = document.getElementById('addVariantBtn');

    let variantIndex = 0;

    function attachRowEvents(row, index) {

        row.querySelector('.variant-title')
            .setAttribute('name', `variant_titles[${index}]`);

        row.querySelector('.variant-product-select')
            .setAttribute('name', `variant_products[${index}]`);

        row.querySelector('.variant-image')
            .setAttribute('name', `variant_images[${index}]`);

        const inputFile = row.querySelector('.variant-image');

        const label = inputFile.closest('label');
        const preview = label.querySelector('.variant-image-preview');
        const uploadText = label.querySelector('.variant-upload-text');

        inputFile.addEventListener('change', function(e) {

            const file = e.target.files[0];

            if (file) {

                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                uploadText.classList.add('hidden');

            } else {

                preview.classList.add('hidden');
                uploadText.classList.remove('hidden');

            }

        });

        // 🔹 Shopify-style selector (перенесён сюда)

        const selector = row.querySelector('.variant-product-selector');

        if(selector){

            const trigger = selector.querySelector('.product-selector-trigger');
            const dropdown = selector.querySelector('.product-selector-dropdown');
            const options = selector.querySelectorAll('.product-option');
            const hiddenInput = selector.querySelector('.variant-product-select');
            const searchInput = selector.querySelector('.product-search');

            trigger.addEventListener('click', () => {

                document.querySelectorAll('.product-selector-dropdown')
                    .forEach(d => d.classList.add('hidden'));

                dropdown.classList.toggle('hidden');

            });

            options.forEach(option => {

                option.addEventListener('click', () => {

                    const id = option.dataset.id;
                    const name = option.dataset.name;
                    const image = option.dataset.image;

                    hiddenInput.value = id;

                    trigger.innerHTML = `
                        <img src="${image}" class="w-6 h-6 rounded object-cover border">
                        <span class="text-sm">${name} <span class="text-gray-400">(ID: ${id})</span></span>
                    `;

                    dropdown.classList.add('hidden');

                });

            });

            searchInput.addEventListener('input', function(){

                const value = this.value.toLowerCase();

                options.forEach(opt => {

                    const text = opt.innerText.toLowerCase();

                    opt.style.display = text.includes(value) ? "flex" : "none";

                });

            });

        }

    }

    function setupImagePreview(inputSelector, previewSelector, textSelector) {

        const inputs = document.querySelectorAll(inputSelector);

        inputs.forEach(input => {

            const label = input.closest('label');
            const preview = label.querySelector(previewSelector);
            const uploadText = label.querySelector(textSelector);

            input.addEventListener('change', function(e) {

                const file = e.target.files[0];

                if (file) {

                    preview.src = URL.createObjectURL(file);
                    preview.classList.remove('hidden');
                    uploadText.classList.add('hidden');

                } else {

                    preview.classList.add('hidden');
                    uploadText.classList.remove('hidden');

                }

            });

        });

    }

    // Parent
    setupImagePreview('.parent-image-input', '.parent-image-preview', '.parent-upload-text');

    // Variant
    setupImagePreview('.variant-image', '.variant-image-preview', '.variant-upload-text');


    function reindexRows() {

        const rows = container.querySelectorAll('.variant-row');

        rows.forEach((row, newIndex) => {

            attachRowEvents(row, newIndex);

        });

        variantIndex = rows.length;

    }

    function createVariantRow() {

        const parentBlock = document.getElementById('parent-product-block');

        if (variantIndex === 0) {

            parentBlock.classList.remove('hidden');

        }

        const template = document.getElementById('variant-row-template');
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.variant-row');

        attachRowEvents(row, variantIndex);

        clone.querySelector('.remove-variant').addEventListener('click', function(e) {

            e.target.closest('.variant-row').remove();

            reindexRows();

            if (container.querySelectorAll('.variant-row').length === 0) {

                parentBlock.classList.add('hidden');

            }

        });

        container.appendChild(clone);

        variantIndex++;

    }

    addBtn.addEventListener('click', createVariantRow);

});

document.addEventListener('click', function(e){

    if(!e.target.closest('.variant-product-selector')){

        document.querySelectorAll('.product-selector-dropdown')
            .forEach(d => d.classList.add('hidden'));

    }

});
</script>