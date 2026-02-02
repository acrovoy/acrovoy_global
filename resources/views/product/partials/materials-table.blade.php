{{-- Materials --}}
                @if($product1->materials->isNotEmpty())
                <div class="mb-8 flex items-start gap-3">

                    <span class="text-sm text-gray-500 pt-1 whitespace-nowrap">
                        {{ __('product/product_show.materials_used') }}
                    </span>

                    <div class="flex flex-wrap gap-2">
                        @foreach($product1->materials as $material)
                        <span class="inline-flex items-center
                         px-3 py-1.5 rounded-md
                         bg-gray-300/70
                         text-gray-800 text-sm leading-none">
                            {{ $material->name }}
                        </span>
                        @endforeach
                    </div>

                </div>
                @endif