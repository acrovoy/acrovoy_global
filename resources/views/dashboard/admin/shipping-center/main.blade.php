@extends('dashboard.admin.layout')

@section('dashboard-content')
<div class="flex flex-col gap-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">Admin Shipping Center</h2>
            <p class="text-sm text-gray-500">
                Manage your delivery services for the platform
            </p>
        </div>

        

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.shipping-center.index') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                Manage Shipping Costs
            </a>

            <a href="{{ route('admin.shipping-templates.index') }}"
               class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                Manage Shipping Templates
            </a>

            
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        
        <div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">ID</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">Order</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">Type</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">Weight</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">Dimensions</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">Price</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">Delivery</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 ">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 ">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($shipments as $shipment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    #{{ $shipment->id }}
                                </td>

                                <td class="px-4 py-3">
                                    #{{ $shipment->order_id }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ class_basename($shipment->shippable_type) }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $shipment->weight ?? '-' }} kg
                                </td>

                                <td class="px-4 py-3">
                                    @if($shipment->length && $shipment->width && $shipment->height)
                                        {{ $shipment->length }} × {{ $shipment->width }} × {{ $shipment->height }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    ${{ number_format($shipment->shipping_price, 2) }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $shipment->delivery_time ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($shipment->status === 'pending') bg-yellow-100 text-yellow-700
                                        @elseif($shipment->status === 'processing') bg-blue-100 text-blue-700
                                        @elseif($shipment->status === 'shipped') bg-indigo-100 text-indigo-700
                                        @elseif($shipment->status === 'delivered') bg-green-100 text-green-700
                                        @elseif($shipment->status === 'cancelled') bg-red-100 text-red-700
                                        @endif
                                    ">
                                        {{ ucfirst($shipment->status) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="#"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                    No shipment requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</div>
@endsection
