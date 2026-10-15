
{{-- Compact product catalog overview --}}
<div class="mb-3 grid grid-cols-2 gap-2 xl:grid-cols-4">

    
{{-- Added --}}
<div class="flex min-w-0 items-center justify-between gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2">
    <div class="min-w-0">
        <div class="flex items-center gap-1.5">
            <p class="text-xs text-stone-500">Added</p>

            {{-- Plan limit tooltip --}}
            <span class="group relative inline-flex">
                <button
                    type="button"
                    aria-label="Product limit information"
                    aria-describedby="product-limit-tooltip"
                    class="flex h-3.5 w-3.5 pointer-events-none items-center justify-center rounded-full border border-stone-400 text-[9px] font-semibold leading-none text-stone-500 transition hover:border-stone-700 hover:text-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300"
                >
                    ?
                </button>

                <span
    id="product-limit-tooltip"
    role="tooltip"
    class="invisible pointer-events-none absolute left-0 top-full z-50 mt-2 w-56 rounded-lg bg-stone-800 px-3 py-2 text-xs font-normal leading-5 text-white opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100"
>
    You are currently on the Free plan. You can add up to 10 products. Upgrade your plan to increase your product limit.
</span>
            </span>
        </div>

        <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
            {{ number_format($addedCount) }}
            <span class="text-xs font-normal text-stone-400">
                / {{ number_format($productLimit) }}
            </span>
        </p>
    </div>

    <span class="h-2 w-2 shrink-0 rounded-full bg-stone-400"></span>
</div>


    {{-- Published --}}
    <div class="flex min-w-0 items-center justify-between gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2">
        <div class="min-w-0">
            <p class="text-xs text-stone-500">Published</p>
            <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                {{ number_format($publishedCount) }}
                <span class="text-xs font-normal text-stone-400">
                    / {{ number_format($addedCount) }}
                </span>
            </p>
        </div>
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
    </div>

    {{-- In catalog --}}
    <div class="flex min-w-0 items-center justify-between gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2">
        <div class="min-w-0">
            <p class="text-xs text-stone-500">In catalog</p>
            <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                {{ number_format($inCatalogCount) }}
                <span class="text-xs font-normal text-stone-400">
                    / {{ number_format($addedCount) }}
                </span>
            </p>
        </div>
        <span class="h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
    </div>

    {{-- Not in catalog --}}
    <div class="flex min-w-0 items-center justify-between gap-2 rounded-lg border border-stone-200 bg-white px-3 py-2">
        <div class="min-w-0">
            <p class="text-xs text-stone-500">Not in catalog</p>
            <p class="mt-0.5 text-lg font-semibold leading-5 text-stone-900">
                {{ number_format($notInCatalogCount) }}
                <span class="text-xs font-normal text-stone-400">
                    / {{ number_format($addedCount) }}
                </span>
            </p>
        </div>
        <span class="h-2 w-2 shrink-0 rounded-full bg-amber-500"></span>
    </div>

</div>



<form
    action="{{ route('supplier.products.index') }}"
    method="GET"
    class="mb-5 overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm"
