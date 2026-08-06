@extends('layouts.app')

@section('content')

{{-- ==========================================================
    HERO
========================================================== --}}
<section class="relative overflow-hidden bg-gradient-to-b from-stone-50 via-white to-white">

    {{-- Background decoration --}}
    <div class="absolute inset-0 pointer-events-none">

        <div class="absolute -right-40 -top-40 w-[600px] h-[600px] rounded-full bg-stone-100 blur-3xl opacity-70"></div>

        <div class="absolute -left-52 bottom-0 w-[450px] h-[450px] rounded-full bg-slate-100 blur-3xl opacity-60"></div>

    </div>

    <div class="container relative mx-auto px-6 lg:px-12 py-24">

        <div class="grid lg:grid-cols-12 gap-16 items-center">

            {{-- ===================================================== --}}
            {{-- LEFT --}}
            {{-- ===================================================== --}}

            <div class="lg:col-span-5">

                <span class="inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm">

                    Hospitality Furniture & Décor Marketplace

                </span>

                <h1 class="mt-8 text-5xl lg:text-7xl font-black leading-[1.05] tracking-tight text-slate-900">

                    Design

                    <span class="text-slate-500">

                        Better

                    </span>

                    Hospitality
                    Spaces

                </h1>

                <p class="mt-8 text-xl leading-9 text-gray-600 max-w-xl">

                    Find verified manufacturers of hotel furniture,
                    restaurant furniture, lighting, décor,
                    outdoor collections and hospitality accessories
                    from around the world.

                </p>

                {{-- CTA --}}

                <div class="flex flex-wrap gap-4 mt-10">

                    <a href="/suppliers"
                       class="rounded-xl bg-slate-900 px-8 py-4 text-white font-semibold transition hover:bg-black">

                        Browse Suppliers

                    </a>

                    <a href="{{ route('buyer.rfqs.create') }}"
                       class="rounded-xl border border-gray-300 bg-white px-8 py-4 font-semibold hover:bg-gray-50">

                        Submit RFQ

                    </a>

                </div>

                {{-- Industries --}}

                <div class="mt-12 flex flex-wrap gap-3">

                    @foreach([
                        'Hotels',
                        'Restaurants',
                        'Resorts',
                        'Cafés',
                        'Bars',
                        'Outdoor'
                    ] as $item)

                        <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700">

                            {{ $item }}

                        </span>

                    @endforeach

                </div>

                {{-- Trust --}}

                <div class="grid grid-cols-2 gap-6 mt-14">

                    <div class="rounded-2xl border border-gray-200 bg-white p-5">

                        <div class="font-semibold text-slate-900">

                            Verified Manufacturers

                        </div>

                        <div class="mt-2 text-sm text-gray-500">

                            Carefully reviewed supplier profiles.

                        </div>

                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5">

                        <div class="font-semibold text-slate-900">

                            International Export

                        </div>

                        <div class="mt-2 text-sm text-gray-500">

                            Manufacturers experienced in global trade.

                        </div>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- RIGHT --}}
            {{-- ===================================================== --}}

            <div class="lg:col-span-7">

                <div class="grid grid-cols-2 gap-5">

                    {{-- BIG IMAGE --}}

                    <div class="col-span-2 overflow-hidden rounded-[32px] shadow-xl">

                        <img
                            src="{{ asset('images/home/banners/hero.jpg') }}"
                            class="h-[340px] w-full object-cover hover:scale-105 duration-700"
                            alt="Hospitality">

                    </div>

                    {{-- CARD 1 --}}

                    <div class="overflow-hidden rounded-[28px] shadow-lg">

                        <img
                            src="{{ asset('images/home/banners/hotel.jpg') }}"
                            class="h-[270px] w-full object-cover hover:scale-110 duration-700">

                    </div>

                    {{-- CARD 2 --}}

                    <div class="overflow-hidden rounded-[28px] shadow-lg">

                        <img
                            src="{{ asset('images/home/banners/outdoor.jpg') }}"
                            class="h-[270px] w-full object-cover hover:scale-110 duration-700">

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- ==========================================================
    2. HOSPITALITY COLLECTIONS
