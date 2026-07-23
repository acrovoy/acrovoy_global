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
<ul class="space-y-5">

    {{-- DASHBOARD --}}
    <li>

        <div class="px-3 text-xs uppercase tracking-wider text-gray-500">
            Dashboard
        </div>

        <ul class="mt-2 ml-4 pl-4 border-l border-stone-200 space-y-1">

            <li>
                <a href="{{ route('admin.home') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.home') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Overview
                </a>
            </li>

        </ul>

    </li>


    {{-- CATALOG --}}
    <li>

        <div class="px-3 text-xs uppercase tracking-wider text-gray-500">
            Catalog
        </div>

        <ul class="mt-2 ml-4 pl-4 border-l border-stone-200 space-y-1">

            <li>
                <a href="{{ route('admin.products.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.products.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Products
                </a>
            </li>

        </ul>

    </li>


    {{-- USERS --}}
    <li>

        <div class="px-3 text-xs uppercase tracking-wider text-gray-500">
            Users
        </div>

        <ul class="mt-2 ml-4 pl-4 border-l border-stone-200 space-y-1">

            <li>
                <a href="{{ route('admin.users.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.users.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Users
                </a>
            </li>

            <li>
                <a href="{{ route('admin.sellers.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.sellers.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Suppliers
                </a>
            </li>

        </ul>

    </li>


    {{-- MESSAGE CENTER --}}
    <li>

        <div class="px-3 text-xs uppercase tracking-wider text-gray-500">
            Message Center
        </div>

        <ul class="mt-2 ml-4 pl-4 border-l border-stone-200 space-y-1">

            <li>
                <a href="{{ route('admin.messenger.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.messenger.index') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Support Requests
                </a>
            </li>

            <li>
                <a href=""
                   class="block py-1.5 text-sm text-stone-600 hover:text-stone-900 hover:font-medium">
                    All Messages
                </a>
            </li>

            <li>
                <a href=""
                   class="block py-1.5 text-sm text-stone-600 hover:text-stone-900 hover:font-medium">
                    Notice Manager
                </a>
            </li>

            
        </ul>

    </li>


    {{-- ORDERS --}}
    <li>

        <div class="px-3 text-xs uppercase tracking-wider text-gray-500">
            Commerce
        </div>

        <ul class="mt-2 ml-4 pl-4 border-l border-stone-200 space-y-1">

            <li>
                <a href="{{ route('admin.orders.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.orders.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Orders & Disputes
                </a>
            </li>

            <li>
                <a href="{{ route('admin.shipping-center.main') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.shipping-center.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Shipping Center
                </a>
            </li>

        </ul>

    </li>


    {{-- SYSTEM --}}
    <li>

        <div class="px-3 text-xs uppercase tracking-wider text-gray-500">
            System
        </div>

        <ul class="mt-2 ml-4 pl-4 border-l border-stone-200 space-y-1">

            <li>
                <a href="{{ route('admin.currencies.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.currencies.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Currencies
                </a>
            </li>

            <li>
                <a href="{{ route('admin.banners.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.banners.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Banners & Notes
                </a>
            </li>

            <li>
                <a href="{{ route('admin.premium-plans.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.premium-plans.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Premium Plans
                </a>
            </li>

            <li>
                <a href="{{ route('admin.faq.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.faq.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    FAQ
                </a>
            </li>

            <li>
                <a href="{{ route('admin.help.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.help.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Help Center
                </a>
            </li>

            <li>
                <a href="{{ route('admin.settings.index') }}"
                   class="block py-1.5 text-sm {{ request()->routeIs('admin.settings.*') ? 'font-semibold text-stone-900' : 'text-stone-600 hover:text-stone-900 hover:font-medium' }}">
                    Settings
                </a>
            </li>

        </ul>

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
