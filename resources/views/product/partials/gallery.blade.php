{{-- GALLERY --}}
<div class="bg-white rounded-xl shadow p-4 mb-4">

    <div class="flex gap-4">

        {{-- THUMBNAILS --}}
        <div class="w-20 shrink-0 flex flex-col gap-3 max-h-[600px] overflow-y-auto pr-1">

            @foreach($product1->thumbnails as $media)

                <img
                    src="{{ $media['thumb'] }}"
                    class="thumbnail w-20 h-20 shrink-0 object-contain bg-gray-100 rounded-lg cursor-pointer border
                           {{ $media['is_main'] ? 'border-blue-700' : 'border-gray-300' }}"
                    data-src="{{ $media['large'] }}"
                    alt="{{ $product1->name }}"
                >

            @endforeach

        </div>


        {{-- MAIN IMAGE --}}
        <div class="flex-1 min-w-0 flex items-start justify-center">

            <img
                id="mainImage"
                src="{{ $product1->main_image_url }}"
                class="w-full h-auto max-h-[600px] object-contain rounded-lg cursor-pointer"
                alt="{{ $product1->name }}"
            >

        </div>

    </div>

</div>