========================================================== --}}
<section class="py-28 bg-white">

    <div class="container mx-auto px-6 lg:px-12">

        {{-- Header --}}

        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-end gap-8">

            <div class="max-w-3xl">

                <span class="text-xs uppercase tracking-[0.25em] text-slate-500 font-semibold">

                    Explore Collections

                </span>

                <h2 class="mt-4 text-5xl font-bold tracking-tight text-slate-900">

                    Furniture & Interior Collections

                </h2>

                <p class="mt-6 text-lg leading-8 text-gray-600">

                    Browse curated hospitality collections from verified suppliers
                    specializing in hotels, restaurants, cafés, resorts and commercial interiors.

                </p>

            </div>

            <a href="{{ route('collections.index') }}"
               class="hidden lg:flex items-center font-semibold text-slate-900 hover:text-black">

                View All Collections

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 ml-2"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"/>

                </svg>

            </a>

        </div>

        {{-- Grid --}}

        <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-10 mt-20">

            @foreach($categories as $cat)

                <a href="{{ $cat['link'] }}"
                   class="group block">

                    <div
                        class="relative overflow-hidden rounded-[34px] bg-slate-100 aspect-[4/5]">

                        <img
                            src="{{ asset($cat['image']) }}"
                            alt="{{ $cat['title'] }}"
                            class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">

                        {{-- Overlay --}}

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-80">

                        </div>

                        {{-- Badge --}}

                        <div
                            class="absolute top-6 left-6">

                            <span
                                class="rounded-full bg-white/90 backdrop-blur px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-800">

                                Collection

                            </span>

                        </div>

                        {{-- Bottom --}}

                        <div
                            class="absolute bottom-0 left-0 right-0 p-8">

                            <h3
                                class="text-2xl font-bold text-white">

                                {{ $cat['title'] }}

                            </h3>

                            <div
                                class="mt-4 flex items-center justify-between">

                                <span
                                    class="text-white/80">

                                    Hospitality Solutions

                                </span>

                                <div
                                    class="w-12 h-12 rounded-full bg-white text-slate-900 flex items-center justify-center font-bold transition group-hover:bg-slate-900 group-hover:text-white">

                                    →

                                </div>

                            </div>

                        </div>

                    </div>

                </a>

            @endforeach

        </div>

        {{-- Bottom Banner --}}

        <div
            class="mt-24 rounded-[36px] bg-slate-900 overflow-hidden">

            <div
                class="grid lg:grid-cols-2 items-center">

                <div
                    class="p-12 lg:p-16">

                    <span
                        class="uppercase tracking-[0.25em] text-xs text-slate-400">

                        Hospitality Projects

                    </span>

                    <h3
                        class="mt-5 text-4xl font-bold text-white leading-tight">

                        Designed for Hotels,
                        Restaurants,
                        Resorts &
                        Commercial Spaces

                    </h3>

                    <p
                        class="mt-6 text-slate-300 text-lg leading-8">

                        From luxury hotel lobbies to outdoor lounges and fine dining interiors,
                        discover collections created specifically for hospitality projects.

                    </p>

                    <a
                        href="/suppliers"
                        class="inline-flex mt-10 rounded-xl bg-white px-7 py-4 font-semibold text-slate-900 hover:bg-gray-100 transition">

                        Explore Suppliers

                    </a>

                </div>

                <div
                    class="hidden lg:block h-full">

                    <img
                        src="{{ asset('images/home/banners/spa.jpg') }}"
                        class="w-full h-full object-cover">

                </div>

            </div>

        </div>

    </div>

</section>

