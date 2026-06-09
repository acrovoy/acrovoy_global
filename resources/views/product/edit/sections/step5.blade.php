<form method="POST"
    action="{{ route('supplier.products.update-step1', [
          'product' => $product->id,
          'step' => 5
      ]) }}"
    enctype="multipart/form-data"
    class="" id="productForm">
    @csrf
    @method('PUT')

    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

    {{-- Commercial Terms  --}}
    <div>
        <h3 class="text-xl font-semibold mb-4">Commercial Terms</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <input type="text" name="moq" placeholder="MOQ (e.g. 10 pcs)" class="input" value="{{ $product->moq }}">

        </div>
    </div>

    {{-- PRICE TIERS  --}}
    <div class=" mt-6 ">
        <h3 class="text-xl font-semibold mb-4">Price Tiers</h3>
        <div id="price-tiers" class="space-y-3">
            @foreach($product->priceTiers as $i => $tier)
            <div class="grid grid-cols-3 gap-4 items-center" id="price-tier-{{ $i }}">
                <input type="number" name="price_tiers[{{ $i }}][min_qty]" placeholder="Min Qty" class="input" value="{{ $tier->min_qty }}">
                <input type="number" name="price_tiers[{{ $i }}][max_qty]" placeholder="Max Qty" class="input input-max-qtty" value="{{ $tier->max_qty }}">
                <div class="flex gap-2">
                    <input type="number" name="price_tiers[{{ $i }}][price]" placeholder="Unit Price $" class="input flex-1" value="{{ $tier->price }}">
                    @if($i > 0) <button type="button" onclick="removePriceTier({{ $i }})" class="text-red-600 font-bold hover:text-red-800">✕</button>@endif
                </div>
            </div>
            @endforeach
            @if (count($product->priceTiers) === 0)
            <div class="grid grid-cols-3 gap-4" id="price-tier-0">
                <input type="number" name="price_tiers[0][min_qty]" placeholder="Min Qty" class="input">
                <input type="number" name="price_tiers[0][max_qty]" placeholder="Max Qty" class="input input-max-qtty">
                <input type="number" name="price_tiers[0][price]" placeholder="Unit Price $" class="input">
            </div>
            @endif
        </div>
        <button type="button" onclick="addPriceTier()" class="text-blue-700 mt-3">+ Add price tier</button>
    </div>

    <div class="flex justify-between">

        <a href="{{ route('supplier.products.edit-step', [$product->id, 4]) }}"
            class="mt-4 text-white bg-gray-600 hover:bg-gray-400 text-white px-6 py-2 rounded">
            Previous
        </a>



        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded">
            Next
        </button>

    </div>

</form>

@vite(['resources/js/product-edit.js'])