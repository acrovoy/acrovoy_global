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
        @php
          
          @endphp
    </div>
</section>

{{-- MAIN CATALOG LAYOUT --}}
<section class="py-2 bg-[#F7F3EA]">
    <div class="container mx-auto px-6">

        <div class="flex flex-col md:flex-row gap-8">

            {{-- FILTERS SIDEBAR --}}
<aside class="w-full md:w-1/4 mb-4">

   {{-- Category links — кликаем, переход сразу --}}
<div class="bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4">
    
    <ul class="space-y-1">
        @foreach($categories as $cat)
            <li>
                <a href="{{ route('catalog.index', ['category' => $cat->slug]) }}"
                   class="text-gray-700 hover:text-black
                          @if(request('category') == $cat->slug) font-bold text-orange-500 @endif">
                    {{ $cat->name }}
                </a>

                {{-- Подкатегории --}}
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

    {{-- Material + Price filters — через форму GET --}}
    <form method="GET" action="{{ route('catalog.index') }}" class="mt-4 bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4">
        {{-- Сохраняем текущую категорию при отправке --}}
        <input type="hidden" name="category" value="{{ request('category') }}">


     {{-- Reset Filters Button styled like section header --}}
<div class="flex items-center justify-between mb-2">
    <h2 class="text-xl font-bold">Filter Products</h2>
    <a href="{{ route('catalog.index', ['category' => request('category')]) }}"
       class="inline-flex items-center gap-1 px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 shadow-sm rounded-full transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Reset
    </a>
</div>


        {{-- Material Filter --}}
            <div>
                <h4 class="font-medium mb-2">Materials</h4>
                <div class="max-h-48 overflow-y-auto border p-2 rounded space-y-2">
                    @foreach(App\Models\Material::all() as $material)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="material[]" value="{{ $material->slug }}"
                                @if(in_array($material->slug, (array) request('material'))) checked @endif>
                            {{ $material->name }}
                        </label>
                    @endforeach
                </div>
            </div>

        {{-- MOQ Filter --}}
<div>
    <h4 class="font-medium mb-2">MOQ</h4>
    <input type="number" name="min_moq" value="{{ request('min_moq') }}" placeholder="Min MOQ" class="w-full p-2 border rounded">
</div>


{{-- Sold Filter --}}
<div>
    <h4 class="font-medium mb-2">Sold (Min)</h4>
    <input type="number" name="sold_from" value="{{ request('sold_from') }}" placeholder="Min sold" class="w-full p-2 border rounded">
</div>

{{-- Lead Time Filter --}}
<div>
    <h4 class="font-medium mb-2">Lead Time (days)</h4>
    <div class="flex space-x-2">
        <input type="number" name="min_lead_time" value="{{ request('min_lead_time') }}" placeholder="Min" class="w-1/2 p-2 border rounded">
        <input type="number" name="max_lead_time" value="{{ request('max_lead_time') }}" placeholder="Max" class="w-1/2 p-2 border rounded">
    </div>
</div>

        {{-- Price Filter --}}
        <div>
            <h4 class="font-medium mb-2">Price</h4>
            <div class="flex space-x-2">
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 p-2 border rounded">
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 p-2 border rounded">
            </div>
        </div>



        {{-- Country Filter --}}
        <div>
            <h4 class="font-medium mb-2">Country of Origin</h4>
            <div class="space-y-2 max-h-48 overflow-y-auto border p-2 rounded">
                @foreach(App\Models\Country::all() as $country)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="country[]" value="{{ $country->id }}"
                            @if(in_array($country->id, (array) request('country'))) checked @endif>
                        {{ $country->name }}
                    </label>
                @endforeach
            </div>
        </div>


        <button type="submit" class="w-full bg-blue-900 text-white py-2 rounded-lg hover:bg-blue-700 transition">
            Apply Filters
        </button>
    </form>

</aside>


            {{-- PRODUCT LIST --}}
            <div class="w-full md:w-3/4">

                {{-- Sort Bar --}}
                <div class="flex justify-between items-center mb-6">
                    <div class="text-gray-700 text-sm">
                        Showing {{ $products->count() }} of {{ $totalProducts }} items
                    </div>

                    
                </div>

                {{-- Product Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    @if($products->count())
                        @foreach ($products as $product)
                            <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">

                                @if($product->slug)
                                    <a href="{{ route('product.show', $product->slug) }}">
                                        <img src="{{ $product->image_url }}"
                                            class="w-full h-auto object-contain">
                                    </a>
                                @else
                                    <img src="{{ $product->image_url }}"
                                        class="w-full h-auto object-contain">
                                @endif

                                <div class="p-4">
                                    <h3 class="font-semibold text-lg mb-2">
                                        <a href="{{ route('product.show', $product->slug) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                                    </h3>
                                    @php
    $materialNames = $product->materials
        ->map(fn($material) => $material->translations->first()?->name ?? $material->name)
        ->join(', ');
@endphp

<p class="text-gray-600 text-sm mb-4 flex flex-wrap items-center gap-1">
    <span>{{ $product->country->name ?? '-' }}</span>
    @if($materialNames)
        <span>•</span>
        <span>{{ $materialNames }}</span>
    @endif
    @if($product->sold_count > 0)
        <span>•</span>
        <span>Продано: {{ $product->sold_count }}</span>
    @endif
</p>
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-blue-900">
                                            {{ price($product->max_tier_price ?? $product->price) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full flex flex-col items-center justify-center py-20">
                            {{-- Нейтральный SVG-заглушка --}}
                            <!-- <svg xmlns="http://www.w3.org/2000/svg" class="w-48 h-48 mb-6 text-gray-400" fill="none" viewBox="0 0 64 64" stroke="currentColor" stroke-width="2">
                                <circle cx="32" cy="32" r="30" stroke-opacity="0.2"/>
                                <path d="M20 45c5-5 24-5 29 0M16 32c8-12 32-12 40 0" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M32 16v16M24 24l8 8 8-8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg> -->

                            {{-- Надпись --}}
                            <h2 class="text-2xl md:text-3xl font-bold text-brown-900 mb-2 text-center">
                                No products found.
                            </h2>
                            <p class="text-gray-600 text-center max-w-md">
                                Currently there are no products available in this category. Please check other categories or try again later.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                <div class="mt-10"></div>

            </div>
        </div>
    </div>
</section>

@endsection
