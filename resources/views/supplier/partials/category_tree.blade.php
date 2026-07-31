<ul class="space-y-1">

@foreach($categories as $category)

    @if(isset($tree[$category->id]))

        {{-- есть дочерние категории --}}
        @include('supplier.partials.category_tree', [
            'categories' => $tree[$category->id],
            'tree' => $tree,
            'supplier' => $supplier
        ])

    @else

        {{-- конечная категория --}}
        <li>

            <a href="{{ request()->fullUrlWithQuery(['category' => $category->slug]) }}"
   class="flex items-center justify-between rounded-lg px-3 py-2 transition

   {{ request('category') == $category->slug
        ? 'bg-[#f7f3ec] border border-[#e8ddd0] text-[#6f4e37]'
        : 'text-gray-700 hover:bg-gray-50'
   }}">

    <div>

        <div class="font-medium">
            {{ $category->name }}
        </div>

        @if($category->parent)
            <div class="mt-0.5 text-xs text-gray-400">
                {{ $category->parent->name }}
            </div>
        @endif

    </div>

</a>

        </li>

    @endif

@endforeach

</ul>