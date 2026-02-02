<nav x-data="{ open: false }" class="sticky top-0 bg-gradient-to-t from-[#F7F3EA] via-[#EADCC5]/80 to-orange-200 border-b border-gray-100 shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Left: Logo + Links -->
            <div class="flex items-center space-x-2">

                <!-- Logo -->
                <a href="{{ route('main') }}" class="flex items-center">
                    <x-application-logo class="block h-9 w-auto text-gray-800" />
                </a>

                <!-- Catalog button -->
                <x-catalog-menu />

            </div>

            <div class="flex justify-between h-16 items-center ml-4">

                <!-- Purchase Country Selector -->
<div class="relative hidden sm:block ml-4">
    <span class="text-xs text-gray-500 whitespace-nowrap">
        {{ __('layouts/navigation.select_purchase_country') }}
    </span>

    @php
        $countries = \App\Models\Country::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $currentCountry = strtolower(
            auth()->user()->purchase_country 
            ?? request()->cookie('purchase_country')
            ?? session('purchase_country', \App\Models\Country::where('is_default', 1)->value('code') ?? 'us')
        );

        $currentCountryName = optional(
            $countries->firstWhere('code', $currentCountry)
        )->name ?? strtoupper($currentCountry);

        // Приоритетные страны, например самые популярные для B2B
        
        $mainCountries = $countries->where('is_priority', 1);
        $otherCountries = $countries->where('is_priority', 0);
    @endphp

    <x-dropdown align="left" width="36">
        <x-slot name="trigger">
            <button class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                <span class="text-sm mr-1">
                    {{ $currentCountryName }}
                </span>
                <svg class="ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="bg-[#F7F3EA] min-w-[160px]">
                
                {{-- Приоритетные страны --}}
                @foreach($mainCountries as $country)
                    @if($country->code !== $currentCountry)
                        <x-dropdown-link href="{{ route('country.set', $country->code) }}">
                            <span class="font-medium">{{ strtoupper($country->code) }}</span>
                            <span class="text-xs text-gray-500">{{ $country->name }}</span>
                        </x-dropdown-link>
                    @endif
                @endforeach

                {{-- Разделитель --}}
                <div class="border-t my-1"></div>

                {{-- Остальные страны со скроллом --}}
                <div class="max-h-64 bg-[#E8E1D5] overflow-y-auto">
                    @foreach($otherCountries as $country)
                        @if($country->code !== $currentCountry)
                            <x-dropdown-link href="{{ route('country.set', $country->code) }}">
                                <span class="font-medium">{{ strtoupper($country->code) }}</span>
                                <span class="text-xs text-gray-500">{{ $country->name }}</span>
                            </x-dropdown-link>
                        @endif
                    @endforeach
                </div>
            </div>
        </x-slot>
    </x-dropdown>
</div>


                <!-- Currency Selector (Desktop) with Priority -->
<div class="relative ml-4 hidden sm:block">
    <span class="text-xs text-gray-500 whitespace-nowrap">
        {{ __('layouts/navigation.select_currency') }}
    </span>

    @php
        // Получаем все активные валюты
        $currencies = \App\Models\Currency::where('is_active', 1)
            ->orderBy('code')
            ->get();

        // Текущая валюта
        $currentCurrency = strtolower(
            auth()->user()->currency
            ?? session('currency', 'usd')
        );

        $currentCurrencyObj = $currencies->firstWhere('code', strtoupper($currentCurrency));
        $currentCurrencyCode = $currentCurrencyObj->code ?? strtoupper($currentCurrency);

        // Разделяем валюты по приоритету
        $mainCurrencies = $currencies->where('is_priority', 1);
        $otherCurrencies = $currencies->where('is_priority', 0);
    @endphp

    <x-dropdown align="left" width="36">
        <x-slot name="trigger">
            <button class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                <span class="text-sm mr-1">
                    {{ $currentCurrencyCode }}
                </span>
                <svg class="ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="bg-[#F7F3EA] min-w-[140px]">
                
                {{-- Приоритетные валюты --}}
                @foreach($mainCurrencies as $currency)
                    @if(strtolower($currency->code) !== $currentCurrency)
                        <x-dropdown-link
                            href="{{ route('currency.set', strtolower($currency->code)) }}"
                        >
                            <div class="flex flex-col leading-tight">
                                <span class="font-medium text-gray-900">
                                    {{ $currency->code }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $currency->name }}
                                </span>
                            </div>
                        </x-dropdown-link>
                    @endif
                @endforeach

                {{-- Разделитель --}}
                <div class="border-t my-1"></div>

                {{-- Остальные валюты --}}
                <div class="max-h-64 bg-[#E8E1D5] overflow-y-auto">
                    @foreach($otherCurrencies as $currency)
                        @if(strtolower($currency->code) !== $currentCurrency)
                            <x-dropdown-link
                                href="{{ route('currency.set', strtolower($currency->code)) }}"
                            >
                                <div class="flex flex-col leading-tight">
                                    <span class="font-medium text-gray-900">
                                        {{ $currency->code }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $currency->name }}
                                    </span>
                                </div>
                            </x-dropdown-link>
                        @endif
                    @endforeach
                </div>

            </div>
        </x-slot>
    </x-dropdown>
