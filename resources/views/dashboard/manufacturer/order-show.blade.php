@extends('dashboard.layout')

@section('dashboard-content')

<a href="{{ route('manufacturer.orders') }}"
    class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to orders
</a>

<div class="flex flex-col gap-4">

    {{-- Header --}}
    <div>
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold">
                Order #{{ $order['id'] }} 
            </h2>
            <span class="px-3 py-1 rounded text-sm
            @if($order['status'] === 'pending') bg-yellow-100 text-yellow-800
            @elseif($order['status'] === 'confirmed') bg-green-100 text-green-800
            @elseif($order['status'] === 'paid') bg-blue-100 text-blue-800
            @elseif($order['status'] === 'shipped') bg-blue-100 text-blue-800
            @else bg-gray-100 text-gray-800
            @endif
            ">
                {{ ucfirst($order['status']) }}
            </span>
        </div>
        <p class="text-sm text-gray-500">
            Review order items, track delivery, and handle disputes or status updates.
        </p>
    </div>

    {{-- Notification --}}
    @include('dashboard.manufacturer.partials.ship-notification', ['$order' => $order, '$r_order' => $r_order])

    {{-- Change status --}}
    <div class="border rounded-lg p-4 bg-gray-50">
        <h3 class="font-semibold mb-3">Change order status</h3>

        @php
            use App\Services\OrderStatusService;
            $available = OrderStatusService::availableStatuses($order['status']);

            // Новая проверка: провайдер LogisticCompany и цена доставки 0
            $isLogisticPending = (
                $order['provider_type'] === \App\Models\LogisticCompany::class
                && $r_order->delivery_price == 0
            );
            if ($isLogisticPending) {
                // Убираем "confirmed" из доступных статусов
                $available = array_filter($available, fn($s) => $s !== 'confirmed');
            }
        @endphp

        @if($isLogisticPending)
            <div class="mb-4 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                <div class="mt-0.5">
                    <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10A8 8 0 11.002 9.999 8 8 0 0118 10zM9 7a1 1 0 112 0v3a1 1 0 01-.293.707l-2 2a1 1 0 11-1.414-1.414L9 9.586V7z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-medium">
                        Status update restricted
                    </div>
                    <div class="mt-1 text-amber-700">
                        The order cannot be confirmed yet. Delivery price and delivery time will be provided by the shipping company soon.
                    </div>
                </div>
            </div>
        @endif

        @if(count($available) > 0)
            <form method="POST"
                action="{{ route('manufacturer.orders.update-status', $order['id']) }}"
                class="flex flex-col gap-3">
                @csrf
                <select name="status"
                        class="border rounded px-3 py-2 text-sm"
                        required>
                    <option value="">Select new status</option>
                    @foreach($available as $status)
                        <option value="{{ $status }}">
                            {{ __('order.status.' . $status) }}
                        </option>
                    @endforeach
                </select>
                <textarea name="comment"
                        rows="2"
                        placeholder="Comment (optional)"
                        class="border rounded px-3 py-2 text-sm"></textarea>

                <button type="submit"
                        class="self-start px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                    Update status
                </button>
            </form>
        @else
            <p class="text-gray-500 italic">
                Status cannot be changed at this moment.
            </p>
        @endif
    </div>
    
    {{-- Product sector --}}
    @include('dashboard.manufacturer.partials.product-order', ['order_items' => $order_items, 'order' => $order])
   
    @if($order['provider_type'] === \App\Models\LogisticCompany::class)
    @elseif($order['provider_type'] === \App\Models\Supplier::class)
        {{-- shipment-desk --}}
        @include('dashboard.manufacturer.partials.shipment-desk', ['order_items' => $order_items, 'order' => $order])
    @endif

    {{-- Disputes --}}
    @include('dashboard.manufacturer.partials.dispute', ['order_items' => $order_items, 'order' => $order])

    {{-- Order status timeline --}}
    @include('dashboard.manufacturer.partials.status-timeline', ['order_items' => $order_items, 'order' => $order])

    {{-- Customer --}}
    <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-2">Customer</h3>
        <p>{{ $order['customer'] }}</p>
        <p class="text-sm text-gray-500">{{ $order['email'] }}</p>
    </div>

   {{-- Contact and Shipping Information --}}
    <div class="border border-gray-200 rounded-xl p-6 bg-white">

        <h3 class="text-base font-semibold text-gray-900 mb-6">
            Contact and Shipping Information
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Recipient Contact --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">
                    Recipient Contact
                </h4>

                <div class="space-y-3 text-sm text-gray-700">

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Name</div>
                        <div class="font-medium text-gray-900">
                            {{ $order['first_name'] }} {{ $order['last_name'] }}
                        </div>
                    </div>

                    @if(!empty($order['phone']))
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Phone</div>
                            <div class="font-medium text-gray-900">
                                {{ $order['phone'] }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Shipping Address --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-800 mb-4 uppercase tracking-wide">
                    Shipping Address
                </h4>

                <div class="space-y-3 text-sm text-gray-700">

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Country</div>
                        <div class="font-medium text-gray-900">
                            {{ $order['country_name'] }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">City</div>
                        <div class="font-medium text-gray-900">
                            {{ $order['city'] }}
                        </div>
                    </div>

                    @if(!empty($order['region']))
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Region</div>
                            <div class="font-medium text-gray-900">
                                {{ $order['region_name'] }}
                            </div>
                        </div>
                    @endif

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Street</div>
                        <div class="font-medium text-gray-900">
                            {{ $order['street'] }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Postal Code</div>
                        <div class="font-medium text-gray-900">
                            {{ $order['postal_code'] }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Footer Order Date --}}
    <div class="flex justify-between text-sm text-gray-500">
        <span>Order date: {{ $order['date'] }}</span>
        
    </div>

</div>

<script>
function pickupModal() {
    return {
        open: false,
        itemId: null,
        origin_country_id: null,
        origin_region_id: null,
        origin_city_id: null,
        origin_city_manual: '',
        origin_address: '',
        origin_contact_name: '',
        origin_contact_phone: '',
        weight: null,
        length: null,
        width: null,
        height: null,
        regions: [],
        cities: [],
        async fetchRegions() {
            if(!this.origin_country_id) {
                this.regions = [];
                this.origin_region_id = null;
                this.cities = [];
                this.origin_city_id = null;
                return;
            }
            try {
                const res = await fetch(`{{ route('manufacturer.locations.regions') }}?country_id=${this.origin_country_id}`);
                this.regions = await res.json();
                this.origin_region_id = null;
                this.cities = [];
                this.origin_city_id = null;
            } catch (e) {
                console.error(e);
            }
        },
        async fetchCities() {
            if(!this.origin_region_id) {
                this.cities = [];
                this.origin_city_id = null;
                return;
            }
            try {
                const res = await fetch(`{{ route('manufacturer.locations.locations') }}?region_id=${this.origin_region_id}`);
                this.cities = await res.json();
                this.origin_city_id = null;
            } catch (e) {
                console.error(e);
            }
        }
    }
}
</script>

@endsection
