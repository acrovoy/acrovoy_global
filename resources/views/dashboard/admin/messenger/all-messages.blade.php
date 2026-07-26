@extends('dashboard.admin.layout')

@section('dashboard-content')

{{-- ADMIN MESSAGE CENTER ACTIONS --}}

<div
    id="message-center-tools"
    data-statistics-url="{{ route('admin.messenger.statistics') }}">


    <div
        class="
            bg-white
            border
            border-stone-200
            rounded-xl
            shadow-sm
            overflow-hidden
            mb-4
        ">


        {{-- HEADER --}}

        <button
            type="button"
            id="message-center-tools-toggle"
            class="
                w-full
                px-5
                py-3
                flex
                items-center
                justify-between
                text-left
                hover:bg-stone-50
                transition
            ">

            <div>

                <div class="text-sm font-semibold text-stone-900">
                    Message Center Tools
                </div>

                <div class="text-xs text-stone-500 mt-1">
                    Administrative conversation management
                </div>

            </div>


            {{-- Arrow --}}

            <span
                id="message-center-tools-arrow"
                class="
                    w-8
                    h-8
                    rounded-lg
                    border
                    border-stone-200
                    flex
                    items-center
                    justify-center
                    text-stone-500
                    transition
                ">

                <svg
                    id="tools-arrow-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 transition-transform"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>

            </span>


        </button>



        {{-- CONTENT --}}

        <div
            id="message-center-tools-content"
            class="
                hidden
                border-t
                border-stone-200
                px-5
                py-4

            ">


            <div class="flex items-center gap-3">

                {{-- Conversations --}}
                <div
                    class="
            inline-flex
            items-center
            gap-2
            h-9
            px-3
            rounded-lg
            border
            border-stone-200
            bg-stone-50
            text-xs
            text-stone-600
        ">
                    <span>Conversations with msgs:</span>

                    <span
                        id="conversations-count"
                        class="font-semibold text-stone-900">
                        0
                    </span>
                </div>


                {{-- Empty conversations --}}
                <div
                    class="
            inline-flex
            items-center
            gap-2
            h-9
            px-3
            rounded-lg
            border
            border-amber-200
            bg-amber-50
            text-xs
            text-amber-700
        ">
                    <span>Empty:</span>

                    <span
                        id="empty-conversations-count"
                        class="font-semibold">
                        0
                    </span>
                </div>


                <button
                    type="button"
                    id="delete-empty-conversations"
                    data-url="{{ route('admin.messenger.delete-empty') }}"
                    class="
            inline-flex
            items-center
            gap-2
            h-9
            px-4
            rounded-lg
            border
            border-red-200
            bg-white
            text-xs
            font-medium
            text-red-600
            hover:bg-red-50
            transition
        ">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-8 0h10" />
                    </svg>

                    Delete empty conversations
                </button>

            </div>


        </div>


    </div>


</div>



<div class="h-[calc(100vh-140px)]">

    <div class="h-full min-h-0 bg-white border border-stone-200 rounded-xl shadow-sm overflow-hidden flex">


        {{-- SIDEBAR --}}
        <aside
            class="w-[360px] border-r border-stone-200 flex flex-col bg-white">






            {{-- HEADER --}}

            <div class="px-5 py-5 border-b border-stone-200">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-stone-900">
                            All Messages
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
            ">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 text-stone-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
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
                ">

                    </div>

                </div>

            </div>



            {{-- CONVERSATION LIST --}}

            <div
                id="conversation-list"
                data-url="{{ route('admin.messenger.all.conversations') }}"
                data-delete-message-url="{{ route('admin.messenger.messages.destroy', ':id') }}"
                class="
        flex-1
        overflow-y-auto mt-1
    ">
            </div>









        </aside>



        {{-- CONVERSATION AREA --}}
        <section
            class="flex-1 flex flex-col min-w-0">


            {{-- HEADER --}}
            <div
                id="conversation-header"
                class="border-b border-stone-200 bg-white">

               
                

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
    class="
        hidden
        inline-flex
        items-center
        justify-center
        h-9
        px-3
        rounded-lg
        border
        border-stone-200
        text-xs
        font-medium
        text-stone-600
        hover:bg-stone-50
        transition
    "
>
    <span id="conversation-header-link-text">
        View
    </span>
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

        

    </div>

</div>




            </div>



            {{-- MESSAGES --}}
            <div
                id="conversation-messages"
                class="
                    flex-1
                    overflow-y-auto
                    px-6
                    py-5
                    bg-stone-50
                ">

                @include(
                'dashboard.admin.messenger.partials.messages'
                )

            </div>



            {{-- COMPOSER --}}
            <div
                class="
                    border-t
                    border-stone-200
                    bg-white
                    px-6
                    py-4
                ">

                @include(
                'dashboard.admin.messenger.partials.composer'
                )

            </div>


        </section>


    </div>

</div>

<div
    id="admin-toast"
    class="
        fixed
        top-6
        right-6
        hidden
        z-50
        px-5
        py-3
        rounded-xl
        border
        border-stone-200
        bg-white
        shadow-lg
        text-sm
        text-stone-700
        flex
        items-center
        gap-3
    ">
    <span
        id="admin-toast-icon"
        class="
            w-2
            h-2
            rounded-full
            bg-green-500
        "></span>

    <span id="admin-toast-message">
        Done
    </span>
</div>

@endsection