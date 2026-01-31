@extends('layouts.app')

@section('content')
<div class="bg-[#F7F3EA] py-8">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 px-4 items-start">

        {{-- LEFT MENU --}}
        <aside class="w-full lg:w-1/4 bg-white shadow-sm rounded-lg p-4 self-start">

            {{-- Role Indicator --}}
            <div class="flex justify-between mb-4 gap-2">
                <div class="flex w-full gap-2">
                    <button type="button"
                            class="flex-1 py-1 rounded-md font-semibold text-center border-2 border-brown-500 text-black bg-white cursor-default">
                        Admin
                    </button>
                </div>
            </div>

            {{-- ADMIN MENU --}}
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.products.index') }}" class="menu-link">
                        Products
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="menu-link {{ request()->routeIs('admin.users.*') ? 'font-bold' : '' }}">
                        Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.sellers.index') }}" class="menu-link">Suppliers</a>
                </li>
                <!-- <li>
                    <a href="" class="menu-link">
                        Message Center
                    </a>
                </li> -->

                 <li>
                    <a href="{{ route('admin.messages') }}" class="menu-link">
                        Message Center Test
                    </a>
                </li>


                <li>
                    <a href="{{ route('admin.orders.index') }}" class="menu-link">Orders & Disputes</a>
                </li>
                <li>
                    <a href="{{ route('admin.currencies.index') }}" class="menu-link">
                        Currencies
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.banners.index') }}" class="menu-link">Banners & Notes</a>
                </li>
                <li>
                    <a href="{{ route('admin.premium-plans.index') }}" class="menu-link {{ request()->routeIs('admin.premium-plans.*') ? 'font-bold' : '' }}">
                        Premium Plans
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.faq.index') }}" class="menu-link {{ request()->routeIs('admin.faq.*') ? 'font-bold' : '' }}">
                        FAQ
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.help.index') }}" class="menu-link {{ request()->routeIs('admin.help.*') ? 'font-bold' : '' }}">
                        Help Center
                    </a>
                </li>

                <li><a href="{{ route('admin.settings.index') }}" class="menu-link">Settings</a></li>
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
