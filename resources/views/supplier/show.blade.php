@extends('layouts.app')

@section('content')
<section class="bg-[#F7F3EA] py-8">
    <div class="container mx-auto px-6">

        @php
                            $score = $supplier->reputation ?? 0;

                            if ($score <= 50) {
                                $level = 'Basic';
                                $color = 'bg-gray-200';
                                $textColor = 'text-gray-600';
                                $nextLevelScore = 51;
                            } elseif ($score <= 120) {
                                $level = 'Silver';
                                $color = 'bg-gray-400';
                                $textColor = 'text-white';
                                $nextLevelScore = 121;
                            } elseif ($score <= 200) {
                                $level = 'Gold';
                                $color = 'bg-yellow-500';
                                $textColor = 'text-white';
                                $nextLevelScore = 201;
                            } else {
                                $level = 'Platinum';
                                $color = 'bg-blue-600';
                                $textColor = 'text-white';
                                $nextLevelScore = $score;
                            }

                            $progress = ($score / $nextLevelScore) * 100;
                            if ($progress > 100) $progress = 100;

                            $rating = $supplier->reviews()->avg('rating') ?? 0;
                            $rating = round($rating, 1);

                        
                        @endphp
                        @php
                        $types = $supplier->supplierTypes->map(function($type){
                            return $type->translation?->name ?? $type->slug;
                        })->filter()->values();
                    @endphp
        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-600 mb-6">
            <a href="/suppliers" class="hover:text-black">Suppliers</a> /
            <span class="text-gray-900">{{ $supplier->name }}</span>
        </div>

        {{-- Supplier Info --}}
        <div class="@if($supplier->is_trusted)paper-notch @endif relative flex flex-col lg:flex-row items-start gap-6 rounded-xl border  p-6 mb-8
            @if($score > 120)bg-gradient-to-br from-white via-[#f7f3ec] to-[#e1d8cb] shadow-lg border-gray-100
            @else
            bg-gradient-to-br from-white via-[#f9f7f3] to-[#f9f7f3] shadow-sm border-gray-200
            @endif ">



        {{-- TOP BADGES --}}
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-20 flex overflow-visible">

      

        @if($supplier->is_trusted)
            <div class="bg-emerald-700 text-white text-[10px] font-semibold rounded-t-full px-3 py-1 uppercase tracking-wide">
                TRUSTED COMPANY
            </div>
        @endif

    </div>



            {{-- Logo --}}
            @if($supplier->logo)
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . $supplier->logo) }}" 
                         alt="{{ $supplier->name }}" 
                         class="w-28 h-28 object-cover rounded-lg">
                </div>
            @endif

            {{-- Info --}}
            <div class="flex-1 flex flex-col gap-2">

                <div class="flex items-start lg:items-center justify-between">
                    <div>
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight">
                            {{ $supplier->name }}
                        </h1>

                        

                        @if($supplier->is_verified)
            <img src="{{ asset('images/icons/verified_icon.png') }}"
                 alt="Verified"
                 class="w-5 h-5 flex-shrink-0">
        @endif
                        @if($types->isNotEmpty())
<div class="">
    <span class="text-sm text-gray-400">
        {{ $types->implode('  -  ') }}
    </span>
