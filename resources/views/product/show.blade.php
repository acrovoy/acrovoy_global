@extends('layouts.app')
@section('content')

<section class="bg-[#F7F3EA] py-8">
    <div class="container mx-auto px-6">

    <x-alerts />


        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-6 flex flex-wrap gap-1">
            <a href="{{ route('catalog.index') }}" class="hover:text-black">{{ __('product/product_show.root') }}</a> /
            <a href="{{ route('catalog.index', $product1->category->slug) }}" class="hover:text-black">
                {{ $product1->category->name ?? 'Category' }}
            </a> /
            <span class="text-gray-900">{{ $product1->name }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">

            {{-- Галерея продукта --}}
            <div class="bg-white rounded-xl shadow p-4 mb-4">
                <img id="mainImage"
                    src="{{ $product1->main_image_url }}"
                    class="w-full h-auto object-contain rounded-lg cursor-pointer"
                    alt="{{ $product1->name }}">

                <div class="flex gap-4 mt-4">
                    @foreach($product1->thumbnails as $media)
                    <img src="{{ $media['thumb'] }}"
                        class="thumbnail w-20 h-20 object-contain bg-gray-100 rounded cursor-pointer border 
                                    {{ $media['is_main'] ? 'border-blue-700' : 'border-gray-300' }}"
                        data-src="{{ $media['large'] }}"
                        alt="{{ $product1->name }}">
                    @endforeach
                </div>
            </div>


            @php
            $reviewsCount = $product1->reviews->count();
            $rating = $reviewsCount > 0 ? round($product1->reviews->avg('rating'), 1) : 0;
            $soldCount = $product1->orders->where('status', 'completed')->sum('quantity');
            $inWishlist = in_array($product1->id, $wishlistIds);
            @endphp


            {{-- Info --}}
            <div class="rounded-xl shadow p-6" x-data="{ showProjectBox: false,
        showCustomizationBox: false }">

                <div class="flex flex-col lg:flex-row lg:items-start gap-4">

                    {{-- LEFT BLOCK --}}
                    <div class="flex-1">

                        {{-- Title --}}
                        <div class="flex items-center gap-3">


                            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight inline-flex items-center gap-2">

                                {{ $product1->name }}

                               
{{-- Wishlist --}}

@auth

    @can('addToWishlist', $product1)

        <button
            type="button"
            class="wishlist-toggle text-gray-400 hover:text-red-500 transition"
            data-product-id="{{ $product1->id }}"
            title="Wishlist">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6 wishlist-icon transition {{ $inWishlist ? 'text-red-500' : 'text-gray-500' }}"
                viewBox="0 0 24 24"
                fill="{{ $inWishlist ? 'currentColor' : 'none' }}"
                stroke="currentColor"
                stroke-width="2">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636
                       l1.318-1.318a4.5 4.5 0 016.364 6.364
                       L12 21.682l-7.682-7.682a4.5 4.5 0 010-6.364z" />

            </svg>

        </button>

    @endcan

@else

    <button
        type="button"
        onclick="dispatchAlert(
            'guest',
            'Please register or log in to add products to your wishlist.'
        )"
        class="wishlist-toggle text-gray-400 hover:text-red-500 transition"
        title="Wishlist">

        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-6 h-6 wishlist-icon transition"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636
                   l1.318-1.318a4.5 4.5 0 016.364 6.364
                   L12 21.682l-7.682-7.682a4.5 4.5 0 010-6.364z" />

        </svg>

    </button>

