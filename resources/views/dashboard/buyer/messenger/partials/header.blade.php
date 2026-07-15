{{-- HEADER --}}

<div
    class="px-6 py-4 flex items-center justify-between bg-white">


    {{-- COMPANY / CUSTOMER INFO --}}

    <div
        class="flex items-center gap-4 min-w-0">

        {{-- Avatar --}}

        <img
    id="conversation-header-avatar"
    src=""
    class="hidden w-11 h-11 rounded-xl object-cover border border-stone-200"
/>



        {{-- Info --}}

        <div class="min-w-0">

            <div class="flex items-center gap-2">
                <h3 id="conversation-header-title" class="hidden text-sm font-semibold text-stone-900 truncate">
                    
                </h3>


                {{-- Online --}}

                <span
                    id="conversation-header-online"
                    class="hidden
                        w-2
                        h-2
                        rounded-full
                        bg-green-500
                    "
                ></span>

            </div>



            <div
    id="conversation-header-subtitle"
    class="text-xs text-stone-500 mt-1"
></div>


        </div>


    </div>




    {{-- ACTIONS --}}

    <div
        class="
            flex
            items-center
            gap-2
        "
    >


        {{-- View Subject --}}

        <a id="conversation-header-link" href="#" 
        class="hidden inline-flex items-center justify-center h-9 px-3 rounded-lg 
        border border-stone-200 text-xs font-medium text-stone-600 hover:bg-stone-50 transition">
        View Product
    </a>




        {{-- More --}}

        <button
            type="button"
            class="hidden
                w-9
                h-9
                rounded-lg
                border
                border-stone-200
                flex
                items-center
                justify-center
                text-stone-500
                hover:bg-stone-50
                transition
            "
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


    </div>


</div>