{{-- ==========================================================
    3. WHY ACROVOY
========================================================== --}}
<section class="py-28 bg-stone-50">

    <div class="container mx-auto px-6 lg:px-12">

        <div class="max-w-3xl mx-auto text-center">

            <span class="text-xs uppercase tracking-[0.25em] text-slate-500 font-semibold">
                Why Acrovoy
            </span>

            <h2 class="mt-4 text-5xl font-bold tracking-tight text-slate-900">
                Built for Hospitality Procurement
            </h2>

            <p class="mt-6 text-lg text-gray-600 leading-8">
                Everything you need to discover trusted suppliers,
                compare suppliers and source furniture for hospitality projects.
            </p>

        </div>

        <div class="grid lg:grid-cols-2 gap-8 mt-20">

            @foreach($advantages as $index => $adv)

                @php
    $icons = [

        // Verified
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                d="M9 12l2 2 4-4M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
        </svg>',

        // Factory
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                d="M3 21h18M5 21V10l5 3V8l5 3V5l4 2v14"/>
        </svg>',

        // Globe
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <circle cx="12" cy="12" r="9" stroke-width="1.7"/>
            <path stroke-width="1.7" d="M3 12h18M12 3a15 15 0 010 18M12 3a15 15 0 000 18"/>
        </svg>',

        // Chair
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                d="M7 10V7a5 5 0 0110 0v3M6 10h12v6H6zm1 6v3m10-3v3"/>
        </svg>',

        // Package
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                d="M21 8l-9-5-9 5 9 5 9-5zm-18 0v8l9 5 9-5V8"/>
        </svg>',

        // Partnership
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                d="M8 12l3 3 5-6M5 5h14v14H5z"/>
        </svg>',

        // Shipping
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                d="M1 7h15v10H1zm15 3h4l3 3v4h-7"/>
        </svg>',

        // Premium
        '<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                d="M12 3l2.6 5.3 5.9.9-4.2 4.1 1 5.8L12 16.8l-5.3 2.8 1-5.8L3.5 9.2l5.9-.9L12 3z"/>
        </svg>',

    ];
@endphp

                <div
                    class="group bg-white rounded-[32px] border border-gray-200 p-10 hover:shadow-2xl hover:-translate-y-1 transition duration-300">

                    <div class="flex items-start justify-between">

                       <div
    class="w-16 h-16 rounded-2xl
           bg-gradient-to-br
           from-white
           to-slate-100
           border border-slate-200
           shadow-sm
           flex items-center justify-center
           text-slate-700
           group-hover:shadow-xl
           group-hover:-translate-y-1
           transition-all duration-300">

    {!! $icons[$index % count($icons)] !!}

</div>

                        <span
                            class="text-xs uppercase tracking-widest text-slate-400">

                            Benefit

                        </span>

                    </div>

                    <h3
                        class="mt-8 text-2xl font-bold text-slate-900">

                        {{ $adv['title'] }}

                    </h3>

                    <p
                        class="mt-5 text-gray-600 leading-8">

                        {{ $adv['text'] }}

                    </p>

                    <div
                        class="mt-8 flex items-center text-sm font-semibold text-slate-900">

                        Learn More

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 ml-2 transition group-hover:translate-x-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"/>

                        </svg>

                    </div>

                </div>

            @endforeach

        </div>

        {{-- Bottom Numbers --}}

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-10 mt-24 pt-12 border-t border-gray-200">

            <div>
                <div class="text-4xl font-bold text-slate-900">180+</div>
                <div class="mt-2 text-gray-500">Export Markets</div>
            </div>

            <div>
                <div class="text-4xl font-bold text-slate-900">24/7</div>
                <div class="mt-2 text-gray-500">Business Access</div>
            </div>

            <div>
                <div class="text-4xl font-bold text-slate-900">100%</div>
                <div class="mt-2 text-gray-500">Hospitality Focus</div>
            </div>

            <div>
                <div class="text-4xl font-bold text-slate-900">B2B</div>
                <div class="mt-2 text-gray-500">Professional Platform</div>
            </div>

        </div>

    </div>

</section>