@endauth



                            </h1>





                        </div>

                        <p class="text-gray-700 mb-2 leading-relaxed">
                            {{ $product1->undername }}
                        </p>

                        {{-- ⭐ Rating --}}


                        <div class="flex flex-wrap items-center text-gray-600 text-xs mb-4 gap-y-1">

                            {{-- Stars --}}
                            <div class="flex items-center gap-1 mr-3">

                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <=floor($rating))
                                    <svg class="w-4 h-4 fill-current text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                    </svg>

                                    @elseif ($i - $rating < 1)

                                        <svg class="w-4 h-4 fill-current text-yellow-300" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                        </svg>

                                        @else

                                        <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                        </svg>

                                        @endif
                                        @endfor

                                        <span>{{ number_format($rating, 1) }}</span>
                            </div>

                            {{-- Reviews --}}
                            <span>
                                ({{ $reviewsCount }} {{ __('product/product_show.reviews') }})
                            </span>

                            {{-- Sold --}}
                            @if($soldCount > 0)
                            <span class="mx-2 hidden sm:inline">•</span>

                            <span>
                                {{ __('product/product_show.sold') }}:
                                {{ $soldCount }}
                            </span>
                            @endif

                        </div>

                    </div>


                    {{-- RIGHT BLOCK ACTIONS --}}
                    <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end gap-3 lg:min-w-[200px]">

                    @auth 
                    @can('addToProject', $product1)

                        {{-- Add to project --}}
                        <div class="w-full sm:w-auto lg:w-[180px] text-left sm:text-left lg:text-right mb-2">

                            <button
                                @click="showProjectBox = !showProjectBox"
                                title="Add to project"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                           px-4 py-2
                           rounded-lg
                           bg-gray-500 text-white
                           text-sm font-semibold
                           shadow-sm
                           hover:bg-gray-700 hover:shadow
                           transition">

                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-700/50">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>

                                <span>Add to project</span>

                            </button>

                            <p class="mt-1 text-xs text-gray-500 leading-snug">
                                Organize your products into projects — create a project in your dashboard first.
                            </p>

                        </div>


                        @endcan 
                        
                        @else

                        {{-- Add to project for guest--}}
                        <div class="w-full sm:w-auto lg:w-[180px] text-left sm:text-left lg:text-right mb-2">

                            <button
                                type="button" onclick="dispatchAlert( 'guest', 'Please register or log in to add products to a project.' )" title="Add to project"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                           px-4 py-2
                           rounded-lg
                           bg-gray-500 text-white
                           text-sm font-semibold
                           shadow-sm
                           hover:bg-gray-700 hover:shadow
                           transition">

                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-700/50">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>

                                <span>Add to project</span>

                            </button>

                            <p class="mt-1 text-xs text-gray-500 leading-snug">
                                Organize your products into projects — create a project in your dashboard first.
                            </p>

                        </div>


                        @endauth

                        {{-- Edit --}}
                        @can('update', $product1)
                        <a href="{{ route('supplier.products.edit-step', [$product1->id, 1]) }}"
                            class="w-full sm:w-auto text-center
                      inline-flex items-center justify-center gap-2
                      px-4 py-2
                      text-sm font-medium
                      text-blue-700
                      border border-blue-600
                      rounded-lg
                      hover:bg-blue-600 hover:text-white
                      transition">

                            Edit

                        </a>
                        @endcan
                        <div class="text-[10px] uppercase mb-2 ">Артикул:
                            <span class="bg-yellow-900 text-white px-1 py-0 rounded text-[10px]">
                                {{ $product1->sku }}
                            </span>
                        </div>

                    </div>

                </div>










                @include('product.partials.notification')


                @include('product.partials.add-to-project', ['product1' => $product1, 'projects' => $projects])

                @include('product.partials.price-table', ['product1' => $product1])








                {{-- Variants --}}
                @if($product1->variantGroup && $product1->variantGroup->items->isNotEmpty())
                <div class="mb-6">
                    <h3 class="font-semibold text-lg">Variants</h3>
                    <span class="text-xs text-gray-500 leading-tight mb-6 block">
                        {{ __('product/product_show.shipping_cost_not_included') }}

                    </span>
                    <div class="flex flex-wrap gap-3">

                        @php
                        $variantItems = $product1->variantGroup->items;

                        // Добавляем родителя, если его нет в items
                        if (!$variantItems->contains('product_id', $product1->id)) {
                        $dummyItem = new \App\Models\ProductVariantItem([
                        'product_id' => $product1->id,
                        'title' => $product1->name,
                        'media_id' => $product1->variantPreview?->id, // preview для родителя
                        ]);
                        $variantItems->prepend($dummyItem);
                        }
                        @endphp

                        @foreach($variantItems as $variantItem)
                        @php
                        $variantProduct = $variantItem->product;
                        if (!$variantProduct) continue;

                        $link = route('product.show', $variantProduct->slug);
                        $title = $variantItem->title ?? $variantProduct->name ?? 'Variant';

                        // 🔹 Берём preview из ProductVariantItem
                        $previewUrl = $variantItem->media
                        ? asset('storage/' . $variantItem->media->variantPath('thumb'))
                        : null;

                        $isActive = $variantProduct->id == $product1->id;


                        @endphp

                        <a href="{{ $link }}" class="variant-btn w-24 flex flex-col items-center gap-1">

                            <div class="w-24 h-24 rounded-md border border-gray-300 shadow-sm hover:border-black transition flex items-center justify-center
                                {{ $isActive ? 'border-2 border-blue-600 ring-2 ring-blue-600' : '' }}">

                                @if($previewUrl)
                                <img src="{{ $previewUrl }}"
                                    alt="{{ $variantItem->title ?? $variantItem->product->name }}"
                                    class="w-24 h-24 object-cover rounded">
                                @else
                                <div class="text-gray-400 text-xs text-center">
                                    No Image
                                </div>
                                @endif
                            </div>

                            <span class="text-sm text-center">
                                {{ $title }}
                            </span>

                        </a>

                        @endforeach

                    </div>
                </div>
                @endif




                {{-- Description --}}
                @if(!empty($product1->description))
                <p class="text-gray-700 mb-6 leading-relaxed">{{ $product1->description }}</p>
                @endif


           {{-- Product Attributes --}}