>
    {{-- Compact filter header --}}
    <div class="flex items-center justify-between gap-3 bg-stone-50/70 px-4 py-3">
        <div class="flex min-w-0 items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 5h16M7 12h10m-7 7h4"
                    />
                </svg>
            </div>

            <div class="min-w-0">
                <h2 class="text-sm font-semibold text-stone-800">
                    Filter products
                </h2>

                <p class="mt-0.5 truncate text-xs text-stone-500">
                    Search and organize your product catalog
                </p>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <a
                href="{{ route('supplier.products.index') }}"
                class="whitespace-nowrap rounded-lg px-3 py-2 text-xs font-medium text-stone-600 transition hover:bg-white hover:text-stone-900"
            >
                Reset filters
            </a>

            <button
                type="button"
                id="toggle-product-filters"
                aria-expanded="false"
                aria-controls="product-filters-panel"
                aria-label="Expand product filters"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600 transition hover:border-stone-300 hover:bg-stone-100 hover:text-stone-900 focus:outline-none focus:ring-2 focus:ring-stone-300"
            >
                <svg
                    id="product-filters-chevron"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 transition-transform duration-200"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m6 9 6 6 6-6"
                    />
                </svg>
            </button>
        </div>
    </div>

    {{-- Collapsible filter fields; hidden by default --}}
    <div id="product-filters-panel" hidden>
        <div class="grid grid-cols-1 gap-4 border-t border-stone-200 p-5 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Search by product name or SKU --}}
            <div>
                <label
                    for="filter-search"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600"
                >
                    Product name or SKU
                </label>

                <div class="relative">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                    >
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="m16 16 4 4" />
                    </svg>

                    <input
                        id="filter-search"
                        type="search"
                        name="search"
                        value="{{ $search ?? request('search') }}"
                        placeholder="Name or SKU..."
                        maxlength="150"
                        class="w-full rounded-lg border border-stone-300 bg-white py-2.5 pl-9 pr-3 text-sm text-stone-800 placeholder:text-stone-400 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                    >
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label
                    for="filter-status"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600"
                >
                    Status
                </label>

                <select
                    id="filter-status"
                    name="status"
                    class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                >
                    <option value="">All statuses</option>

                    <option value="draft" @selected(($status ?? '') === 'draft')>
                        Draft
                    </option>

                    <option value="pending" @selected(($status ?? '') === 'pending')>
                        On moderation
                    </option>

                    <option value="approved" @selected(($status ?? '') === 'approved')>
                        Published
                    </option>

                    <option value="rejected" @selected(($status ?? '') === 'rejected')>
                        Rejected
                    </option>
                </select>
            </div>

            {{-- Category --}}
            <div>
                <label
                    for="filter-category"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600"
                >
                    Category
                </label>

                <select
                    id="filter-category"
                    name="category"
                    class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                >
                    <option value="">All categories</option>

                    @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected((string) ($categoryFilter ?? '') === (string) $category->id)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sort --}}
            <div>
                <label
                    for="filter-sort"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600"
                >
                    Sort by
                </label>

                <select
                    id="filter-sort"
                    name="sort"
                    class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                >
                    <option value="newest" @selected(($sort ?? 'newest') === 'newest')>
                        Newest first
                    </option>

                    <option value="oldest" @selected(($sort ?? '') === 'oldest')>
                        Oldest first
                    </option>

                    <option value="name_asc" @selected(($sort ?? '') === 'name_asc')>
                        Name: A–Z
                    </option>

                    <option value="name_desc" @selected(($sort ?? '') === 'name_desc')>
                        Name: Z–A
                    </option>

                    <option value="status" @selected(($sort ?? '') === 'status')>
                        By status
                    </option>
                </select>
            </div>

            {{-- Creator --}}
            <div class="sm:col-span-2 xl:col-span-4">
                <label
                    for="filter-user"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-stone-600"
                >
                    Created by
                </label>

                <input
                    id="filter-user"
                    type="text"
                    name="user"
                    value="{{ $userFilter ?? request('user') }}"
                    placeholder="Enter creator name..."
                    maxlength="150"
                    class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-800 placeholder:text-stone-400 focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200 sm:max-w-sm"
                >
            </div>
        </div>

        {{-- Filter actions --}}
        <div class="flex justify-end border-t border-stone-200 bg-stone-50/50 px-5 py-3">
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-stone-800 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m16 16 4 4" />
                </svg>

                Apply filters
            </button>
        </div>
    </div>
</form>


