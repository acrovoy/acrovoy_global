@extends('layouts.app')
@section('content')

<section class="bg-[#F7F3EA] py-4">
    <div class="container mx-auto px-6">

    <x-alerts />


        @include('product.partials.breadcrumb', ['product1' => $product1, 'projects' => $projects])

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">

            {{-- Галерея продукта --}}
            <div>
            @include('product.partials.gallery', ['product1' => $product1, 'projects' => $projects])


            


{{-- PRODUCT CUSTOMIZATION ABILITY --}}

@php
    /*
    |--------------------------------------------------------------------------
    | TEMPORARY STUBS — replace with real product data
    |--------------------------------------------------------------------------
    */

    
    

    // TODO: $product1->customization_description
    $customizationDescription = 'Велюр — на дотик оксамитова тканина з невеликим ворсом. '
        .'Велюр відрізняється благородним зовнішнім виглядом. '
        .'Прекрасно підходить для створення вишуканого інтер\'єру.';

    // TODO: $product1->variations — flat list, 3 rows × 7 columns = 21 swatches.
    // Add a real `image` per item later; without it the placeholder is used.
    $variations = [
        // Row 1 — named colours
        ['code' => '01', 'name' => 'Beige'],
        ['code' => '02', 'name' => 'Cream'],
        ['code' => '03', 'name' => 'Caramel'],
        ['code' => '04', 'name' => 'Coffee'],
        ['code' => '02', 'name' => 'Cream'],
        ['code' => '03', 'name' => 'Caramel'],
        ['code' => '04', 'name' => 'Coffee'],
        // Row 2 — code only
        ['code' => '00', 'name' => null],
        ['code' => '01', 'name' => null],
        ['code' => '02', 'name' => null],
        ['code' => '03', 'name' => null],
        ['code' => '04', 'name' => null],
        ['code' => '05', 'name' => null],
        ['code' => '06', 'name' => null],
        // Row 3 — code only
        ['code' => '00', 'name' => null],
        ['code' => '01', 'name' => null],
        ['code' => '02', 'name' => null],
        ['code' => '03', 'name' => null],
        ['code' => '04', 'name' => null],
        ['code' => '05', 'name' => null],
        ['code' => '06', 'name' => null],
    ];

    $swatchPlaceholder = 'images/gallery/sample.png';

    // TODO: $product1->customization_available
    $customizationAvailable = true;
@endphp

