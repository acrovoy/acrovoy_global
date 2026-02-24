<ul class="space-y-0.5 ml-3 border-l border-gray-200 pl-3 text-sm">

@foreach($categories as $category)

    <li class="relative">

        {{-- ROOT или Parent Node (НЕ clickable) --}}
        @if(isset($tree[$category->id]))

            <div class="flex items-center px-2 py-1.5 text-sm font-semibold text-gray-700 tracking-wide select-none">
                {{ $category->name }}
            </div>

            {{-- Recursive children --}}
            @include('supplier.partials.category_tree', [
                'categories' => $tree[$category->id],
                'tree' => $tree,
                'supplier' => $supplier
            ])

        @else

            {{-- Leaf Category → CLICKABLE LINK --}}
            <a href="{{ request()->fullUrlWithQuery(['category' => $category->slug]) }}"
               class="flex items-center gap-2 px-2 py-1.5 rounded-md transition-all duration-150
               {{ request('category') == $category->slug
                    ? 'text-emerald-600 font-semibold bg-emerald-50'
                    : 'text-gray-600 text-sm hover:text-gray-900 hover:bg-gray-50' }}">

                <span class="w-3"></span>

                <span class="truncate">{{ $category->name }}</span>
            </a>

        @endif

    </li>

@endforeach

</ul>