<div class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">

    <div class="flex flex-col gap-3 border-b border-stone-200 bg-stone-50/70 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-600">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                >
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 6h13M8 12h13M8 18h13M3.5 6h.01M3.5 12h.01M3.5 18h.01" />
                </svg>
            </div>

            <div>
                
                <p class="mt-0.5 text-xs text-stone-500">
                    <span id="selected-products-count" class="font-medium text-stone-700">0</span>
                    products selected
                </p>
            </div>
        </div>

        <button
            type="button"
            id="bulk-archive-products"
            data-url="{{ route('supplier.products.bulk-archive') }}"
            disabled
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 shadow-sm transition duration-150 hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-stone-300 focus:ring-offset-2 disabled:cursor-not-allowed disabled:border-stone-200 disabled:bg-stone-100 disabled:text-stone-400 disabled:shadow-none"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.7"
            >
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3" />
            </svg>
            Delete selected
        </button>
    </div>

    <table class="w-full border-collapse text-sm">

        <thead class="bg-gray-50 border-b">
            <tr>
                
                <th class="px-4 py-2 text-left font-medium">Product</th>
                <th class="px-4 py-2 text-left font-medium">SKU</th>
                <th class="px-4 py-2 text-left font-medium">Price</th>
                <th class="px-4 py-2 text-left font-medium">Stock</th>
                <th class="px-4 py-2 text-left font-medium">Status</th>
                <th class="px-4 py-2 text-right font-medium">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
            @forelse ($products as $product)

            @php
                $image = $product->images?->where('is_main', 1)->first()?->cdn_url 
                    ?? $product->images?->first()?->cdn_url;
            @endphp

                <tr class="hover:bg-gray-50 transition">
                   

                   

<td class="w-[380px] max-w-[380px] px-4 py-2">
    <div class="flex w-full max-w-[360px] min-w-0 items-start gap-3">
        @if (
            $product->status !== 'pending'
            && !$product->editingCopy
        )
            <input
                type="checkbox"
                class="product-select-checkbox mt-1 h-4 w-4 shrink-0 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                value="{{ $product->id }}"
                data-name="{{ $product->name }}"
                aria-label="Select {{ $product->name }}"
            >
        @else
            <span class="w-4 shrink-0"></span>
        @endif

        <img
            src="{{ $image ?? asset('images/no-image.png') }}"
            alt="{{ $product->name }}"
            class="h-12 w-12 shrink-0 rounded object-cover"
        >

        <div class="min-w-0 flex-1">
            <span class="block whitespace-normal break-words [overflow-wrap:anywhere] font-medium leading-5 text-gray-800">
                {{ $product->name }}
            </span>

            @if ($product->category)
                <span class="mt-1 block truncate text-xs lowercase text-stone-500">
                    {{ $product->category->name }}
                </span>
            @endif
        </div>
    </div>
</td>



                    <td class="px-4 py-2 text-gray-800 text-xs">{{ $product->sku ?? '—' }}</td>

                    <td class="px-4 py-2">
                        <button class="price-button text-blue-600 hover:underline"
                                data-product-id="{{ $product->id }}"
                                data-product-price-tiers='@json($product->priceTiers ?? [])'>
                            @if($product->priceTiers->count())
                                @php $minPriceTier = $product->priceTiers->min('price'); @endphp
                                ${{ number_format($minPriceTier, 2) }}
                            @else
                                —
                            @endif
                        </button>
                    </td>

                    <td class="px-4 py-2">
                        <button class="stock-button text-blue-600 hover:underline"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-warehouses='@json($product->warehouses)'>
                            {{ $product->warehouses->sum(fn($w) => $w->pivot->quantity ?? 0) }}
                        </button>
                    </td>

                    <td class="px-4 py-2">
                        
                    </td>

                  
<td class="px-4 py-2">
    <div class="flex flex-col items-end gap-2">

    
        {{-- Status and update indicator --}}
        <div class="flex items-center justify-end gap-2">

            
@if ($product->status === 'pending' || $product->editingCopy)
    <span
        class="group relative inline-flex shrink-0 cursor-help outline-none"
        tabindex="0"
        aria-describedby="product-update-tooltip-{{ $product->id }}"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-3.5 w-3.5 animate-spin text-amber-600"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M20 7v5h-5M4 17v-5h5"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M5.5 9A7 7 0 0 1 17 5.5L20 12M4 12l3 6.5A7 7 0 0 0 18.5 15"
            />
        </svg>

        <span
            id="product-update-tooltip-{{ $product->id }}"
            role="tooltip"
            class="invisible pointer-events-none absolute bottom-full right-0 z-50 mb-2 w-52 rounded-lg bg-stone-800 px-3 py-2 text-left text-xs font-normal leading-5 text-white opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:opacity-100 group-focus:visible group-focus:opacity-100"
        >
            Product card update is pending.
        </span>
    </span>
