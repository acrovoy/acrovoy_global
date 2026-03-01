@extends('dashboard.admin.layout')

@section('dashboard-content')

<div x-data="shipmentPage()">

<a href="{{ route('admin.shipping-center.main') }}"
   class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-4">
    ← Back to orders
</a>

<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-semibold">
            Order #{{ $order->id }}
        </h2>
        <p class="text-sm text-gray-500 mb-6">
            Order details and Acrovoy delivery management.
        </p>
    </div>

    <span class="px-3 py-1 rounded text-sm
        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
        @elseif($order->status === 'paid') bg-indigo-100 text-indigo-800
        @elseif($order->status === 'shipped') bg-green-100 text-green-800
        @elseif($order->status === 'completed') bg-green-200 text-green-900
        @else bg-gray-100 text-gray-800
        @endif
    ">
        {{ ucfirst($order->status) }}
    </span>
</div>

{{-- ORDER ITEMS --}}
<div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">

    <h3 class="font-medium mb-2 text-xs uppercase tracking-wide text-gray-500 border-b pb-2">
        Products in the order:
    </h3>

    @foreach($order->items as $item)
        <div class="py-1 flex justify-between items-center">

            <div class="flex items-center gap-3">
                <img
                    src="{{ $item->product && $item->product->mainImage
                        ? asset('storage/' . $item->product->mainImage->image_path)
                        : asset('images/no-photo.png') }}"
                    class="w-12 h-12 rounded object-contain bg-gray-50 border"
                >

                <div>
                    <p class="font-medium text-gray-900">
                        {{ $item->product_name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ $item->quantity }} × {{ number_format($item->price, 2) }} $
                    </p>
                </div>
            </div>

            <div class="font-semibold text-gray-900">
                {{ number_format($item->price * $item->quantity, 2) }} $
            </div>

        </div>
    @endforeach

    <div class="text-right mt-4 text-lg font-semibold border-t pt-4">
        Total: {{ number_format($order->total, 2) }} $
    </div>

</div>


{{-- Product sector --}}
    @include('dashboard.admin.orders.partials.product-order', ['order' => $order])
   
    @if($order['provider_type'] === \App\Models\LogisticCompany::class)
    @elseif($order['provider_type'] === \App\Models\Supplier::class)
        {{-- shipment-desk --}}
        @include('dashboard.manufacturer.partials.shipment-desk', ['order' => $order])
    @endif

{{-- ACROVOY SHIPMENTS --}}
@if($order->delivery_method === 'Delivery by Acrovoy')

<div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">

<div class="flex items-center justify-between pb-2">
    
    <h3 class="font-medium text-xs uppercase tracking-wide text-gray-500">
        Acrovoy Shipments (Per Item)
    </h3>

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

</div>

    <div class="space-y-6">

@php $totalShipping = 0; @endphp

