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
    </div>
    <button type="button" onclick="addPriceTier()" class="text-blue-700 mt-3">+ Add price tier</button>
</div>