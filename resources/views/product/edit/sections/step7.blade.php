@include('product.edit.partials.progress-bar', [
    'mode' => 'edit'])


<form method="POST"
    action="{{ route('supplier.products.update-step1', [
          'product' => $product->id,
          'step' => 7
      ]) }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">


    {{-- Product Variants --}}
    <div>
        <h3 class="text-xl font-semibold ">Product Variants</h3>

        @include('product.partials.variant-editor')

    </div>
    <div class="mt-6">

    <h3 class="text-xl font-semibold mb-4">
        Customization & Lead Time
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- LEAD TIME --}}
        <div>

            <label class="block text-sm font-medium text-gray-700 mb-2">
                Lead Time
            </label>

            <div class="flex items-center gap-3">

                <input
                    type="number"
                    name="lead_time"
                    placeholder="e.g., 25"
                    value="{{ old('lead_time', $product->lead_time) }}"
                    class="w-full rounded-xl border border-gray-300
                           px-4 py-3 text-sm
                           focus:ring-2 focus:ring-black/10
                           focus:border-black transition">

                <span class="text-sm text-gray-500 whitespace-nowrap">
                    day(s)
                </span>

            </div>

        </div>

        {{-- CUSTOMIZATION --}}
        <div>

            <label class="block text-sm font-medium text-gray-700 mb-2">
                Customization
            </label>

            <select
                name="customization"
                class="w-full rounded-xl border border-gray-300
                       px-4 py-3 text-sm bg-white
                       focus:ring-2 focus:ring-black/10
                       focus:border-black transition">

                <option value="1"
                    {{ old('customization', $product->customization) == 1 ? 'selected' : '' }}>
                    Customization Available
                </option>

                <option value="0"
                    {{ old('customization', $product->customization) == 0 ? 'selected' : '' }}>
                    No Customization
                </option>

            </select>

        </div>

    </div>

</div>



    <div class="flex justify-between mt-6">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 6]) }}"
            class="mt-4 bg-gray-50 border border-gray-400 hover:bg-gray-200 text-gray-400 px-6 py-2 rounded hover:text-gray-600">
            Previous
        </a>


        <div>
            <button type="submit" class="mt-4 bg-gray-50 border border-gray-400 text-gray-400 px-6 py-2 rounded hover:bg-gray-200 hover:text-gray-600 mr-2">
                Save as Draft
            </button>

            <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500">
                Publish
            </button>

        </div>
    </div>


</form>