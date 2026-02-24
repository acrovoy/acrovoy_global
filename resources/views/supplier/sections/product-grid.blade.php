<div class="w-full lg:w-3/4">
                @if($products->isEmpty())
                    <div class="w-full text-center py-16">
                        <h2 class="text-2xl md:text-3xl font-bold text-brown-900 mb-2">
                            No products found.
                        </h2>

                        <p class="text-gray-600 max-w-md mx-auto">
                            Currently there are no products available under this filter(s).
                            Please check other categories & filters or try again later.
                        </p>
                    </div>
                @else


                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-start" id="product-list">
                                
                                @foreach($products as $product)
                                    {{-- === Ваша карточка продукта === --}}
                                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                                        <a href="{{ url('/product/' . $product->slug) }}" class="block overflow-hidden rounded-t-lg">
                                            <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('images/no-image.png') }}"
                                                class="w-full aspect-square object-cover"
                                                alt="{{ $product->name }}">
                                        </a>

                                        <div class="p-3 flex flex-col h-full">
                                            <h3 class="font-semibold text-base mb-1">
                                                <a href="{{ url('/product/' . $product->slug) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                                            </h3>

                                            @php
                                                $rating = $product->reviews()->avg('rating') ?? 0;
                                                $rating = number_format((float)$rating, 1, '.', '');
                                                $reviewsProductsCount = $product->reviews()->count();
                                                $soldCount = $product->sold_count ?? 0;
                                            @endphp

                                            <div class="flex items-center gap-2 mb-2 text-xs">
                                                <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="url(#gold-gradient)" xmlns="http://www.w3.org/2000/svg">
                                                    <defs>
                                                        <linearGradient id="gold-gradient" x1="0" y1="0" x2="1" y2="1">
                                                            <stop offset="0%" stop-color="#FFD700"/>
                                                            <stop offset="100%" stop-color="#FFC107"/>
                                                        </linearGradient>
                                                    </defs>
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/>
                                                </svg>
                                                <span class="font-semibold text-gray-500">{{ $rating }}</span>
                                                <span class="text-gray-500">({{ $reviewsProductsCount }} review{{ $reviewsProductsCount != 1 ? 's' : '' }})</span>
                                                @if($soldCount > 0)
                                                    <span class="before:content-['•'] before:mx-1"></span>
                                                    <span>Продано: {{ $soldCount }}</span>
                                                @endif
                                            </div>

                                            <p class="text-gray-600 text-xs mb-1">
                                                @php
                                                    $materialNames = $product->materials->map(function($material) {
                                                        $translation = $material->translations->firstWhere('locale', app()->getLocale()) ?? $material->translations->firstWhere('locale','en');
                                                        return $translation ? $translation->name : $material->name;
                                                    })->implode(', ');
                                                @endphp
                                                {{ $materialNames }}
                                            </p>

                                            <div class="bg-gray-50 rounded-lg p-2 mb-2 text-xs">
                                                @foreach($product->priceTiers as $tier)
                                                    <div class="flex justify-between text-gray-700 py-1 border-b last:border-b-0">
                                                        <span>{{ $tier->min_qty }} - {{ $tier->max_qty ?? '∞' }} pcs</span>
                                                        <span class="font-semibold text-blue-gray">{{ number_format($tier->price, 2) }} ₴</span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="mt-auto pt-2">
                                                <button class="w-full border border-gray-300 py-1.5 rounded-xl
                                                            text-gray-800 font-medium shadow-sm
                                                            hover:border-black hover:text-black hover:shadow-md transition-all transform hover:scale-105 text-sm">
                                                    Add to Cart
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- === Конец карточки === --}}
                                @endforeach
                            </div>


                @endif




            </div>

    </div>
    <div class="mt-6">
        {{ $products->links() }}
    </div>