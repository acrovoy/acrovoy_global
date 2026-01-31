<div class="relative" id="catalogWrapper">
    <button id="catalogToggle" class="flex items-center space-x-1 hover:text-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="2" y="2" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="12" y="2" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="2" y="12" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="12" y="12" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Catalog</span>
    </button>

    <!-- Mega Menu -->
    <div id="catalogMenu" class="hidden absolute left-0 top-full mt-2 bg-[#F7F3EA] shadow-xl border border-gray-200 z-50 w-[900px] rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-8">
            @foreach($catalogColumns as $column)
                <div class="space-y-6">
                    @foreach($column as $category)
                        <div>
                            <h4 class="text-lg font-semibold mb-3">
                                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="hover:text-black">
                                    {{ $category->name }}
                                </a>
                            </h4>

                            @if($category->children->count())
                                <ul class="space-y-1 text-gray-600">
                                    @foreach($category->children as $child)
                                        <li class="relative group">
                                            <a href="{{ route('catalog.index', ['category' => $child->slug]) }}" class="hover:text-black flex justify-between items-center">
                                                {{ $child->name }}
                                                @if($child->children->count())
                                                    <span class="ml-1">→</span>
                                                @endif
                                            </a>

                                            @if($child->children->count())
                                                <ul class="absolute left-full top-0 mt-0 ml-2 w-48 bg-[#E0DDD1] border border-gray-200 rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                                    @foreach($child->children as $grandchild)
                                                        <li>
                                                            <a href="{{ route('catalog.index', ['category' => $grandchild->slug]) }}" class="block px-2 py-1 hover:bg-gray-200 rounded">
                                                                {{ $grandchild->name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