@if($product1->attributeValues->count())

<div class="bg-white rounded-xl shadow p-6 mb-6">

    <h3 class="font-semibold text-lg mb-2 leading-none">
        {{ __('product/product_show.specification') }}
    </h3>

    <p class="text-sm text-gray-500 leading-tight">
        {{ __('product/product_show.shipping_cost_not_included') }}
    </p>

    @php
    /*
    |--------------------------------------------------------------------------
    | FILTER HIDDEN BOOLEAN ATTRIBUTES
    |--------------------------------------------------------------------------
    |
    | Boolean = 0 → полностью не показываем.
    | Boolean = 1 → показываем как Yes.
    |
    */

    $attributeValues = $product1->attributeValues
        ->filter(function ($attrValue) {

            $attribute = $attrValue->attribute;

            if (!$attribute) {
                return false;
            }


 /*
            |--------------------------------------------------------------------------
            | MEASUREMENT ATTRIBUTES
            |--------------------------------------------------------------------------
            |
            | Размеры выводятся в отдельном блоке.
            | Поэтому здесь их исключаем из Specification.
            |
            */

            if ($attribute->type === 'measurement') {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | BOOLEAN ATTRIBUTES
            |--------------------------------------------------------------------------
            |
            | Boolean = 0 → не показываем.
            | Boolean = 1 → показываем.
            |
            */


            if ($attribute->type === 'boolean') {

                $value = $attrValue->translations
                    ->firstWhere('locale', app()->getLocale())
                    ?->value;

                // Если перевода текущего языка нет —
                // берём первый доступный
                if ($value === null || $value === '') {
                    $value = $attrValue->translations
                        ->first()
                        ?->value;
                }

                return in_array(
                    strtolower(trim((string) $value)),
                    ['1', 'true', 'yes', 'on', 'y'],
                    true
                );
            }

            return true;
        })
        ->values();

    $visibleAttributes = $attributeValues->take(8);
    $hiddenAttributes = $attributeValues->slice(8);
@endphp

    @if($attributeValues->count())

        <div class="relative mt-2">

            <ul
                id="product-attributes-list"
                class="divide-y divide-gray-200 text-gray-700"
            >

                {{-- FIRST 8 --}}
                @foreach($visibleAttributes as $attrValue)

                    <li class="flex justify-between py-2">

                        <span class="text-gray-600">
                            {{ $attrValue->attribute->name ?? $attrValue->attribute->code }}
                        </span>

                        <span class="font-medium text-gray-900">

                            @php
                                $attribute = $attrValue->attribute;
                                $unit = $attribute?->unit;

                                $displayValue = $attrValue->display_value;

                                $unitName = $unit?->translations
                                    ?->firstWhere('locale', app()->getLocale())
                                    ?->name;

                                // Fallback на английский
                                if (!$unitName) {
                                    $unitName = $unit?->translations
                                        ?->firstWhere('locale', 'en')
                                        ?->name;
                                }

                                // Последний fallback
                                $unitName = $unitName
                                    ?: $unit?->name
                                    ?: $unit?->code;
                            @endphp

                            {{ $displayValue }}{{ $unitName ? ' ' . $unitName : '' }}

                        </span>

                    </li>

                @endforeach


                {{-- HIDDEN ATTRIBUTES --}}
                @if($hiddenAttributes->count())

                    <div
                        id="hidden-product-attributes"
                        class="hidden"
                    >

                        @foreach($hiddenAttributes as $attrValue)

                            <li class="flex justify-between py-2">

                                <span class="text-gray-600">
                                    {{ $attrValue->attribute->name ?? $attrValue->attribute->code }}
                                </span>

                                <span class="font-medium text-gray-900">

                                    @php
                                        $attribute = $attrValue->attribute;
                                        $unit = $attribute?->unit;

                                        $displayValue = $attrValue->display_value;

                                        $unitName = $unit?->translations
                                            ?->firstWhere('locale', app()->getLocale())
                                            ?->name;

                                        // Fallback на английский
                                        if (!$unitName) {
                                            $unitName = $unit?->translations
                                                ?->firstWhere('locale', 'en')
                                                ?->name;
                                        }

                                        // Последний fallback
                                        $unitName = $unitName
                                            ?: $unit?->name
                                            ?: $unit?->code;
                                    @endphp

                                    {{ $displayValue }}{{ $unitName ? ' ' . $unitName : '' }}

                                </span>

                            </li>

                        @endforeach

                    </div>

                @endif

            </ul>


            {{-- BLUR + SHOW ALL BUTTON --}}
            @if($hiddenAttributes->count())

                <button
                    type="button"
                    id="product-attributes-toggle"
                    class="relative w-full mt-0 h-12 flex items-end justify-center group"
                    aria-expanded="false"
                >

                    {{-- Blur --}}
                    <div
                        id="product-attributes-blur"
                        class="absolute inset-x-0 bottom-0 h-14
                               bg-gradient-to-t
                               from-white
                               via-white/90
                               to-transparent
                               pointer-events-none"
                    ></div>


                    {{-- Button Content --}}
                    <span
                        class="relative z-10
                               inline-flex items-center gap-2
                               px-4 py-2
                               rounded-lg
                               bg-white
                               border border-gray-200
                               shadow-sm
                               text-sm font-medium
                               text-gray-700
                               transition
                               group-hover:text-gray-900
                               group-hover:border-gray-300"
                    >

                        <span id="product-attributes-toggle-text">
                            Show all specifications
                        </span>

                        <span
                            class="flex items-center justify-center
                                   w-5 h-5"
                        >

                            <svg
                                id="product-attributes-arrow"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 transition-transform duration-200"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>

                        </span>

                    </span>

                </button>

            @endif

        </div>

    @endif

</div>

@endif


                @include('product.partials.materials-table', ['product1' => $product1])




{{-- =========================================================
    PRODUCT DIMENSIONS
========================================================= --}}

@php

    $measurementAttributes = $product1->attributeValues
    ->filter(function ($attrValue) {

        $value = $attrValue->display_value;

        return $attrValue->attribute?->type === 'measurement'
            && filled($value)
            && (float) $value > 0;

    })
    ->values();

@endphp


@if($measurementAttributes->isNotEmpty())

<div class="mb-6">

    <div class="relative overflow-hidden rounded-2xl bg-white shadow">

        {{-- =====================================================
            HEADER
        ====================================================== --}}

        <div class="px-6 pt-6">

            <div class="flex items-center justify-between">

                <div>

                    <h3 class="text-lg font-semibold text-gray-900">
                        Dimensions
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Product measurements
                    </p>

                </div>

                <div
                    class="text-[10px]
                           font-medium
                           uppercase
                           tracking-[0.18em]
                           text-gray-400"
                >
                    DIM
                </div>

            </div>

        </div>


       


        {{-- =====================================================
            VALUES
        ====================================================== --}}

        <div class="p-6">

            <div
                class="grid
                       grid-cols-2
                       md:grid-cols-4
                       gap-x-6
                       gap-y-5"
            >

                @foreach($measurementAttributes as $attrValue)

                    @php

                        $attribute = $attrValue->attribute;

                        $unit = $attrValue->unit
                            ?? $attribute?->unit;

                        $unitName = $unit?->translations
                            ?->firstWhere(
                                'locale',
                                app()->getLocale()
                            )
                            ?->name;

                        $unitName ??= $unit?->translations
                            ?->firstWhere('locale', 'en')
                            ?->name;

                        $unitName ??=
                            $unit?->name
                            ?? $unit?->code;

                    @endphp


                    <div>

                        <div
                            class="text-[11px]
                                   text-gray-500
                                   mb-1"
                        >
                            {{ $attribute->name ?? $attribute->code }}
                        </div>

                        <div
                            class="flex
                                   items-baseline
                                   gap-1.5"
                        >

                            <span
                                class="text-lg
                                       font-semibold
                                       tracking-tight
                                       text-gray-900"
                            >
                                {{ $attrValue->display_value }}
                            </span>

                            @if($unitName)

                                <span
                                    class="text-xs
                                           text-gray-400"
                                >
                                    {{ $unitName }}
                                </span>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endif



                {{-- Commercial Terms --}}
                <div class="bg-[#F7F3EA] border border-gray-200 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm text-gray-700">
                        <div>
                            <p class="text-gray-500">{{ __('product/product_show.MOQ') }}</p>
                            <p class="font-semibold text-gray-900">
                                {{ $product1->moq ?? 'N/A' }} {{ __('product/product_show.pcs') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('product/product_show.lead_time') }}</p>
                            <p class="font-semibold text-gray-900">
                                {{ $product1->lead_time ?? 'N/A' }} {{ __('product/product_show.days') }}
                            </p>
                        </div>
                        <div>

                            
                        
                        

@auth

    @if($product1->customization)

        @can('customize', $product1)

            <button
                @click="showCustomizationBox = !showCustomizationBox"
                title="Need Customization"
                class="w-full flex items-center justify-center gap-2
                       px-4 py-2
                       rounded-lg
                       bg-gray-500 text-white
                       text-sm font-semibold
                       shadow-sm
                       hover:bg-gray-700 hover:shadow
                       transition">

                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-700/50 shrink-0">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"/>

                    </svg>

                </span>

                <span class="truncate">Customization</span>

            </button>

        @endcan

    @else

        <p class="text-gray-500">
            {{ __('product/product_show.customization') }}
        </p>

        <p class="font-semibold text-gray-900">
            Not available
        </p>

    @endif

@else

    @if($product1->customization)

        <button
            type="button"
            onclick="dispatchAlert(
                'guest',
                'Please register or log in to request product customization.'
            )"
            title="Need Customization"
            class="w-full flex items-center justify-center gap-2
                   px-4 py-2
                   rounded-lg
                   bg-gray-500 text-white
                   text-sm font-semibold
                   shadow-sm
                   hover:bg-gray-700 hover:shadow
                   transition">

            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-700/50 shrink-0">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"/>

                </svg>

            </span>

            <span class="truncate">Customization</span>

        </button>

    @else

        <p class="text-gray-500">
            {{ __('product/product_show.customization') }}
        </p>

        <p class="font-semibold text-gray-900">
            Not available
        </p>

    @endif

