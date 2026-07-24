{{-- HEADER --}}

<div class="px-5 py-5 border-b border-stone-200">

    <div class="flex items-center justify-between">

        <div>
            <h2 class="text-lg font-semibold text-stone-900">
                Message center
            </h2>

            <p class="text-sm text-stone-500 mt-1">
                Customer conversations
            </p>
        </div>


       

    </div>



     {{-- SEARCH --}}

    <div class="mt-4">

        <div
            class="
                flex
                items-center
                gap-2
                bg-stone-50
                border
                border-stone-200
                rounded-lg
                px-3
            "
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-stone-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                />
            </svg>


            <input
                id="conversation-search"
                type="text"
                placeholder="Search conversations..."
                class="
                    w-full
                    bg-transparent
                    border-none
                    outline-none
                    text-sm
                    py-2
                    text-stone-700
                "
            >

        </div>

    </div>

</div> 



{{-- CONVERSATION LIST --}}

<div
    id="conversation-list"
    data-url="/dashboard/buyer/messenger/conversations"
    class="
        flex-1
        overflow-y-auto mt-1
    "
>
</div>