</div>



                <!-- Language / Locale Dropdown -->
                <div class="relative ml-4 mr-3 hidden sm:block">
                    <span class="text-xs text-gray-500 whitespace-nowrap">
                        {{ __('layouts/navigation.select_language') }}
                    </span>

                    @php
                    use App\Models\Language;

                    $languages = Language::where('is_active', true)
                        ->orderBy('sort_order')
                        ->get(); // <- Получаем коллекцию объектов
                    $currentLang = app()->getLocale();
                    @endphp

                    <x-dropdown align="right" width="36">
    <x-slot name="trigger">
        @php
            $currentLanguage = $languages->firstWhere('code', $currentLang);
        @endphp
        <button class="flex items-center text-gray-700 hover:text-gray-900 font-medium"
                dir="{{ $currentLanguage->direction ?? 'ltr' }}">
            <span class="text-sm mr-1">
                {{ $currentLanguage->native_name ?? $currentLanguage->name }}
            </span>
            <svg class="ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="bg-[#F7F3EA] min-w-[120px]">
            @foreach($languages as $language)
                @if($language->code !== $currentLang)
                    <x-dropdown-link href="{{ route('locale.switch', $language->code) }}"
                                     dir="{{ $language->direction ?? 'ltr' }}">
                        {{ $language->native_name ?? $language->name }}
                    </x-dropdown-link>
                @endif
            @endforeach
        </div>
    </x-slot>
</x-dropdown>

                    
                </div>




                 {{-- 🔔 Уведомление о спорах --}}
@php
use App\Models\OrderDispute;

$disputeCount = 0;
$disputeLink = null;

if (auth()->check()) {

    // 🧑 Покупатель
    if (auth()->user()->role === 'buyer') {

        $buyerOpenDisputes = OrderDispute::whereHas('order', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
            ->get();

        $disputeCount = $buyerOpenDisputes->count();

        if ($disputeCount > 0) {
            $disputeLink = route(
                'buyer.orders.show',
                $buyerOpenDisputes->first()->order_id
            );
        }
    }

    // 🏭 Продавец (manufacturer)
    if (
        auth()->user()->role === 'manufacturer' &&
        auth()->user()->supplier
    ) {

        $sellerOpenDisputes = OrderDispute::whereHas(
                'order.items.product',
                function ($q) {
                    $q->where(
                        'supplier_id',
                        auth()->user()->supplier->id
                    );
                }
            )
            ->whereIn('status', ['pending', 'supplier_offer', 'rejected', 'admin_review'])
            ->get();

        $disputeCount = $sellerOpenDisputes->count();

        if ($disputeCount > 0) {
            $disputeLink = route(
                'manufacturer.orders.show',
                $sellerOpenDisputes->first()->order_id
            );
        }
    }
}
@endphp

{{-- 🔔 Значок --}}
@if($disputeCount > 0 && $disputeLink)
    <a href="{{ $disputeLink }}"
       title="У вас есть {{ $disputeCount }} активных споров"
       class="mr-6 relative flex items-center text-red-600 hover:text-red-700">

        {{-- Иконка --}}
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-5 h-5"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                     a6 6 0 10-12 0v3.159
                     c0 .538-.214 1.055-.595 1.436L4 17h5m6 0
                     v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Счётчик --}}
        <span class="absolute -top-1 -right-1
                     w-4 h-4 flex items-center justify-center
                     text-xs font-bold text-white
                     bg-red-600 rounded-full">
            {{ $disputeCount }}
        </span>
    </a>
