
@extends('dashboard.layout')

@section('dashboard-content')

@php
    $company = ActiveContext::company();
    $supplierId = ActiveContext::supplierId();
    $supplier = $supplierId ? \App\Models\Supplier::find($supplierId) : null;

    /*
    |--------------------------------------------------------------------------
    | Supplier reputation
    |--------------------------------------------------------------------------
    */

    $score = max((int) ($supplier?->reputation ?? 0), 0);

    if ($score <= 50) {
        $level = 'Bronze';
        $nextLevelScore = 51;
    } elseif ($score <= 120) {
        $level = 'Silver';
        $nextLevelScore = 121;
    } elseif ($score <= 200) {
        $level = 'Gold';
        $nextLevelScore = 201;
    } else {
        $level = 'Platinum';
        $nextLevelScore = $score;
    }

    $progress = $nextLevelScore > 0
        ? min(($score / $nextLevelScore) * 100, 100)
        : 0;

    $pointsNeeded = max($nextLevelScore - $score, 0);

    /*
    |--------------------------------------------------------------------------
    | Demonstration data
    |--------------------------------------------------------------------------
    | Replace these values with real database statistics when available.
    */

    $totalOrders = 24;
    $newOrders = 3;
    $processingOrders = 7;
    $completedOrders = 14;

    $activeRfqs = 5;
    $unansweredRfqs = 2;

    $activeProjects = 6;
    $pendingProjects = 2;

    $unreadMessages = 4;
    $rating = 4.6;
    $openDisputes = 1;

    $supplierMetrics = [
        [
            'label' => 'Total orders',
            'value' => $totalOrders,
            'detail' => $newOrders . ' new orders',
            'icon' => 'orders',
            'href' => route('supplier.orders'),
        ],
        [
            'label' => 'Active RFQs',
            'value' => $activeRfqs,
            'detail' => $unansweredRfqs . ' awaiting your response',
            'icon' => 'rfq',
            'href' => route('supplier.rfqs.index'),
        ],
        [
            'label' => 'Active projects',
            'value' => $activeProjects,
            'detail' => $pendingProjects . ' pending',
            'icon' => 'projects',
            'href' => '#supplier-projects',
        ],
        [
            'label' => 'Unread messages',
            'value' => $unreadMessages,
            'detail' => 'Buyer conversations',
            'icon' => 'messages',
            'href' => null,
        ],
    ];
@endphp

{{-- ========================================================= --}}
{{-- WELCOME HEADER --}}
{{-- ========================================================= --}}

<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

        <div>
            <p class="text-sm font-medium text-gray-500 mb-2">
                Supplier workspace
            </p>

            <h1 class="text-3xl font-semibold tracking-tight text-gray-900">
                Welcome{{ $company?->name ? ', ' . $company->name : '' }}
            </h1>

            <p class="text-gray-500 mt-2 max-w-2xl leading-relaxed">
                Discover buyer opportunities, respond to sourcing requests,
                manage orders and build your reputation on Acrovoy.
            </p>
        </div>

        <a href="{{ route('supplier.orders') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                  bg-gray-900 text-white text-sm font-medium rounded-lg
                  hover:bg-gray-800 transition shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.7">
                <path d="M6 3h12l2 4H4l2-4Z"/>
                <path d="M4 7v13h16V7M9 11h6"/>
            </svg>
            Manage orders
        </a>

    </div>
</div>


{{-- ========================================================= --}}
{{-- HERO: BUYER OPPORTUNITIES --}}
{{-- ========================================================= --}}