@forelse($order->items as $item)
    @forelse($item->shipments as $shipment)

        @php $totalShipping += $shipment->shipping_price ?? 0; @endphp

        <div class="relative bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">

            {{-- LEFT STATUS STRIPE --}}
            <div class="absolute left-0 top-0 bottom-0 w-1
                @switch($shipment->status)
                    @case('pending') bg-yellow-400 @break
                    @case('accepted') bg-blue-500 @break
                    @case('picked_up') bg-indigo-500 @break
                    @case('in_transit') bg-cyan-500 @break
                    @case('arrived_at_destination') bg-purple-500 @break
                    @case('delivered') bg-emerald-500 @break
                    @case('completed') bg-gray-500 @break
                    @case('cancelled') bg-red-500 @break
                    @default bg-gray-300
                @endswitch
            "></div>

            <div class="pl-6 pr-6 py-5">

                {{-- TOP HEADER --}}
                <div class="flex justify-between items-start">

                    <div>
                        <div class="text-lg font-semibold text-gray-900">
                            Shipment #{{ $shipment->id }}
                        </div>

                        <div class="text-xs uppercase tracking-wider text-gray-500 mt-1">
                            {{ str_replace('_',' ', $shipment->status) }}
                        </div>
                    </div>

                    <button
                        type="button"
                        class="px-4 py-2 text-xs font-medium bg-gray-900 text-white rounded-lg hover:bg-black transition"
                        @click="openShipmentModal({{ $shipment->id }})"
                    >
                        Edit Shipment
                    </button>
                </div>

                {{-- PRODUCT BLOCK --}}
                <div class="mt-5 bg-gray-900 text-white rounded-lg px-5 py-4">

                    <div class="text-sm font-medium">
                        {{ $item->product_name ?? 'Product unavailable' }}
                    </div>

                    <div class="mt-1 text-xs text-gray-300 uppercase tracking-wide">
                        Quantity: {{ $item->quantity ?? '-' }}
                    </div>

                </div>

                {{-- ROUTE --}}
                <div class="mt-6 grid md:grid-cols-2 gap-8 text-sm">

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                            Pickup Location
                        </div>
                        <div class="font-medium text-gray-900 leading-relaxed">
                            {{ $shipment->origin_address ?? '-' }}<br>
                            {{ optional($shipment->originCity)->name ?? '-' }},
                            {{ optional($shipment->originRegion)->name ?? '-' }},
                            {{ optional($shipment->originCountry)->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                            Delivery Location
                        </div>
                        <div class="font-medium text-gray-900 leading-relaxed">
                            {{ $shipment->destination_address ?? '-' }}<br>
                            {{ optional($shipment->destinationCity)->name ?? '-' }},
                            {{ optional($shipment->destinationRegion)->name ?? '-' }},
                            {{ optional($shipment->destinationCountry)->name ?? '-' }}
                        </div>
                    </div>

                </div>

                {{-- TECH DATA --}}
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm border-t pt-6">

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">
                            Weight
                        </div>
                        <div class="text-lg font-semibold text-gray-900 mt-1">
                            {{ $shipment->weight ?? '-' }} kg
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">
                            Dimensions
                        </div>
                        <div class="text-sm font-semibold text-gray-900 mt-1">
                            {{ $shipment->length ?? '-' }} ×
                            {{ $shipment->width ?? '-' }} ×
                            {{ $shipment->height ?? '-' }} cm
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">
                            Transit Time
                        </div>
                        <div class="text-lg font-semibold text-gray-900 mt-1">
                            {{ $shipment->delivery_time ?? '-' }} days
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">
                            Shipping Cost
                        </div>
                        <div class="text-lg font-bold text-gray-900 mt-1">
                            {{ number_format($shipment->shipping_price, 2) }} $
                        </div>
                    </div>

                </div>

                {{-- TRACKING --}}
                <div class="mt-6 border-t pt-6">
                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                        Tracking Number
                    </div>
                    <div class="bg-gray-100 rounded-lg px-4 py-3 font-mono text-sm text-gray-900">
                        {{ $shipment->tracking_number ?? '-' }}
                    </div>
                </div>

                {{-- COMPACT SHIPMENT STATUS HISTORY --}}

<div class="mt-6 border-t pt-6">
    <h4 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">
        Shipment Status History
    </h4>

    <div class="relative pl-6">
        {{-- Vertical line --}}
        <div class="absolute left-2 top-0 w-px h-full bg-gray-200"></div>

        <div class="space-y-3">
            @forelse($shipment->statuses ?? [] as $status)
                @php
                    $statusValue = $status['status'] instanceof \App\Enums\ShipmentStatus
                        ? $status['status']->value
                        : $status['status'];
                    $isCurrent = $statusValue === $shipment->status;
                    $displayStatus = str_replace('_', ' ', ucfirst($statusValue));
                    $displayDate = $status['date'] ?? $status['created_at'] ?? '';
                @endphp

                <div class="relative flex items-start space-x-2">
                    {{-- Timeline Dot --}}
                    <div class="flex-shrink-0 mt-1 w-2.5 h-2.5 rounded-full
                        {{ $isCurrent ? 'bg-emerald-500' : 'bg-gray-400' }}">
                    </div>

                    {{-- Status + Date on one line --}}
                    <div class="text-sm text-gray-700 flex items-center space-x-3">
                        <span class="font-medium text-gray-800">{{ $displayStatus }}</span>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($displayDate)->format('d M y | H:i') }}</span>
                    </div>
                </div>

                {{-- Comment (if any) --}}
                @if(!empty($status['comment']))
                    <div class="ml-8 mt-1 text-xs text-gray-600 bg-gray-50 border border-gray-100 rounded px-2 py-1">
                        {{ $status['comment'] }}
                    </div>
                @endif

            @empty
                <div class="text-sm text-gray-500 ml-6">No status history</div>
            @endforelse
        </div>
    </div>
