{{-- resources/views/catalog/partials/filters.blade.php --}}
{{-- Используется $request для checked/значений --}}
{{-- Material Filter --}}
<div>
    <h4 class="text-sm font-medium text-gray-700 mb-2">Materials</h4>
    <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
        @foreach(App\Models\Material::all() as $material)
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="material[]" value="{{ $material->slug }}"
                    @if(in_array($material->slug, (array) $request->input('material'))) checked @endif
                    class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-0">
                {{ $material->name }}
            </label>
        @endforeach
    </div>
</div>

{{-- Price Filter --}}
<div>
    <h4 class="text-sm font-medium text-gray-700 mb-2">Price</h4>
    <div class="flex space-x-2">
        <input type="number" name="min_price" value="{{ $request->min_price }}" placeholder="Min"
               class="w-1/2 p-2 border rounded text-sm">
        <input type="number" name="max_price" value="{{ $request->max_price }}" placeholder="Max"
               class="w-1/2 p-2 border rounded text-sm">
    </div>
</div>

{{-- MOQ Filter --}}
<div>
    <h4 class="text-sm font-medium text-gray-700 mb-2">MOQ</h4>
    <input type="number" name="min_moq" value="{{ $request->min_moq }}" placeholder="Min MOQ"
           class="w-full p-2 border rounded text-sm">
</div>

{{-- Sold Filter --}}
<div>
    <h4 class="text-sm font-medium text-gray-700 mb-2">Sold (Min)</h4>
    <input type="number" name="sold_from" value="{{ $request->sold_from }}" placeholder="Min sold"
           class="w-full p-2 border rounded text-sm">
</div>

{{-- Lead Time Filter --}}
<div>
    <h4 class="text-sm font-medium text-gray-700 mb-2">Lead Time (days)</h4>
    <div class="flex space-x-2">
        <input type="number" name="min_lead_time" value="{{ $request->min_lead_time }}" placeholder="Min"
               class="w-1/2 p-2 border rounded text-sm">
        <input type="number" name="max_lead_time" value="{{ $request->max_lead_time }}" placeholder="Max"
               class="w-1/2 p-2 border rounded text-sm">
    </div>
</div>

{{-- Country Filter --}}
<div>
    <h4 class="text-sm font-medium text-gray-700 mb-2">Country of Origin</h4>
    <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
        @foreach(App\Models\Country::all() as $country)
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="country[]" value="{{ $country->id }}"
                    @if(in_array($country->id, (array) $request->input('country'))) checked @endif
                    class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-0">
                {{ $country->name }}
            </label>
        @endforeach
    </div>
</div>

{{-- Apply Button --}}
<button type="submit"
        class="w-full py-2.5 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition duration-200">
    Apply Filters
</button>