@endif


{{-- Rfq offers --}}

@php 
use App\Models\RfqOffer;

$offerCount = 0;
$offerLink = null;

if (auth()->check() && auth()->user()->role === 'buyer') {

    $newOffers = RfqOffer::whereHas('rfq', function ($q) {
            $q->where('buyer_id', auth()->id())
              ->where('status', 'active');
        })
        ->whereNull('buyer_viewed_at')
        ->where('status', 'pending')
        ->get();

    $offerCount = $newOffers->count();

    if ($offerCount > 0) {
        $offerLink = route(
            'buyer.rfqs.show',
            $newOffers->first()->rfq_id
        );
    }
}
@endphp

@if($offerCount > 0 && $offerLink)
    <a href="{{ $offerLink }}"
       title="You have {{ $offerCount }} new offers"
       class="mr-6 relative flex items-center text-indigo-600 hover:text-indigo-700">

        {{-- Bell icon --}}
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-5 h-5"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                     a6 6 0 10-12 0v3.159
                     c0 .538-.214 1.055-.595 1.436L4 17h5m6 0
                     v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <span class="absolute -top-1 -right-1
                     w-4 h-4 flex items-center justify-center
                     text-xs font-bold text-white
                     bg-indigo-600 rounded-full">
            {{ $offerCount }}
        </span>
    </a>
