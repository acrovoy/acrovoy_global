{{-- SUPPORT REQUEST DRAWER --}}

<div
    id="create-support-request-drawer"
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
        data-close-support-request
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
                Request Acrovoy Support
            </h3>


            <p class="text-sm text-gray-500 mt-1">
                Create a support request and our team will assist you.
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


            {{-- QUEUE INFO --}}

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
                    How support works
                </p>


                <ul class="list-disc pl-5 space-y-1">

                    <li>
                        Requests are handled in the order they are received.
                    </li>

                    <li>
                        Response time depends on the current support queue.
                    </li>

                    <li>
                        Please provide detailed information to help us resolve your issue faster.
                    </li>

                </ul>

            </div>





            {{-- SUBJECT --}}

            <div>

                <label
                    class="
                        text-xs
                        text-gray-500
                        uppercase
                        tracking-wide
                    "
                >
                    Subject
                </label>


                <input
                    id="support-subject"
                    type="text"
                    maxlength="150"
                    placeholder="Example: Payment issue, supplier dispute..."
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





            {{-- CATEGORY --}}

            <div>

                <label
                    class="
                        text-xs
                        text-gray-500
                        uppercase
                        tracking-wide
                    "
                >
                    Category
                </label>


                <select
                    id="support-category"
                    class="
                        w-full
                        mt-2
                        border
                        border-gray-200
                        rounded-lg
                        px-3
                        py-2
                        text-sm
                        bg-white
                        focus:outline-none
                        focus:ring-2
                        focus:ring-gray-900/10
                    "
                >

                    <option value="">
                        Select category
                    </option>

                    <option value="Technical issue">
                        Technical issue
                    </option>

                    <option value="Payment issue">
                        Payment issue
                    </option>

                    <option value="Order issue">
                        Order issue
                    </option>

                    <option value="Supplier / Buyer issue">
                        Supplier / Buyer issue
                    </option>

                    <option value="Dispute">
                        Dispute
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

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
                    id="support-description"
                    rows="6"
                    maxlength="2000"
                    placeholder="Describe your issue..."
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






            {{-- WARNING --}}

            <div
                class="
                    p-4
                    rounded-lg
                    bg-yellow-50
                    border
                    border-yellow-100
                    text-sm
                    text-yellow-800
                "
            >

                <p class="font-semibold mb-1">
                    Before contacting support
                </p>


                <p>
                    Please try resolving the issue directly with the other participant first.
                    Support should be used for disputes, technical problems, fraud reports,
                    or situations requiring Acrovoy assistance.
                </p>


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
                data-close-support-request
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
                id="submit-support-request"
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
                Submit Request
            </button>


        </div>



    </div>


</div>