@endauth







                            </p>
                        </div>
                    </div>
                </div>






                @include('product.partials.customization')









                @include('product.partials.shippingtemplates-table', ['product1' => $product1])





@auth

@can('addToProject', $product1)
                {{-- CTA Panel --}}
                <div class="mt-4 bg-white border border-gray-200 rounded-2xl p-6 shadow-lg mb-6">

                    <form method="POST" action="{{ route('buyer.cart.add.redirect', $product1->id) }}">
                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-blue-950 hover:bg-blue-900 text-white py-4 rounded-xl
                                    text-lg font-semibold tracking-wide shadow-md transition-all transform hover:scale-105 mb-4">
                            {{ __('product/product_show.checkout') }}
                        </button>
                    </form>

                    <div class="grid grid-cols-2 gap-4">
                        <button class="open-conversation w-full border border-gray-300 py-3 rounded-xl
                                   text-gray-800 font-medium shadow-sm
                                   hover:border-black hover:text-black hover:shadow-md transition-all transform hover:scale-105" data-subject-type="App\Models\Product"
                                    data-subject-id="{{ $product1->id }}">
                            {{ __('product/product_show.contact_supllire') }}
                        </button>

                        

                            <x-conversation.drawer
                                subjectType="App\Models\Product"
                                :subjectId="$product1->id"
                                :messagesUrl="url('/dashboard/buyer/messenger/conversations')"
                            />


                        <form method="POST" action="{{ route('buyer.cart.add', $product1->id) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full border border-gray-300 py-3 rounded-xl
                                        text-gray-800 font-medium shadow-sm
                                        hover:border-black hover:text-black hover:shadow-md
                                        transition-all transform hover:scale-105">
                                {{ __('product/product_show.add_to_cart') }}
                            </button>
                        </form>




                    </div>



                </div>

