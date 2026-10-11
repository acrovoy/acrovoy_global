
@extends('dashboard.layout')

@section('dashboard-content')

<x-alerts />

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                My Cart
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                View and manage your selected products.
            </p>
        </div>

        @if(!$cartItems->isEmpty())
            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4 text-gray-400"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.7"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span>
                    {{ $cartItems->count() }}
                    {{ $cartItems->count() === 1 ? 'item' : 'items' }}
                </span>
            </div>
        @endif
    </div>

    {{-- Empty cart --}}
    @if($cartItems->isEmpty())

        <div class="rounded-xl border border-gray-200 bg-white px-5 py-16 text-center shadow-sm sm:py-20">

            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8 text-gray-400"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.5"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
            </div>

            <h2 class="text-lg font-semibold text-gray-900">
                Your cart is empty
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                Looks like you haven't added any products yet.
                Explore our catalog to find something for your business.
            </p>

            <a href="{{ route('catalog.index') }}"
               class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                Browse Products

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>
            </a>
        </div>

    @else

        {{-- Cart items --}}
        <div class="space-y-3">

            @foreach($cartItems as $item)

                <div class="cart-item rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md sm:p-5">

                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        {{-- Product --}}
                        <div class="flex min-w-0 flex-1 items-start gap-3 sm:gap-4">

                            <div class="hidden pt-1 text-xs font-medium tabular-nums text-gray-400 sm:block">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.
                            </div>

                            <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-100 bg-gray-50 sm:h-24 sm:w-24">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}"
                                         alt="{{ $item->product?->name }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="h-full w-full object-cover">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-8 w-8 text-gray-300"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="1.5"
                                         stroke-linecap="round"
                                         stroke-linejoin="round"
                                         aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <path d="m21 15-5-5L5 21"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1 pt-0.5">

                                <a href="{{ route('product.show', $item->product?->slug) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="break-words text-sm font-semibold leading-5 text-gray-900 transition-colors hover:text-gray-600 focus:outline-none focus:underline sm:text-base">
                                    {{ $item->product?->name }}
                                </a>

                                <p class="mt-2 text-xs text-gray-500 sm:text-sm">
                                    Price:
                                    <span class="item-price ml-1 font-medium tabular-nums text-gray-700">
                                        ${{ number_format($item->price, 2) }}
                                    </span>
                                </p>

                                @if(($item->product->moq ?? 1) > 1)
                                    <p class="mt-1 text-xs text-gray-400">
                                        Minimum order: {{ $item->product->moq }} units
                                    </p>
                                @endif

                            </div>
                        </div>

                        {{-- Quantity and item total --}}
                        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3 border-t border-gray-100 pt-4 sm:justify-start lg:border-0 lg:pt-0">

                            <div class="flex flex-col gap-1.5">
                                <span class="text-xs font-medium text-gray-500">
                                    Quantity
                                </span>

                                <form action="{{ route('buyer.cart.update', $item->id) }}"
                                      method="POST"
                                      class="cart-update-form inline-flex w-fit items-center rounded-lg border border-gray-200 bg-white"
                                      data-moq="{{ $item->product->moq ?? 1 }}">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                            name="action"
                                            value="decrease"
                                            aria-label="Decrease quantity"
                                            class="quantity-button flex h-10 w-10 items-center justify-center rounded-l-lg text-lg text-gray-600 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-400 disabled:cursor-not-allowed disabled:opacity-40">
                                        <span aria-hidden="true">−</span>
                                    </button>

                                    <span class="quantity-value flex h-10 min-w-10 items-center justify-center border-x border-gray-200 px-2 text-sm font-semibold tabular-nums text-gray-900">
                                        {{ $item->quantity }}
                                    </span>

                                    <button type="submit"
                                            name="action"
                                            value="increase"
                                            aria-label="Increase quantity"
                                            class="quantity-button flex h-10 w-10 items-center justify-center rounded-r-lg text-lg text-gray-600 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-400">
                                        <span aria-hidden="true">+</span>
                                    </button>

                                </form>
                            </div>

                            <div class="ml-auto min-w-24 text-right sm:ml-4 lg:ml-6">

                                <p class="text-xs text-gray-500">
                                    Item total
                                </p>

                                <p class="item-total mt-1 text-base font-semibold tabular-nums text-gray-900">
                                    ${{ number_format($item->price * $item->quantity, 2) }}
                                </p>

                                <form action="{{ route('buyer.cart.remove', $item->id) }}"
                                      method="POST"
                                      class="mt-1"
                                      onsubmit="return confirm('Remove this product from your cart?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="inline-flex min-h-7 items-center text-xs font-medium text-gray-500 underline-offset-4 transition hover:text-red-600 hover:underline focus:outline-none focus:underline">
                                        Remove
                                    </button>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            @endforeach

        </div>

        {{-- Cart total --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        Order summary
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $cartItems->count() }}
                        {{ $cartItems->count() === 1 ? 'product' : 'products' }} in your cart
                    </p>
                </div>

                <div class="flex flex-col gap-1 sm:items-end">
                    <span class="text-sm text-gray-500">
                        Cart total
                    </span>

                    <span id="cart-total"
                          class="text-2xl font-semibold tracking-tight tabular-nums text-gray-900">
                        ${{ number_format($total, 2) }}
                    </span>
                </div>

            </div>

            <div class="mt-5 border-t border-gray-100 pt-5">

                <a href="{{ route('buyer.orders.checkout') }}"
                   class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 sm:w-auto sm:min-w-52">
                    Proceed to Checkout

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>

            </div>
        </div>

    @endif

