

@include('product.edit.partials.progress-bar', [$mode = 'edit'])

<form method="POST"
    action="{{ route('supplier.products.update', [
          'product' => $product->id,
          'step' => 3
      ]) }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">


    @include('product.edit.partials.custom-attribute-form', [
    'customAttributes' => $customAttributes,
    'product' => $product,])

    
    

    
    

    


    
    


    <div class="flex justify-between">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 2]) }}"
            class="mt-4 bg-gray-50 border border-gray-400 hover:bg-gray-100 text-gray-400 px-6 py-2 rounded">
            Previous
        </a>



        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded">
            Next
        </button>

    </div>

</form>