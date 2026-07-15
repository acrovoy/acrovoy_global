@extends('dashboard.layout')

@section('dashboard-content')

<div class="h-[calc(100vh-80px)]">

    <div class="h-full bg-white border border-stone-200 rounded-xl shadow-sm overflow-hidden flex">


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


@endsection