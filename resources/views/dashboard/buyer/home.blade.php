@extends('dashboard.layout')

@section('dashboard-content')

{{-- HEADER --}}
<div class="mb-8">
    <h1 class="text-3xl font-semibold text-gray-900">
        Welcome back 👋
    </h1>
    <p class="text-gray-500 mt-2">
        Manage your orders, RFQs, projects and unlock better purchasing conditions.
    </p>
</div>

{{-- HERO BLOCK --}}
<div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl p-6 mb-8 flex flex-col lg:flex-row gap-6 items-center">

    {{-- LEFT TEXT --}}
    <div class="flex-1">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">
            Smarter procurement starts here
        </h2>

        <p class="text-gray-600 mb-4 leading-relaxed">
            Track orders, manage RFQs, communicate with suppliers and streamline your purchasing process in one place.
            Built for efficiency, transparency, and scale.
        </p>

        <div class="flex gap-3">
            <a href="{{ route('buyer.orders.index') }}"
               class="px-4 py-2 rounded-lg bg-black text-white text-sm hover:bg-gray-800 transition">
                View Orders
            </a>

            <a href="{{ route('buyer.rfqs.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-100 transition">
                My RFQs
            </a>
        </div>
    </div>

    {{-- RIGHT SVG --}}
    <div class="w-full lg:w-64 flex justify-center">
        <svg width="140" height="140" viewBox="0 0 24 24" fill="none" class="text-gray-400">
            <path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M6 7v10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </div>

</div>

{{-- FEATURES GRID --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

    {{-- Feature 1 --}}
    <div class="border border-gray-200 rounded-xl p-5 bg-white">
        <div class="mb-3">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-gray-500">
                <path d="M4 19V5h16v14H4z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M8 9h8M8 13h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <h3 class="font-semibold mb-1">Centralized Orders</h3>
        <p class="text-sm text-gray-500">
            Track all your purchases in one structured dashboard.
        </p>
    </div>

    {{-- Feature 2 --}}
    <div class="border border-gray-200 rounded-xl p-5 bg-white">
        <div class="mb-3">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-gray-500">
                <path d="M12 3v18" stroke="currentColor" stroke-width="1.5"/>
                <path d="M5 10l7-7 7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <h3 class="font-semibold mb-1">RFQ Management</h3>
        <p class="text-sm text-gray-500">
            Send requests and receive structured supplier offers.
        </p>
    </div>

    {{-- Feature 3 --}}
    <div class="border border-gray-200 rounded-xl p-5 bg-white">
        <div class="mb-3">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" class="text-gray-500">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                      stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </div>
        <h3 class="font-semibold mb-1">Secure Communication</h3>
        <p class="text-sm text-gray-500">
            Direct messaging with verified manufacturers.
        </p>
    </div>

</div>


{{-- CUSTOM FURNITURE BLOCK --}}
<div class="border border-gray-200 rounded-xl p-6 bg-white mb-8">

    <div class="flex flex-col lg:flex-row items-start gap-6">

        {{-- SVG --}}
        <div class="w-full lg:w-16 flex justify-center">
            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" class="text-gray-500">
                <path d="M3 7h18v10H3V7z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M7 7V5h10v2" stroke="currentColor" stroke-width="1.5"/>
                <path d="M7 13h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M7 16h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>

        {{-- CONTENT --}}
        <div class="flex-1">

            <h2 class="text-xl font-semibold text-gray-900 mb-2">
                Custom Furniture Manufacturing
            </h2>

            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Design and order custom furniture directly from verified manufacturers.
                Create production-ready projects, define specifications, materials, dimensions and requirements —
                and send them directly to factories for quotation and manufacturing.
            </p>

            {{-- KEY POINTS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600 mb-4">

                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                    Custom product design & specifications
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                    Direct factory communication
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                    RFQ-based production requests
                </div>

                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                    Transparent pricing from manufacturers
                </div>

            </div>

            {{-- CTA --}}
            <div class="flex gap-3">
                <a href="{{ route('buyer.projects.index') }}"
                   class="px-4 py-2 bg-black text-white text-sm rounded-lg hover:bg-gray-800 transition">
                    Create Project
                </a>

                <a href="{{ route('buyer.rfqs.index') }}"
                   class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-100 transition">
                    Send RFQ
                </a>
            </div>

        </div>

    </div>

</div>


{{-- PREMIUM BLOCK --}}
<div class="border border-gray-200 rounded-xl p-6 bg-gradient-to-r from-white to-gray-50">

    <div class="flex flex-col lg:flex-row justify-between gap-6 items-start lg:items-center">

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">
                Unlock Premium Buyer Experience
            </h2>

            <p class="text-gray-600 text-sm leading-relaxed max-w-xl">
                Get priority RFQ processing, better supplier visibility, advanced analytics and faster negotiation cycles.
                Upgrade your account to gain strategic advantage.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('buyer.premium-plans') }}"
               class="px-5 py-2.5 bg-black text-white rounded-lg text-sm hover:bg-gray-800 transition text-center">
                View Premium Plans
            </a>

            <a href="#"
               class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition text-center">
                Compare Benefits
            </a>
        </div>

    </div>

</div>

@endsection