{{-- resources/views/catalog/partials/product_card.blade.php --}}
@php
    $mainImage = $product->images->firstWhere('is_main', 1) ?? $product->images->first();
    $materialNames = $product->materials
        ->map(fn($material) => $material->translations->first()?->name ?? $material->name)
        ->join(', ');
@endphp

<div class="bg-white rounded-xl shadow hover:shadow-lg transition-all duration-300 overflow-hidden group">

    {{-- Основное изображение --}}
    @if($product->slug)
        <a href="{{ route('product.show', $product->slug) }}">
            <img src="{{ $product->catalog_image_url }}"
                 class="w-full h-auto object-contain"
                 alt="{{ $product->name }}">
        </a>
    @else
        <img src="{{ $product->catalog_image_url }}"
             class="w-full h-auto object-contain"
             alt="{{ $product->name }}">
    @endif

    <div class="p-4">
        <h3 class="font-semibold text-lg mb-2">
            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-blue-600">{{ $product->name }}</a>
        </h3>

        <p class="text-gray-600 text-sm mb-2 flex flex-wrap items-center gap-1">
            @if(!empty($materialNames))
                <span>{{ $materialNames }}</span>
            @endif

            @if($product->sold_count > 0)
                <span>•</span>
                <span>Продано: {{ $product->sold_count }}</span>
            @endif
        </p>

        {{-- Variants preview --}}
        @if($product->variantGroup?->items->isNotEmpty())
            <div class="overflow-hidden h-0 group-hover:h-10 transition-[height] duration-300 mb-2">
                <div class="flex flex-wrap gap-2">
                    @foreach($product->variantGroup->items as $variant)
                        <div class="w-8 h-8 border rounded overflow-hidden">
                            <img 
                                src="{{ $variant->media?->cdn_url ?? asset('images/no-image.png') }}"
                                alt="{{ $variant->title }}"
                                class="w-full h-full object-cover"
                                title="{{ $variant->title }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex justify-between items-center">
            <span class="font-semibold text-gray-900">
                {{ price($product->max_tier_price ?? $product->price) }}
            </span>
        </div>
    </div>
</div>