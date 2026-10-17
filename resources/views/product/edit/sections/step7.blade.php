@include('product.edit.partials.progress-bar', [
    'mode' => 'edit'])


<form method="POST"
    action="{{ route('supplier.products.update', [
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
    



    <div class="flex justify-between mt-8">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 6]) }}"
            class="mt-4 bg-gray-50 border border-gray-400 hover:bg-gray-200 text-gray-400 px-6 py-2 rounded hover:text-gray-600">
            Previous
        </a>


        <div>
           

            <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-500">
                Next
            </button>

        </div>
    </div>


</form>