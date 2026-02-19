{{-- ACROVOY delivery details --}}

<div class="mt-4 border border-gray-200 rounded-xl bg-white shadow-sm p-5">

    
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xs uppercase tracking-wide text-gray-500 mr-2">


        @if($shipment?->provider_type === \App\Models\LogisticCompany::class)
            <span >Acrovoy Delivery Details</span>
        @elseif($shipment?->provider_type === \App\Models\Supplier::class)
            <span >Delivery Details</span>
        @else
            <span >Delivery Details</span>
        @endif


        </h3>
    </div>

    

    {{-- Shipments List --}}
    @if(!empty($order->items))
        <div class="mt-4 border-t border-gray-200 pt-4">
        
        {{-- Notification if delivery price not confirmed --}}
                    @if($order['delivery_price'] > 0)
                    
                        @if(empty($order['delivery_price_confirmed']) || !$order['delivery_price_confirmed'])
                                <div class="flex items-center gap-3 mb-4 p-3 rounded border border-orange-300 bg-orange-50 text-orange-800">
                                <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M12 18.5a6.5 6.5 0 1 0 0-13 6.5 6.5 0 0 0 0 13z"/>
                                </svg>
                                <span class="text-sm font-medium">
                                    Shipping provider is waiting for delivery price confirmation. Please check and Confirm delivery price.
                                </span>
                            </div>
                        @endif
                    @endif
        
        @foreach($order->items as $item)
            @foreach($item->shipments as $shipment)
                @if($shipment->provider_type === \App\Models\LogisticCompany::class)
                    
                @endif
            @endforeach
        @endforeach

            <h4 class="text-xs uppercase tracking-wide text-gray-500 mr-2 mb-2">Shipments:</h4>
            @foreach($order->items as $item)

            
                @if(!empty($item->shipments))
                    @foreach($item->shipments as $shipment)
                        <div class="relative mb-4 p-4 border rounded-lg bg-gray-50 text-sm text-gray-700 shadow-sm">

                            {{-- Status badge in top-right corner --}}
                            <span class="absolute top-4 right-4 px-2 py-1 text-xs font-semibold rounded-full
                                @if($shipment->status === 'pending') bg-yellow-100 text-yellow-700
                                @elseif($shipment->status === 'calculated') bg-green-100 text-green-700
                                @elseif($shipment->status === 'shipped') bg-blue-100 text-blue-700
                                @elseif($shipment->status === 'delivered') bg-gray-200 text-gray-900
                                @else bg-gray-100 text-gray-600
                                @endif
                            ">
                                {{ ucfirst($shipment->status ?? '-') }}
                            </span>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

                                <div>
                                    <div><strong>Product:</strong> {{ $item->product_name ?? 'N/A' }}</div>
                                    <div><strong>Quantity:</strong> {{ $item->quantity ?? '-' }}</div>
                                    @if($shipment->weight)
                                        <div><strong>Weight:</strong> {{ $shipment->weight ?? '-' }} kg</div>
                                    @endif
                                    @if($shipment->length || $shipment->width || $shipment->height)
                                        <div><strong>Dimensions:</strong> 
                                            {{ $shipment->length ?? '-' }} × 
                                            {{ $shipment->width ?? '-' }} × 
                                            {{ $shipment->height ?? '-' }}
                                        </div>
                                    @endif
                                    
                                    <div><strong>Delivery Time:</strong> {{ $shipment->delivery_time ?? '-' }} days</div>
                                    
                                    @if($shipment?->provider_type === \App\Models\LogisticCompany::class)
                                        <div><strong>Shipping Price:</strong> {{ number_format($shipment->shipping_price ?? 0, 2) }} $</div>
                                    @elseif($shipment?->provider_type === \App\Models\Supplier::class)
                                       
                                    @endif

                                    <div><strong>Shipping Provider:</strong> {{ $shipment->provider?->name ?? '-' }}</div>
                                </div>

                                {{-- Tracking number block --}}
                                <div class="flex flex-col justify-center mt-2 sm:mt-0">
                                    <label class="text-xs text-gray-500 mb-1">Tracking Number</label>
                                    <div class="flex items-center bg-white border rounded px-2 py-1 text-gray-800 select-all cursor-pointer"
                                        onclick="navigator.clipboard.writeText('{{ $shipment->tracking_number ?? '' }}'); alert('Tracking number copied!');">
                                        {{ $shipment->tracking_number ?? '-' }}
                                        @if(!empty($shipment->tracking_number))
                                            <svg class="w-4 h-4 ml-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8m-8-4h8m-8-4h8" />
                                            </svg>
                                        @endif
                                    </div>
                                    <small class="text-gray-400 mt-1">Click to copy</small>
                                </div>

                            </div>



                            {{-- Horizontal Timeline --}}
                <div class="flex items-center justify-between mt-4 mb-4">
                    @php
                        $statuses = ['pending', 'accepted', 'picked_up', 'in_transit', 'arrived_at_destination', 'delivered', 'completed'];
                    @endphp

                    @foreach($statuses as $status)
                        @php
                            $currentIndex = array_search($shipment->status, $statuses);
                            $statusIndex = array_search($status, $statuses);
                            $isActive = $statusIndex <= $currentIndex;
                        @endphp

                        <div class="flex-1 relative flex items-center">
                            {{-- Circle --}}
                            <div class="w-4 h-4 rounded-full border-2
                                @if($isActive)
                                    border-emerald-500 bg-emerald-500
                                @else
                                    border-gray-300 bg-white
                                @endif
                                z-10
                            "></div>

                            {{-- Line --}}
                            @if(!$loop->last)
                                <div class="absolute top-1/2 left-4 w-full h-0.5
                                    @if($isActive)
                                        bg-emerald-500
                                    @else
                                        bg-gray-300
                                    @endif
                                    transform -translate-y-1/2
                                "></div>
                            @endif

                            {{-- Label --}}
                            <span class="absolute top-6 left-1/2 transform -translate-x-1/2 text-xs text-gray-500 whitespace-nowrap">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </div>
                    @endforeach
                </div>



                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    @endif


    {{-- Delivery Price --}}
    <div class="flex justify-end text-sm text-gray-700 mb-4 mt-6">
        <div>
        <div class="text-xs uppercase tracking-wide text-gray-500 mr-2">
            Total Delivery Cost
        </div>
        <div class="text-lg font-semibold text-gray-900">
            {{ number_format($order['delivery_price'], 2) }} $
        </div>
        </div>
    </div>


    @php
        $hasLogisticShipment = $order->items
            ->flatMap->shipments
            ->contains('provider_type', \App\Models\LogisticCompany::class);
    @endphp


    @if($hasLogisticShipment)

    <div class="flex flex-col sm:flex-row sm:items-center gap-2">

        {{-- Invoice for Delivery --}}
        @if(!empty($order->invoice_delivery_file))
            <a href="{{ asset('storage/' . $order->invoice_delivery_file) }}"
               target="_blank"
               class="px-3 py-1.5 text-sm
                      border border-blue-300 text-blue-700
                      rounded-md
                      hover:bg-blue-50 hover:border-blue-400">
                Download Delivery Invoice
            </a>
        @else
            <button class="px-3 py-1.5 text-sm
                           border border-gray-300 text-gray-400
                           rounded-md cursor-not-allowed"
                    disabled>
                Delivery invoice not uploaded by the carrier yet
            </button>
        @endif


        {{-- Confirm Delivery Price --}}
        @if($order->delivery_price > 0)
            @if(!$order->delivery_price_confirmed)
                <form method="POST"
                      action="{{ route('buyer.orders.confirm-delivery-price', $order->id) }}">
                    @csrf
                    <button type="submit"
                            class="px-3 py-1.5 text-sm
                                   border border-green-400 text-green-700
                                   rounded-md
                                   hover:bg-green-50 hover:border-green-500">
                        Confirm Delivery Price
                    </button>
                </form>
            @else
                <button type="button"
                        class="px-3 py-1.5 text-sm
                               border border-gray-300 text-gray-400
                               rounded-md cursor-not-allowed flex items-center gap-2"
                        disabled>
                    <svg class="w-4 h-4 text-green-600 flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="3"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Delivery Costs Confirmed
                </button>
            @endif
        @endif

    </div>

@endif



</div>