@endcan

@else
<div class="mt-4 bg-white border border-gray-200 rounded-2xl p-6 shadow-lg mb-6">

                    

                        <button
                            type="button"
                            onclick="dispatchAlert(
            'guest',
            'Please register or log in to proceed to checkout.'
        )"
                            class="w-full bg-blue-950 hover:bg-blue-900 text-white py-4 rounded-xl
                                    text-lg font-semibold tracking-wide shadow-md transition-all transform hover:scale-105 mb-4">
                            {{ __('product/product_show.checkout') }}
                        </button>
                 

                    <div class="grid grid-cols-2 gap-4">
                        <button 
                        type="button"
                        onclick="dispatchAlert(
                'guest',
                'Please register or log in to contact the supplier.'
            )"
                        class="w-full border border-gray-300 py-3 rounded-xl
                                   text-gray-800 font-medium shadow-sm
                                   hover:border-black hover:text-black hover:shadow-md transition-all transform hover:scale-105" data-subject-type="App\Models\Product"
                                    data-subject-id="{{ $product1->id }}">
                            {{ __('product/product_show.contact_supllire') }}
                        </button>

                        

                            <x-conversation.drawer
                                subjectType="App\Models\Product"
                                :subjectId="$product1->id"
                                :messagesUrl="url('/dashboard/buyer/messenger/conversations')"
                            />


                        
                            @csrf
                            <button
                                type="button"
                                onclick="dispatchAlert(
                'guest',
                'Please register or log in to add products to your cart.'
            )"
                                class="w-full border border-gray-300 py-3 rounded-xl
                                        text-gray-800 font-medium shadow-sm
                                        hover:border-black hover:text-black hover:shadow-md
                                        transition-all transform hover:scale-105">
                                {{ __('product/product_show.add_to_cart') }}
                            </button>
                     




                    </div>



                </div>


