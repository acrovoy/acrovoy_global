<div
    id="conversation-drawer"
    class="fixed inset-y-0 right-0 h-full w-full max-w-[460px]
           bg-white shadow-2xl
           transform translate-x-full
           transition-transform duration-300
           z-50 flex flex-col"

    data-subject-type="{{ $subjectType }}"
    data-subject-id="{{ $subjectId }}"
>

    {{-- HEADER --}}
    <div class="border-b bg-gray-50 px-6 py-5">

        <div class="flex items-center gap-4">

            <img
                id="conversation-avatar"
                class="w-12 h-12 rounded-xl object-cover bg-gray-100 border border-gray-200"
                src=""
            >

            <div class="flex-1 min-w-0">

                <div
                    id="conversation-title"
                    class="font-semibold text-gray-900 truncate"
                >
                </div>

                <div
                    id="conversation-status"
                    class="text-xs text-gray-500 mt-1 truncate"
                >
                </div>

            </div>

            <button
                type="button"
                id="close-conversation"
                class="w-9 h-9 flex items-center justify-center
                       rounded-lg text-gray-400
                       hover:bg-gray-100 hover:text-gray-700
                       transition"
            >
                ✕
            </button>

        </div>

    </div>


    {{-- MESSAGES --}}
    <div
        id="conversation-messages"
        class="flex-1 min-h-0 overflow-y-auto
               px-6 py-5
               space-y-4
               bg-white"
    >

    </div>


    {{-- COMPOSER --}}
    <div class="border-t bg-white px-6 py-4">

        <form id="conversation-form">

            @csrf

            <textarea
                id="conversation-input"
                rows="3"
                class="w-full
                       border border-gray-200
                       rounded-xl
                       px-4 py-3
                       text-sm
                       resize-none
                       focus:outline-none
                       focus:ring-2
                       focus:ring-gray-900/10"
                placeholder="Write a message..."
            ></textarea>

            <div class="flex justify-end mt-3">

                <button
                    type="submit"
                    class="px-5 py-2.5
                           bg-gray-900
                           text-white
                           rounded-lg
                           text-sm
                           hover:bg-gray-800
                           transition
                           shadow-sm"
                >
                    Send
                </button>

            </div>

        </form>

    </div>

</div>



{{-- OVERLAY --}}
<div
    id="conversation-overlay"
    class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-40"
>
</div>