<div class="relative overflow-hidden bg-[#F7F3EA] border border-gray-200
            rounded-2xl p-6 sm:p-8 mb-8">

    <div class="absolute -right-10 -top-12 w-56 h-56 rounded-full
                border border-gray-300/60 pointer-events-none"></div>

    <div class="absolute -right-2 -top-4 w-36 h-36 rounded-full
                border border-gray-300/60 pointer-events-none"></div>

    <div class="relative flex flex-col lg:flex-row lg:items-center
                justify-between gap-8">

        <div class="max-w-2xl">

            <span class="inline-flex items-center gap-2 text-xs
                         font-semibold uppercase tracking-wider text-gray-600 mb-4">
                <span class="w-2 h-2 rounded-full bg-gray-900"></span>
                Your next business opportunity
            </span>

            <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight
                       text-gray-900 leading-tight">
                Find projects worth producing for.
            </h2>

            <p class="mt-3 text-gray-600 text-sm sm:text-base
                      leading-relaxed max-w-xl">
                Connect with buyers looking for hospitality furniture and décor.
                Review their requirements, prepare a competitive offer and
                build long-term business relationships.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 mt-6">

                <a href="{{ route('supplier.rfqs.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3
                          bg-gray-900 text-white text-sm font-medium rounded-lg
                          hover:bg-gray-800 transition">
                    Explore buyer requests

                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8">
                        <path d="M5 12h14M13 6l6 6-6 6"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <a href="#supplier-projects"
                   class="inline-flex items-center justify-center px-5 py-3
                          bg-white/70 text-gray-800 text-sm font-medium
                          border border-gray-300 rounded-lg
                          hover:bg-white transition">
                    Explore project opportunities
                </a>

            </div>
        </div>

        <div class="hidden lg:flex items-center justify-center
                    w-44 h-44 shrink-0 rounded-2xl
                    bg-white/60 border border-white">

            <svg class="w-24 h-24 text-gray-700"
                 viewBox="0 0 100 100" fill="none">

                <path d="M14 43 50 22l36 21v40H14V43Z"
                      stroke="currentColor" stroke-width="1.7"
                      stroke-linejoin="round"/>

                <path d="M27 83V49h46v34"
                      stroke="currentColor" stroke-width="1.7"/>

                <path d="M39 83V62h22v21"
                      stroke="currentColor" stroke-width="1.7"/>

                <path d="M35 35h30"
                      stroke="currentColor" stroke-width="1.7"
                      stroke-linecap="round"/>

                <circle cx="77" cy="24" r="10"
                        fill="#F7F3EA" stroke="currentColor"
                        stroke-width="1.5"/>

                <path d="m73 24 3 3 5-6"
                      stroke="currentColor" stroke-width="1.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

        </div>

    </div>
</div>


{{-- ========================================================= --}}
{{-- BUSINESS OVERVIEW --}}
{{-- ========================================================= --}}

<div class="flex flex-col sm:flex-row sm:items-end
            sm:justify-between gap-2 mb-4">

    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            Your business overview
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            A quick look at your current activity on Acrovoy.
        </p>
    </div>

    <span class="text-xs text-gray-400">
        Demonstration data
    </span>

</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

    @foreach ($supplierMetrics as $metric)
        <a href="{{ $metric['href'] ?? '#supplier-messages' }}"
           @if (!$metric['href']) aria-disabled="true" @endif
           class="group bg-white border border-gray-200 rounded-xl p-5
                  shadow-sm transition
                  {{ $metric['href'] ? 'hover:border-gray-300 hover:shadow-md' : 'cursor-default' }}">

            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-sm text-gray-500">
                        {{ $metric['label'] }}
                    </p>

                    <p class="text-3xl font-semibold tracking-tight
                              text-gray-900 mt-3">
                        {{ $metric['value'] }}
                    </p>

                    <p class="text-xs text-gray-500 mt-2">
                        {{ $metric['detail'] }}
                    </p>
                </div>

                <div class="w-10 h-10 rounded-lg bg-gray-50 border
                            border-gray-100 flex items-center justify-center
                            text-gray-600 shrink-0">

                    @if ($metric['icon'] === 'orders')
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6">
                            <path d="M6 3h12l2 4H4l2-4Z"/>
                            <path d="M4 7v13h16V7M9 11h6"/>
                        </svg>
                    @elseif ($metric['icon'] === 'rfq')
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6">
                            <path d="M5 3h10l4 4v14H5V3Z"
                                  stroke-linejoin="round"/>
                            <path d="M14 3v5h5M8 12h8M8 16h6"
                                  stroke-linecap="round"/>
                        </svg>
                    @elseif ($metric['icon'] === 'projects')
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <path d="M3 10h18M9 10v10"
                                  stroke-linecap="round"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6">
                            <path d="M21 11.5a7.5 7.5 0 0 1-7.5 7.5H7l-4 2v-6.5A7.5 7.5 0 1 1 21 11.5Z"
                                  stroke-linejoin="round"/>
                            <path d="M8 10h8M8 14h5"
                                  stroke-linecap="round"/>
                        </svg>
                    @endif

                </div>
            </div>

            @if ($metric['href'])
                <div class="flex items-center justify-between mt-5 pt-3
                            border-t border-gray-100 text-xs text-gray-500">
                    <span>View details</span>

                    <svg class="w-4 h-4 group-hover:translate-x-0.5
                                transition-transform"
                         viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7">
                        <path d="M5 12h14M13 6l6 6-6 6"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </div>
            @endif

        </a>
    @endforeach

