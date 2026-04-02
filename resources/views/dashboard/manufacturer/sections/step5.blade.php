{{-- Price tiers --}}
<div>
    <h3 class="text-xl font-semibold mb-4">Price Tiers</h3>
    <div id="price-tiers" class="space-y-3">
        <div class="grid grid-cols-3 gap-4">
            <input type="number" name="price_tiers[0][min_qty]" placeholder="Min Qty" class="input">
            <input type="number" name="price_tiers[0][max_qty]" placeholder="Max Qty" class="input">
            <input type="number" name="price_tiers[0][price]" placeholder="Unit Price $" class="input">
        </div>
    </div>
    <button type="button" onclick="addPriceTier()" class="text-blue-700 mt-3">+ Add price tier</button>
</div>

{{-- Commercial Terms --}}
<div class="mt-6">
    <h3 class="text-xl font-semibold mb-4">Commercial Terms</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <input type="text" name="moq" placeholder="MOQ (e.g. 10 pcs)" class="input">
        <input type="text" name="lead_time" placeholder="Lead time (e.g. 25–35 days)" class="input">
        <select name="customization" class="input">
            <option value="available">Customization Available</option>
            <option value="not_available">No Customization</option>
        </select>
    </div>
</div>

 
 