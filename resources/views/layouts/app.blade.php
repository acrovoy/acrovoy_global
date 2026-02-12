<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SLaravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">


    <!-- Top Info Bar -->
<div class="bg-[#3B2F2F] text-white font-sans text-sm h-8 flex items-center border-b border-gray-700 overflow-hidden relative px-6">

    <!-- Скроллирующийся контейнер для мобилок -->
    <div class="marquee-wrapper flex items-center w-full">
        @auth
            <span class="marquee-item mr-6">{{ __('layouts/app.welcome_to_acrovoy') }}</span>
        @else
            <span class="marquee-item mr-6">{{ __('layouts/app.sign_up_today') }}</span>
            <a href="{{ route('register') }}" class="marquee-item mr-6 px-2 py-0.5 border border-white rounded text-white text-xs hover:bg-white hover:text-[#3B2F2F]">
                {{ __('layouts/app.join_now') }}
            </a>
        @endauth
        <a href="{{ route('register.manufacturer') }}" class="marquee-item underline">
            <span class="ml-0 mr-8">|</span>{{ __('layouts/app.for_seller') }}</a>

        <a href="{{ route('help.index') }}" class="marquee-item">
            <span class="ml-8 mr-8">|</span>{{ __('layouts/app.help_center') }}
        </a>



    </div>

</div>

<style>
/* Мобильные и планшеты: бегущая строка */
@media (max-width: 1024px) {
    .marquee-wrapper {
        display: inline-flex;
        animation: scroll-left 15s linear infinite;
    }

    .marquee-wrapper:hover {
        animation-play-state: paused;
    }

    .marquee-item {
        white-space: nowrap;
    }

    @keyframes scroll-left {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
}

/* Десктоп: центрируем статично */
@media (min-width: 1025px) {
    .marquee-wrapper {
        display: flex;
        justify-content: center;
        gap: 0.75rem; /* Отступы между элементами */
        animation: none;
    }
}
</style>








    {{-- Навигация --}}
    @include('layouts.navigation')

    {{-- Заголовок страницы (опционально) --}}
    @hasSection('header')
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </header>
    @endif

    {{-- Контент страницы --}}
    <main>
        @yield('content')
    </main>

    @include('layouts.footer')
</div>



</body>
</html>
