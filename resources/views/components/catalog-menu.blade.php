
<div class="relative" id="catalogWrapper">
    <button id="catalogToggle" class="flex items-center space-x-1.5 text-gray-800 hover:text-black transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
            <rect x="2" y="2" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="12" y="2" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="2" y="12" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="12" y="12" width="6" height="6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="text-sm font-medium">Catalog</span>
    </button>

    <!-- Mega Menu -->
    <div id="catalogMenu" class="hidden absolute left-0 top-full mt-2 bg-[#F7F3EA] shadow-[0_12px_32px_rgba(0,0,0,0.12)] border border-[#E5E0D5] z-50 w-[900px] rounded-xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-8">
            @foreach($catalogColumns as $column)
                <div class="space-y-6">
                    @foreach($column as $category)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3 tracking-tight">
                                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="hover:text-[#8A7653] transition-colors duration-150">
                                    {{ $category->name }}
                                </a>
                            </h4>

                            @if($category->children->count())
                                <ul class="space-y-1 text-[13px] leading-5 text-gray-600">
                                    @foreach($category->children as $child)
                                        <li class="relative group">
                                            <a href="{{ route('catalog.index', ['category' => $child->slug]) }}" class="hover:text-gray-950 flex justify-between items-center transition-colors duration-150">
                                                {{ $child->name }}
                                                @if($child->children->count())
                                                    <span class="ml-1 text-gray-400 group-hover:text-gray-800 transition-colors duration-150">→</span>
                                                @endif
                                            </a>

                                            @if($child->children->count())
                                                <ul class="absolute left-full top-0 mt-0 ml-2 w-48 bg-white border border-gray-200 rounded-lg shadow-[0_8px_24px_rgba(0,0,0,0.10)] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 py-1">
                                                    @foreach($child->children as $grandchild)
                                                        <li>
                                                            <a href="{{ route('catalog.index', ['category' => $grandchild->slug]) }}" class="block px-3 py-2 text-[12px] leading-4 text-gray-600 hover:text-gray-950 hover:bg-[#F7F3EA] rounded-md transition-colors duration-150">
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