</div>







                

            </div>

        </div>

    @empty
        <div class="py-6 text-sm text-gray-500">
            No shipment records for this product yet.
        </div>
    @endforelse
@empty
    <div class="py-6 text-sm text-gray-500">
        No shipment records created yet.
    </div>
@endforelse

</div>





{{-- TOTAL SHIPPING AMOUNT --}}
<div class="mt-6 border rounded-xl p-4 border-gray-200 bg-gray-50">

    <div class="flex justify-between items-center">

        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500">
                Total Shipping Amount
            </div>

            <div class="mt-1 text-2xl font-semibold text-gray-900">
                {{ number_format($totalShipping, 2) }} $
            </div>
        </div>

    </div>

</div>



    {{-- Invoice & Calculate Total --}}
<div class="border rounded-lg p-4 bg-gray-50 mt-4">
    <div class="text-xs uppercase tracking-wide text-gray-500">
                Invoice for delivery service
            </div>
    

    <form method="POST"
          action="{{ route('admin.orders.upload-invoice-delivery', $order->id) }}"
          enctype="multipart/form-data"
          class="flex flex-col gap-3">
        @csrf

        {{-- Invoice --}}
        <label class="text-sm font-medium">Invoice (PDF)</label>
        <div class="relative w-full">
            <input type="file" name="invoice_delivery_file" accept="application/pdf"
                   class="opacity-0 absolute inset-0 w-full h-full cursor-pointer"
                   onchange="document.getElementById('invoice-label-delivery').innerText = this.files[0]?.name || 'Choose a file'">
            <button type="button"
                    class="w-full px-4 py-2 bg-gray-200 border border-gray-700 rounded hover:bg-gray-300 text-gray-700 text-sm text-left cursor-pointer">
                <span id="invoice-label-delivery">Choose a file</span>
            </button>
        </div>

        @if(!empty($order->invoice_delivery_file))
            <a href="{{ asset('storage/' . $order->invoice_delivery_file) }}" target="_blank"
               class="text-blue-600 hover:underline text-sm">
                View current delivery invoice
            </a>
        @endif

        <div class="flex gap-2 mt-3">
            <button type="submit"
                    class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                Upload Invoice
            </button>

            <button type="button"
    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
    onclick="calculateAndNotify({{ $order->id }}, '#total-shipping-display')">
    Calculate & Notify Buyer
</button>

        </div>
    </form>
</div>

</div>
@endif