@endif


            <div class="inline-flex items-center px-1 py-1 text-xs font-semibold
                @if ($product->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif ($product->status === 'approved') bg-green-100 text-green-800
                @elseif ($product->status === 'rejected') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-700
                @endif">

                @if ($product->status === 'pending')
                    On moderation
                @elseif ($product->status === 'approved')
                    Published
                @else
                    {{ ucfirst($product->status) }}
                @endif
            </div>
        </div>



        {{-- Creation date --}}
        <span
            class="text-[10px] lowercase tracking-wide text-stone-400"
            title="{{ $product->created_at?->format('Y-m-d H:i') }}"
        >
            created {{ $product->created_at?->format('d M Y') }}
        </span>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 whitespace-nowrap text-sm">
            <a
                href="{{ route('product.show', $product->slug) }}"
                target="_blank"
                class="text-stone-700 transition hover:text-black hover:underline"
            >
                View
            </a>

            @if ($product->status !== 'pending')

                @if (!$product->editingCopy || $product->editingCopy->status !== 'pending')
                    <a
                        href="{{ $product->status === 'approved'
                            ? route('supplier.products.edit-approved', $product->id)
                            : route('supplier.products.edit-step', [$product->id, 1]) }}"
                        class="text-blue-700 transition hover:text-blue-900 hover:underline"
                    >
                        Edit
                    </a>
                @endif

                @if (!$product->editingCopy)
                    <button
                        type="button"
                        class="delete-product text-red-700 transition hover:text-red-900 hover:underline"
                        data-id="{{ $product->id }}"
                        data-url="{{ route('supplier.products.archive', ['product' => $product->id]) }}"
                    >
                        Delete
                    </button>
                @endif

            @endif
        </div>

    </div>
</td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center text-gray-500">
                        No products found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

{{-- Stock Drawer --}}
<div id="stock-drawer-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 transition-opacity">
</div>

