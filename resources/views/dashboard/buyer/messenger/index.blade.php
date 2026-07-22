@extends('dashboard.layout')

@section('dashboard-content')

<div class="h-[calc(100vh-80px)]">

    <div class="h-full min-h-0 bg-white border border-stone-200 rounded-xl shadow-sm overflow-hidden flex">


        {{-- SIDEBAR --}}
        <aside
            class="w-[360px] border-r border-stone-200 flex flex-col bg-white"
        >

            @include(
                'dashboard.buyer.messenger.partials.sidebar'
            )

        </aside>



        {{-- CONVERSATION AREA --}}
        <section
            class="flex-1 flex flex-col min-w-0"
        >


            {{-- HEADER --}}
            <div
                id="conversation-header"
                class="border-b border-stone-200 bg-white"
            >

                @include(
                    'dashboard.buyer.messenger.partials.header'
                )

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
                "
            >

                @include(
                    'dashboard.buyer.messenger.partials.messages'
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
                "
            >

                @include(
                    'dashboard.buyer.messenger.partials.composer'
                )

            </div>


        </section>


    </div>

</div>

{{-- REQUEST SUPPORT DRAWER --}}

<div
    id="request-support-drawer"
    class="
        fixed
        inset-0
        z-50
        hidden
    "
>

    {{-- Backdrop --}}

    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        data-close-support
    ></div>


    {{-- Panel --}}

    <div
        class="
            absolute
            right-0
            top-0
            h-full
            w-full
            max-w-[460px]
            bg-white
            shadow-2xl
            flex
            flex-col
        "
    >

        {{-- Header --}}

        <div class="px-6 py-5 border-b bg-gray-50">

            <div class="flex items-start justify-between">

                <div>

                    <h2 class="text-lg font-semibold text-gray-900">
                        Request Support
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Need assistance from the Acrovoy team?
                    </p>

                </div>

                <button
                    type="button"
                    data-close-support
                    class="
                        w-9
                        h-9
                        rounded-lg
                        border
                        border-gray-200
                        flex
                        items-center
                        justify-center
                        text-gray-500
                        hover:bg-gray-100
                        transition
                    "
                >
                    ✕
                </button>

            </div>

        </div>



        {{-- Body --}}

        <div
            class="
                flex-1
                overflow-y-auto
                px-6
                py-5
                space-y-5
            "
        >

            {{-- Description --}}

            <div
                class="
                    p-4
                    rounded-lg
                    bg-yellow-50
                    border
                    border-yellow-100
                    text-sm
                    text-yellow-800
                    leading-relaxed
                "
            >

                <p class="font-semibold mb-2">
                    What happens when you request support?
                </p>

                <ul class="list-disc pl-5 space-y-1">

                    <li>
                        The Acrovoy Support Team will be notified.
                    </li>

                    <li>
                        A support specialist may join this conversation.
                    </li>

                    <li>
                        Both participants will be able to see all support messages.
                    </li>

                </ul>

            </div>



            {{-- Reason --}}

            <div>

                <label
                    class="
                        text-xs
                        text-gray-500
                        uppercase
                        tracking-wide
                    "
                >
                    Reason (optional)
                </label>

                <textarea
                    id="support-reason"
                    rows="5"
                    maxlength="1000"
                    placeholder="Describe the issue..."
                    class="
                        w-full
                        mt-2
                        border
                        border-gray-200
                        rounded-lg
                        px-3
                        py-2
                        text-sm
                        resize-none
                        focus:outline-none
                        focus:ring-2
                        focus:ring-gray-900/10
                    "
                ></textarea>

                <p
                    class="
                        mt-2
                        text-xs
                        text-gray-500
                    "
                >
                    Example:
                    Supplier is not responding,
                    payment request outside the platform,
                    dispute about specifications,
                    technical issue.
                </p>

            </div>



            {{-- Info --}}

            <div
                class="
                    p-4
                    rounded-lg
                    bg-gray-50
                    border
                    border-gray-200
                "
            >

                <div class="flex gap-3">

                    <div class="text-xl">
                        🛟
                    </div>

                    <div>

                        <div
                            class="
                                text-sm
                                font-semibold
                                text-gray-900
                            "
                        >
                            Before requesting support
                        </div>

                        <p
                            class="
                                text-sm
                                text-gray-600
                                mt-2
                                leading-relaxed
                            "
                        >
                            Support is intended for disputes,
                            technical issues, fraud reports,
                            or assistance with negotiations.
                        </p>

                        <p
                            class="
                                text-sm
                                text-gray-600
                                mt-2
                            "
                        >
                            Please try resolving the issue directly before requesting support.
                        </p>

                    </div>

                </div>

            </div>

        </div>



        {{-- Footer --}}

        <div
            class="
                border-t
                border-gray-200
                bg-white
                px-6
                py-4
                flex
                items-center
                justify-between
                gap-2
            "
        >

            <button
                type="button"
                data-close-support
                class="
                    px-4
                    py-2
                    text-sm
                    rounded-lg
                    border
                    border-gray-200
                    text-gray-600
                    hover:bg-gray-50
                    transition
                "
            >
                Cancel
            </button>

            <button
                id="request-support-submit"
                type="button"
                class="
                    px-4
                    py-2
                    text-sm
                    rounded-lg
                    bg-gray-900
                    text-white
                    hover:bg-gray-800
                    transition
                    shadow-sm
                "
            >
                Request Support
            </button>

        </div>

    </div>

</div>

<x-conversation.support-request-drawer />

@endsection