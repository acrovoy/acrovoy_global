{{-- Shipping Information --}}
@if($product1->shippingTemplates->isNotEmpty())
<div class="bg-white rounded-xl shadow p-6 mb-6">
    <h3 class="font-semibold text-lg mb-2 leading-none">
        {{ __('product/product_show.shipping_information') }}
    </h3>

    <p class="text-sm text-gray-500 leading-tight">
        {{ __('product/product_show.shipping_templates_text2') }}
        <span class="font-medium">
            {{ __('product/product_show.shipping_center') }}
        </span>.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
        @foreach($product1->shippingTemplates as $template)
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <h4 class="font-semibold text-gray-900">
                {{ $template->title }}
            </h4>

 {{-- DELIVERY TIME --}}
@if(empty($template->price) || $template->price == 0 || empty($template->delivery_time))


@else
<div class="">
                        <div class="font-medium text-xs text-gray-400">
                            {{ __('product/product_show.delivery_time') }} {{ $template->delivery_time }} {{ __('product/product_show.days') }}
                        </div>
                        
                    </div>

@endif

            @if(!empty($template->description))
                <p class="text-gray-700 text-sm mt-1">
                    {{ $template->description }}
                </p>
            @endif

            <div class="mt-3 text-gray-700 text-sm space-y-2">

                {{-- PRICE AND DELIVERY --}}
                @if(empty($template->price) || $template->price == 0 || empty($template->delivery_time))
                    <div class="inline-flex items-center gap-2
                        bg-blue-50 border border-blue-100
                        px-3 py-1.5 rounded-lg text-gray-900 font-medium text-xs">
                        Delivery costs and delivery time will be calculated after order placement
                    </div>
                @else
                    {{-- PRICE --}}
                    <div class="inline-flex items-center gap-2
                        bg-blue-50 border border-blue-100
                        px-3 py-1.5 rounded-lg">
                        <span class="text-sm text-blue-900 font-medium">
                            {{ __('product/product_show.price') }}
                        </span>
                        <span class="text-base font-semibold text-blue-900">
                            ${{ number_format($template->price, 2) }}
                        </span>
                    </div>

                    
                @endif

            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
