<div x-data="shipmentPage()" x-cloak>
    {{-- SHIPMENTS --}}

    
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h3 class="font-medium mb-2 text-xs uppercase tracking-wide text-gray-500 border-b pb-2">
            Order Shipments (Per Item)
        </h3>

        <div class="divide-y divide-gray-100">
            @php
                $totalShipping = 0;
            @endphp

            @forelse($order_items as $item)
    @forelse($item->shipments as $shipment)
        @php
            $totalShipping += $shipment->shipping_price ?? 0;
        @endphp

        <div class="py-6 border-b border-gray-200 last:border-0">
            <div class="flex justify-between items-start gap-6">

                {{-- LEFT SIDE --}}
                <div class="flex-1 space-y-3">

                    <div class="flex flex-col gap-3">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <div class="text-base font-semibold text-gray-900">
            Shipment #{{ $shipment->id }}
        </div>

        <span class="px-3 py-1 text-xs font-medium rounded-full
            @if(!empty($order->delivery_price_confirmed) && $order->delivery_price_confirmed)
                bg-emerald-100 text-emerald-700
            @elseif($shipment->status === 'pending')
                bg-amber-100 text-amber-700
            @elseif($shipment->status === 'calculated')
                bg-blue-100 text-blue-700
            @elseif($shipment->status === 'shipped')
                bg-blue-100 text-blue-700
            @elseif($shipment->status === 'delivered')
                bg-gray-200 text-gray-900
            @else
                bg-gray-100 text-gray-600
            @endif
        ">
            @if(!empty($order->delivery_price_confirmed) && $order->delivery_price_confirmed)
                Confirmed by Buyer
            @else
                {{ ucfirst($shipment->status) }}
            @endif
        </span>
    </div>

    
</div>

                    {{-- Delivery --}}
                    <div class="text-sm text-gray-700">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                            Delivery Address
                        </div>
                        <div class="leading-relaxed">
                            {{ $shipment->destination_address ?? '-' }},
                            {{ optional($shipment->destinationCity)->name ?? '-' }},
                            {{ optional($shipment->destinationRegion)->name ?? '-' }},
                            {{ optional($shipment->destinationCountry)->name ?? '-' }}
                        </div>
                    </div>

                    {{-- Product block --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $item->product_name ?? 'Product unavailable' }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500 uppercase tracking-wide">
                            Quantity:
                            <span class="text-gray-800 font-medium normal-case">
                                {{ $item->quantity ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Shipment Details Grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-2">

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                                Weight
                            </div>
                            <div class="text-sm font-medium text-gray-800">
                                {{ $shipment->weight ?? '-' }} kg
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                                Dimensions (cm)
                            </div>
                            <div class="text-sm font-medium text-gray-800">
                                {{ $shipment->length ?? '-' }} ×
                                {{ $shipment->width ?? '-' }} ×
                                {{ $shipment->height ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                                Delivery Time
                            </div>
                            <div class="text-sm font-medium text-gray-800">
                                {{ $shipment->delivery_time ?? '-' }} days
                            </div>
                        </div>

                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                                Shipping Price
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ number_format($shipment->shipping_price, 2) }} $
                            </div>
                        </div>

                    </div>

                    {{-- Tracking --}}
<div class="pt-2 text-sm text-gray-700">
    <span class="text-xs uppercase tracking-wide text-gray-500">
        Tracking Number:
    </span>

    <span class="ml-2 inline-block px-3 py-1 border border-gray-300 bg-gray-50 text-gray-800 font-medium text-sm rounded-md">
        {{ $shipment->tracking_number ?? '-' }}
    </span>
</div>



                </div>

                {{-- RIGHT SIDE --}}
                <div class="flex-shrink-0">
                    <button type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition"
                            @click="openShipmentModal({{ $shipment->id }})">
                        Edit
                    </button>
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

        @php
    $declaredPrice = $order['delivery_price'] ?? 0;
    $isExceeded = $totalShipping > $declaredPrice;
@endphp

{{-- Total Shipping Amount --}}
<div class="mt-6 border rounded-xl p-4
    {{ $isExceeded ? 'border-red-200 bg-red-50' : 'border-emerald-200 bg-emerald-50' }}">

    <div class="flex justify-between items-center">

        <div>
            <div class="text-xs uppercase tracking-wide
                {{ $isExceeded ? 'text-red-600' : 'text-emerald-600' }}">
                Total Shipping Amount
            </div>

            <div class="mt-1 text-2xl font-semibold
                {{ $isExceeded ? 'text-red-700' : 'text-emerald-700' }}">
                {{ number_format($totalShipping, 2) }} $
            </div>
        </div>

        <div class="text-right">
            <div class="text-xs uppercase tracking-wide text-gray-500">
                Declared at Order
            </div>

            <div class="mt-1 text-lg font-medium text-gray-800">
                {{ number_format($declaredPrice, 2) }} $
            </div>
        </div>

    </div>

    <div class="mt-2 pt-2 border-t
        {{ $isExceeded ? 'border-red-200 text-red-700' : 'border-emerald-200 text-emerald-700' }}
        text-sm">

        @if($isExceeded)
            The total shipping amount exceeds the declared delivery price.
            Please adjust the shipment costs to match the confirmed order amount.
        @else
            The total shipping amount is within the declared delivery price.
        @endif

    </div>

</div>

        
    </div>

    {{-- MODAL --}}
    <div
        x-show="isOpen"
        x-transition
        x-cloak
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
</div>

@php
$allShipments = $order_items->flatMap(function($item){
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
        const res = await fetch(`/dashboard/manufacturer/orders/{{ $order['id'] }}/shipments/${this.shipmentId}`, {
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