{{-- ==========================================================
    4. FEATURED SUPPLIERS
========================================================== --}}
<section class="py-28 bg-white">

    <div class="container mx-auto px-6 lg:px-12">

        {{-- Header --}}

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">

            <div class="max-w-3xl">

                <span class="uppercase tracking-[0.25em] text-xs text-slate-500 font-semibold">

                    Trusted Manufacturers

                </span>

                <h2 class="mt-4 text-5xl font-bold tracking-tight text-slate-900">

                    Featured Suppliers

                </h2>

                <p class="mt-6 text-lg leading-8 text-gray-600">

                    Discover verified hospitality suppliers trusted by buyers
                    worldwide.

                </p>

            </div>

            <a href="/suppliers"
               class="hidden lg:flex items-center font-semibold text-slate-900 hover:text-black">

                View All Suppliers

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 ml-2"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5l7 7-7 7"/>

                </svg>

            </a>

        </div>

        {{-- Suppliers --}}

        <div class="grid lg:grid-cols-3 gap-10 mt-20">

            @foreach($featuredSuppliers as $supplier)

                <a
                    href="{{ $supplier['link'] }}"
                    class="group rounded-[34px] overflow-hidden border border-gray-200 bg-white hover:shadow-2xl hover:border-slate-300 transition duration-300">

                    {{-- Image --}}

                    <div class="relative overflow-hidden">

                        <img
                            src="{{ asset($supplier['image']) }}"
                            class="w-full h-72 object-cover group-hover:scale-105 transition duration-700"
                            alt="{{ $supplier['name'] }}">

                        <div
                            class="absolute top-5 left-5">

                            <span
                                class="rounded-full bg-white/90 backdrop-blur px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-800">

                                Verified

                            </span>

                        </div>

                    </div>

                    {{-- Body --}}

                    <div class="p-8">

                        <h3
                            class="text-2xl font-bold text-slate-900">

                            {{ $supplier['name'] }}

                        </h3>

                        <p
                            class="mt-2 text-gray-500">

                            {{ $supplier['country'] }}

                        </p>

                        <div
                            class="mt-6 rounded-2xl bg-slate-50 p-5">

                            <div
                                class="text-xs uppercase tracking-widest text-gray-400">

                                Specialization

                            </div>

                            <div
                                class="mt-2 font-medium text-slate-800">

                                {{ $supplier['products'] }}

                            </div>

                        </div>

                        {{-- Features --}}

                        <div
                            class="grid grid-cols-2 gap-3 mt-6 text-sm">

                            <div
                                class="rounded-xl bg-gray-50 px-4 py-3">

                                ✓ Export Ready

                            </div>

                            <div
                                class="rounded-xl bg-gray-50 px-4 py-3">

                                ✓ OEM / ODM

                            </div>

                            <div
                                class="rounded-xl bg-gray-50 px-4 py-3">

                                ✓ Hospitality

                            </div>

                            <div
                                class="rounded-xl bg-gray-50 px-4 py-3">

                                ✓ Verified

                            </div>

                        </div>

                        {{-- Footer --}}

                        <div
                            class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">

                            <span
                                class="font-semibold text-slate-900">

                                View Company

                            </span>

                            <div
                                class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center transition group-hover:translate-x-1">

                                →

                            </div>

                        </div>

                    </div>

                </a>

            @endforeach

        </div>

    </div>

</section>

