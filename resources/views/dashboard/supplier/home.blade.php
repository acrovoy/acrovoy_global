@extends('dashboard.layout')

@section('dashboard-content')

@php
    $company = ActiveContext::company();
@endphp

{{-- HEADER --}}
<div class="mb-8">
    <h1 class="text-3xl font-semibold text-gray-900">
        Welcome Brilliant Seller | {{ $company?->name }} | {{ ucfirst(ActiveContext::role()) }}  </h1>
    <p class="text-gray-500 mt-2">
        Manage RFQs, orders, products and grow your manufacturing business globally.
    </p>
</div>


@php
    $supplierId = ActiveContext::id();

$supplier = \App\Models\Supplier::find($supplierId);

    $score = $supplier->reputation ?? 0;

    // Уровни репутации
    if ($score <= 50) {
        $level = 'Bronze';
        $color = 'bg-yellow-600';
        $textColor = 'text-yellow-700';
        $icon = '';
        $nextLevelScore = 51;
    } elseif ($score <= 120) {
        $level = 'Silver';
        $color = 'bg-gray-400';
        $textColor = 'text-gray-600';
        $icon = '';
        $nextLevelScore = 121;
    } elseif ($score <= 200) {
        $level = 'Gold';
        $color = 'bg-yellow-500';
        $textColor = 'text-yellow-600';
        $icon = '';
        $nextLevelScore = 201;
    } else {
        $level = 'Platinum';
        $color = 'bg-blue-500';
        $textColor = 'text-blue-600';
        $icon = '';
        $nextLevelScore = $score; // максимум
    }

    $progress = ($score / $nextLevelScore) * 100;
    if ($progress > 100) $progress = 100;

    $rating = $supplier->reviews()->avg('rating') ?? 0;
    $rating = round($rating, 1);
@endphp

<div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-5">

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-gray-600">
                    <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4z"
                          stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>

            <div>
                <div class="text-sm text-gray-500">Supplier Reputation</div>
                <div class="font-semibold text-gray-900">
                    {{ $level }} Level
                </div>
            </div>
        </div>

        {{-- SCORE --}}
        <div class="text-right">
            <div class="text-sm text-gray-500">Score</div>
            <div class="text-lg font-semibold text-gray-900">
                {{ $score }}
            </div>
        </div>

    </div>

    {{-- PROGRESS BAR --}}
    <div class="relative w-full h-2 bg-gray-100 rounded-full overflow-hidden mb-3">
        <div class="h-full bg-gray-900 rounded-full transition-all duration-700 ease-out"
     style="width: {{ $progress }}%;">
</div>
    </div>

    <div class="flex justify-between text-xs text-gray-500 mb-5">
        <span>Progress to next level</span>
        <span>{{ max($nextLevelScore - $score, 0) }} points needed</span>
    </div>

    {{-- RATING --}}
    <div class="flex items-center justify-between mb-5">

        <div class="flex items-center gap-1">
            @for ($i = 1; $i <= 5; $i++)
                <svg class="w-4 h-4 {{ $i <= round($rating) ? 'text-gray-900' : 'text-gray-300' }}"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 15l-5.878 3.09L5.36 11.545 1 7.91l6.061-.545L10 2l2.939 5.365L19 7.91l-4.36 3.635 1.238 6.545z"/>
                </svg>
            @endfor

            <span class="ml-2 text-sm text-gray-600">
                {{ $rating }}
            </span>
        </div>

        <div class="text-xs text-gray-500">
            Based on verified reviews
        </div>

    </div>

    {{-- RECENT LOGS --}}
    <div class="border-t pt-4">

        <div class="text-sm font-semibold text-gray-900 mb-3">
            Recent Activity
        </div>

        <ul class="space-y-2 max-h-40 overflow-y-auto pr-1">

            @foreach($supplier->reputationLogs()->latest()->take(5)->get() as $log)
                <li class="flex items-center justify-between text-sm">

                    <span class="text-gray-600">
                        {{ $log->reason }}
                    </span>

                    <span class="font-medium {{ $log->score_change > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $log->score_change > 0 ? '+' : '' }}{{ $log->score_change }}
                    </span>

                </li>
            @endforeach

        </ul>

    </div>

</div>





{{-- ========================= --}}
{{-- SUPPLIER KPI SUMMARY 2x3 --}}
{{-- ========================= --}}

@php
    // MOCK DATA (позже подключим реальные)
    $totalOrders = 24;
    $newOrders = 3;
    $processingOrders = 7;
    $completedOrders = 14;

    $activeRfqs = 5;
    $unansweredRfqs = 2;

    // NEW PROJECT KPI
    $activeProjects = 6;
    $pendingProjects = 2;

    $unreadMessages = 4;

    $rating = 4.6;
    $openDisputes = 1;
