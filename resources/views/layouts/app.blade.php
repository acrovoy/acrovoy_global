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


    {{-- Временная зона (для проверки скрипта) --}}
<div id="timezone-display" class="fixed bottom-2 left-2 text-gray-600 text-xs px-2 py-1 rounded z-50">
    <span id="timezone-value">detecting...</span>
    <span id="current-time">--:--:--</span>
    <p>Registered at: <span data-utc="{{ auth()->user()->created_at ?? '' }}"></span></p>
</div>


</div>

<script>
(function () {
    // ----------------------
    // 1. Определяем временную зону
    // ----------------------
    const tz = @json(auth()->user()->timezone ?? null) // timezone из базы для залогиненных
              || localStorage.getItem('timezone')      // из localStorage
              || Intl.DateTimeFormat().resolvedOptions().timeZone; // системная как fallback

    const tzStored = localStorage.getItem('timezone');

    // Сохраняем в localStorage, если изменилось
    if (tzStored !== tz) {
        localStorage.setItem('timezone', tz);
    }

    // ----------------------
    // 2. Показываем TZ на странице
    // ----------------------
    const tzElement = document.getElementById('timezone-value');
    if (tzElement) tzElement.textContent = tz;

    // ----------------------
    // 3. Функция для обновления текущего времени
    // ----------------------
    function updateTime() {
        const now = new Date();
        const options = { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: tz };
        const timeStr = now.toLocaleTimeString([], options);
        const currentTimeElement = document.getElementById('current-time');
        if (currentTimeElement) currentTimeElement.textContent = timeStr;
    }

    // ----------------------
    // 4. Функция для конвертации UTC → локальное время
    // ----------------------
    function convertUtcToLocal(utcString) {
        if (!utcString) return '--:--';
        const date = new Date(utcString + 'Z'); // Z = UTC
        return date.toLocaleString([], { 
            timeZone: tz,
            hour12: false,
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    // ----------------------
    // 5. Конвертируем все элементы с data-utc
    // ----------------------
    document.querySelectorAll('[data-utc]').forEach(el => {
        const utc = el.getAttribute('data-utc');
        el.textContent = convertUtcToLocal(utc);
    });

    // ----------------------
    // 6. Запускаем обновление текущего времени каждую секунду
    // ----------------------
    updateTime();
    setInterval(updateTime, 1000);

})();
</script>

</body>
</html>