{{-- CUSTOMER INFO --}}
<div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">

    <h3 class="font-semibold mb-4">
        Customer Information
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

        <div>
            <div class="text-gray-500">Name</div>
            <div class="font-medium">
                {{ $order->first_name }} {{ $order->last_name }}
            </div>
        </div>

        <div>
            <div class="text-gray-500">Phone</div>
            <div>{{ $order->phone }}</div>
        </div>

        <div>
            <div class="text-gray-500">Country</div>
            <div>{{ $order->countryRelation?->name }}</div>
        </div>

        <div>
            <div class="text-gray-500">City</div>
            <div>{{ $order->cityRelation?->name }}</div>
        </div>

        <div>
            <div class="text-gray-500">Region</div>
            <div>{{ $order->regionRelation?->name }}</div>
        </div>

        <div>
            <div class="text-gray-500">Postal Code</div>
            <div>{{ $order->postal_code }}</div>
        </div>

        <div class="md:col-span-2">
            <div class="text-gray-500">Street</div>
            <div>{{ $order->street }}</div>
        </div>

    </div>

</div>

{{-- MODAL --}}
<div
    x-show="isOpen"
    x-transition
    style="display: none"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
>
    <div class="bg-white rounded-lg w-full max-w-md p-5 relative">
        <h3 class="text-lg font-semibold mb-4">Edit Shipment</h3>

        <form @submit.prevent="saveShipment">
            <div class="grid grid-cols-1 gap-3">

                {{-- Weight --}}
                <div>
                    <label class="text-sm text-gray-600">Weight (kg)</label>
                    <input type="number" step="0.01" x-model="form.weight"
                           class="w-full border rounded p-2">
                </div>

                {{-- Dimensions --}}
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="text-sm text-gray-600">Length (cm)</label>
                        <input type="number" step="0.01" x-model="form.length"
                               class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Width (cm)</label>
                        <input type="number" step="0.01" x-model="form.width"
                               class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Height (cm)</label>
                        <input type="number" step="0.01" x-model="form.height"
                               class="w-full border rounded p-2">
                    </div>
                </div>

                {{-- Delivery Time --}}
                <div>
                    <label class="text-sm text-gray-600">Delivery Time (days)</label>
                    <input type="number" x-model="form.delivery_time"
                           class="w-full border rounded p-2">
                </div>

                {{-- Shipping Price --}}
                <div>
                    <label class="text-sm text-gray-600">Shipping Price ($)</label>
                    <input type="number" step="0.01" x-model="form.shipping_price"
                           class="w-full border rounded p-2">
                </div>

                {{-- Status --}}
                    <div>
                        <label class="text-sm text-gray-600">Status</label>
                        <select x-model="form.status" class="w-full border rounded p-2">
                            <template x-for="status in filteredStatuses" :key="status.value">
                                <option :value="status.value" x-text="status.label"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Status Comment --}}
                    <div x-show="statusChanged">
                        <label class="text-sm text-gray-600">Status Comment</label>
                        <textarea
                            x-model="form.comment"
                            class="w-full border rounded p-2"
                            rows="3"
                            placeholder="Optional comment about status change"
                        ></textarea>
                    </div>

                {{-- Tracking Number --}}
                <div>
                    <label class="text-sm text-gray-600">Tracking Number</label>
                    <input type="text" x-model="form.tracking_number"
                           class="w-full border rounded p-2"
                           placeholder="Enter tracking number">
                </div>

            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="closeModal()" class="px-3 py-1 bg-gray-200 rounded">Cancel</button>
                <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>
@php
$allShipments = $order->items->flatMap(function($item){
    return $item->shipments->map(function($s){
        return [
            'id' => $s->id,
            'weight' => $s->weight,
            'length' => $s->length,
            'width' => $s->width,
            'height' => $s->height,
            'delivery_time' => $s->delivery_time,
            'shipping_price' => $s->shipping_price,
            'status' => $s->status,
            'tracking_number' => $s->tracking_number,
            'origin_address' => $s->origin_address,
            'originCity' => optional($s->originCity)->name,
            'originRegion' => optional($s->originRegion)->name,
            'originCountry' => optional($s->originCountry)->name,
            'destination_address' => $s->destination_address,
            'destinationCity' => optional($s->destinationCity)->name,
            'destinationRegion' => optional($s->destinationRegion)->name,
            'destinationCountry' => optional($s->destinationCountry)->name,
        ];
    });
})->values();
@endphp

