 <h3 class="text-xl font-semibold mb-4">Variants</h3>
 <div id="variantsContainer" class="space-y-4">
     @foreach($variants as $i => $variant)
     <div class="border rounded p-3 flex items-center gap-4 variant-row">
         {{-- preview --}}
         <div class="w-20 h-20 border rounded overflow-hidden">
             <img
                 src="{{ $variant->media?->url ?? asset('images/placeholder.png') }}"
                 class="w-full h-full object-cover variant-image-preview">
         </div>

         {{-- title --}}
         <div class="flex-1">
             <input
                 type="text"
                 name="variants[{{ $i }}][title]"
                 class="input w-full variant-title"
                 value="{{ $variant->title }}"
                 placeholder="Variant name">
         </div>

         {{-- image upload --}}
         <input
             type="file"
             name="variants[{{ $i }}][image]"
             class="variant-image"
             accept="image/*">

         {{-- id --}}
         <input
             type="hidden"
             name="variants[{{ $i }}][id]"
             value="{{ $variant->id ?? '' }}">

         <button type="button" class="remove-variant text-red-500 ml-2">Remove</button>
     </div>
     @endforeach
 </div>

 <button type="button" id="addVariantBtn" class="mt-3 text-blue-700 font-medium">
     + Add variant
 </button>

 <template id="variant-row-template">
     <div class="border rounded p-3 flex items-center gap-4 variant-row">
         <div class="w-20 h-20 border rounded bg-gray-100">
             <img src="{{ asset('images/placeholder.png') }}" class="w-full h-full object-cover variant-image-preview">
         </div>

         <div class="flex-1">
             <input type="text" name="" class="input w-full variant-title" placeholder="Variant name">
         </div>

         <input type="file" name="" class="variant-image" accept="image/*">
         <input type="hidden" name="" class="variant-id" value="">
         <button type="button" class="remove-variant text-red-500 ml-2">Remove</button>
     </div>
 </template>

 <script>
     document.addEventListener('DOMContentLoaded', function() {

         const container = document.getElementById('variantsContainer');
         const addBtn = document.getElementById('addVariantBtn');
         let variantIndex = container.children.length;

         function attachEvents(row, index) {
             // обновляем имена инпутов
             row.querySelector('.variant-title').name = `variants[${index}][title]`;
             row.querySelector('.variant-image').name = `variants[${index}][image]`;
             row.querySelector('.variant-id').name = `variants[${index}][id]`;

             // превью картинки
             const inputFile = row.querySelector('.variant-image');
             const preview = row.querySelector('.variant-image-preview');

             inputFile.addEventListener('change', function(e) {
                 const file = e.target.files[0];
                 if (file) {
                     preview.src = URL.createObjectURL(file);
                 } else {
                     preview.src = "{{ asset('images/placeholder.png') }}";
                 }
             });

             // кнопка удаления
             row.querySelector('.remove-variant').addEventListener('click', function(e) {
                 row.remove();
                 reindexRows();
             });
         }

         function reindexRows() {
             const rows = container.querySelectorAll('.variant-row');
             rows.forEach((row, i) => attachEvents(row, i));
             variantIndex = rows.length;
         }

         function addVariant() {
             const template = document.getElementById('variant-row-template').content.cloneNode(true);
             const row = template.querySelector('.variant-row');
             attachEvents(row, variantIndex);
             container.appendChild(row);
             variantIndex++;
         }

         addBtn.addEventListener('click', addVariant);

         // инициализация существующих
         reindexRows();
     });
 </script>