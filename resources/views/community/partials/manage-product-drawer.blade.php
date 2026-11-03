<div
    id="manage-products-drawer"
    class="fixed inset-0 z-[10000] hidden"
    data-index-url="{{ route('community.posts.products.index', ['id' => '__POST_ID__']) }}"
    data-search-url="{{ route('community.posts.products.search', ['id' => '__POST_ID__']) }}"
    data-sync-url="{{ route('community.posts.products.sync', ['id' => '__POST_ID__']) }}"
>
    {{-- Backdrop --}}
    <div
        data-drawer-backdrop
        class="absolute inset-0 bg-stone-950/40 opacity-0 transition-opacity duration-300"
    ></div>

    {{-- Drawer --}}
    <aside
        data-drawer-panel
        class="absolute right-0 top-0 flex h-full w-full max-w-2xl translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300"
        role="dialog"
        aria-modal="true"
        aria-labelledby="manage-products-title"
    >
        {{-- Header --}}
        <div class="flex items-start justify-between border-b border-stone-200 px-6 py-5">
            <div class="min-w-0">
                <h2
                    id="manage-products-title"
                    class="text-lg font-semibold text-stone-900"
                >
                    Manage products
                </h2>

                <p
                    id="manage-products-post-title"
                    class="mt-1 truncate text-xs text-stone-500"
                ></p>
            </div>

            <button
                type="button"
                data-close-products-drawer
                aria-label="Close drawer"
                class="ml-4 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-stone-400 transition hover:bg-stone-100 hover:text-stone-800"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="border-b border-stone-200 px-6 py-4">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                    <circle cx="10.8" cy="10.8" r="6.8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16 16 5 5"/>
                </svg>

                <input
                    id="manage-products-search"
                    type="search"
                    autocomplete="off"
                    placeholder="Search by product, category or supplier..."
                    class="w-full rounded-lg border border-stone-200 bg-white py-2.5 pl-10 pr-4 text-sm text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-stone-400 focus:ring-2 focus:ring-stone-100"
                >
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        data-products-tab="all"
                        class="rounded-md bg-stone-900 px-3 py-1.5 text-xs font-medium text-white"
                    >
                        All products
                    </button>

                    <button
                        type="button"
                        data-products-tab="selected"
                        class="rounded-md border border-stone-200 bg-white px-3 py-1.5 text-xs font-medium text-stone-600 transition hover:bg-stone-50"
                    >
                        Selected
                        <span
                            id="selected-products-tab-count"
                            class="ml-1"
                        >0</span>
                    </button>
                </div>

                <span
                    id="manage-products-results-count"
                    class="text-xs text-stone-400"
                ></span>
            </div>
        </div>

        {{-- Product list --}}
        <div
            id="manage-products-list"
            class="min-h-0 flex-1 overflow-y-auto px-6 py-2"
        >
            <div class="flex justify-center py-16">
                <span class="text-sm text-stone-400">Loading products...</span>
            </div>
        </div>

        {{-- Pagination --}}
        <div
            id="manage-products-pagination"
            class="hidden items-center justify-between border-t border-stone-100 px-6 py-3"
        >
            <span
                id="manage-products-pagination-info"
                class="text-xs text-stone-500"
            ></span>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    data-products-page="prev"
                    class="rounded-md border border-stone-200 px-3 py-1.5 text-xs text-stone-600 transition hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    Previous
                </button>

                <button
                    type="button"
                    data-products-page="next"
                    class="rounded-md border border-stone-200 px-3 py-1.5 text-xs text-stone-600 transition hover:bg-stone-50 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    Next
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between border-t border-stone-200 bg-stone-50 px-6 py-4">
            <div>
                <div
                    id="manage-products-selected-count"
                    class="text-sm font-semibold text-stone-800"
                >
                    0 products selected
                </div>

                <p class="mt-0.5 text-xs text-stone-500">
                    Changes will be applied to this publication.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    data-close-products-drawer
                    class="rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-xs font-medium text-stone-700 transition hover:bg-stone-100"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="save-community-products"
                    class="inline-flex items-center gap-2 rounded-lg bg-stone-900 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-stone-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Save changes
                </button>
            </div>
        </div>
    </aside>
</div>