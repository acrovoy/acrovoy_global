@extends('layouts.app')

@section('title', 'Collections')

@section('content')

<section class="bg-white">

    <div class="mx-auto max-w-[1600px] px-6 lg:px-10 py-12">

        {{-- HERO --}}

        {{-- ===================================================== --}}
{{-- HERO --}}
{{-- ===================================================== --}}

<section class="mb-16">

    <div class="max-w-4xl">

        <div class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-4 py-2">

            <span class="text-[11px] font-semibold uppercase tracking-[0.22em] text-gray-500">
                Product Collections
            </span>

        </div>

        <h1 class="mt-6 text-4xl lg:text-6xl font-semibold tracking-tight text-gray-900 leading-tight">

            Curated collections
            <br>

            for global sourcing

        </h1>

        <p class="mt-8 max-w-3xl text-lg leading-8 text-gray-600">

            Discover carefully selected product collections from verified suppliers.
            Explore trending products, seasonal selections and professionally
            curated assortments designed to simplify sourcing.

        </p>

    </div>

</section>

        {{-- FEATURED --}}

        {{-- ===================================================== --}}
{{-- FEATURED COLLECTION --}}
{{-- ===================================================== --}}

@if($featured)

<section class="mb-20">

    <a href="{{ route('collections.show', $featured->slug) }}"
       class="group block overflow-hidden rounded-3xl border border-gray-200 bg-white">

        <div class="grid lg:grid-cols-[1.4fr_520px]">

            {{-- ================= LEFT ================= --}}

            <div class="flex flex-col justify-center p-12 lg:p-16">

                <div class="inline-flex items-center gap-2">

                    <span class="rounded-full bg-gray-900 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-white">
                        Featured Collection
                    </span>

                    <span class="text-sm text-gray-500">
                        {{ $featured->products_count }} Products
                    </span>

                </div>

                <h2 class="mt-8 text-4xl font-semibold tracking-tight text-gray-900">

                    {{ $featured->name }}

                

                </h2>

                @if($featured->short_description)

                    <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-600">

                        {{ $featured->short_description }}

                    </p>

                @endif

                <div class="mt-10">

                    <span class="inline-flex items-center gap-3 font-medium text-gray-900">

                        Explore Collection

                        <svg class="w-5 h-5 transition group-hover:translate-x-1"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 5l7 7-7 7"/>

                        </svg>

                    </span>

                </div>

            </div>

            {{-- ================= IMAGE ================= --}}

            <div class="relative bg-gray-100">

                <img
                    src="{{ $featured->cover?->cdn_url }}"
                    class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105">

            </div>

        </div>

    </a>

</section>

@endif

        {{-- GRID --}}

        {{-- ===================================================== --}}
{{-- COLLECTIONS --}}
{{-- ===================================================== --}}

<section>

    <div class="flex items-end justify-between mb-8">

        <div>

            <h2 class="text-2xl font-semibold text-gray-900">
                Latest Collections
            </h2>

            <p class="mt-2 text-gray-500">
                Browse all curated product collections.
            </p>

        </div>

    </div>


    <div class="grid gap-8 sm:grid-cols-2 xl:grid-cols-3">

        @foreach($collections as $collection)

            <a href="{{ route('collections.show', $collection->slug) }}"
               class="group overflow-hidden rounded-3xl border border-gray-200 bg-white transition duration-300 hover:border-gray-300 hover:shadow-xl">

                {{-- IMAGE --}}

                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">

                    <img
                        src="{{ $collection->cover_image_url }}"
                        class="h-full w-full object-cover transition duration-700 group-hover:scale-105">

                </div>


                {{-- CONTENT --}}

                <div class="p-7">

                    <div class="flex items-center justify-between">

                        <span class="text-xs uppercase tracking-[0.18em] text-gray-500">

                            {{ ucfirst($collection->type) }}

                        </span>

                        <span class="text-sm text-gray-500">

                            {{ $collection->products_count }} products

                        </span>

                    </div>


                    <h3 class="mt-5 text-xl font-semibold text-gray-900">

                        {{ $collection->name }}

                    </h3>


                    @if($collection->short_description)

                        <p class="mt-4 line-clamp-3 text-sm leading-7 text-gray-600">

                            {{ $collection->short_description }}

                        </p>

                    @endif


                    <div class="mt-8 flex items-center justify-between">

                        <span class="text-sm font-medium text-gray-900">

                            View Collection

                        </span>

                        <svg
                            class="h-5 w-5 text-gray-400 transition group-hover:translate-x-1"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 5l7 7-7 7"/>

                        </svg>

                    </div>

                </div>

            </a>

        @endforeach

    </div>

</section>

    </div>

</section>

@endsection