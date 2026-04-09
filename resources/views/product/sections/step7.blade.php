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