</div>

<script>
function getMoq(form) {
    return parseInt(form.dataset.moq || 1);
}

document.addEventListener('DOMContentLoaded', function () {

    const forms = document.querySelectorAll('form[action*="cart/update"]');

    forms.forEach(function(form) {

        const quantityEl = form.querySelector('.quantity-value');
        const minusBtn = form.querySelector('button[value="decrease"]');
        const moq = getMoq(form);

        function updateState() {
            const qty = parseInt(quantityEl.textContent);

            minusBtn.disabled = qty <= moq;
            minusBtn.classList.toggle('opacity-40', qty <= moq);
            minusBtn.classList.toggle('cursor-not-allowed', qty <= moq);
        }

        updateState();

        form.addEventListener('submit', async function(e) {

            e.preventDefault();

            const button = e.submitter;
            if (!button) return;

            const action = button.value;
            const currentQty = parseInt(quantityEl.textContent);

            // Keep the existing client-side MOQ protection.
            if (action === 'decrease' && currentQty <= moq) {
                return;
            }

            // Prevent duplicate submissions while the request is running.
            if (form.dataset.busy === 'true') {
                return;
            }

            form.dataset.busy = 'true';

            const buttons = form.querySelectorAll('button[type="submit"]');
            buttons.forEach(function (btn) {
                btn.disabled = true;
            });

            try {

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');

                const headers = {
                    'Accept': 'application/json'
                };

                if (csrfMeta) {
                    headers['X-CSRF-TOKEN'] = csrfMeta.content;
                }

                const response = await fetch(form.getAttribute('action'), {

                    method: 'POST',

                    headers: headers,

                    body: new URLSearchParams({
                        _token: form.querySelector('input[name="_token"]')?.value || '',
                        _method: 'PATCH',
                        action: action
                    })

                });

                if (!response.ok) {
                    console.error('Server error', response.status);
                    return;
                }

                const data = await response.json();

                if (!data.quantity) {
                    console.error('Invalid JSON', data);
                    return;
                }

                // Update quantity.
                quantityEl.textContent = data.quantity;

                // Update item total.
                form.closest('.cart-item')
                    .querySelector('.item-total')
                    .textContent = '$' + data.itemTotal;

                // Update unit price if it changed.
                const itemPrice = form.closest('.cart-item').querySelector('.item-price');

                if (itemPrice) {
                    if (itemPrice.textContent !== '$' + data.price) {
                        itemPrice.textContent = '$' + data.price;
                        itemPrice.classList.add('price-flash');

                        setTimeout(() => {
                            itemPrice.classList.remove('price-flash');
                        }, 500);
                    }
                }

                // Update cart total.
                const cartTotal = document.querySelector('#cart-total');

                if (cartTotal) {
                    cartTotal.textContent = '$' + data.cartTotal;
                }

                // Update MOQ button state.
                updateState();

            } catch(error) {

                console.error('Fetch failed:', error);

            } finally {

                delete form.dataset.busy;

                buttons.forEach(function (btn) {
                    btn.disabled = false;
                });

                updateState();
            }

        });

    });

});
</script>

<style>
.price-flash {
    color: #ff0000 !important;
    font-weight: 700;
    transition: color 0.5s ease;
}

@media (prefers-reduced-motion: reduce) {
    .price-flash {
        transition: none;
    }
}
</style>

@endsection
