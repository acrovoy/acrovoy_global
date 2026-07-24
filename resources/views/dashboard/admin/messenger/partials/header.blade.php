{{-- HEADER --}}

<div class="px-6 py-4 bg-white border-b border-stone-200">

    {{-- TOP --}}
    <div class="flex items-center gap-4">

        {{-- Avatar --}}
        <img
            id="conversation-header-avatar"
            src=""
            class="hidden w-11 h-11 rounded-xl object-cover border border-stone-200 shrink-0"
        />

        {{-- Info --}}
        <div class="flex-1 min-w-0">

            <div class="flex items-center gap-2">

                <h3
                    id="conversation-header-title"
                    class="hidden text-lg font-semibold text-stone-900 truncate"
                ></h3>

                <span
                    id="conversation-header-online"
                    class="hidden w-2 h-2 rounded-full bg-green-500 shrink-0"
                ></span>

            </div>

            <div
                id="conversation-header-subtitle"
                class="text-sm text-stone-500 mt-1 truncate"
            ></div>

        </div>

    </div>

    {{-- ACTIONS --}}
    <div
        class="flex flex-wrap items-center gap-2 mt-4"
    >

        {{-- View Subject --}}
        <a
            id="conversation-header-link"
            href="#"
            class="hidden inline-flex items-center justify-center h-9 px-3 rounded-lg border border-stone-200 text-xs font-medium text-stone-600 hover:bg-stone-50 transition"
        >
            View Product
        </a>

        {{-- More --}}
        <button
            type="button"
            class="hidden w-9 h-9 rounded-lg border border-stone-200 flex items-center justify-center text-stone-500 hover:bg-stone-50 transition"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v.01M12 12v.01M12 18v.01"
                />
            </svg>
        </button>

        <button
            id="conversation-toggle-status"
            type="button"
            class="hidden inline-flex items-center gap-2 px-3 py-2 rounded-lg border text-xs font-medium transition"
        ></button>

        <button
            id="conversation-delete"
            type="button"
            class="hidden inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-red-300 bg-red-600 text-white text-xs font-medium hover:bg-red-700 transition"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7H5M10 11v6m4-6v6M9 7V4h6v3m-8 0h10"
                />
            </svg>

            Delete conversation
        </button>

        <button
            id="conversation-create-notice"
            type="button"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-blue-300 bg-blue-600 text-white text-xs font-medium hover:bg-blue-700 transition"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Create Notice
        </button>

    </div>

</div>