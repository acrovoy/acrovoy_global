{{-- Product Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3  xl:grid-cols-4 gap-8">
    @if($products->count())
    @foreach ($products as $product)

    @php
    $mainImage = $product->images->firstWhere('is_main', 1) ?? $product->images->first();
    $materialNames = $product->materials
    ->map(fn($material) => $material->translations->first()?->name ?? $material->name)
    ->join(', ');
    $rating = number_format((float)$product->reviews_avg_rating ?? 0, 1, '.', '');
    $reviewsProductsCount = $product->reviews_count ?? 0;
    $soldCount = $product->sold_count ?? 0;
    @endphp

    <div class="relative group sm:h-[420px]">

        <div class="bg-white rounded-xl shadow hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col sm:group-hover:position-absolute product-card--inner top-0 left-0 group-hover:w-full min-h-full">

            {{-- Основное изображение каталога --}}
            @if($product->slug)
            <a href="{{ route('product.show', $product->slug) }}">
                <img loading="lazy" src="{{ $product->catalog_image_url }}" class="w-full h-auto object-contain" alt="{{ $product->name }}">
            </a>
            @else
            <img src="{{ $product->catalog_image_url }}" class="w-full h-auto object-contain" alt="{{ $product->name }}">
            @endif

            <div class="p-4 flex flex-col flex-1 z-10 bg-white">
                <h3 class="font-semibold text-base">
                    <a href="{{ route('product.show', $product->slug) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                </h3>

                @if($reviewsProductsCount > 0 || $soldCount > 0)
                <div class="flex items-center gap-2 mb-1 mt-1 text-xs">
                    @if($reviewsProductsCount > 0)
                    <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="url(#gold-gradient)" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="gold-gradient" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#FFD700" />
                                <stop offset="100%" stop-color="#FFC107" />
                            </linearGradient>
                        </defs>
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z" />
                    </svg>
                    <span class="font-semibold text-gray-500">{{ $rating }}</span>
                    <span class="text-gray-500">({{ $reviewsProductsCount }} review{{ $reviewsProductsCount != 1 ? 's' : '' }})</span>
                    @endif
                    @if($soldCount > 0)
                    <span class="text-gray-500">Sold: {{ $soldCount }}</span>
                    @endif
                </div>
                @endif

                <p class="text-gray-600 text-sm mb-2 flex flex-wrap items-center gap-1">
                    @if(!empty($materialNames))
                    <span class="text-gray-500 text-xs">{{ $materialNames }}</span>
                    @endif
                </p>

                {{-- Всплывающий блок при наведении --}}
                <div class="overflow-hidden sm:max-h-0 sm:group-hover:max-h-60 transition-[max-height] duration-300 mb-4 space-y-1">
                    @if($product->variantGroup?->items->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-2">
                        @foreach($product->variantGroup->items as $variant)
                        <div class="w-8 h-8 border rounded overflow-hidden">
                            <img src="{{ $variant->media?->cdn_url ?? asset('images/no-image.png') }}"
                                alt="{{ $variant->title }}"
                                class="w-full h-full object-cover"
                                title="{{ $variant->title }}">
                        </div>
                        @endforeach
                    </div>
                    @endif


                    <div class="bg-gray-50 rounded-lg p-2 text-xs">
                        @foreach($product->priceTiers as $tier)
                        <div class="flex justify-between text-gray-700 py-1 border-b last:border-b-0">
                            <span>{{ $tier->min_qty }} - {{ $tier->max_qty ?? '∞' }} pcs</span>
                            <span class="font-semibold text-blue-gray">{{ number_format($tier->price, 2) }} ₴</span>
                        </div>
                        @endforeach
                    </div>



                    @if($product->moq)
                    <span class="text-xs text-gray-500 block pb-2">Min order quantity: {{ $product->moq }}</span>
                    @endif
                    @if($product->supplier)
                    <p class="text-xs text-gray-400 mb-0 mt-6">
                        <span class="text-[10px]">SUPPLIER:</span>
                        <a href="{{ route('supplier.show', $product->supplier->slug) }}" class="text-xs text-gray-500 hover:text-blue-600">
                            {{ $product->supplier->name }}
                        </a>

                        @if($product->supplier->level === 'Platinum')
                        <span class="px-1 py-0.5 text-[7px] font-bold uppercase  bg-gray-900 text-white rounded ml-1">
                            PLATINUM
                        </span>
                        @elseif($product->supplier->level === 'Gold')
                        <span class="px-1 py-0.5 text-[7px] font-bold uppercase  bg-amber-100 text-amber-700 border border-amber-200 rounded ml-1">
                            GOLD
                        </span>
                        @elseif($product->supplier->level === 'Silver')
                        <span class="px-1 py-0.5 text-[7px] font-bold uppercase  bg-gray-100 text-gray-700 border border-gray-200 rounded ml-1">
                            SILVER
                        </span>
                        @endif

                    </p>

                    <div class="flex gap-1 mb-3">


                        @if($product->supplier->is_verified)
                        <span class="px-1 py-0.5 text-[7px] font-semibold bg-blue-100 text-blue-700 rounded">
                            VERIFIED
                        </span>
                        @endif
                        @if($product->supplier->is_trusted)
                        <span class="px-1 py-0.5 text-[7px] font-semibold bg-green-100 text-green-700 rounded">
                            TRUSTED
                        </span>
                        @endif
                        @if($product->supplier->is_premium)
                        <span class="px-1 py-0.5 text-[7px] font-semibold bg-purple-100 text-purple-700 rounded">
                            PREMIUM
                        </span>
                        @endif
                    </div>







                    @endif
                </div>

                <div class="flex justify-between items-center gap-2 mt-auto">
                    <div class="flex justify-between items-center gap-2">
                        <!-- Кнопка Add to Cart -->
                        <form method="POST" action="{{ route('buyer.cart.add', $product->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full border border-gray-300 p-1.5 rounded-sm
                                    text-gray-800 font-medium shadow-sm
                                    hover:border-black hover:text-black hover:shadow-md
                                    transition-all transform hover:scale-105 text-sm"
                                title="Add to Cart">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13L17 13M7 13H5.4" />
                                </svg>
                            </button>
                        </form>

                        <!-- Кнопка Add to Wishlist -->
                        <button
                            class="w-full border border-gray-300 p-1.5 rounded-sm
                   text-gray-800 font-medium shadow-sm
                   hover:border-black hover:text-black hover:shadow-md transition-all transform hover:scale-105 text-sm"
                            title="Add to Wishlist">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-3 w-3"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.682l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Цена -->
                    <span class="font-semibold text-gray-900">{{ price($product->max_tier_price ?? $product->price) }}</span>
                </div>




            </div>
        </div>

    </div>

    @endforeach
    @else
    <div class="col-span-full flex flex-col items-center justify-center py-20">
        <h2 class="text-2xl md:text-3xl font-bold text-brown-900 mb-2 text-center">
            No products found.
        </h2>
        <p class="text-gray-600 text-center max-w-md">
            Currently there are no products available in this category. Please check other categories or try again later.
        </p>
    </div>
    @endif
</div>