</div>


{{-- ========================================================= --}}
{{-- SUPPLIER REPUTATION --}}
{{-- ========================================================= --}}

<div class="bg-white border border-gray-200 rounded-2xl
            p-5 sm:p-7 shadow-sm mb-8">

    <div class="flex flex-col sm:flex-row sm:items-start
                sm:justify-between gap-5 mb-6">

        <div class="flex items-start gap-4">

            <div class="w-12 h-12 rounded-xl bg-[#F7F3EA]
                        flex items-center justify-center shrink-0">

                <svg class="w-6 h-6 text-gray-700"
                     viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4Z"
                          stroke-linejoin="round"/>
                    <path d="m9 12 2 2 4-4"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>

            </div>

            <div>
                <p class="text-sm text-gray-500">
                    Supplier reputation
                </p>

                <h2 class="text-xl font-semibold text-gray-900 mt-1">
                    {{ $level }} level
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Your reputation score helps communicate your activity
                    and standing on Acrovoy.
                </p>
            </div>

        </div>

        <div class="sm:text-right shrink-0">
            <p class="text-sm text-gray-500">
                Reputation score
            </p>

            <p class="text-3xl font-semibold tracking-tight text-gray-900 mt-1">
                {{ $score }}
            </p>

            <p class="text-xs text-gray-500 mt-1">
                Points
            </p>
        </div>

    </div>

    <div class="flex items-center justify-between gap-3 mb-2">
        <span class="text-sm font-medium text-gray-700">
            Progress to next level
        </span>

        <span class="text-xs text-gray-500">
            @if ($level === 'Platinum')
                Highest level reached
            @else
                {{ $pointsNeeded }} points needed
            @endif
        </span>
    </div>

    <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
        <div class="h-full bg-gray-900 rounded-full
                    transition-all duration-700"
             style="width: {{ $progress }}%;">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">

        <div class="rounded-xl bg-gray-50 p-4">
            <p class="text-xs text-gray-500 mb-2">
                Current level
            </p>

            <p class="text-sm font-semibold text-gray-900">
                {{ $level }}
            </p>
        </div>

        <div class="rounded-xl bg-gray-50 p-4">
            <p class="text-xs text-gray-500 mb-2">
                Buyer rating
            </p>

            <div class="flex items-center gap-2">
                <span class="text-base font-semibold text-gray-900">
                    {{ number_format($rating, 1) }}
                </span>

                <span class="text-sm text-gray-500">
                    / 5
                </span>
            </div>
        </div>

        <div class="rounded-xl bg-gray-50 p-4">
            <p class="text-xs text-gray-500 mb-2">
                Open disputes
            </p>

            <p class="text-sm font-semibold text-gray-900">
                {{ $openDisputes }}
            </p>
        </div>

    </div>

    <p class="text-xs text-gray-400 mt-4">
        Reputation and rating values are demonstration data until connected
        to verified platform statistics.
    </p>

</div>


