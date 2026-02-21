@extends('layouts.app')

@section('content')

{{-- HERO BANNER --}}
<section class="py-2 bg-[#F7F3EA]">
    <div class="container mx-auto px-6">
        <h1 class="text-4xl font-extrabold text-gray-900">
            Suppliers
        </h1>
        <p class="text-lg text-gray-700 mt-2">
            Browse our trusted suppliers for premium furniture and materials.
        </p>
    </div>
</section>

{{-- MAIN LAYOUT --}}
<section class="py-8 bg-[#F7F3EA]">
    <div class="container mx-auto px-6">
        <div class="flex flex-col md:flex-row gap-6">

            {{-- FILTER SIDEBAR --}}
            <aside class="w-full md:w-1/4 mb-4">

                {{-- Category Hierarchy Dropdown --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-5 mb-6">
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-gray-700 mb-4">
                        Filter By Category
                    </h4>
                    <div class="space-y-3">
                        @foreach($categories as $level0)
                            <div x-data="{ open: false }" class="border-b border-gray-100 pb-2">
                                {{-- LEVEL 0 --}}
                                <button type="button"
                                        @click="open = !open"
                                        class="w-full flex items-center justify-between py-2 text-left group">

                                    <span class="text-sm font-semibold text-gray-800 group-hover:text-black">
                                        {{ $level0->name }}
                                    </span>

                                    @if($level0->children->count())
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                             :class="{ 'rotate-180': open }"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    @endif
                                </button>

                                {{-- LEVEL 1 --}}
                                @if($level0->children->count())
                                    <div x-show="open" x-transition class="pl-4 mt-2 space-y-2">
                                        @foreach($level0->children as $level1)
                                            <div x-data="{ openChild: false }">
                                                <button type="button"
                                                        @click="openChild = !openChild"
                                                        class="w-full flex items-center justify-between text-left text-sm text-gray-700 hover:text-black py-1">

                                                    <span>{{ $level1->name }}</span>

                                                    @if($level1->children->count())
                                                        <svg class="w-3 h-3 text-gray-400 transition-transform duration-200"
                                                             :class="{ 'rotate-180': openChild }"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    @endif
                                                </button>

                                                {{-- LEVEL 2 --}}
                                                @if($level1->children->count())
                                                    <div x-show="openChild"
                                                         x-transition
                                                         class="pl-4 mt-1 space-y-1">
                                                        @foreach($level1->children as $level2)
                                                            <a href="{{ route('suppliers.index', ['category' => $level2->slug]) }}"
                                                               class="block text-sm text-gray-500 hover:text-black">
                                                                {{ $level2->name }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Filters Form --}}
                <form method="GET" action="" 
                      class="mt-6 bg-white border border-gray-200 rounded-2xl p-6 space-y-6">

                    {{-- Сохраняем текущую категорию --}}
                    @if($activeCategory)
                        <input type="hidden" name="category" value="{{ $activeCategory }}">
                    @endif

                    {{-- Сохраняем выбранные типы для sidebar --}}
                    @foreach($activeTypes as $type)
                        <input type="hidden" name="supplier_type[]" value="{{ $type }}">
                    @endforeach

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-700">
                            Filters by Country
                        </h2>

                        
                    </div>

                    {{-- Country Filter --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">
                            Country of Origin
                        </h4>

                        <div class="max-h-56 overflow-y-auto space-y-2 pr-1">
                            @foreach($countries as $country)
                                <label class="flex items-center justify-between text-sm text-gray-600 hover:text-gray-900 cursor-pointer transition">
                                    <span>{{ $country->name }}</span>

                                    <input type="checkbox"
                                           name="country[]"
                                           value="{{ $country->id }}"
                                           @checked(in_array($country->id, $activeCountries))
                                           class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-0">
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Apply Button --}}
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-gray-900 text-white text-sm font-medium 
                                   hover:bg-gray-800 transition duration-200">
                        Apply Filters
                    </button>

                </form>

            </aside>

            {{-- SUPPLIER CONTENT --}}
            <div class="w-full md:flex-1">

                {{-- Top Filter Bar --}}
<form method="GET" action="/suppliers" 
      class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-5">

    {{-- Сохраняем текущую категорию --}}
    @if($activeCategory)
        <input type="hidden" name="category" value="{{ $activeCategory }}">
    @endif

    {{-- Сохраняем выбранные страны --}}
    @foreach($activeCountries as $countryId)
        <input type="hidden" name="country[]" value="{{ $countryId }}">
    @endforeach

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

        {{-- Left: Results count + Reset --}}
        <div class="flex items-center gap-4 text-sm text-gray-600 tracking-wide">
            <span>
                Showing 
                <span class="font-semibold text-gray-900">
                    {{ $suppliers->count() }}
                </span> supplier(s)
            </span>
            <a href="{{ route('suppliers.index') }}"
               class="text-sm text-gray-500 hover:text-gray-900 transition underline">
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

 {{-- Новый фильтр Golden Supplier --}}
 
    {{-- Новый фильтр Golden Supplier --}}
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
            <path d="M5 12l5 5L20 7"/>
        </svg>
        GOLD
    </span>

</label>


        </div>

    </div>
</form>

                {{-- Supplier Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @forelse($suppliers as $supplier)
        <a href="{{ url('/supplier/' . $supplier->slug) }}"
           class="group relative block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden supplier-card"
           data-country="{{ $supplier->country->name ?? '' }}">

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

            

            

            {{-- Картинка --}}
            <div class="overflow-hidden">
                <img src="{{ $supplier->catalog_image ? asset('storage/' . $supplier->catalog_image) : asset('images/no-logo.png') }}" 
                     class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105" 
                     alt="{{ $supplier->name }}">
            </div>


            {{-- Supplier Level Badge --}}
<div class="px-5 pt-3">
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold tracking-wide shadow-xl
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

        {{ strtoupper($level) }}
    </span>
</div>

           

            <div class="p-5 pt-1 text-center flex flex-col items-center">



           









                {{-- Название --}}
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-1 justify-center group-hover:text-black transition">
                    {{ $supplier->name }}
                    @if($supplier->is_verified)
                        <img src="{{ asset('images/icons/verified_icon.png') }}" 
                             alt="Verified" 
                             class="w-4 h-4">
                    @endif
                </h3>

                

                {{-- Страна + описание --}}
                <p class="text-xs text-gray-500">
                    {{ $supplier->country->name ?? '' }}
                    
                </p>

                {{-- Additional Supplier Info --}}
<div class="text-xs text-gray-600 space-y-2 mt-4 border-t pt-4 w-full pb-5">
    {{-- Type --}}
    @php
        $types = $supplier->supplierTypes->map(function($type){
            return $type->translation?->name ?? $type->slug;
        })->filter()->values();
    @endphp

    @if($types->isNotEmpty())
    <div class="flex justify-between items-center">
        <span class="flex items-center gap-1.5">

            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 21h18M4 21V7l8-4 8 4v14"/>
            </svg>

            Type
        </span>

        <span class="font-medium text-gray-800 text-right">
            {{ $types->implode(', ') }}
        </span>
    </div>
    @endif





    <div class="flex justify-between items-center">
        <span class="flex items-center gap-1.5">
            {{-- Years --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/>
            </svg>
            Years
        </span>
        <span class="font-medium text-gray-800">{{ $supplier->years_on_platform }}+</span>
    </div>

    <div class="flex justify-between items-center">
        <span class="flex items-center gap-1.5">
            {{-- Export --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/>
            </svg>
            Export
        </span>
        <span class="font-medium text-gray-800">Asia, Europe</span>
    </div>

    <div class="flex justify-between items-center">
        <span class="flex items-center gap-1.5">
            {{-- MOQ --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <rect x="3" y="7" width="18" height="13" rx="2" stroke-width="1.5"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 7l9 6 9-6"/>
            </svg>
            MOQ
        </span>
        <span class="font-medium text-gray-800">12</span>
    </div>

    <div class="flex justify-between items-center">
        <span class="flex items-center gap-1.5">
            {{-- Rating --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l2.036 6.26h6.584c.969 0 1.371 1.24.588 1.81l-5.329 3.87 2.036 6.26c.3.921-.755 1.688-1.54 1.118L12 17.77l-5.326 3.474c-.785.57-1.84-.197-1.54-1.118l2.036-6.26-5.329-3.87c-.783-.57-.38-1.81.588-1.81h6.584l2.036-6.26z"/>
            </svg>
            Rating
        </span>
        <span class="font-medium text-gray-800">{{ $rating }} / 5</span>
    </div>

    <div class="flex justify-between items-center">
    <span class="inline-flex items-center gap-1">
        {{-- SVG иконка документа --}}
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h5l5 5v9a2 2 0 01-2 2z"/>
        </svg>
        Completed Orders
    </span>
    <span class="font-medium text-gray-800">28+</span>
</div>



    @if($supplier->has_logistics)
        <div class="flex justify-between items-center text-green-700 mb-4">
            <span class="flex items-center gap-1.5">
                {{-- Logistics --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <rect x="1" y="7" width="15" height="10" rx="2" stroke-width="1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 10h3l3 3v4h-6z"/>
                </svg>
                Logistics
            </span>
            <span class="font-medium">Available</span>
        </div>
    @endif
 {{-- TRUSTED бейдж под картинкой --}}

</div>

                

            </div>
            @if($supplier->is_trusted == 1)
                <div class="absolute bottom-0 left-0 w-full bg-emerald-900 text-white text-xs font-semibold tracking-wide text-center py-2">
                    TRUSTED SUPPLIER
                </div>
                @else
<div class="absolute bottom-0 left-0 w-full bg-gray-200 text-white text-xs font-semibold tracking-wide text-center py-2">
                    STANDARD
                </div>

            @endif
        </a>
    @empty
        <p class="text-gray-500 col-span-full">No suppliers found for selected filters.</p>
    @endforelse
</div>








            </div>

        </div>
    </div>
</section>

@endsection