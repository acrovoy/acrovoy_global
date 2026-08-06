@extends('layouts.app')

@section('title', $collection->name)

@section('content')

<section class="bg-white">

    <div class="mx-auto max-w-[1600px] px-6 lg:px-10 py-10">

{{-- ===================================================== --}}
{{-- HERO --}}
{{-- ===================================================== --}}

<section id="collection-hero">


    {{-- ================= HEADER ================= --}}

    <div class="max-w-4xl">


        <div class="flex flex-wrap items-center gap-3">


            <span
                class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-600">

                Curated Collection

            </span>



            @if($collection->is_featured)

                <span
                    class="inline-flex items-center rounded-full bg-gray-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white">

                    Featured

                </span>

            @endif


        </div>



        <h1
            class="mt-8 text-5xl lg:text-6xl font-semibold tracking-tight leading-[1.05] text-gray-900">

            {{ $collection->name }}

        </h1>



        @if($collection->short_description)

            <p
                class="mt-8 max-w-3xl text-lg leading-8 text-gray-600">

                {{ $collection->short_description }}

            </p>

        @endif



    </div>




    {{-- ================= COVER ================= --}}


    <div class="mt-14">


        <div
            class="group relative overflow-hidden rounded-[40px] border border-gray-200 bg-gray-100">


            <div class="aspect-[16/9]">


                <img
                    src="{{ $collection->cover?->cdn_url ?? asset('images/placeholders/collection.jpg') }}"
                    alt="{{ $collection->name }}"
                    class="h-full w-full object-cover transition duration-700 group-hover:scale-105">


            </div>



            {{-- gradient --}}

            <div
                class="absolute inset-x-0 bottom-0 h-56 bg-gradient-to-t from-black/50 via-black/10 to-transparent">
            </div>



            <div
                class="absolute bottom-10 left-10">


                <div
                    class="text-xs uppercase tracking-[0.22em] text-white/70">

                    Acrovoy Collection

                </div>


                <div
                    class="mt-3 text-3xl font-semibold text-white">

                    {{ $collection->name }}

                </div>


            </div>


        </div>


    </div>




    {{-- ================= INFORMATION ================= --}}


    <div
        class="mt-10 flex flex-wrap items-center gap-10 border-b border-gray-200 pb-12">


        <div>

            <div class="text-3xl font-semibold text-gray-900">

                {{ $collection->products->count() }}

            </div>

            <div class="mt-2 text-sm text-gray-500">

                Products

            </div>

        </div>



        <div class="h-12 w-px bg-gray-200"></div>



        <div>

            <div class="text-3xl font-semibold text-gray-900">

                {{ ucfirst($collection->type) }}

            </div>

            <div class="mt-2 text-sm text-gray-500">

                Collection Type

            </div>

        </div>



        <div class="h-12 w-px bg-gray-200"></div>



        <div>

            <div class="text-3xl font-semibold text-gray-900">

                {{ $collection->updated_at->format('M Y') }}

            </div>

            <div class="mt-2 text-sm text-gray-500">

                Last Updated

            </div>

        </div>



        <div class="ml-auto">


            <div class="text-xs uppercase tracking-[0.18em] text-gray-400">

                Collection ID

            </div>


            <div class="mt-2 font-semibold text-gray-900">

                {{ $collection->public_id }}

            </div>


        </div>



    </div>


</section>


   

        {{-- ===================================================== --}}
        {{-- DESCRIPTION --}}
        {{-- ===================================================== --}}
        <section
    id="collection-description"
    class="mt-16">

    <div class="grid gap-10 lg:grid-cols-[1.6fr_420px]">

        {{-- ===================================================== --}}
        {{-- DESCRIPTION --}}
        {{-- ===================================================== --}}
        <div class="rounded-3xl border border-gray-200 bg-white p-10">

            <div class="mb-8">

                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">

                    About this Collection

                </div>

                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-gray-900">

                    Carefully selected products

                </h2>

            </div>

            @if($collection->description)

                <div class="prose prose-lg max-w-none prose-gray">

                    {!! nl2br(e($collection->description)) !!}

                </div>

            @else

                <p class="text-lg leading-8 text-gray-500">

                    This curated collection brings together products selected
                    according to quality, supplier reputation and commercial
                    value. Browse the products below to discover the complete
                    assortment.

                </p>

            @endif

        </div>

{{-- ===================================================== --}}
{{-- SUPPLIERS --}}
{{-- ===================================================== --}}

@if($suppliers->count())


<section class="rounded-3xl border border-gray-200 bg-white p-10">


    <div class="flex items-end justify-between">


        <div>

            <div class="text-xs uppercase tracking-[0.22em] text-gray-400">

                Collection Partners

            </div>


            <h3 class="mt-3 text-2xl font-semibold text-gray-900">

                Supplier(s) behind this collection

            </h3>

        </div>


        <div class="text-sm text-gray-500">

            {{ $suppliers->count() }} suppliers

        </div>


    </div>




    <div class="mt-10 divide-y divide-gray-100">


        @foreach($suppliers as $supplier)


        <a
            href="{{ route('supplier.show', $supplier->slug) }}"
            class="group flex items-center gap-8 py-8 transition">


            {{-- LOGO --}}

            <div
                class="h-28 w-28 shrink-0 overflow-hidden rounded-3xl border border-gray-200 bg-gray-50">


                <img
                    src="{{ $supplier->logo?->cdn_url ?? asset('images/no-image.png') }}"
                    alt="{{ $supplier->name }}"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105">


            </div>




            {{-- INFO --}}

            <div class="flex-1">


                <div>

