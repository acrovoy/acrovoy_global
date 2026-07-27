<div
    id="app-drawer-overlay"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 z-50">
</div>

<div
    id="app-drawer"
    class="fixed right-0 top-0 h-full w-[560px] bg-white shadow-2xl
           translate-x-full transition-transform duration-300
           z-[60] flex flex-col">

    {{-- Header --}}
    <div class="flex items-start justify-between border-b bg-gray-50 px-6 py-5">

        <div>

            <h2
                id="drawer-title"
                class="text-lg font-semibold text-gray-900">

                Edit

            </h2>

            <p
                id="drawer-description"
                class="mt-1 text-sm text-gray-500">

            </p>

        </div>

        <button
            type="button"
            onclick="closeDrawer()"
            class="text-gray-400 hover:text-gray-700 transition">

            <svg
                class="w-6 h-6"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12"/>

            </svg>

        </button>

    </div>

    {{-- Dynamic content --}}
    <div
        id="drawer-body"
        class="flex-1 overflow-y-auto">

        <div class="flex items-center justify-center h-full">

            <svg
                class="w-6 h-6 animate-spin text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24">

                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v8z">
                </path>

            </svg>

        </div>

    </div>

</div>