{{-- ========================================================= --}}
{{-- CORE TOOLS --}}
{{-- ========================================================= --}}

<div class="mb-4">
    <h2 class="text-xl font-semibold text-gray-900">
        Manage your business
    </h2>

    <p class="text-sm text-gray-500 mt-1">
        The tools you need to move from a buyer request to a completed order.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

    {{-- RFQ MARKETPLACE --}}

    <a href="{{ route('supplier.rfqs.index') }}"
       class="group bg-white border border-gray-200 rounded-xl
              p-5 sm:p-6 shadow-sm hover:shadow-md
              hover:border-gray-300 transition">

        <div class="w-11 h-11 rounded-xl bg-[#F7F3EA]
                    flex items-center justify-center mb-5">

            <svg class="w-5 h-5 text-gray-800"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.6">
                <path d="M5 3h10l4 4v14H5V3Z"
                      stroke-linejoin="round"/>
                <path d="M14 3v5h5M8 12h8M8 16h6"
                      stroke-linecap="round"/>
            </svg>

        </div>

        <h3 class="font-semibold text-gray-900">
            Buyer Requests
        </h3>

        <p class="text-sm text-gray-500 leading-relaxed mt-2">
            Review RFQs, understand buyer requirements and submit
            competitive offers.
        </p>

        <div class="flex items-center justify-between mt-5 pt-4
                    border-t border-gray-100">

            <span class="text-sm font-medium text-gray-900">
                Explore RFQs
            </span>

            <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-0.5
                        transition-transform"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.7">
                <path d="M5 12h14M13 6l6 6-6 6"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>

        </div>

    </a>


    {{-- ORDERS --}}

    <a href="{{ route('supplier.orders') }}"
       class="group bg-white border border-gray-200 rounded-xl
              p-5 sm:p-6 shadow-sm hover:shadow-md
              hover:border-gray-300 transition">

        <div class="w-11 h-11 rounded-xl bg-[#F7F3EA]
                    flex items-center justify-center mb-5">

            <svg class="w-5 h-5 text-gray-800"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.6">
                <path d="M6 3h12l2 4H4l2-4Z"/>
                <path d="M4 7v13h16V7M9 11h6"/>
            </svg>

        </div>

        <h3 class="font-semibold text-gray-900">
            Orders & Fulfillment
        </h3>

        <p class="text-sm text-gray-500 leading-relaxed mt-2">
            Keep track of confirmed orders, production progress
            and delivery arrangements.
        </p>

        <div class="flex items-center justify-between mt-5 pt-4
                    border-t border-gray-100">

            <span class="text-sm font-medium text-gray-900">
                Manage orders
            </span>

            <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-0.5
                        transition-transform"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.7">
                <path d="M5 12h14M13 6l6 6-6 6"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>

        </div>

    </a>


    {{-- REPUTATION --}}

    <a href="#supplier-reputation"
       onclick="document.getElementById('supplier-reputation')?.scrollIntoView({behavior: 'smooth'}); return false;"
       class="group bg-white border border-gray-200 rounded-xl
              p-5 sm:p-6 shadow-sm hover:shadow-md
              hover:border-gray-300 transition">

        <div class="w-11 h-11 rounded-xl bg-[#F7F3EA]
                    flex items-center justify-center mb-5">

            <svg class="w-5 h-5 text-gray-800"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.6">
                <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4Z"
                      stroke-linejoin="round"/>
                <path d="m9 12 2 2 4-4"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>

        </div>

        <h3 class="font-semibold text-gray-900">
            Reputation & Trust
        </h3>

        <p class="text-sm text-gray-500 leading-relaxed mt-2">
            Monitor your supplier level and build buyer confidence
            through reliable business performance.
        </p>

        <div class="flex items-center justify-between mt-5 pt-4
                    border-t border-gray-100">

            <span class="text-sm font-medium text-gray-900">
                View reputation
            </span>

            <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-0.5
                        transition-transform"
                 viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.7">
                <path d="M5 12h14M13 6l6 6-6 6"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>

        </div>

    </a>

</div>


