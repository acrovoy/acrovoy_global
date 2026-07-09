
<div
    x-show="showCustomizationBox"
    x-transition
    @click.outside="showCustomizationBox = false"
    class="mt-6 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-6">

    <h4 class="text-lg font-semibold text-gray-900 mb-2">
        Request Product Customization
    </h4>

    <p class="text-sm text-gray-600 mb-5">
        Need this product with different dimensions, materials, colors, or other specifications?
        Create a customization request and send it directly to the manufacturer.
    </p>

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
        <form action="{{ route('buyer.rfqs.customization.store') }}" method="POST">
            @csrf

            <input
                type="hidden"
                name="product_id"
                value="{{ $product1->id }}">
                <input
                type="hidden"
                name="type"
                value="product">
                <input
                type="hidden"
                name="title"
                value="{{ $product1->name }}">

            <button
                type="submit"
                class="w-full bg-blue-950 hover:bg-blue-900 text-white py-3 rounded-lg text-sm font-semibold transition shadow-md">
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
                class="w-full bg-gray-400 text-white py-3 rounded-lg text-sm font-semibold cursor-not-allowed">
                Create Customization RFQ
            </button>
        </div>
    @endguest

</div>

