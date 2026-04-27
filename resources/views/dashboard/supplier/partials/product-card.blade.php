<div class="flex items-center gap-4 max-w-full border rounded-lg p-3 bg-white shadow-sm">
    {{-- Image --}}
    @if($product->image_url)
        <a href="{{ route('product.show', $product->slug) }}" class="flex-shrink-0">
            <img 
                src="{{ $product->image_url }}" 
                alt="{{ $product->name }}"
                class="w-20 h-20 rounded-lg object-cover"
            >
        </a>
    @endif

    {{-- Text --}}
    <div class="flex flex-col">
        {{-- Product name --}}
        <a 
            href="{{ route('product.show', $product->slug) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="text-sm font-semibold text-gray-900 leading-tight hover:underline hover:text-blue-900"
        >
            {{ $product->name }}
        </a>

        {{-- Category --}}
        @if($product->category)
            <span class="text-xs text-gray-500 mt-1">
                {{ $product->category->name }}
            </span>
        @endif

        {{-- Supplier --}}
        <a 
            href="{{ route('supplier.show', $product->supplier->slug) }}"
            class="text-xs text-gray-400 mt-2 hover:text-blue-900 hover:underline"
            target="_blank"
            rel="noopener noreferrer"
        >
            by {{ $product->supplier->name }}
        </a>
    </div>
</div>
