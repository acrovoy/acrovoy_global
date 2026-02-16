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
            <th class="px-4 py-3 text-left font-medium text-gray-500">Order</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Type</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Customer</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Destination</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Total</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Created</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
            
        </tr>
    </thead>

    <tbody class="bg-white divide-y divide-gray-100">
        @forelse($orders as $order)
            <tr onclick="window.location='{{ route('admin.orders.shipments', $order) }}'" class="hover:bg-gray-50 transition cursor-pointer">
                <td class="px-4 py-3 font-medium text-gray-900">
                    #{{ $order->id }}
                </td>

                <td class="px-4 py-3">
                    {{ ucfirst($order->type) }}
                </td>

                <td class="px-4 py-3">
                    {{ $order->first_name }} {{ $order->last_name }}
                </td>

                <td class="px-4 py-3">
                    {{ $order->countryRelation?->name ?? '-' }},
    {{ $order->regionRelation?->name ?? '-' }},
    {{ $order->cityRelation?->name ?? $order->city ?? '-' }}
                </td>

                <td class="px-4 py-3 font-semibold text-gray-900">
                    {{ number_format($order->total, 2) }} $
                </td>

                <td class="px-4 py-3">
                    {{ $order->created_at->format('d M Y') }}
                </td>

                <td class="px-4 py-3">
                    @if($order->status === 'cancelled')
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                            Cancelled
                        </span>

                    @elseif($order->delivery_price <= 0)
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                            Awaiting for Acrovoy calculation
                        </span>

                    @elseif(!$order->delivery_price_confirmed)
                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700">
                            Awaiting for buyer confirmation
                        </span>

                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700">
                            Confirmed by buyer
                        </span>
                    @endif
                </td>

                
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                    No orders awaiting delivery calculation.
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
