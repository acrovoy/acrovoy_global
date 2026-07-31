<div class="rounded-2xl border border-stone-200 bg-white">

    {{-- Header --}}
    <div class="flex items-center justify-between px-7 py-5 border-b border-stone-200">

        <div>

            <div class="text-[11px] uppercase tracking-[0.22em] font-semibold text-stone-400">
                Navigation
            </div>

            <h2 class="mt-1 text-xl font-semibold tracking-tight text-stone-900">
                Catalog
            </h2>

        </div>

        <a href="{{ route('supplier.show', $supplier->slug) }}?tab=products"
   class="text-sm font-medium text-stone-400 hover:text-stone-700 transition">
    Reset
</a>

    </div>

    {{-- Categories --}}
    <div class="px-7 py-6">

        @include('supplier.partials.category_tree', [
            'categories' => $rootCategories,
            'tree' => $tree,
            'supplier' => $supplier
        ])

    </div>

</div>