@endphp


<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">


    {{-- ========================= --}}
    {{-- BLOCK 1: ORDERS --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-lg px-5 py-3 text-sm">

        <div class="flex flex-wrap items-center gap-2 text-gray-600 font-medium">

            <span>Orders:</span>

            <span class="text-gray-900">
                Total {{ $totalOrders }}
            </span>

            <span class="text-gray-300">•</span>

            <span class="text-blue-600">
                New {{ $newOrders }}
            </span>

            <span class="text-gray-300">•</span>

            <span class="text-yellow-600">
                In Production {{ $processingOrders }}
            </span>

            <span class="text-gray-300">•</span>

            <span class="text-green-600">
                Completed {{ $completedOrders }}
            </span>

        </div>

    </div>



    {{-- ========================= --}}
    {{-- BLOCK 2: RFQs --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-lg px-5 py-3 text-sm">

        <div class="flex flex-wrap items-center gap-2 text-gray-600 font-medium">

            <span>RFQs:</span>

            <span class="text-gray-900">
                Active {{ $activeRfqs }}
            </span>

            <span class="text-gray-300">•</span>

            <span class="text-red-500">
                Unanswered {{ $unansweredRfqs }}
            </span>

        </div>

    </div>



    {{-- ========================= --}}
    {{-- BLOCK 3: PROJECTS --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-lg px-5 py-3 text-sm">

        <div class="flex flex-wrap items-center gap-2 text-gray-600 font-medium">

            <span>Projects:</span>

            <span class="text-gray-900">
                Active {{ $activeProjects }}
            </span>

            <span class="text-gray-300">•</span>

            <span class="text-orange-600">
                Pending {{ $pendingProjects }}
            </span>

        </div>

    </div>



    {{-- ========================= --}}
    {{-- BLOCK 4: MESSAGES --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-lg px-5 py-3 text-sm">

        <div class="flex flex-wrap items-center gap-2 text-gray-600 font-medium">

            <span>Messages:</span>

            <span class="text-indigo-600">
                Unread {{ $unreadMessages }}
            </span>

        </div>

    </div>



    {{-- ========================= --}}
    {{-- BLOCK 5: TRUST --}}
    {{-- ========================= --}}
    <div class="bg-white border border-gray-200 rounded-lg px-5 py-3 text-sm">

        <div class="flex flex-wrap items-center gap-2 text-gray-600 font-medium">

            <span>Trust:</span>

            <span class="text-gray-900 flex items-center gap-1">

                <span class="text-yellow-500">★</span>

                {{ $rating }} / 5

            </span>

            <span class="text-gray-300">•</span>

            <span class="text-red-600">

                Open Disputes {{ $openDisputes }}

            </span>

        </div>

    </div>


</div>




{{-- HERO BLOCK --}}
<div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6 mb-8 flex flex-col lg:flex-row gap-6 items-center">

    {{-- TEXT --}}
    <div class="flex-1">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">
            Turn RFQs into production contracts
        </h2>

        <p class="text-gray-600 text-sm leading-relaxed mb-4">
            Receive structured requests for quotation (RFQs), respond with competitive offers,
            and convert leads into long-term manufacturing contracts with verified buyers worldwide.
        </p>

        <div class="flex gap-3">
            <a href="{{ route('supplier.rfqs.index') }}"
               class="px-4 py-2 bg-black text-white text-sm rounded-lg hover:bg-gray-800 transition">
                View RFQs
            </a>

            <a href="{{ route('manufacturer.orders') }}"
               class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-100 transition">
                Manage Orders
            </a>
        </div>
    </div>

    {{-- SVG --}}
    <div class="w-full lg:w-64 flex justify-center">
        <svg width="140" height="140" viewBox="0 0 24 24" fill="none" class="text-gray-400">
            <path d="M4 20h16V8l-8-5-8 5v12z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.5"/>
        </svg>
    </div>

</div>

{{-- CORE FEATURES --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

    {{-- RFQ --}}
    <div class="border border-gray-200 rounded-xl p-5 bg-white">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-gray-500 mb-3">
            <path d="M4 4h16v16H4V4z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <h3 class="font-semibold mb-1">RFQ Marketplace</h3>
        <p class="text-sm text-gray-500">
            Access real buyer requests and submit competitive offers.
        </p>
    </div>

    {{-- ORDERS --}}
    <div class="border border-gray-200 rounded-xl p-5 bg-white">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-gray-500 mb-3">
            <path d="M6 6h12v14H6V6z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M9 10h6M9 14h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <h3 class="font-semibold mb-1">Order Management</h3>
        <p class="text-sm text-gray-500">
            Track production, shipping and fulfillment in one place.
        </p>
    </div>

    {{-- TRUST --}}
    <div class="border border-gray-200 rounded-xl p-5 bg-white">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-gray-500 mb-3">
            <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6l7-4z"
                  stroke="currentColor" stroke-width="1.5"/>
        </svg>
        <h3 class="font-semibold mb-1">Verified Buyers</h3>
        <p class="text-sm text-gray-500">
            Work only with verified and qualified buyers.
        </p>
    </div>

</div>


{{-- PROJECT REQUESTS (KEY ADVANTAGE BLOCK) --}}
<div class="border border-gray-200 rounded-xl p-6 bg-white mb-8">

    <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Project-Based Manufacturing Requests
    </h2>

    <p class="text-gray-600 text-sm leading-relaxed mb-6 max-w-3xl">
        Receive structured multi-product project requests from buyers. Estimate production costs,
        propose materials, prepare visual concepts and convert sourcing projects into long-term contracts.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">

        {{-- MULTI PRODUCT --}}
        <div class="p-3 border rounded-lg">
            <div class="font-semibold">
                Multi-Product Projects
            </div>

            <p class="text-gray-500 text-xs mt-1">
                Buyers submit grouped product sourcing requests
            </p>
        </div>


        {{-- COST ESTIMATION --}}
        <div class="p-3 border rounded-lg">
            <div class="font-semibold">
                Cost Planning
            </div>

            <p class="text-gray-500 text-xs mt-1">
                Prepare structured quotations across full project scope
            </p>
        </div>


        {{-- VISUALIZATION --}}
        <div class="p-3 border rounded-lg">
            <div class="font-semibold">
                Concept Visualization
            </div>

            <p class="text-gray-500 text-xs mt-1">
                Present materials, finishes and production solutions
            </p>
        </div>


        {{-- LONG TERM CONTRACTS --}}
        <div class="p-3 border rounded-lg">
            <div class="font-semibold">
                Contract Conversion
            </div>

            <p class="text-gray-500 text-xs mt-1">
                Turn sourcing projects into repeat manufacturing orders
            </p>
        </div>

    </div>

    {{-- CTA --}}
    <div class="mt-6 flex gap-3">

        <a href=""
           class="px-4 py-2 bg-black text-white text-sm rounded-lg hover:bg-gray-800 transition">
            View Projects
        </a>

        <a href="#"
           class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-100 transition">
            Learn Workflow
        </a>

    </div>

</div>


{{-- PRODUCTION FLOW (VERY IMPORTANT BLOCK) --}}
<div class="border border-gray-200 rounded-xl p-6 bg-white mb-8">

    <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Manufacturing Workflow
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm">

        <div class="p-3 border rounded-lg">
            <div class="font-semibold">1. RFQ Received</div>
            <p class="text-gray-500 text-xs mt-1">Buyer submits request</p>
        </div>

        <div class="p-3 border rounded-lg">
            <div class="font-semibold">2. Offer Sent</div>
            <p class="text-gray-500 text-xs mt-1">You submit pricing</p>
        </div>

        <div class="p-3 border rounded-lg">
            <div class="font-semibold">3. Negotiation</div>
            <p class="text-gray-500 text-xs mt-1">Buyer confirms terms</p>
        </div>

        <div class="p-3 border rounded-lg">
            <div class="font-semibold">4. Production</div>
            <p class="text-gray-500 text-xs mt-1">Manufacturing starts</p>
        </div>

        <div class="p-3 border rounded-lg">
            <div class="font-semibold">5. Delivery</div>
            <p class="text-gray-500 text-xs mt-1">Order completed</p>
        </div>

    </div>

</div>

{{-- PREMIUM BLOCK --}}
<div class="border border-gray-200 rounded-xl p-6 bg-gradient-to-r from-white to-gray-50">

    <div class="flex flex-col lg:flex-row justify-between gap-6 items-start lg:items-center">

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">
                Upgrade to Premium Seller
            </h2>

            <p class="text-gray-600 text-sm leading-relaxed max-w-xl">
                Increase visibility in RFQ marketplace, get priority ranking,
                access high-value buyers and boost conversion rate.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturer.premium-plans') }}"
               class="px-5 py-2.5 bg-black text-white rounded-lg text-sm hover:bg-gray-800 transition text-center">
                View Plans
            </a>

            <a href="#"
               class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition text-center">
                Learn Benefits
            </a>
        </div>

    </div>

</div>

@endsection