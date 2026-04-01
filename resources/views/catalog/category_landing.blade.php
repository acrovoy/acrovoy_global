@extends('layouts.app')

@section('content')

{{-- HERO CATEGORY BANNER --}}
<section class="py-2 bg-[#F7F3EA]">
    <div class="container mx-auto px-6">
        <h1 class="text-4xl font-extrabold text-gray-900">
            {{ $selectedCategory->name ?? 'All Products' }}
        </h1>
        <p class="text-lg text-gray-700 mt-2">
            Explore professional furniture for hotels, resorts & restaurants
        </p>
    </div>
</section>

{{-- MAIN CATALOG LAYOUT --}}
<section class="py-2 bg-[#F7F3EA]">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row gap-8">

            {{-- FILTERS SIDEBAR --}}
            <aside class="w-full md:w-1/4 mb-4">
                {{-- Category links --}}
                <div class="bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4">
                    <ul class="space-y-1">
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('catalog.index', ['category' => $cat->slug]) }}"
                                   class="text-gray-700 hover:text-black
                                          @if(request('category') == $cat->slug) font-bold text-orange-500 @endif">
                                    {{ $cat->name }}
                                </a>

                                @if($cat->children->count())
                                    <ul class="ml-4 mt-1 space-y-1">
                                        @foreach($cat->children as $child)
                                            <li>
                                                <a href="{{ route('catalog.index', ['category' => $child->slug]) }}"
                                                   class="text-gray-600 hover:text-black
                                                          @if(request('category') == $child->slug) font-bold text-orange-500 @endif">
                                                    {{ $child->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Filters Form --}}
                
            </aside>

            {{-- PRODUCT LIST --}}
            <div class="w-full md:w-3/4">

                {{-- Подкатегории --}}
                @if($subcategories->count())
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-4">Subcategories</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($subcategories as $subcat)
                                <a href="{{ route('catalog.index', ['category' => $subcat->slug]) }}"
                                   class="block p-4 border rounded-lg hover:shadow">
                                    {{ $subcat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                

            </div>

        </div>
    </div>
</section>

@endsection