<div>

    @php
    $parent = $variants->firstWhere('product_id', $product->id);
    $children = $variants->where('product_id','!=',$product->id);
    @endphp

    {{-- 🔹 Parent Product Preview Editor --}}
    <div id="parent-product-block"
        class="mb-4 p-4 border rounded-xl bg-gray-50 flex flex-col items-start gap-4 {{ $parent ? '' : 'hidden' }} shadow-sm hover:shadow-md transition-shadow duration-200">

        <div class="flex-1 w-full">
            <label class="text-sm font-medium text-left">Main Product Title</label>

            @if($parent)
                <input type="hidden" name="variants[0][id]" value="{{ $parent->id }}">
            @endif

            

            <input type="text"
                name="variants[0][title]"
                value="{{ old('variants.0.title', $parent->title ?? $product->name ?? '') }}"
                class="w-full border rounded-lg border-gray-300 p-2 mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Example: White Fabric">

            <input type="hidden"
                name="variants[0][linked_product_id]"
                value="{{ $product->id }}">

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
                    <span class="text-gray-500 text-sm leading-tight variant-upload-text absolute" style="{{ $v->image ? 'display:none;' : '' }}">
                        Click<br>to upload
                    </span>
                    <img class="variant-image-preview w-full h-full object-cover rounded {{ $v->image ? '' : 'hidden' }}" src="{{ $v->image_url ?? '' }}">
                    <input type="file" name="variants[{{ $loop->index+1 }}][image]" class="variant-image opacity-0 absolute inset-0 cursor-pointer" accept="image/*">
                </label>
                <img class="variant-image-preview w-24 h-24 object-cover rounded border bg-gray-100 mt-2 transition-transform duration-150 hover:scale-105 {{ $v->image ? '' : 'hidden' }}" src="{{ $v->image_url ?? '' }}">
            </div>

        </div>
        @endforeach

    </div>

    <button type="button"
        id="addVariantBtn"
        class="text-blue-700 mt-3 font-medium px-4 py-2 rounded-lg hover:bg-blue-50 transition">
        + Add Variant
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
    document.addEventListener('DOMContentLoaded', function() {

        const container = document.getElementById('variant-container');
        const addBtn = document.getElementById('addVariantBtn');

        let variantIndex = 0;

        /* INIT existing rows */

        const existingRows = container.querySelectorAll('.variant-row');

        if (existingRows.length) {

            existingRows.forEach((row, index) => {

                attachRowEvents(row, index);

                const preview = row.querySelector('.variant-image-preview');
                const uploadText = row.querySelector('.variant-upload-text');

                if (preview && preview.src) {
                    preview.classList.remove('hidden');
                    uploadText?.classList.add('hidden');
                }

                initProductSelector(row);

            });

            variantIndex = existingRows.length;

            document
                .getElementById('parent-product-block')
                .classList.remove('hidden');

        }

        /* remove variant */

        container.addEventListener('click', function(e) {

            const btn = e.target.closest('.remove-variant');

            if (btn) {

                const row = btn.closest('.variant-row');
                row.remove();

                reindexRows();

                const parentBlock =
                    document.getElementById('parent-product-block');

                if (container.querySelectorAll('.variant-row').length === 0) {
                    parentBlock.classList.add('hidden');
                }

            }

        });

        /* attach events */

        function attachRowEvents(row, index) {

            row.querySelector('.variant-title')
                .setAttribute('name', `variants[${index+1}][title]`);

            row.querySelector('.variant-product-select')
                .setAttribute('name', `variants[${index+1}][linked_product_id]`);

            row.querySelector('.variant-image')
                .setAttribute('name', `variants[${index+1}][image]`);

                

            const inputFile = row.querySelector('.variant-image');
            const label = inputFile.closest('label');
            const idInput = row.querySelector('.variant-id');
            const preview = label.querySelector('.variant-image-preview');
            const uploadText = label.querySelector('.variant-upload-text');
            const parentInput = document.querySelector('.parent-image-input');
            const parentPreview = document.querySelector('.parent-image-preview');
            const parentUploadText = document.querySelector('.parent-upload-text');

            if (parentInput) {
                parentInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        parentPreview.src = URL.createObjectURL(file);
                        parentPreview.classList.remove('hidden');
                        parentUploadText.classList.add('hidden');
                    } else {
                        parentPreview.classList.add('hidden');
                        parentUploadText.classList.remove('hidden');
                    }
                });
            }

            if(idInput){
                idInput.setAttribute('name', `variants[${index+1}][id]`);
            }

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

            initProductSelector(row);

        }

        /* product selector */

        function initProductSelector(row) {

            const selector = row.querySelector('.variant-product-selector');

            if (!selector) return;

            const trigger = selector.querySelector('.product-selector-trigger');
            const dropdown = selector.querySelector('.product-selector-dropdown');

            const options = selector.querySelectorAll('.product-option');

            const hiddenInput = selector.querySelector('.variant-product-select');

            const searchInput = selector.querySelector('.product-search');

            if (hiddenInput.value) {

                const selectedOption =
                    selector.querySelector(`[data-id="${hiddenInput.value}"]`);

                if (selectedOption) {

                    const id = selectedOption.dataset.id;
                    const name = selectedOption.dataset.name;
                    const image = selectedOption.dataset.image;

                    trigger.innerHTML =
                        `<img src="${image}" class="w-6 h-6 rounded object-cover border">
<span class="text-sm">${name}
<span class="text-gray-400">(ID: ${id})</span>
</span>`;

                }

            }

            trigger.addEventListener('click', () => {

                document
                    .querySelectorAll('.product-selector-dropdown')
                    .forEach(d => d.classList.add('hidden'));

                dropdown.classList.toggle('hidden');

            });

            options.forEach(option => {

                option.addEventListener('click', () => {

                    const id = option.dataset.id;
                    const name = option.dataset.name;
                    const image = option.dataset.image;

                    hiddenInput.value = id;

                    trigger.innerHTML =
                        `<img src="${image}" class="w-6 h-6 rounded object-cover border">
<span class="text-sm">${name}
<span class="text-gray-400">(ID: ${id})</span>
</span>`;

                    dropdown.classList.add('hidden');

                });

            });

            searchInput.addEventListener('input', function() {

                const value = this.value.toLowerCase();

                options.forEach(opt => {

                    const text = opt.innerText.toLowerCase();

                    opt.style.display =
                        text.includes(value) ? "flex" : "none";

                });

            });

        }

        /* reindex */

        function reindexRows() {

            const rows = container.querySelectorAll('.variant-row');

            rows.forEach((row, index) => attachRowEvents(row, index));

            variantIndex = rows.length;

        }

        /* create row */

        function createVariantRow() {

            const parentBlock =
                document.getElementById('parent-product-block');

            if (variantIndex === 0)
                parentBlock.classList.remove('hidden');

            const template =
                document.getElementById('variant-row-template');

            const clone =
                template.content.cloneNode(true);

            container.appendChild(clone);

            const rows = container.querySelectorAll('.variant-row');

            const row = rows[rows.length - 1];

            attachRowEvents(row, variantIndex);

            variantIndex++;

        }

        addBtn.addEventListener('click', createVariantRow);

    });

    /* close dropdown */

    document.addEventListener('click', function(e) {

        if (!e.target.closest('.variant-product-selector')) {

            document
                .querySelectorAll('.product-selector-dropdown')
                .forEach(d => d.classList.add('hidden'));

        }

    });
</script>