<div id="stock-drawer"
     class="fixed right-0 top-0 h-full w-[440px] bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300
            z-50 flex flex-col">

    {{-- Header --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">
            Stock management
        </h3>

        <p class="text-sm text-gray-500 mt-1" id="stock-product-name"></p>
    </div>

    {{-- Body --}}
    <form id="stock-form" class="flex flex-col flex-1">

        <input type="hidden" name="product_id" id="stock-product-id">

        {{-- Warehouses --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-3">

            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                Warehouses
            </div>

            <div id="warehouse-stock-list" class="space-y-3">
                {{-- JS inject --}}
            </div>
        </div>

        {{-- Footer --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between">

            <div class="text-xs text-gray-400">
                Changes apply immediately after saving
            </div>

            <div class="flex gap-2">
                <button type="button"
                        id="stock-cancel"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-200
                               text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                               hover:bg-gray-800 transition shadow-sm">
                    Save changes
                </button>
            </div>
        </div>

    </form>
</div>

{{-- Price Drawer --}}
<div id="price-drawer-overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 transition-opacity">
</div>

<div id="price-drawer"
     class="fixed right-0 top-0 h-full w-[460px] bg-white shadow-2xl
            transform translate-x-full transition-transform duration-300 z-50
            flex flex-col">

    {{-- Header --}}
    <div class="px-6 py-5 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">
            Price tiers
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Configure quantity-based pricing
        </p>
    </div>

    {{-- Body --}}
    <form id="price-form" class="flex flex-col flex-1">

        {{-- Notifications --}}
        <div id="drawer-notification-block" class="px-6 pt-4"></div>

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3">

            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">
                Tiers
            </div>

            <div id="drawer-price-tiers" class="space-y-3">
                {{-- JS inject --}}
            </div>

            <button type="button"
                    onclick="addDrawerPriceTier()"
                    class="mt-2 text-sm text-gray-500 hover:text-gray-800
                           flex items-center gap-1 transition">
                + Add price tier
            </button>
        </div>

        {{-- Footer --}}
        <div class="border-t bg-white px-6 py-4 flex items-center justify-between">

            <div class="text-xs text-gray-400">
                Changes apply after saving
            </div>

            <div class="flex gap-2">
                <button type="button"
                        id="price-cancel"
                        class="px-4 py-2 text-sm rounded-lg border border-gray-200
                               text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-900 text-white
                               hover:bg-gray-800 transition shadow-sm">
                    Save changes
                </button>
            </div>

        </div>

    </form>
</div>




<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ===========================
       STOCK DRAWER (WAREHOUSES)
    =========================== */

     const stockDrawer = document.getElementById('stock-drawer');
    const stockOverlay = document.getElementById('stock-drawer-overlay');
    const stockCancelBtn = document.getElementById('stock-cancel');

    const productNameEl = document.getElementById('stock-product-name');
    const productIdInput = document.getElementById('stock-product-id');
    const warehouseList = document.getElementById('warehouse-stock-list');
    const stockForm = document.getElementById('stock-form');

    const allWarehouses = @json($warehouses);

    /* ===========================
       OPEN
    =========================== */

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.stock-button');
        if (!btn) return;

        const productId = btn.dataset.productId;
        const productName = btn.dataset.productName ?? '';

        const productWarehouses = JSON.parse(btn.dataset.productWarehouses || '[]');

        productNameEl.textContent = productName;
        productIdInput.value = productId;

        renderWarehouses(allWarehouses, productWarehouses);

        openStockDrawer();
    });

    /* ===========================
       RENDER (MAIN FIX)
    =========================== */

    function renderWarehouses(allWarehouses, productWarehouses) {

    warehouseList.innerHTML = '';

    // EMPTY STATE
    if (!allWarehouses || allWarehouses.length === 0) {
        warehouseList.innerHTML = `
            <div class="flex flex-col items-start gap-3 p-5 rounded-xl
                        border border-gray-200 bg-gray-50">

                <div class="flex items-center gap-2 text-gray-900 font-medium text-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v2m0 4h.01M10.29 3.86l-8 14A2 2 0 004 21h16a2 2 0 001.71-3.14l-8-14a2 2 0 00-3.42 0z"/>
                    </svg>

                    No warehouses found
                </div>

                <p class="text-xs text-gray-500 leading-relaxed">
                    Before you can manage stock, you need to create at least one warehouse.
                </p>

                <a href="{{ route('supplier.warehouses.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm
                          bg-gray-900 text-white rounded-lg
                          hover:bg-gray-800 transition shadow-sm">

                    Create warehouse

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        `;
        return;
    }

    // NORMAL STATE
    allWarehouses.forEach(wh => {

        const match = productWarehouses.find(w => w.id === wh.id);
        const qty = match?.pivot?.quantity ?? 0;

        warehouseList.insertAdjacentHTML('beforeend', `
            <div class="group flex items-center justify-between gap-4
                        px-4 py-3 rounded-lg border border-gray-200
                        bg-white hover:bg-gray-50 transition">

                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-900">
                        ${wh.name}
                    </span>

                    <span class="text-xs text-gray-400">
                        Stock quantity
                    </span>
                </div>

                <div class="flex items-center gap-2">

                    <input
                        type="number"
                        name="stocks[${wh.id}]"
                        value="${qty}"
                        min="0"
                        class="w-24 px-3 py-1.5 text-sm text-right
                               border border-gray-200 rounded-md
                               bg-white text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-gray-900/10
                               focus:border-gray-400
                               transition"
                    />

                    <span class="text-xs text-gray-400">
                        pcs
                    </span>
                </div>
            </div>
        `);
    });
}

    /* ===========================
       OPEN / CLOSE
    =========================== */

    function openStockDrawer() {
        stockDrawer.classList.remove('translate-x-full');
        stockOverlay.classList.remove('hidden');
    }

    function closeStockDrawer() {
        stockDrawer.classList.add('translate-x-full');
        stockOverlay.classList.add('hidden');
    }

    stockOverlay.addEventListener('click', closeStockDrawer);
    stockCancelBtn.addEventListener('click', closeStockDrawer);

    /* ===========================
       SUBMIT
    =========================== */

    stockForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const productId = productIdInput.value;

        const formData = new FormData(stockForm);
        const stocks = {};

        for (const [key, value] of formData.entries()) {
            if (key.startsWith('stocks[')) {
                const warehouseId = key.match(/\[(\d+)\]/)[1];
                stocks[warehouseId] = value === '' ? 0 : parseInt(value, 10);
            }
        }

        fetch(`/dashboard/supplier/products/${productId}/update-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ stocks })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update stock');
            }
        })
        .catch(err => console.error(err));
    });

    /* ===========================
       PRICE DRAWER
    =========================== */
    const drawer = document.getElementById('price-drawer');
    const overlay = document.getElementById('price-drawer-overlay');
    const cancelBtn = document.getElementById('price-cancel');
    const drawerTiers = document.getElementById('drawer-price-tiers');

    /* ===========================
       OPEN DRAWER
    ============================ */
    document.querySelectorAll('.price-button').forEach(btn => {
        btn.addEventListener('click', () => {
            drawerTiers.innerHTML = '';

            const productId = btn.dataset.productId;
            const tiers = JSON.parse(btn.dataset.productPriceTiers || '[]');

           if (tiers && tiers.length > 0) {
    tiers.forEach((tier, index) => {
        drawerTiers.insertAdjacentHTML('beforeend', `
            <div class="grid grid-cols-4 gap-3 mb-2 items-center p-2 border rounded-lg bg-gray-50">
                <input type="number"
                       class="p-2 border rounded text-sm w-full"
                       value="${tier.min_qty ?? ''}"
                       placeholder="Min">

                <input type="number"
                       class="p-2 border rounded text-sm w-full"
                       value="${tier.max_qty ?? ''}"
                       placeholder="Max">

                <input type="number"
                       class="p-2 border rounded text-sm w-full"
                       value="${tier.price ?? ''}"
                       placeholder="Price">

                ${index === tiers.length - 1 && tiers.length > 1
                    ? '<button type="button" class="text-red-500 hover:text-red-700 text-lg remove-tier">×</button>'
                    : ''
                }
            </div>
        `);
    });
} else {
    window.addDrawerPriceTier?.();
}

            drawer.dataset.productId = productId;
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
        });
    });

    /* ===========================
       CLOSE DRAWER
    ============================ */
    function closeDrawer() {
        drawer.classList.add('translate-x-full');
        overlay.classList.add('hidden');
    }
    overlay.addEventListener('click', closeDrawer);
    cancelBtn.addEventListener('click', closeDrawer);

    function updateDrawerTierChain(startIndex) {
        const rows = drawerTiers.querySelectorAll('.grid');
        for (let i = startIndex; i < rows.length; i++) {
            const prevRow = rows[i - 1];
            const prevMaxInput = prevRow.querySelector('input:nth-of-type(2)');
            const currentRow = rows[i];
            const currentMinInput = currentRow.querySelector('input:nth-of-type(1)');
            const currentMaxInput = currentRow.querySelector('input:nth-of-type(2)');

            if (prevMaxInput && currentMinInput) {
                const prevMaxValue = prevMaxInput.value.trim();
                const prevMax = parseInt(prevMaxValue, 10);
                if (prevMaxValue !== '' && !isNaN(prevMax)) {
                    currentMinInput.value = prevMax + 1;
                }
            }

            if (currentMinInput && currentMaxInput) {
                const minValue = currentMinInput.value.trim();
                const maxValue = currentMaxInput.value.trim();
                const min = parseInt(minValue, 10);
                const max = parseInt(maxValue, 10);
                if (maxValue !== '' && !isNaN(max) && !isNaN(min) && max <= min) {
                    currentMaxInput.value = min + 1;
                }
            }
        }
    }

    function showDrawerNotification(message, type = 'error') {
        const container = document.getElementById('drawer-notification-block');
        if (!container) return;

        const colors = {
            success: 'bg-green-100 border-green-300 text-green-800',
            error: 'bg-red-100 border-red-300 text-red-800'
        };

        const html = `
            <div class="mb-2 rounded-lg ${colors[type]} px-4 py-3">
                ${message}
            </div>
        `;

        container.innerHTML = html;
    }

    function removeDrawerNotifications() {
        const container = document.getElementById('drawer-notification-block');
        if (!container) return;
        container.innerHTML = '';
    }

    function refreshDrawerDeleteButtons() {
        const rows = drawerTiers.querySelectorAll('.grid');
        rows.forEach((row, idx) => {
            const btn = row.querySelector('.remove-tier');
            if (idx === rows.length - 1 && rows.length > 1) {
                if (!btn) {
                    row.insertAdjacentHTML('beforeend', '<button type="button" class="text-red-600 remove-tier">&times;</button>');
                } else {
                    btn.style.display = '';
                }
            } else {
                if (btn) btn.remove();
            }
        });
    }

    function checkDrawerMaxMinValidation() {
        const rows = drawerTiers.querySelectorAll('.grid');
        let isError = false;

        for (const row of rows) {
            const inputs = row.querySelectorAll('input');
            const minValue = inputs[0]?.value.trim();
            const maxValue = inputs[1]?.value.trim();
            const min = parseInt(minValue, 10);
            const max = parseInt(maxValue, 10);
            if (minValue !== '' && maxValue !== '' && !isNaN(min) && !isNaN(max) && max < min) {
                isError = true;
                break;
            }
        }

        removeDrawerNotifications();
        if (isError) {
            showDrawerNotification('Max Quantity не может быть меньше Min Quantity!', 'error');
        }
        return !isError;
    }

    drawerTiers.addEventListener('change', function (e) {
        if (e.target.tagName !== 'INPUT') return;
        const row = e.target.closest('.grid');
        if (!row) return;

        const rows = Array.from(drawerTiers.querySelectorAll('.grid'));
        const index = rows.indexOf(row);
        const inputs = row.querySelectorAll('input');
        if (e.target === inputs[1]) {
            updateDrawerTierChain(index + 1);
        }
        checkDrawerMaxMinValidation();
    });

    /* ===========================
       ADD TIER
    ============================ */
    window.addDrawerPriceTier = function () {
        const rows = drawerTiers.querySelectorAll('.grid');
        const currentCount = rows.length;

        if (rows.length > 0) {
            const lastMaxInput = rows[rows.length - 1].querySelector('input:nth-of-type(2)');
            const lastMaxValue = lastMaxInput?.value.trim() ?? '';
            if (lastMaxValue === '' || isNaN(parseInt(lastMaxValue, 10))) {
                removeDrawerNotifications();
                return;
            }

            const prevButton = rows[rows.length - 1].querySelector('.remove-tier');
            if (prevButton) prevButton.remove();
        }

        const prevMaxValue = rows.length > 0 ? parseInt(rows[rows.length - 1].querySelector('input:nth-of-type(2)').value.trim(), 10) : '';
        const nextMinValue = rows.length > 0 ? prevMaxValue + 1 : '';
        const minReadonly = rows.length > 0 ? 'readonly' : '';

        drawerTiers.insertAdjacentHTML('beforeend', `
            <div class="grid grid-cols-4 gap-4 mb-2 items-center" data-index="${currentCount}">
                <input type="number" class="input p-2 border rounded text-xs" placeholder="Min Qty" value="${nextMinValue}" ${minReadonly}>
                <input type="number" class="input p-2 border rounded text-xs" placeholder="Max Qty">
                <input type="number" class="input p-2 border rounded text-xs" placeholder="Unit Price $">
                ${currentCount === 0 ? '' : '<button type="button" class="text-red-600 remove-tier">&times;</button>'}
            </div>
        `);
    };

    /* ===========================
       REMOVE TIER
    ============================ */
    drawerTiers.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-tier')) {
            e.target.closest('.grid').remove();
            refreshDrawerDeleteButtons();
        }
    });

    /* ===========================
       SAVE TIERS → AJAX
    ============================ */
    document.getElementById('price-form').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!checkDrawerMaxMinValidation()) {
        return;
    }

    const productId = drawer.dataset.productId;
    const tiers = [];

    drawerTiers.querySelectorAll('.grid').forEach(tier => {
        const inputs = tier.querySelectorAll('input');
        if (!inputs[0].value && !inputs[1].value && !inputs[2].value) return;

        tiers.push({
            min_qty: inputs[0].value,
            max_qty: inputs[1].value,
            price: inputs[2].value
        });
    });

    fetch(`/dashboard/supplier/products/${productId}/update-price-tiers`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tiers })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const priceBtn = document.querySelector(`.price-button[data-product-id="${productId}"]`);
            if (data.tiers.length) {
                const minTier = data.tiers.reduce((prev, curr) => prev.min_qty < curr.min_qty ? prev : curr);
                priceBtn.textContent = `$${parseFloat(minTier.price).toFixed(2)}`;
                priceBtn.dataset.productPriceTiers = JSON.stringify(data.tiers);
            }

            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        } else {
            alert('Failed to save price tiers.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error while saving price tiers.');
    });
});


});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.delete-product').forEach(btn => {

        btn.addEventListener('click', function () {

            const productId = this.dataset.id;
            const url = this.dataset.url;
            const row = this.closest('tr');

            confirmModal.open({

                title: 'Archive Product',

                message: 'Are you sure you want to archive this product?',

                description: 'The product will be hidden from your active product list. You can restore it later if archive management is available.',

                confirmText: 'Archive Product',

                cancelText: 'Cancel',

                onConfirm: () => {

                    fetch(url, {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),

                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(
                                data.message || 'Failed to archive product.'
                            );
                        }

                        return data;
                    })
                    .then(data => {

                        if (data.success) {
                            row.remove();
                        } else {
                            alert(data.message || 'Failed to archive product.');
                        }

                    })
                    .catch(error => {

                        console.error(error);

                        alert(error.message || 'An unexpected error occurred. Please try again.');

                    });

                }

            });

        });

    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.product-select-checkbox');
    const countElement = document.getElementById('selected-products-count');
    const deleteButton = document.getElementById('bulk-archive-products');

    if (!countElement || !deleteButton) {
        return;
    }

    const updateSelection = () => {
        const selected = document.querySelectorAll(
            '.product-select-checkbox:checked'
        );

        countElement.textContent = selected.length;
        deleteButton.disabled = selected.length === 0;
    };

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelection);
    });

    
deleteButton.addEventListener('click', () => {
    const selected = Array.from(
        document.querySelectorAll('.product-select-checkbox:checked')
    );

    if (!selected.length) {
        return;
    }

    const ids = selected.map((checkbox) => Number(checkbox.value));

    confirmModal.open({
        title: 'Archive Selected Products',
        message: `Are you sure you want to archive ${ids.length} selected product(s)?`,
        description: 'The selected products will be hidden from your active product list. This action does not permanently delete them.',
        confirmText: 'Archive Products',
        cancelText: 'Cancel',

        onConfirm: async () => {
            deleteButton.disabled = true;
            deleteButton.textContent = 'Archiving...';

            try {
                const response = await fetch(deleteButton.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message || 'Failed to archive selected products.'
                    );
                }

                data.ids.forEach((id) => {
                    const checkbox = document.querySelector(
                        `.product-select-checkbox[value="${id}"]`
                    );

                    checkbox?.closest('tr')?.remove();
                });

                updateSelection();

            } catch (error) {
                console.error(error);
                alert(
                    error.message ||
                    'An unexpected error occurred. Please try again.'
                );
            } finally {
                deleteButton.textContent = 'Delete selected';
                updateSelection();
            }
        },
    });
});


});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('toggle-product-filters');
    const filtersPanel = document.getElementById('product-filters-panel');
    const chevron = document.getElementById('product-filters-chevron');

    if (!toggleButton || !filtersPanel || !chevron) {
        return;
    }

    toggleButton.addEventListener('click', () => {
        const isExpanded =
            toggleButton.getAttribute('aria-expanded') === 'true';

        const nextExpanded = !isExpanded;

        filtersPanel.hidden = !nextExpanded;

        toggleButton.setAttribute(
            'aria-expanded',
            String(nextExpanded)
        );

        toggleButton.setAttribute(
            'aria-label',
            nextExpanded
                ? 'Collapse product filters'
                : 'Expand product filters'
        );

        chevron.classList.toggle('rotate-180', nextExpanded);
    });
});
</script>


