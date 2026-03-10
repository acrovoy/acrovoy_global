<div>

    {{-- 🔹 Parent Product Preview Editor --}}
    <div class="mb-4 p-4 border rounded-xl bg-gray-50 flex items-center gap-4">
        <div class="flex-1">
            <label class="text-sm font-medium">Parent Product Title</label>
            <input type="text"
                   name="parent_title"
                   value="{{ old('parent_title') }}"
                   class="w-full border rounded-lg p-2 mt-1">
        </div>

        <div class="flex flex-col items-center gap-2">
            <label class="text-sm font-medium">Parent Product Image</label>
            <input type="file" name="parent_image" class="parent-image-input" accept="image/*">
            
        </div>
    </div>

    {{-- 🔹 Variant Container --}}
    <div id="variant-container" class="flex flex-col gap-3"></div>

    <button type="button"
            id="addVariantBtn"
            class="text-blue-700 mt-3 font-medium">
        + Add Variant
    </button>

</div>

{{-- 🔹 Template for a variant row --}}
<template id="variant-row-template">
    <div class="variant-row border rounded-xl p-4 bg-white shadow-sm space-y-3 flex flex-col gap-2">

        <div>
            <label class="text-sm font-medium">Variant Title</label>
            <input type="text"
                   class="variant-title w-full border rounded-lg p-2 mt-1"
                   placeholder="Example: White Fabric">
        </div>

        <div>
            <label class="text-sm font-medium">Variant Product</label>
            <select class="variant-product-select w-full border rounded-lg p-2 mt-1">
                <option value="">-- Select Product --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->name ?? $p->slug }} (ID: {{ $p->id }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium">Variant Image</label>
            <input type="file"
                   class="variant-image w-full border rounded-lg mt-1"
                   accept="image/*">
            <img class="variant-image-preview w-16 h-16 object-cover rounded mt-1" src="{{ asset('images/placeholder.png') }}">
        </div>

        <button type="button"
                class="remove-variant text-red-600 text-sm">
            Remove
        </button>

    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {

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

        // Превью картинки варианта
        const inputFile = row.querySelector('.variant-image');
        const preview = row.querySelector('.variant-image-preview');

        inputFile.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if(file){
                preview.src = URL.createObjectURL(file);
            } else {
                preview.src = "{{ asset('images/placeholder.png') }}";
            }
        });
    }

    function reindexRows() {
        const rows = container.querySelectorAll('.variant-row');
        rows.forEach((row, newIndex) => {
            attachRowEvents(row, newIndex);
        });
        variantIndex = rows.length;
    }

    function createVariantRow() {
        const template = document.getElementById('variant-row-template');
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('.variant-row');

        attachRowEvents(row, variantIndex);

        clone.querySelector('.remove-variant').addEventListener('click', function (e) {
            e.target.closest('.variant-row').remove();
            reindexRows();
        });

        container.appendChild(clone);

        variantIndex++;
    }

    addBtn.addEventListener('click', createVariantRow);

    // 🔹 Parent Image Preview
    const parentInput = document.querySelector('.parent-image-input');
    const parentPreview = document.querySelector('.parent-image-preview');

    parentInput.addEventListener('change', function(e){
        const file = e.target.files[0];
        if(file){
            parentPreview.src = URL.createObjectURL(file);
        } else {
            parentPreview.src = "{{ asset('images/placeholder.png') }}";
        }
    });

});
</script>