{{-- ========================================================= --}}
{{-- PROJECT-BASED OPPORTUNITIES --}}
{{-- ========================================================= --}}

<div id="supplier-projects"
     class="bg-white border border-gray-200 rounded-2xl
            p-5 sm:p-7 shadow-sm mb-8">

    <div class="flex flex-col lg:flex-row lg:items-start
                lg:justify-between gap-5 mb-7">

        <div class="max-w-2xl">

            <span class="text-xs font-semibold uppercase
                         tracking-wider text-gray-500">
                Beyond individual products
            </span>

            <h2 class="text-2xl font-semibold tracking-tight
                       text-gray-900 mt-2">
                Project-based manufacturing opportunities
            </h2>

            <p class="text-sm text-gray-500 leading-relaxed mt-3">
                Hospitality buyers may need furniture and décor for an entire
                hotel, restaurant or outdoor area. Structured project requests
                can help suppliers understand the wider scope and prepare
                coordinated proposals.
            </p>

        </div>

        <div class="shrink-0 rounded-xl bg-[#F7F3EA] px-4 py-3
                    border border-gray-200">

            <p class="text-xs text-gray-500">
                Demonstration overview
            </p>

            <p class="text-lg font-semibold text-gray-900 mt-1">
                {{ $activeProjects }} active projects
            </p>

            <p class="text-xs text-gray-500 mt-1">
                {{ $pendingProjects }} pending
            </p>

        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-gray-50
                        flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-gray-700" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="3" y="4" width="8" height="7" rx="1"/>
                    <rect x="13" y="4" width="8" height="7" rx="1"/>
                    <rect x="3" y="13" width="8" height="7" rx="1"/>
                    <rect x="13" y="13" width="8" height="7" rx="1"/>
                </svg>
            </div>

            <h3 class="text-sm font-semibold text-gray-900">
                Multiple products
            </h3>

            <p class="text-xs text-gray-500 leading-relaxed mt-2">
                Understand the full product scope of a hospitality project.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-gray-50
                        flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-gray-700" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.6">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v10M15 9.5c0-1.1-1.3-2-3-2s-3 .9-3 2
                             1.3 2 3 2 3 .9 3 2-1.3 2-3 2-3-.9-3-2"/>
                </svg>
            </div>

            <h3 class="text-sm font-semibold text-gray-900">
                Cost planning
            </h3>

            <p class="text-xs text-gray-500 leading-relaxed mt-2">
                Prepare clear pricing for quantities, materials and finishes.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-gray-50
                        flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-gray-700" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <path d="m21 15-5-5L5 21"/>
                </svg>
            </div>

            <h3 class="text-sm font-semibold text-gray-900">
                Materials & concepts
            </h3>

            <p class="text-xs text-gray-500 leading-relaxed mt-2">
                Present available finishes, customization options and solutions.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 p-4">
            <div class="w-9 h-9 rounded-lg bg-gray-50
                        flex items-center justify-center mb-3">
                <svg class="w-4 h-4 text-gray-700" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.6">
                    <path d="M12 3v18M3 12h18"/>
                    <circle cx="12" cy="12" r="9"/>
                </svg>
            </div>

            <h3 class="text-sm font-semibold text-gray-900">
                Long-term business
            </h3>

            <p class="text-xs text-gray-500 leading-relaxed mt-2">
                Build relationships that can lead to repeat orders.
            </p>
        </div>

    </div>

    <div class="flex flex-col sm:flex-row gap-3 mt-6">

        <a href="{{ route('supplier.rfqs.index') }}"
           class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5 bg-gray-900 text-white text-sm font-medium
                  rounded-lg hover:bg-gray-800 transition">
            Explore available requests

            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="1.7">
                <path d="M5 12h14M13 6l6 6-6 6"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
        </a>

        <a href="#supplier-workflow"
           class="inline-flex items-center justify-center px-4 py-2.5
                  border border-gray-300 text-gray-800 text-sm font-medium
                  rounded-lg hover:bg-gray-50 transition">
            Understand the workflow
        </a>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MANUFACTURING WORKFLOW --}}
{{-- ========================================================= --}}

