<div x-data="{ open: false }" class="relative inline-block ml-1">

    <!-- Кнопка вопросика -->
    <button
        type="button"
        @click="open = !open"
        @click.outside="open = false"
        class="text-gray-400 hover:text-blue-500 transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-5 h-5"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="1.8">
            <circle cx="12" cy="12" r="9"></circle>
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M10.7 9.2a1.8 1.8 0 113.6 0c0 1.2-1.6 1.4-1.6 2.6"/>
            <circle cx="12" cy="15.6" r="0.6" fill="currentColor"></circle>
        </svg>
    </button>

    <!-- Tooltip -->
    <div
        x-show="open"
        x-transition
        class="absolute z-50 p-3 text-sm text-white bg-gray-800 rounded-lg shadow-lg top-6 left-0 {{ $width }}"
    >
        {{ $slot }}
    </div>

</div>