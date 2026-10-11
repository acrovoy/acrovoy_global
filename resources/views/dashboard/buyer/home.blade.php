
@extends('dashboard.layout')

@section('dashboard-content')

@php
    /*
    |--------------------------------------------------------------------------
    | Buyer Dashboard — demonstration placeholders
    |--------------------------------------------------------------------------
    | Replace these values with real database data when the UI is approved.
    | Existing route names are preserved where they were provided.
    */

    $buyerDashboard = [
        'active_projects' => null,
        'open_rfqs' => null,
        'new_offers' => null,
        'unread_messages' => null,
    ];

    $buyerHasActivity = collect($buyerDashboard)->contains(
        fn ($value) => $value !== null && $value > 0
    );
@endphp

<div class="space-y-8">

    {{-- =========================================================
         1. WELCOME
    ========================================================== --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-2">
                    ACROVOY BUYER WORKSPACE
                </p>

                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">
                    Find the right furniture for your next project.
                </h1>

                <p class="text-sm sm:text-base text-gray-500 mt-2 max-w-2xl leading-relaxed">
                    Discover products, connect with manufacturers and source
                    furniture and décor for hotels, restaurants and cafés.
                </p>
            </div>

            <a href="{{ route('buyer.orders.index') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                      border border-gray-200 bg-white rounded-lg text-sm font-medium
                      text-gray-700 hover:bg-gray-50 transition whitespace-nowrap">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.7">
                    <path d="M8 4H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3"/>
                    <rect x="8" y="2" width="8" height="4" rx="1"/>
                    <path d="M8 11h8M8 15h5"/>
                </svg>
                My Orders
            </a>
        </div>
    </section>


    {{-- =========================================================
         2. MAIN SOURCING HERO
    ========================================================== --}}
    <section class="relative overflow-hidden rounded-2xl border border-gray-200
                    bg-[#F7F3EA]">

        <div class="grid grid-cols-1 lg:grid-cols-5">

            {{-- Main content --}}
            <div class="lg:col-span-3 p-6 sm:p-8 lg:p-10 flex flex-col justify-center">

                <span class="inline-flex items-center gap-2 text-xs font-semibold
                             uppercase tracking-wider text-gray-600 mb-4">
                    <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                    Your sourcing starts here
                </span>

                <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight
                           text-gray-900 leading-tight max-w-xl">
                    What are you looking for?
                </h2>

                <p class="text-sm sm:text-base text-gray-600 mt-3 mb-6
                          max-w-xl leading-relaxed">
                    Find products in the catalogue or tell us what you need.
                    We can help you discover suitable suppliers and request
                    quotations for your project.
                </p>

                {{-- Search form placeholder.
                     Connect to the actual catalogue search when its route is known. --}}
                <form action="#"
                      method="GET"
                      onsubmit="return false"
                      class="bg-white rounded-xl border border-gray-200 p-1.5
                             flex flex-col sm:flex-row gap-2 max-w-2xl">

                    <label for="buyer-product-search" class="sr-only">
                        Search products and furniture
                    </label>

                    <div class="flex-1 flex items-center gap-3 px-3 min-w-0">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0"
                             viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.7">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m16 16 4 4"/>
                        </svg>

                        <input id="buyer-product-search"
                               type="search"
                               name="q"
                               placeholder="e.g. outdoor dining chairs"
                               class="w-full min-w-0 border-0 outline-none
                                      focus:ring-0 text-sm text-gray-900
                                      placeholder:text-gray-400 bg-transparent">
                    </div>

                    <button type="button"
                            onclick="window.location.href='#buyer-discover'"
                            class="inline-flex items-center justify-center gap-2
                                   px-5 py-3 sm:py-2.5 rounded-lg bg-gray-900
                                   text-white text-sm font-medium
                                   hover:bg-gray-800 transition">
                        Explore products

                        <svg class="w-4 h-4" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </button>
                </form>

                <div class="flex flex-wrap gap-x-5 gap-y-2 mt-5 text-sm">

                    <a href="#buyer-discover"
                       class="inline-flex items-center gap-2 text-gray-700
                              hover:text-black transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.7">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <path d="M3 10h18M9 4v16"/>
                        </svg>
                        Browse furniture
                    </a>

                    <a href="{{ route('buyer.rfqs.index') }}"
                       class="inline-flex items-center gap-2 text-gray-700
                              hover:text-black transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.7">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
                        </svg>
                        My RFQs
                    </a>

                </div>
            </div>


            {{-- Decorative illustration --}}
            <div class="hidden lg:flex lg:col-span-2 items-center justify-center
                        p-8 relative">

                <div class="absolute w-64 h-64 rounded-full border border-[#D8D0C2]"></div>
                <div class="absolute w-48 h-48 rounded-full border border-[#D8D0C2]"></div>

                <svg class="relative w-full max-w-xs h-auto"
                     viewBox="0 0 300 260"
                     fill="none"
                     xmlns="http://www.w3.org/2000/svg"
                     aria-label="Furniture illustration"
                     role="img">

                    {{-- Floor --}}
                    <path d="M24 222H276"
                          stroke="#C7BDAE" stroke-width="1.5"/>

                    {{-- Table --}}
                    <path d="M84 132H221L205 146H96L84 132Z"
                          fill="#C7B49A"/>
                    <path d="M84 132H221"
                          stroke="#8D7D68" stroke-width="2"/>
                    <path d="M108 146V208M199 146V208"
                          stroke="#8D7D68" stroke-width="5"
                          stroke-linecap="round"/>

                    {{-- Left chair --}}
                    <path d="M36 121Q36 112 45 112H73Q82 112 82 121V158H36V121Z"
                          fill="#D9C8B1" stroke="#A89780" stroke-width="1.5"/>
                    <path d="M42 158V207M76 158V207"
                          stroke="#817360" stroke-width="4"
                          stroke-linecap="round"/>
                    <path d="M42 181H76"
                          stroke="#817360" stroke-width="3"/>

                    {{-- Right chair --}}
                    <path d="M221 110Q221 101 230 101H257Q266 101 266 110V148H221V110Z"
                          fill="#A7B0A0" stroke="#778373" stroke-width="1.5"/>
                    <path d="M227 148V205M260 148V205"
                          stroke="#687461" stroke-width="4"
                          stroke-linecap="round"/>
                    <path d="M227 177H260"
                          stroke="#687461" stroke-width="3"/>

                    {{-- Vase --}}
                    <path d="M142 128L147 111H164L169 128L165 143H146L142 128Z"
                          fill="#EEE7DA" stroke="#B8A995" stroke-width="1.5"/>
                    <path d="M155 111V84M155 102L140 91M155 96L170 83"
                          stroke="#7B8974" stroke-width="2"
                          stroke-linecap="round"/>
                    <path d="M140 91Q132 82 140 77Q149 82 140 91Z"
                          fill="#9DA991"/>
                    <path d="M170 83Q179 74 180 83Q176 92 170 83Z"
                          fill="#9DA991"/>
                    <path d="M155 84Q145 72 154 67Q164 74 155 84Z"
                          fill="#7B8974"/>

                    {{-- Decorative sun --}}
                    <circle cx="231" cy="49" r="17"
                            fill="#E5D7C2"/>
                    <path d="M20 53H83M32 65H72"
                          stroke="#C7BDAE" stroke-width="1.5"
                          stroke-linecap="round"/>
                </svg>
            </div>

        </div>
    </section>


    {{-- =========================================================
         3. PRIMARY BUYER ACTIONS
    ========================================================== --}}
    <section>
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                How can we help you?
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Start with the task you need to complete.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

            {{-- Discover --}}
            <a href="#buyer-discover"
               class="group bg-white border border-gray-200 rounded-xl p-5
                      hover:border-gray-300 hover:shadow-sm transition">

                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center
                            justify-center mb-4">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m16 16 4 4"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-900">
                    Discover products
                </h3>

                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    Explore furniture, materials, styles and solutions
                    for hospitality spaces.
                </p>

                <span class="inline-flex items-center gap-2 mt-4 text-sm
                             font-medium text-gray-900">
                    Explore catalogue
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </span>
            </a>


            {{-- Source a product --}}
            <a href="{{ route('buyer.rfqs.index') }}"
               class="group bg-white border border-gray-200 rounded-xl p-5
                      hover:border-gray-300 hover:shadow-sm transition">

                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center
                            justify-center mb-4">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="10.5" cy="10.5" r="6.5"/>
                        <path d="m15.5 15.5 5 5M10.5 7v7M7 10.5h7"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-900">
                    Source a product
                </h3>

                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    Looking for a specific item? Submit a request
                    and explore sourcing options.
                </p>

                <span class="inline-flex items-center gap-2 mt-4 text-sm
                             font-medium text-gray-900">
                    Start a request
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </span>
            </a>


            {{-- Projects --}}
            <a href="#buyer-projects"
               class="group bg-white border border-gray-200 rounded-xl p-5
                      hover:border-gray-300 hover:shadow-sm transition">

                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center
                            justify-center mb-4">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <path d="M8 4V2h8v2M8 10h8M8 14h5"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-900">
                    Manage a project
                </h3>

                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    Organize furniture selections, requirements and
                    supplier discussions for your next property.
                </p>

                <span class="inline-flex items-center gap-2 mt-4 text-sm
                             font-medium text-gray-900">
                    View projects
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </span>
            </a>

        </div>
    </section>


    {{-- =========================================================
         4. BUYER ACTIVITY
    ========================================================== --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-end
                    sm:justify-between gap-3 mb-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Your purchasing activity
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Keep track of requests, offers and conversations.
                </p>
            </div>

            <a href="{{ route('buyer.rfqs.index') }}"
               class="text-sm font-medium text-gray-700 hover:text-black">
                View all RFQs →
            </a>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">

            {{-- Active projects --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-sm text-gray-500">Active projects</span>
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <path d="M8 4V2h8v2"/>
                    </svg>
                </div>

                <p class="text-2xl font-semibold text-gray-900 mt-3">
                    {{ $buyerDashboard['active_projects'] ?? '—' }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    Projects in progress
                </p>
            </div>


            {{-- Open RFQs --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-sm text-gray-500">Open RFQs</span>
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                        <path d="M14 2v6h6M8 13h8M8 17h6"/>
                    </svg>
                </div>

                <p class="text-2xl font-semibold text-gray-900 mt-3">
                    {{ $buyerDashboard['open_rfqs'] ?? '—' }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    Requests awaiting progress
                </p>
            </div>


            {{-- Offers --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-sm text-gray-500">New offers</span>
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M12 3v18M5 10l7-7 7 7"/>
                    </svg>
                </div>

                <p class="text-2xl font-semibold text-gray-900 mt-3">
                    {{ $buyerDashboard['new_offers'] ?? '—' }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    Offers to review
                </p>
            </div>


            {{-- Messages --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-sm text-gray-500">Unread messages</span>
                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8Z"/>
                    </svg>
                </div>

                <p class="text-2xl font-semibold text-gray-900 mt-3">
                    {{ $buyerDashboard['unread_messages'] ?? '—' }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    Supplier conversations
                </p>
            </div>

        </div>

        {{-- Placeholder for future real activity feed --}}
        <div class="mt-4 bg-white border border-dashed border-gray-300
                    rounded-xl p-5 sm:p-6">

            <div class="flex items-start gap-3">
                <div class="w-9 h-9 flex-shrink-0 rounded-lg bg-gray-100
                            flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M12 8v4l3 2"/>
                        <circle cx="12" cy="12" r="9"/>
                    </svg>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Your latest activity
                    </h3>

                    <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                        New supplier offers, RFQ updates and project activity
                        will appear here when connected to your account data.
                    </p>
                </div>
            </div>

        </div>
    </section>


    {{-- =========================================================
         5. DISCOVER BY HOSPITALITY NEED
    ========================================================== --}}
    <section id="buyer-discover">

        <div class="flex flex-col sm:flex-row sm:items-end
                    sm:justify-between gap-3 mb-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Discover furniture for your space
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Explore solutions for different hospitality environments.
                </p>
            </div>

            {{-- TODO: Replace with the real catalogue route --}}
            <a href="#"
               class="text-sm font-medium text-gray-700 hover:text-black">
                Browse all products →
            </a>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Category 1 --}}
            <a href="#"
               class="group overflow-hidden bg-white border border-gray-200
                      rounded-xl hover:shadow-sm transition">

                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo- outdoor-chair-placeholder"
                        alt="Outdoor hospitality furniture"
                        class="hidden"
                        loading="lazy">

                    <div class="w-full h-full flex items-center justify-center
                                bg-[#F0E9DE]">
                        <svg class="w-28 h-28 text-[#A69782]" viewBox="0 0 120 120"
                             fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M25 45h70v25H25z"/>
                            <path d="M30 70v28M90 70v28"/>
                            <path d="M38 45V29q0-8 8-8h28q8 0 8 8v16"/>
                            <path d="M18 99h84"/>
                        </svg>
                    </div>
                </div>

                <div class="p-4">
                    <h3 class="font-semibold text-gray-900">
                        Outdoor Furniture
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Furniture for terraces, patios and outdoor dining.
                    </p>
                    <span class="inline-block text-sm font-medium mt-3">
                        Explore category →
                    </span>
                </div>
            </a>


            {{-- Category 2 --}}
            <a href="#"
               class="group overflow-hidden bg-white border border-gray-200
                      rounded-xl hover:shadow-sm transition">

                <div class="aspect-[4/3] bg-[#EAE8E2] flex items-center justify-center">
                    <svg class="w-28 h-28 text-[#8B897F]" viewBox="0 0 120 120"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 48h76v14H22z"/>
                        <path d="M28 62v35M92 62v35"/>
                        <path d="M34 48V30h52v18"/>
                        <path d="M40 30v18M60 30v18M80 30v18"/>
                    </svg>
                </div>

                <div class="p-4">
                    <h3 class="font-semibold text-gray-900">
                        Restaurant Furniture
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Dining chairs, tables, booths and bar seating.
                    </p>
                    <span class="inline-block text-sm font-medium mt-3">
                        Explore category →
                    </span>
                </div>
            </a>


            {{-- Category 3 --}}
            <a href="#"
               class="group overflow-hidden bg-white border border-gray-200
                      rounded-xl hover:shadow-sm transition">

                <div class="aspect-[4/3] bg-[#E7E9E5] flex items-center justify-center">
                    <svg class="w-28 h-28 text-[#838C80]" viewBox="0 0 120 120"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M24 53q0-12 12-12h48q12 0 12 12v22H24z"/>
                        <path d="M30 75v22M90 75v22"/>
                        <path d="M36 41V31h48v10"/>
                        <path d="M24 63h72"/>
                    </svg>
                </div>

                <div class="p-4">
                    <h3 class="font-semibold text-gray-900">
                        Hotel Furniture
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Guestroom, lobby and lounge furniture.
                    </p>
                    <span class="inline-block text-sm font-medium mt-3">
                        Explore category →
                    </span>
                </div>
            </a>


            {{-- Category 4 --}}
            <a href="#"
               class="group overflow-hidden bg-white border border-gray-200
                      rounded-xl hover:shadow-sm transition">

                <div class="aspect-[4/3] bg-[#EDE5E1] flex items-center justify-center">
                    <svg class="w-28 h-28 text-[#A18A7D]" viewBox="0 0 120 120"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M24 44h72v40H24z"/>
                        <path d="M24 60h72M60 44v40"/>
                        <path d="M31 84v14M89 84v14"/>
                        <path d="M31 44V31h58v13"/>
                    </svg>
                </div>

                <div class="p-4">
                    <h3 class="font-semibold text-gray-900">
                        Décor & Accessories
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Finishing touches for memorable hospitality spaces.
                    </p>
                    <span class="inline-block text-sm font-medium mt-3">
                        Explore category →
                    </span>
                </div>
            </a>

        </div>
    </section>


    {{-- =========================================================
         6. PROJECT WORKSPACE
    ========================================================== --}}
    <section id="buyer-projects"
             class="bg-white border border-gray-200 rounded-xl p-5 sm:p-6">

        <div class="flex flex-col md:flex-row md:items-center
                    md:justify-between gap-5">

            <div class="flex items-start gap-4">

                <div class="hidden sm:flex w-12 h-12 flex-shrink-0 rounded-xl
                            bg-gray-100 items-center justify-center">
                    <svg class="w-6 h-6 text-gray-700" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <rect x="3" y="4" width="18" height="16" rx="2"/>
                        <path d="M8 4V2h8v2M8 10h8M8 14h5"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Bring your project together
                    </h2>

                    <p class="text-sm text-gray-500 mt-2 max-w-2xl leading-relaxed">
                        Collect product selections, organize requirements,
                        compare supplier offers and keep your sourcing
                        discussions connected to each project.
                    </p>

                    <div class="flex flex-wrap gap-x-4 gap-y-2 mt-4 text-sm text-gray-600">
                        <span class="inline-flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Product selections
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Supplier offers
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Project discussions
                        </span>
                    </div>
                </div>

            </div>

            <div class="flex flex-col sm:flex-row gap-2 flex-shrink-0">

                {{-- TODO: Connect to the actual project creation route --}}
                <a href="#"
                   class="inline-flex justify-center items-center gap-2 px-4 py-2.5
                          rounded-lg bg-gray-900 text-white text-sm font-medium
                          hover:bg-gray-800 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Create Project
                </a>

                <a href="#buyer-projects"
                   class="inline-flex justify-center items-center px-4 py-2.5
                          rounded-lg border border-gray-200 text-sm font-medium
                          text-gray-700 hover:bg-gray-50 transition">
                    View Projects
                </a>

            </div>

        </div>
    </section>


    {{-- =========================================================
         7. ACROVOY SOURCING
    ========================================================== --}}
    <section class="overflow-hidden rounded-xl border border-gray-200
                    bg-gray-900 text-white">

        <div class="grid grid-cols-1 lg:grid-cols-5">

            <div class="lg:col-span-3 p-6 sm:p-8">

                <div class="inline-flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-gray-300" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="10.5" cy="10.5" r="6.5"/>
                        <path d="m15.5 15.5 5 5M10.5 7v7M7 10.5h7"/>
                    </svg>

                    <span class="text-xs font-semibold uppercase
                                 tracking-wider text-gray-300">
                        Acrovoy Sourcing
                    </span>
                </div>

                <h2 class="text-xl sm:text-2xl font-semibold tracking-tight">
                    Can't find what you need?
                </h2>

                <p class="text-sm text-gray-300 mt-3 max-w-xl leading-relaxed">
                    Tell us what you are looking for. We can help identify
                    suitable manufacturers, explore alternatives and
                    collect quotations for your requirements.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5 text-sm text-gray-200">
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400">✓</span>
                        <span>Products outside the catalogue</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400">✓</span>
                        <span>Custom sizes and specifications</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400">✓</span>
                        <span>Alternative manufacturers</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400">✓</span>
                        <span>Commercial offers for comparison</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mt-6">

                    <a href="{{ route('buyer.rfqs.index') }}"
                       class="inline-flex items-center justify-center gap-2
                              px-4 py-2.5 rounded-lg bg-white text-gray-900
                              text-sm font-medium hover:bg-gray-100 transition">
                        Submit a Request
                        <span>→</span>
                    </a>

                    <a href="{{ route('buyer.rfqs.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2.5
                              rounded-lg border border-gray-600 text-white
                              text-sm font-medium hover:bg-gray-800 transition">
                        Track My RFQs
                    </a>

                </div>

                <p class="text-xs text-gray-400 mt-4">
                    Sourcing availability, scope and terms are confirmed individually.
                </p>

            </div>


            <div class="hidden lg:flex lg:col-span-2 items-center justify-center
                        p-8 bg-gray-800/50">

                <svg class="w-48 h-48 text-gray-400" viewBox="0 0 160 160"
                     fill="none" stroke="currentColor" stroke-width="1.5"
                     aria-hidden="true">

                    <circle cx="80" cy="80" r="62" stroke-dasharray="4 6"/>
                    <circle cx="80" cy="80" r="42"/>

                    <rect x="51" y="51" width="58" height="58" rx="8"/>
                    <path d="M62 67h36M62 79h36M62 91h22"
                          stroke-linecap="round"/>

                    <circle cx="111" cy="108" r="20" fill="#1F2937"/>
                    <path d="m103 108 6 6 11-13"
                          stroke-linecap="round" stroke-linejoin="round"/>

                    <path d="M28 38h18M37 29v18M116 32h16M124 24v16"
                          stroke-linecap="round"/>

                </svg>

            </div>

        </div>
    </section>


    {{-- =========================================================
         8. SUPPLIER DISCOVERY
    ========================================================== --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-end
                    sm:justify-between gap-3 mb-4">

            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Find the right supplier
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Explore manufacturers and companies that match your needs.
                </p>
            </div>

            {{-- TODO: Connect to the real supplier directory route --}}
            <a href="#"
               class="text-sm font-medium text-gray-700 hover:text-black">
                Browse suppliers →
            </a>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center
                            justify-center mb-4">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M3 21V8l9-5 9 5v13"/>
                        <path d="M7 21v-7h10v7M8 10h.01M12 10h.01M16 10h.01"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-900">
                    Manufacturers
                </h3>

                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    Explore companies with production capabilities
                    and custom manufacturing options.
                </p>
            </div>


            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center
                            justify-center mb-4">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <path d="M3 7h18v13H3zM7 7V4h10v3"/>
                        <path d="M3 12h18M10 12v3h4v-3"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-900">
                    Distributors & Exporters
                </h3>

                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    Find companies offering products for commercial
                    supply and international trade.
                </p>
            </div>


            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center
                            justify-center mb-4">
                    <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.7">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-900">
                    Global Sourcing
                </h3>

                <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                    Explore sourcing options across suppliers
                    and markets for your requirements.
                </p>
            </div>

        </div>
    </section>


    {{-- =========================================================
         9. SECONDARY NAVIGATION
    ========================================================== --}}
    <section class="border-t border-gray-200 pt-5">

        <div class="flex flex-col sm:flex-row sm:items-center
                    sm:justify-between gap-3">

            <p class="text-sm text-gray-500">
                Everything you need to manage your purchasing activity.
            </p>

            <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm">

                <a href="{{ route('buyer.orders.index') }}"
                   class="text-gray-600 hover:text-gray-900 transition">
                    Orders
                </a>

                <a href="{{ route('buyer.rfqs.index') }}"
                   class="text-gray-600 hover:text-gray-900 transition">
                    RFQs & Offers
                </a>

                <a href="#buyer-projects"
                   class="text-gray-600 hover:text-gray-900 transition">
                    Projects
                </a>

            </div>

        </div>
    </section>

</div>

@endsection
