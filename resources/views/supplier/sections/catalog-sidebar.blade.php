<div class="bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4">

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold">Company Catalog</h2>

        <a href="{{ route('supplier.show', $supplier->slug) }}?tab=products"
           class="text-sm text-gray-500 hover:text-gray-900 transition font-medium">
            Reset
        </a>
    </div>

    @include('supplier.partials.category_tree', [
        'categories' => $rootCategories,
        'tree' => $tree,
        'supplier' => $supplier
    ])

</div>