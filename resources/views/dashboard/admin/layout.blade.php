@extends('layouts.app')

@section('content')
<div class="bg-[#F7F3EA] py-8">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 px-4 items-start">

        {{-- LEFT MENU --}}
        <aside class="w-full lg:w-1/4 bg-white border border-gray-200 rounded-xl shadow-sm p-4 self-start">

    {{-- HEADER --}}
    <div class="mb-5">
        <div class="text-xs text-gray-500 uppercase tracking-wider">Control Panel</div>
        <div class="text-lg font-semibold text-gray-900">Admin Dashboard</div>
    </div>

    {{-- ROLE --}}
    <div class="mb-5">
        <div class="px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold text-center">
            Administrator
        </div>
    </div>

    {{-- MENU --}}
    <ul class="space-y-1">

    <li>
    <a href="{{ route('admin.home') }}"
       class="menu-link {{ request()->routeIs('admin.home') ? 'bg-gray-100 font-semibold' : '' }}">
        Overview
    </a>
</li>

        <li>
            <a href="{{ route('admin.products.index') }}"
               class="menu-link {{ request()->routeIs('admin.products.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Products
            </a>
        </li>

        <li>
            <a href="{{ route('admin.users.index') }}"
               class="menu-link {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Users
            </a>
        </li>

        <li>
            <a href="{{ route('admin.sellers.index') }}"
               class="menu-link {{ request()->routeIs('admin.sellers.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Suppliers
            </a>
        </li>

        <li>
            <a href="{{ route('admin.messages') }}"
               class="menu-link {{ request()->routeIs('admin.messages*') ? 'bg-gray-100 font-semibold' : '' }}">
                Message Center Test
            </a>
        </li>

        <li>
            <a href="{{ route('admin.orders.index') }}"
               class="menu-link {{ request()->routeIs('admin.orders.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Orders & Disputes
            </a>
        </li>

        <li>
            <a href="{{ route('admin.shipping-center.main') }}"
               class="menu-link {{ request()->routeIs('admin.shipping-center.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Shipping Center
            </a>
        </li>

        <li>
            <a href="{{ route('admin.currencies.index') }}"
               class="menu-link {{ request()->routeIs('admin.currencies.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Currencies
            </a>
        </li>

        <li>
            <a href="{{ route('admin.banners.index') }}"
               class="menu-link {{ request()->routeIs('admin.banners.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Banners & Notes
            </a>
        </li>

        <li>
            <a href="{{ route('admin.premium-plans.index') }}"
               class="menu-link {{ request()->routeIs('admin.premium-plans.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Premium Plans
            </a>
        </li>

        <li>
            <a href="{{ route('admin.faq.index') }}"
               class="menu-link {{ request()->routeIs('admin.faq.*') ? 'bg-gray-100 font-semibold' : '' }}">
                FAQ
            </a>
        </li>

        <li>
            <a href="{{ route('admin.help.index') }}"
               class="menu-link {{ request()->routeIs('admin.help.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Help Center
            </a>
        </li>

        <li>
            <a href="{{ route('admin.settings.index') }}"
               class="menu-link {{ request()->routeIs('admin.settings.*') ? 'bg-gray-100 font-semibold' : '' }}">
                Settings
            </a>
        </li>

    </ul>

</aside>

        {{-- RIGHT CONTENT --}}
        <main class="w-full lg:w-3/4 bg-white shadow-sm rounded-lg p-6 min-h-[400px]">
            @yield('dashboard-content')
        </main>

    </div>
</div>

<style>
.menu-link {
    display: block;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-family: 'Figtree', sans-serif;
    line-height: 1.2;
    font-size: 0.95rem;
    transition: background 0.2s;
}
.menu-link:hover {
    background: #e0e7ff;
}
</style>
@endsection
