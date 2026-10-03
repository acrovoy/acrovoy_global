<div
    x-show="showCustomizationBox"
    x-cloak
    x-transition.opacity
    @keydown.escape.window="showCustomizationBox = false"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
>
    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-md"
        @click="showCustomizationBox = false"
    ></div>


    {{-- MODAL --}}
    <div
        x-show="showCustomizationBox"
        x-transition
        @click.stop
        class="relative z-10 w-full max-w-2xl max-h-[90vh]
               overflow-y-auto
               bg-white
               border border-gray-200
               rounded-2xl
               p-6
               shadow-2xl"
    >

        <div class="flex items-start justify-between mb-5">

            <div>
                <h4 class="text-lg font-semibold text-gray-900">
                    Request Product Customization
                </h4>

                <p class="text-sm text-gray-600 mt-1">
                    Need this product with different dimensions, materials, colors, or other specifications?
                    Create a customization request and send it directly to the manufacturer.
                </p>
            </div>


            {{-- CLOSE --}}
            <button
                type="button"
                @click="showCustomizationBox = false"
                class="ml-4 flex-shrink-0 flex items-center justify-center
                       w-9 h-9 rounded-full
                       text-gray-500
                       hover:bg-gray-100
                       hover:text-gray-900
                       transition"
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
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>

        </div>


        <div class="mb-5 rounded-lg bg-gray-50 border border-gray-200 p-4">

            <h5 class="font-medium text-gray-900 mb-2">
                What happens next?
            </h5>

            <ul class="list-disc list-inside space-y-2 text-sm text-gray-600">
                <li>A new RFQ will be created using this product as a starting point.</li>
                <li>The product specifications will be copied automatically to the RFQ.</li>
                <li>You can modify the requirements to match your project.</li>
                <li>When you're ready, simply publish the RFQ, and it will be sent to the supplier.</li>
            </ul>

        </div>


        @auth

            <form
                action="{{ route('buyer.rfqs.customization.store') }}"
                method="POST"
            >
                @csrf

                <input
                    type="hidden"
                    name="product_id"
                    value="{{ $product1->id }}"
                >

                <input
                    type="hidden"
                    name="type"
                    value="product"
                >

                <input
                    type="hidden"
                    name="title"
                    value="{{ $product1->name }}"
                >

                <button
                    type="submit"
                    class="w-full bg-blue-950 hover:bg-blue-900
                           text-white py-3 rounded-lg
                           text-sm font-semibold
                           transition shadow-md"
                >
                    Create Customization RFQ
                </button>

            </form>

        @endauth


        @guest

            <div class="text-center py-4">

                <p class="text-sm text-gray-500 mb-3">
                    Please sign in to request product customization.
                </p>

                <button
                    disabled
                    class="w-full bg-gray-400 text-white py-3
                           rounded-lg text-sm font-semibold
                           cursor-not-allowed"
                >
                    Create Customization RFQ
                </button>

            </div>

        @endguest

    </div>

</div>