@endif

                <p class="text-gray-700 mb-2 leading-relaxed">{{ __('product/product_show.place_of_origin') }} <strong>{{ $product1->country?->name ?? 'Country not specified' }}</strong>
                </p>

                {{-- Supplier Info --}}

                


                @php
                    $supplier = $product1->supplier;
                @endphp

                

                @if($supplier)

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

                    {{-- HEADER --}}
                    <div class="relative overflow-hidden px-6 py-7 border-b border-gray-200 bg-white">

                        @php
                            $level = $supplier->level;

                            $supplierRating = round(
                                $supplier->supplierReviews->avg('rating') ?? 0,
                                1
                            );

                            $glow = match($level) {
                                'Silver'   => 'linear-gradient(135deg,#d1d5db,#f3f4f6)',
                                'Gold'     => 'linear-gradient(135deg,#f59e0b,#fde68a)',
                                'Platinum' => 'linear-gradient(135deg,#1f2937,#6b7280)',
                                default    => 'linear-gradient(135deg,#e5e7eb,#f9fafb)',
                            };
                        @endphp

                        <div class="absolute inset-0 pointer-events-none overflow-hidden">

                            <div
                                class="absolute -top-20 -right-16 w-52 h-52 rounded-full blur-3xl opacity-20"
                                style="background: {{ $glow }};">
                            </div>

                            <div
                                class="absolute -bottom-20 -left-16 w-56 h-56 rounded-full blur-3xl opacity-10"
                                style="background: {{ $glow }};">
                            </div>

                        </div>

                        <div class="relative flex flex-col items-center text-center">

                            <a
                                href="{{ route('supplier.show', $supplier->slug) }}"
                                class="w-20 h-20 rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm">

                                <img
                                    src="{{ $supplier->logo?->cdn_url ?? asset('images/no-logo.png') }}"
                                    class="w-full h-full object-cover">

                            </a>

                            <a
                                href="{{ route('supplier.show', $supplier->slug) }}"
                                class="mt-4 text-lg font-semibold text-gray-900 hover:text-emerald-700 transition">

                                {{ $supplier->name }}

                            </a>

                            <div class="mt-2">

                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold

                                    {{ $level === 'Basic' ? 'bg-gray-100 text-gray-600 border border-gray-200' : '' }}
                                    {{ $level === 'Silver' ? 'bg-gray-200 text-gray-700 border border-gray-300' : '' }}
                                    {{ $level === 'Gold' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                    {{ $level === 'Platinum' ? 'bg-slate-900 text-white border border-slate-700' : '' }}">

                                    {{ strtoupper($level) }} SUPPLIER

                                </span>

                            </div>

                            <div class="mt-4 flex items-center gap-1">

                                @for($i=1;$i<=5;$i++)
                                    @if($i <= floor($supplierRating))
                                        <svg class="w-4 h-4 fill-yellow-500" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 fill-gray-300" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                                        </svg>
                                    @endif
                                @endfor

                                <span class="ml-2 text-sm text-gray-500">
                                    {{ number_format($supplierRating,1) }}
                                    ({{ $supplier->supplierReviews->count() }})
                                </span>

                            </div>

                            @if($supplier->country)
                                <div class="mt-3 text-sm text-gray-500">
                                    {{ $supplier->country->name }}
                                </div>
                            @endif

                            <a
                                href="{{ route('supplier.show', $supplier->slug) }}"
                                class="mt-6 inline-flex items-center justify-center w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">

                                Visit Supplier Profile

                                <svg
                                    class="ml-2 w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 5l7 7-7 7"/>

                                </svg>

                            </a>

                        </div>

                    </div>

                </div>

                @endif

            </div>
        </div>
    </div>
</section>


{{-- Chat Drawer --}}
<div id="chatDrawer"
    class="fixed top-0 right-0 h-full w-96 bg-white shadow-xl transform translate-x-full transition-transform duration-300 z-50 flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-semibold text-lg">{{ __('product/product_show.chat_with') }} {{ $product1->supplier->name }}</h3>
        <button id="closeChat" class="text-gray-500 hover:text-black">&times;</button>
    </div>

    {{-- Messages --}}
    <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-4">
        <div class="flex items-center gap-4 max-w-full" id="messages-product">

            {{-- Image --}}
            @if($product1->image_url)
            <img
                src="{{ $product1->image_url }}"
                alt="{{ $product1->name }}"
                class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
            @endif

            {{-- Text --}}
            <div class="flex flex-col">
                <span class="text-sm font-semibold text-gray-900 leading-tight">
                    {{ $product1->name }}
                </span>

                @if($product1->category)
                <span class="text-xs text-gray-500 mt-1">
                    {{ $product1->category->name }}
                </span>
                @endif

                <span class="text-xs text-gray-400 mt-2">
                    by {{ $product1->supplier->name }}
                </span>
            </div>

        </div>
    </div>


    {{-- Input --}}
    <div class="p-4 border-t">

        <form id="chatForm" class="flex gap-3">
            <input type="text" name="text" placeholder="{{ __('product/product_show.type_your_message') }}"
                class="flex-1 border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
            <button type="submit"
                class="bg-[#23423F] text-white px-4 py-2 rounded-lg text-sm hover:bg-[#1D2D33]">
                {{ __('product/product_show.send') }}
            </button>
        </form>

    </div>
</div>


<x-conversation.drawer
    subject-type="App\Models\Product"
    :subject-id="$product1->id"
/>



@if($product1->attributeValues->count() > 8)
<script>
document.addEventListener('DOMContentLoaded', function () {

    const toggle = document.getElementById('product-attributes-toggle');
    const hidden = document.getElementById('hidden-product-attributes');
    const blur = document.getElementById('product-attributes-blur');
    const arrow = document.getElementById('product-attributes-arrow');

    if (!toggle || !hidden) {
        return;
    }

    toggle.addEventListener('click', function () {

        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        if (isExpanded) {

            // CLOSE
            hidden.classList.add('hidden');

            blur.classList.remove('hidden');

            arrow.classList.remove('rotate-180');

            toggle.setAttribute('aria-expanded', 'false');

        } else {

            // OPEN
            hidden.classList.remove('hidden');

            blur.classList.add('hidden');

            arrow.classList.add('rotate-180');

            toggle.setAttribute('aria-expanded', 'true');
        }

    });

});
</script>
@endif



<script>
    // Если хочешь, можно сделать клик по кнопке для перехода на связанный товар
    document.querySelectorAll('.color-option').forEach(btn => {
        btn.addEventListener('click', () => {
            const link = btn.dataset.link;
            if (link && link !== '#') {
                window.location.href = link;
            }
        });
    });
</script>

<script>
    const contactBtn = document.getElementById('contactSupplierBtn');
    const chatDrawer = document.getElementById('chatDrawer');
    const closeChat = document.getElementById('closeChat');

    contactBtn.addEventListener('click', () => {
        chatDrawer.classList.remove('translate-x-full');
        chatDrawer.classList.add('translate-x-0');
    });

    closeChat.addEventListener('click', () => {
        chatDrawer.classList.add('translate-x-full');
        chatDrawer.classList.remove('translate-x-0');
    });
</script>




<script>
    const colorOptions = document.querySelectorAll('.color-option');

    colorOptions.forEach(option => {
        option.addEventListener('click', () => {

            // remove active state from all
            colorOptions.forEach(o =>
                o.classList.remove('ring-2', 'ring-blue-900')
            );

            // add active state
            option.classList.add('ring-2', 'ring-blue-900');

            // change main image
            if (option.dataset.image) {
                mainImage.src = option.dataset.image;
            }
        });
    });
</script>

<script>
    window.productGallery = @json($gallery);
</script>

<script>

document.addEventListener('click', async function (e) {

    const btn = e.target.closest('.wishlist-toggle');
    if (!btn) return;

    const productId = btn.dataset.productId;

    try {

        const response = await fetch(`/buyer/wishlist/toggle/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        const icon = btn.querySelector('.wishlist-icon');

        if (data.status === 'added') {

    icon.classList.add('text-red-500');
    icon.classList.remove('text-gray-500');

    icon.setAttribute('fill', 'currentColor');

} else {

    icon.classList.add('text-gray-500');
    icon.classList.remove('text-red-500');

    icon.setAttribute('fill', 'none');
}

        updateWishlistBadge();

    } catch (error) {
        console.error(error);
    }

});

</script>

<script>

async function updateWishlistBadge() {

    try {

        const response = await fetch('/buyer/wishlist/count');
        const data = await response.json();

        const badge = document.querySelector('#wishlist-count');

        if (!badge) return;

        if (data.count > 0) {

            badge.textContent = data.count;
            badge.classList.remove('hidden');

        } else {

            badge.classList.add('hidden');
        }

    } catch (error) {
        console.error(error);
    }

}

/**
 * 🔥 ВАЖНО: инициализация состояния при загрузке страницы
 * (вот чего тебе не хватало — из-за этого после refresh не подсвечивалось)
 */
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.wishlist-toggle').forEach(btn => {

        const icon = btn.querySelector('.wishlist-icon');

        const isActive =
            icon.classList.contains('text-red-500');

        // если уже активен — оставляем красным
        if (isActive) {
            icon.classList.add('text-red-500');
            icon.classList.remove('text-gray-400');
        }

    });

    updateWishlistBadge();
});

</script>
{{-- MediaViewer компонент --}}
<x-media-viewer id="productViewer" :images="$gallery"></x-media-viewer>

@vite('resources/js/product-gallery.js')

@endsection