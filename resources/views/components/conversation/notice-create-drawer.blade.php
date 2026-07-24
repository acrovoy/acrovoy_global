{{-- CREATE NOTICE DRAWER --}}

<div
    id="create-notice-drawer"
    class="
        fixed
        inset-0
        z-50
        hidden
    "
>

    {{-- OVERLAY --}}

    <div
        class="
            absolute
            inset-0
            bg-black/40
            backdrop-blur-sm
        "
        data-close-notice
    ></div>



    {{-- DRAWER --}}

    <div
        class="
            absolute
            right-0
            top-0
            h-full
            w-[460px]
            bg-white
            shadow-2xl
            flex
            flex-col
        "
    >

        {{-- HEADER --}}

        <div
            class="
                px-6
                py-5
                border-b
                bg-gray-50
            "
        >

            <h3 class="text-lg font-semibold text-gray-900">
                Create Notice
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Create a system notification that will appear in the Notice Center.
            </p>

        </div>




        {{-- BODY --}}

        <div
            class="
                flex-1
                overflow-y-auto
                px-6
                py-5
                space-y-5
            "
        >

            {{-- INFO --}}

            <div
                class="
                    p-4
                    rounded-lg
                    bg-blue-50
                    border
                    border-blue-100
                    text-sm
                    text-blue-800
                    leading-relaxed
                "
            >

                <p class="font-semibold mb-2">
                    Notice information
                </p>

                <p>
                    Notices are displayed in the user's Notice Center and can be
                    used to inform users about important events, updates, or system actions.
                </p>

            </div>



            {{-- TITLE --}}

            <div>

                <label
                    class="
                        text-xs
                        text-gray-500
                        uppercase
                        tracking-wide
                    "
                >
                    Title
                </label>

                <input
                    id="notice-title"
                    type="text"
                    maxlength="150"
                    placeholder="Notice title..."
                    class="
                        w-full
                        mt-2
                        border
                        border-gray-200
                        rounded-lg
                        px-3
                        py-2
                        text-sm
                        focus:outline-none
                        focus:ring-2
                        focus:ring-gray-900/10
                    "
                >

            </div>




            {{-- SUBTITLE --}}

            <div>

                <label
                    class="
                        text-xs
                        text-gray-500
                        uppercase
                        tracking-wide
                    "
                >
                    Subtitle
                </label>

                <input
                    id="notice-subtitle"
                    type="text"
                    maxlength="200"
                    placeholder="Short description..."
                    class="
                        w-full
                        mt-2
                        border
                        border-gray-200
                        rounded-lg
                        px-3
                        py-2
                        text-sm
                        focus:outline-none
                        focus:ring-2
                        focus:ring-gray-900/10
                    "
                >

            </div>




            {{-- DESCRIPTION --}}

            <div>

                <label
                    class="
                        text-xs
                        text-gray-500
                        uppercase
                        tracking-wide
                    "
                >
                    Description
                </label>

                <textarea
                    id="notice-description"
                    rows="8"
                    maxlength="5000"
                    placeholder="Write the full notice..."
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

            </div>

        </div>




        {{-- FOOTER --}}

        <div
            class="
                border-t
                bg-white
                px-6
                py-4
                flex
                justify-between
                gap-2
            "
        >

            <button
                type="button"
                data-close-notice
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
                id="submit-notice"
                type="button"
                class="
                    px-4
                    py-2
                    text-sm
                    rounded-lg
                    bg-blue-600
                    text-white
                    hover:bg-blue-700
                    transition
                    shadow-sm
                "
            >
                Create Notice
            </button>

        </div>

    </div>

</div>