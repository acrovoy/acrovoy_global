{{-- Product Variants --}}
<div class="mb-6">
<h3 class="text-xl font-semibold">Product Variants</h3>

@include('dashboard.supplier.partials.variant-editor')

</div>
<div>
    <h3 class="text-xl font-semibold mb-4">Customization & Lead Time</h3>

    
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <input type="text" name="lead_time" placeholder="Lead time (e.g. 25–35 days)" class="input">
        <select name="customization" class="input">
            <option value="available">Customization Available</option>
            <option value="not_available">No Customization</option>
        </select>
    </div>

</div>