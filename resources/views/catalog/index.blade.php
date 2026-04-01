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

                {{-- Filters Form --}}
                <form method="GET" action="{{ route('catalog.index') }}" class="mt-6 bg-white border border-gray-200 rounded-2xl p-6">

                    <input type="hidden" name="category" value="{{ request('category') }}">

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-700">Filters</h2>
                    </div>

                    {{-- Контейнер с отступами между фильтрами --}}
                    <div class="space-y-6 mt-4">

                        {{-- Material Filter --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Materials</h4>
                            <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                                @foreach(App\Models\Material::all() as $material)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="material[]" value="{{ $material->slug }}"
                                        @if(in_array($material->slug, (array) request('material'))) checked @endif
                                    class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-0">
                                    {{ $material->name }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Price Filter --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Price</h4>
                            <div class="flex space-x-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                    class="w-1/2 p-2 border rounded text-sm">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                                    class="w-1/2 p-2 border rounded text-sm">
                            </div>
                        </div>

                        {{-- MOQ Filter --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">MOQ</h4>
                            <input type="number" name="min_moq" value="{{ request('min_moq') }}" placeholder="Min MOQ"
                                class="w-full p-2 border rounded text-sm">
                        </div>

                        {{-- Sold Filter --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Sold (Min)</h4>
                            <input type="number" name="sold_from" value="{{ request('sold_from') }}" placeholder="Min sold"
                                class="w-full p-2 border rounded text-sm">
                        </div>

                        {{-- Lead Time Filter --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Lead Time (days)</h4>
                            <div class="flex space-x-2">
                                <input type="number" name="min_lead_time" value="{{ request('min_lead_time') }}" placeholder="Min"
                                    class="w-1/2 p-2 border rounded text-sm">
                                <input type="number" name="max_lead_time" value="{{ request('max_lead_time') }}" placeholder="Max"
                                    class="w-1/2 p-2 border rounded text-sm">
                            </div>
                        </div>

                        {{-- Country Filter --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Country of Origin</h4>
                            <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                                @foreach(App\Models\Country::all() as $country)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="country[]" value="{{ $country->id }}"
                                        @if(in_array($country->id, (array) request('country'))) checked @endif
                                    class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-0">
                                    {{ $country->name }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Apply Button --}}
                        <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition duration-200">
                            Apply Filters
                        </button>

                    </div>
                </form>

            </aside>


            {{-- PRODUCT LIST --}}
            <div class="w-full md:w-3/4">

                {{-- Top Filter Bar --}}
                <form method="GET" action="" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-5">
                    {{-- Сохраняем все текущие фильтры кроме supplier_type --}}
                    @foreach(request()->except('supplier_type') as $key => $value)
                    @if(is_array($value))
                    @foreach($value as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                    @endforeach
                    @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                    @endforeach

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                        {{-- Left: Results count + Reset --}}
                        <div class="flex items-center gap-4 text-sm text-gray-600 tracking-wide">
                            <span>
                                Showing
                                <span class="font-semibold text-gray-900">
                                    {{ $products->count() }}
                                </span> products(s)
                            </span>
                            <a href="{{ route('catalog.index', [
    'category' => request()->route('category') ?? request('category')
]) }}"
class="text-sm text-orange-800 hover:text-gray-900 transition underline">
    Reset all filters
</a>
                        </div>

                        {{-- Right: Supplier Type --}}
                        <div class="flex flex-wrap items-center gap-6">
                            @foreach($types as $key => $label)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer group">
                                <input type="checkbox"
                                    name="supplier_type[]"
                                    value="{{ $key }}"
                                    onchange="this.form.submit()"
                                    @checked(in_array($key, $activeTypes))
                                    class="w-4 h-4 border-gray-300 rounded text-yellow-700 focus:ring-yellow-700">
                                <span class="group-hover:text-black transition">
                                    {{ $label }}
                                </span>
                            </label>
                            @endforeach

                            {{-- Голд --}}
                            <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer group">
                                <input type="checkbox"
                                    name="supplier_type[]"
                                    value="gold"
                                    onchange="this.form.submit()"
                                    @checked(in_array('gold', $activeTypes))
                                    class="w-4 h-4 border-gray-300 rounded text-amber-600 focus:ring-amber-600">
                                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-semibold tracking-wide
                             bg-amber-100 text-amber-700 border border-amber-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path d="M5 12l5 5L20 7" />
                                    </svg>
                                    GOLD
                                </span>
                            </label>
                        </div>

                    </div>
                </form>






                {{-- Product Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    @if($products->count())
                    @foreach ($products as $product)

                    @php
                    $mainImage = $product->images->firstWhere('is_main', 1) ?? $product->images->first();

                    // Восстанавливаем materialNames, чтобы не было ошибки
                    $materialNames = $product->materials
                    ->map(fn($material) => $material->translations->first()?->name ?? $material->name)
                    ->join(', ');
                    @endphp

                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition-all duration-300 overflow-hidden group">

                        {{-- Основное изображение каталога --}}
                        @if($product->slug)
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ $product->catalog_image_url }}"
                                class="w-full h-auto object-contain"
                                alt="{{ $product->name }}">
                        </a>
                        @else
                        <img src="{{ $product->catalog_image_url }}"
                            class="w-full h-auto object-contain"
                            alt="{{ $product->name }}">
                        @endif

                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2">
                                <a href="{{ route('product.show', $product->slug) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                            </h3>

                            <p class="text-gray-600 text-sm mb-2 flex flex-wrap items-center gap-1">

                                <!-- <span>{{ $product->country->name ?? '-' }}</span> -->

                                @if(!empty($materialNames))

                                <!-- <span>•</span> -->

                                <span>{{ $materialNames }}</span>
                                @endif


                                @if($product->sold_count > 0)
                                <span>•</span>
                                <span>Продано: {{ $product->sold_count }}</span>
                                @endif
                            </p>

                            {{-- Variants preview (выплывает при наведении) --}}
                            @if($product->variantGroup?->items->isNotEmpty())
                            <div class="overflow-hidden h-0 group-hover:h-10 transition-[height] duration-300 mb-2">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($product->variantGroup->items as $variant)
                                    <div class="w-8 h-8 border rounded overflow-hidden">
                                        <img
                                            src="{{ $variant->media?->cdn_url ?? asset('images/no-image.png') }}"
                                            alt="{{ $variant->title }}"
                                            class="w-full h-full object-cover"
                                            title="{{ $variant->title }}">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-900">
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