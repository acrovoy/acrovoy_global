{{-- SUCCESS --}}
@if(session('success'))
<div class="mb-6 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
    {{ session('success') }}
</div>
@endif

{{-- ERROR --}}
@if(session('error'))
<div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
<div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
    <ul class="list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST"
      action="{{ $shippingTemplate && $shippingTemplate->id
                  ? route('manufacturer.shipping-templates.update', $shippingTemplate->id)
                  : route('manufacturer.shipping-templates.store') }}"
      class="space-y-2 bg-gray-50 border rounded-xl shadow-sm p-6"
      id="shippingTemplateForm">
    @csrf

    @if($shippingTemplate && $shippingTemplate->id)
        @method('PUT')
    @endif

    <input type="hidden" name="manufacturer_id" value="{{ auth()->id() }}">

    @php
        $languages = \App\Models\Language::where('is_active', true)->get();
        $locations = \App\Models\Location::with('country')
    ->whereNull('parent_id') // если сначала хотим страны
    ->get();

$selectedLocations = $shippingTemplate
    ? $shippingTemplate->locations->pluck('id')->toArray()
    : [];
    @endphp

    {{-- Step 1: Basic Info --}}
    <div class="form-step" data-step="1">
        <h3 class="text-2xl font-bold mb-6">Basic Information</h3>

        {{-- TRANSLATIONS --}}
        <div x-data="{ open: false }" class="border rounded-lg p-4 mb-6 bg-white">

            <h4 class="font-semibold mb-4">Template Translations</h4>

            @foreach($languages as $index => $language)
                @php
                    $title = $shippingTemplate
                        ? $shippingTemplate->translations->firstWhere('locale', $language->code)->title ?? ''
                        : '';
                    $description = $shippingTemplate
                        ? $shippingTemplate->translations->firstWhere('locale', $language->code)->description ?? ''
                        : '';
                @endphp

                @if($index === 0)
                    {{-- MAIN LANGUAGE --}}
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">
                            {{ strtoupper($language->code) }}
                        </label>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium text-gray-700">
                                Template Title
                            </label>
                            <input type="text"
                                   name="title[{{ $language->code }}]"
                                   class="input"
                                   placeholder="Title ({{ $language->code }})"
                                   value="{{ old('title.' . $language->code, $title) }}">
                        </div>

                        <div>
                            <label class="block mb-1 font-medium text-gray-700">
                                Description
                            </label>
                            <textarea name="description[{{ $language->code }}]"
                                      class="input"
                                      rows="3"
                                      placeholder="Description ({{ $language->code }})">{{ old('description.' . $language->code, $description) }}</textarea>
                        </div>
                    </div>
                @else
                    {{-- OTHER LANGUAGES --}}
                    <div x-show="open" x-collapse class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">
                            {{ strtoupper($language->code) }}
                        </label>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium text-gray-700">
                                Template Title
                            </label>
                            <input type="text"
                                   name="title[{{ $language->code }}]"
                                   class="input"
                                   placeholder="Title ({{ $language->code }})"
                                   value="{{ old('title.' . $language->code, $title) }}">
                        </div>

                        <div>
                            <label class="block mb-1 font-medium text-gray-700">
                                Description
                            </label>
                            <textarea name="description[{{ $language->code }}]"
                                      class="input"
                                      rows="3"
                                      placeholder="Description ({{ $language->code }})">{{ old('description.' . $language->code, $description) }}</textarea>
                        </div>
                    </div>
                @endif
            @endforeach

            @if(count($languages) > 1)
                <button type="button"
                        @click="open = !open"
                        class="mt-2 text-sm text-blue-600 hover:underline flex items-center gap-1">
                    Other Languages
                    <svg :class="{ 'rotate-180': open }"
                         class="w-4 h-4 transition-transform"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- PRICE --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700">Price</label>
            <input type="number" step="0.01" name="price" class="input"
                   value="{{ old('price', $shippingTemplate->price ?? '') }}">
            <p class="text-sm text-gray-500 mt-1">
                Enter price in <strong>USD</strong>. It will be automatically converted to the user's selected currency.
            </p>
        </div>

        {{-- DELIVERY TIME --}}
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700">Delivery Time</label>
            <input type="text" name="delivery_time" class="input"
                   value="{{ old('delivery_time', $shippingTemplate->delivery_time ?? '') }}"
                   placeholder="e.g. 3-5 days">
        </div>


        
        {{-- LOCATIONS --}}
<x-location-tree 
    :locations="$countries" 
    :selectedLocations="old('locations', $selectedLocations ?? [])" 
/>






    </div>

    {{-- Navigation --}}
    <div class="flex justify-between mt-6">
        <button type="button"
                id="prevBtn"
                class="bg-gray-300 px-6 py-2 rounded hidden hover:bg-gray-400 transition">
            Back
        </button>

        <button type="submit"
                id="submitBtn"
                class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition mt-4">
            Save Template
        </button>
    </div>
</form>


<style>
.input {
    width: 100%;
    border: 1px solid #d1d5db;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    font-family: 'Figtree', sans-serif;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    outline: none;
}
</style>
