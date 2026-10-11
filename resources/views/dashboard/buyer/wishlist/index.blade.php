
@extends('dashboard.layout')

@section('dashboard-content')

@php
    $hasItems = $items->isNotEmpty();
@endphp

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">
                My Wishlist
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Products you have saved to review, compare or purchase later.
            </p>
        </div>

        <a
            href="{{ route('catalog.index') }}"
            class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg
                   border border-gray-200 bg-white px-4 py-2 text-sm font-medium
                   text-gray-700 transition hover:bg-gray-50
                   focus:outline-none focus:ring-2 focus:ring-gray-300"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 5v14m-7-7h14" />
            </svg>

            Browse Products
        </a>
    </div>

    {{-- Status / error messages --}}
    <div
        id="wishlist-message"
        class="hidden rounded-lg border px-4 py-3 text-sm"
        role="status"
        aria-live="polite"
    ></div>

    @if ($hasItems)

        {{-- Wishlist summary --}}
        <div class="flex flex-col gap-1 border-b border-gray-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-600">
                Saved products
                <span class="font-semibold text-gray-900">
                    {{ $items->total() }}
                </span>
            </p>

            <p class="text-xs text-gray-500">
                Your saved products are available here whenever you need them.
            </p>
        </div>

        {{-- Product list --}}
        <div id="wishlist-list" class="space-y-4">

            @foreach ($items as $item)

                @php
                    $product = $item->product;
                @endphp

                @continue(!$product)

                <article
                    class="wishlist-card rounded-xl border border-gray-200 bg-white p-4 shadow-sm
                           transition duration-200 hover:border-gray-300 hover:shadow-md
                           sm:p-5"
                    data-product-id="{{ $product->getKey() }}"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                        {{-- Product image --}}
                        <a
                            href="{{ route('product.show', $product->slug) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-24 w-full flex-shrink-0 items-center justify-center
                                   overflow-hidden rounded-lg bg-gray-50 sm:h-24 sm:w-28"
                            aria-label="View {{ $product->name }}"
                        >
                            @if ($product->catalog_image_url)
                                <img
                                    src="{{ $product->catalog_image_url }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                    onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.classList.remove('hidden');"
                                >

                                <div class="hidden h-full w-full items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-9 w-9"
                                         viewBox="0 0 24 24"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="1.3"
                                         aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m21 15-5-5L5 21" />
                                    </svg>
                                </div>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-9 w-9 text-gray-300"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.3"
                                     aria-hidden="true">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m21 15-5-5L5 21" />
                                </svg>
                            @endif
                        </a>

                        {{-- Product information --}}
                        <div class="min-w-0 flex-1">
                            <a
                                href="{{ route('product.show', $product->slug) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-block break-words font-semibold text-gray-900
                                       transition hover:text-gray-600
                                       focus:outline-none focus:underline"
                            >
                                {{ $product->name }}
                            </a>

                            <div class="mt-2 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                <span class="text-xs text-gray-500">
                                    Price
                                </span>

                                <span class="item-price text-sm font-semibold text-gray-900">
                                    {{ price($product->max_tier_price ?? $product->price) }}
                                </span>
                            </div>

                            <p class="mt-2 text-xs text-gray-400">
                                Saved {{ $item->created_at?->format('M j, Y') ?? '' }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-2 sm:justify-end">

                            {{-- View product --}}
                            <a
                                href="{{ route('product.show', $product->slug) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex min-h-10 items-center justify-center rounded-lg
                                       border border-gray-200 bg-white px-3 py-2 text-sm
                                       font-medium text-gray-700 transition hover:bg-gray-50
                                       focus:outline-none focus:ring-2 focus:ring-gray-300"
                            >
                                View Product
                            </a>

                            {{-- Add to cart --}}
                            <form
                                method="POST"
                                action="{{ route('buyer.cart.add', $product->getKey()) }}"
                                class="wishlist-cart-form"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex min-h-10 items-center justify-center rounded-lg
                                           bg-gray-900 px-3 py-2 text-sm font-medium text-white
                                           transition hover:bg-gray-800
                                           focus:outline-none focus:ring-2 focus:ring-gray-400
                                           focus:ring-offset-2"
                                >
                                    Add to Cart
                                </button>
                            </form>

                            {{-- Remove from wishlist --}}
                            <button
                                type="button"
                                class="wishlist-remove inline-flex min-h-10 items-center
                                       justify-center rounded-lg px-3 py-2 text-sm font-medium
                                       text-gray-500 transition hover:bg-red-50 hover:text-red-600
                                       focus:outline-none focus:ring-2 focus:ring-red-200
                                       disabled:cursor-not-allowed disabled:opacity-50"
                                data-url="{{ route('buyer.wishlist.destroy', $product) }}"
                                data-csrf="{{ csrf_token() }}"
                                aria-label="Remove {{ $product->name }} from wishlist"
                            >
                                Remove
                            </button>

                        </div>
                    </div>
                </article>

            @endforeach

        </div>

        {{-- Pagination --}}
        @if ($items->hasPages())
            <div class="border-t border-gray-200 pt-5">
                {{ $items->links() }}
            </div>
        @endif

    @else

        {{-- Empty state --}}
        <div class="rounded-xl border border-gray-200 bg-white px-5 py-16 text-center sm:py-20">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8 text-gray-400"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.5"
                     aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78Z" />
                </svg>
            </div>

            <h2 class="mt-5 text-lg font-semibold text-gray-900">
                Your wishlist is empty
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                Save products you like and return to them whenever you're ready
                to compare options or plan your next purchase.
            </p>

            <a
                href="{{ route('catalog.index') }}"
                class="mt-6 inline-flex min-h-11 items-center justify-center rounded-lg
                       bg-gray-900 px-5 py-3 text-sm font-semibold text-white
                       transition hover:bg-gray-800
                       focus:outline-none focus:ring-2 focus:ring-gray-400
                       focus:ring-offset-2"
            >
                Explore Products
            </a>

        </div>

    @endif

    {{-- Future functionality placeholder: projects and supplier quotes --}}
    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 sm:p-5">
        <div class="flex items-start gap-3">
            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-white text-gray-500 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.6"
                     aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 7.5A2.5 2.5 0 0 1 5.5 5h4l2 2h7A2.5 2.5 0 0 1 21 9.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 18.5z" />
                </svg>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-800">
                    Plan your next project
                </h3>

                <p class="mt-1 text-sm leading-5 text-gray-500">
                    Project specifications, saved supplier lists and quote requests
                    can be connected to your wishlist as these features become available.
                </p>

                <span class="mt-2 inline-flex rounded-md bg-white px-2 py-1 text-xs font-medium text-gray-500 ring-1 ring-inset ring-gray-200">
                    Future enhancement
                </span>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('wishlist-list');
    const messageBox = document.getElementById('wishlist-message');

    function showMessage(message, type = 'error') {
        if (!messageBox) return;

        messageBox.textContent = message;
        messageBox.classList.remove(
            'hidden',
            'border-red-200',
            'bg-red-50',
            'text-red-700',
            'border-green-200',
            'bg-green-50',
            'text-green-700'
        );

        if (type === 'success') {
            messageBox.classList.add(
                'border-green-200',
                'bg-green-50',
                'text-green-700'
            );
        } else {
            messageBox.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );
        }
    }

    document.querySelectorAll('.wishlist-remove').forEach(function (button) {
        button.addEventListener('click', async function () {
            if (button.disabled) return;

            const card = button.closest('.wishlist-card');

            if (!card) return;

            const confirmed = window.confirm(
                'Remove this product from your wishlist?'
            );

            if (!confirmed) return;

            const originalText = button.textContent.trim();

            button.disabled = true;
            button.textContent = 'Removing…';

            try {
                const csrfToken = button.dataset.csrf ||
                    document.querySelector('meta[name="csrf-token"]')?.content;

                if (!csrfToken) {
                    throw new Error(
                        'Security token is missing. Refresh the page and try again.'
                    );
                }

                const response = await fetch(button.dataset.url, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json().catch(function () {
                    return {};
                });

                if (!response.ok || data.status !== 'removed') {
                    if (response.status === 401 || response.status === 419) {
                        throw new Error(
                            'Your session may have expired. Refresh the page and try again.'
                        );
                    }

                    if (response.status === 403) {
                        throw new Error(
                            'You do not have permission to modify this wishlist.'
                        );
                    }

                    throw new Error(
                        data.message || 'Unable to remove this product. Please try again.'
                    );
                }

                card.remove();

                if (typeof window.updateWishlistBadge === 'function') {
                    try {
                        await window.updateWishlistBadge();
                    } catch (badgeError) {
                        console.error('Wishlist badge update failed:', badgeError);
                    }
                }

                const remainingCards = list
                    ? list.querySelectorAll('.wishlist-card').length
                    : 0;

                if (remainingCards === 0) {
                    /*
                     * Reload so Laravel can render the correct empty state.
                     * This also handles the last item on a paginated page.
                     */
                    window.location.reload();
                    return;
                }

                showMessage(
                    data.message || 'Product removed from your wishlist.',
                    'success'
                );

            } catch (error) {
                console.error('Wishlist remove error:', error);

                showMessage(
                    error.message || 'Something went wrong. Please try again.'
                );

                button.disabled = false;
                button.textContent = originalText;
            }
        });
    });
});
</script>
@endpush
