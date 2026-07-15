{{-- COMPOSER --}}

<form
    id="conversation-form"
    class="flex items-end gap-3"
>


    @csrf


    {{-- ATTACHMENT BUTTON --}}

    <button
        type="button"
        id="conversation-attachment"
        class="
            w-10
            h-10
            shrink-0
            rounded-lg
            border
            border-stone-200
            bg-white
            flex
            items-center
            justify-center
            text-stone-500
            hover:bg-stone-50
            transition
        "
        title="Attach file"
    >

        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 10-5.656-5.656l-7.071 7.071a6 6 0 108.485 8.485L20 13"
            />

        </svg>

    </button>




    {{-- MESSAGE INPUT --}}

    <div
        class="
            flex-1
            relative
        "
    >

        <textarea
            id="conversation-input"
            rows="1"
            placeholder="Write a message..."
            class="
                w-full
                resize-none
                rounded-xl
                border
                border-stone-200
                bg-white
                px-4
                py-3
                pr-12
                text-sm
                text-stone-700

                placeholder:text-stone-400

                focus:outline-none
                focus:ring-2
                focus:ring-stone-900/10
                focus:border-stone-300

                transition
            "
        ></textarea>


    </div>




    {{-- SEND BUTTON --}}

    <button
        type="submit"
        id="conversation-send"
        class="
            h-10
            px-5
            rounded-lg

            bg-stone-900
            text-white

            text-sm
            font-medium

            hover:bg-stone-800

            transition

            shadow-sm
        "
    >

        Send

    </button>


</form>