<div class="flex between-justify">
    <h4 class="text-lg font-semibold text-gray-900">

        {{ $supplier->name }}
    

    </h4>
 @if($supplier->is_verified)
                                <img src="{{ asset('images/icons/verified_icon.png') }}"
                                    alt="Verified"
                                    class="w-5 h-5 flex-shrink-0">
                                @endif
</div>

  


        </div>


        @php
    $level = $supplier->level;
@endphp


@if($level)

<span
    class="mt-2 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold tracking-wide border

    {{ $level === 'Basic'
        ? 'bg-gray-50 text-gray-600 border-gray-200'
        : '' }}

    {{ $level === 'Silver'
        ? 'bg-slate-100 text-slate-700 border-slate-200'
        : '' }}

    {{ $level === 'Gold'
        ? 'bg-amber-50 text-amber-700 border-amber-200'
        : '' }}

    {{ $level === 'Platinum'
        ? 'bg-gradient-to-r from-slate-900 via-gray-700 to-slate-900 text-white border-slate-700'
        : '' }}
    ">


    {{ strtoupper($level) }} PARTNER


</span>

@endif



                @if($supplier->short_description)

                <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">

                    {{ $supplier->short_description }}

                </p>

                @endif


            </div>




            {{-- ARROW --}}

            <div
                class="text-gray-300 transition group-hover:translate-x-2 group-hover:text-gray-900">


                →

            </div>


        </a>


        @endforeach


    </div>


</section>


@endif




    </div>

</section>


        {{-- ===================================================== --}}
        {{-- FILTERS --}}
        {{-- ===================================================== --}}
        <section
            id="collection-filters"
            class="mt-12">

        </section>


        {{-- ===================================================== --}}
        {{-- PRODUCTS --}}
        {{-- ===================================================== --}}
        <section
    id="collection-products"
    class="mt-20">

    <div class="flex flex-col gap-8">

        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}

        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">

            <div>

                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500">

                    Collection Products

                </div>

                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">

                    Browse Products

                </h2>

            </div>


            <div class="flex flex-col gap-3 sm:flex-row">

                {{-- SEARCH --}}

                <div class="relative">

                    <svg
                        class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z"/>

                    </svg>

                    <input
                        type="text"
                        placeholder="Search products..."

                        class="w-72 rounded-xl border border-gray-200 bg-white py-3 pl-12 pr-4 text-sm shadow-sm transition focus:border-gray-900 focus:ring-4 focus:ring-gray-900/5">

                </div>


                {{-- SORT --}}

                <select
                    class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm">

                    <option>

                        Featured

                    </option>

                    <option>

                        Newest

                    </option>

                    <option>

                        Price ↑

                    </option>

                    <option>

                        Price ↓

                    </option>

                </select>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- PRODUCTS GRID --}}
        {{-- ===================================================== --}}

        @if($collection->products->count())

            <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">

                @foreach($collection->products as $product)

                    {{-- Здесь позже подключим готовую карточку товара --}}

                    <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                        <a href="#">

                            <div class="aspect-square overflow-hidden bg-gray-100">

                                <img
                                    src="{{ $product->main_image_url ? asset($product->main_image_url) : asset('images/no-image.png') }}"
                                    class="h-full w-full object-cover transition duration-700 hover:scale-105">

                            </div>

                        </a>

                        <div class="p-6">

                            <div class="text-xs uppercase tracking-[0.18em] text-gray-500">

                                {{ $product->supplier->company_name ?? 'Supplier' }}

                            </div>

                            <h3 class="mt-3 text-lg font-semibold text-gray-900 line-clamp-2">

                                {{ $product->name }}

                            </h3>

                            <div class="mt-3 text-sm text-gray-500">

                                SKU: {{ $product->sku }}

                            </div>

                            <div class="mt-6">

                                <a
                                    href="{{ route('product.show', $product->slug) }}"
                                    class="inline-flex items-center text-sm font-semibold text-gray-900 hover:text-black">

                                    View Product

                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="rounded-3xl border border-dashed border-gray-300 bg-gray-50 py-32 text-center">

                <h3 class="text-xl font-semibold text-gray-900">

                    No products available

                </h3>

                <p class="mt-3 text-gray-500">

                    This collection does not contain any products yet.

                </p>

            </div>

        @endif

    </div>

</section>


        {{-- ===================================================== --}}
        {{-- PAGINATION --}}
        {{-- ===================================================== --}}
        <section
    id="collection-pagination"
    class="mt-20">

    

</section>

    </div>

</section>

@endsection