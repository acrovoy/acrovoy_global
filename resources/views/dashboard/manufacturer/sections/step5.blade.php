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


    