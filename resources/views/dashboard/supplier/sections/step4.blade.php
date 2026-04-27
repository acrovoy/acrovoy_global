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

        @vite(['resources/js/product-create.js', 'resources/js/product-form-steps.js'])











        
        