{{-- ==========================================================
    5. FEATURED COLLECTIONS
========================================================== --}}
<section class="py-28 bg-stone-50">

    <div class="container mx-auto px-6 lg:px-12">

        {{-- Header --}}

        <div class="max-w-3xl">

            <span class="text-xs uppercase tracking-[0.25em] text-slate-500 font-semibold">

                Curated Spaces

            </span>

            <h2 class="mt-4 text-5xl font-bold tracking-tight text-slate-900">

                Featured Hospitality Collections

            </h2>

            <p class="mt-6 text-lg leading-8 text-gray-600">

                Explore furniture and interior concepts created for hotels,
                restaurants, cafés, resorts and commercial hospitality spaces.

            </p>

        </div>

        {{-- Collections Grid --}}

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-10 mt-20">

            @foreach($collections as $collection)

                <a
                    href="{{ $collection['link'] }}"
                    class="group overflow-hidden rounded-[34px] bg-white border border-gray-200 hover:border-slate-300 hover:shadow-2xl transition duration-300">

                    {{-- Image --}}

                    <div class="relative overflow-hidden">

                        <img
                            src="{{ asset($collection['image']) }}"
                            class="w-full h-80 object-cover transition duration-700 group-hover:scale-110"
                            alt="{{ $collection['title'] }}">

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent">

                        </div>

                        <div
                            class="absolute top-6 left-6">

                            <span
                                class="rounded-full bg-white/90 backdrop-blur px-4 py-2 text-xs uppercase tracking-widest font-semibold text-slate-900">

                                Collection

                            </span>

                        </div>

                    </div>

                    {{-- Content --}}

                    <div class="p-8">

                        <h3
                            class="text-2xl font-bold text-slate-900">

                            {{ $collection['title'] }}

                        </h3>

                        <p
                            class="mt-4 text-gray-600 leading-7">

                            Designed to inspire hospitality projects with
                            coordinated furniture, décor and commercial interiors.

                        </p>

                        <div
                            class="mt-8 flex items-center justify-between">

                            <span
                                class="font-semibold text-slate-900">

                                Explore Collection

                            </span>

                            <div
                                class="w-11 h-11 rounded-full bg-slate-900 text-white flex items-center justify-center transition group-hover:translate-x-1">

                                →

                            </div>

                        </div>

                    </div>

                </a>

            @endforeach

        </div>

        

    </div>

</section>

{{-- ==========================================================
    6. RFQ CTA
========================================================== --}}

<section class="py-28 bg-white">

    <div class="container mx-auto px-6 lg:px-12">

        <div class="overflow-hidden rounded-[40px] bg-slate-900">

            <div class="grid lg:grid-cols-2 items-center">

                {{-- LEFT --}}

                <div class="p-12 lg:p-20">

                    <span class="uppercase tracking-[0.25em] text-xs text-slate-400">

                        Hospitality Projects

                    </span>

                    <h2 class="mt-5 text-5xl font-bold leading-tight text-white">

                        Planning Your Next
                        Hotel or Restaurant
                        Project?

                    </h2>

                    <p class="mt-8 text-lg leading-8 text-slate-300">

                        Submit your project requirements and receive quotations
                        from verified hospitality furniture manufacturers.

                        Whether you are furnishing a boutique hotel,
                        luxury resort, restaurant, café or commercial
                        interior project, ACROVOY helps you connect
                        with trusted suppliers worldwide.

                    </p>

                    <div class="grid grid-cols-2 gap-4 mt-10">

                        <div class="rounded-2xl bg-white/5 p-5">

                            <div class="text-white font-semibold">

                                Verified Suppliers

                            </div>

                            <div class="mt-2 text-slate-400 text-sm">

                                Manufacturers with export experience.

                            </div>

                        </div>

                        <div class="rounded-2xl bg-white/5 p-5">

                            <div class="text-white font-semibold">

                                Fast Quotations

                            </div>

                            <div class="mt-2 text-slate-400 text-sm">

                                Receive multiple offers.

                            </div>

                        </div>

                        <div class="rounded-2xl bg-white/5 p-5">

                            <div class="text-white font-semibold">

                                Hospitality Focus

                            </div>

                            <div class="mt-2 text-slate-400 text-sm">

                                Hotels, restaurants and resorts.

                            </div>

                        </div>

                        <div class="rounded-2xl bg-white/5 p-5">

                            <div class="text-white font-semibold">

                                Global Network

                            </div>

                            <div class="mt-2 text-slate-400 text-sm">

                                Manufacturers worldwide.

                            </div>

                        </div>

                    </div>

                    <div class="mt-12 flex flex-wrap gap-4">

                        <a
                            href="{{ route('buyer.rfqs.create') }}"
                            class="rounded-xl bg-white px-8 py-4 font-semibold text-slate-900 hover:bg-gray-100 transition">

                            Submit RFQ

                        </a>

                        <a
                            href="/suppliers"
                            class="rounded-xl border border-white/20 px-8 py-4 font-semibold text-white hover:bg-white/10 transition">

                            Browse Suppliers

                        </a>

                    </div>

                </div>

                {{-- RIGHT --}}

                <div class="hidden lg:block h-full">

                    <img
                        src="{{ asset('images/home/banners/hotel2.jpg') }}"
                        class="w-full h-full object-cover"
                        alt="Hospitality Project">

                </div>

            </div>

        </div>

    </div>