<div class="p-6"
     x-data="{ selected: {}, showProjectBox: false, showCustomizationBox: false }">

    {{-- HEADER ROW : TITLE + ABILITIES (left) / ACTIONS (right) --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

        {{-- LEFT : TITLE + ABILITY LIST --}}
        <div class="min-w-0 flex-1">
@if($product1->customization)
            <h3 class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-[18px] font-semibold text-gray-900">

                <span>Supplier's Сustomization</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="58" height="15" viewBox="0 0 58 15" fill="none">

    <!-- A -->
    <path
        d="M1133 0 1008 360H471L346 0H51L565 1409H913L1425 0ZM942 582 803 987 739 1192Q723 1134 709 1088Q695 1042 537 582Z"
        transform="translate(0 11.55) scale(0.0092 -0.0075)"
        fill="#008EFF"
    />

    <!-- b -->
    <path
        d="M1167 545Q1167 277 1059.5 128.5Q952 -20 752 -20Q637 -20 553 30Q469 80 424 174H422Q422 139 417.5 78Q413 17 408 0H135Q143 93 143 247V1484H424V1070L420 894H424Q519 1102 770 1102Q962 1102 1064.5 956.5Q1167 811 1167 545ZM874 545Q874 729 820 818Q766 907 653 907Q539 907 479.5 811.5Q420 716 420 536Q420 364 478.5 268Q537 172 651 172Q874 172 874 545Z"
        transform="translate(13.6068 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- i -->
    <path
        d="M143 0V1082H424V0ZM143 1277V1484H424V1277Z"
        transform="translate(25.115 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- l -->
    <path
        d="M143 0V1484H424V0Z"
        transform="translate(30.350 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- i -->
    <path
        d="M143 0V1082H424V0ZM143 1277V1484H424V1277Z"
        transform="translate(35.585 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- t -->
    <path
        d="M420 -18Q296 -18 229 49.5Q162 117 162 254V892H25V1082H176L264 1336H440V1082H645V892H440V330Q440 251 470 213.5Q500 176 563 176Q596 176 657 190V16Q553 -18 420 -18Z"
        transform="translate(40.820 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- y -->
    <path
        d="M283 -425Q182 -425 106 -412V-212Q159 -220 203 -220Q263 -220 302.5 -201Q342 -182 373.5 -138Q405 -94 444 11L16 1082H313L483 575Q523 466 584 241L609 336L674 571L834 1082H1128L700 -57Q614 -265 521.5 -345Q429 -425 283 -425Z"
        transform="translate(47.094 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

</svg>

                

                
            </h3>
@endif
           @if($customAbilityAttributes->isNotEmpty())

    <ul class="mt-4 space-y-1.5 pl-5 list-disc text-sm text-gray-700">

        @foreach($customAbilityAttributes as $attrValue)

            @foreach($attrValue->options as $valueOption)

                @php
                    $option = $valueOption->option;
                @endphp

                @if($option)
                    <li class="leading-snug">
                        {{ $option->translatedValue() }}
                    </li>
                @endif

            @endforeach

        @endforeach

    </ul>

@endif

        </div>

        {{-- RIGHT : ACTIONS --}}
        <div class="flex flex-col items-start sm:items-end gap-3 lg:min-w-[200px] shrink-0">

            @auth
                @can('addToProject', $product1)

                    {{-- ADD TO PROJECT — logged in with permission --}}
                    <div class="w-full sm:w-auto lg:w-[180px] text-left sm:text-left lg:text-right mb-2">

                        <button
                            type="button"
                            @click="showProjectBox = !showProjectBox"
                            title="Add to project"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                               px-4 py-2
                               rounded-lg
                               bg-gray-700 text-white
                               text-sm font-semibold
                               shadow-sm
                               hover:bg-gray-900 hover:shadow
                               transition">

                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />

                                </svg>
                            </span>

                            <span>Add to project</span>

                        </button>

                        <p class="mt-1 text-xs text-gray-500 leading-snug">
                            Organize your products into projects — create a project in your dashboard first.
                        </p>

                    </div>






                    {{-- CUSTOMIZATION --}}
            @if($product1->customization)

                <div class="w-full sm:w-auto lg:w-[180px] text-left sm:text-left lg:text-right">

                    <button
                        type="button"
                        @click="showCustomizationBox = !showCustomizationBox"
                        title="Customization"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                               px-4 py-2
                               rounded-lg
                               bg-gray-700 text-white
                               text-sm font-semibold
                               shadow-sm
                               hover:bg-gray-900 hover:shadow
                               transition">

                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />

                            </svg>
                        </span>

                        <span>Customization</span>

                    </button>

                </div>

            @endif




                @endcan
            @else

                {{-- ADD TO PROJECT — guest --}}
                <div class="w-full sm:w-auto lg:w-[180px] text-left sm:text-left lg:text-right mb-2">

                    <button
                        type="button"
                        onclick="dispatchAlert('guest', 'Please register or log in to add products to a project.')"
                        title="Add to project"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2
                               px-4 py-2
                               rounded-lg
                               bg-gray-700 text-white
                               text-sm font-semibold
                               shadow-sm
                               hover:bg-gray-900 hover:shadow
                               transition">

                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />

                            </svg>
                        </span>

                        <span>Add to project</span>

                    </button>

                    <p class="mt-1 text-xs text-gray-500 leading-snug">
                        Organize your products into projects — create a project in your dashboard first.
                    </p>

                </div>



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

            


            @include('product.partials.customization')


            @include('product.partials.add-to-project', ['product1' => $product1, 'projects' => $projects ?? collect()])

        </div>

    </div>


    {{-- Commercial Terms --}}
                
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm text-gray-700">
                        <div>
                            <span class="text-amber-800">{{ __('product/product_show.MOQ') }}:</span>
                            <span class="font-semibold text-gray-600">
                                {{ $product1->moq ?? 'N/A' }} {{ __('product/product_show.pcs') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-amber-800">{{ __('product/product_show.lead_time') }}:</span>
                            <span class="font-semibold text-gray-600">
                                {{ $product1->lead_time ?? 'N/A' }} {{ __('product/product_show.days') }}
                            </span>
                        </div>
                        
                    </div>
                

    {{-- DESCRIPTION --}}
    @if($customizationDescription)
        <p class="mt-5 max-w-2xl text-sm leading-relaxed text-gray-600">
            {{ $customizationDescription }}
        </p>
    @endif

    {{-- VARIATION SWATCHES : 3 rows × 7 columns --}}
    @if(!empty($variations))
        <div class="mt-5 grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-7 gap-x-3 gap-y-4">

            @foreach($variations as $item)

                @php
                    $key   = 'v'.$loop->index.'-'.($item['code'] ?? '');
                    $label = trim(($item['code'] ?? '').' '.($item['name'] ?? ''));
                    $src   = $item['image'] ?? $swatchPlaceholder;
                @endphp

                <button
                    type="button"
                    @click="selected['{{ $key }}'] = !selected['{{ $key }}']"
                    title="{{ $label ?: 'Variation' }}"
                    class="group block w-full text-left focus:outline-none">

                    <div
                        class="w-full aspect-square overflow-hidden rounded-md border border-gray-200 bg-gray-50 transition"
                        :class="selected['{{ $key }}']
                            ? 'ring-2 ring-gray-900 ring-offset-2'
                            : 'group-hover:border-gray-400'">

                        <img
                            src="{{ asset($src) }}"
                            alt="{{ $label ?: 'Variation' }}"
                            loading="lazy"
                            class="w-full h-full object-cover">

                    </div>

                    @if($label)
                        <div class="mt-1.5 text-center text-[11px] leading-tight text-gray-600">

                            {{ $label }}

                        </div>
                    @endif

                </button>

            @endforeach

        </div>
    @endif

    

    {{-- SELECT VARIATIONS --}}
    <div class="mt-6 flex justify-center">

        <button
            type="button"
            @click="$dispatch('open-variations-modal')"
            title="More materials and color"
            class="inline-flex items-center justify-center rounded-full border-2 border-gray-800 bg-white
                   px-2 py-0.5 text-sm font-medium text-gray-900
                   transition hover:bg-gray-100 hover:border-gray-900
                   focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2">

            See more supplier's materials and color variations

        </button>

    </div>

</div>
</div>

            


            {{-- Info --}}
            <div class="rounded-xl shadow p-6" x-data="{ showCustomizationBox: false }">

                <div class="flex flex-col lg:flex-row lg:items-start gap-4">

                    {{-- LEFT BLOCK --}}
                    <div class="flex-1">

                    @include('product.partials.title', ['product1' => $product1, 'projects' => $projects])    


                    <p class="text-gray-700 leading-relaxed">
                                                <span class="truncate max-w-full font-normal !text-gray-600 text-[13px] leading-4 ms-1">
                                                    {{ $soldCount }}
                                                    {{ __('product/product_show.sold') }}
                                                    
                                                </span>

                                                <span class="text-gray-300 text-xs mx-2 hidden sm:inline ">|</span>

                                                <span class="truncate max-w-full font-normal !text-[#AD5F00] text-[13px] leading-4 ms-1">
                                                    <b>#7</b> most popular in Powder Coated Garden Chairs
                                                </span>

                    </p>

                        
                    


                    <div class="flex flex-col lg:flex-row lg:items-start gap-4">

    {{-- LEFT BLOCK --}}
    <div class="flex-1 min-w-0">

       

       


        {{-- Undername + Wishlist --}}
        <div class="flex items-start justify-between gap-4 mt-2">

            {{-- Undername + Reviews --}}
            <div class="min-w-0">

                {{-- Undername --}}
                <p class="text-gray-700 mb-2 leading-relaxed text-sm">
                    {{ $product1->undername }}
                </p>


                {{-- Rating + Reviews --}}
                <div class="flex flex-wrap items-center text-gray-600 text-xs mb-4 gap-y-1">

                    {{-- Stars --}}
                    <div class="flex items-center gap-1 mr-3">

                        @for ($i = 1; $i <= 5; $i++)

                            @if ($i <= floor($rating))

                                <svg
                                    class="w-4 h-4 fill-current text-yellow-500"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                </svg>

                            @elseif ($i - $rating < 1)

                                <svg
                                    class="w-4 h-4 fill-current text-yellow-300"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z" />
                                </svg>

                            @else

                                <svg
                                    class="w-4 h-4 fill-current text-gray-300"
                                    viewBox="0 0 20 20"
                                >
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

                </div>

            </div>

            <div class="flex items-center gap-3">


           
@auth

    @can('addToProject', $product1)

        {{-- REGISTERED BUYER --}}
        <form
            method="POST"
            action="{{ route('buyer.cart.add', $product1->id) }}"
        >
            @csrf

            <button
                type="submit"
                title="{{ __('product/product_show.add_to_cart') }}"
                class="text-gray-500 hover:text-gray-900
                       hover:scale-110
                       transition-all duration-200"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-7 h-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 4h2l1.5 10a2 2 0 0 0 2 2h8.5a2 2 0 0 0 1.9-1.4L21 7H6"
                    />
                    <circle cx="9" cy="20" r="1.2" />
                    <circle cx="18" cy="20" r="1.2" />
                </svg>
            </button>
        </form>

    @endcan

@else

    {{-- GUEST --}}
    <button
        type="button"
        title="{{ __('product/product_show.add_to_cart') }}"
        onclick="dispatchAlert(
            'guest',
            'Please register or log in to add products to your cart.'
        )"
        class="text-gray-500 hover:text-gray-900
               hover:scale-110
               transition-all duration-200"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-7 h-7"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3 4h2l1.5 10a2 2 0 0 0 2 2h8.5a2 2 0 0 0 1.9-1.4L21 7H6"
            />
            <circle cx="9" cy="20" r="1.2" />
            <circle cx="18" cy="20" r="1.2" />
        </svg>
    </button>

@endif



    {{-- Wishlist --}}
    <div class="shrink-0">
        @include('product.partials.wishlist', [
            'product1' => $product1,
            'projects' => $projects
        ])
    </div>

</div>

        </div>

    </div>

</div>





                    </div>


                    
                    

                </div>










                @include('product.partials.notification')


                

              
<div>
    @include('product.partials.price-table', ['product1' => $product1])

    <div class="flex justify-end mt-1 mb-4">
    <div class="text-[12px] uppercase text-gray-500 pr-2">
        SKU: {{ $product1->sku }}
    </div>
</div>
</div>








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




               



{{-- =========================================================
    PRODUCT DIMENSIONS
========================================================= --}}


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
                       md:grid-cols-3
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






{{-- Supplier Info --}}

@php
    $supplier = $product1->supplier;
    $profile = $supplier?->profile;
    $hasCapabilities = $profile?->manufacturingCapabilities?->isNotEmpty();
@endphp

@if($supplier)

    @php
        $level = $supplier->level ?? 'Basic';

        $supplierRating = round($supplier->supplierReviews->avg('rating') ?? 0, 1);
        $reviewsCount   = $supplier->supplierReviews->count();

        $pillClasses = match($level) {
            'Silver'   => 'bg-gray-200 text-gray-700 border-gray-300',
            'Gold'     => 'bg-amber-100 text-amber-700 border-amber-200',
            'Platinum' => 'bg-slate-900 text-white border-slate-700',
            default    => 'bg-gray-100 text-gray-600 border-gray-200',
        };

        /*
        |--------------------------------------------------------------------------
        | TEMPORARY STUBS — replace with real supplier columns
        |--------------------------------------------------------------------------
        */

        $responseTime = '≤3h';              // TODO: '≤'.$supplier->response_time.'h'
        $onTimeRate   = 85.7;               // TODO: $supplier->on_time_dispatch_rate
    @endphp

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden p-8">

        {{-- TOP ROW : LOGO + IDENTITY --}}
        <div class="flex items-start gap-4">

            <a
                href="{{ route('supplier.show', $supplier->slug) }}"
                class="w-16 h-16 shrink-0 rounded-xl overflow-hidden border border-gray-200 bg-white">

                <img
                    src="{{ $supplier->logo?->cdn_url ?? asset('images/no-logo.png') }}"
                    alt="{{ $supplier->name }}"
                    class="w-full h-full object-cover">

            </a>

            <div class="min-w-0 flex-1">

                <div class="flex items-center gap-2.5">

                    <a
                        href="{{ route('supplier.show', $supplier->slug) }}"
                        class="mt-1 truncate text-[18px] font-semibold text-gray-900 transition hover:text-blue-900">

                        {{ $supplier->name }}

                    </a>

                    @if($supplier->is_verified)
                        <img src="{{ asset('images/icons/verified_icon.png') }}"
                            alt="Verified"
                            class="w-4 h-4 flex-shrink-0 mt-1">
                    @endif

                </div>

                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-2">

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-[11px] font-semibold tracking-wide {{ $pillClasses }}">

                        {{ strtoupper($level) }} SUPPLIER

                    </span>

                    <div class="flex items-center">

                        @for($i = 1; $i <= 5; $i++)
                            <svg
                                class="w-3.5 h-3.5 {{ $i <= floor($supplierRating) ? 'fill-yellow-500' : 'fill-gray-300' }}"
                                viewBox="0 0 20 20"
                                aria-hidden="true">

                                <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>

                            </svg>
                        @endfor

                        <span class="ml-1.5 text-xs text-gray-500 whitespace-nowrap">

                            {{ number_format($supplierRating, 1) }} ({{ $reviewsCount }})

                        </span>

                    </div>

                </div>

            </div>

        </div>

        @if($hasCapabilities || $responseTime || $onTimeRate)

            {{-- BOTTOM ROW : CAPABILITIES + PERFORMANCE --}}
            <div class="mt-5 flex flex-wrap items-start justify-between gap-x-6 gap-y-4">

                @if($hasCapabilities)
                    <div class="space-y-2">

                        @foreach($profile->manufacturingCapabilities as $capability)
                            <div class="flex items-center gap-2 text-sm text-gray-600">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    width="20"
                                    height="20"
                                    class="shrink-0"
                                    fill="none"
                                    stroke="#7a8291"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true">

                                    <path d="M15.12 4.47 Q19.39 4.61 19.53 8.88 Q22.45 12 19.53 15.12 Q19.39 19.39 15.12 19.53 Q12 22.45 8.88 19.53 Q4.61 19.39 4.47 15.12 Q1.55 12 4.47 8.88 Q4.61 4.61 8.88 4.47 Q12 1.55 15.12 4.47 Z"/>
                                    <path d="M8.55 12.15 L11.05 14.55 L15.6 9.35"/>

                                </svg>

                                <span>{{ $capability->name }}</span>

                            </div>
                        @endforeach

                    </div>
                @endif

                @if($responseTime || $onTimeRate)
                    <div class="flex items-start gap-8">

                        @if($responseTime)
                            <div>

                                <div class="text-base font-bold text-gray-900 leading-tight">

                                    {{ $responseTime }}

                                </div>

                                <div class="mt-0.5 text-xs text-gray-500 leading-snug">

                                    Response time

                                </div>

                            </div>
                        @endif

                        @if($onTimeRate)
                            <div>

                                <div class="text-base font-bold text-gray-900 leading-tight">

                                    {{ $onTimeRate }}%

                                </div>

                                <div class="mt-0.5 text-xs text-gray-500 leading-snug">

                                    On-time dispatch rate

                                </div>

                            </div>
                        @endif

                    </div>
                @endif

            </div>

        @endif

    </div>

@endif





                






               









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

         $groupedAttributes = $attributeValues
        ->groupBy(function ($attrValue) {
            return $attrValue->attribute?->group_id ?? 0;
        })
        ->sortBy(function ($items, $groupId) {

            if ($groupId == 0) {
                return 999999;
            }

            return $items->first()->attribute?->group?->sort_order ?? 999999;
        });

    $visibleAttributes = $attributeValues->take(8);
    $hiddenAttributes = $attributeValues->slice(8);
@endphp

    @if($attributeValues->count())

    <div class="relative mt-2">

        @php
            $visibleGroupedAttributes = $visibleAttributes
                ->groupBy(fn ($attrValue) => $attrValue->attribute?->group_id);

            $hiddenGroupedAttributes = $hiddenAttributes
                ->groupBy(fn ($attrValue) => $attrValue->attribute?->group_id);
        @endphp

        <ul
            id="product-attributes-list"
            class="text-gray-700"
        >

            {{-- FIRST 8 --}}
            @foreach($visibleGroupedAttributes as $groupId => $groupAttributes)

                @php
                    $group = $groupAttributes->first()->attribute?->group;
                @endphp

             @if($group)
    <li class="py-3 mt-3 border-0">
        <div
            class="
                inline-flex
                items-center
                px-3
                py-1.5
                rounded-md
                bg-gray-50
                border
                border-gray-200
                text-sm
                font-semibold
                text-gray-800
            "
        >
            {{ $group->name ?? $group->code }}
        </div>
    </li>
@endif

                @foreach($groupAttributes as $index => $attrValue)

    <li class="
        flex
        justify-between
        py-2
        {{ $index < $groupAttributes->count() - 1 ? 'border-b border-gray-200' : 'border-b border-gray-200' }}
    ">

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

                if (!$unitName) {
                    $unitName = $unit?->translations
                        ?->firstWhere('locale', 'en')
                        ?->name;
                }

                $unitName = $unitName
                    ?: $unit?->name
                    ?: $unit?->code;
            @endphp

            {{ $displayValue }}{{ $unitName ? ' ' . $unitName : '' }}

        </span>

    </li>

@endforeach

            @endforeach


            {{-- HIDDEN ATTRIBUTES --}}
            @if($hiddenAttributes->count())

                <div
                    id="hidden-product-attributes"
                    class="hidden divide-y divide-gray-200"
                >

                    @foreach($hiddenGroupedAttributes as $groupId => $groupAttributes)

                        @php
                            $group = $groupAttributes->first()->attribute?->group;
                        @endphp

                       @if($group)
    <li class="py-3 mt-3 border-0">
        <div
            class="
                inline-flex
                items-center
                px-3
                py-1.5
                rounded-md
                bg-gray-50
                border
                border-gray-200
                text-sm
                font-semibold
                text-gray-800
            "
        >
            {{ $group->name ?? $group->code }}
        </div>
    </li>
@endif

                        @foreach($groupAttributes as $index => $attrValue)

    <li class="
        flex
        justify-between
        py-2
        border-b border-gray-200
    ">

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

                if (!$unitName) {
                    $unitName = $unit?->translations
                        ?->firstWhere('locale', 'en')
                        ?->name;
                }

                $unitName = $unitName
                    ?: $unit?->name
                    ?: $unit?->code;
            @endphp

            {{ $displayValue }}{{ $unitName ? ' ' . $unitName : '' }}

        </span>

    </li>

@endforeach

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