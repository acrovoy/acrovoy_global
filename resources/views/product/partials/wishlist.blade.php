{{-- Wishlist --}}

@auth

    @can('addToWishlist', $product1)

        <button
            type="button"
            class="wishlist-toggle text-gray-400 hover:text-red-500 transition"
            data-product-id="{{ $product1->id }}"
            title="Wishlist">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6 wishlist-icon transition {{ $inWishlist ? 'text-red-500' : 'text-gray-500' }}"
                viewBox="0 0 24 24"
                fill="{{ $inWishlist ? 'currentColor' : 'none' }}"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636
                       l1.318-1.318a4.5 4.5 0 016.364 6.364
                       L12 21.682l-7.682-7.682a4.5 4.5 0 010-6.364z" />

            </svg>

        </button>

    @endcan

@else

    <button
        type="button"
        onclick="dispatchAlert(
            'guest',
            'Please register or log in to add products to your wishlist.'
        )"
        class="wishlist-toggle text-gray-400 hover:text-red-500 transition"
        title="Wishlist">

        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-6 h-6 wishlist-icon transition"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636
                   l1.318-1.318a4.5 4.5 0 016.364 6.364
                   L12 21.682l-7.682-7.682a4.5 4.5 0 010-6.364z" />

        </svg>

    </button>

@endauth