</section>

{{-- ==========================================================
    7. GLOBAL NETWORK + ABOUT + FOOTER CTA
========================================================== --}}

<section class="py-28 bg-stone-50">

    <div class="container mx-auto px-6 lg:px-12">

        {{-- ===================================================== --}}
        {{-- Supplier Countries --}}
        {{-- ===================================================== --}}

        <div class="text-center max-w-3xl mx-auto">

            <span class="uppercase tracking-[0.25em] text-xs font-semibold text-slate-500">

                Global Network

            </span>

            <h2 class="mt-4 text-5xl font-bold tracking-tight text-slate-900">

                Our Suppliers Around the World

            </h2>

            <p class="mt-6 text-lg leading-8 text-gray-600">

                Connect with suppliers from leading hospitality
                production regions and discover suppliers for projects
                of every size.

            </p>

        </div>

        <div class="flex flex-wrap justify-center gap-4 mt-16">

            @foreach($countriees as $country)

                <div
                    class="rounded-full border border-gray-200 bg-white px-6 py-3 font-medium text-slate-700 hover:border-slate-400 hover:shadow-lg transition">

                    {{ $country }}

                </div>

            @endforeach

        </div>

    </div>

</section>


{{-- ==========================================================
    ABOUT
========================================================== --}}

<section class="py-28 bg-white">

    <div class="container mx-auto px-6 lg:px-12">

        <div class="grid lg:grid-cols-2 gap-20 items-center">

            {{-- LEFT --}}

            <div>

                <span class="uppercase tracking-[0.25em] text-xs font-semibold text-slate-500">

                    About ACROVOY

                </span>

                <h2 class="mt-4 text-5xl font-bold tracking-tight text-slate-900">

                    The Global Marketplace
                    for Hospitality Interiors

                </h2>

                <p class="mt-8 text-lg leading-8 text-gray-600">

                    ACROVOY is a professional B2B platform connecting buyers
                    with verified suppliers of hospitality furniture,
                    lighting, décor, outdoor collections and commercial
                    interior solutions.

                </p>

                <p class="mt-6 text-lg leading-8 text-gray-600">

                    Whether you're furnishing a boutique hotel,
                    luxury resort, restaurant, café or large commercial
                    development, ACROVOY helps you discover reliable
                    suppliers and build long-term partnerships.

                </p>

                <div class="grid grid-cols-2 gap-6 mt-12">

                    <div>

                        <div class="text-4xl font-bold text-slate-900">

                            180+

                        </div>

                        <div class="mt-2 text-gray-500">

                            Countries

                        </div>

                    </div>

                    <div>

                        <div class="text-4xl font-bold text-slate-900">

                            B2B

                        </div>

                        <div class="mt-2 text-gray-500">

                            Marketplace

                        </div>

                    </div>

                </div>

            </div>

            {{-- RIGHT --}}

            <div>

                <div
                    class="overflow-hidden rounded-[36px] shadow-2xl">

                    <img
                        src="{{ asset('images/home/banners/outdoor1.jpg') }}"
                        class="w-full h-[650px] object-cover"
                        alt="About Acrovoy">

                </div>

            </div>

        </div>

    </div>

</section>







@endsection