@endif

 <!-- Cart Dropdown -->@auth
    @if(auth()->user()->role === 'buyer')
        <a href="{{ route('buyer.cart.index') }}"
           class="relative mr-6 text-gray-700 hover:text-black">

            {{-- Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-6 w-6"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13L17 13M7 13H5.4" />
            </svg>

            {{-- Badge --}}
            @php
                $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
            @endphp

            @if($cartCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-600 text-white
                             text-xs rounded-full px-2 py-0.5">
                    {{ $cartCount }}
                </span>
            @endif
        </a>
    @endif
@endauth





                <!-- Right: User Dropdown -->
                <div class="hidden sm:flex sm:items-center">


               




                    @auth
                        <x-dropdown align="right" width="48">
                            <div class="bg-[#F7F3EA] shadow-lg rounded-lg p-2"></div>
                            <x-slot name="trigger">
                                <button class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="bg-[#F7F3EA]">
                                    <x-dropdown-link href="{{ 
    auth()->user()->role === 'manufacturer'
        ? route('manufacturer.home')
        : route('buyer.home')
}}">
    {{ __('layouts/navigation.dashboard') }}
</x-dropdown-link>

                                    @if(Auth::user()->role === 'admin')
                                        <x-dropdown-link href="{{ route('admin.home') }}">
                                            {{ __('layouts/navigation.admin_dashboard') }}
                                        </x-dropdown-link>
                                    @endif

                                    <x-dropdown-link href="{{ route('profile.edit') }}">{{ __('layouts/navigation.profile') }}</x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link href="{{ route('logout') }}"
                                                         onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('layouts/navigation.log_out') }}
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </x-slot>
                        </x-dropdown>
                    @endauth

                    @guest
                        @if (Route::has('login'))
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900">
                                    {{ __('layouts/navigation.log_in') }}
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="font-semibold text-gray-600 hover:text-gray-900">
                                        {{ __('layouts/navigation.register') }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endguest
                </div>

            </div>

            <!-- Hamburger -->
            <div class="sm:hidden">
                <button @click="open = ! open"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition class="sm:hidden">
        <!-- Country Selector -->
        <div class="pt-4 border-t border-gray-200 px-4">
            <span class="text-xs text-gray-500 block mb-1">{{ __('layouts/navigation.select_purchase_country') }}</span>

            @php
            $countries = \App\Models\Country::where('is_active', 1)
                ->orderBy('name')
                ->get();

            $currentCountry = strtolower(
                auth()->user()->purchase_country
                ?? session('purchase_country', 'us')
            );

            $currentCountryName = optional(
                $countries->firstWhere('code', $currentCountry)
            )->name ?? strtoupper($currentCountry);
        @endphp

            <x-dropdown align="left" width="36">

            <x-slot name="trigger">
                <button class="flex items-center w-full text-gray-700 hover:text-gray-900 font-medium justify-between">
                    {{ $currentCountryName }}
                    <svg class="ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="bg-[#F7F3EA] min-w-[160px]">
                    @foreach($countries as $country)
                        @if($country->code !== $currentCountry)
                            <x-dropdown-link href="{{ route('country.set', $country->code) }}">
                                <span class="font-medium">{{ strtoupper($country->code) }}</span>
                                <span class="text-xs text-gray-500">{{ $country->name }}</span>
                            </x-dropdown-link>
                        @endif
                    @endforeach
                </div>
            </x-slot>

        </x-dropdown>
        </div>

        <!-- Currency Selector (Mobile) -->
<div class="pt-4 border-t border-gray-200 px-4">
    <span class="text-xs text-gray-500 block mb-1">
        {{ __('layouts/navigation.select_currency') }}
    </span>

    <x-dropdown align="left" width="36">
        <x-slot name="trigger">
            <button
                class="flex items-center w-full text-gray-700 hover:text-gray-900 font-medium justify-between"
            >
                {{ strtoupper($currentCurrency) }}

                <svg class="ml-1 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="bg-[#F7F3EA] min-w-[120px]">
                @foreach($currencies as $currency)
                    @if(strtolower($currency->code) !== $currentCurrency)
                        <x-dropdown-link
                            href="{{ route('currency.set', strtolower($currency->code)) }}"
                        >
                            <div class="flex flex-col leading-tight">
                                <span class="font-medium text-gray-900">
                                    {{ $currency->code }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $currency->name }}
                                </span>
                            </div>
                        </x-dropdown-link>
                    @endif
                @endforeach
            </div>
        </x-slot>
    </x-dropdown>
</div>







        <!-- Language Selector -->
        <div class="pt-4 border-t border-gray-200 px-4">
            @php
                $languages = Language::where('is_active', true)
                        ->orderBy('sort_order')
                        ->get(); // <- Получаем коллекцию объектов
                    $currentLang = app()->getLocale();
                    @endphp
            <span class="text-xs text-gray-500 block mb-1">{{ __('layouts/navigation.select_language') }}</span>
            <x-dropdown>
    <x-slot name="trigger">
        <button class="flex items-center" dir="{{ $currentLanguage->direction ?? 'ltr' }}">
            {{ $currentLanguage->native_name ?? $currentLanguage->name }}
        </button>
    </x-slot>

    <x-slot name="content">
        @foreach($languages as $language)
            @if($language->code !== $currentLang)
                <x-dropdown-link
                    href="{{ route('locale.switch', $language->code) }}"
                    dir="{{ $language->direction ?? 'ltr' }}">
                    {{ $language->native_name ?? $language->name }}
                </x-dropdown-link>
            @endif
        @endforeach
    </x-slot>
</x-dropdown>


 

        </div>







        <!-- @auth
    @if(Auth::user()->role === 'buyer')
        <div class="pt-4 border-t border-gray-200 px-4">
            <a href="{{ route('buyer.cart.index') }}"
               class="flex items-center justify-between text-gray-700 hover:text-gray-900 font-medium">

                <div class="flex items-center gap-3">
                    {{-- Cart icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m12-9l2 9m-5-9v9" />
                    </svg>

                    <span>My Cart</span>
                </div>

                {{-- Cart count --}}
                @php
                    $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                @endphp

                @if($cartCount > 0)
                    <span class="bg-black text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
        </div>
    @endif
@endauth -->


        <!-- Auth / Guest -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                @if(Auth::user()->role === 'admin')    
                <a href="{{ route('admin.home') }}" class="block px-4 py-2 text-gray-700">{{ __('layouts/navigation.admin_dashboard') }}</a>
                @endif
                <a href="{{ route('manufacturer.home') }}" class="block px-4 py-2 text-gray-700">{{ __('layouts/navigation.dashboard') }}</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700">{{ __('layouts/navigation.profile') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-gray-700">{{ __('layouts/navigation.log_out') }}</a>
                </form>
            </div>
        </div>
        @endauth

        @guest
        <div class="px-4 py-4 border-t border-gray-200 space-y-2">
            <a href="{{ route('login') }}" class="block text-gray-700 font-medium">{{ __('layouts/navigation.log_in') }}</a>
            <a href="{{ route('register') }}" class="block text-blue-600 font-medium">{{ __('layouts/navigation.register') }}</a>
        </div>
        @endguest

    </div>
</nav>