<script>
window.allowedTransitions = @json(
    collect($allShipments)->mapWithKeys(function ($shipment) {
        return [
            $shipment['id'] => \App\Services\ShipmentStatusService::availableStatuses($shipment['status'])
        ];
    })
);
</script>

<script>
function shipmentPage() {
    return {
        isOpen: false,
        shipmentId: null,
        form: {
            weight: '',
            length: '',
            width: '',
            height: '',
            delivery_time: '',
            shipping_price: '',
            status: '',
            tracking_number:'',
            comment: ''
        },
        shipments: @json($allShipments),

        // 🔹 Оставляем полный список (не удаляем!)
        shipmentStatuses: [
            { value: 'pending', label: 'Pending' },
            { value: 'accepted', label: 'Accepted' },
            { value: 'picked_up', label: 'Picked Up' },
            { value: 'in_transit', label: 'In Transit' },
            { value: 'arrived_at_destination', label: 'Arrived at Destination' },
            { value: 'delivered', label: 'Delivered' },
            { value: 'completed', label: 'Completed' },
            { value: 'cancelled', label: 'Cancelled' },
        ],

        // 🔥 Новый computed список — только разрешённые
        get filteredStatuses() {
            if (!this.shipmentId) return this.shipmentStatuses;

            const shipment = this.shipments.find(s => s.id === this.shipmentId);
            if (!shipment) return this.shipmentStatuses;

            const allowed = window.allowedTransitions[this.shipmentId] ?? [];

            return this.shipmentStatuses.filter(s =>
                s.value === shipment.status || allowed.includes(s.value)
            );
        },

        get statusChanged() {
            const shipment = this.shipments.find(s => s.id === this.shipmentId);
            if (!shipment) return false;
            return shipment.status !== this.form.status;
        },

        openShipmentModal(id) {
            this.shipmentId = id;
            const shipment = this.shipments.find(s => s.id === id);
            if(shipment){
                this.form.weight = shipment.weight ?? '';
                this.form.length = shipment.length ?? '';
                this.form.width = shipment.width ?? '';
                this.form.height = shipment.height ?? '';
                this.form.delivery_time = shipment.delivery_time ?? '';
                this.form.shipping_price = shipment.shipping_price ?? '';
                this.form.status = shipment.status ?? 'pending';
                this.form.comment = '';
                this.form.tracking_number = shipment.tracking_number ?? '';
            } else {
                console.warn('Shipment not found for id:', id);
            }
            this.isOpen = true;
        },

        closeModal() {
            this.isOpen = false;
        },

        async saveShipment() {
    try {
        const res = await fetch(`/dashboard/admin/orders/{{ $order->id }}/shipments/${this.shipmentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(this.form)
        });

        if(!res.ok) {
            const text = await res.text();  // получаем HTML ошибки
            console.error(text);
            throw new Error('Failed to save');
        }

        const data = await res.json(); // теперь можно парсить JSON
        console.log(data);
        location.reload();
    } catch(e) {
        alert('Error saving shipment');
        console.error(e);
    }
}

    }
}
</script>



<script>
async function calculateAndNotify(orderId, displaySelector) {
    try {
        // формируем URL динамически
        const route = `/dashboard/admin/orders/${orderId}/calculate-delivery`;

        const response = await fetch(route, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        });

        if (!response.ok) throw new Error('Failed to calculate delivery');

        const data = await response.json();

        // обновляем на странице
        const displayEl = document.querySelector(displaySelector);
        if (displayEl) {
            displayEl.innerText = data.totalShipping.toFixed(2) + ' $';
        }

        alert(data.message || 'Total shipping updated successfully!');
    } catch (err) {
        console.error(err);
        alert('Error calculating total shipping');
    }
}
</script>



</div> 

@endsection