<div id="supplier-workflow"
     class="bg-white border border-gray-200 rounded-2xl
            p-5 sm:p-7 shadow-sm mb-8">

    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">
            From buyer request to delivery
        </h2>

        <p class="text-sm text-gray-500 mt-2 max-w-2xl">
            A clear process helps both sides understand requirements,
            commercial terms and the next steps.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

        @php
            $workflowSteps = [
                [
                    'number' => '01',
                    'title' => 'Buyer request',
                    'description' => 'Review products, quantities, specifications and deadlines.',
                ],
                [
                    'number' => '02',
                    'title' => 'Your offer',
                    'description' => 'Provide pricing, lead time, materials and terms.',
                ],
                [
                    'number' => '03',
                    'title' => 'Discussion',
                    'description' => 'Clarify details and agree on the proposed solution.',
                ],
                [
                    'number' => '04',
                    'title' => 'Order & production',
                    'description' => 'Proceed according to the confirmed order terms.',
                ],
                [
                    'number' => '05',
                    'title' => 'Delivery',
                    'description' => 'Coordinate fulfillment and complete the order.',
                ],
            ];
        @endphp

        @foreach ($workflowSteps as $step)
            <div class="relative rounded-xl bg-gray-50 border border-gray-100 p-4">

                <span class="inline-flex items-center justify-center
                             w-8 h-8 rounded-lg bg-white border
                             border-gray-200 text-xs font-semibold text-gray-700">
                    {{ $step['number'] }}
                </span>

                <h3 class="text-sm font-semibold text-gray-900 mt-4">
                    {{ $step['title'] }}
                </h3>

                <p class="text-xs text-gray-500 leading-relaxed mt-2">
                    {{ $step['description'] }}
                </p>

            </div>
        @endforeach

    </div>

</div>


{{-- ========================================================= --}}
{{-- PREMIUM SELLER --}}
{{-- ========================================================= --}}

<div class="bg-[#F7F3EA] border border-gray-200 rounded-2xl
            p-5 sm:p-7 mb-8">

    <div class="flex flex-col lg:flex-row lg:items-center
                lg:justify-between gap-6">

        <div class="max-w-2xl">

            <div class="inline-flex items-center gap-2 text-xs
                        font-semibold uppercase tracking-wider text-gray-600 mb-3">

                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.6">
                    <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3
                             -5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"
                          stroke-linejoin="round"/>
                </svg>

                Supplier growth
            </div>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Grow your visibility on Acrovoy
            </h2>

            <p class="text-sm text-gray-600 leading-relaxed mt-3">
                Explore the available Premium Seller plans and review
                the features designed to support your business visibility
                and participation on the platform.
            </p>

        </div>

        <div class="flex flex-col sm:flex-row gap-3 shrink-0">

            <a href="{{ route('supplier.premium-plans') }}"
               class="inline-flex items-center justify-center gap-2
                      px-5 py-3 bg-gray-900 text-white text-sm
                      font-medium rounded-lg hover:bg-gray-800 transition">
                View Premium plans

                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.7">
                    <path d="M5 12h14M13 6l6 6-6 6"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </a>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- FOOTER QUICK LINKS --}}
{{-- ========================================================= --}}

<div class="flex flex-col sm:flex-row sm:items-center
            sm:justify-between gap-3 pb-2">

    <p class="text-xs text-gray-400">
        Acrovoy — Global Hospitality Furniture & Décor Marketplace
    </p>

    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">

        <a href="{{ route('supplier.rfqs.index') }}"
           class="text-gray-600 hover:text-gray-900 transition">
            Buyer requests
        </a>

        <a href="{{ route('supplier.orders') }}"
           class="text-gray-600 hover:text-gray-900 transition">
            Orders
        </a>

        <a href="{{ route('supplier.premium-plans') }}"
           class="text-gray-600 hover:text-gray-900 transition">
            Premium plans
        </a>

    </div>

</div>

@endsection
