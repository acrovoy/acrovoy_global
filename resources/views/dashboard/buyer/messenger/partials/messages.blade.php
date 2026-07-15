{{-- EMPTY STATE --}}

<div
    id="conversation-empty-state"
    class="
        h-full
        flex
        items-center
        justify-center
    "
>

    <div
        class="
            text-center
            max-w-sm
        "
    >

        <div
            class="
                mx-auto
                w-14
                h-14
                rounded-full
                bg-stone-100
                border
                border-stone-200
                flex
                items-center
                justify-center
                text-stone-400
            "
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-6 h-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.82L3 20l1.45-3.62A7.87 7.87 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                />
            </svg>

        </div>


        <h3
            class="
                mt-4
                text-sm
                font-medium
                text-stone-700
            "
        >
            Select a conversation
        </h3>


        <p
            class="
                mt-1
                text-xs
                text-stone-500
            "
        >
            Choose a customer conversation to view messages.
        </p>

    </div>

</div>



{{-- MESSAGES CONTAINER --}}

<div
    id="conversation-message-list"
    class="
        hidden
        space-y-4
        max-w-4xl
        mx-auto
    "
>

    {{-- 
        JS:
        ConversationMessages.render()

        вставляет сюда сообщения
    --}}

</div>