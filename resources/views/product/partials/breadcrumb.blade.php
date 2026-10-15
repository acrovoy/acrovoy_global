{{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-4 flex flex-wrap gap-1">
            <a href="{{ route('catalog.index') }}" class="hover:text-black">{{ __('product/product_show.root') }}</a> /
            <a href="{{ route('catalog.index', $product1->category->slug) }}" class="hover:text-black">
                {{ $product1->category->name ?? 'Category' }}
            </a> /
            <span class="text-gray-900">{{ $product1->name }}</span>


           


        </div>