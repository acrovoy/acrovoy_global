{{-- Товары --}}
@foreach($order->shipments as $shipment)
        @php
            $provider_type =  $shipment->provider_type;
        @endphp
    @endforeach

<div x-data="{
    open: false,
    itemId: null,
    origin_country_id: null,
    origin_region_id: null,
    origin_city_id: null,
    origin_city_manual: '',
    origin_address: '',
    origin_contact_name: '',
    origin_contact_phone: ''
}" class="rounded-lg border p-4 bg-white shadow-sm">

    <div class="flex items-center justify-between mb-2 border-b pb-2">
        <h3 class="text-xs uppercase tracking-wide text-gray-500">
            Products in the order:
        </h3>

@if($provider_type === \App\Models\LogisticCompany::class)
        <span class="px-2 py-1 text-xs rounded
        @if(!empty($order->delivery_price_confirmed) && $order->delivery_price_confirmed)
            bg-emerald-100 text-emerald-700
        @else
            bg-red-100 text-red-700
        @endif
    ">
        @if(!empty($order->delivery_price_confirmed) && $order->delivery_price_confirmed)
            Costs Confirmed by the Buyer
        @else 
            Costs are NOT confirmed by the Buyer
        @endif
    </span>
@else
@endif

    </div>

    

    @foreach($order->items as $item)
        <div class="py-1 flex justify-between items-center">
            <div class="flex items-center gap-3">
                {{-- Картинка товара --}}
                <img
                        src="{{ $item->product?->mainImage?->image_path 
                            ? asset('storage/' . $item->product->mainImage->image_path) 
                            : asset('images/no-photo.png') }}"
                        alt="{{ $item->product?->name ?? 'Product' }}"
                        class="w-12 h-12 rounded object-contain bg-gray-50 border"
                    />

                {{-- Название и количество --}}
                @php
                    if ($order->type === 'product') {
                        $itemId = $item->product?->id;
                        $itemTitle = $item->product?->name;
                    } elseif ($order->type === 'rfq') {
                        $itemId = $order->rfqOffer?->rfq?->id;
                        $itemTitle = $order->rfqOffer?->rfq?->title;
                    } else {
                        $itemId = null;
                        $itemTitle = 'Item';
                    }
                @endphp

                <div class="max-w-xs">
                    <p class="font-medium text-xs text-gray-500 truncate">
                        ID: {{ $itemId ?? '-' }}
                    </p>

                    <p class="font-medium text-gray-900 truncate" title="{{ $itemTitle }}">
                        
                        @if($order->type === 'rfq' && $order->rfqOffer?->rfq)
        
                            <a href="{{ route('admin.rfqs.show', $order->rfqOffer->rfq->id) }}"
                            class="hover:underline text-indigo-600"
                            target="_blank"
                            rel="noopener">
                                {{ $itemTitle ?? '-' }}
                            </a>

                        @elseif($item->product?->slug)

                            <a href="{{ route('product.show', $item->product->slug) }}"
                            class="hover:underline text-indigo-600"
                            target="_blank"
                            rel="noopener">
                                {{ $itemTitle ?? '-' }}
                            </a>

                        @else

                            {{ $itemTitle ?? '-' }}

                        @endif
                        
                    </p>

                    <p class="text-xs text-gray-500">
                        {{ $item->quantity }} × {{ number_format($item->price, 2) }} $
                    </p>
                </div>
            </div>


        
       
            {{-- Enter origin & dimensions --}}
            
                    

                    @php
                        $shipment = $item->shipments->first();
                    @endphp

                    <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 w-full sm:w-96">
                        <div class="flex justify-between text-sm font-medium text-gray-900 mb-2">
                            <div>Данные о грузе и месте погрузки</div>
                        </div>

                        @if($shipment && ($shipment->origin_address || $shipment->origin_city_id || $shipment->origin_region_id))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-700">
                            

                             @include('dashboard.admin.orders.partials.origin_dimens_block', ['shipment' => $shipment])
                               
                            </div>


                            

                        @else
                            <div class="px-3 py-1.5 text-sm text-gray-700">
                                @if($provider_type === \App\Models\LogisticCompany::class)
                                Pickup address not entered by the shipper. 
                                Shipping provider cannot calculate the shipping costs.
                                @else
                                Supplier didnt enter the shipping details for buyer
                                @endif
                            </div>
                            
                        @endif
                    </div>
                    
               
                
            

        

            {{-- Сумма за товар --}}
            <div class="font-semibold text-gray-900 ml-4">
                {{ number_format($item->quantity * $item->price, 2) }} $
            </div>
        </div>
    @endforeach

    {{-- Стоимость доставки --}}
    
        <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-xs uppercase tracking-wide text-gray-500">
            <span>Delivery: <span class="text-xs uppercase tracking-wide text-gray-500">{{$order->delivery_method}}</span></span>
                
            <span class="font-semibold">{{ number_format($order->delivery_price, 2) }} $</span>
            
        </div>
    

    

    {{-- Общая сумма заказа --}}
    <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-gray-800 font-medium text-lg">
        <span>Total</span>

        @if($provider_type === \App\Models\LogisticCompany::class)
            <span>{{ number_format($order->total + $order->delivery_price, 2) }} $</span>
        @elseif($provider_type === \App\Models\Supplier::class)
            <span>{{ number_format($order->total, 2) }} $</span>
        @else
            <span>{{ number_format($order->total, 2) }} $</span>
        @endif

    </div>

    {{-- Invoice for product --}}
@include('dashboard.admin.orders.partials.invoice-order', ['order' => $order])
   
</div>