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
     <h3 class="text-xl font-semibold mb-4">Customization & Lead Time</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
       
        <input type="text" name="lead_time" placeholder="Lead time (e.g. 25–35 days)" class="input" value="{{ $product->lead_time }}">
        <select name="customization" class="input">
            <option value="1" {{ old('customization', $product->customization) == 1 ? 'selected' : '' }}>
                Customization Available
            </option>
            <option value="0" {{ old('customization', $product->customization) == 0 ? 'selected' : '' }}>
                No Customization
            </option>
        </select>
    </div>
</div>



<div class="flex justify-between">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 6]) }}"
            class="mt-4 text-white bg-gray-600 hover:bg-gray-400 text-white px-6 py-2 rounded">
            Previous
        </a>



        <button type="submit" class="mt-4 bg-gray-50 border border-gray-500 text-gray-800 px-6 py-2 rounded hover:bg-gray-200">
            Save as Draft
        </button>

    </div>



</form>