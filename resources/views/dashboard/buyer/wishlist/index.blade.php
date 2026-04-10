@extends('dashboard.layout')

@section('dashboard-content')

<div class="mb-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold mt-1 text-gray-800">
                My Wishlist
            </h1>

            <p class="text-sm text-gray-500">
                Products you saved for later.
            </p>
        </div>
    </div>

</div>


@if($items->isEmpty())

<div class="flex flex-col items-center justify-center text-center py-20">

    {{-- Icon --}}
    <div class="w-20 h-20 flex items-center justify-center rounded-full bg-gray-100 mb-6">
    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-10 h-10 text-gray-400"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M4.318 6.318a4.5 4.5 0 016.364 0L12
                 7.636l1.318-1.318a4.5 4.5 0
                 116.364 6.364L12 21.682l-7.682-7.682
                 a4.5 4.5 0 010-6.364z" />
    </svg>
</div>

    {{-- Title --}}
    <h2 class="text-xl font-semibold text-gray-800 mb-2">
        Your wishlist is empty
    </h2>

    {{-- Subtitle --}}
    <p class="text-gray-500 max-w-md mb-6">
        Save products you like to your wishlist and come back later.
    </p>

    {{-- Button --}}
    <a href="{{ route('catalog.index') }}"
       class="px-6 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-700 transition">

        Browse Products

    </a>

</div>


@else


<div class="space-y-4">

@foreach($items as $product)

<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 flex flex-col md:flex-row justify-between items-start md:items-center hover:shadow-md transition">


    {{-- LEFT SIDE --}}
    <div class="flex items-start md:items-center gap-4 flex-1">

       


        {{-- Image --}}
        <div class="w-20 h-20 flex-shrink-0 rounded overflow-hidden bg-gray-100">

            <img src="{{ $product->catalog_image_url }}"
                 class="w-full h-full object-cover">

        </div>


        {{-- Product info --}}
        <div>

            <a href="{{ route('product.show', $product->slug) }}"
               target="_blank"
               class="font-semibold text-gray-900 hover:text-gray-700 transition-colors">

                {{ $product->name }}

            </a>


            <p class="text-sm text-gray-500">

                Price:

                <span class="item-price">

                    {{ price($product->max_tier_price ?? $product->price) }}

                </span>

            </p>

        </div>

    </div>



    {{-- RIGHT SIDE --}}
    <div class="flex items-center gap-4 mt-4 md:mt-0">


        {{-- Add to cart --}}
        <form method="POST"
              action="{{ route('buyer.cart.add', $product->id) }}">

            @csrf

            <button
                class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200 transition">

                Add to Cart

            </button>

        </form>



        {{-- Remove --}}
        

          

            <button
    class="wishlist-remove text-sm text-red-500 hover:underline"
    data-product-id="{{ $product->id }}">

    Remove

</button>

       


    </div>


</div>

@endforeach


</div>

@endif


<div class="mt-6">
    {{ $items->links() }}
</div>


@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.wishlist-remove').forEach(button => {

        button.addEventListener('click', async function () {

            const productId = this.dataset.productId;

            try {
                const response = await fetch(`/buyer/wishlist/toggle/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.status === 'removed') {

                    // удаляем карточку товара из UI
                    this.closest('.bg-white').remove();

                }

                if (data.status === 'removed') {

                this.closest('.bg-white').remove();

                updateWishlistBadge(); // 🔥 ВОТ ЭТО НУЖНО

                }

            } catch (error) {
                console.error('Wishlist remove error:', error);
            }

        });

    });

});
</script>