</div>
@endif


                        <span class="text-gray-600 font-medium whitespace-nowrap">
                           |&nbsp;&nbsp; {{ $supplier->country ? $supplier->country->name : 'N/A' }}
                        </span>

                         


                    </div>



                   


                    <p class="text-gray-700">{{ $supplier->short_description ?? '' }}</p>
                    <p class="text-gray-700 mb-3">{{ $supplier->description ?? '' }}</p>
                    
                    </div>

                   


                    {{-- Reputation --}}
                    <div class="ml-auto w-full lg:w-auto self-start">
                                               
                        
                            <div class="px-5 pt-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] whitespace-nowrap font-semibold tracking-wide shadow-xl
                                    {{ $level === 'Basic' ? 'bg-gray-50 text-gray-400 border border-gray-200' : '' }}
                                    {{ $level === 'Silver' ? 'bg-gray-200 text-gray-700 border border-gray-300' : '' }}
                                    {{ $level === 'Gold' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                    {{ $level === 'Platinum' ? 'bg-slate-800 text-white border border-slate-700' : '' }}
                                ">

                                    @if($level === 'Basic')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="8"/>
                                        </svg>
                                    @elseif($level === 'Silver')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path d="M12 3l7 18H5l7-18z"/>
                                        </svg>
                                    @elseif($level === 'Gold')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path d="M5 12l5 5L20 7"/>
                                        </svg>
                                    @elseif($level === 'Platinum')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path d="M12 2l3 7h7l-5.5 4.2L18 21l-6-4-6 4 1.5-7.8L2 9h7z"/>
                                        </svg>
                                    @endif

                                    {{ strtoupper($level) }} SUPPLIER
                                </span>
                            </div>
                    </div>
                   
                </div>

                

            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">



           <div class="w-full lg:w-1/4 space-y-6">




 {{-- Категории продавца --}}
<div class="bg-white shadow rounded-xl overflow-hidden px-6 py-4 space-y-4">
    
    <h2 class="text-xl font-bold mb-4">Presented Categories</h2>
    <ul class="space-y-2">
        @foreach($rootCategories as $cat)
            <li>
                {{-- Корневая категория --}}
                <span class="text-gray-800 font-semibold text-lg">
                    {{ $cat->name }}
                </span>

                {{-- Подкатегории --}}
                @if($cat->children->count())
                    <ul class="ml-4 mt-1 space-y-1">
                        @foreach($cat->children as $child)
                            @if($categoryIds->contains($child->id))
                                <li>
                                    <a href="{{ url('/supplier/' . $supplier->slug . '?category=' . $child->slug) }}"
                                       class="block px-2 py-1 rounded transition-colors duration-200
                                       {{ request('category') == $child->slug ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                                        {{ $child->name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>







    {{-- LEFT FILTER --}}
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" action="{{ url()->current() }}">
            <input type="hidden" name="sort" value="{{ request('sort', 'featured') }}">

            <h2 class="text-xl font-bold mb-4">Filter Products</h2>

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

            {{-- Price Filter --}}
            <div class="mt-4">
                <h3 class="font-semibold mb-2">Price</h3>
                <div class="flex space-x-2">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-1/2 p-2 border rounded">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-1/2 p-2 border rounded">
                </div>
            </div>

            {{-- MOQ Filter --}}
            <div class="mt-4">
                <h3 class="font-semibold mb-2">MOQ</h3>
                <input type="number" name="min_moq" value="{{ request('min_moq') }}" placeholder="Min MOQ" class="w-full p-2 border rounded">
            </div>

            {{-- Sold Filter --}}
            <div class="mt-4">
                <h3 class="font-semibold mb-2">Sold (Min)</h3>
                <input type="number" name="sold_from" value="{{ request('sold_from') }}" placeholder="Min sold" class="w-full p-2 border rounded">
            </div>

            {{-- Lead Time --}}
            <div class="mt-4">
                <h3 class="font-semibold mb-2">Lead Time (days)</h3>
                <div class="flex space-x-2">
                    <input type="number" name="min_lead_time" value="{{ request('min_lead_time') }}" placeholder="Min" class="w-1/2 p-2 border rounded">
                    <input type="number" name="max_lead_time" value="{{ request('max_lead_time') }}" placeholder="Max" class="w-1/2 p-2 border rounded">
                </div>
            </div>

            {{-- Country Filter --}}
            <div class="mt-4">
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

            <button type="submit" class="mt-4 w-full bg-blue-900 text-white py-2 rounded-lg hover:bg-blue-700 transition">Apply Filters</button>
        </form>
    </div>
</div>






            {{-- RIGHT PRODUCTS --}}
            <div class="w-full lg:w-3/4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-start" id="product-list">
                @foreach($supplier->products as $product)
                    {{-- === Ваша карточка продукта === --}}
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                        <a href="{{ url('/product/' . $product->slug) }}" class="block overflow-hidden rounded-t-lg">
                            <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('images/no-image.png') }}"
                                 class="w-full aspect-square object-cover"
                                 alt="{{ $product->name }}">
                        </a>

                        <div class="p-3">
                            <h3 class="font-semibold text-base mb-1">
                                <a href="{{ url('/product/' . $product->slug) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                            </h3>

                            @php
                                $rating = $product->reviews()->avg('rating') ?? 0;
                                $rating = number_format((float)$rating, 1, '.', '');
                                $reviewsCount = $product->reviews()->count();
                                $soldCount = $product->sold_count ?? 0;
                            @endphp

                            <div class="flex items-center gap-2 mb-2 text-sm">
                                <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 24 24" fill="url(#gold-gradient)" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="gold-gradient" x1="0" y1="0" x2="1" y2="1">
                                            <stop offset="0%" stop-color="#FFD700"/>
                                            <stop offset="100%" stop-color="#FFC107"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.27 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/>
                                </svg>
                                <span class="font-semibold text-gray-800">{{ $rating }}</span>
                                <span>({{ $reviewsCount }} отзыв{{ $reviewsCount != 1 ? 'ов' : '' }})</span>
                                @if($soldCount > 0)
                                    <span class="before:content-['•'] before:mx-1"></span>
                                    <span>Продано: {{ $soldCount }}</span>
                                @endif
                            </div>

                            <p class="text-gray-600 text-xs mb-1">
                                @php
                                    $materialNames = $product->materials->map(function($material) {
                                        $translation = $material->translations->firstWhere('locale', app()->getLocale()) ?? $material->translations->firstWhere('locale','en');
                                        return $translation ? $translation->name : $material->name;
                                    })->implode(', ');
                                @endphp
                                {{ $materialNames }}
                            </p>

                            <div class="bg-gray-50 rounded-lg p-2 mb-2 text-xs">
                                @foreach($product->priceTiers as $tier)
                                    <div class="flex justify-between text-gray-700 py-1 border-b last:border-b-0">
                                        <span>{{ $tier->min_qty }} - {{ $tier->max_qty ?? '∞' }} pcs</span>
                                        <span class="font-semibold text-blue-900">{{ number_format($tier->price, 2) }} ₴</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-1">
                                <button class="w-full border border-gray-300 py-1.5 rounded-xl
                                            text-gray-800 font-medium shadow-sm
                                            hover:border-black hover:text-black hover:shadow-md transition-all transform hover:scale-105 text-sm">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- === Конец карточки === --}}
                @endforeach
            </div>

        </div>
    </div>
</section>

<style>
  .paper-notch {
    position: relative;
    overflow: hidden;
}

/* Main notch cut */
.paper-notch::after {
    content: "";
    position: absolute;
    bottom: -22px;
    left: 50%;
    transform: translateX(-50%);

    width: 150px;
    height: 48px;

    background: #0b7a4c;

    border-radius: 999px;

    box-shadow:
        inset 0 2px 6px rgba(0,0,0,0.04),
        0 -2px 4px rgba(0,0,0,0.03);
}

/* Slight paper depth highlight */
.paper-notch::before {
    content: "";
    position: absolute;
    bottom: -26px;
    left: 50%;
    transform: translateX(-50%);

    width: 160px;
    height: 56px;

    background: linear-gradient(
        to bottom,
        rgba(255,255,255,0.6),
        rgba(0,0,0,0.03)
    );

    border-radius: 999px;
    
}







</style>
@endsection
