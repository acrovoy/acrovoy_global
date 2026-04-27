       {{-- Country of origin --}}
        <div>
            <h3 class="text-xl font-semibold mb-4">Country of Origin</h3>

            <select name="country_id" class="input w-full">
                <option value="">Select a country</option>
                @foreach($countries as $country)
                <option value="{{ $country->id }}">
                    {{ $country->name }}
                </option>
                @endforeach
            </select>
            <p class="text-sm text-gray-500 mt-1">Выберите страну, из которой поставляется товар.</p>
        </div>



        {{-- Shipping Dimensions --}}
<div class="mt-6 bg-white border rounded-xl p-6">
    <h3 class="text-xl font-semibold mb-4">Shipping Dimensions

    <x-help-tooltip width="w-80">
    <div class="space-y-2 leading-relaxed">
        <div class="font-semibold text-white">Shipping Dimensions</div>
        <div class="text-gray-200 text-sm">
            Укажите габариты и вес упаковки для расчёта доставки и логистики.
            Размеры упаковки могут отличаться от реальных размеров самого товара.
        </div>
        <ul class="text-gray-300 text-xs list-disc ml-4 space-y-1">
            <li>Length — длина упаковки в сантиметрах</li>
            <li>Width — ширина упаковки в сантиметрах</li>
            <li>Height — высота упаковки в сантиметрах</li>
            <li>Weight — вес упаковки в килограммах</li>
            <li>Package Type — тип упаковки: коробка, паллет, комплект и т.д.</li>
        </ul>
        <div class="text-blue-400 text-xs border-t border-gray-700 pt-2">
            Note: <span class="text-white/80">транспортировочные габариты могут включать упаковку и защитные материалы,
            поэтому могут быть больше реальных размеров товара.</span>
        </div>
    </div>
</x-help-tooltip>


    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Length --}}
        <div>
            <label class="block mb-1 font-medium">Length (cm)</label>
            <input 
                type="number" 
                step="0.1" 
                name="shipping[length]" 
                value="{{ old('shipping.length', $product->shippingDimensions->length ?? '') }}"
                class="input w-full"
            >
        </div>

        {{-- Width --}}
        <div>
            <label class="block mb-1 font-medium">Width (cm)</label>
            <input 
                type="number" 
                step="0.1" 
                name="shipping[width]" 
                value="{{ old('shipping.width', $product->shippingDimensions->width ?? '') }}"
                class="input w-full"
            >
        </div>

        {{-- Height --}}
        <div>
            <label class="block mb-1 font-medium">Height (cm)</label>
            <input 
                type="number" 
                step="0.1" 
                name="shipping[height]" 
                value="{{ old('shipping.height', $product->shippingDimensions->height ?? '') }}"
                class="input w-full"
            >
        </div>

        {{-- Weight --}}
        <div>
            <label class="block mb-1 font-medium">Weight (kg)</label>
            <input 
                type="number" 
                step="0.01" 
                name="shipping[weight]" 
                value="{{ old('shipping.weight', $product->shippingDimensions->weight ?? '') }}"
                class="input w-full"
            >
        </div>

        {{-- Package Type --}}
        <div>
            <label class="block mb-1 font-medium">Package Type</label>
            <select name="shipping[package_type]" class="input w-full">
                <option value="box" {{ (old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'box') ? 'selected' : '' }}>Box</option>
                <option value="pallet" {{ (old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'pallet') ? 'selected' : '' }}>Pallet</option>
                <option value="set" {{ (old('shipping.package_type', $product->shippingDimensions->package_type ?? '') == 'set') ? 'selected' : '' }}>Set</option>
            </select>
        </div>

    </div>

    <p class="text-sm text-gray-500 mt-2">
        Укажите габариты и вес упаковки для расчёта доставки и логистики.
    </p>
</div>




       
       {{-- Shipping Templates --}}
<div class="mt-6">
    <h3 class="text-xl font-semibold mb-4">Shipping Templates</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Platform / Default Shipping --}}
        @if($defaultShippingTemplate)
            <label
                class="border-2 border-dashed border-gray-400 rounded-xl p-4 cursor-pointer transition
                       hover:border-gray-700 hover:bg-gray-100
                       flex gap-3 items-start bg-gray-50 shadow-sm">

                <input
                    type="checkbox"
                    class="mt-1"
                    checked
                    disabled
                >

                <input
                    type="hidden"
                    name="shipping_templates[]"
                    value="{{ $defaultShippingTemplate->id }}"
                                        
                >

                <div>
                    <div class="font-semibold text-gray-900 flex items-center gap-2">
                        {{ $defaultShippingTemplate->title }}
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">
                            Platform delivery
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                        {{ $defaultShippingTemplate->description }}
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        Price and delivery time will be calculated after order placement
                    </div>
                </div>
            </label>
        @endif

        {{-- Seller Shipping Templates --}}
        @foreach($shippingTemplates as $template)
            <label
                class="border rounded-xl p-4 cursor-pointer transition
                       hover:border-blue-600 hover:bg-blue-50
                       flex gap-3 items-start bg-white shadow-sm">

                <input
                    type="checkbox"
                    name="shipping_templates[]"
                    value="{{ $template->id }}"
                    class="mt-1">

                <div>
                    <div class="font-semibold text-gray-900">
                        {{ $template->title }}
                    </div>

                    <div class="text-sm text-gray-600 mt-1">
                        {{ $template->description }}
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        Seller-defined delivery
                    </div>
                </div>
            </label>
        @endforeach

    </div>

    <p class="text-sm text-gray-500 mt-2">
        Выберите один или несколько шаблонов доставки.
        Если выбран только платформенный вариант — заказ будет ожидать расчёта доставки.
    </p>
</div>




    