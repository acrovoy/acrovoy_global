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


{{-- ACROVOY SHIPMENTS --}}
@if($order->delivery_method === 'Delivery by Acrovoy')

<div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">

    <h3 class="font-medium mb-2 text-xs uppercase tracking-wide text-gray-500 border-b pb-2">
        Acrovoy Shipments (Per Item)
    </h3>

    <div class="divide-y divide-gray-100">

        @php
            $totalShipping = 0;
        @endphp

        @forelse($order->items as $item)
            @forelse($item->shipments as $shipment)

                @php
                    $totalShipping += $shipment->shipping_price ?? 0;
                @endphp


                <div class="py-4">

                    <div class="flex justify-between items-center mb-2">
                        <div class="flex flex-col">
                            <div class="font-medium">
                                Shipment #{{ $shipment->id }}
                            </div>

                         
                            {{-- Origin / Destination Summary --}}
                <div class="mt-1 text-sm text-gray-900 space-y-1">
                    <div>
                        <span class="font-medium uppercase tracking-wide text-gray-500 text-xs">Pickup:</span>
                        {{ $shipment->origin_address ?? '-' }},
                        {{ optional($shipment->originCity)->name ?? '-' }},
                        {{ optional($shipment->originRegion)->name ?? '-' }},
                        {{ optional($shipment->originCountry)->name ?? '-' }}
                    </div>
                    <div>
                        <span class="font-medium uppercase tracking-wide text-gray-500  text-xs">Delivery:</span>
                        {{ $shipment->destination_address ?? '-' }}, {{ optional($shipment->destinationCity)->name ?? '-' }}, 
                        {{ optional($shipment->destinationRegion)->name ?? '-' }}, 
                        {{ optional($shipment->destinationCountry)->name ?? '-' }}
                    </div>
                </div>

                            <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->product_name ?? 'Product unavailable' }}
                                </div>

                                <div class="mt-1 text-xs uppercase tracking-wide text-gray-500">
                                    Quantity: <span class="font-medium text-gray-700">{{ $item->quantity ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <span class="px-2 py-1 text-xs 
                            @if(!empty($order->delivery_price_confirmed) && $order->delivery_price_confirmed)
                                bg-emerald-100 text-emerald-700
                            @elseif($shipment->status === 'pending')
                                bg-yellow-100 text-yellow-700
                            @elseif($shipment->status === 'calculated')
                                bg-blue-100 text-blue-700
                            @elseif($shipment->status === 'shipped')
                                bg-green-100 text-green-700
                            @elseif($shipment->status === 'delivered')
                                bg-green-200 text-green-900
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

                        <button type="button"
                                class="px-2 py-1 text-sm bg-gray-100 rounded hover:bg-gray-200"
                                @click="openShipmentModal({{ $shipment->id }})">
                            Edit
                        </button>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-700">

                        <div>
                            <div class="text-xs text-gray-500">Weight</div>
                            <div>{{ $shipment->weight ?? '-' }} kg</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Dimensions (cm)</div>
                            <div>
                                {{ $shipment->length ?? '-' }} ×
                                {{ $shipment->width ?? '-' }} ×
                                {{ $shipment->height ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Delivery Time (days)</div>
                            <div>{{ $shipment->delivery_time ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Shipping Price</div>
                            <div class="font-semibold">
                                {{ number_format($shipment->shipping_price, 2) }} $
                            </div>
                        </div>

                    </div>

                    {{-- Display tracking number --}}
                    <div class="mt-2 text-sm text-gray-700">
                        Tracking Number: {{ $shipment->tracking_number ?? '-' }}
                    </div>

                </div>
            @empty
                <div class="py-4 text-sm text-gray-500">
                    No shipment records for this product yet.
                </div>
            @endforelse
        @empty
            <div class="py-4 text-sm text-gray-500">
                No shipment records created yet.
            </div>
        @endforelse

    </div>

    {{-- Total Shipping Amount --}}
    <div id="total-shipping-display" class="mt-4 p-3 bg-gray-100 rounded text-right text-lg font-semibold">
    Total Shipping: {{ number_format($totalShipping, 2) }} $
</div>



    {{-- Invoice & Calculate Total --}}
<div class="border rounded-lg p-4 bg-gray-50 mt-4">
    <h3 class="font-semibold mb-3">Invoice & Total Shipping</h3>

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
            <div>{{ $order->country }}</div>
        </div>

        <div>
            <div class="text-gray-500">City</div>
            <div>{{ $order->city }}</div>
        </div>

        <div>
            <div class="text-gray-500">Region</div>
            <div>{{ $order->region }}</div>
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
                        <option value="pending">Pending</option>
                        <option value="calculated">Calculated</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                    </select>
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
            tracking_number:''
        },
        shipments: @json($order->shipments),
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
                this.form.tracking_number = shipment.tracking_number ?? '';
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
                if(!res.ok) throw new Error('Failed to save');
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
