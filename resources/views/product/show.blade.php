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


            





<div class="p-6"
     x-data="{ selected: {}, showProjectBox: false, showCustomizationBox: false }">

    {{-- HEADER ROW : TITLE + ABILITIES (left) / ACTIONS (right) --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

        {{-- LEFT : TITLE + ABILITY LIST --}}
        <div class="min-w-0 flex-1">
@if($product1->customization)
            <h3 class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-[18px] font-semibold text-gray-900">

                <span>Customization & Materials</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="15" viewBox="0 0 72 15" fill="none">

    <!-- O -->
    <path
        d="M1507 711Q1507 491 1420 324Q1333 157 1171 68.5Q1009 -20 793 -20Q461 -20 272.5 175.5Q84 371 84 711Q84 1050 272 1240Q460 1430 795 1430Q1130 1430 1318.5 1238Q1507 1046 1507 711ZM1206 711Q1206 939 1098 1068.5Q990 1198 795 1198Q597 1198 489 1069.5Q381 941 381 711Q381 479 491.5 345.5Q602 212 793 212Q991 212 1098.5 342Q1206 472 1206 711Z"
        transform="translate(0 11.55) scale(0.0092 -0.0075)"
        fill="#008EFF"
    />

    <!-- p -->
    <path
        d="M1167 546Q1167 275 1058.5 127.5Q950 -20 752 -20Q638 -20 553.5 29.5Q469 79 424 172H418Q424 142 424 -10V-425H143V833Q143 986 135 1082H408Q413 1064 416.5 1011Q420 958 420 906H424Q519 1105 770 1105Q959 1105 1063 959.5Q1167 814 1167 546ZM874 546Q874 910 651 910Q539 910 479.5 812Q420 714 420 538Q420 363 479.5 267.5Q539 172 649 172Q874 172 874 546Z"
        transform="translate(14.6556 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- t -->
    <path
        d="M420 -18Q296 -18 229 49.5Q162 117 162 254V892H25V1082H176L264 1336H440V1082H645V892H440V330Q440 251 470 213.5Q500 176 563 176Q596 176 657 190V16Q553 -18 420 -18Z"
        transform="translate(26.1648 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- i -->
    <path
        d="M143 1277V1484H424V1277ZM143 0V1082H424V0Z"
        transform="translate(32.4392 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- o -->
    <path
        d="M1171 542Q1171 279 1025 129.5Q879 -20 621 -20Q368 -20 224 130Q80 280 80 542Q80 803 224 952.5Q368 1102 627 1102Q892 1102 1031.5 957.5Q1171 813 1171 542ZM877 542Q877 735 814 822Q751 909 631 909Q375 909 375 542Q375 361 437.5 266.5Q500 172 618 172Q877 172 877 542Z"
        transform="translate(37.674 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- n -->
    <path
        d="M844 0V607Q844 892 651 892Q549 892 486.5 804.5Q424 717 424 580V0H143V840Q143 927 140.5 982.5Q138 1038 135 1082H403Q406 1063 411 980.5Q416 898 416 867H420Q477 991 563 1047Q649 1103 768 1103Q940 1103 1032 997Q1124 891 1124 687V0Z"
        transform="translate(49.1832 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

    <!-- s -->
    <path
        d="M1055 316Q1055 159 926.5 69.5Q798 -20 571 -20Q348 -20 229.5 50.5Q111 121 72 270L319 307Q340 230 391.5 198Q443 166 571 166Q689 166 743 196Q797 226 797 290Q797 342 753.5 372.5Q710 403 606 424Q368 471 285 511.5Q202 552 158.5 616.5Q115 681 115 775Q115 930 234.5 1016.5Q354 1103 573 1103Q766 1103 883.5 1028Q1001 953 1030 811L781 785Q769 851 722 883.5Q675 916 573 916Q473 916 423 890.5Q373 865 373 805Q373 758 411.5 730.5Q450 703 541 685Q668 659 766.5 631.5Q865 604 924.5 566Q984 528 1019.5 468.5Q1055 409 1055 316Z"
        transform="translate(60.6924 11.55) scale(0.0092 -0.0075)"
        fill="#00346D"
    />

</svg>

                

                
            </h3>
@endif

        @if($product1->customization)
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

@if($product1->customization)
    {{-- Commercial Terms --}}
    
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm text-gray-700">
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
                
    @if($product1->materials->isNotEmpty())

    @php
        $materialGroups = $product1->materials
            ->groupBy('material_group_id')
            ->values();

        $firstGroup = $materialGroups->first();
    @endphp

    @if($firstGroup)

        @php
            $materialGroup = $firstGroup->first()?->materialGroup;
        @endphp

        <div class="mb-4 mt-4">

            @if($materialGroup)

                <div class="mb-5">

                    {{-- GROUP HEADER --}}
                    <div class="flex items-start justify-between gap-6">

                        {{-- GROUP NAME + DESCRIPTION --}}
                        <div class="min-w-0 flex-1">

                            <h4 class="text-sm font-semibold text-gray-900">
                                {{ $materialGroup->translatedName() ?? $materialGroup->name ?? $materialGroup->slug }}
                            </h4>

                            @if($materialGroup->translatedDescription())

                                <div class="mt-1 text-xs text-gray-500 leading-relaxed">
                                    {{ $materialGroup->translatedDescription() }}
                                </div>

                            @endif

                        </div>

                        {{-- BRAND + LOGO --}}
                        @if($materialGroup->brand)

                            <div class="shrink-0 flex flex-col items-center w-[80px]">

                                {{-- BRAND LOGO --}}
                                @if($materialGroup->logo?->cdn_url)

                                    <div
                                        class="w-[80px] h-[80px]
                                               flex items-center justify-center
                                               overflow-hidden"
                                    >

                                        @if($materialGroup->brand_url)

                                            <a
                                                href="{{ $materialGroup->brand_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="w-full h-full flex items-center justify-center"
                                            >

                                                <img
                                                    src="{{ $materialGroup->logo->cdn_url }}"
                                                    alt="{{ $materialGroup->brand }}"
                                                    class="w-full h-full object-contain"
                                                >

                                            </a>

                                        @else

                                            <img
                                                src="{{ $materialGroup->logo->cdn_url }}"
                                                alt="{{ $materialGroup->brand }}"
                                                class="w-full h-full object-contain"
                                            >

                                        @endif

                                    </div>

                                @endif

                            </div>

                        @endif

                    </div>


                    {{-- MATERIALS --}}
                    <div class="mt-3 flex flex-wrap gap-x-1 gap-y-2">

                        @foreach($firstGroup as $material)

                            <div class="w-20 text-center">

                                {{-- MATERIAL IMAGE --}}
                                <div
                                    class="w-20 h-20
                                           overflow-hidden
                                           rounded
                                           border
                                           border-gray-200
                                           bg-gray-50"
                                >

                                    @if($material->photo?->cdn_url)

                                        <img
                                            src="{{ $material->photo->cdn_url }}"
                                            alt="{{ $material->name }}"
                                            class="w-full h-full object-cover"
                                        >

                                    @else

                                        <img
                                            src="{{ asset('images/no-image.png') }}"
                                            alt="{{ $material->name }}"
                                            class="w-full h-full object-cover"
                                        >

                                    @endif

                                </div>

                                {{-- MATERIAL NAME --}}
                                <div
                                    class="mt-1
                                           text-[10px]
                                           font-medium
                                           leading-tight
                                           text-gray-800
                                           text-center"
                                >
                                    {{ $material->name }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif

        </div>

    @endif

@endif

@if(isset($materialGroups) && $materialGroups->count() > 1)
{{-- SELECT VARIATIONS --}}
<div class="mt-4 flex justify-center">

    <button
        type="button"
        @click="$dispatch('open-variations-modal')"
        title="More materials and color"
        class="inline-flex items-center justify-center
               rounded-full
               border-2 border-gray-200
               bg-white
               px-2 py-0.5
               text-xs font-medium text-gray-900
               transition
               hover:bg-gray-100
               hover:border-gray-300
               focus:outline-none
               focus:ring-2
               focus:ring-gray-900
               focus:ring-offset-2"
    >
        See more materials and colors available for this product
    </button>

</div>



@include('product.partials.variations-modal', [
    'product' => $product1
])

@endif

@endif




</div>
</div>

            


            {{-- Info --}}
            <div class="rounded-xl shadow p-6">

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

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden p-6">

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

            @php
                $capabilities = $profile->manufacturingCapabilities;
                $visibleCapabilities = $capabilities->take(5);
                $hiddenCapabilities = $capabilities->skip(5);
            @endphp

            <div
                x-data="{ expanded: false }"
                class="space-y-2"
            >

                {{-- VISIBLE CAPABILITIES --}}
                @foreach($visibleCapabilities as $capability)

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


                {{-- HIDDEN CAPABILITIES --}}
                @if($hiddenCapabilities->isNotEmpty())

                    <div
                        x-show="expanded"
                        x-collapse
                        x-cloak
                        class="space-y-2"
                    >

                        @foreach($hiddenCapabilities as $capability)

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


                    {{-- EXPAND BUTTON --}}
<div class="flex justify-center">
    <button
        type="button"
        @click="expanded = !expanded"
        class="mt-1
               inline-flex
               items-center
               justify-center
               gap-1.5
               text-xs
               font-medium
               text-gray-500
               transition
               hover:text-gray-900
               focus:outline-none"
    >
        <span
            x-text="expanded ? 'Show less' : 'Show more'"
        ></span>

        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="16"
            height="16"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="transition-transform duration-200"
            :class="{ 'rotate-180': expanded }"
        >
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>
</div>

                @endif

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





                






               









               

              
                




               







               






            </div>
        </div>




{{-- COMMON BLOCK --}}
<div class="">
    

{{-- Product Attributes : Key attributes --}}

@if($product1->attributeValues->count())

    @php
        /*
        |--------------------------------------------------------------------------
        | FILTER HIDDEN BOOLEAN ATTRIBUTES
        |--------------------------------------------------------------------------
        |
        | Boolean = 0 → полностью не показываем.
        | Boolean = 1 → показываем как Yes.
        | Measurement → размеры выводятся отдельным блоком.
        |
        | Показываются ВСЕ атрибуты — без лимита и без скрытия.
        |
        */

        $attributeValues = $product1->attributeValues
            ->filter(function ($attrValue) {

                $attribute = $attrValue->attribute;

                if (!$attribute) {
                    return false;
                }

                if ($attribute->type === 'measurement') {
                    return false;
                }

                if ($attribute->type === 'boolean') {

                    $value = $attrValue->translations
                        ->firstWhere('locale', app()->getLocale())
                        ?->value;

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

        $pairCount = (int) ceil($attributeValues->count() / 2);
    @endphp

    @if($attributeValues->count())

        <div class="p-2 mb-6">

            <div class="leading-nonetext-[18px] font-semibold text-gray-900 pl-2">
                {{ __('product/product_show.key_attributes') }}
            </div>

            <div class="bg-white mt-4 rounded-xl border border-gray-200 overflow-hidden">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 -mt-px">

                    @foreach($attributeValues as $index => $attrValue)

                        @php
                            $attribute = $attrValue->attribute;
                            $unit      = $attribute?->unit;

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

                            $valueText = trim(
                                $displayValue . ($unitName ? ' ' . $unitName : '')
                            );

                            $isLong = mb_strlen($valueText) > 60;

                            $cellBorder = $index % 2 === 1
                                ? 'lg:border-l lg:border-gray-200'
                                : '';
                        @endphp

                        {{-- LABEL --}}
                        <div
                            class="border-t border-gray-200
                                   bg-gray-50
                                   px-6 py-3
                                   text-base text-gray-600
                                   {{ $cellBorder }}"
                        >
                            {{ $attribute->name ?? $attribute->code }}
                        </div>

                        {{-- VALUE --}}
                        <div
                            class="border-t border-gray-200
                                   bg-white
                                   px-6 py-3
                                   text-base font-semibold text-gray-900"
                        >

                            @if($isLong)

                                <div x-data="{ expanded: false }">

                                    <div x-show="!expanded" x-cloak>
                                        {{ Str::limit($valueText, 60) }}
                                    </div>

                                    <div x-show="expanded" x-cloak>
                                        {{ $valueText }}
                                    </div>

                                    <button
                                        type="button"
                                        @click="expanded = !expanded"
                                        class="mt-1 inline-flex items-center gap-1
                                               text-xs font-medium text-gray-500
                                               underline underline-offset-2
                                               hover:text-gray-700
                                               focus:outline-none
                                               focus:ring-2
                                               focus:ring-gray-900
                                               focus:ring-offset-2
                                               rounded"
                                    >
                                        <span x-text="expanded ? 'Show less' : 'Show more'"></span>
                                    </button>

                                </div>

                            @else

                                {{ $valueText }}

                            @endif

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    @endif

@endif









  {{-- Description --}}
@if(!empty($product1->description))
    <div class="product-description text-gray-700 mb-6 leading-relaxed px-8">
        {!! $product1->description !!}
    </div>
@endif

        <style>
    .product-description p {
        margin-bottom: 0.75rem;
    }

    .product-description ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin: 0.75rem 0;
    }

    .product-description ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin: 0.75rem 0;
    }

    .product-description li {
        margin: 0.25rem 0;
    }

    .product-description strong,
    .product-description b {
        font-weight: 700;
    }
</style>      






{{-- Shipping / Payment Method / Actions --}}

@php
    
    // TODO: $product1->paymentGuarantees
    $paymentGuarantees = [
        [
            'icon'        => 'shield',
            'title'       => 'Secure payments',
            'logos'       => ['VISA', 'MC', 'AMEX', 'PayPal', ' Pay', 'G Pay'],
            'description' => 'Every payment you make on Alibaba.com is secured with strict SSL encryption and PCI DSS data protection...',
        ],
        [
            'icon'        => 'return',
            'title'       => 'Easy Return',
            'logos'       => [],
            'description' => 'Make free local returns for defects on qualifying purchases',
        ],
        [
            'icon'        => 'money',
            'title'       => 'Money-back protection',
            'logos'       => [],
            'description' => 'Claim a refund if your order doesn\'t ship, is missing, or arrives with product issues',
        ],
    ];

    
@endphp

<div class="bg-white rounded-xl shadow p-8 mb-8">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-10 gap-y-8">

        
{{-- COLUMN 1 : SHIPPING --}}
<div x-data="{ showMore: false }">

    <h3 class="font-bold text-[17px] leading-tight text-gray-900">
        Shipping
    </h3>

    <p class="mt-2 text-sm leading-relaxed text-gray-600">
        Shipping fee and delivery date to be negotiated. Chat with supplier now for more details.
    </p>

    @if(!empty($deliveryOptions))

        <hr class="mt-4 border-gray-200">

        <div class="mt-4 space-y-5">

            @foreach($deliveryOptions as $option)

                @php $isExpanded = $option['expanded'] ?? true; @endphp

                <div class="flex items-start gap-3"
                    @if(!$isExpanded) x-show="showMore" x-cloak @endif>

                    {{-- Truck icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6 shrink-0 text-emerald-600"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.7"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true">

                        <path d="M1 3h13v13H1z"/>
                        <path d="M14 8h4l3 3v5h-7z"/>
                        <circle cx="5.5" cy="18.5" r="2"/>
                        <circle cx="17.5" cy="18.5" r="2"/>

                    </svg>

                    <div class="min-w-0 flex-1">

                        <div class="flex items-start justify-between gap-3">
<div>
                            <span class="text-sm font-semibold text-gray-900 leading-snug">
                                {{ $option['title'] }}
                            </span>

                            @if($option['description'])
                            <div class="mt-1 text-xs text-gray-500 leading-snug">
                                {{ $option['description'] }}
                            </div>
                        @endif
</div>


                            @if($option['price'])
                                <span class="shrink-0 text-sm font-semibold text-gray-900">
                                    {{ $option['price'] }}
                                </span>
                            @endif

                        </div>

                        

                        @if(!empty($option['badge']))
                            <div class="mt-2 inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-900">
                                {{ $option['badge'] }}
                            </div>
                        @endif

                    </div>

                </div>

            @endforeach

        </div>

        {{-- SHOW MORE --}}
        @if($hasHiddenDelivery)

            <div class="mt-4 flex justify-center">

                <button
                    type="button"
                    id="shipping-toggle"
                    @click="showMore = !showMore"
                    :aria-expanded="showMore.toString()"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 transition hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 rounded">

                    <span x-text="showMore ? 'Show less' : 'Show more'">Show more</span>

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4 transition-transform duration-200"
                        :class="showMore ? 'rotate-180' : ''"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true">

                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />

                    </svg>

                </button>

            </div>

        @endif

    @endif

</div>



        {{-- COLUMN 2 : PAYMENT METHOD --}}
        <div>

            <h3 class="font-bold text-[17px] leading-tight text-gray-900">
                Payment Method
            </h3>

            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                Shipping fee and delivery date to be negotiated. Chat with supplier now for more details.
            </p>

            @if(!empty($paymentGuarantees))

                <hr class="mt-4 border-gray-200">

                <div class="mt-4 space-y-5">

                    @foreach($paymentGuarantees as $item)

                        <div class="flex items-start gap-3">

                            {{-- Green guarantee icon --}}
                            <span class="mt-0.5 flex w-5 h-5 shrink-0 items-center justify-center rounded border border-emerald-600">

                                @switch($item['icon'])

                                    @case('return')
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-3 h-3 text-emerald-600"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true">

                                            <path d="M1 4v6h6"/>
                                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>

                                        </svg>
                                        @break

                                    @case('money')
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-3 h-3 text-emerald-600"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true">

                                            <path d="M12 2v20"/>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>

                                        </svg>
                                        @break

                                    @default
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-3 h-3 text-emerald-600"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="3"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true">

                                            <path d="M4 12.5l5.5 5.5L20 6.5"/>

                                        </svg>

                                    @endswitch

                            </span>

                            <div class="min-w-0 flex-1">

                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">

                                    <span class="text-sm font-semibold text-gray-900 leading-snug">
                                        {{ $item['title'] }}
                                    </span>

                                    @if(!empty($item['logos']))
                                        <span class="flex flex-wrap items-center gap-1">

                                            @foreach($item['logos'] as $logo)
                                                <span class="inline-flex h-4 min-w-[26px] items-center justify-center rounded-[3px] border border-gray-300 bg-white px-1 text-[8px] font-bold leading-none text-gray-700">
                                                    {{ $logo }}
                                                </span>
                                            @endforeach

                                        </span>
                                    @endif

                                </div>

                                @if($item['description'])
                                    <div class="mt-1 text-xs text-gray-500 leading-snug">
                                        {{ $item['description'] }}
                                    </div>
                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

        {{-- COLUMN 3 : ACTIONS --}}
        <div class="flex flex-col">
@auth
            @can('addToProject', $product1)

            <form method="POST" action="{{ route('buyer.cart.add.redirect', $product1->id) }}">
                        @csrf

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center rounded-full
                       bg-[#1B2A5E] px-6 py-3.5
                       text-[15px] font-semibold text-white
                       transition hover:bg-[#16224b]
                       focus:outline-none focus:ring-2 focus:ring-[#1B2A5E] focus:ring-offset-2">
                            {{ __('product/product_show.checkout') }}
                        </button>
                    </form>



            <button
                type="button"
                class="mt-3 w-full inline-flex items-center justify-center rounded-full
                       bg-[#E2460B] px-6 py-3.5
                       text-[15px] font-semibold text-white
                       transition hover:bg-[#c93d09]
                       focus:outline-none focus:ring-2 focus:ring-[#E2460B] focus:ring-offset-2">

                Send inquiry

            </button>

            <button
                class="open-conversation mt-3 w-full inline-flex items-center justify-center rounded-full
                       border border-gray-800 bg-white px-6 py-3.5
                       text-[15px] font-semibold text-gray-900
                       transition hover:bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2"  data-subject-type="App\Models\Product"
                                    data-subject-id="{{ $product1->id }}">

                {{ __('product/product_show.contact_supllire') }}

            </button>


           

            <p class="mt-4 text-sm leading-relaxed text-gray-500">
                Only orders placed and paid through Alibaba.com can enjoy free protection by
            </p>
@endcan

@else


                    <button
                            type="button"

                            onclick="dispatchAlert(
            'guest',
            'Please register or log in to proceed to checkout.'
        )"


                            class="w-full inline-flex items-center justify-center rounded-full
                       bg-[#1B2A5E] px-6 py-3.5
                       text-[15px] font-semibold text-white
                       transition hover:bg-[#16224b]
                       focus:outline-none focus:ring-2 focus:ring-[#1B2A5E] focus:ring-offset-2">
                            {{ __('product/product_show.checkout') }}
                        </button>

                       
                        <button
                type="button"

                onclick="dispatchAlert(
                'guest',
                'Please register or log in to send an inquiry.'
            )"


                class="mt-3 w-full inline-flex items-center justify-center rounded-full
                       bg-[#E2460B] px-6 py-3.5
                       text-[15px] font-semibold text-white
                       transition hover:bg-[#c93d09]
                       focus:outline-none focus:ring-2 focus:ring-[#E2460B] focus:ring-offset-2">

                Send inquiry

            </button>



            <button
            type="button"
                        onclick="dispatchAlert(
                'guest',
                'Please register or log in to contact the supplier.'
            )"
                            class="mt-3 w-full inline-flex items-center justify-center rounded-full
                       border border-gray-800 bg-white px-6 py-3.5
                       text-[15px] font-semibold text-gray-900
                       transition hover:bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2">

                {{ __('product/product_show.contact_supllire') }}

            </button>
                        

                            


                        
                          




                    </div>



                </div>


@endif



        </div>

    </div>

</div>






           
                
  <!-- <p class="text-gray-700 mb-2 leading-relaxed">{{ __('product/product_show.place_of_origin') }} <strong>{{ $product1->country?->name ?? 'Country not specified' }}</strong>
                </p>

                 -->















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