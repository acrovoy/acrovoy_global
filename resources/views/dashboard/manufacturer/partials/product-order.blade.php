{{-- Товары --}}
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
    </div>

    @foreach($order['items'] as $item)
        <div class="py-1 flex justify-between items-center">
            <div class="flex items-center gap-3">
                {{-- Картинка товара --}}
                <img
                    src="{{ $item['image'] ? asset('storage/' . $item['image']) : asset('images/no-photo.png') }}"
                    alt="{{ $item['product'] }}"
                    class="w-12 h-12 rounded object-contain bg-gray-50 border"
                />

                {{-- Название и количество --}}
                <div class="max-w-xs">
                    <p class="font-medium text-gray-900 truncate" title="{{ $item['product'] }}">
                        {{ $item['product'] }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $item['qty'] }} × {{ number_format($item['price'], 2) }} $
                    </p>
                </div>
            </div>


        @if($order['provider_type'] === \App\Models\LogisticCompany::class)
        
            {{-- Enter origin & dimensions --}}
            <div x-data="pickupModal()" x-cloak>
                    @foreach($order_items as $itemO)

                    @php
                        $shipment = $itemO->shipments->first();
                    @endphp

                    <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 w-full sm:w-96">
                        <div class="flex justify-between text-sm font-medium text-gray-900 mb-2">
                            <div>Данные о грузе и месте погрузки</div>
                        </div>

                        @if($shipment && ($shipment->origin_address || $shipment->origin_city_id || $shipment->origin_region_id))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-700">
                                {{-- Левая колонка: данные погрузки --}}
                                <div class="space-y-1">
                                    @if($shipment->origin_contact_name)
                                        <div><strong>Contact Name:</strong> {{ $shipment->origin_contact_name }}</div>
                                    @endif
                                    @if($shipment->origin_contact_phone)
                                        <div><strong>Phone:</strong> {{ $shipment->origin_contact_phone }}</div>
                                    @endif
                                    @if($shipment->origin_address)
                                        <div><strong>Address:</strong> {{ $shipment->origin_address }}</div>
                                    @endif
                                    @if($shipment->originCity?->name)
                                        <div><strong>City:</strong> {{ $shipment->originCity?->name }}</div>
                                    @endif
                                    @if($shipment->originRegion?->name)
                                        <div><strong>Region:</strong> {{ $shipment->originRegion?->name }}</div>
                                    @endif
                                    @if($shipment->originCountry?->name)
                                        <div><strong>Country:</strong> {{ $shipment->originCountry?->name }}</div>
                                    @endif
                                </div>

                                {{-- Правая колонка: данные упаковки --}}
                                <div class="space-y-1">
                                    <div><strong>Weight:</strong> {{ $shipment->weight ?? '-' }} kg</div>
                                    <div><strong>Dimensions:</strong> 
                                        {{ $shipment->length ?? '-' }} × 
                                        {{ $shipment->width ?? '-' }} × 
                                        {{ $shipment->height ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Edit button --}}
                            <button 
                                type="button"
                                @if(!empty($order['delivery_price_confirmed']) && $order['delivery_price_confirmed'])
                                    disabled
                                    class="mt-2 px-3 py-1.5 text-sm border border-gray-300 text-gray-400 rounded-md cursor-not-allowed"
                                @else
                                    class="mt-2 px-3 py-1.5 text-sm border border-blue-300 text-blue-700 rounded-md hover:bg-blue-50 hover:border-blue-400"
                                @endif
                                @click="
                                    open = true; 
                                    itemId = {{ $itemO->id }};
                                    origin_country_id = {{ $shipment->origin_country_id ?? 'null' }};
                                    origin_region_id = {{ $shipment->origin_region_id ?? 'null' }};
                                    origin_city_id = {{ $shipment->origin_city_id ?? 'null' }};
                                    origin_city_manual = '{{ $shipment->origin_city_manual ?? '' }}';
                                    origin_address = '{{ $shipment->origin_address ?? '' }}';
                                    origin_contact_name = '{{ $shipment->origin_contact_name ?? '' }}';
                                    origin_contact_phone = '{{ $shipment->origin_contact_phone ?? '' }}';
                                    weight = '{{ $shipment->weight ?? '' }}';
                                    length = '{{ $shipment->length ?? '' }}';
                                    width = '{{ $shipment->width ?? '' }}';
                                    height = '{{ $shipment->height ?? '' }}';
                                    fetchRegions();
                                    fetchCities();
                                "
                            >
                                Edit pickup address and dimensions
                            </button>

                        @else
                            <div class="px-3 py-1.5 text-sm text-gray-700">
                                Pickup address not entered. Shipping provider cannot calculate the shipping costs.
                            </div>
                            <button type="button"
                                    class="mt-2 px-3 py-1.5 text-sm border border-blue-300 text-blue-700 rounded-md hover:bg-blue-50 hover:border-blue-400"
                                    @click="open = true; itemId = {{ $itemO->id }};">
                                Enter Pickup Address and Dimensions
                            </button>
                        @endif
                    </div>
                    @endforeach
               
                @include('dashboard.manufacturer.modals.address_dimensions', ['countries' => $countries])
            </div>

        @else
        @endif

            {{-- Сумма за товар --}}
            <div class="font-semibold text-gray-900 ml-4">
                {{ number_format($item['total'], 2) }} $
            </div>
        </div>
    @endforeach

    {{-- Стоимость доставки --}}
    @if(!empty($order['delivery_price']) && $order['delivery_price'] > 0)
        <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-xs uppercase tracking-wide text-gray-500">
            <span>Delivery: <span class="text-xs uppercase tracking-wide text-gray-500">{{$order['delivery_method']}}</span></span>
                @if($order['delivery_method'] === 'Delivery by Acrovoy')
                    <span class="font-semibold">0.00 $</span>
                @else
            <span class="font-semibold">{{ number_format($order['delivery_price'], 2) }} $</span>
            @endif
        </div>
    @else
        <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-gray-700 text-sm">
            <span>Delivery by Acrovoy</span>
            <span class="font-semibold">{{$order['delivery_price']}}</span>
        </div>
    @endif

    {{-- Общая сумма заказа --}}
    <div class="py-3 flex justify-between items-center border-t mt-2 pt-2 text-gray-800 font-medium text-lg">
        <span>Total</span>

        @if($order['provider_type'] === \App\Models\LogisticCompany::class)
            <span>{{ number_format($order['total'], 2) }} $</span>
        @elseif($order['provider_type'] === \App\Models\Supplier::class)
            <span>{{ number_format($order['totalwithdelivery'], 2) }} $</span>
        @else
            <span>{{ number_format($order['total'], 2) }} $</span>
        @endif

    </div>

    {{-- Invoice for product --}}
@include('dashboard.manufacturer.partials.invoice-order', ['order_items' => $order_items, 'order' => $order])
   
</div>